@extends('layouts.app')

@section('header', 'Role Management')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Role Management</h3>
        @can('role-create')
        <a href="{{ route('roles.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            <i class="fas fa-plus mr-2"></i> Create New Role
        </a>
        @endcan
    </div>
    
    @if ($message = Session::get('success'))
        <div class="px-6 pt-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $message }}</span>
            </div>
        </div>
    @endif

    <div class="p-6 overflow-x-auto">
        <table class="w-full text-left display stripe hover data-table">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 font-medium">No</th>
                    <th class="px-6 py-3 font-medium">Name</th>
                    <th class="px-6 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            </tbody>
        </table>
    </div>
</div>

@push('styles')
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
<script type="text/javascript">
  $(function () {
    
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('roles.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: "text-right"},
        ],
        order: [[1, 'asc']], // Default sort by Name
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
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
         language: {
             searchPlaceholder: "Search roles...",
             lengthMenu: "Show _MENU_ roles",
         },
         columnDefs: [
             { className: "px-6 py-4", targets: "_all" }
         ]
    });
    
  });
</script>
@endpush
@endsection
