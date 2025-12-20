@extends('layouts.app')

@section('header', 'Design Template')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Cinzel&family=EB+Garamond&family=Grand+Hotel&family=Inter&family=Montserrat&family=Open+Sans&family=Pacifico&family=Playfair+Display&family=Poppins&family=Roboto&family=Work+Sans&display=swap" rel="stylesheet">
<style>
    /* Font Families */
    .font-helvetica { font-family: 'Helvetica', 'Arial', sans-serif; }
    .font-futura { font-family: 'Futura', 'Trebuchet MS', sans-serif; }
    .font-roboto { font-family: 'Roboto', sans-serif; }
    .font-inter { font-family: 'Inter', sans-serif; }
    .font-poppins { font-family: 'Poppins', sans-serif; }
    .font-montserrat { font-family: 'Montserrat', sans-serif; }
    .font-opensans { font-family: 'Open Sans', sans-serif; }
    .font-avenir { font-family: 'Avenir', 'Nunito', sans-serif; }
    .font-worksans { font-family: 'Work Sans', sans-serif; }
    .font-din { font-family: 'DIN', 'Oswald', sans-serif; }
    
    .font-garamond { font-family: 'Garamond', serif; }
    .font-bodoni { font-family: 'Bodoni MT', 'Libre Bodoni', serif; }
    .font-didot { font-family: 'Didot', 'Didot LT STD', serif; }
    .font-baskerville { font-family: 'Baskerville', 'Baskerville Old Face', serif; }
    .font-ebgaramond { font-family: 'EB Garamond', serif; }
    
    .font-playfair { font-family: 'Playfair Display', serif; }
    .font-pacifico { font-family: 'Pacifico', cursive; }
    .font-cinzel { font-family: 'Cinzel', serif; }
    .font-alfaslab { font-family: 'Alfa Slab One', cursive; }
    .font-grandhotel { font-family: 'Grand Hotel', cursive; }
</style>
@endpush

