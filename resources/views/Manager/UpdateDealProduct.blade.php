@extends('Components.Manager')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Manager - Add Deal Product';
    });
</script>
@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Manager/deal.css') }}">
@endpush

@section('main')
    <main id="deal_Products">
        @php
            $dealId = $dealId;
            $dealProducts = $dealproducts;
            $dealProductsIds = [];
            $dealActualPrice = $dealProducts->dealActualPrice;
            $dealDiscountedPrice = $dealProducts->dealDiscountedPrice;
            $user_id = session('id');
            $branch_id = session('branch_id');
            foreach ($dealProducts->handlers as $product) {
                $dealProductsIds[] = $product->product_id;
            }
        @endphp
        <section class="products">
            @foreach ($Products as $product)
                @php
                    $isSelected = in_array($product->id, $dealProductsIds);
                @endphp
                <div class="imgbox {{ $isSelected ? 'selected' : '' }}" onclick="toggleProductSelection(this)">
                    <img src="{{ asset('Images/ProductImages/' . $product->productImage) }}" alt="Product">
                    <p class="category_name">{{ $product->category_name }}</p>
                    <p class="product_id">{{ $product->id }}</p>
                    <p class="product_name">{{ $product->productName }}</p>
                    <p class="product_size">{{ $product->productVariation }}</p>
                    <p class="product_price">{{ $product->productPrice }} Pkr</p>
                </div>
            @endforeach
        </section>
        <div id="data">
            <input type="hidden" id="productIds" required>
            <input type="hidden" id="products" required>
            <input type="hidden" id="size">
            <input type="hidden" id="price">
            <input type="button" id="adddeal" value="Add Product to Deal"
                onclick="dealDetails({{ json_encode($dealProducts) }})">
        </div>

        <div id="dealProductInfoOverlay"></div>
        <form class="dealProdInfo" id="dealProdInfo" action="{{ route('addDealProduct') }}" method="POST"
            enctype="multipart/form-data" onsubmit="show_Loader()">
            @csrf
            <h3>Deal Details</h3>
            <hr>

            <input type="hidden" name="id" value="{{ $dealId }}">
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">

            <div class="inputdivs" id="productsNames">
            </div>

            <div class="inputdiv">
                <label for="dealPrice">Deal Actual Price:</label>
                <input type="text" name="currentDealPrice" id="currentDealPrice" required>

                <label for="dealFinalPrice">Deal Final Price:</label>
                <input type="number" name="dealFinalPrice" id="dealFinalPrice" min="1"
                    placeholder="Enter Final Price of Deal" required>
            </div>

            <div class="btns">
                <button type="button" id="cancel" onclick="closeDetails()">Cancel</button>
                <input type="button" value="Calculate"
                    onclick="calculatedealPrice({{ json_encode($dealActualPrice) }}, {{ json_encode($dealDiscountedPrice) }})">
                <input type="submit" value="Add Product">
            </div>
        </form>
    </main>

    <script>
        function toggleProductSelection(element) {

            let productId = element.querySelector('.product_id').textContent;
            let productName = element.querySelector('.product_name').textContent;
            let productVariation = element.querySelector('.product_size').textContent;
            let productPrice = element.querySelector('.product_price').textContent;

            let productIdField = document.getElementById('productIds');
            let productNameField = document.getElementById('products');
            let productSizeField = document.getElementById('size');
            let productPriceField = document.getElementById('price');

            if (element.classList.contains('selected')) {
                element.classList.remove('selected');

                productIdField.value = productIdField.value.replace(productId + ',', '');
                productNameField.value = productNameField.value.replace(productName + ',', '');
                productSizeField.value = productSizeField.value.replace(productVariation + ',', '');
                productPriceField.value = productPriceField.value.replace(productPrice + ',', '');

            } else {
                element.classList.add('selected');

                productIdField.value += productId + ',';
                productNameField.value += productName + ',';
                productSizeField.value += productVariation + ',';
                productPriceField.value += productPrice + ',';
            }
        }

        let dealPrice = 0;

        function dealDetails(dealProducts) {
            let products = document.getElementById('products').value;
            if (products.trim() === '') {
                alert("Select a Product First");
                closeDetails();
            } else {
                let overlay = document.getElementById('dealProductInfoOverlay');
                let prompt = document.getElementById('dealProdInfo');

                let Ids = document.getElementById('productIds').value;
                let productIdArray = Ids.split(',').filter(Boolean);

                let container = document.getElementById('productsNames');
                let productNameArray = products.split(',').filter(Boolean);

                let sizes = document.getElementById('size').value;
                let sizeArray = sizes.split(',').filter(Boolean);

                let prices = document.getElementById('price').value;
                let priceArray = prices.split(',').filter(Boolean);

                container.innerHTML = '';

                let productsDetails = [];

                for (let i = 0; i < productNameArray.length; i++) {
                    let product = {
                        id: productIdArray[i],
                        name: productNameArray[i],
                        size: sizeArray[i],
                        price: priceArray[i]
                    };
                    productsDetails.push(product);
                }

                const div = document.createElement('div');
                div.style.display = 'flex';
                div.style.margin = 'auto';
                div.style.alignItems = 'center';
                div.style.flexWrap = 'wrap';

                const RowData1 = document.createElement('p');
                RowData1.display = 'flex';
                RowData1.style.margin = '5px';
                RowData1.style.width = '184px';
                RowData1.textContent = "Product Name";

                const RowData2 = document.createElement('p');
                RowData2.display = 'flex';
                RowData2.style.margin = '5px';
                RowData2.style.width = '180px';
                RowData2.textContent = "Product Variation";

                const RowData3 = document.createElement('p');
                RowData3.display = 'flex';
                // RowData3.style.margin = '5px';
                RowData3.textContent = "Quantity";
                RowData3.style.width = '180px';

                const RowData4 = document.createElement('p');
                RowData4.display = 'flex';
                // RowData4.style.margin = '5px';
                RowData4.textContent = "Total Price";
                RowData4.style.width = '180px';

                div.appendChild(RowData1);
                div.appendChild(RowData2);
                div.appendChild(RowData3);
                div.appendChild(RowData4);
                container.appendChild(div);

                productsDetails.forEach((product, index) => {
                    const prod_div = document.createElement("div");
                    const id_input = document.createElement("input");
                    const size_input = document.createElement("input");
                    const name_input = document.createElement("input");
                    const quantity_input = document.createElement("input");
                    const total_price_input = document.createElement("input");
                    let messageP = document.createElement("p");

                    prod_div.style.display = 'flex';
                    prod_div.style.margin = 'auto';
                    prod_div.style.alignItems = 'center';
                    prod_div.style.flexWrap = 'wrap';

                    id_input.type = "hidden";
                    id_input.value = product.id;
                    id_input.name = 'product_id' + index;

                    name_input.type = "text";
                    name_input.style.margin = '5px';
                    name_input.value = product.name;
                    name_input.readOnly = true;
                    name_input.name = 'product_name_' + index;

                    size_input.type = "text";
                    size_input.style.margin = '5px';
                    size_input.value = product.size;
                    size_input.readOnly = true;
                    size_input.name = 'product_variation_' + index;

                    quantity_input.min = "1";
                    quantity_input.style.margin = '5px';
                    quantity_input.placeholder = 'Enter Product Quantity';
                    quantity_input.addEventListener('input', function() {
                        calculateTotal(this, total_price_input, product.price);
                    });
                    quantity_input.id = 'quantity_input_' + index;
                    quantity_input.name = 'product_quantity_' + index;
                    quantity_input.classList.add('product_quantity');

                    messageP.style.display = 'none';
                    messageP.style.margin = '0';
                    messageP.style.padding = '0';
                    messageP.style.fontSize = '12px';
                    messageP.style.color = 'red';
                    messageP.textContent = 'Product quantity must be a number greater than 0';
                    messageP.classList.add('messageP');

                    total_price_input.type = "text";
                    total_price_input.classList.add('total-price');
                    total_price_input.style.margin = '5px';
                    total_price_input.readOnly = true;
                    total_price_input.name = 'product_total_price_' + index;

                    prod_div.appendChild(id_input);
                    prod_div.appendChild(name_input);
                    prod_div.appendChild(size_input);
                    prod_div.appendChild(quantity_input);
                    prod_div.appendChild(total_price_input);
                    prod_div.appendChild(messageP);
                    container.appendChild(prod_div);
                });
                overlay.style.display = 'block';
                prompt.style.display = 'flex';
            }
        }

        function calculateTotal(quantity_input, total_price_input, price) {
            let quantity = parseInt(quantity_input.value);
            let total = isNaN(quantity) ? 0 : quantity * parseFloat(price);
            total_price_input.value = total.toFixed(2) + ' Pkr';
            return total;
        }

        function calculatedealPrice(dealActualPrice, dealDiscountedPrice) {

            let isValid = true;

            let productQuantities = document.getElementsByClassName('product_quantity');
            let message = document.getElementsByClassName('messageP');
            let productQuantitiesArray = Array.from(productQuantities);
            let messagesArray = Array.from(message);

            productQuantitiesArray.forEach((element, index) => {
                let quantity = parseInt(element.value);
                if (isNaN(quantity) || quantity <= 0) {
                    messagesArray[index].style.display = 'block';
                    isValid = false;
                } else {
                    messagesArray[index].style.display = 'none';
                }
            });
            if (!isValid) {
                return;
            }

            let ActualPrice = parseFloat(dealActualPrice.match(/\d+/)[0]);
            let DiscountedPrice = parseFloat(dealDiscountedPrice.match(/\d+/)[0]);
            let dealPrice = ActualPrice;
            let deal_discounted_Price = DiscountedPrice;
            let totalInputs = document.querySelectorAll('.total-price');
            let parts, numericPart, currencyPart;
            totalInputs.forEach(input => {
                parts = input.value.split(' ');
                numericPart = parseFloat(parts[0]);
                currencyPart = parts[1];
                dealPrice += numericPart;
                deal_discounted_Price += numericPart;
            });
            let currentDealPrice = document.getElementById('currentDealPrice');
            currentDealPrice.value = dealPrice + " " + currencyPart;
            let DealPrice = document.getElementById('dealFinalPrice');
            DealPrice.value = deal_discounted_Price;
        }

        function closeDetails() {
            let overlay = document.getElementById('dealProductInfoOverlay');
            let prompt = document.getElementById('dealProdInfo');
            let totalInputs = document.querySelectorAll('.total-price');
            let currentDealPrice = document.getElementById('currentDealPrice');
            let dealFinalPrice = document.getElementById('dealFinalPrice');
            totalInputs.forEach(input => {
                input.value = "";
            });
            currentDealPrice.value = "";
            dealFinalPrice.value = "";
            overlay.style.display = 'none';
            prompt.style.display = 'none';
        }
    </script>
@endsection
