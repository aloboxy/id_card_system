<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Student::with('school')->select('students.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('full_name', function ($row) {
                    return '<div class="font-medium text-gray-800">'.$row->full_name.'</div>
                            <div class="text-xs text-gray-500">'.$row->student_id.'</div>';
                })
                ->addColumn('school_name', function ($row) {
                    return '<div class="text-sm text-gray-600">'.($row->school->name ?? 'N/A').'</div>';
                })
                ->addColumn('class', function ($row) {
                    return '<div class="text-sm text-gray-600">'.($row->class_with_section ?? '').'</div>';
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
                    $viewUrl = route('students.show', $row->id);
                    $editUrl = route('students.edit', $row->id);
                    $deleteUrl = route('students.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '<div class="flex items-center justify-end space-x-2">
                            <a href="'.$viewUrl.'" class="p-1 px-2 text-blue-600 hover:bg-blue-50 rounded-md transition-colors" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="'.$editUrl.'" class="p-1 px-2 text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="'.$deleteUrl.'" method="POST" class="inline-block" onsubmit="return confirm(\'Are you sure you want to delete this student?\');">
                                '.$csrf.'
                                '.$method.'
                                <button type="submit" class="p-1 px-2 text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            </div>';
                })
                ->rawColumns(['full_name', 'school_name', 'class', 'is_active', 'action'])
                ->make(true);
        }

        return view('students.index');
    }

    public function create()
    {
        $schools = \App\Models\School::all();

        return view('students.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'student_id' => 'nullable|string|max:255',
            'admission_number' => 'nullable|string|max:255',
            'class' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'fingerprint_image' => 'nullable|image|max:2048',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        // Auto-generate student_id and admission_number if not provided
        if (empty($validated['student_id'])) {
            $school = \App\Models\School::findOrFail($validated['school_id']);

            if (empty($school->school_code)) {
                return back()->withErrors(['school_id' => 'School must have a school code to auto-generate student ID.'])->withInput();
            }

            // Get next student number for this school
            $count = \App\Models\Student::where('school_id', $school->id)->count() + 1;
            $studentId = strtoupper($school->school_code).'-'.str_pad($count, 4, '0', STR_PAD_LEFT);

            $validated['student_id'] = $studentId;
            $validated['admission_number'] = $studentId;
        } else {
            // If student_id provided, use same for admission_number if not provided
            if (empty($validated['admission_number'])) {
                $validated['admission_number'] = $validated['student_id'];
            }
        }

        $data = $validated;
        if ($request->hasFile('fingerprint_image')) {
            $path = $request->file('fingerprint_image')->store('fingerprints', 'public');
            $data['fingerprint_image_path'] = $path;
        }

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $schools = \App\Models\School::all();

        return view('students.edit', compact('student', 'schools'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'student_id' => 'required|string|max:255',
            'admission_number' => 'required|string|max:255',
            'class' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'fingerprint_image' => 'nullable|image|max:2048',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        if ($request->hasFile('fingerprint_image')) {
            $path = $request->file('fingerprint_image')->store('fingerprints', 'public');
            $data['fingerprint_image_path'] = $path;
        }

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