@section('content')
<div class="flex flex-col h-[calc(100vh-8rem)]">
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 mx-4 mt-4" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline">There were some problems with your input.</span>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="flex flex-1 overflow-hidden">
    <!-- Sidebar Tools -->
    <div class="w-64 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700">Tools</h3>
        </div>
        
        <div class="p-4 space-y-4 overflow-y-auto flex-1">
            <!-- Template Settings -->
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Template Settings</h4>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Template Name</label>
                        <input type="text" id="template-name" value="{{ $template->name ?? 'New Template' }}" onchange="updateTemplateName(this.value)" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Active</label>
                        <input type="checkbox" name="is_active" id="template-active" {{ isset($template) && $template->is_active ? 'checked' : '' }} onchange="updateTemplateActive()" class="w-5 h-5">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">School</label>
                        <select id="school-select" onchange="updateSchool(this.value)" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                            <option value="">Select School</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ (isset($template) && $template->school_id == $school->id) ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                        
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Role</label>
                        <select id="role-select" onchange="updateRole(this.value)" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                            <option value="student" {{ (isset($template) && $template->role == 'student') ? 'selected' : '' }}>Student</option>
                            <option value="staff" {{ (isset($template) && $template->role == 'staff') ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Width</label>
                            <input type="number" id="template-width" value="{{ $template->width ?? 350 }}" onchange="updateCanvasSize()" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Height</label>
                            <input type="number" id="template-height" value="{{ $template->height ?? 550 }}" onchange="updateCanvasSize()" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Text -->
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Text</h4>
                <button onclick="addText()" class="w-full flex items-center px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm text-gray-700 transition-colors">
                    <i class="fas fa-font w-5"></i> Add Text
                </button>
            </div>

            <!-- Add Shapes -->
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Shapes</h4>
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="addRect()" class="flex items-center justify-center px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm text-gray-700 transition-colors">
                        <i class="far fa-square"></i>
                    </button>
                    <button onclick="addCircle()" class="flex items-center justify-center px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm text-gray-700 transition-colors">
                        <i class="far fa-circle"></i>
                    </button>
                </div>
            </div>

            <!-- Background & Images -->
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Images</h4>
                <button onclick="document.getElementById('img-upload').click()" class="w-full flex items-center px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm text-gray-700 transition-colors mb-2">
                    <i class="fas fa-plus-circle w-5"></i> Add Image
                </button>
                <input type="file" id="img-upload" class="hidden" accept="image/*" onchange="handleAddImage(this)">
                
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-4">Background</h4>
                <button onclick="document.getElementById('bg-upload').click()" class="w-full flex items-center px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded-md text-sm text-gray-700 transition-colors">
                    <i class="fas fa-image w-5"></i> Upload Background
                </button>
                <input type="file" id="bg-upload" class="hidden" accept="image/*" onchange="handleBackgroundUpload(this)">
                <button onclick="removeBackground()" class="w-full flex items-center px-3 py-2 mt-2 bg-red-50 hover:bg-red-100 rounded-md text-sm text-red-600 transition-colors">
                    <i class="fas fa-trash w-5"></i> Remove Background
                </button>
            </div>

            <!-- Placeholders -->
            <div>
                 <!-- Student Placeholders -->
                <div id="student-placeholders" class="{{ (isset($template) && $template->role == 'staff') ? 'hidden' : '' }}">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Student Placeholders</h4>
                    <div class="space-y-2">
                        <button onclick="addPlaceholder('full_name', 'Student Name')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-user w-5"></i> Name
                        </button>
                        <button onclick="addPlaceholder('student_id', 'Student ID')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-id-badge w-5"></i> Student ID
                        </button>
                        <button type="button" onclick="addPlaceholder('class_with_section', 'Class & Section')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-graduation-cap w-5"></i> Class
                        </button>
                        <button onclick="addPlaceholder('dob', 'Date of Birth')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-birthday-cake w-5"></i> Date of Birth
                        </button>
                    </div>
                </div>

                <!-- Staff Placeholders -->
                <div id="staff-placeholders" class="{{ (isset($template) && $template->role == 'staff') ? '' : 'hidden' }}">
                     <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Staff Placeholders</h4>
                     <div class="space-y-2">
                        <button onclick="addPlaceholder('full_name', 'Staff Name')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-user w-5"></i> Name
                        </button>
                        <button onclick="addPlaceholder('staff_id', 'Staff ID')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-id-badge w-5"></i> Staff ID
                        </button>
                        <button onclick="addPlaceholder('dob', 'Date of Birth')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-birthday-cake w-5"></i> Date of Birth
                        </button>
                         <button onclick="addPlaceholder('designation', 'Designation')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-briefcase w-5"></i> Designation
                        </button>
                         <button onclick="addPlaceholder('department', 'Department')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-building w-5"></i> Department
                        </button>
                         <button onclick="addPlaceholder('joining_date', 'Joining Date')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-calendar-check w-5"></i> Joining Date
                        </button>
                         <button onclick="addPlaceholder('phone', 'Phone')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-phone w-5"></i> Phone
                        </button>
                         <button onclick="addPlaceholder('email', 'Email')" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-envelope w-5"></i> Email
                        </button>
                    </div>
                </div>
            
                <!-- Photo with Shape Selector -->
                <div class="mb-3 mt-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Photo Placeholder</h3>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="addPhotoPlaceholder('rectangle')" class="flex items-center justify-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-square"></i>
                        </button>
                        <button type="button" onclick="addPhotoPlaceholder('rounded')" class="flex items-center justify-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-square-full"></i>
                        </button>
                        <button type="button" onclick="addPhotoPlaceholder('circle')" class="flex items-center justify-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-circle"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Other Elements</h3>
                    <div class="space-y-2">
                         <button onclick="addIssueDate()" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-calendar w-5"></i> Issue Date
                        </button>
                         <button onclick="addExpiryDate()" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                             <i class="fas fa-calendar w-5"></i> Expiry Date
                        </button>
                        <button onclick="addQRCode()" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-qrcode w-5"></i> QR Code
                        </button>
                        <button onclick="addFingerprint()" class="w-full flex items-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md text-sm transition-colors">
                            <i class="fas fa-fingerprint w-5"></i> Fingerprint
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Properties Panel (Dynamic) -->
            <div id="properties-panel" class="hidden border-t border-gray-200 pt-4 mt-4">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Properties</h4>
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Width</label>
                            <input type="number" id="prop-width" onchange="updateDimension('width', this.value)" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Height</label>
                            <input type="number" id="prop-height" onchange="updateDimension('height', this.value)" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Color</label>
                        <input type="color" id="prop-color" onchange="updateProperty('fill', this.value)" class="w-full h-8 rounded cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Font Family</label>
                        <select id="prop-fontfamily" onchange="updateProperty('fontFamily', this.value)" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                            <optgroup label="Modern / Graphic">
                                <option value="Helvetica" style="font-family: 'Helvetica', 'Arial', sans-serif;">Helvetica</option>
                                <option value="Futura" style="font-family: 'Futura', 'Trebuchet MS', sans-serif;">Futura</option>
                                <option value="Roboto" style="font-family: 'Roboto', sans-serif;">Roboto</option>
                                <option value="Inter" style="font-family: 'Inter', sans-serif;">Inter</option>
                                <option value="Poppins" style="font-family: 'Poppins', sans-serif;">Poppins</option>
                                <option value="Montserrat" style="font-family: 'Montserrat', sans-serif;">Montserrat</option>
                                <option value="Open Sans" style="font-family: 'Open Sans', sans-serif;">Open Sans</option>
                                <option value="Avenir" style="font-family: 'Avenir', 'Nunito', sans-serif;">Avenir</option>
                                <option value="Work Sans" style="font-family: 'Work Sans', sans-serif;">Work Sans</option>
                                <option value="DIN" style="font-family: 'DIN', 'Oswald', sans-serif;">DIN</option>
                            </optgroup>
                            
                            <optgroup label="Traditional / Serif">
                                <option value="Garamond" style="font-family: 'Garamond', serif;">Garamond</option>
                                <option value="Bodoni MT" style="font-family: 'Bodoni MT', serif;">Bodoni</option>
                                <option value="Didot" style="font-family: 'Didot', serif;">Didot</option>
                                <option value="Baskerville" style="font-family: 'Baskerville', serif;">Baskerville</option>
                                <option value="EB Garamond" style="font-family: 'EB Garamond', serif;">EB Garamond</option>
                            </optgroup>
                            
                            <optgroup label="Styling / Display">
                                <option value="Playfair Display" style="font-family: 'Playfair Display', serif;">Playfair Display</option>
                                <option value="Pacifico" style="font-family: 'Pacifico', cursive;">Pacifico</option>
                                <option value="Cinzel" style="font-family: 'Cinzel', serif;">Cinzel</option>
                                <option value="Alfa Slab One" style="font-family: 'Alfa Slab One', cursive;">Alfa Slab One</option>
                                <option value="Grand Hotel" style="font-family: 'Grand Hotel', cursive;">Grand Hotel</option>
                            </optgroup>

                            <optgroup label="Other">
                                <option value="Arial" style="font-family: Arial;">Arial</option>
                                <option value="Times New Roman" style="font-family: 'Times New Roman';">Times New Roman</option>
                                <option value="Courier New" style="font-family: 'Courier New';">Courier New</option>
                                <option value="Verdana" style="font-family: Verdana;">Verdana</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Font Size</label>
                        <input type="number" id="prop-fontsize" onchange="updateProperty('fontSize', parseInt(this.value))" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                    </div>
                    <div class="flex space-x-1">
                        <button onclick="toggleFontStyle('bold')" id="btn-bold" class="flex-1 px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded border border-gray-200 font-bold" title="Bold">B</button>
                        <button onclick="toggleFontStyle('italic')" id="btn-italic" class="flex-1 px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded border border-gray-200 italic" title="Italic">I</button>
                        <button onclick="toggleFontStyle('underline')" id="btn-underline" class="flex-1 px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded border border-gray-200 underline" title="Underline">U</button>
                    </div>
                    <div class="flex space-x-1">
                        <button onclick="toggleTextAlign('left')" id="btn-align-left" class="flex-1 px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded border border-gray-200" title="Align Left"><i class="fas fa-align-left"></i></button>
                        <button onclick="toggleTextAlign('center')" id="btn-align-center" class="flex-1 px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded border border-gray-200" title="Align Center"><i class="fas fa-align-center"></i></button>
                        <button onclick="toggleTextAlign('right')" id="btn-align-right" class="flex-1 px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded border border-gray-200" title="Align Right"><i class="fas fa-align-right"></i></button>
                        <button onclick="toggleTextAlign('justify')" id="btn-align-justify" class="flex-1 px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded border border-gray-200" title="Justify"><i class="fas fa-align-justify"></i></button>
                    </div>
                    <button onclick="deleteSelected()" class="w-full px-3 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-md text-sm transition-colors">
                        Delete Selected
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Canvas Area -->
    <div class="flex-1 bg-gray-100 flex flex-col relative overflow-hidden">
        <!-- Toolbar -->
        <div class="bg-white border-b border-gray-200 p-2 flex justify-between items-center">
            <div class="flex space-x-2">
                <button onclick="setSide('front')" id="btn-front" class="px-4 py-2 rounded-md text-sm font-medium bg-indigo-600 text-white">Front Side</button>
                <button onclick="setSide('back')" id="btn-back" class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100">Back Side</button>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex space-x-1 mr-4 border-r border-gray-200 pr-4">
                    <button onclick="undo()" id="btn-undo" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors" title="Undo (Ctrl+Z)">
                        <i class="fas fa-undo"></i>
                    </button>
                    <button onclick="redo()" id="btn-redo" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors" title="Redo (Ctrl+Y)">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600">Zoom:</label>
                    <input type="range" id="zoom-slider" min="50" max="200" value="100" oninput="setZoom(this.value)" class="w-32">
                    <span id="zoom-val" class="text-sm text-gray-600">100%</span>
                </div>
                <button onclick="saveTemplate()" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 transition-colors">
                    Save Template
                </button>
            </div>
        </div>

        <!-- Canvas Container -->
        <div class="flex-1 overflow-auto flex items-center justify-center p-8" id="canvas-wrapper">
            <div class="shadow-lg bg-white" id="canvas-container-front">
                <canvas id="canvas-front"></canvas>
            </div>
            <div class="shadow-lg bg-white hidden" id="canvas-container-back">
                <canvas id="canvas-back"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for Saving -->
<form id="save-form" action="{{ (isset($template) && $template->exists) ? route('id-card-templates.update', $template) : route('id-card-templates.store') }}" method="POST" class="hidden">
    @csrf
    @if(isset($template) && $template->exists)
        @method('PUT')
    @endif
    <input type="hidden" name="name" id="hidden-name" value="{{ $template->name ?? 'New Template' }}">
    <input type="hidden" name="school_id" id="hidden-school-id" value="{{ $template->school_id ?? '' }}">
    <input type="hidden" name="role" id="hidden-role" value="{{ $template->role ?? 'student' }}">
    <input type="hidden" name="active" id="hidden-active" value="{{ isset($template) && $template->is_active ? '1' : '0' }}">
    <input type="hidden" name="width" id="hidden-width" value="{{ $template->width ?? 350 }}">
    <input type="hidden" name="height" id="hidden-height" value="{{ $template->height ?? 550 }}">
    <input type="hidden" name="design_data" id="design_data">
    <input type="hidden" name="design_data_back" id="design_data_back">
</form>

@push('scripts')
<script src="https://unpkg.com/fabric@5.3.0/dist/fabric.min.js"></script>
<script>
    let canvasFront, canvasBack;
    let currentSide = 'front';
    let currentCanvas;
    
    // Undo/Redo State
    let history = [];
    let historyStep = -1;
    let isUndoing = false;
    const MAX_HISTORY = 50;

    document.addEventListener('DOMContentLoaded', function() {
        const width = {{ $template->width ?? 350 }};
        const height = {{ $template->height ?? 550 }};

        // Initialize Front Canvas
        canvasFront = new fabric.Canvas('canvas-front', {
            width: width,
            height: height,
            backgroundColor: '#ffffff',
            preserveObjectStacking: true
        });

        // Initialize Back Canvas
        canvasBack = new fabric.Canvas('canvas-back', {
            width: width,
            height: height,
            backgroundColor: '#ffffff',
            preserveObjectStacking: true
        });

        currentCanvas = canvasFront;

        // Load existing data if any
        const savedDataFront = @json($template->design_data ?? null);
        const savedDataBack = @json($template->design_data_back ?? null);

        // History Events
        const historyEvents = ['object:added', 'object:removed', 'object:modified', 'object:skewing', 'object:scaling', 'object:rotating', 'object:moving'];

        // Function to attach listeners
        function attachHistoryListeners() {
            historyEvents.forEach(event => {
                canvasFront.on(event, () => {
                    if (!isUndoing) saveHistory();
                });
                canvasBack.on(event, () => {
                    if (!isUndoing) saveHistory();
                });
            });
            
            // Selection listeners (safe to attach earlier but good to keep together)
            canvasFront.on('selection:created', onSelection);
            canvasFront.on('selection:updated', onSelection);
            canvasFront.on('object:scaling', onSelection);
            canvasFront.on('selection:cleared', onDeselection);
            
            canvasBack.on('selection:created', onSelection);
            canvasBack.on('selection:updated', onSelection);
            canvasBack.on('object:scaling', onSelection);
            canvasBack.on('selection:cleared', onDeselection);
        }

        // Load existing data if any
        const savedDataFront = @json($template->design_data ?? null);
        const savedDataBack = @json($template->design_data_back ?? null);
        
        let toLoad = 0;
        if (savedDataFront) toLoad++;
        if (savedDataBack) toLoad++;
        
        let loaded = 0;
        
        const initHistory = () => {
             loaded++;
             if (loaded >= toLoad) {
                 // Save initial state after everything is loaded
                 // Use a slight timeout to ensure render is complete
                 setTimeout(() => {
                     saveHistory();
                     attachHistoryListeners();
                 }, 100);
             }
        };

        if (savedDataFront) {
            canvasFront.loadFromJSON(savedDataFront, () => {
                canvasFront.renderAll();
                initHistory();
            });
        }
        
        if (savedDataBack) {
            canvasBack.loadFromJSON(savedDataBack, () => {
                canvasBack.renderAll();
                initHistory();
            });
        }
        
        if (toLoad === 0) {
            saveHistory();
            attachHistoryListeners();
        }

        // Keyboard support
        document.addEventListener('keydown', function(e) {
            // Check for Undo/Redo shortcuts first
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
                e.preventDefault();
                undo();
                return;
            }
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'y') {
                e.preventDefault();
                redo();
                return;
            }
            
            // Don't trigger other shortcuts if user is typing in an input
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

            if (!currentCanvas) return;
            const activeObject = currentCanvas.getActiveObject();

            if (activeObject) {
                 // Don't trigger if user is editing text on canvas
                if (activeObject.isEditing) return;

                const step = e.shiftKey ? 10 : 1;

                switch(e.key) {
                    case 'ArrowLeft':
                        activeObject.set('left', activeObject.left - step);
                        currentCanvas.renderAll();
                        if(!isUndoing) saveHistory(); // Save on move
                        e.preventDefault();
                        break;
                    case 'ArrowRight':
                        activeObject.set('left', activeObject.left + step);
                        currentCanvas.renderAll();
                        if(!isUndoing) saveHistory();
                        e.preventDefault();
                        break;
                    case 'ArrowUp':
                        activeObject.set('top', activeObject.top - step);
                        currentCanvas.renderAll();
                        if(!isUndoing) saveHistory();
                        e.preventDefault();
                        break;
                    case 'ArrowDown':
                        activeObject.set('top', activeObject.top + step);
                        currentCanvas.renderAll();
                        if(!isUndoing) saveHistory();
                        e.preventDefault();
                        break;
                    case 'Delete':
                    case 'Backspace':
                        deleteSelected();
                        e.preventDefault();
                        break;
                }
            }
        });
    });
    
    // History Functions
    function saveHistory() {
        if (isUndoing) return; // Don't save if we are currently undoing/redoing
        
        // Remove redo stack if we are in the middle of history
        if (historyStep < history.length - 1) {
            history = history.slice(0, historyStep + 1);
        }
        
        // Limit history size
        if (history.length > MAX_HISTORY) {
            history.shift();
            historyStep--;
        }

        const state = {
            front: JSON.stringify(canvasFront.toJSON()),
            back: JSON.stringify(canvasBack.toJSON()),
            side: currentSide
        };
        
        history.push(state);
        historyStep++;
        updateUndoRedoButtons();
    }
    
    function undo() {
        if (historyStep > 0) {
            isUndoing = true;
            historyStep--;
            const state = history[historyStep];
            loadState(state, () => {
                isUndoing = false;
                updateUndoRedoButtons();
            });
        }
    }
    
    function redo() {
        if (historyStep < history.length - 1) {
            isUndoing = true;
            historyStep++;
            const state = history[historyStep];
            loadState(state, () => {
                 isUndoing = false;
                 updateUndoRedoButtons();
            });
        }
    }
    
    function loadState(state, callback) {
        let loadedCount = 0;
        const totalCanvases = 2; // Front and Back

        const checkDone = () => {
            loadedCount++;
            if (loadedCount >= totalCanvases) {
                if (state.side !== currentSide) {
                     setSide(state.side);
                }
                if (callback) callback();
            }
        };
        
        canvasFront.loadFromJSON(state.front, () => {
             canvasFront.renderAll();
             checkDone();
        });
        
        canvasBack.loadFromJSON(state.back, () => {
             canvasBack.renderAll();
             checkDone();
        });
    }
    
    function updateUndoRedoButtons() {
        const btnUndo = document.getElementById('btn-undo');
        const btnRedo = document.getElementById('btn-redo');
        
        if (historyStep > 0) {
            btnUndo.classList.remove('text-gray-300', 'cursor-not-allowed');
            btnUndo.classList.add('text-gray-500', 'hover:bg-indigo-50', 'hover:text-indigo-600');
            btnUndo.disabled = false;
        } else {
            btnUndo.classList.add('text-gray-300', 'cursor-not-allowed');
            btnUndo.classList.remove('text-gray-500', 'hover:bg-indigo-50', 'hover:text-indigo-600');
            btnUndo.disabled = true;
        }
        
        if (historyStep < history.length - 1) {
            btnRedo.classList.remove('text-gray-300', 'cursor-not-allowed');
            btnRedo.classList.add('text-gray-500', 'hover:bg-indigo-50', 'hover:text-indigo-600');
            btnRedo.disabled = false;
        } else {
            btnRedo.classList.add('text-gray-300', 'cursor-not-allowed');
            btnRedo.classList.remove('text-gray-500', 'hover:bg-indigo-50', 'hover:text-indigo-600');
            btnRedo.disabled = true;
        }
    }

    function setSide(side) {
        currentSide = side;
        const btnFront = document.getElementById('btn-front');
        const btnBack = document.getElementById('btn-back');
        const containerFront = document.getElementById('canvas-container-front');
        const containerBack = document.getElementById('canvas-container-back');

        if (side === 'front') {
            btnFront.classList.add('bg-indigo-600', 'text-white');
            btnFront.classList.remove('text-gray-600', 'hover:bg-gray-100');
            btnBack.classList.remove('bg-indigo-600', 'text-white');
            btnBack.classList.add('text-gray-600', 'hover:bg-gray-100');
            
            containerFront.classList.remove('hidden');
            containerBack.classList.add('hidden');
            currentCanvas = canvasFront;
        } else {
            btnBack.classList.add('bg-indigo-600', 'text-white');
            btnBack.classList.remove('text-gray-600', 'hover:bg-gray-100');
            btnFront.classList.remove('bg-indigo-600', 'text-white');
            btnFront.classList.add('text-gray-600', 'hover:bg-gray-100');
            
            containerBack.classList.remove('hidden');
            containerFront.classList.add('hidden');
            currentCanvas = canvasBack;
        }
        
        // Re-render to ensure proper display
        currentCanvas.requestRenderAll();
    }

    function addText() {
        const text = new fabric.IText('Double click to edit', {
            left: 50,
            top: 50,
            fontFamily: 'Arial',
            fill: '#333333',
            fontSize: 20
        });
        currentCanvas.add(text);
        currentCanvas.setActiveObject(text);
    }

    function addRect() {
        const rect = new fabric.Rect({
            left: 50,
            top: 50,
            fill: '#indigo',
            width: 100,
            height: 100
        });
        currentCanvas.add(rect);
        currentCanvas.setActiveObject(rect);
    }

    function addCircle() {
        const circle = new fabric.Circle({
            left: 50,
            top: 50,
            fill: '#indigo',
            radius: 50
        });
        currentCanvas.add(circle);
        currentCanvas.setActiveObject(circle);
    }

    function addPlaceholder(field, label) {
        if (!currentCanvas) {
            console.error('Canvas not initialized yet');
            alert('Please wait for the canvas to load before adding placeholders.');
            return;
        }
        const placeholderText = '@{{' + label + '}}';
        const text = new fabric.IText(placeholderText, {
            left: 50,
            top: 50,
            fontFamily: 'Arial',
            fill: '#000000',
            fontSize: 18,
            data: { field: field }
        });
        currentCanvas.add(text);
        currentCanvas.setActiveObject(text);
        currentCanvas.renderAll();
    }

    function addPhotoPlaceholder(shape = 'rectangle') {
        if (!currentCanvas) {
            console.error('Canvas not initialized yet');
            alert('Please wait for the canvas to load.');
            return;
        }
        
        const width = 100;
        const height = 120;
        let shapeObj;
        
        // Create the actual shape that will be visible
        if (shape === 'circle') {
            const diameter = Math.min(width, height);
            shapeObj = new fabric.Circle({
                left: 50,
                top: 50,
                radius: diameter / 2,
                fill: '#e0e0e0',
                stroke: '#333333',
                strokeWidth: 2,
                data: { field: 'photo', shape: 'circle' }
            });
        } else if (shape === 'rounded') {
            shapeObj = new fabric.Rect({
                left: 50,
                top: 50,
                width: width,
                height: height,
                rx: 15,
                ry: 15,
                fill: '#e0e0e0',
                stroke: '#333333',
                strokeWidth: 2,
                data: { field: 'photo', shape: 'rounded' }
            });
        } else {
            shapeObj = new fabric.Rect({
                left: 50,
                top: 50,
                width: width,
                height: height,
                fill: '#e0e0e0',
                stroke: '#333333',
                strokeWidth: 2,
                data: { field: 'photo', shape: 'rectangle' }
            });
        }
        
        currentCanvas.add(shapeObj);
        currentCanvas.setActiveObject(shapeObj);
        currentCanvas.renderAll();
    }

    function addIssueDate() {
        if (!currentCanvas) {
            console.error('Canvas not initialized yet');
            alert('Please wait for the canvas to load.');
            return;
        }
        
        const text = new fabric.IText('Issue Date', {
            left: 50,
            top: 50,
            fontFamily: 'Arial',
            fill: '#000000',
            fontSize: 14,
            data: { field: 'issue_date' }
        });
        
        currentCanvas.add(text);
        currentCanvas.setActiveObject(text);
        currentCanvas.renderAll();
    }

    function addExpiryDate() {
        if (!currentCanvas) {
            console.error('Canvas not initialized yet');
            alert('Please wait for the canvas to load.');
            return;
        }
        
        const text = new fabric.IText('Expiry Date', {
            left: 50,
            top: 50,
            fontFamily: 'Arial',
            fill: '#000000',
            fontSize: 14,
            data: { field: 'expiry_date' }
        });
        
        currentCanvas.add(text);
        currentCanvas.setActiveObject(text);
        currentCanvas.renderAll();
    }

    function addQRCode() {
        if (!currentCanvas) {
            console.error('Canvas not initialized yet');
            alert('Please wait for the canvas to load.');
            return;
        }
        
        const rect = new fabric.Rect({
            left: 0,
            top: 0,
            width: 80,
            height: 80,
            fill: '#ffffff',
            stroke: '#000000',
            strokeWidth: 2
        });
        
        const text = new fabric.Text('QR', {
            fontSize: 14,
            fontWeight: 'bold',
            fill: '#666',
            originX: 'center',
            originY: 'center',
            left: 40,
            top: 40
        });
        
        // Group them together
        const group = new fabric.Group([rect, text], {
            left: 50,
            top: 50,
            data: { field: 'qr_code' }
        });
        
        currentCanvas.add(group);
        currentCanvas.setActiveObject(group);
        currentCanvas.renderAll();
    }

    function addFingerprint() {
        if (!currentCanvas) {
            console.error('Canvas not initialized yet');
            alert('Please wait for the canvas to load.');
            return;
        }
        
        const rect = new fabric.Rect({
            left: 0,
            top: 0,
            width: 60,
            height: 80,
            fill: '#eeeeee',
            stroke: '#333333',
            strokeWidth: 1
        });
        
        const text = new fabric.Text('FP', {
            fontSize: 12,
            fontWeight: 'bold',
            fill: '#666',
            originX: 'center',
            originY: 'center',
            left: 30,
            top: 40
        });
        
        // Group them together
        const group = new fabric.Group([rect, text], {
            left: 50,
            top: 50,
            data: { field: 'fingerprint' }
        });
        
        currentCanvas.add(group);
        currentCanvas.setActiveObject(group);
        currentCanvas.renderAll();
    }

    function deleteSelected() {
        const activeObject = currentCanvas.getActiveObject();
        if (activeObject) {
            currentCanvas.remove(activeObject);
            currentCanvas.discardActiveObject();
            ;
        }
    }

    function onSelection(e) {
        if (!e.selected || e.selected.length === 0) return;
        const obj = e.selected[0];
        document.getElementById('properties-panel').classList.remove('hidden');
        
        // Update inputs based on selection
        if (obj.fill && typeof obj.fill === 'string') document.getElementById('prop-color').value = obj.fill;
        if (obj.fontSize) document.getElementById('prop-fontsize').value = obj.fontSize;
        if (obj.fontFamily) document.getElementById('prop-fontfamily').value = obj.fontFamily;
        
        // Update font style buttons
        if (obj.fontWeight === 'bold') {
            document.getElementById('btn-bold').classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
        } else {
            document.getElementById('btn-bold').classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
        }
        
        if (obj.fontStyle === 'italic') {
            document.getElementById('btn-italic').classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
        } else {
            document.getElementById('btn-italic').classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
        }
        
        if (obj.underline) {
            document.getElementById('btn-underline').classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
        } else {
            document.getElementById('btn-underline').classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
        }
        
        // Update alignment buttons
        const align = obj.textAlign || 'left';
        ['left', 'center', 'right', 'justify'].forEach(a => {
            const btn = document.getElementById('btn-align-' + a);
            if (a === align) {
                btn.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
            } else {
                btn.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
            }
        });

        // Update dimensions
        document.getElementById('prop-width').value = Math.round(obj.width * obj.scaleX);
        document.getElementById('prop-height').value = Math.round(obj.height * obj.scaleY);
    }

    function onDeselection() {
        document.getElementById('properties-panel').classList.add('hidden');
    }
    function updateProperty(prop, value) {
        const activeObject = currentCanvas.getActiveObject();
        if (activeObject) {
            activeObject.set(prop, value);
            currentCanvas.requestRenderAll();
            if(!isUndoing) saveHistory();
        }
    }
    
    function toggleTextAlign(align) {
        const activeObject = currentCanvas.getActiveObject();
        if (activeObject) {
            // Determine new originX based on alignment
            let newOriginX = 'left';
            if (align === 'center') {
                newOriginX = 'center';
            } else if (align === 'right') {
                newOriginX = 'right';
            }
            
            // Get current point for the NEW origin
            // This ensures the object stays visually in place
            const p = activeObject.getPointByOrigin(newOriginX, activeObject.originY);
            
            activeObject.set({
                textAlign: align,
                originX: newOriginX,
                left: p.x,
                top: p.y // technically originY didn't change so Top should be fine, but setPositionByOrigin sets both
            });
            
            // We can also use setPositionByOrigin but we need to manually set the props after
            // activeObject.setPositionByOrigin(p, newOriginX, activeObject.originY); 
            // activeObject.set('textAlign', align);

            currentCanvas.requestRenderAll();
            
            // Update buttons
            ['left', 'center', 'right', 'justify'].forEach(a => {
                const btn = document.getElementById('btn-align-' + a);
                if (a === align) {
                    btn.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                } else {
                    btn.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                }
            });
            
            if(!isUndoing) saveHistory();
        }
    }
    
    function toggleFontStyle(style) {
        const activeObject = currentCanvas.getActiveObject();
        if (!activeObject) return;

        if (style === 'bold') {
            const isBold = activeObject.fontWeight === 'bold';
            activeObject.set('fontWeight', isBold ? 'normal' : 'bold');
            
            // Toggle button view
            const btn = document.getElementById('btn-bold');
            if (!isBold) {
                 btn.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
            } else {
                 btn.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
            }
        } else if (style === 'italic') {
            const isItalic = activeObject.fontStyle === 'italic';
            activeObject.set('fontStyle', isItalic ? 'normal' : 'italic');
            
            // Toggle button view
            const btn = document.getElementById('btn-italic');
            if (!isItalic) {
                 btn.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
            } else {
                 btn.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
            }
        } else if (style === 'underline') {
            const isUnderline = activeObject.underline;
            activeObject.set('underline', !isUnderline);
            
            // Toggle button view
            const btn = document.getElementById('btn-underline');
            if (!isUnderline) {
                 btn.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
            } else {
                 btn.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
            }
        }
        
        currentCanvas.requestRenderAll();
        if(!isUndoing) saveHistory();
    }
    
    function updateDimension(prop, value) {
        const activeObject = currentCanvas.getActiveObject();
        if (activeObject) {
            const val = parseInt(value);
            if (prop === 'width') {
                activeObject.scaleToWidth(val);
            } else if (prop === 'height') {
                activeObject.scaleToHeight(val);
            }
            currentCanvas.requestRenderAll();
        }
    }



    function removeBackground() {
        currentCanvas.setBackgroundImage(null, currentCanvas.renderAll.bind(currentCanvas));
        currentCanvas.backgroundColor = '#ffffff';
        ;
    }

    function setZoom(value) {
        const zoom = parseInt(value) / 100;
        document.getElementById('zoom-val').innerText = value + '%';
        
        // Get current logical dimensions from inputs
        const width = parseInt(document.getElementById('template-width').value) || {{ $template->width ?? 350 }};
        const height = parseInt(document.getElementById('template-height').value) || {{ $template->height ?? 550 }};
        
        canvasFront.setZoom(zoom);
        canvasFront.setWidth(width * zoom);
        canvasFront.setHeight(height * zoom);
        
        canvasBack.setZoom(zoom);
        canvasBack.setWidth(width * zoom);
        canvasBack.setHeight(height * zoom);
        
        canvasFront.requestRenderAll();
        canvasBack.requestRenderAll();
    }

    function updateTemplateName(value) {
        document.getElementById('hidden-name').value = value;
    }
    
    function updateTemplateActive() {
        const activeCheckbox = document.getElementById('template-active');
        document.getElementById('hidden-active').value = activeCheckbox.checked ? '1' : '0';
    }
    
    function updateSchool(value) {
        document.getElementById('hidden-school-id').value = value;
    }

    function updateRole(value) {
        document.getElementById('hidden-role').value = value;
        
        // Toggle placeholders
        const studentPlaceholders = document.getElementById('student-placeholders');
        const staffPlaceholders = document.getElementById('staff-placeholders');
        
        if (value === 'student') {
            studentPlaceholders.classList.remove('hidden');
            staffPlaceholders.classList.add('hidden');
        } else {
            studentPlaceholders.classList.add('hidden');
            staffPlaceholders.classList.remove('hidden');
        }
    }

    function updateCanvasSize() {
        const width = parseInt(document.getElementById('template-width').value);
        const height = parseInt(document.getElementById('template-height').value);
        
        // Get current zoom
        const zoomVal = document.getElementById('zoom-slider').value;
        const zoom = parseInt(zoomVal) / 100;
        
        if (width && height) {
            canvasFront.setWidth(width * zoom);
            canvasFront.setHeight(height * zoom);
            canvasBack.setWidth(width * zoom);
            canvasBack.setHeight(height * zoom);
            
            // Update hidden inputs for saving
            document.querySelector('input[name="width"]').value = width;
            document.querySelector('input[name="height"]').value = height;
            
            // Re-render
            canvasFront.requestRenderAll();
            canvasBack.requestRenderAll();
        }
    }

    // Helper to resize images before adding to canvas
    function resizeImage(file, maxWidth, callback) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                
                if (width > maxWidth) {
                    height = Math.round(height * (maxWidth / width));
                    width = maxWidth;
                }
                
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                
                callback(canvas.toDataURL('image/jpeg', 0.8)); // Compress slightly
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function handleAddImage(input) {
        if (input.files && input.files[0]) {
            // Resize to max 800px width to save memory/perf
            resizeImage(input.files[0], 800, function(dataUrl) {
                fabric.Image.fromURL(dataUrl, function(img) {
                    // Initial display size (visual only)
                    if (img.width > 200) {
                        img.scaleToWidth(200);
                    }
                    
                    img.set({
                        left: 50,
                        top: 50
                    });
                    
                    currentCanvas.add(img);
                    currentCanvas.setActiveObject(img);
                    currentCanvas.requestRenderAll();
                });
            });
        }
        input.value = '';
    }

    function handleBackgroundUpload(input) {
        if (input.files && input.files[0]) {
            // Resize to max 1200px width for background
            resizeImage(input.files[0], 1200, function(dataUrl) {
                fabric.Image.fromURL(dataUrl, function(img) {
                    // Remove existing background object if any
                    const objects = currentCanvas.getObjects();
                    objects.forEach(function(obj) {
                        if (obj.data && obj.data.type === 'background') {
                            currentCanvas.remove(obj);
                        }
                    });

                    // Get logical dimensions
                    const width = parseInt(document.getElementById('template-width').value) || {{ $template->width ?? 350 }};
                    const height = parseInt(document.getElementById('template-height').value) || {{ $template->height ?? 550 }};
                    
                    // Calculate scale to cover the canvas while maintaining aspect ratio
                    const scale = Math.max(width / img.width, height / img.height);
                    
                    img.set({
                        originX: 'center',
                        originY: 'center',
                        left: width / 2,
                        top: height / 2,
                        scaleX: scale,
                        scaleY: scale,
                        data: { type: 'background' } // Mark as background
                    });
                    
                    currentCanvas.add(img);
                    img.sendToBack();
                    currentCanvas.setActiveObject(img);
                    currentCanvas.requestRenderAll();
                });
            });
        }
        // Reset input so same file can be selected again
        input.value = '';
    }

    function saveTemplate() {
        try {
            // console.log('Starting saveTemplate...');
            
            // Sync values one last time
            const nameInput = document.getElementById('template-name');
            const schoolSelect = document.getElementById('school-select');
            const roleSelect = document.getElementById('role-select');
            
            if (!nameInput.value) {
                alert('Please enter a template name.');
                return;
            }
            if (!schoolSelect.value) {
                alert('Please select a school.');
                return;
            }

            document.getElementById('hidden-name').value = nameInput.value;
            document.getElementById('hidden-school-id').value = schoolSelect.value;
            
            // Explicitly sync active status
            const activeCheckbox = document.getElementById('template-active');
            if (activeCheckbox) {
                document.getElementById('hidden-active').value = activeCheckbox.checked ? '1' : '0';
            }
            
            console.log('Checking canvases...');
            if (typeof canvasFront === 'undefined') {
                alert('Error: Front canvas not initialized!');
                return;
            }
            if (typeof canvasBack === 'undefined') {
                alert('Error: Back canvas not initialized!');
                return;
            }
            
            console.log('Validating front canvas objects...');
            const frontObjects = canvasFront.getObjects();
            console.log('Front canvas has', frontObjects.length, 'objects');
            
            // Try to serialize without the 'data' parameter first
            console.log('Serializing front canvas...');
            let jsonFront;
            try {
                // Include custom properties when serializing
                const frontData = canvasFront.toJSON(['data']);
                jsonFront = JSON.stringify(frontData);
                console.log('Front canvas serialized successfully');
            } catch (e) {
                console.error('Error serializing front canvas:', e);
                alert('Error saving front canvas: ' + e.message + '\nTry removing recent objects and save again.');
                return;
            }
            
            console.log('Serializing back canvas...');
            let jsonBack;
            try {
                const backData = canvasBack.toJSON(['data']);
                jsonBack = JSON.stringify(backData);
                console.log('Back canvas serialized successfully');
            } catch (e) {
                console.error('Error serializing back canvas:', e);
                alert('Error saving back canvas: ' + e.message);
                return;
            }
            
            console.log('Setting form values...');
            document.getElementById('design_data').value = jsonFront;
            document.getElementById('design_data_back').value = jsonBack;
            
            console.log('Submitting form...');
            const form = document.getElementById('save-form');
            if (form) {
                form.submit();
            } else {
                alert('Error: Save form not found!');
            }
        } catch (e) {
            console.error('Unexpected error:', e);
            alert('An error occurred while saving: ' + e.message);
        }
    }
</script>
@endpush
@endsection
