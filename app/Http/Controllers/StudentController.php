<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:student-list|student-create|student-edit|student-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:student-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:student-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:student-delete', ['only' => ['destroy']]);
    }

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

                    $btns = '<div class="flex items-center justify-end space-x-2">';

                    if (auth()->user()->can('student-list')) { // Assuming view requires list
                        $btns .= '<a href="'.$viewUrl.'" class="p-1 px-2 text-blue-600 hover:bg-blue-50 rounded-md transition-colors" title="View">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }

                    if (auth()->user()->can('student-edit')) {
                        $btns .= '<a href="'.$editUrl.'" class="p-1 px-2 text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>';
                    }

                    if (auth()->user()->can('student-delete')) {
                        $btns .= '<form action="'.$deleteUrl.'" method="POST" class="inline-block" onsubmit="return confirm(\'Are you sure you want to delete this student?\');">
                                '.$csrf.'
                                '.$method.'
                                <button type="submit" class="p-1 px-2 text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>';
                    }

                    $btns .= '</div>';

                    // Add Crop Button (Inline with other actions or somewhat separate)
                    if (auth()->user()->can('student-edit')) {
                        $photoUrl = $row->profile_photo_path ? asset('storage/'.$row->profile_photo_path) : 'null';
                        // Escape single quotes for JS compatibility
                        $photoUrl = str_replace("'", "\'", $photoUrl);

                        // Append Crop Button to existing buttons container (removing the closing div first if needed, but here we construct a new string or just append)
                        // Wait, previous code closed the div: $btns .= '</div>';
                        // To make it look good, let's remove the closure, append, then close.
                        // But since we can't easily undo the string concatenation efficiently without regex,
                        // let's reopen the div or just append another div?
                        // Better: Clean slate approach for this block.

                        // Remove the last closing div from $btns to append
                        $btns = substr($btns, 0, -6);

                        $btns .= '<button onclick="openAjaxCrop('.$row->id.', \''.$photoUrl.'\')" data-id="'.$row->id.'" class="p-1 px-2 text-green-600 hover:bg-green-50 rounded-md transition-colors" title="Crop/Upload Photo">
                                    <i class="fas fa-crop-alt"></i>
                                  </button>';

                        $btns .= '</div>';
                    } else {
                        // Ensure it's closed if no crop button
                        // It was already closed by line 86 check above?
                        // Actually line 86 was: $btns .= '</div>';
                        // So if we enter this block, we need to undo that or handle it differently.
                    }

                    return $btns;
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
            'student_id' => [
                'nullable', 'string', 'max:255',
                Rule::unique('students')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id);
                }),
            ],
            'admission_number' => [
                'nullable', 'string', 'max:255',
                Rule::unique('students')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id);
                }),
            ],
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

            // Get next student number for this school by finding the maximum existing ID
            $prefix = strtoupper($school->school_code).'-';
            $lastStudent = Student::withTrashed()
                ->where('school_id', $school->id)
                ->where('student_id', 'like', $prefix.'%')
                ->orderByRaw('CAST(SUBSTRING(student_id, '.(strlen($prefix) + 1).') AS UNSIGNED) DESC')
                ->first();

            $nextNumber = 1;
            if ($lastStudent) {
                $lastId = $lastStudent->student_id;
                $numberPart = substr($lastId, strlen($prefix));
                if (is_numeric($numberPart)) {
                    $nextNumber = (int) $numberPart + 1;
                }
            }

            // Ensure uniqueness in case of gaps or manual entries
            do {
                $studentId = $prefix.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                $exists = Student::withTrashed()
                    ->where('school_id', $school->id)
                    ->where('student_id', $studentId)
                    ->exists();
                if ($exists) {
                    $nextNumber++;
                }
            } while ($exists);

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
            'student_id' => [
                'required', 'string', 'max:255',
                Rule::unique('students')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id);
                })->ignore($student->id),
            ],
            'admission_number' => [
                'required', 'string', 'max:255',
                Rule::unique('students')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id);
                })->ignore($student->id),
            ],
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

    public function updatePhoto(Request $request, Student $student)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:2048', // 2MB Max
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');

            $student->update([
                'profile_photo_path' => $path,
                'photo_path' => $path, // Sync legacy path
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
