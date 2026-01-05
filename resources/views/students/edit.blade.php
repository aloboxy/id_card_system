@extends('layouts.app')

@section('header', 'Edit Student')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Edit Student: {{ $student->full_name }}</h3>
        </div>
        <form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label for="school_id" class="block text-sm font-medium text-gray-700">School</label>
                    <select name="school_id" id="school_id" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                        <option value="">Select School</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id', $student->school_id) == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                    <input type="text" name="student_id" id="student_id" value="{{ old('student_id', $student->student_id) }}"  
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $student->first_name) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                        <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $student->middle_name) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                        @error('middle_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $student->last_name) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Profile Photo -->
                <div class="col-span-2">
                    <label for="profile_photo" class="block text-sm font-medium text-gray-700">Profile Photo (Optional)</label>
                    
                    {{-- Preview Area --}}
                    <div id="profile-photo-preview" class="mb-3 mt-2">
                        @if($student->profile_photo_path)
                            <img id="profile-photo-img" src="{{ asset('storage/' . $student->profile_photo_path) }}" alt="Profile Photo" class="h-32 w-32 object-cover border-2 border-gray-300 rounded-lg shadow-sm">
                        @else
                            <img id="profile-photo-img" src="" alt="Profile Photo Preview" class="hidden h-32 w-32 object-cover border-2 border-gray-300 rounded-lg shadow-sm">
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*" onchange="previewProfilePhoto(event)" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <button type="button" onclick="toggleCamera('profile')" class="mt-1 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-camera mr-2"></i> Use Camera
                        </button>
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

                <!-- Fingerprint Image -->
                <div class="col-span-2">
                    <label for="fingerprint_image" class="block text-sm font-medium text-gray-700">Fingerprint Image (Optional)</label>
                    
                    {{-- Preview Area --}}
                    <div id="fingerprint-preview" class="mb-3 mt-2">
                        @if($student->fingerprint_image_path)
                            <img id="fingerprint-img" src="{{ asset('storage/' . $student->fingerprint_image_path) }}" alt="Fingerprint" class="h-32 w-32 object-contain border-2 border-gray-300 rounded-lg shadow-sm bg-gray-50">
                        @else
                            <img id="fingerprint-img" src="" alt="Fingerprint Preview" class="hidden h-32 w-32 object-contain border-2 border-gray-300 rounded-lg shadow-sm bg-gray-50">
                        @endif
                    </div>
                    
                    <input type="file" name="fingerprint_image" id="fingerprint_image" accept="image/*" onchange="previewFingerprint(event)" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('fingerprint_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="admission_number" class="block text-sm font-medium text-gray-700">Admission Number</label>
                    <input type="text" name="admission_number" id="admission_number" value="{{ old('admission_number', $student->admission_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                    @error('admission_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="class" class="block text-sm font-medium text-gray-700">Class</label>
                    <input type="text" name="class" id="class" value="{{ old('class', $student->class) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                    @error('class')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                    <select name="gender" id="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                        <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address', $student->address) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $student->city) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="state" class="block text-sm font-medium text-gray-700">County</label>
                    <input type="text" name="state" id="state" value="{{ old('state', $student->state) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
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
                    Update Student
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let stream = null;

    function previewProfilePhoto(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('profile-photo-img');
                img.src = e.target.result;
                img.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function previewFingerprint(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('fingerprint-img');
                img.src = e.target.result;
                img.classList.remove('hidden');
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
