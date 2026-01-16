@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css?v={{ time() }}">
<style>
    .img-container {
        width: 100%;
        min-height: 400px;
        max-height: 500px;
        background-color: #f3f4f6;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .img-container img {
        display: block;
        max-width: 100%;
        max-height: 100%;
    }
</style>
@endpush

@section('header', 'Add Student')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Student Details</h3>
        </div>
        <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="school_id" class="block text-sm font-medium text-gray-700">School</label>
                    <select name="school_id" id="school_id" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                        <option value="">Select School</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                    @error('school_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                    <input type="text" name="student_id" id="student_id" value="{{ old('student_id') }}" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate based on school code (e.g., CMFI-0001)</p>
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-full grid grid-cols-3 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                        <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" >
                        @error('middle_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Profile Photo --}}
                <div class="md:col-span-2">
                    <label for="profile_photo" class="block text-sm font-medium text-gray-700">Profile Photo (Optional)</label>
                    
                    {{-- Preview Area --}}
                    <div id="profile-photo-preview" class="hidden mb-3 mt-2">
                        <img id="profile-photo-img" src="" alt="Profile Photo Preview" class="h-32 w-32 object-cover border-2 border-gray-300 rounded-lg shadow-sm">
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*" onchange="previewProfilePhoto(event)" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <button type="button" onclick="toggleCamera('profile')" class="mt-1 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-camera mr-2"></i> Use Camera
                        </button>
                        <button type="button" id="crop-button" onclick="openCropModal()" class="mt-1 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden">
                            <i class="fas fa-crop-alt mr-2"></i> Crop
                        </button>
                    </div>

                    {{-- Cropping Modal --}}
                    <div id="crop-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeCropModal()"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">Crop Profile Photo</h3>
                                        <button type="button" onclick="closeCropModal()" class="text-gray-400 hover:text-gray-500">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="img-container rounded-lg overflow-hidden border border-gray-200 shadow-inner">
                                        <img id="cropper-image" src="" crossorigin="anonymous">
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="button" onclick="applyCrop()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Apply Crop
                                    </button>
                                    <button type="button" onclick="closeCropModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Camera UI --}}
                    <div id="camera-container-profile" class="hidden mt-3 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="camera-select-profile" class="block text-sm font-medium text-gray-700 mb-1">Select Camera</label>
                                <select id="camera-select-profile" onchange="changeCamera('profile')" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                                    <option value="user">Front Camera (Selfie)</option>
                                    <option value="environment" selected>Back Camera (Main)</option>
                                </select>
                            </div>
                            <div id="zoom-container-profile" class="hidden">
                                <label for="zoom-range-profile" class="block text-sm font-medium text-gray-700 mb-1">Zoom</label>
                                <input type="range" id="zoom-range-profile" oninput="applyZoom('profile', this.value)" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600 mt-2">
                            </div>
                        </div>
                        <div class="relative max-w-sm mx-auto">
                            <video id="video-profile" autoplay playsinline class="w-full rounded-lg bg-black"></video>
                            <canvas id="canvas-profile" class="hidden"></canvas>
                        </div>
                        <div class="mt-3 flex justify-center space-x-3">
                            <button type="button" onclick="capturePhoto('profile')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Capture
                            </button>
                            <button type="button" onclick="stopCamera('profile')" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Cancel
                            </button>
                        </div>
                    </div>
                    @error('profile_photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fingerprint Image --}}
                <div class="md:col-span-2">
                    <label for="fingerprint_image" class="block text-sm font-medium text-gray-700">Fingerprint Image (Optional)</label>
                    
                    {{-- Preview Area --}}
                    <div id="fingerprint-preview" class="hidden mb-3 mt-2">
                        <img id="fingerprint-img" src="" alt="Fingerprint Preview" class="h-32 w-32 object-contain border-2 border-gray-300 rounded-lg shadow-sm bg-gray-50">
                    </div>
                    
                    <input type="file" name="fingerprint_image" id="fingerprint_image" accept="image/*" onchange="previewFingerprint(event)" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('fingerprint_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="admission_number" class="block text-sm font-medium text-gray-700">Admission Number</label>
                    <input type="text" name="admission_number" id="admission_number" value="{{ old('admission_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to use same as Student ID</p>
                    @error('admission_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="class" class="block text-sm font-medium text-gray-700">Class</label>
                    <input type="text" name="class" id="class" value="{{ old('class') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                    @error('class')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                    <select name="gender" id="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700">County</label>
                    <input type="text" name="state" id="state" value="{{ old('state') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                    @error('state')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-black bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Student
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js?v={{ time() }}"></script>
<script>
    let stream = null;
    window.cropper = null;

    function previewProfilePhoto(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profile-photo-preview');
                const img = document.getElementById('profile-photo-img');
                const cropBtn = document.getElementById('crop-button');
                
                img.src = e.target.result;
                if(preview) preview.classList.remove('hidden');
                if(img) img.classList.remove('hidden'); 
                if(cropBtn) cropBtn.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    // Cropping functions
    function openCropModal() {
        const previewImg = document.getElementById('profile-photo-img');
        // relaxed check: ignore hidden class, focus on src availability
        if (!previewImg || !previewImg.src || previewImg.src === window.location.href) {
            alert('Please select or capture an image first.');
            return;
        }

        const cropperImg = document.getElementById('cropper-image');
        const modal = document.getElementById('crop-modal');
        
        // Show modal
        modal.classList.remove('hidden');
        
        // Ensure modal is visible and layout is stable before defining cropper
        if (window.cropper) {
            window.cropper.destroy();
            window.cropper = null;
        }

        // Set source and handle potential CORS
        cropperImg.crossOrigin = 'anonymous';
        cropperImg.src = previewImg.src;
        
        // Use a sufficient delay to ensure the image is loaded and modal rendered
        setTimeout(() => {
            window.cropper = new Cropper(cropperImg, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.9,
                background: false,
                restore: false,
                guides: true,
                center: true,
                highlight: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                checkOrientation: true,
                ready: function() {
                    console.log('Cropper is ready to capture.');
                    this.cropper.crop(); // Ensure crop box is active
                }
            });
        }, 400);
    }

    function closeCropModal() {
        document.getElementById('crop-modal').classList.add('hidden');
        if (window.cropper) {
            window.cropper.destroy();
            window.cropper = null;
        }
    }

    function applyCrop() {
        if (!window.cropper) {
            alert('Cropper not initialized');
            return;
        }

        try {
            // Get the cropped canvas
            const canvas = window.cropper.getCroppedCanvas({
                width: 600,
                height: 600,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            if (!canvas) {
                alert('Could not generate cropped image.');
                return;
            }

            // Convert to Blob for both preview and file input
            canvas.toBlob((blob) => {
                if (!blob) {
                    alert('Crop failed: Image data empty.');
                    return;
                }

                // Create a fresh URL from the blob
                const blobUrl = URL.createObjectURL(blob);
                
                // 1. Force refresh the preview on the main page
                const previewImg = document.getElementById('profile-photo-img');
                if (previewImg) {
                    const parent = previewImg.parentNode;
                    const newImg = document.createElement('img');
                    
                    newImg.id = 'profile-photo-img';
                    newImg.className = previewImg.className;
                    newImg.style.cssText = previewImg.style.cssText;
                    newImg.alt = "Profile Photo Preview";
                    newImg.crossOrigin = 'anonymous';
                    newImg.src = blobUrl;
                    newImg.classList.remove('hidden');
                    
                    // Visual confirmation
                    newImg.style.border = '4px solid #10b981';
                    setTimeout(() => { newImg.style.border = ''; }, 1500);
                    
                    parent.replaceChild(newImg, previewImg);
                }

                const previewContainer = document.getElementById('profile-photo-preview');
                if (previewContainer) {
                    previewContainer.classList.remove('hidden');
                }

                // 2. Update the actual form file input
            // 2. Update the actual form file input
            canvas.toBlob((blob) => {
                if (blob) {
                    const file = new File([blob], 'cropped_' + Date.now() + '.jpg', { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    
                    const fileInput = document.getElementById('profile_photo');
                    fileInput.files = dataTransfer.files;
                    
                    // Dispatch change event to ensure any listeners or form handling logic is triggered
                    fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    console.log('Form file input updated with cropped image.');
                    
                    // Add a notification next to the button
                    const btn = document.getElementById('crop-button');
                    const originalContent = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i> Cropped!';
                    btn.classList.add('bg-green-50', 'text-green-700', 'border-green-200');
                    setTimeout(() => {
                        btn.innerHTML = originalContent;
                        btn.classList.remove('bg-green-50', 'text-green-700', 'border-green-200');
                    }, 3000);
                }
                
                // 3. Close modal only after everything is successful
                closeCropModal();
            }, 'image/jpeg', 0.95);

        } catch (error) {
            console.error('Cropping error:', error);
            alert('Error during cropping: ' + error.message);
        }
    }

    function previewFingerprint(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('fingerprint-preview');
                const img = document.getElementById('fingerprint-img');
                img.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    async function toggleCamera(type) {
        const container = document.getElementById(`camera-container-${type}`);
        const video = document.getElementById(`video-${type}`);
        const faceMode = document.getElementById(`camera-select-${type}`).value;
        
        if (container.classList.contains('hidden')) {
            // Check if browser supports mediaDevices
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                const message = "Camera API is not supported in this browser or context. \n\nNote: Camera access REQUIRES HTTPS (or localhost). If you are using a custom domain over HTTP, the browser will block camera access for security.";
                console.error(message);
                alert(message);
                return;
            }

            try {
                console.log("Requesting camera access...");
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: faceMode } 
                });
                video.srcObject = stream;
                container.classList.remove('hidden');

                // Check for zoom capability
                setTimeout(() => {
                    const tracks = stream.getVideoTracks();
                    if (tracks.length === 0) return;
                    
                    const track = tracks[0];
                    if (typeof track.getCapabilities !== 'function') {
                        console.log("Zoom not supported: getCapabilities not available");
                        return;
                    }

                    const capabilities = track.getCapabilities();
                    const zoomContainer = document.getElementById(`zoom-container-${type}`);
                    const zoomRange = document.getElementById(`zoom-range-${type}`);

                    if (capabilities.zoom) {
                        zoomContainer.classList.remove('hidden');
                        zoomRange.min = capabilities.zoom.min;
                        zoomRange.max = capabilities.zoom.max;
                        zoomRange.step = capabilities.zoom.step;
                        zoomRange.value = track.getConstraints().advanced?.[0]?.zoom || capabilities.zoom.min;
                    } else {
                        zoomContainer.classList.add('hidden');
                    }
                }, 500); // Small delay to allow stream initialization
            } catch (err) {
                console.error("Error accessing camera:", err);
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    alert("Permission denied. Please allow camera access in your browser settings and try again.");
                } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                    alert("No camera device found.");
                } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                    alert("Camera is currently in use by another application.");
                } else {
                    alert("Error accessing camera: " + err.message);
                }
            }
        } else {
            stopCamera(type);
        }
    }

    async function changeCamera(type) {
        if (stream) {
            const video = document.getElementById(`video-${type}`);
            const faceMode = document.getElementById(`camera-select-${type}`).value;

            try {
                stream.getTracks().forEach(track => track.stop());
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: faceMode } 
                });
                video.srcObject = stream;

                // Check for zoom capability after camera change
                setTimeout(() => {
                    const tracks = stream.getVideoTracks();
                    if (tracks.length === 0) return;
                    
                    const track = tracks[0];
                    const zoomContainer = document.getElementById(`zoom-container-${type}`);
                    const zoomRange = document.getElementById(`zoom-range-${type}`);

                    if (typeof track.getCapabilities === 'function') {
                        const capabilities = track.getCapabilities();
                        if (capabilities.zoom) {
                            zoomContainer.classList.remove('hidden');
                            zoomRange.min = capabilities.zoom.min;
                            zoomRange.max = capabilities.zoom.max;
                            zoomRange.step = capabilities.zoom.step;
                            zoomRange.value = capabilities.zoom.min;
                        } else {
                            zoomContainer.classList.add('hidden');
                        }
                    } else {
                        console.log("Zoom not supported: getCapabilities not available");
                        zoomContainer.classList.add('hidden');
                    }
                }, 500);
            } catch (err) {
                console.error("Error switching camera:", err);
                alert("Error switching camera: " + err.message);
            }
        }
    }

    async function applyZoom(type, value) {
        if (stream) {
            const track = stream.getVideoTracks()[0];
            try {
                await track.applyConstraints({
                    advanced: [{ zoom: value }]
                });
            } catch (err) {
                console.error("Error applying zoom:", err);
            }
        }
    }

    function stopCamera(type) {
        const container = document.getElementById(`camera-container-${type}`);
        const video = document.getElementById(`video-${type}`);
        
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        
        video.srcObject = null;
        container.classList.add('hidden');
    }

    function capturePhoto(type) {
        const video = document.getElementById(`video-${type}`);
        const canvas = document.getElementById(`canvas-${type}`);
        const context = canvas.getContext('2d');
        const fileInput = document.getElementById(type === 'profile' ? 'profile_photo' : 'fingerprint_image');

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(blob => {
                const file = new File([blob], `${type}_photo_${Date.now()}.jpg`, { type: "image/jpeg" });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                
                // Dispatch change event
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));

                // Trigger preview
                const event = { target: { files: [file] } };
                if (type === 'profile') {
                    previewProfilePhoto(event);
                } else {
                    previewFingerprint(event);
                }
                
                stopCamera(type);
            }, 'image/jpeg', 0.95);
        }
    }
</script>
@endpush
@endsection
