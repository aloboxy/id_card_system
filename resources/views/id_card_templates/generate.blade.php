@extends('layouts.app')

@section('header', 'Generate ID Cards')

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
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Bulk Generation</h2>
                <p class="text-sm text-gray-500">Template: {{ $template->name }}</p>
                <p class="text-sm text-gray-500">Total Records: {{ $records->count() }}</p>
            </div>
            <div class="space-x-2">
                <button onclick="startBulkGeneration()" id="btn-generate" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-cogs mr-2"></i> Start Generation
                </button>
            </div>
        </div>

        <!-- Progress Area -->
        <div id="progress-container" class="hidden mt-6">
            <div class="flex justify-between text-sm font-medium text-gray-600 mb-1">
                <span id="progress-text">Initializing...</span>
                <span id="progress-percent">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div id="progress-bar" class="bg-green-600 h-2.5 rounded-full" style="width: 0%"></div>
            </div>
            <p id="status-detail" class="text-xs text-gray-400 mt-1">Preparing resources...</p>
        </div>
    </div>

    <!-- Preview Area (Optional: Show just the first one) -->
    @if($records->count() > 0)
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-md font-semibold text-gray-700 mb-4">Preview (First Record)</h3>
        <div class="flex flex-col md:flex-row gap-8 justify-center items-start">
            <div>
                <span class="text-xs text-gray-500 block mb-1 text-center">Front</span>
                <div class="border border-gray-200 shadow-sm">
                    <canvas id="preview-front"></canvas>
                </div>
            </div>
            @if($template->design_data_back)
            <div>
                <span class="text-xs text-gray-500 block mb-1 text-center">Back</span>
                <div class="border border-gray-200 shadow-sm">
                    <canvas id="preview-back"></canvas>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://unpkg.com/fabric@5.3.0/dist/fabric.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
    const templateDataFront = @json($template->design_data);
    const templateDataBack = @json($template->design_data_back);
    const records = @json($records);
    const templateWidth = {{ $template->width }};
    const templateHeight = {{ $template->height }};
    const school_issue_date = "{{ $school_issue_date }}";
    const school_expiry_date = "{{ $school_expiry_date }}";
    
    // Preview the first record
    document.addEventListener('DOMContentLoaded', function() {
        if (records.length > 0) {
            renderCard('preview-front', templateDataFront, records[0]);
            if (templateDataBack) {
                renderCard('preview-back', templateDataBack, records[0]);
            }
        }
    });

    async function startBulkGeneration() {
        const btn = document.getElementById('btn-generate');
        const progressContainer = document.getElementById('progress-container');
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        const progressPercent = document.getElementById('progress-percent');
        const statusDetail = document.getElementById('status-detail');

        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        progressContainer.classList.remove('hidden');

        const zip = new JSZip();
        
        // Create off-screen canvases
        const canvasFront = new fabric.StaticCanvas(null, { width: templateWidth, height: templateHeight });
        const canvasBack = templateDataBack ? new fabric.StaticCanvas(null, { width: templateWidth, height: templateHeight }) : null;

        try {
            for (let i = 0; i < records.length; i++) {
                const record = records[i];
                const percent = Math.round(((i + 1) / records.length) * 100);
                
                // Update UI
                progressText.innerText = `Processing ${i + 1} of ${records.length}`;
                progressPercent.innerText = `${percent}%`;
                progressBar.style.width = `${percent}%`;
                statusDetail.innerText = `Generating ID for: ${record.first_name} ${record.last_name}`;

                // Generate Front
                await renderToCanvas(canvasFront, templateDataFront, record, school_issue_date, school_expiry_date);
                const frontBlob = await getCanvasBlob(canvasFront);
                // Use student_id or staff_id or just name for filename
                const id = record.student_id || record.staff_id || 'id';
                const name = (record.first_name + '_' + record.last_name).replace(/[^a-z0-9]/gi, '_').toLowerCase();
                zip.file(`${name}_${id}_front.jpg`, frontBlob);

                // Generate Back
                if (canvasBack && templateDataBack) {
                    await renderToCanvas(canvasBack, templateDataBack, record, school_issue_date, school_expiry_date);
                    const backBlob = await getCanvasBlob(canvasBack);
                    zip.file(`${name}_${id}_back.jpg`, backBlob);
                }

                // Small delay to allow UI update
                await new Promise(resolve => setTimeout(resolve, 10));
            }

            statusDetail.innerText = 'Compressing files...';
            const content = await zip.generateAsync({type: "blob"});
            saveAs(content, "id_cards_bulk.zip");
            
            statusDetail.innerText = 'Done!';
            btn.innerHTML = '<i class="fas fa-check mr-2"></i> Completed';

        } catch (error) {
            console.error('Generation Error:', error);
            statusDetail.innerText = 'Error: ' + error.message;
            statusDetail.classList.add('text-red-500');
            alert('An error occurred during generation. Check console for details.');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    function renderCard(canvasId, templateData, record) {
        const canvas = new fabric.StaticCanvas(canvasId, {
            width: templateWidth,
            height: templateHeight,
            backgroundColor: '#ffffff'
        });
        renderToCanvas(canvas, templateData, record, school_issue_date, school_expiry_date);
    }

    function renderToCanvas(canvas, templateData, record, school_issue_date, school_expiry_date) {
        return new Promise((resolve, reject) => {
            canvas.clear();
            canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));
            
            canvas.loadFromJSON(templateData, function() {
                const objects = canvas.getObjects();
                let pendingImages = 0;

                // Helper to check if we are done
                const checkDone = () => {
                    if (pendingImages === 0) {
                        canvas.renderAll();
                        resolve();
                    }
                };

                console.log('Total objects on canvas:', objects.length);
                objects.forEach((obj, index) => {
                    console.log(`Object ${index}:`, obj.type, 'data:', obj.data);
                    
                    // Text Replacements
                    if (obj.type === 'i-text' || obj.type === 'text') {
                        if (obj.data && obj.data.field) {
                            const field = obj.data.field;
                            let value = '';
                            
                            // console.log('Processing field:', field, 'for record:', record.id);
                            
                            switch(field) {
                                // Common
                                case 'full_name': value = record.first_name + ' ' + record.last_name; break;
                                case 'phone': value = record.phone || ''; break;
                                case 'email': value = record.email || ''; break;
                                case 'issue_date': value = school_issue_date; break;
                                case 'expiry_date': value = school_expiry_date; break;
                                
                                // Student Specific
                                case 'student_id': value = record.student_id || ''; break;
                                case 'class': value = record.class || ''; break;
                                case 'class_with_section': value = (record.class || '') + (record.section ? ' - ' + record.section : ''); break;
                                case 'dob': value = record.date_of_birth ? new Date(record.date_of_birth).toLocaleDateString() : ''; break;
                                
                                // Staff Specific
                                case 'staff_id': value = record.staff_id || ''; break;
                                case 'designation': value = record.designation || ''; break;
                                case 'department': value = record.department || ''; break;
                                case 'joining_date': value = record.joining_date ? new Date(record.joining_date).toLocaleDateString() : ''; break;
                            }
                            
                            if (value) {
                                // console.log('Replacing', obj.text, 'with', value);
                                // Replace the entire text with the value if it's a placeholder
                                obj.set('text', value);
                            }
                        } else if (obj.text) {
                            // Also check for text that contains @{{placeholder}} patterns
                            let text = obj.text;
                            const originalText = text;
                            
                            // Common Placeholders
                            text = text.replace(/\{\{full_name\}\}/gi, record.first_name + ' ' + record.last_name);
                            text = text.replace(/\{\{phone\}\}/gi, record.phone || '');
                            text = text.replace(/\{\{email\}\}/gi, record.email || '');
                            text = text.replace(/\{\{issue_date\}\}/gi, school_issue_date);
                            text = text.replace(/\{\{expiry_date\}\}/gi, school_expiry_date);
                            text = text.replace(/\{\{dob\}\}/gi, record.date_of_birth ? new Date(record.date_of_birth).toLocaleDateString() : '');
                            
                            // Student Placeholders
                            text = text.replace(/\{\{student_id\}\}/gi, record.student_id || '');
                            text = text.replace(/\{\{class_with_section\}\}/gi, (record.class || '') + (record.section ? ' - ' + record.section : ''));
                            text = text.replace(/\{\{class\}\}/gi, record.class || '');
                            
                            // Staff Placeholders
                            text = text.replace(/\{\{staff_id\}\}/gi, record.staff_id || '');
                            text = text.replace(/\{\{designation\}\}/gi, record.designation || '');
                            text = text.replace(/\{\{department\}\}/gi, record.department || '');
                            text = text.replace(/\{\{joining_date\}\}/gi, record.joining_date || '');

                            // Legacy/Friendly Names support
                            text = text.replace(/\{\{Student Name\}\}/gi, record.first_name + ' ' + record.last_name);
                            text = text.replace(/\{\{Staff Name\}\}/gi, record.first_name + ' ' + record.last_name);
                            text = text.replace(/\{\{Student ID\}\}/gi, record.student_id || '');
                            text = text.replace(/\{\{Staff ID\}\}/gi, record.staff_id || '');
                            text = text.replace(/\{\{Class & Section\}\}/gi, (record.class || '') + (record.section ? ' - ' + record.section : ''));
                            text = text.replace(/\{\{Class\}\}/gi, record.class || '');
                            text = text.replace(/\{\{Issue Date\}\}/gi, school_issue_date);
                            text = text.replace(/\{\{Expiry Date\}\}/gi, school_expiry_date);
                            
                            
                            if (text !== obj.text) {
                                // console.log('Text replacement:', originalText, '->', text);
                                obj.set('text', text);
                            }
                        }
                    }

                    // Group Text Replacements (Issue/Expiry Date)
                    if (obj.type === 'group' && obj.data && (obj.data.field === 'issue_date' || obj.data.field === 'expiry_date')) {
                         const field = obj.data.field;
                         let value = '';
                         
                         switch(field) {
                             case 'issue_date': value = school_issue_date; break;
                             case 'expiry_date': value = school_expiry_date; break;
                         }
                         
                         if (value) {
                             console.log('Replacing group field', field, 'with', value);
                             // Find the text object inside the group
                             obj._objects.forEach(innerObj => {
                                 if (innerObj.type === 'text' || innerObj.type === 'i-text') {
                                     innerObj.set('text', value);
                                 }
                             });
                             // Mark group as dirty ensuring render picks up changes
                             obj.dirty = true;
                         }
                    }
                    
                    // Profile Photo - check both direct objects and groups
                    const isPhoto = (obj.data && obj.data.field === 'photo') || 
                                    (obj.type === 'group' && obj.data && obj.data.field === 'photo');
                    
                    if (isPhoto) {
                        // console.log('Found photo placeholder');
                        // console.log('Profile photo path:', record.photo_path || record.profile_photo_path);
                        
                        // Try both photo_path and profile_photo_path
                        const photoPath = record.photo_path || record.profile_photo_path;
                        
                        if (photoPath) {
                            pendingImages++;
                            // Make sure the path doesn't start with a slash
                            const cleanPath = photoPath.startsWith('/') ? photoPath.substring(1) : photoPath;
                            const photoUrl = `/storage/${cleanPath}`;
                            console.log('Attempting to load photo from:', photoUrl);
                            
                            // Create a new image element to handle loading
                            const imgElement = new Image();
                            imgElement.crossOrigin = 'Anonymous';
                            
                            imgElement.onload = function() {
                                try {
                                    console.log('Image loaded successfully, dimensions:', imgElement.width, 'x', imgElement.height);
                                    
                                    // Create fabric image from the loaded image element
                                    const fabricImg = new fabric.Image(imgElement, {
                                        left: obj.left,
                                        top: obj.top,
                                        originX: 'left',
                                        originY: 'top',
                                        selectable: false,
                                        evented: false,
                                        objectCaching: true
                                    });
                                    
                                    // Get shape info
                                    const shape = obj.data?.shape || 'rectangle';
                                    const objWidth = obj.getScaledWidth();
                                    const objHeight = obj.getScaledHeight();
                                    
                                    // Calculate scaling to fill the frame while maintaining aspect ratio
                                    const scaleX = objWidth / fabricImg.width;
                                    const scaleY = objHeight / fabricImg.height;
                                    const scale = Math.max(scaleX, scaleY);
                                    
                                    // Apply scaling
                                    fabricImg.scale(scale);
                                    
                                    // Center the image in the frame
                                    const centerX = obj.left + (objWidth - (fabricImg.width * scale)) / 2;
                                    const centerY = obj.top + (objHeight - (fabricImg.height * scale)) / 2;
                                    
                                    fabricImg.set({
                                        left: centerX,
                                        top: centerY
                                    });
                                    
                                    // Apply clipPath to crop to exact shape
                                    if (shape === 'circle') {
                                        const radius = Math.min(objWidth, objHeight) / 2;
                                        fabricImg.clipPath = new fabric.Circle({
                                            radius: radius,
                                            left: obj.left + objWidth / 2,
                                            top: obj.top + objHeight / 2,
                                            originX: 'center',
                                            originY: 'center',
                                            absolutePositioned: true
                                        });
                                    } else if (shape === 'rounded') {
                                        fabricImg.clipPath = new fabric.Rect({
                                            width: objWidth,
                                            height: objHeight,
                                            rx: 15,
                                            ry: 15,
                                            left: obj.left,
                                            top: obj.top,
                                            absolutePositioned: true
                                        });
                                    } else {
                                        // Rectangle
                                        fabricImg.clipPath = new fabric.Rect({
                                            width: objWidth,
                                            height: objHeight,
                                            left: obj.left,
                                            top: obj.top,
                                            absolutePositioned: true
                                        });
                                    }
                                    
                                    // Add the image to canvas and remove the placeholder
                                    canvas.add(fabricImg);
                                    canvas.remove(obj);
                                    
                                    console.log('Photo added with shape:', shape, 'at position:', centerX, centerY, 'with scale:', scale);
                                    
                                    // Force a re-render
                                    canvas.renderAll();
                                    
                                } catch (error) {
                                    console.error('Error processing image:', error);
                                } finally {
                                    pendingImages--;
                                    checkDone();
                                }
                            };
                            
                            imgElement.onerror = function() {
                                console.error('Failed to load image from:', photoUrl);
                                pendingImages--;
                                checkDone();
                            };
                            
                            // Start loading the image
                            imgElement.src = photoUrl;
                            
                            // Add a timeout to prevent hanging
                            setTimeout(() => {
                                if (!imgElement.complete) {
                                    console.warn('Image loading timed out:', photoUrl);
                                    imgElement.onerror = null;
                                    pendingImages--;
                                    checkDone();
                                }
                            }, 5000); // 5 second timeout
                         } else {
                             console.log('No profile photo for this student');
                         }
                     }

                    // QR Code - check both direct objects and groups
                    const isQRCode = (obj.data && obj.data.field === 'qr_code') || 
                                     (obj.type === 'group' && obj.data && obj.data.field === 'qr_code');
                    
                    if (isQRCode) {
                        // console.log('Found QR code placeholder');
                        // console.log('QR code URL:', record.qr_code_url);
                        
                        if (record.qr_code_url) {
                            pendingImages++;
                            fabric.Image.fromURL(record.qr_code_url, function(qrImg) {
                                if (!qrImg) {
                                    console.error('Failed to load QR code');
                                    pendingImages--;
                                    checkDone();
                                    return;
                                }
                                
                                console.log('QR code loaded successfully');
                                
                                // Get dimensions from the object or group
                                const width = obj.width || obj.getScaledWidth();
                                const height = obj.height || obj.getScaledHeight();
                                
                                qrImg.scaleToWidth(width);
                                qrImg.set({
                                    left: obj.left,
                                    top: obj.top
                                });
                                
                                canvas.add(qrImg);
                                canvas.remove(obj);
                                console.log('QR code added to canvas');
                                pendingImages--;
                                checkDone();
                            }, { crossOrigin: 'anonymous' });
                        } else {
                            console.log('No QR code URL for this student');
                        }
                    }

                    // Fingerprint
                    if (obj.data && obj.data.field === 'fingerprint') {
                        if (record.fingerprint_image_path) {
                            pendingImages++;
                            const fingerprintUrl = '/storage/' + record.fingerprint_image_path;
                            fabric.Image.fromURL(fingerprintUrl, function(img) {
                                if (!img) { pendingImages--; checkDone(); return; }
                                img.set({
                                    left: obj.left,
                                    top: obj.top,
                                    scaleX: (obj.width * obj.scaleX) / img.width,
                                    scaleY: (obj.height * obj.scaleY) / img.height
                                });
                                canvas.add(img);
                                canvas.remove(obj);
                                pendingImages--;
                                checkDone();
                            }, { crossOrigin: 'anonymous' });
                        } else {
                            obj.set('fill', '#f5f5f5');
                        }
                    }
                });
                
                // After processing all objects, check if we're done
                // (in case there were no pending images)
                checkDone();
            });
        });
    }

    function getCanvasBlob(canvas) {
        return new Promise((resolve) => {
            // Ensure white background for JPEG (no transparency)
            // Force a final render before capturing
            canvas.renderAll();
            canvas.backgroundColor = '#ffffff';
            canvas.renderAll();
            
            // Convert to high-quality JPEG
            const dataURL = canvas.toDataURL({
                format: 'jpeg',
                quality: 1.0,  // Maximum quality
                multiplier: 3,  // 3x resolution for extra clarity
                enableRetinaScaling: false  // Prevent double scaling
            });
            
            // Convert data URL to blob
            fetch(dataURL)
                .then(res => res.blob())
                .then(blob => resolve(blob));
        });
    }
</script>
@endpush
@endsection

