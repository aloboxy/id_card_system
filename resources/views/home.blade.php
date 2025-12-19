@extends('layouts.app')

@section('header', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Schools Card -->
    @if(auth()->user()->can('school-list') || auth()->user()->isAdmin())
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-500 text-sm font-medium">Total Schools</h3>
            <div class="p-2 bg-indigo-50 rounded-lg">
                <i class="fas fa-school text-indigo-600"></i>
            </div>
        </div>
        <div class="flex items-baseline">
            <span class="text-3xl font-bold text-gray-800">{{ \App\Models\School::count() }}</span>
        </div>
    </div>
    @endif

    <!-- Students Card -->
    @if(auth()->user()->can('student-list') || auth()->user()->isAdmin())
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-500 text-sm font-medium">Total Students</h3>
            <div class="p-2 bg-green-50 rounded-lg">
                <i class="fas fa-user-graduate text-green-600"></i>
            </div>
        </div>
        <div class="flex items-baseline">
            <span class="text-3xl font-bold text-gray-800">{{ \App\Models\Student::count() }}</span>
        </div>
    </div>
    @endif

    <!-- Templates Card -->
    @if(auth()->user()->can('template-list') || auth()->user()->isAdmin())
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-500 text-sm font-medium">Active Templates</h3>
            <div class="p-2 bg-purple-50 rounded-lg">
                <i class="fas fa-id-card text-purple-600"></i>
            </div>
        </div>
        <div class="flex items-baseline">
            <span class="text-3xl font-bold text-gray-800">{{ \App\Models\IdCardTemplate::where('is_active', true)->count() }}</span>
        </div>
    </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Students -->
    @if(auth()->user()->can('student-list') || auth()->user()->isAdmin())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Recent Students</h3>
            <a href="{{ route('students.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 font-medium">Name</th>
                        <th class="px-6 py-3 font-medium">School</th>
                        <th class="px-6 py-3 font-medium">Class</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse(\App\Models\Student::with('school')->latest()->take(5)->get() as $student)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $student->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $student->student_id }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $student->school->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $student->class_with_section }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">No students found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-4">
            @if(auth()->user()->can('student-create') || auth()->user()->isAdmin())
            <a href="{{ route('students.create') }}" class="flex flex-col items-center justify-center p-6 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors group">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-plus text-indigo-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-indigo-700">Add Student</span>
            </a>
            @endif

            @if(auth()->user()->can('template-create') || auth()->user()->isAdmin())
            <a href="{{ route('id-card-templates.create') }}" class="flex flex-col items-center justify-center p-6 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-paint-brush text-purple-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-purple-700">Design Template</span>
            </a>
            @endif

            @if(auth()->user()->can('school-create') || auth()->user()->isAdmin())
            <a href="{{ route('schools.create') }}" class="flex flex-col items-center justify-center p-6 bg-green-50 rounded-xl hover:bg-green-100 transition-colors group">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-green-700">Add School</span>
            </a>
            @endif

            @if(auth()->user()->can('template-list') || auth()->user()->isAdmin())
            <a href="{{ route('id-card-templates.index') }}" class="flex flex-col items-center justify-center p-6 bg-orange-50 rounded-xl hover:bg-orange-100 transition-colors group">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-print text-orange-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-orange-700">Generate IDs</span>
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
