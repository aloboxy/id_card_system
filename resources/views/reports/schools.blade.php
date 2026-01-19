@extends('layouts.app')
@section('header', 'School Data Management')
@section('content')

{{-- Crop Modal --}}
@include('components.modals.crop-image')

{{-- Hidden Form for AJAX Upload --}}
<form id="ajax-upload-form" method="POST" enctype="multipart/form-data" class="hidden">
    @csrf
    @method('PUT')
    <input type="file" id="ajax_profile_photo" name="profile_photo">
</form>

<div class="space-y-6">
    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <!-- School Selection -->
            <div>
                <label for="school_select" class="block text-sm font-medium text-gray-700 mb-2">Select School</label>
                <select id="school_select" name="school_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Choose a School --</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category Selection -->
            <div>
                <label for="category_select" class="block text-sm font-medium text-gray-700 mb-2">Select Category</label>
                <select id="category_select" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" disabled>
                    <option value="students">Students</option>
                    <option value="staff">Staff</option>
                </select>
            </div>

            <!-- Actions -->
            <div>
                 <button id="load_data_btn" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150" disabled>
                    Load Data
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden" id="data-container">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800" id="table-title">Data List</h3>
        </div>
        <div class="p-6 overflow-x-auto">
            <table id="main-table" class="w-full text-left display stripe hover" style="width:100%">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase" id="table-head">
                    <!-- Dynamic Headers will be injected here -->
                </thead>
            </table>
        </div>
    </div>
    
    <!-- Empty State -->
    <div id="empty-state" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-search text-2xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900">No Data Selected</h3>
        <p class="text-gray-500 mt-2 max-w-sm">Please select a school and category to view the records.</p>
    </div>
</div>

@push('styles')
<style>
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
<script>
    let table = null;
    let selectedSchoolId = '';
    let selectedCategory = 'students';
    let pendingEntityId = null; // For cropping
    
    // Elements
    const schoolSelect = document.getElementById('school_select');
    const categorySelect = document.getElementById('category_select');
    const loadBtn = document.getElementById('load_data_btn');
    const dataContainer = document.getElementById('data-container');
    const emptyState = document.getElementById('empty-state');
    const tableHead = document.getElementById('table-head');
    const tableTitle = document.getElementById('table-title');

    // Enable/Disable logic
    schoolSelect.addEventListener('change', function() {
        if(this.value) {
            categorySelect.disabled = false;
            loadBtn.disabled = false;
        } else {
            categorySelect.disabled = true;
            loadBtn.disabled = true;
        }
    });

    loadBtn.addEventListener('click', function() {
        selectedSchoolId = schoolSelect.value;
        selectedCategory = categorySelect.value;
        
        if(!selectedSchoolId) return;

        loadTable(selectedSchoolId, selectedCategory);
    });

    function loadTable(schoolId, category) {
        // Toggle UI
        emptyState.classList.add('hidden');
        dataContainer.classList.remove('hidden');

        // Destroy existing table
        if (table) {
            table.destroy();
            $('#main-table').empty(); // Clear content
        }

        // Configuration based on Category
        let url = '';
        let columns = [];
        let headerHtml = '';

        if(category === 'students') {
            tableTitle.textContent = "Students List";
            url = "{{ route('students.index') }}";
            headerHtml = `
                <tr>
                    <th class="px-6 py-3 font-medium">No</th>
                    <th class="px-6 py-3 font-medium">Name / ID</th>
                    <th class="px-6 py-3 font-medium">Class</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr>
            `;
            columns = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'full_name', name: 'full_name' },
                { data: 'class', name: 'class' },
                { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ];
        } else {
            tableTitle.textContent = "Staff List";
            url = "{{ route('staff.index') }}";
            headerHtml = `
                <tr>
                    <th class="px-6 py-3 font-medium">No</th>
                    <th class="px-6 py-3 font-medium">Name / ID</th>
                    <th class="px-6 py-3 font-medium">Designation</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr>
            `;
            columns = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'full_name', name: 'full_name' },
                { data: 'designation', name: 'designation' },
                { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ];
        }

        // Set Headers
        tableHead.innerHTML = headerHtml;

        // Initialize DataTable
        table = $('#main-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                data: function(d) {
                    d.school_id = schoolId;
                }
            },
            columns: columns,
            language: {
                searchPlaceholder: "Search...",
            },
            columnDefs: [
                { className: "px-6 py-4", targets: "_all" }
            ],
            dom: 'Blfrtip',
            buttons: [
                'copy', 'excel', 'pdf', 'print'
            ]
        });
    }

    // --- CROP FUNCTIONALITY ---

    // Global function triggered by the crop button in DataTable
    window.openAjaxCrop = function(entityId, currentPhotoUrl) {
        // Set pending ID for potential new file selection
        window.pendingEntityId = entityId;

        // If no photo exists, trigger file input directly
        if (!currentPhotoUrl || currentPhotoUrl === 'null' || currentPhotoUrl === '') {
             document.getElementById('ajax_profile_photo').click();
             return;
        }

        // If photo exists, open crop modal directly
        startAjaxCropProcess(entityId, currentPhotoUrl);
    };

    // Handle file selection from empty state or generally
    document.getElementById('ajax_profile_photo').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0] && window.pendingEntityId) {
            const file = e.target.files[0];
            startAjaxCropProcess(window.pendingEntityId, file);
        }
    });

    function startAjaxCropProcess(id, source) {
         // Open Crop Modal
         // Using the component provided in layout or included
         openCropModal(source, null, 'ajax_profile_photo', function(blob) {
            uploadCroppedImage(id, blob);
         });
    }

    function uploadCroppedImage(id, blob) {
         const formData = new FormData();
         formData.append('profile_photo', blob, 'cropped_update.jpg');
         formData.append('_token', '{{ csrf_token() }}');

         // Determine endpoint based on category
         // Students: /students/{id}/update-photo
         // Staff: /staff/{id}/update-photo (Need to ensure this route exists)
         let endpoint = '';
         if(selectedCategory === 'students') {
             endpoint = `/students/${id}/update-photo`;
         } else {
             endpoint = `/staff/${id}/update-photo`;
         }

         // Show loading indicator on button if found (tricky since we might have closed modal)
         // We can use a global loader or toast
         const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
         });
         
         Toast.fire({
            icon: 'info',
            title: 'Uploading image...'
         });

         fetch(endpoint, {
             method: 'POST',
             body: formData,
             headers: {
                 'X-Requested-With': 'XMLHttpRequest',
                 'Accept': 'application/json'
             }
         })
         .then(async response => {
             if (!response.ok) {
                 const text = await response.text();
                 throw new Error(text || 'Server error');
             }
             return response.json();
         })
         .then(data => {
             if(data.success) {
                 table.ajax.reload(null, false);
                 Toast.fire({
                    icon: 'success',
                    title: 'Photo updated successfully'
                 });
             } else {
                 throw new Error(data.message || 'Unknown error');
             }
         })
         .catch(error => {
             console.error('Upload Error:', error);
             Toast.fire({
                icon: 'error',
                title: 'Upload failed'
             });
             alert('Error uploading image: ' + error.message);
         });
    }

</script>
@endpush
@endsection
