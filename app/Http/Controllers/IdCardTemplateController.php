<?php

namespace App\Http\Controllers;

use App\Models\IdCardTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class IdCardTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = IdCardTemplate::with('school')->select('id_card_templates.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return '<div class="font-medium text-gray-900">'.$row->name.'</div>
                            <div class="text-xs text-gray-500">'.(\Illuminate\Support\Str::limit($row->description, 50)).'</div>';
                })
                ->addColumn('school_name', function ($row) {
                    return '<div class="text-sm text-gray-600">'.($row->school->name ?? 'N/A').'</div>';
                })
                ->addColumn('dimensions', function ($row) {
                    return '<div class="text-xs text-gray-500">'.$row->width.'x'.$row->height.'px</div>';
                })
                ->editColumn('is_active', function ($row) {
                    if ($row->is_active) {
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
                    } else {
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('id-card-templates.edit', $row->id);
                    $deleteUrl = route('id-card-templates.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '<div class="text-right text-sm font-medium">
                              <a href="'.$editUrl.'" class="p-1 px-2 text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="'.$deleteUrl.'" method="POST" class="inline-block" onsubmit="return confirm(\'Are you sure you want to delete this template?\');">
                                '.$csrf.'
                                '.$method.'
                                <button type="submit" class="p-1 px-2 text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            </div>';
                })
                ->rawColumns(['name', 'school_name', 'dimensions', 'is_active', 'action'])
                ->make(true);
        }

        return view('id_card_templates.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('id_card_templates.designer', [
            'template' => new IdCardTemplate([
                'width' => 300,
                'height' => 200,
                'design_data' => $this->getDefaultDesignData(),
            ]),
            'is_edit' => false,
            'schools' => \App\Models\School::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:id_card_templates,name',
            'description' => 'nullable|string',
            'width' => 'required|integer|min:100|max:1000',
            'height' => 'required|integer|min:100|max:1000',
            'design_data' => 'required|string', // Fabric JSON is sent as string
            'design_data_back' => 'nullable|string',
            'school_id' => 'required|exists:schools,id',
            'active' => 'boolean',
        ]);

        $template = IdCardTemplate::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'width' => $validated['width'],
            'height' => $validated['height'],
            'design_data' => json_decode($validated['design_data'], true),
            'design_data_back' => isset($validated['design_data_back']) ? json_decode($validated['design_data_back'], true) : null,
            'active' => $request->input('active', 0) == '1',
            'created_by' => Auth::id() ?? 1,
            'school_id' => $validated['school_id'],
        ]);

        return redirect()
            ->route('id-card-templates.show', $template)
            ->with('success', 'ID Card Template created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(IdCardTemplate $idCardTemplate)
    {
        return view('id_card_templates.show', [
            'template' => $idCardTemplate->load('creator', 'school'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IdCardTemplate $idCardTemplate)
    {
        return view('id_card_templates.designer', [
            'template' => $idCardTemplate,
            'is_edit' => true,
            'schools' => \App\Models\School::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IdCardTemplate $idCardTemplate)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('id_card_templates', 'name')->ignore($idCardTemplate->id),
            ],
            'description' => 'nullable|string',
            'width' => 'required|integer|min:100|max:1000',
            'height' => 'required|integer|min:100|max:1000',
            'design_data' => 'required|string',
            'design_data_back' => 'nullable|string',
            'school_id' => 'required|exists:schools,id',
        ]);

        // Decode design_data
        $designData = json_decode($validated['design_data'], true);

        // Decode design_data_back if present
        $designDataBack = null;
        if (isset($validated['design_data_back'])) {
            $designDataBack = json_decode($validated['design_data_back'], true);
        }

        $idCardTemplate->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'width' => $validated['width'],
            'height' => $validated['height'],
            'design_data' => $designData,
            'design_data_back' => $designDataBack,
            'school_id' => $validated['school_id'],
            'is_active' => $request->input('active', 0) == '1',
        ]);

        return redirect()
            ->route('id-card-templates.generate', $idCardTemplate)
            ->with('success', 'Template saved! Ready to generate ID cards.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IdCardTemplate $idCardTemplate)
    {
        // Check if template is being used by any students
        if ($idCardTemplate->students()->exists()) {
            return back()->with('error', 'Cannot delete template. It is being used by one or more students.');
        }

        $idCardTemplate->delete();

        return redirect()
            ->route('id-card-templates.index')
            ->with('success', 'ID Card Template deleted successfully!');
    }

    /**
     * Toggle the active status of the template.
     */
    public function toggleStatus(IdCardTemplate $idCardTemplate)
    {
        $idCardTemplate->update([
            'is_active' => ! $idCardTemplate->is_active,
        ]);

        return back()->with('success', 'Template status updated successfully!');
    }

    /**
     * Show the generation view.
     */
    public function generate(IdCardTemplate $idCardTemplate)
    {
        // For MVP, just get all students from the same school (or all students for now)
        // In a real app, we'd have a selection step
        $students = \App\Models\Student::where('school_id', $idCardTemplate->school_id)->get();

        // If no students found for the school, just get first 5 for demo purposes if dev
        if ($students->isEmpty()) {
            $students = \App\Models\Student::take(5)->get();
        }

        // Fetch school dates once
        $school = $idCardTemplate->school;
        $school_expiry_data = $school->expire_date ? Carbon::parse($school->expire_date)->format('Y-m-d') : date('Y-m-d');
        $school_issue_data = $school->date_issue ? Carbon::parse($school->date_issue)->format('Y-m-d') : date('Y-m-d');

        return view('id_card_templates.generate', [
            'template' => $idCardTemplate,
            'students' => $students,
            'school_expiry_date' => $school_expiry_data,
            'school_issue_date' => $school_issue_data,
        ]);
    }

    /**
     * Get the default design data for a new template.
     */
    protected function getDefaultDesignData(): array
    {
        // Return empty canvas to avoid any serialization issues
        // User can add elements using the designer tools
        return [
            'background' => '#ffffff',
            'objects' => [],
        ];
    }
}
