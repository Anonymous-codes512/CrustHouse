@extends('Components.Manager')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Manager - Product';
    });
</script>
@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Manager/product.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush
@push('scripts')
    <script src="{{ asset('JavaScript\product.js') }}"></script>
@endpush
<style>
    #productTable_paginate,
    #productTable_filter,
    .dataTables_length,
    .dataTables_info {
        margin: 0.5vw 4.2vw !important;
        font-size: 0.8rem !important;
    }

    #productTable_next,
    #productTable_previous,
    #productTable_paginate span a {
        margin: 0.5vw !important;
        padding: 0 !important;
        font-size: 0.8rem
    }

    table.dataTable {
        border-collapse: collapse !important;
    }
</style>

@section('main')
    <main id="product">
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
        <div class="newCategory">
            <button onclick="addProduct()">Add New Product</button>
        </div>

        @php
            $productsData = $productsData;
            $branch_id = session('branch_id');
        @endphp

        <table id="productTable">
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Product Variations</th>
                    <th>Product Prices</th>
                    <th>Product Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $renderedProducts = [];
                @endphp
                @foreach ($productsData as $product)
                    @php
                        if (isset($renderedProducts[$product->productName])) {
                            continue;
                        }

                        $variations = [];
                        $prices = [];
                        foreach ($productsData as $variation) {
                            if ($variation->productName === $product->productName) {
                                $variations[] = $variation->productVariation;
                                $prices[] = $variation->productPrice;
                            }
                        }

                        $renderedProducts[$product->productName] = true;
                    @endphp
                    <tr style="border-bottom: 1px solid #000;">
                        <td><img src="{{ asset('Images/ProductImages/' . $product->productImage) }}" alt="Image"></td>
                        <td>{{ $product->productName }}</td>
                        <td>
                            <div style="display: flex; flex-direction:column;">
                                @foreach ($variations as $variation)
                                    <p>{{ $variation }}</p>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction:column;">
                                @foreach ($prices as $price)
                                    <p>Rs. {{ $price }} </p>
                                @endforeach
                            </div>
                        <td>{{ $product->category_name }}</td>
                        <td>
                            <a onclick="editProduct({{ json_encode($product) }}, {{ json_encode($productsData) }})"><i
                                    class='bx bxs-edit-alt'></i></a>
                            <a onclick="showConfirmDelete('{{ route('deleteProduct', $product->id) }}')"><i
                                    class='bx bxs-trash-alt'></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{--  
            |---------------------------------------------------------------|
            |================ Add new Product Overlay ======================|
            |---------------------------------------------------------------|
        --}}

        <div id="overlay"></div>
        <form class="newproduct" id="newProduct" action="{{ route('createProduct') }}" method="POST"
            enctype="multipart/form-data" onsubmit="show_Loader()">
            @csrf
            <h3>Add New Product</h3>
            <hr>
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">
            <div class="inputdivs">
                <label for="category">Select category</label>
                <select name="categoryId" id="category" required>
                    <option value="" selected disabled>Select Product Category</option>
                    @foreach ($categoryData as $category)
                        <option value="{{ $category->id }},{{ $category->categoryName }}">{{ $category->categoryName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="inputdivs inputProductName">
                <div class="ProductName">
                    <label for="productName">Product Name</label>
                    <input type="text" id="productName" name="productName" placeholder="Product Name" required>
                </div>
                <div class="ProductVariation">
                    <label for="noOfVariations">Variations</label>
                    <input type="number" id="noOfVariations" min="0" name="noOfVariations"
                        oninput="updateVariationFields(this.value)" placeholder="Ex 4 etc">
                </div>
            </div>
            <p id="msg"></p>
            <div id="variationsGroup"></div>

            <div class="inputdivs">
                <label for="upload-file" class="choose-file-btn">
                    <span>Choose File <br><span style="color: #b11d1d; margin:0;padding:0;">(Max = 1MB, Ratio = 3:2)*</span></span>
                    <input type="file" id="upload-file" name="productImage" accept=".jpg,.jpeg,.png" required>
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
            
            <div class="btns">
                <button id="cancel" onclick="closeAddProduct()">Cancel</button>
                <input type="submit" value="Add">
            </div>

        </form>

        {{--  
            |---------------------------------------------------------------|
            |=================== Edit Product Overlay ======================|
            |---------------------------------------------------------------|
        --}}

        <div id="editOverlay"></div>
        <form class="editproduct" id="editProduct" action="{{ route('updateProduct') }}" method="POST"
            enctype="multipart/form-data" onsubmit="show_Loader()">
            @csrf
            <h3>Edit Product</h3>
            <hr>  

            <input type="hidden" id="pId" name="pId">
            <input type="hidden" id="cId" name="CId">

            <div class="inputdivs">
                <label for="pName">Product Name</label>
                <input type="text" id="pName" name="productName" placeholder="Product Name" readonly required>
            </div>

            <div class="inputdivs inputProductName" id="editVariationsGroup"></div>

            <div class="inputdivs">
                <label for="upload-update-file" class="choose-file-btn">
                    <span>Choose File <br><span style="color: #b11d1d; margin:0;padding:0;">(Max = 1MB, Ratio = 3:2)*</span></span>
                    <input type="file" id="upload-update-file" name="productImage" accept=".jpg,.jpeg,.png">
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

            <div class="btns">
                <button type="button" id="cancel" onclick="closeEditProduct()">Cancel</button>
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
            <h3 id="message-text">Are you sure you want to delete this product</h3>
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
            $('#productTable').DataTable({
                "paging": true,
                "lengthMenu": [5, 10, 25, 50, 100]
            });
        });

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
    </script>
@endsection
