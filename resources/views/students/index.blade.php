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
        $('#students-table').DataTable({
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
                { data: 'full_name', name: 'full_name' }, // Note: sorting/searching might rely on accessor logic server-side if not configured
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
    });
</script>
@endpush
@endsection
