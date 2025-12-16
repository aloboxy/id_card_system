@extends('layouts.app')

@section('header', 'ID Card Templates')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">All Templates</h3>
        <a href="{{ route('id-card-templates.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            <i class="fas fa-plus mr-2"></i> Create Template
        </a>
    </div>
    <div class="p-6 overflow-x-auto">
        <table id="templates-table" class="w-full text-left display stripe hover">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 font-medium">No</th>
                    <th class="px-6 py-3 font-medium">Name / Description</th>
                    <th class="px-6 py-3 font-medium">School</th>
                    <th class="px-6 py-3 font-medium">Dimensions</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#templates-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('id-card-templates.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'school_name', name: 'school.name' },
                { data: 'dimensions', name: 'dimensions', orderable: false, searchable: false },
                { data: 'is_active', name: 'is_active' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            language: {
                searchPlaceholder: "Search templates...",
                lengthMenu: "Show _MENU_ templates",
            },
            columnDefs: [
                { className: "px-6 py-4", targets: "_all" }
            ]
        });
    });
</script>
@endpush
@endsection
