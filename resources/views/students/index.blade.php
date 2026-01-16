@extends('layouts.app')
@section('header', 'Students')
@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">All Students</h3>
        <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            <i class="fas fa-plus mr-2"></i> Add Student
        </a>
    </div>
    <div class="p-6 overflow-x-auto">
        <table id="students-table" class="w-full text-left display stripe hover">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 font-medium">No</th>
                    <th class="px-6 py-3 font-medium">Name / ID</th>
                    <th class="px-6 py-3 font-medium">School</th>
                    <th class="px-6 py-3 font-medium">Class</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

{{-- Crop Modal Component --}}
@include('components.modals.crop-image')

{{-- Hidden Form for AJAX Upload --}}
<form id="ajax-upload-form" method="POST" enctype="multipart/form-data" class="hidden">
    @csrf
    @method('PUT')
    <input type="file" id="ajax_profile_photo" name="profile_photo">
    <input type="hidden" id="ajax_student_id" name="student_id_val">
</form>

@push('styles')
<!-- DataTables CSS -->

<style>
    /* Tailwind-ish DataTables Styling */
    .dataTables_wrapper .dataTables_length select {
        padding-right: 2rem;
        background-position: right 0.5rem center;
        border-radius: 0.375rem;
        border-color: #d1d5db;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.375rem;
        border-color: #d1d5db;
        padding: 0.5rem;
        margin-left: 0.5rem;
    }
    table.dataTable.no-footer {
        border-bottom: 1px solid #e5e7eb;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery and DataTables JS -->
<script>
    $(document).ready(function() {
        // DataTable Initialization
        const table = $('#students-table').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="fas fa-copy mr-1"></i> Copy',
                    className: 'bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center border border-gray-300'
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                    className: 'bg-green-100 hover:bg-green-200 text-green-800 font-bold py-2 px-4 rounded inline-flex items-center border border-green-300'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                    className: 'bg-red-100 hover:bg-red-200 text-red-800 font-bold py-2 px-4 rounded inline-flex items-center border border-red-300'
                }
            ],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            ajax: "{{ route('students.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'full_name', name: 'full_name' },
                { data: 'school_name', name: 'school.name' },
                { data: 'class', name: 'class' }, 
                { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            language: {
                searchPlaceholder: "Search students...",
                lengthMenu: "Show _MENU_ students",
            },
            columnDefs: [
                { className: "px-6 py-4", targets: "_all" }
            ]
        });

        // Global function to trigger crop from DataTable actions
        window.openAjaxCrop = function(studentId, currentPhotoUrl) {
            // If no photo exists, trigger file input directly
            if (!currentPhotoUrl || currentPhotoUrl === 'null') {
                 // Set context for when file is selected
                 window.pendingStudentId = studentId;
                 document.getElementById('ajax_profile_photo').click();
                 return;
            }

            // If photo exists, open crop modal directly
            startAjaxCropProcess(studentId, currentPhotoUrl);
        };

        // Handle file selection from empty state
        document.getElementById('ajax_profile_photo').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0] && window.pendingStudentId) {
                const file = e.target.files[0];
                startAjaxCropProcess(window.pendingStudentId, file);
                // Clear pending ID
                window.pendingStudentId = null;
            }
        });

        function startAjaxCropProcess(studentId, source) {
             // Use the shared component function
             // We pass 'null' for imgId since we don't have a preview on this page to update
             // We pass 'ajax_profile_photo' as inputId to hold the final blob
             openCropModal(source, null, 'ajax_profile_photo', function(blob) {
                // Determine callback: upload immediately
                uploadCroppedImage(studentId, blob);
             });
        }

        function uploadCroppedImage(studentId, blob) {
             const formData = new FormData();
             formData.append('profile_photo', blob, 'cropped_update.jpg');
             // formData.append('_method', 'PUT'); // Removed: Route is registered as POST
             formData.append('_token', '{{ csrf_token() }}');

             // Show loading state
             const btn = document.querySelector(`button[data-id="${studentId}"]`);
             if(btn) {
                 const originalHtml = btn.innerHTML;
                 btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                 btn.disabled = true;
             }

             console.log('Starting upload for student:', studentId);
             if(!blob) {
                 console.error('No blob data to upload!');
                 alert('Error: Image data is empty.');
                 return;
             }

             // Assuming you have an endpoint like /students/{id}/update-photo
             // Or reusing the main update route but sending minimal data?
             // Since we don't have a specific lightweight route, we might need one or create a special handler.
             // For now, let's assume we can utilize the main update route if we are careful, 
             // but usually a dedicated API endpoint is better to avoid validation errors on missing fields.
             // Let's use a specialized route that we will create next.
             
             fetch(`/students/${studentId}/update-photo`, {
                 method: 'POST',
                 body: formData,
                 headers: {
                     'X-Requested-With': 'XMLHttpRequest',
                     'Accept': 'application/json'
                 }
             })
             .then(async response => {
                 const contentType = response.headers.get("content-type");
                 if (!response.ok) {
                    // Try to get error message from JSON or text
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                         const err = await response.json();
                         throw new Error(err.message || 'Server returned an error.');
                    } else {
                         const text = await response.text();
                         console.error('Server HTML Error:', text);
                         throw new Error('Server error (check console for details). Status: ' + response.status);
                    }
                 }
                 return response.json();
             })
             .then(data => {
                 if(data.success) {
                     // Reload table
                     table.ajax.reload(null, false);
                     // Show a less intrusive notification or keep alert for now
                     // alert('Photo updated successfully!'); 
                     // Let's use a console log + button success state to not block UI
                     console.log('Success:', data.message);
                 } else {
                     alert('Failed to update photo: ' + (data.message || 'Unknown error'));
                 }
             })
             .catch(error => {
                 console.error('Upload Error:', error);
                 alert('Error uploading image: ' + error.message);
             })
             .finally(() => {
                 if(btn) {
                    btn.innerHTML = originalHtml; // Restore button (though row might be reloaded)
                    btn.disabled = false;
                 }
             });
        }
    });
</script>
@endpush
@endsection
