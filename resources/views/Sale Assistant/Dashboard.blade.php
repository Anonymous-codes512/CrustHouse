@extends('Components.Salesman')

@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Salesman/dashboard.css') }}">
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Salesman - Dashboard';
    });
</script>

@section('main')
    @if (session('pdf_filename'))
        <input type="hidden" value="{{ session('pdf_filename') }}" id="pdf_link">
        <a id="orderRecipt" href="{{ asset('PDF/' . session('pdf_filename')) }}" download>Download PDF</a>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let pdfLink = document.getElementById('orderRecipt');
                pdfLink.style.display = 'none';
                pdfLink.click();
                let file_name = document.getElementById('pdf_link').value;
                route = "{{ route('deleteReceiptPDF', '_file_name') }}";
                route = route.replace('_file_name', file_name);
                window.location.href = route;
            });
        </script>
    @endif

    @if (session('success'))
        <div id="success" class="alert alert-success">
            {{ session('success') }}
        </div>
        <script>
            localStorage.removeItem('ProductsInCart');
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

    <input id="orderNo" type="hidden" value="1">
    <main id="salesman">
        @php
            $allProducts = $Products;
            $staff_id = $staff_id;
            $branch_id = $branch_id;
            $addons = $addons;
            $servingProducts = $cartProducts
                ->filter(function ($product) {
                    return $product->order_status === 0;
                })
                ->groupBy('order_number');
            $finalizeProducts = $cartProducts->filter(function ($product) {
                return $product->order_status === null;
            });

            $totalbill = 0;
            $taxes = $taxes;
            $discounts = $discounts;
            $maximum_discount_percentage_value = $branch_data->max_discount_percentage;
            $orderTypeArray = [];
            $discountTypeArray = [];
            foreach ($payment_methods as $value) {
                if ($value->order_type != null) {
                    $orderTypeArray[] = $value->order_type;
                } elseif ($value->discount_type != null) {
                    $discountTypeArray[] = $value->discount_type;
                }
            }
        @endphp

        <div id="productsSide">
            <div id="category_bar">
                <div onclick="selectCategory('{{ route('salesman_dashboard', [$staff_id, $branch_id]) }}', this)">
                    <a id="all_category" class="category_link">All</a>
                </div>
                @foreach ($Categories as $category)
                    <div
                        onclick="selectCategory('{{ route('salesman_dash', [$category->categoryName, $staff_id, $branch_id]) }}', this)">
                        <a class="category_link">{{ $category->categoryName }}</a>
                    </div>
                @endforeach
                <div onclick="selectCategory('{{ route('salesman_dash', ['Deals', $staff_id, $branch_id]) }}', this)">
                    <a class="category_link">Deals</a>
                </div>
            </div>

            <div id="products">
                @php
                    $displayedProductNames = [];
                    $displayedDealTitles = [];
                @endphp

                @if ($Products !== null)
                    @foreach ($Products->items() as $product)
                        @if ($product->category_name !== 'Addons' && !in_array($product->productName, $displayedProductNames))
                            @php
                                $displayedProductNames[] = $product->productName;
                            @endphp

                            <div id="imageBox" class="imgbox"
                                onclick="showProductAddToCart({{ json_encode($product) }} , {{ json_encode($allProducts) }}, {{ json_encode($addons) }})">
                                <img rel="preload" src="{{ asset('Images/ProductImages/' . $product->productImage) }}" alt="Product"
                                    loading="lazy" as="image">
                                <p class="product_name">{{ $product->productName }}</p>
                            </div>
                        @endif
                    @endforeach
                @elseif ($Deals !== null)
                    @foreach ($Deals as $deal)
                        @if (!in_array($deal->deal->dealTitle, $displayedDealTitles))
                            @php
                                $displayedDealTitles[] = $deal->deal->dealTitle;
                            @endphp
                            @if ($deal->deal->dealStatus != 'not active')
                                <div id='imageBox' class="imgbox"
                                    onclick="showDealAddToCart({{ json_encode($deal) }}, {{ json_encode($Deals) }}, {{ json_encode($allProducts) }})">
                                    <img rel="preload" src="{{ asset('Images/DealImages/' . $deal->deal->dealImage) }}" alt="Product"
                                        loading="lazy" as="image">
                                    <p class="product_name">{{ $deal->deal->dealTitle }}</p>
                                </div>
                            @endif
                        @endif
                    @endforeach
                @endif
            </div>
            {{-- @if ($Products !== null)
                <div id="deals_seperate_section">
                    <h3 id="deals_seperate_section_heading">Deals</h3>
                    <div id="deals_seperate_section_imgDiv">
                        @foreach ($Deals as $deal)
                            @if ($deal->deal !== null && !in_array($deal->deal->dealTitle, $displayedDealTitles))
                                @php
                                    $displayedDealTitles[] = $deal->deal->dealTitle;
                                @endphp
                                @if ($deal->deal->dealStatus != 'not active')
                                    <div class="deal_imgbox"
                                        onclick="showDealAddToCart({{ json_encode($deal) }}, {{ json_encode($Deals) }}, {{ json_encode($allProducts) }})">
                                        <img src="{{ asset('Images/DealImages/' . $deal->deal->dealImage) }}"
                                            alt="Product" loading="lazy">
                                        <p class="product_name">{{ $deal->deal->dealTitle }}</p>
                                        {{-- <p class="product_price">{{ $deal->deal->dealDiscountedPrice }}</p> -}}
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif --}}
            @if ($Deals === null || $Products !== null)
                <div class="pagination_div">
                    {{ $Products->links() }} <!-- Pagination links -->
                </div>
            @endif
        </div>

        <style>
            .pagination_div {
                display: flex;
                justify-content: center;
                margin: 7px 0;
            }

            .pagination {
                display: flex;
                align-items: center;
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .page-item {
                margin: 0 5px;
            }

            .page-link {
                display: block;
                padding: 4px 12px;
                font-size: 1rem;
                background-color: transparent;
                color: #000;
                border: 1px solid transparent;
                border-radius: 5px;
                text-decoration: none;
                transition: background-color 0.3s, transform 0.2s;
            }

            .page-link:hover {
                background-color: #ffbb00;
                transform: translateY(-2px);
            }

            .page-item.active .page-link {
                background-color: #ffbb00;
                color: #000;
                border-color: #ffbb00;
            }

            .page-item.disabled .page-link {
                background-color: transparent;
                color: #000;
                border: 1px solid black;
                pointer-events: none;
            }

            .page-item a {
                color: black;
                border: 1px solid black;
            }

            .page-item.disabled a {
                color: transparent;
                border-color: black;
            }
        </style>

        <div id="receipt">
            <h4 id="heading">Receipt</h4>
            <div id="cart">

                <input type="hidden" name="salesman_id" id="salesman_id" value={{ $staff_id }}>
                <div id="selectedProducts" name="products">
                </div>

                <script>
                    let taxes = @json($taxes);
                </script>

                <form action="{{ route('placeOrder', $staff_id) }}" method="post" enctype="multipart/form-data"
                    onsubmit="return validateDiscount()">
                    @csrf
                    <input type="hidden" name="cartItems" id="cartItems">
                    <input type="hidden" name="branch_id" value="{{ $branch_id }}">

                    <div class="payment-div">
                        <div class="cash-fields">
                            <div class="paymentfields">
                                <label for="totalbill">Total Amount</label>
                                <input type="text" name="totalbill" id="totalbill" readonly>
                                <input type="hidden" name="totaltaxes" id="totaltaxes" readonly>
                            </div>
                            <div class="paymentfields">
                                <label for="paymentMethod">Payment Method:</label>
                                <div class="paymentfields"
                                    style="flex-direction: row; align-items: center; justify-content:space-between;">
                                    <span id="false-option0">Cash</span>
                                    <label class="switch">
                                        <input id="paymentmethod" type="checkbox">
                                        <span class="slider round"></span>
                                    </label>
                                    <span id="true-option0">Online</span>
                                </div>
                                <select style="display: none; background-color: #fff" name="payment_method"
                                    id="paymentMethod" onchange="adjustTax()">
                                    @foreach ($payment_methods as $methods)
                                        @if ($methods->payment_method != null)
                                            <option value="{{ $methods->payment_method }}">{{ $methods->payment_method }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="paymentfields"
                                style="flex-direction: row; justify-content:space-between; align-items:center;">
                                <span id="false-option">
                                    @if ($orderTypeArray)
                                        {{ $orderTypeArray[0] }}
                                    @else
                                        Dine-In
                                    @endif &nbsp;&nbsp;
                                </span><label class="switch">
                                    <input id="order_type" type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label><span id="true-option">
                                    @if ($orderTypeArray)
                                        {{ $orderTypeArray[1] }}
                                    @else
                                        Takeaway
                                    @endif
                                </span>
                            </div>
                            <div class="paymentfields">
                                <label for="recievecash"> Cash Tendered
                                </label>
                                <input style="background-color: #fff" type="number" name="recievecash" id="recievecash"
                                    placeholder="Rupees" oninput="validateNumericInput(this)" required>
                            </div>

                            <div class="paymentfields">
                                <label for="change">Balance</label>
                                <input type="number" name="change" id="change" min="0" placeholder="Rupees"
                                    readonly>
                            </div>
                            <input type="hidden" name="orderType" id="orderTypeHidden">
                        </div>

                        <div style="display: flex; flex-direction:column; width: 48%;">
                            <div class="discount-field">
                                <div class="paymentfields" style="flex-direction:row;align-items: center;">
                                    <label style="width:200px" id="toggle-text" for="discountEnableDisable">Enable
                                        Discount</label>
                                    <input type="checkbox" name="discount" id="discountEnableDisable"
                                        onclick="toggleDiscount()">
                                </div>
                                {{-- <div>
                                    <h3>Execution Time: {{ $executionTime }} seconds</h3>
                                </div> --}}

                                <div id="discount-Type-div" class="paymentfields" style="display:none">
                                    <label for="discountType">Type of Discount</label>
                                    <div id="discountTypeDiv"
                                        style="flex-direction: row; display:none; justify-content:center; align-items:center;">
                                        <span id="false-option2">
                                            @if ($discountTypeArray)
                                                {{ $discountTypeArray[1] }}
                                            @else
                                                Percentage
                                            @endif
                                        </span>
                                        <label class="switch">
                                            <input id="discounttype" type="checkbox" value="%"
                                                onclick="updateTotalONSwitch({{ json_encode($maximum_discount_percentage_value) }})">>
                                            <span class="slider round"></span>
                                        </label>
                                        <span id="true-option2">
                                            @if ($discountTypeArray)
                                                {{ $discountTypeArray[0] }}
                                            @else
                                                Fixed
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <input type="hidden" name="discount_type" id="discountType" value="%">

                                <div class="paymentfields" id="discountFieldDiv" style="display: none">
                                    <label for="discount">Discount Applied</label>
                                    <input style="background-color: #fff" type="number" name="discount" id="discount"
                                        min="0" placeholder="Rupees" disabled step="any"
                                        oninput="updateTotalONInput({{ json_encode($maximum_discount_percentage_value) }})">
                                </div>

                                <div class="paymentfields" id="discountReasonDiv" style="display: none">
                                    <label for="discount_reason">Reason for Discount
                                    </label>
                                    <select style="background-color: #fff" name="discount_reason" id="discount_reason"
                                        disabled>
                                        <option value="" selected>Select</option>
                                        @foreach ($discounts as $discount)
                                            <option value="{{ $discount->discount_reason }}">
                                                {{ $discount->discount_reason }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="TablesList" class="tablesList">
                                <label for="tables_list">Available Tables</label>
                                <select name="table_number" id="tables_list" onchange="handleTableChange()">
                                    <option value="">Select Tables</option>
                                    @foreach ($dineInTables as $table)
                                        @if ($table->table_status === 1)
                                            <option value="{{ $table->id }}">{{ $table->table_number }}</option>
                                        @endif
                                    @endforeach
                                    <option value="0">Finalize Order</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="buttons">
                        <input type="submit" id="proceed" value="Proceed">
                        <button onclick="window.location='{{ route('clearCart', $staff_id) }}'" type="button"
                            id="clearCart">Clear
                            Cart</button>
                    </div>
                </form>
            </div>
        </div>

        {{--  
            |---------------------------------------------------------------|
            |========================= Add to cart UI ======================|
            |---------------------------------------------------------------|
        --}}

        <div id="overlay"></div>
        <div id="addToCart" class="addTocart">
            @csrf
            <input type="hidden" id="product_id" name="product_id">
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">
            <input type="hidden" name="salesman_id" id="salesman_id" value={{ $staff_id }}>

            <p id="headTitle" class="head1">Customize Item</p>
            <input id="prodName" class="prodName" name="productname" style="border: none; display:none;" readonly>
            <div style="display: none;margin: 10px 0.5vw; border-bottom:1px solid #393939" id="productCustomDiv">
                <span id="prodPrice" class="prodPrice" style="width: 50%;"> </span>
                <input name="productprice" style="border: none; text-align:right; font-size:0.9vw;" id="price"
                    readonly>
            </div>
            {{-- <p class="head1">Please Select</p> --}}

            <label id="prodVariationLabel" class="prodVariationLabel" for="prodVariation">Product Variation</label>
            <select tabindex="0" name="prodVariation" id="prodVariation" class="prodVariation"></select>

            <label id="addOnsLabel" class="addOnsLabel" for="addons" style="display:none;">AddOns</label>
            <select name="addOn" id="addons" class="addons" style="display:none;">

            </select>

            {{-- <label id="drinkFlavourLabel" class="drinkFlavourLabel" for="drinkFlavour">Drink Flavour</label>
            <select name="drinkFlavour" id="drinkFlavour" class="drinkFlavour"></select> --}}

            <div id="quantity" class="quantity">
                <p>Quantity</p>
                <i onclick="decreaseQuantity()" class='bx bxs-checkbox-minus'></i>
                <input type="number" name="prodQuantity" id="prodQuantity" value="1" min="1">
                <i onclick="increaseQuantity()" class='bx bxs-plus-square'></i>
            </div>

            <p id="bottom" class="bottom">Total Price <input name="totalprice"
                    style="background-color:transparent; border: none; text-align:right;" id="totalprice" readonly></p>

            <div id="buttons">
                <button type="button" onclick="closeAddToCart()">Close</button>
                <input id="addbtn" type="button" onclick="addProductToCart()" value="Add">
            </div>
        </div>

        <div id="addDealToCart" class="addTocart">
            @csrf
            <input type="hidden" id="deal_id" name="deal_id">
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">
            <input type="hidden" name="salesman_id" id="salesman_id" value={{ $staff_id }}>

            <p id="dealTitle" class="head1">Customize Item</p>
            <input id="dealName" class="prodName" name="productname" style="border: none; display:none;" readonly>
            <div style="display: none; margin: 10px 0.5vw; border-bottom:1px solid #393939" id="dealCustomDiv">
                <span id="prodPrice" class="prodPrice" style="width: 50%;"> </span>
                <input name="productprice" style="border: none; text-align:right; font-size:0.9vw;" id="dealPrice"
                    readonly>
            </div>
            {{-- <p class="head1">Please Select</p> --}}

            <label id="pizzaVariationLabel" class="prodVariationLabel" for="prodVariation">Pizza Variation</label>
            <select tabindex="0" name="prodVariation" id="pizzaVariation" class="prodVariation"></select>

            <label id="toppingLabel" class="addOnsLabel" for="addons" style="display:none;">Select Topping</label>
            <select name="addOn" id="topping" class="addons" style="display:none;">

            </select>

            <label id="drinkFlavourLabel" class="drinkFlavourLabel" for="drinkFlavour">Select Drink Flavour</label>
            <select name="drinkFlavour" id="drinkFlavour" class="drinkFlavour"></select>

            <div id="dealquantity" class="quantity">
                <p>Quantity</p>
                <i onclick="decreaseDealQuantity()" class='bx bxs-checkbox-minus'></i>
                <input type="number" name="prodQuantity" id="dealQuantity" value="1" min="1">
                <i onclick="increaseDealQuantity()" class='bx bxs-plus-square'></i>
            </div>

            <p id="dealBottom" class="bottom">Total Price <input name="totalprice"
                    style="background-color:transparent; border: none; text-align:right;" id="totalDealPrice" readonly>
            </p>

            <div id="buttons">
                <button type="button" onclick="closeDealAddToCart()">Close</button>
                <input id="addbtn" type="button" onclick="addDealToCart()" value="Add">
            </div>
        </div>

        {{--  
            |---------------------------------------------------------------|
            |========================= Dine-In Orders ======================|
            |---------------------------------------------------------------|
        --}}

        <div id="dineInOrdersOverlay"></div>
        <div id="dineInOrdersDiv">
            <h3>Dine-In Orders</h3>
            <div id="dineInOrdersTable">
                <div class="searchBarDiv">
                    <input type="text" id="searchBar" name="search" placeholder="Search by table number..."
                        style="background-image: url('{{ asset('Images/search.png') }}');">
                </div>
                <div id="tableDiv">
                    <table>
                        <thead>
                            <tr>
                                <th>Table #</th>
                                <th>Table Number</th>
                                <th>Order #</th>
                                <th>Order Items</th>
                                <th>Item Qty</th>
                                <th>Item Price</th>
                                <th>Total Bill</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($servingProducts as $orderNumber => $orders)
                                <tr class="table-row">
                                    <td id="table-number"> {{ $orders->first()->table_id }}</td>
                                    <td id="table-number"> {{ $orders->first()->dineInTable->table_number }}</td>
                                    <!-- Assuming table_id is used for table number -->
                                    <td>{{ $orderNumber }}</td>
                                    <td>
                                        @foreach ($orders as $order)
                                            <div>{{ $order->productName }}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($orders as $order)
                                            <div>{{ $order->productQuantity }}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($orders as $order)
                                            <div>{{ $order->totalPrice }}</div>
                                        @endforeach
                                    </td>
                                    <td>Rs.
                                        {{ $orders->sum(fn($order) => floatval(str_replace('Rs. ', '', $order->totalPrice))) }}
                                    </td> <!-- Sum up the total price for all items -->
                                    <td>
                                        <a title="Add New Product"
                                            onclick="addNewProductToDineInOrder({{ json_encode($orders) }}, '{{ route('addNewProductToDineInOrder', [$orderNumber, $orders->first()->table_id]) }}')"><i
                                                class='bx bxs-cart-add'></i></a>
                                        <a title="Proceed to Payment"
                                            href="{{ route('addNewProductToDineInOrder', [$orderNumber, $orders->first()->table_id]) }}"><i
                                                class='bx bxs-right-arrow-square'></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
            <div id="closeBtn">
                <button onclick="hideDineInOrders()">Close</button>
            </div>
        </div>

        {{--  
        |---------------------------------------------------------------|
        |========================== Online Orders ======================|
        |---------------------------------------------------------------|
        --}}

        <div id="onlineOrdersOverlay"></div>
        <div id="onlineOrdersDiv">
            <h3>Online Orders</h3>
            <div id="dineInOrdersTable">
                <div class="searchBarDiv">
                    <input type="text" id="onlineOrderSearchBar" name="search"
                        placeholder="Search by Order Number..."
                        style="background-image: url('{{ asset('Images/search.png') }}');">
                </div>
                <div id="tableDiv">
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer Name</th>
                                <th>Customer Phone</th>
                                <th>Address</th>
                                <th>Order Status</th>
                                <th>Order Items</th>
                                <th style="text-align: center;">Price</th>
                                <th style="text-align: center;">Item QTY</th>
                                <th style="text-align: center;">Item Price</th>
                                <th style="text-align: center;">Total Bill</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($onlineOrders as $order)
                                <tr class="table-row">
                                    <td id="table-number">
                                        {{ $order->order_number }}
                                    </td>
                                    <td>{{ $order->customers->name }}</td>
                                    <td>{{ $order->customers->phone_number }}</td>
                                    <td class="truncate-text" title="{{ $order->order_address }}">
                                        {{ $order->order_address }}
                                    </td>
                                    <td>
                                        @php
                                            $color = '';
                                            $statusText = '';
                                            switch ($order->status) {
                                                case 1:
                                                    $statusText = 'Completed';
                                                    $color = '#000'; // black
                                                    break;

                                                case 2:
                                                    $statusText = 'Pending';
                                                    $color = '#FFC107'; // Yellow
                                                    break;

                                                case 3:
                                                    $statusText = 'Cancel';
                                                    $color = '#F44336'; // Red
                                                    break;

                                                case 4:
                                                    $statusText = 'Send to Chef';
                                                    $color = '#2196F3'; // Blue
                                                    break;

                                                case 5:
                                                    $statusText = 'Order Ready';
                                                    $color = '#4CAF50'; // green
                                                    break;

                                                default:
                                                    $statusText = 'Unknown';
                                                    $color = '#9E9E9E'; // Gray
                                            }
                                        @endphp
                                        <span style="color: {{ $color }};">
                                            {{ $statusText }}
                                        </span>
                                    </td>

                                    <td>
                                        @foreach ($order->items as $item)
                                            <div>
                                                @if ($item->addons)
                                                    {{ $item->product_name }} with {{ $item->addons }}
                                                @else
                                                    {{ $item->product_name }}
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                    <td style="text-align: center;">
                                        @foreach ($order->items as $item)
                                            <div>{{ $item->product_price }}</div>
                                        @endforeach
                                    </td>
                                    <td style="text-align: center;">
                                        @foreach ($order->items as $item)
                                            <div>{{ $item->product_quantity }}</div>
                                        @endforeach
                                    </td>
                                    <td style="text-align: center;">
                                        @foreach ($order->items as $item)
                                            <div>{{ $item->total_price }}</div>
                                        @endforeach
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $order->total_bill }}
                                    </td>
                                    <td>
                                        @if ($order->status == 1 || $order->status == 3)
                                            <!-- Disabled for status 1 and 3 -->
                                            <a title="Confirm order">
                                                <i style="background-color:#1ac371; cursor: default;"
                                                    class='bx bx-check'></i>
                                            </a>
                                            <a title="Assign to Rider">
                                                <i style="background-color:#1ac371; cursor: default;"
                                                    class='bx bxs-right-arrow-square'></i>
                                            </a>
                                        @elseif ($order->status == 2)
                                            <!-- Default for status 2 -->
                                            <a href="{{ route('confirmOnlineOrder', [$branch_id, $staff_id, $order->id]) }}"
                                                title="Confirm order">
                                                <i class='bx bx-check'></i>
                                            </a>
                                            <a title="Assign to Rider">
                                                <i class='bx bxs-right-arrow-square'></i>
                                            </a>
                                        @elseif ($order->status == 4 || $order->status == 5)
                                            <!-- Disabled first icon and default for second -->
                                            <a title="Confirm order">
                                                <i style="background-color:#4d4d4d; cursor: default;"
                                                    class='bx bx-check'></i>
                                            </a>
                                            <a href="{{ route('confirmOnlineOrder', [$branch_id, $staff_id, $order->id]) }}"
                                                title="Assign to Rider">
                                                <i class='bx bxs-right-arrow-square'></i>
                                            </a>
                                        @else
                                            <!-- Default for any other status -->
                                            <a href="{{ route('confirmOnlineOrder', [$branch_id, $staff_id, $order->id]) }}"
                                                title="Confirm order">
                                                <i class='bx bx-check'></i>
                                            </a>
                                            <a href="{{ route('assignToRider', [$staff_id, $order->id]) }}"
                                                title="Assign to Rider">
                                                <i class='bx bxs-right-arrow-square'></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="closeBtn">
                <button onclick="hideOnlineOrders()">Close</button>
            </div>
        </div>

        {{--  
        |---------------------------------------------------------------|
        |========================== All Orders =========================|
        |---------------------------------------------------------------|
        --}}

        <div id="allOrdersOverlay"></div>
        <div id="allOrdersDiv">
            <h3>All Orders</h3>
            <div id="dineInOrdersTable">
                <div class="searchBarDiv">
                    <input type="text" id="allOrderSearchBar" name="search" placeholder="Search by Order Number..."
                        style="background-image: url('{{ asset('Images/search.png') }}');">
                </div>
                <div id="tableDiv">
                    <table>
                        <thead>
                            <tr>
                                <th>Order id</th>
                                <th>Order Number</th>
                                <th>Salesman</th>
                                <th>Total Bill</th>
                                <th>Order Type</th>
                                <th>Order Status</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr class="table-row">
                                    <td>{{ $order->id }}</td>
                                    <td id="table-number">{{ $order->order_number }}</td>
                                    <td>{{ $order->salesman->name }}</td>
                                    <td>{{ $order->total_bill }}</td>
                                    <td>{{ $order->ordertype }}</td>
                                    @if ($order->status == 1)
                                        <td class="status">Completed</td>
                                    @elseif ($order->status == 2)
                                        <td class="status">Pending</td>
                                    @elseif ($order->status == 3)
                                        <td class="status">Canceled</td>
                                    @elseif ($order->status == 4)
                                        <td class="status">Send to Chef</td>
                                    @elseif ($order->status == 5)
                                        <td class="status">Ready for delivery</td>
                                    @endif
                                    <td id="actionstd">
                                        <a id="view" href="#"
                                            onclick="showOrderItems({{ json_encode($order) }})">View</a>
                                        @if ($order->status == 1)
                                            <a id="cancel-order"
                                                style="background-color:#4d4d4d; cursor: default;">Cancel</a>
                                        @elseif($order->status == 2)
                                            <a id="cancel-order"
                                                href="{{ route('cancelorder', [$order->id, $staff_id]) }}">Cancel</a>
                                        @elseif($order->status == 3)
                                            <a id="cancel-order"
                                                style="background-color:#4d4d4d;  cursor: default;">Cancel</a>
                                        @elseif($order->status == 4)
                                            <a id="cancel-order"
                                                href="{{ route('cancelorder', [$order->id, $staff_id]) }}">Cancel</a>
                                        @elseif($order->status == 5)
                                            <a id="cancel-order"
                                                href="{{ route('cancelorder', [$order->id, $staff_id]) }}">Cancel</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="closeBtn">
                <button onclick="hideAllOrders()">Close</button>
            </div>
        </div>

        <div id="orderItemsOverlay" style="display:none;"></div>
        <div id="orderItems" style="display:none;">
            <div class="table">
                <table id="itemtable" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody id="orderItemsBody">
                        <!-- Order items will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>

            <div class="btns">
                <a style="text-decoration:none;" href="#" id="printReciptLink"><button id="printbtn"
                        type="button">Print</button></a>
                <button id="closebtn" type="button" onclick="closeOrderItems()">Close</button>
            </div>
        </div>

        <div id="custom-popup" style="display: none;">
            <p id="popup-message"></p>
            <button id="enable-sound" onclick="playAudio()" style="display: none;">Enable Sound</button>
            <audio id="notification-sound" src="{{ asset('Sound/notification.mp3') }}" allow="autoplay"></audio>
        </div>

        <div class="msg">
            <div class="msg1">
                <div class="msg_img">
                    <img rel="preload" src="{{ asset('Images/OnlineOrdering/addedcart.png') }}" alt="" loading="lazy" as="image">
                </div>
                <div class="msg_text">
                    <span>Item Added to Cart</span>
                </div>
            </div>
        </div>

        <style>
            #custom-popup {
                position: fixed;
                left: -300px;
                bottom: 20px;
                width: 350px;
                max-width: 600px;
                background-color: #9c7301;
                color: #ffffff;
                padding: 15px;
                border-radius: 5px;
                box-shadow: 0 0 10px #535353;
                font-size: 16px;
                font-weight: 600;
                transition: right 0.5s ease-in-out;
                z-index: 1100;
            }

            #custom-popup.show {
                left: 20px;
            }
        </style>

    </main>
    @push('scripts')
        <script src="{{ asset('JavaScript/salesman.js') }}"></script>
    @endpush

    <script>
        let interval = 10000;
        let remainingTime = interval;
        let previousData = [];
        async function fetchData() {
            try {
                // const response = await fetch("http://192.168.1.108:7000/getNotificationData");
                // const response = await fetch("http://192.168.1.108:8000/getNotificationData");
                // const response = await fetch("http://192.168.100.7:8000/getNotificationData");
                const response = await fetch("http://192.168.100.7:7000/getNotificationData");
                const data = await response.json();

                if (JSON.stringify(data.collection) !== JSON.stringify(previousData)) {
                    document.getElementById('enable-sound').click();

                    previousData = data.collection;
                    data.collection.forEach((message) => {
                        showPopup(message.message, message.id);
                    });
                }
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        async function deleteNotification(messageId) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const response = await fetch(`http://192.168.100.7:7000/deleteOnlineNotification/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    },
                });

                if (response.ok) {
                    console.log(`Notification ${messageId} deleted successfully.`);
                } else {
                    console.error('Failed to delete notification:', response.statusText);
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        }

        function showPopup(message, messageId) {
            const popup = document.getElementById('custom-popup');
            const popupMessage = document.getElementById('popup-message');
            const notificationSound = document.getElementById('notification-sound');

            popup.style.display = "block";
            popupMessage.textContent = message;
            popup.classList.add('show');

            setTimeout(async () => {
                popup.style.display = "none";
                popup.classList.remove('show');
                await deleteNotification(messageId);
            }, 3000);
        }

        function updateCountdown() {
            const seconds = Math.ceil(remainingTime / 1000);
        }

        function playAudio() {
            const notificationSound = document.getElementById('notification-sound');
            notificationSound.play();
        }

        function startCountdown() {
            updateCountdown();
            setInterval(() => {
                remainingTime -= 1000;
                if (remainingTime <= 0) {
                    fetchData();
                    remainingTime = interval;
                }
                updateCountdown();
            }, 1000);
        }

        startCountdown();

        // document.getElementById('serving_tables').addEventListener('change', function() {
        //     const selectedTable = this.value;
        //     if (selectedTable != 0) {
        //         window.location.href = this.options[this.selectedIndex].getAttribute('data-url');
        //     }
        // });


        // document.getElementById('deals_seperate_section_imgDiv').addEventListener('wheel', function(event) {
        //     event.preventDefault();
        //     this.scrollLeft += event.deltaY;
        // });

        function selectCategory(route, element) {
            let categoryLinks = document.getElementsByClassName('category_link');
            for (let i = 0; i < categoryLinks.length; i++) {
                categoryLinks[i].classList.remove('selected');
            }
            let link = element.getElementsByTagName('a')[0];
            link.classList.add('selected');
            document.cookie = "selected_category=" + link.textContent.trim() + "; path=/";
            window.location = route;
        }

        function getCookie(name) {
            let value = "; " + document.cookie;
            let parts = value.split("; " + name + "=");
            if (parts.length === 2) return parts.pop().split(";").shift();
        }

        window.onload = function() {
            let selectedCategory = getCookie("selected_category");
            let categoryLinks = document.getElementsByClassName('category_link');
            if (!selectedCategory) {
                categoryLinks[0].classList.add('selected');
                return;
            }
            for (let i = 0; i < categoryLinks.length; i++) {
                if (categoryLinks[i].textContent.trim() === selectedCategory) {
                    categoryLinks[i].classList.add('selected');
                    break;
                }
            }
        };


        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search_bar');
            const productDivs = document.querySelectorAll('#imageBox');

            searchInput.addEventListener('input', function() {
                const filter = searchInput.value.toLowerCase();
                productDivs.forEach(function(div) {
                    const productName = div.querySelector('.product_name').textContent
                        .toLowerCase();
                    if (productName.includes(filter)) {
                        div.style.display = 'flex';
                    } else {
                        div.style.display = 'none';
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            let search_input = document.getElementById('searchBar');
            let table_rows = document.querySelectorAll('.table-row');

            search_input.addEventListener('input', () => {
                let filter_table_number = search_input.value.toLowerCase();
                table_rows.forEach(function(table_row) {
                    let table_number = table_row.querySelector('#table-number').textContent
                        .toLowerCase();
                    if (table_number.includes(filter_table_number)) {
                        table_row.style.display = 'table-row';
                    } else {
                        table_row.style.display = 'none';
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            let search_input = document.getElementById('onlineOrderSearchBar');
            let table_rows = document.querySelectorAll('.table-row');

            search_input.addEventListener('input', () => {
                let filter_table_number = search_input.value.toLowerCase();
                table_rows.forEach(function(table_row) {
                    let table_number = table_row.querySelector('#table-number').textContent
                        .toLowerCase();
                    if (table_number.includes(filter_table_number)) {
                        table_row.style.display = 'table-row';
                    } else {
                        table_row.style.display = 'none';
                    }
                });
            });
        });

        function showDineInOrders() {
            document.getElementById('dineInOrdersOverlay').style.display = 'block';
            document.getElementById('dineInOrdersDiv').style.display = 'flex';
        }

        function hideDineInOrders() {
            document.getElementById('dineInOrdersOverlay').style.display = 'none';
            document.getElementById('dineInOrdersDiv').style.display = 'none';
        }

        function showOnlineOrders() {
            document.getElementById('onlineOrdersOverlay').style.display = 'block';
            document.getElementById('onlineOrdersDiv').style.display = 'flex';
        }

        function hideOnlineOrders() {
            document.getElementById('onlineOrdersOverlay').style.display = 'none';
            document.getElementById('onlineOrdersDiv').style.display = 'none';
        }

        function showAllOrders() {
            document.getElementById('allOrdersOverlay').style.display = 'block';
            document.getElementById('allOrdersDiv').style.display = 'flex';
        }

        function hideAllOrders() {
            document.getElementById('allOrdersOverlay').style.display = 'none';
            document.getElementById('allOrdersDiv').style.display = 'none';
        }

        document.body.addEventListener('dblclick', () => {
            hideDineInOrders();
            hideOnlineOrders();
        });

        function showOrderItems(order) {
            let orderItemsBody = document.getElementById('orderItemsBody');
            orderItemsBody.innerHTML = '';
            order.items.forEach(item => {
                let row = `
            <tr>
                <td>${order.order_number}</td>
                <td>${item.product_name}</td>
                <td>${item.product_quantity}</td>
                <td>${item.total_price}</td>
            </tr>`;

                orderItemsBody.insertAdjacentHTML('beforeend', row);
            });

            let route = `{{ route('printrecipt', ':orderId') }}`;
            route = route.replace(':orderId', order.id);
            document.getElementById('printReciptLink').setAttribute('href', route);

            document.getElementById('orderItemsOverlay').style.display = 'block';
            document.getElementById('orderItems').style.display = 'flex';
            document.getElementById('allOrdersDiv').style.display = 'none';
        }

        function closeOrderItems() {
            document.getElementById('orderItemsOverlay').style.display = 'none';
            document.getElementById('orderItems').style.display = 'none';
            document.getElementById('allOrdersDiv').style.display = 'flex';
        }
    </script>
@endsection
