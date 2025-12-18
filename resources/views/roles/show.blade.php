@extends('layouts.app')

@section('header', 'Show Role')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb mb-4">
        <div class="pull-left">
            <h2 class="text-xl font-bold"> Show Role</h2>
        </div>
        <div class="pull-right">
            <a class="text-indigo-600 hover:text-indigo-900" href="{{ route('roles.index') }}"> Back</a>
        </div>
    </div>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
    <div class="grid grid-cols-1 gap-6">
        <div class="col-span-1">
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700">Name:</label>
                <p class="mt-1 text-sm text-gray-900">{{ $role->name }}</p>
            </div>
        </div>
        <div class="col-span-1">
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-2">Permissions:</label>
                <div class="grid grid-cols-3 gap-4">
                @if(!empty($rolePermissions))
                    @foreach($rolePermissions as $v)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $v->name }}
                        </span>
                    @endforeach
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
