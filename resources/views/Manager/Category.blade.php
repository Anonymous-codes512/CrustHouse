@extends('Components.Manager')
<script>
document.addEventListener('DOMContentLoaded', function() {
            let branchName = document.getElementById('branch_name').value;
            let titleElement = document.getElementById('dynamic-title');
            titleElement.textContent = branchName + ' | Manager - Category';
        });
</script>
@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Manager/category.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush 
<style>
    #categoryTable_paginate,
    #categoryTable_filter,
    .dataTables_length,
    .dataTables_info {
        margin: 0.5vw 6.3vw !important;
        font-size: 0.8rem !important;
    }

    #categoryTable_next,
    #categoryTable_previous,
    #categoryTable_paginate span a {
        margin: 0.5vw !important;
        padding: 0 !important;
        font-size: 0.8rem
    }

    table.dataTable {
        border-collapse: collapse !important;
    }
</style>
@section('main')
    <main id="category">
        @if (session('success'))
            <div id="success" class="alert alert-success">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(() => {
                    document.getElementById('success').classList.add('alert-hide');
                }, 2000);

                setTimeout(() => {
                    document.getElementById('success').style.display = 'none';
                }, 3000);
            </script>
        @endif

        @if (session('error'))
            <div id="error" class="alert alert-danger">
                {{ session('error') }}
            </div>
            <script>
              setTimeout(() => {
                    document.getElementById('error').classList.add('alert-hide');
                }, 2000);

                setTimeout(() => {
                    document.getElementById('error').style.display = 'none';
                }, 3000);
            </script>
        @endif
        @php
            $id = session('id');
            $branch_id = session('branch_id');
        @endphp

        <div class="addnewCategory">
            <button onclick="addCategory()">Add New Category</button>
        </div>

        @php
            $categories = $categories;
        @endphp

        <table id="categoryTable">
            <thead>
                <tr>
                    <th>Category Image</th>
                    <th>Category Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $value)
                    <tr>
                        <td><img src={{ asset('Images/CategoryImages/' . $value->categoryImage) }} alt="Image"></td>
                        <td>{{ $value->categoryName }}</td>
                        <td>
                            <a onclick="editcategory('{{ $value->categoryName }}', '{{ $value->id }}')"><i
                                    class='bx bxs-edit-alt'></i>
                            </a>
                            <a onclick="showConfirmDelete('{{ route('deleteCategory', $value->id) }}')"><i
                                    class='bx bxs-trash-alt'></i>
                            </a>
                        </td>
                    </tr>
                    @php
                        $name = $value->categoryName;
                        $id = $value->id;
                    @endphp
                @endforeach
            </tbody>
        </table>

        {{--  
            |---------------------------------------------------------------|
            |================ Add new Category Overlay =====================|
            |---------------------------------------------------------------|
        --}}

        <div id="overlay"></div>
        <form class="newcategory" id="newCategory" action="{{ route('createCategory') }}" method="POST"
            enctype="multipart/form-data" onsubmit="show_Loader()">
            @csrf
            <h3>Add New Category</h3>
            <hr>

            <input type="hidden" name="admin_id" value="{{ $id }}">
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">

            <div class="inputdivs">
                <label for="upload-file" class="choose-file-btn">
                    <span>Choose File <br> <span style="margin:0;padding:0;">(Max = 1MB, Ratio = 3:2)</span></span>
                    <input type="file" id="upload-file" name="CategoryImage" accept=".jpg,.jpeg,.png">
                    <p id="filename"></p>
                </label>
            </div>
            <script>
                function getDominantColor(imageData) {
                    const { data, width, height } = imageData;
                    const colorCounts = {};
                    let maxCount = 0;
                    let dominantColor = '#ffffff';
            
                    for (let i = 0; i < data.length; i += 4) {
                        const r = data[i];
                        const g = data[i + 1];
                        const b = data[i + 2];
                        const alpha = data[i + 3];
                        
                        if (alpha === 0) continue; // Skip transparent pixels
                        
                        const color = `rgb(${r},${g},${b})`;
                        colorCounts[color] = (colorCounts[color] || 0) + 1;
                        
                        if (colorCounts[color] > maxCount) {
                            maxCount = colorCounts[color];
                            dominantColor = color;
                        }
                    }
            
                    return dominantColor;
                }
            
                document.getElementById('upload-file').addEventListener('change', function() {
                    const fileInput = this;
                    const maxSizeInBytes = 1 * 1024 * 1024; // 1MB
                    const targetRatio = 3 / 2;
            
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
            
                        if (file.size > maxSizeInBytes) {
                            alert('File size exceeds the limit of 1MB. Please choose a smaller file.');
                            fileInput.value = '';
                            document.getElementById('filename').textContent = '';
                            return;
                        }
            
                        const img = new Image();
                        img.src = URL.createObjectURL(file);
            
                        img.onload = function() {
                            const imgWidth = img.width;
                            const imgHeight = img.height;
                            const imgRatio = imgWidth / imgHeight;
            
                            let canvasWidth, canvasHeight;
                            let offsetX = 0, offsetY = 0;
            
                            if (imgRatio > targetRatio) {
                                // Image is wider than the target ratio, add padding to the top and bottom
                                canvasWidth = imgWidth;
                                canvasHeight = imgWidth / targetRatio;
                                offsetY = (canvasHeight - imgHeight) / 2;
                            } else if (imgRatio < targetRatio) {
                                // Image is taller or square (1:1), add padding to the sides
                                canvasHeight = imgHeight;
                                canvasWidth = imgHeight * targetRatio;
                                offsetX = (canvasWidth - imgWidth) / 2;
                            } else {
                                // Image has the exact target ratio
                                canvasWidth = imgWidth;
                                canvasHeight = imgHeight;
                            }
            
                            const canvas = document.createElement('canvas');
                            const tempCanvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            const tempCtx = tempCanvas.getContext('2d');
            
                            canvas.width = canvasWidth;
                            canvas.height = canvasHeight;
            
                            tempCanvas.width = imgWidth;
                            tempCanvas.height = imgHeight;
            
                            // Draw the image on a temporary canvas to get image data
                            tempCtx.drawImage(img, 0, 0);
                            const imageData = tempCtx.getImageData(0, 0, imgWidth, imgHeight);
                            const dominantColor = getDominantColor(imageData);
            
                            // Fill the canvas with the dominant color
                            ctx.fillStyle = dominantColor;
                            ctx.fillRect(0, 0, canvasWidth, canvasHeight);
            
                            // Draw the image in the center of the canvas
                            ctx.drawImage(img, offsetX, offsetY, imgWidth, imgHeight);
            
                            canvas.toBlob(function(blob) {
                                const paddedFile = new File([blob], file.name, { type: file.type });
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(paddedFile);
            
                                // Replace the file input files with the new padded file
                                fileInput.files = dataTransfer.files;
            
                                document.getElementById('filename').textContent = paddedFile.name;
                            }, file.type);
                        };
            
                        img.onerror = function() {
                            alert('Failed to load the image. Please try another file.');
                            fileInput.value = '';
                            document.getElementById('filename').textContent = '';
                        };
                    }
                });
            </script>

            <div class="inputdivs">
                <label for="categoryname">Category Name</label>
                <input type="text" id="categoryname" name="categoryName" placeholder="Category Name" required>
            </div>
            @error('categoryName')
                <span class="error-message">{{ $message }}</span>
            @enderror
            <div class="btns">
                <button type="button" id="cancel" onclick="closeAddCatogry()">Cancel</button>
                <input type="submit" value="Add">
            </div>
        </form>


        {{--   
            |---------------------------------------------------------------|
            |================== Edit Category Overlay ======================|
            |---------------------------------------------------------------|
        --}}

        <div id="editOverlay"></div>
        <form class="updateCategory" id="updateCategory" action="{{ route('updateCategory') }}" method="POST"
            enctype="multipart/form-data" onsubmit="show_Loader()">
            @csrf
            <h3>Update Category</h3>
            <hr>

            <div class="inputdivs">
                <label for="upload-update-file" class="choose-file-btn">
                    <span>Choose File <br><span style="color: #b11d1d; margin:0;padding:0;">(Max = 1MB, Ratio = 3:2)*</span></span>
                    <input type="hidden" id="upid" name="id">
                    <input type="file" id="upload-update-file" name="CategoryImage" accept=".jpg,.jpeg,.png">
                    <p id="filenam"></p>
                </label>
            </div>
            <script>
                function getDominantColor(imageData) {
                    const { data, width, height } = imageData;
                    const colorCounts = {};
                    let maxCount = 0;
                    let dominantColor = '#ffffff';
            
                    for (let i = 0; i < data.length; i += 4) {
                        const r = data[i];
                        const g = data[i + 1];
                        const b = data[i + 2];
                        const alpha = data[i + 3];
                        
                        if (alpha === 0) continue; // Skip transparent pixels
                        
                        const color = `rgb(${r},${g},${b})`;
                        colorCounts[color] = (colorCounts[color] || 0) + 1;
                        
                        if (colorCounts[color] > maxCount) {
                            maxCount = colorCounts[color];
                            dominantColor = color;
                        }
                    }
            
                    return dominantColor;
                }
            
                document.getElementById('upload-update-file').addEventListener('change', function() {
                    const fileInput = this;
                    const maxSizeInBytes = 1 * 1024 * 1024; // 1MB
                    const targetRatio = 3 / 2;
            
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
            
                        if (file.size > maxSizeInBytes) {
                            alert('File size exceeds the limit of 1MB. Please choose a smaller file.');
                            fileInput.value = '';
                            document.getElementById('filename').textContent = '';
                            return;
                        }
            
                        const img = new Image();
                        img.src = URL.createObjectURL(file);
            
                        img.onload = function() {
                            const imgWidth = img.width;
                            const imgHeight = img.height;
                            const imgRatio = imgWidth / imgHeight;
            
                            let canvasWidth, canvasHeight;
                            let offsetX = 0, offsetY = 0;
            
                            if (imgRatio > targetRatio) {
                                // Image is wider than the target ratio, add padding to the top and bottom
                                canvasWidth = imgWidth;
                                canvasHeight = imgWidth / targetRatio;
                                offsetY = (canvasHeight - imgHeight) / 2;
                            } else if (imgRatio < targetRatio) {
                                // Image is taller or square (1:1), add padding to the sides
                                canvasHeight = imgHeight;
                                canvasWidth = imgHeight * targetRatio;
                                offsetX = (canvasWidth - imgWidth) / 2;
                            } else {
                                // Image has the exact target ratio
                                canvasWidth = imgWidth;
                                canvasHeight = imgHeight;
                            }
            
                            const canvas = document.createElement('canvas');
                            const tempCanvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            const tempCtx = tempCanvas.getContext('2d');
            
                            canvas.width = canvasWidth;
                            canvas.height = canvasHeight;
            
                            tempCanvas.width = imgWidth;
                            tempCanvas.height = imgHeight;
            
                            // Draw the image on a temporary canvas to get image data
                            tempCtx.drawImage(img, 0, 0);
                            const imageData = tempCtx.getImageData(0, 0, imgWidth, imgHeight);
                            const dominantColor = getDominantColor(imageData);
            
                            // Fill the canvas with the dominant color
                            ctx.fillStyle = dominantColor;
                            ctx.fillRect(0, 0, canvasWidth, canvasHeight);
            
                            // Draw the image in the center of the canvas
                            ctx.drawImage(img, offsetX, offsetY, imgWidth, imgHeight);
            
                            canvas.toBlob(function(blob) {
                                const paddedFile = new File([blob], file.name, { type: file.type });
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(paddedFile);
            
                                // Replace the file input files with the new padded file
                                fileInput.files = dataTransfer.files;
            
                                document.getElementById('filename').textContent = paddedFile.name;
                            }, file.type);
                        };
            
                        img.onerror = function() {
                            alert('Failed to load the image. Please try another file.');
                            fileInput.value = '';
                            document.getElementById('filename').textContent = '';
                        };
                    }
                });
            </script>
            <div class="inputdivs">
                <label for="categorynam">Category Name</label>
                <input type="text" id="categorynam" name="categoryName"placeholder="Category Name" required>
            </div>
            @error('categoryName')
                <span class="error-message">{{ $message }}</span>
            @enderror
            <div class="btns">
                <button type="button" id="cancel" onclick="closeEditCatogry()">Cancel</button>
                <input type="submit" value="Update">
            </div>
        </form>

        {{--   
            |---------------------------------------------------------------|
            |===================== Confirm deletion ========================|
            |---------------------------------------------------------------|
        --}}
        <div id="confirmDeletionOverlay"></div>
        <div class="confirmDeletion" id="confirmDeletion">
            <h3 id="message-text">Are you sure you want to delete this category</h3>
            <div class="box-btns">
                <button id="confirm">Delete</button>
                <button id="close" onclick="closeConfirmDelete()">Close</button>
            </div>
        </div>

    </main>

    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#categoryTable').DataTable({
                "paging": true,
                "lengthMenu": [5, 10, 25, 50, 100]
            });
        });

        function addCategory() {
            let overlay = document.getElementById('overlay');
            let popup = document.getElementById('newCategory');
            overlay.style.display = 'block';
            popup.style.display = 'flex';
        }

        function closeAddCatogry() {
            let overlay = document.getElementById('overlay');
            let popup = document.getElementById('newCategory');
            overlay.style.display = 'none';
            popup.style.display = 'none';
        }

        function showConfirmDelete(deleteUrl) {
            let confirmDeletionOverlay = document.getElementById('confirmDeletionOverlay');
            let confirmDeletionPopup = document.getElementById('confirmDeletion');
            confirmDeletionOverlay.style.display = 'block';
            confirmDeletionPopup.style.display = 'block';

            let confirmButton = document.getElementById('confirm');
            confirmButton.onclick = function() {
                show_Loader();
                window.location.href = deleteUrl;
            };
        }

        function closeConfirmDelete() {
            let confirmDeletionOverlay = document.getElementById('confirmDeletionOverlay');
            let confirmDeletionPopup = document.getElementById('confirmDeletion');
            confirmDeletionOverlay.style.display = 'none';
            confirmDeletionPopup.style.display = 'none';
        }

        const uploadFile = document.getElementById('upload-file');
        const filenameSpan = document.getElementById('filename');
        uploadFile.addEventListener('change', function(e) {
            const fileName = this.value.split('\\').pop();
            filenameSpan.textContent = fileName ? fileName : 'No file chosen';
        });

        function editcategory(categoryName, id) {
            let overlay = document.getElementById('editOverlay');
            let popup = document.getElementById('updateCategory');
            overlay.style.display = 'block';
            popup.style.display = 'flex';
            document.getElementById('categorynam').value = categoryName;
            document.getElementById('upid').value = id;
        }

        function closeEditCatogry() {
            let overlay = document.getElementById('editOverlay');
            let popup = document.getElementById('updateCategory');
            overlay.style.display = 'none';
            popup.style.display = 'none';
        }

        const uploadUpdatedFile = document.getElementById('upload-update-file');
        const filenamSpan = document.getElementById('filenam');
        uploadUpdatedFile.addEventListener('change', function(e) {
            const fileNam = this.value.split('\\').pop();
            filenamSpan.textContent = fileNam ? fileNam : 'No file chosen';
        });
    </script>
@endsection
