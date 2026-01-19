<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\School;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:staff-list|staff-create|staff-edit|staff-delete', ['only' => ['index','show']]);
         $this->middleware('permission:staff-create', ['only' => ['create','store']]);
         $this->middleware('permission:staff-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:staff-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Staff::with('school')->select('staff.*');

            if ($request->has('school_id') && $request->school_id != '') {
                $data->where('school_id', $request->school_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('full_name', function ($row) {
                    return '<div class="font-medium text-gray-800">'.$row->full_name.'</div>
                            <div class="text-xs text-gray-500">'.$row->staff_id.'</div>';
                })
                ->addColumn('school_name', function ($row) {
                    return '<div class="text-sm text-gray-600">'.($row->school->name ?? 'N/A').'</div>';
                })
                ->addColumn('designation', function ($row) {
                    return '<div class="text-sm text-gray-600">'.$row->designation.'</div>';
                })
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT_WS(' ', first_name, middle_name, last_name) like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('school_name', function ($query, $keyword) {
                    $query->whereHas('school', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('is_active', function ($row) {
                    if ($row->is_active) {
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
                    } else {
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $viewUrl = route('staff.show', $row->id);
                    $editUrl = route('staff.edit', $row->id);
                    $deleteUrl = route('staff.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    $btns = '<div class="flex items-center justify-end space-x-2">';
                    
                    if(auth()->user()->can('staff-list')){
                         $btns .= '<a href="'.$viewUrl.'" class="p-1 px-2 text-blue-600 hover:bg-blue-50 rounded-md transition-colors" title="View">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }

                    if(auth()->user()->can('staff-edit')){
                         $btns .= '<a href="'.$editUrl.'" class="p-1 px-2 text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>';
                    }

                    if(auth()->user()->can('staff-delete')){
                         $btns .= '<form action="'.$deleteUrl.'" method="POST" class="inline-block" onsubmit="return confirm(\'Are you sure you want to delete this staff member?\');">
                                '.$csrf.'
                                '.$method.'
                                <button type="submit" class="p-1 px-2 text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>';
                    }
                    
                    if(auth()->user()->can('staff-edit')){
                         $photoUrl = $row->profile_photo_path ? asset('storage/'.$row->profile_photo_path) : 'null';
                         // Escape single quotes
                         $photoUrl = str_replace("'", "\'", $photoUrl);

                         $btns .= '<button onclick="openAjaxCrop('.$row->id.', \''.$photoUrl.'\')" data-id="'.$row->id.'" class="p-1 px-2 text-green-600 hover:bg-green-50 rounded-md transition-colors" title="Crop/Upload Photo">
                                    <i class="fas fa-crop-alt"></i>
                                  </button>';
                    }

                    $btns .= '</div>';
                    return $btns;
                })
                ->rawColumns(['full_name', 'school_name', 'designation', 'is_active', 'action'])
                ->make(true);
        }

        return view('staff.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schools = School::all();
        return view('staff.create', compact('schools'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'staff_id' => [
                'nullable', 'string', 'max:255',
                Rule::unique('staff')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id);
                })
            ],
            'email' => 'nullable|string|email|max:255|unique:staff,email',
            'phone' => 'nullable|string|max:255',
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'profile_photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ]);

        // Auto-generate staff_id if not provided
        if (empty($validated['staff_id'])) {
            $school = School::findOrFail($validated['school_id']);

            if (empty($school->school_code)) {
                return back()->withErrors(['school_id' => 'School must have a school code to auto-generate staff ID.'])->withInput();
            }

            // Get next staff number for this school by finding the maximum existing ID
            $prefix = 'STf-' . strtoupper($school->school_code) . '-';
            $lastStaff = Staff::withTrashed()
                ->where('school_id', $school->id)
                ->where('staff_id', 'like', $prefix . '%')
                ->orderByRaw('CAST(SUBSTRING(staff_id, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
                ->first();

            $nextNumber = 1;
            if ($lastStaff) {
                $lastId = $lastStaff->staff_id;
                $numberPart = substr($lastId, strlen($prefix));
                if (is_numeric($numberPart)) {
                    $nextNumber = (int)$numberPart + 1;
                }
            }

            // Ensure uniqueness in case of gaps or manual entries
            do {
                $staffId = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                $exists = Staff::withTrashed()
                    ->where('school_id', $school->id)
                    ->where('staff_id', $staffId)
                    ->exists();
                if ($exists) {
                    $nextNumber++;
                }
            } while ($exists);

            $validated['staff_id'] = $staffId;
        }

        $data = $validated;

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('staff_photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        if ($request->hasFile('signature')) {
            $path = $request->file('signature')->store('staff_signatures', 'public');
            $data['signature_path'] = $path;
        }

        Staff::create($data);

        return redirect()->route('staff.index')->with('success', 'Staff member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
        return view('staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        $schools = School::all();
        return view('staff.edit', compact('staff', 'schools'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'staff_id' => [
                'required', 'string', 'max:255',
                Rule::unique('staff')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id);
                })->ignore($staff->id)
            ],
            'email' => 'nullable|string|email|max:255|unique:staff,email,' . $staff->id,
            'phone' => 'nullable|string|max:255',
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'profile_photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ]);

        $data = $validated;

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('staff_photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        if ($request->hasFile('signature')) {
            $path = $request->file('signature')->store('staff_signatures', 'public');
            $data['signature_path'] = $path;
        }

        $staff->update($data);

        return redirect()->route('staff.index')->with('success', 'Staff member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully.');
    }

    public function updatePhoto(Request $request, Staff $staff)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:2048', // 2MB Max
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('staff_photos', 'public');

            $staff->update([
                'profile_photo_path' => $path,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully',
                'path' => asset('storage/'.$path),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image file uploaded'], 400);
    }
}
