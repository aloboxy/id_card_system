@extends('layouts.app')

@section('header', 'Template Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">{{ $template->name }}</h3>
            <div class="flex space-x-3">
                <a href="{{ route('id-card-templates.edit', $template) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Edit Design
                </a>
            </div>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Details</h4>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Dimensions</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $template->width }} x {{ $template->height }} px</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($template->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $template->description ?? 'No description provided.' }}</dd>
                    </div>
                </dl>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Preview</h4>
                <div class="border border-gray-200 rounded bg-gray-50 flex items-center justify-center p-4">
                    <canvas id="preview-canvas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Generate ID Cards</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-600 mb-4">Select students to generate ID cards using this template.</p>
            <!-- Placeholder for generation form -->
            <a href="{{ route('id-card-templates.generate', $template) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                Generate for All Active Students
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/fabric@5.3.0/dist/fabric.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = new fabric.StaticCanvas('preview-canvas', {
            width: {{ $template->width }},
            height: {{ $template->height }},
            backgroundColor: '#ffffff',
        });

        // Scale down preview if too large
        const containerWidth = document.getElementById('preview-canvas').parentElement.clientWidth - 32;
        if ({{ $template->width }} > containerWidth) {
            const scale = containerWidth / {{ $template->width }};
            canvas.setZoom(scale);
            canvas.setWidth({{ $template->width }} * scale);
            canvas.setHeight({{ $template->height }} * scale);
        }

        const designData = @json($template->design_data);
        if (designData) {
            canvas.loadFromJSON(designData, function() {
                canvas.renderAll();
            });
        }
    });
</script>
@endpush
@endsection
