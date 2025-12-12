{{-- 
    Reusable Image Upload Component dengan Dropzone
    
    Usage:
    <x-image-upload :model="$product ?? null" max-files="3" label="Gambar Produk" />
    
    Props:
    - model: Model instance (Product, Category, dll) yang punya media
    - maxFiles: Maximum jumlah file (default: 3)
    - label: Label untuk form field (default: "Gambar")
    - maxSize: Max size per file dalam MB (default: 2)
    - name: Name attribute untuk input (default: "document")
--}}

@props([
    'model' => null,
    'maxFiles' => 3,
    'label' => 'Gambar',
    'maxSize' => 2,
    'name' => 'document',
    'helpText' => null
])

<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="image">
                    {{ $label }}
                    <i class="bi bi-question-circle-fill text-info"
                        data-toggle="tooltip" 
                        data-placement="top"
                        title="{{ $helpText ?? "Maks. {$maxFiles} file, ukuran maks. {$maxSize}MB, format: .jpg, .jpeg, .png, .gif" }}">
                    </i>
                </label>
                
                <div class="dropzone d-flex flex-wrap align-items-center justify-content-center" id="document-dropzone">
                    <div class="dz-message" data-dz-message>
                        <div class="text-center p-4">
                            <i class="bi bi-cloud-arrow-up" style="font-size: 3rem; color: #6c757d;"></i>
                            <p class="mt-3 mb-1 font-weight-bold">Klik atau drag file ke sini</p>
                            <small class="text-muted">Maks {{ $maxFiles }} gambar, {{ $maxSize }}MB per file</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@once
@push('page_scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Prevent auto discovery for this specific element if it hasn't been handled globally
        if (typeof Dropzone !== 'undefined') {
            Dropzone.autoDiscover = false;
        }

        var dropzoneId = "document-dropzone";
        var dropzoneElement = document.getElementById(dropzoneId);

        // Check if Dropzone is already attached to avoid duplicates
        if (dropzoneElement && !dropzoneElement.dropzone) {
            
            var uploadedDocumentMap = {};
            
            var myDropzone = new Dropzone("#" + dropzoneId, {
                url: '{{ route("dropzone.upload") }}',
                maxFilesize: {{ $maxSize }},
                acceptedFiles: '.jpg, .jpeg, .png, .gif',
                maxFiles: {{ $maxFiles }},
                addRemoveLinks: true,
                dictRemoveFile: '<i class="bi bi-x-circle text-danger"></i> Hapus',
                dictMaxFilesExceeded: 'Maksimal {{ $maxFiles }} gambar',
                dictFileTooBig: 'File terlalu besar (@{{filesize}}MB). Maks: @{{maxFilesize}}MB.',
                dictInvalidFileType: 'Tipe file tidak diizinkan.',
                dictDefaultMessage: 'Drop files here to upload',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                
                success: function(file, response) {
                    console.log('✅ Upload berhasil:', response);
                    $('form').append('<input type="hidden" name="{{ $name }}[]" value="' + response.name + '">');
                    uploadedDocumentMap[file.name] = response.name;
                },
                
                error: function(file, errorMessage) {
                    console.error('❌ Upload error:', errorMessage);
                    if (typeof errorMessage === 'object') {
                        errorMessage = 'Upload gagal. Silakan coba lagi.';
                    }
                    $(file.previewElement).find('.dz-error-message').text(errorMessage);
                },
                
                removedfile: function(file) {
                    console.log('🗑️ Menghapus file:', file);
                    file.previewElement.remove();
                    
                    var name = '';
                    if (typeof file.file_name !== 'undefined') {
                        name = file.file_name;
                    } else {
                        name = uploadedDocumentMap[file.name];
                    }
                    
                    // Delete from server via AJAX
                    if (name) {
                        $.ajax({
                            type: 'POST',
                            url: '{{ route("dropzone.delete") }}',
                            data: {
                                '_token': "{{ csrf_token() }}",
                                'filename': name
                            },
                            success: function(response) {
                                console.log('✅ File berhasil dihapus dari server');
                            },
                            error: function(xhr, status, error) {
                                console.error('❌ Gagal menghapus file:', error);
                            }
                        });
                    }
                    
                    // Remove hidden input
                    $('form').find('input[name="{{ $name }}[]"][value="' + name + '"]').remove();
                    delete uploadedDocumentMap[file.name];
                },
                
                init: function() {
                    var thisDropzone = this;
                    console.log('🔄 Initializing Dropzone...');
                    
                    @if($model && method_exists($model, 'getMedia'))
                        @php
                            $existingMedia = $model->getMedia('images');
                        @endphp
                        
                        @if($existingMedia->count() > 0)
                            var files = {!! json_encode($existingMedia) !!};
                            console.log('📷 Loading', files.length, 'existing image(s)');
                            
                            files.forEach(function(file, index) {
                                var mockFile = {
                                    name: file.file_name,
                                    size: file.size,
                                    file_name: file.file_name,
                                    type: 'image/' + (file.extension || 'jpeg')
                                };
                                
                                thisDropzone.emit("addedfile", mockFile);
                                thisDropzone.emit("thumbnail", mockFile, file.original_url);
                                thisDropzone.emit("complete", mockFile);
                                
                                $('form').append('<input type="hidden" name="{{ $name }}[]" value="' + file.file_name + '">');
                                uploadedDocumentMap[file.file_name] = file.file_name;
                            });
                            
                            // Adjust max files count based on existing files
                            var existingCount = files.length;
                            thisDropzone.options.maxFiles = thisDropzone.options.maxFiles - existingCount;
                            
                            console.log('✅ Dropzone initialized with', files.length, 'file(s)');
                        @else
                            console.log('ℹ️ No existing images found');
                        @endif
                    @else
                        console.log('ℹ️ No model provided or no media support');
                    @endif
                }
            });
        }
    });
</script>
@endpush

@push('page_css')
<style>
    /* Dropzone Styling */
    .dropzone {
        border: 2px dashed #cbd5e0;
        border-radius: 0.5rem;
        background: #f7fafc;
        transition: all 0.3s ease;
        min-height: 200px;
    }
    
    .dropzone:hover {
        border-color: #5a67d8;
        background: #edf2f7;
    }
    
    .dropzone .dz-message {
        margin: 0;
    }
    
    .dropzone.dz-drag-hover {
        border-color: #5a67d8;
        background: #e6fffa;
    }
    
    /* Dropzone Preview */
    .dropzone .dz-preview {
        margin: 10px;
    }
    
    .dropzone .dz-preview .dz-image {
        border-radius: 8px;
        overflow: hidden;
        width: 120px;
        height: 120px;
    }
    
    .dropzone .dz-preview .dz-image img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
    
    .dropzone .dz-preview .dz-remove {
        font-size: 14px;
        text-align: center;
        display: block;
        margin-top: 5px;
    }
    
    .dropzone .dz-preview .dz-remove:hover {
        text-decoration: none;
        color: #e53e3e;
    }
</style>
@endpush
@endonce
