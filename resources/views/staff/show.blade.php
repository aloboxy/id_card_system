@extends('layouts.app')

@section('header', 'Staff Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Staff Information</h3>
            <div class="flex space-x-3">
                <a href="{{ route('staff.edit', $staff->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('staff.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
        
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Left Column: Photo --}}
                <div class="md:col-span-1 space-y-6">
                    <div class="flex flex-col items-center p-4 border rounded-lg bg-gray-50">
                        @if($staff->profile_photo_path)
                            <img src="{{ asset('storage/' . $staff->profile_photo_path) }}" alt="Profile Photo" class="h-48 w-48 object-cover rounded-lg shadow-sm mb-4">
                        @else
                            <div class="h-48 w-48 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 mb-4">
                                <i class="fas fa-user-tie text-4xl"></i>
                            </div>
                        @endif
                        <span class="text-sm font-medium text-gray-500">Profile Photo</span>
                    </div>

                    @if($staff->signature_path)
                    <div class="flex flex-col items-center p-4 border rounded-lg bg-gray-50">
                        <img src="{{ asset('storage/' . $staff->signature_path) }}" alt="Signature" class="h-32 w-32 object-contain rounded-lg shadow-sm bg-white mb-2">
                        <span class="text-sm font-medium text-gray-500">Signature</span>
                    </div>
                    @endif
                </div>

                {{-- Right Column: Details --}}
                <div class="md:col-span-2 space-y-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Professional Information</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs text-gray-400">Full Name</label>
                                <p class="text-lg font-medium text-gray-900">{{ $staff->full_name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400">Staff ID</label>
                                <p class="text-sm font-medium text-gray-900">{{ $staff->staff_id }}</p>
                            </div>
                             <div>
                                <label class="block text-xs text-gray-400">Joining Date</label>
                                <p class="text-sm font-medium text-gray-900">{{ $staff->joining_date ? $staff->joining_date->format('F d, Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400">Designation</label>
                                <p class="text-sm font-medium text-gray-900">{{ $staff->designation }}</p>
                            </div>
                             <div>
                                <label class="block text-xs text-gray-400">Department</label>
                                <p class="text-sm font-medium text-gray-900">{{ $staff->department ?? 'N/A' }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs text-gray-400">School</label>
                                <p class="text-sm font-medium text-gray-900">{{ $staff->school->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Contact Information</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                             <div>
                                <label class="block text-xs text-gray-400">Email</label>
                                <p class="text-sm font-medium text-gray-900">{{ $staff->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400">Phone</label>
                                <p class="text-sm font-medium text-gray-900">{{ $staff->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4 flex items-center justify-between text-xs text-gray-400">
                        <span>Created: {{ $staff->created_at->format('M d, Y') }}</span>
                        <span>Last Updated: {{ $staff->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
