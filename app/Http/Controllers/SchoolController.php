<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = School::latest()->select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function($row){
                    return '<div class="font-medium text-gray-800">'.$row->name.'</div>
                            <div class="text-xs text-gray-500">'.$row->address.'</div>';
                })
                ->addColumn('contact', function($row){
                    return '<div class="text-sm text-gray-600">'.($row->contact_email ?? '').'</div>
                            <div class="text-xs text-gray-500">'.($row->contact_phone ?? '').'</div>';
                })
                ->editColumn('is_active', function($row){
                    if($row->is_active) {
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
                    } else {
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>';
                    }
                })
                ->addColumn('action', function($row){
                    $editUrl = route('schools.edit', $row->id);
                    $deleteUrl = route('schools.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    
                    return '<div class="text-right text-sm font-medium">
                            <a href="'.$editUrl.'" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="'.$deleteUrl.'" method="POST" class="inline-block" onsubmit="return confirm(\'Are you sure you want to delete this school?\');">
                                '.$csrf.'
                                '.$method.'
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                            </div>';
                })
                ->rawColumns(['name', 'contact', 'is_active', 'action'])
                ->make(true);
        }

        return view('schools.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('schools.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'school_code' => 'required|string|max:10|unique:schools,school_code',
            'address' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'date_issue' => 'nullable|date',
            'expire_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        School::create($validated);

        return redirect()->route('schools.index')->with('success', 'School created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school)
    {
        return view('schools.show', compact('school'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school)
    {
        return view('schools.edit', compact('school'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'school_code' => 'required|string|max:10|unique:schools,school_code,'.$school->id,
            'address' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'date_issue' => 'nullable|date',
            'expire_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $school->update($validated);

        return redirect()->route('schools.index')->with('success', 'School updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        // Permanently delete the school (not soft delete)
        $school->forceDelete();

        return redirect()->route('schools.index')->with('success', 'School deleted successfully.');
    }
}
