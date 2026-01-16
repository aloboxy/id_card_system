
{{-- Image Cropping & Editing Modal --}}
<div id="crop-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="z-index: 9999;" onclick="if(event.target === this || event.target.querySelector('.backdrop-blur-sm')) closeCropModal()">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" onclick="if(event.target === this) closeCropModal()">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="relative z-50 inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
            <div class="bg-white flex-1 overflow-hidden flex flex-col">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-crop-alt mr-2 text-indigo-600"></i> Edit & Crop Image
                    </h3>
                    <button type="button" onclick="closeCropModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-full hover:bg-gray-100">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                {{-- Toolbar --}}
                <div class="bg-gray-50 border-b border-gray-200 px-4 py-2 flex flex-wrap gap-2 items-center justify-center" onclick="event.stopPropagation()">
                    <div class="flex space-x-1">
                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); cropper.zoom(0.1)" class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-white rounded border border-transparent hover:border-gray-300" title="Zoom In">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); cropper.zoom(-0.1)" class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-white rounded border border-transparent hover:border-gray-300" title="Zoom Out">
                            <i class="fas fa-search-minus"></i>
                        </button>
                    </div>
                    <div class="w-px h-6 bg-gray-300 mx-2"></div>
                    <div class="flex space-x-1">
                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); cropper.rotate(-90)" class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-white rounded border border-transparent hover:border-gray-300" title="Rotate Left">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); cropper.rotate(90)" class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-white rounded border border-transparent hover:border-gray-300" title="Rotate Right">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                    <div class="w-px h-6 bg-gray-300 mx-2"></div>
                    <div class="flex space-x-1">
                         <button type="button" onclick="event.preventDefault(); event.stopPropagation(); cropper.scaleX(-window.cropper.getData().scaleX || -1)" class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-white rounded border border-transparent hover:border-gray-300" title="Flip Horizontal">
                            <i class="fas fa-arrows-alt-h"></i>
                        </button>
                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); cropper.scaleY(-window.cropper.getData().scaleY || -1)" class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-white rounded border border-transparent hover:border-gray-300" title="Flip Vertical">
                            <i class="fas fa-arrows-alt-v"></i>
                        </button>
                    </div>
                    <div class="w-px h-6 bg-gray-300 mx-2"></div>
                     <div class="flex space-x-1">
                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); cropper.reset()" class="p-2 text-red-600 hover:text-red-700 hover:bg-white rounded border border-transparent hover:border-gray-300" title="Reset">
                            <i class="fas fa-sync-alt"></i> Reset
                        </button>
                    </div>
                </div>

                {{-- Image Container --}}
                <div class="p-4 bg-gray-100 flex justify-center items-center overflow-auto" style="height: 60vh; min-height: 300px;" onclick="event.stopPropagation()">
                    <div class="img-container w-full h-full shadow-lg bg-white">
                        <img id="cropper-image" src="" crossorigin="anonymous" class="max-w-full block">
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-white px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100" onclick="event.stopPropagation()">
                <button type="button" onclick="applyCrop()" class="w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-6 py-2.5 bg-indigo-600 text-base font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-check mr-2"></i> Save Changes
                </button>
                <button type="button" onclick="closeCropModal()" class="mt-3 w-full inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-6 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css?v={{ time() }}">
<style>
    .img-container img {
        display: block;
        max-width: 100%;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js?v={{ time() }}"></script>
<script>
    window.cropper = null;
    let targetImageId = null; // ID of the image element to update on the main page
    let targetInputId = null; // ID of the file input to update
    let onCropSuccess = null; // Callback function after successful crop

    /**
     * Open the crop modal for a specific image source
     * 
     * @param {string|File} source - Image URL or File object
     * @param {string} imgId - ID of the preview <img> element to update
     * @param {string} inputId - ID of the hidden file <input> to update
     * @param {function} callback - Optional callback after crop is applied
     */
    function openCropModal(source, imgId = 'profile-photo-img', inputId = 'profile_photo', callback = null) {
        targetImageId = imgId;
        targetInputId = inputId;
        onCropSuccess = callback;

        const cropperImg = document.getElementById('cropper-image');
        const modal = document.getElementById('crop-modal');
        
        // Handle Source
        if (source instanceof File) {
            const reader = new FileReader();
            reader.onload = (e) => {
                initializeCropper(e.target.result);
            };
            reader.readAsDataURL(source);
        } else if (typeof source === 'string' && source.length > 0) {
            initializeCropper(source);
        } else {
             // Fallback: try to grab from the targetPreview element
             const previewEl = document.getElementById(imgId);
             if(previewEl && previewEl.src && previewEl.src !== window.location.href) {
                 initializeCropper(previewEl.src);
             } else {
                 alert('No image selected to crop.');
                 return;
             }
        }
        
        function initializeCropper(imgSrc) {
            modal.classList.remove('hidden');
            
            if (window.cropper) {
                window.cropper.destroy();
                window.cropper = null;
            }

            cropperImg.src = imgSrc;
            
            // Wait for modal transition
            setTimeout(() => {
                window.cropper = new Cropper(cropperImg, {
                    aspectRatio: 1, // Default to square, can make dynamic later
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.9,
                    restore: false,
                    modal: true,
                    guides: true,
                    center: true,
                    highlight: true,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    checkOrientation: true,
                    background: false,
                    responsive: true,
                    zoomable: true,
                    rotatable: true,
                    scalable: true,
                });
            }, 300);
        }
    }

    function closeCropModal() {
        document.getElementById('crop-modal').classList.add('hidden');
        if (window.cropper) {
            window.cropper.destroy();
            window.cropper = null;
        }
    }

    function applyCrop() {
        if (!window.cropper) return;

        try {
            const canvas = window.cropper.getCroppedCanvas({
                width: 600,
                height: 600,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            if (!canvas) {
                alert('Could not crop image.');
                return;
            }

            canvas.toBlob((blob) => {
                if (!blob) return;

                // 1. Update Preview Image
                if (targetImageId) {
                    const blobUrl = URL.createObjectURL(blob);
                    const previewImg = document.getElementById(targetImageId);
                    if (previewImg) {
                        previewImg.src = blobUrl;
                        previewImg.classList.remove('hidden');
                        
                        // Visual Feedback
                        previewImg.style.transition = 'box-shadow 0.3s ease';
                        previewImg.style.boxShadow = '0 0 0 4px #10b981'; // Green ring
                        setTimeout(() => { previewImg.style.boxShadow = ''; }, 2000);
                    }
                }

                // 2. Update File Input
                if (targetInputId) {
                    const file = new File([blob], 'cropped_image_' + Date.now() + '.jpg', { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    
                    const fileInput = document.getElementById(targetInputId);
                    if (fileInput) {
                        fileInput.files = dataTransfer.files;
                         // Dispatch change event manually since programmatic updates don't trigger it
                        fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }

                // 3. Callback
                if (typeof onCropSuccess === 'function') {
                    onCropSuccess(blob);
                }

                closeCropModal();
                
            }, 'image/jpeg', 0.95);

        } catch (error) {
            console.error('Cropping error:', error);
            alert('Something went wrong while cropping.');
        }
    }
</script>
@endpush
