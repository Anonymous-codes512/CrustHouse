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

@push('scripts')
    <script src="{{ asset('JavaScript/Salesman1.js') }}"></script>
@endpush

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
            $allProducts = $AllProducts;
            $staff_id = $staff_id;
            $branch_id = $branch_id;
            $cartProducts = $cartProducts;
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
                    @foreach ($Products as $product)
                        @if (!in_array($product->productName, $displayedProductNames))
                            @php
                                $displayedProductNames[] = $product->productName;
                            @endphp

                            <div id="imageBox" class="imgbox"
                                onclick="showAddToCart({{ json_encode($product) }} ,null, {{ json_encode($allProducts) }})">
                                <img src="{{ asset('Images/ProductImages/' . $product->productImage) }}" alt="Product">
                                <p class="product_name">{{ $product->productName }}</p>
                                {{-- <p class="product_price">From Rs. {{ $product->productPrice }}</p> --}}
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
                                    onclick="showAddToCart({{ json_encode($deal) }}, {{ json_encode($Deals) }}, {{ json_encode($allProducts) }})">
                                    <img src="{{ asset('Images/DealImages/' . $deal->deal->dealImage) }}" alt="Product">
                                    <p class="product_name">{{ $deal->deal->dealTitle }}</p>
                                    {{-- <p class="product_price">{{ $deal->deal->dealDiscountedPrice }}</p> --}}
                                </div>
                            @endif
                        @endif
                    @endforeach
                @endif

            </div>
            @if ($Products !== null)
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
                                        onclick="showAddToCart({{ json_encode($deal) }}, {{ json_encode($Deals) }}, {{ json_encode($allProducts) }})">
                                        <img src="{{ asset('Images/DealImages/' . $deal->deal->dealImage) }}"
                                            alt="Product">
                                        <p class="product_name">{{ $deal->deal->dealTitle }}</p>
                                        {{-- <p class="product_price">{{ $deal->deal->dealDiscountedPrice }}</p> --}}
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div id="receipt">
            <h4 id="heading">Receipt</h4>
            <div id="cart">

                <input type="hidden" name="salesman_id" id="salesman_id" value={{ $staff_id }}>
                <div id="selectedProducts" name="products">
                    @foreach ($cartProducts as $Value)
                        @php
                            $priceString = $Value->totalPrice;
                            preg_match('/\d+(\.\d+)?/', $priceString, $matches);
                            $numericPart = $matches[0];
                            $totalbill = $totalbill + $numericPart;
                        @endphp
                        <div id="productdiv">
                            <div class="product_name">
                                @if ($Value->productAddon && strpos($Value->productName, $Value->productAddon) === false)
                                    <p style="margin: 0; max-width:80%;" id="product-name">
                                        {{ $Value->productName . ' with ' . $Value->productAddon }}</p>
                                    <span id="product_price{{ $Value->id }}">{{ $Value->totalPrice }}</span>
                                @else
                                    <p style="margin: 0; max-width:80%;" id="product-name">{{ $Value->productName }}</p>
                                    <span id="product_price{{ $Value->id }}">{{ $Value->totalPrice }}</span>
                                @endif
                            </div>
                            <div class="product-controls">
                                <button
                                    onclick="window.location='{{ route('removeOneProduct', [$Value->id, $Value->salesman_id, $branch_id]) }}'"
                                    id="remove-product"><i class='bx bxs-trash'></i></button>
                                <div class="quantity-control">
                                    <a class="quantity-decrease-btn"
                                        href="{{ route('decreaseQuantity', [$Value->id, $Value->salesman_id, $branch_id]) }}">
                                        <i class='bx bxs-checkbox-minus'></i>
                                    </a>
                                    <input class="quantity-display-field" type="text"
                                        name="prodQuantity{{ $Value->id }}" id="product_quantity{{ $Value->id }}"
                                        value="{{ $Value->productQuantity }}" readonly>
                                    <a class="quantity-increase-btn"
                                        href="{{ route('increaseQuantity', [$Value->id, $Value->salesman_id, $branch_id]) }}">
                                        <i class='bx bxs-plus-square'></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @php
                    $totalTaxes = 0.0;
                    foreach ($taxes as $tax) {
                        $totalTaxes += $totalbill * ((float) $tax->tax_value / 100);
                    }
                    $totalbill += $totalTaxes;
                    $totalbill = (int) $totalbill;
                @endphp

                <form action="{{ route('placeOrder', $staff_id) }}" method="post" enctype="multipart/form-data"
                    onsubmit="return validateDiscount()">
                    @csrf
                    <div class="payment-div">
                        <div class="cash-fields">
                            <div class="paymentfields">
                                <label for="totalbill">Total Amount</label>
                                <input type="text" name="totalbill" id="totalbill" value="Rs {{ $totalbill }}"
                                    readonly>
                                <input type="hidden" name="totaltaxes" id="totaltaxes" value="{{ $totalTaxes }}"
                                    readonly>
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
                                    id="paymentMethod">
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
                                <script>
                                    function toggleDiscount() {
                                        togglebtn = document.getElementById('discountEnableDisable').checked;
                                        if (togglebtn == true) {
                                            document.getElementById('toggle-text').textContent = "Disable Discount";
                                            document.getElementById('toggle-text').style.width = "250px";
                                            document.getElementById('discount').disabled = false;
                                            document.getElementById('discount_reason').disabled = false;
                                            document.getElementById('discountType').disabled = false;

                                            document.getElementById('discount-Type-div').style.display = "flex";
                                            document.getElementById('discountFieldDiv').style.display = "flex";
                                            document.getElementById('discountReasonDiv').style.display = "flex";
                                            document.getElementById('discountReasonDiv').required = true;
                                            document.getElementById('discountTypeDiv').style.display = "flex";
                                        } else {
                                            document.getElementById('toggle-text').textContent = "Enable Discount";
                                            document.getElementById('toggle-text').style.width = "200px";
                                            document.getElementById('discount').disabled = true;
                                            document.getElementById('discount_reason').disabled = true;
                                            document.getElementById('discountType').disabled = true;

                                            document.getElementById('discount-Type-div').style.display = "none";
                                            document.getElementById('discountFieldDiv').style.display = "none";
                                            document.getElementById('discountReasonDiv').style.display = "none";
                                            document.getElementById('discountTypeDiv').style.display = "none";
                                        }
                                    }
                                </script>

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
                                                onclick="updateTotalONSwitch({{ json_encode($totalbill) }}, {{ json_encode($maximum_discount_percentage_value) }})">>
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
                                        oninput="updateTotalONInput({{ json_encode($totalbill) }},{{ json_encode($maximum_discount_percentage_value) }})">
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
                                <label for="tables_list">Select Table Number</label>
                                <select name="table_number" id="tables_list">
                                    @foreach ($dineInTables as $table)
                                        <option value="{{ $table->table_number }}">{{ $table->table_number }}</option>
                                    @endforeach
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

        <div id="overlay"></div>
        <form id="addToCart" action="{{ route('saveToCart') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="product_id" name="product_id">
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">
            <input type="hidden" name="salesman_id" id="salesman_id" value={{ $staff_id }}>

            <p id="headTitle" class="head1">Customize Item</p>
            <input id="prodName" name="productname" style="border: none; display:none;" readonly>
            <div style="display: none;margin: 10px 0.5vw; border-bottom:1px solid #393939" id="productCustomDiv">
                <span id="prodPrice" style="width: 50%;"> </span>
                <input name="productprice" style="border: none; text-align:right; font-size:0.9vw;" id="price"
                    readonly>
            </div>
            {{-- <p class="head1">Please Select</p> --}}

            <label id="prodVariationLabel" for="prodVariation">Product Variation</label>
            <select tabindex="0" name="prodVariation" id="prodVariation"></select>

            <label id="addOnsLabel" for="addons">Add Ons</label>
            <select name="addOn" id="addons"></select>

            <label id="drinkFlavourLabel" for="drinkFlavour">Drink Flavour</label>
            <select name="drinkFlavour" id="drinkFlavour"></select>

            <div id="quantity">
                <p>Quantity</p>
                <i onclick="decrease()" class='bx bxs-checkbox-minus'></i>
                <input type="number" name="prodQuantity" id="prodQuantity" value="1" min="0">
                <i onclick="increase()" class='bx bxs-plus-square'></i>
            </div>

            <p id="bottom">Total Price <input name="totalprice"
                    style="background-color:transparent; border: none; text-align:right;" id="totalprice" readonly></p>

            <div id="buttons">
                <button type="button" onclick="closeAddToCart()">Close</button>
                <input id="addbtn" type="submit" value="Add">
            </div>
        </form>

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
                                <th>Order #</th>
                                <th>Order Items</th>
                                <th>Item Price</th>
                                <th>Total Bill</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($orders as $order) --}}
                            <tr class="table-row">
                                <td id="table-number">
                                    1
                                </td>
                                <td>
                                    CH102
                                </td>
                                <td>
                                    <div>Pizza</div>
                                    <div>Calzon</div>
                                    <div>Pasta</div>
                                </td>
                                <td>
                                    <div>Rs. 1200</div>
                                    <div>Rs. 1500</div>
                                    <div>Rs. 1100</div>
                                </td>
                                <td>
                                    Rs. 3800
                                </td>
                                <td>
                                    <a title="Add New Prduct"><i class='bx bxs-cart-add'></i></a>
                                    <a title="Proceed to Payment"><i class='bx bxs-right-arrow-square'></i></a>
                                </td>
                            </tr>
                            {{-- @endforeach --}}
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

    <script>
        let interval = 10000; // 10 seconds
        let remainingTime = interval;
        let previousData = [];

        async function fetchData() {
            try {
                const response = await fetch("http://127.0.0.1:8000/getNotificationData");
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

                const response = await fetch(`http://127.0.0.1:8000/deleteOnlineNotification/${messageId}`, {
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

        document.addEventListener("DOMContentLoaded", function() {
            fetchData();
        });


        document.addEventListener('DOMContentLoaded', function() {
            let toggle = document.getElementById('order_type');
            const falsetext = document.getElementById('false-option').textContent;
            const truetext = document.getElementById('true-option').textContent;
            let orderTypeHidden = document.getElementById('orderTypeHidden');

            function updateHiddenInput() {
                orderTypeHidden.value = toggle.checked ? truetext : falsetext;
                if (orderTypeHidden.value.trim() == "Dine-In") {
                    document.getElementById('TablesList').style.display = 'flex';
                } else {
                    document.getElementById('TablesList').style.display = 'none';
                }
            }

            updateHiddenInput();

            toggle.addEventListener('change', function() {
                updateHiddenInput();
            });
        });

        function validateDiscount() {
            const discountEnabled = document.getElementById('discountEnableDisable').checked;
            const discountReason = document.getElementById('discount_reason').value;

            if (discountEnabled && !discountReason) {
                alert('Please select a reason for the discount.');
                return false;
            }
            return true;
        }
        let discountTypeInput = document.getElementById('discountType');
        let toggleDiscountType = document.getElementById('discounttype');

        function updateTotalONSwitchChange(total, discountLimit, discountType) {
            let discount = parseInt(document.getElementById("discount"));
            let discountAmount = parseInt(discount.value);

            let totalBill = parseInt(total);
            let discountLimitValue = parseInt(discountLimit);
            if (isNaN(discountAmount)) {
                document.getElementById("totalbill").value = `Rs ${totalBill}`;
                return;
            }
            let fixedDiscountAmount = parseInt((discountLimitValue / 100) * total);

            if (discountType == "%" && discountAmount > discountLimitValue) {
                alert(`Discount in Percentage should be less than or equal to ${discountLimitValue}.`);
                discount.value = discountLimitValue;
                discountAmount = discountLimitValue;
            }

            if (discountType == "-" && discountAmount > fixedDiscountAmount) {
                alert(
                    `Discount amount should be less than or equal to ${fixedDiscountAmount} (${discountLimitValue}% of total bill.)`
                );
                discount.value = fixedDiscountAmount;
                discountAmount = fixedDiscountAmount;
            }

            if (discountType == "%") {
                let discountedBill = parseInt(totalBill - ((discountAmount / 100) * totalBill));
                document.getElementById("totalbill").value = `Rs ${discountedBill}`;
            }

            if (discountType == "-") {
                let discountedBill = parseInt(totalBill - discountAmount);
                document.getElementById("totalbill").value = `Rs ${discountedBill}`;
            }
        }

        function updateTotalONSwitch(total, discountLimit) {
            discountTypeInput.value = toggleDiscountType.checked ? '-' : '%';
            document.getElementById("discount").value = '';
            updateTotalONSwitchChange(total, discountLimit, discountTypeInput.value);
        };

        function updateTotalONInput(total, discountLimit) {
            let discount = document.getElementById("discount");
            discount.addEventListener('input', () => {
                let sanitizedValue = discount.value.match(/^\d*(?:\.\d*)?$/);
                if (sanitizedValue) {
                    sanitizedValue = sanitizedValue[0];
                } else {
                    sanitizedValue = '';
                }
                discount.value = sanitizedValue;
            })

            let discountType = document.getElementById("discountType").value;

            let discountAmount = parseInt(discount.value);
            let totalBill = parseInt(total);
            let discountLimitValue = parseInt(discountLimit);
            if (isNaN(discountAmount)) {
                document.getElementById("totalbill").value = `Rs ${totalBill}`;
                return;
            }
            let fixedDiscountAmount = parseInt((discountLimitValue / 100) * total);

            if (discountType == "%" && discountAmount > discountLimitValue) {
                alert(`Discount in Percentage should be less than or equal to ${discountLimitValue}.`);
                discount.value = discountLimitValue;
                discountAmount = discountLimitValue;
            } else if (discountType == "-" && discountAmount > fixedDiscountAmount) {
                alert(
                    `Discount amount should be less than or equal to ${fixedDiscountAmount} (${discountLimitValue}% of total bill.)`
                );
                discount.value = fixedDiscountAmount;
                discountAmount = fixedDiscountAmount;
            }

            if (discountType == "%") {
                let discountedBill = parseInt(totalBill - ((discountAmount / 100) * totalBill));
                document.getElementById("totalbill").value = `Rs ${discountedBill}`;
            } else if (discountType == "-") {
                let discountedBill = parseInt(totalBill - discountAmount);
                document.getElementById("totalbill").value = `Rs ${discountedBill}`;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            let togglePaymentMethod = document.getElementById('paymentmethod');
            const falsetext1 = document.getElementById('false-option0').textContent;
            const truetext1 = document.getElementById('true-option0').textContent;
            let paymentMethodSelect = document.getElementById('paymentMethod');
            let removedCashOption = null;

            function updatePaymentMethod() {
                if (togglePaymentMethod.checked) {
                    paymentMethodSelect.style.display = 'flex';
                    for (let i = paymentMethodSelect.options.length - 1; i >= 0; i--) {
                        if (paymentMethodSelect.options[i].value.toLowerCase() === 'cash') {
                            removedCashOption = paymentMethodSelect.options[i];
                            paymentMethodSelect.remove(i);
                        }
                    }
                    paymentMethodSelect.value = truetext1;
                    if (!paymentMethodSelect.value) {
                        paymentMethodSelect.selectedIndex = 0;
                    }

                } else {
                    paymentMethodSelect.style.display = 'none';
                    paymentMethodSelect.value = falsetext1;
                    if (removedCashOption) {
                        let cashOption = document.createElement('option');
                        cashOption.value = removedCashOption.value;
                        cashOption.textContent = removedCashOption.textContent;
                        paymentMethodSelect.add(cashOption, paymentMethodSelect.options[
                            0]);
                        removedCashOption = null;
                    }
                }
            }
            updatePaymentMethod();

            togglePaymentMethod.addEventListener('change', function() {
                updatePaymentMethod();
            });
        });

        document.getElementById('deals_seperate_section_imgDiv').addEventListener('wheel', function(event) {
            event.preventDefault();
            this.scrollLeft += event.deltaY;
        });

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
            if (parts.length == 2) return parts.pop().split(";").shift();
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

        function validateNumericInput(input) {
            let sanitizedValue = input.value.match(/^\d*(?:\.\d*)?$/);
            if (sanitizedValue) {
                sanitizedValue = sanitizedValue[0];
            } else {
                sanitizedValue = '';
            }
            input.value = sanitizedValue;
            calculateChange(input.value);
        }

        function calculateChange(receivedBill) {
            let totalBillStr = document.getElementById('totalbill').value;
            let totalBill = parseInt(totalBillStr.replace('Rs', '').trim());

            receivedBill = parseInt(receivedBill);

            if (isNaN(receivedBill)) {
                document.getElementById('change').value = '';
            }

            if (isNaN(totalBill)) {
                totalBill = 0;
            }
            let change = receivedBill - totalBill;
            document.getElementById('proceed').disabled = change < 0;
            document.getElementById('change').value = change.toFixed(2);
        }


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
