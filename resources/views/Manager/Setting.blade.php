@extends('Components.Manager')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Manager - Settings';
    });
</script>

@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Manager/setting.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush

@section('main')

    @php
        $id = session('id');
        $branch_id = session('branch_id');
    @endphp

    <main id="settings">
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

        <h2 id="heading">Settings</h2>

        <div id="options">


            {{--  
            |---------------------------------------------------------------|
            |====================== Discount Overlay =======================|
            |---------------------------------------------------------------|
            --}}

            <button class="opt-buttons" onclick="discountOverlay()">Discount</button>
            <div id="discountOverlay"></div>
            <div id="discount">
                <h3>Add New Discount</h3>
                <hr>
                @if ($discounts)
                    <div class="container-fields">
                        @foreach ($discounts as $discount)
                            <form id="texFields" action="{{ route('updateDiscount') }}" enctype="multipart/form-data"
                                method="POST" onsubmit="show_Loader()">
                                @csrf
                                <input type="hidden" name="discount_id" value="{{ $discount->id }}">

                                <div style="width: 50%;" class="inputdivs">
                                    <label for="discountReason">update Discount Reason</label>
                                    <input type="text" id="discountReason" name="discount_reason"
                                        placeholder="Family , General,etc..." value="{{ $discount->discount_reason }}"
                                        required>
                                </div>

                                <div id="option_button">
                                    <button type="submit">
                                        <i class='bx bxs-edit-alt'></i>
                                    </button>
                                    <button class="deleteTax" type="button"
                                        onclick="showConfirmDelete('{{ route('deleteDiscount', $discount->id) }}')">
                                        <i class='bx bxs-trash-alt'></i>
                                    </button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                    <form action="{{ route('createDiscount') }}" method="POST" onsubmit="show_Loader()"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                        <hr>
                        <div id="texFields">
                            <div style="width: 50%;" class="inputdivs">
                                <label for="discountReason">Add Discount Reason</label>
                                <input type="text" id="discountReason" name="discount_reason"
                                    placeholder="Family , General,etc..." required>
                            </div>
                        </div>

                        <div class="forms-btns">
                            <button type="button" id="cancel" onclick="closeDiscountOverlay()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @else
                    <form method="POST" onsubmit="show_Loader()" action="{{ route('createDiscount') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                        <hr>
                        <div id="texFields">
                            <div style="width: 50%;" class="inputdivs">
                                <label for="discountReason">Add Discount Reason</label>
                                <input type="text" id="discountReason" name="discount_reason"
                                    placeholder="Family , General,etc..." required>
                            </div>
                        </div>

                        <div class="forms-btns">
                            <button type="button" id="cancel" onclick="closeDiscountOverlay()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @endif
            </div>

            {{--  
            |---------------------------------------------------------------|
            |==================== Discount Type Overlay ====================|
            |---------------------------------------------------------------|
            --}}
            <button class="opt-buttons" onclick="showDiscountTypeOverlay()">Discount Type Settings</button>
            <div id="discountTypeOverlay"></div>
            <div id="discountType">
                <h3>Add Discount Type</h3>
                <hr>
                @if ($discountTypes)
                    <div class="container-fields">
                        @foreach ($discountTypes as $type)
                            <form id="texFields" action="{{ route('updateDiscountTypes') }}" enctype="multipart/form-data"
                                method="POST" onsubmit="show_Loader()">
                                @csrf
                                <input type="hidden" name="discount_type_id" value="{{ $type->id }}">
                                <div class="inputdivs">
                                    <label for="discountTypes">Discount Types</label>
                                    <input type="text" id="discountTypes" name="discount_type"
                                        value="{{ $type->discount_type }}" placeholder="fixed, percentage..." required>
                                </div>

                                <div id="option_button">
                                    <button type="submit">
                                        <i class='bx bxs-edit-alt'></i>
                                    </button>
                                    <button class="deleteTax" type="button"
                                        onclick="showConfirmDelete('{{ route('deleteDiscountTypes', $type->id) }}')">
                                        <i class='bx bxs-trash-alt'></i>
                                    </button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                    <form action="{{ route('createDiscountTypes') }}" method="POST" onsubmit="show_Loader()"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                        <hr>
                        <div style="width: 50%; margin:auto" class="inputdivs">
                            <label for="discountTypes">Discount Type</label>
                            <input type="text" id="discountTypes" name="discount_type"
                                placeholder="fixed, percentage..." required>
                        </div>

                        <div class="forms-btns">
                            <button id="cancel" type="button" onclick="closeDiscountTypeOverlay()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @else
                    <form action="{{ route('createDiscountTypes') }}" method="POST" onsubmit="show_Loader()"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                        <hr>
                        <div style="width: 50%; margin:auto;" class="inputdivs">
                            <label for="discountTypes">Discount Type</label>
                            <input type="text" id="discountTypes" name="discount_type"
                                placeholder="fixed, percentage..." required>
                        </div>

                        <div class="forms-btns">
                            <button id="cancel" type="button" onclick="closeDiscountTypeOverlay()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @endif
            </div>

            {{--  
            |---------------------------------------------------------------|
            |==================== Discount Value Overlay ===================|
            |---------------------------------------------------------------|
            --}}

            <button class="opt-buttons" onclick="showDiscountValueOverlay()">Discount Value Settings</button>
            <div id="discountValueOverlay"></div>
            <div id="discountValue">
                <h3>Discount Value Settings</h3>
                <hr>
                @if ($receipt->max_discount_percentage != 20.0)
                    <div class="container-fields">
                        @php
                            $discount_percentage = $receipt->max_discount_percentage;
                        @endphp
                        <form action="{{ route('updateDiscountValue') }}" enctype="multipart/form-data" method="POST"
                            onsubmit="show_Loader()">
                            @csrf
                            <input type="hidden" name="discount_value_id" value="{{ $receipt->id }}">

                            <div style="width: 50%; margin:auto;" class="inputdivs">
                                <label for="discount_percentage_value">Update Discount Percentage Value</label>
                                <input type="number" name="max_discount_percentage" id="discount_percentage_value"
                                    value="{{ $discount_percentage }}" min="0" step="any" required>
                            </div>

                            <div class="forms-btns">
                                <button type="button" id="cancel"
                                    onclick="closeDiscountValueOverlay()">Cancel</button>
                                <button style="height: 3.5vw;" class="add" type="submit">
                                    Update
                                </button>
                                <button style="height: 3.5vw;" class="deleteTax" type="button"
                                    onclick="showConfirmDelete('{{ route('deleteDiscountValue', $receipt->id) }}')">
                                    Delete
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <form method="POST" onsubmit="show_Loader()" action="{{ route('createDiscountValue') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">

                        <div style="width: 50%; margin:auto; justify-content:center;" class="inputdivs">
                            <label for="discount_percentage_values">Add Discount Percentage Value</label>
                            <input type="number" name="max_discount_percentage" id="discount_percentage_values"
                                min="0" step="any" required>
                        </div>

                        <div class="forms-btns">
                            <button type="button" id="cancel" onclick="closeDiscountValueOverlay()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @endif
            </div>

            {{--  
            |---------------------------------------------------------------|
            |======================== Tex Overlay ==========================|
            |---------------------------------------------------------------|
            --}}

            <button class="opt-buttons" onclick="texOverlay()">Taxes</button>
            <div id="texOverlay"></div>
            <div id="newTax">
                <h3>Add New Tex</h3>
                <hr>
                @if ($taxes)
                    <div class="container-fields">
                        @foreach ($taxes as $tax)
                            <form id="texFields" action="{{ route('updateTax') }}" enctype="multipart/form-data"
                                method="POST" onsubmit="show_Loader()">
                                @csrf
                                <input type="hidden" name="tax_id" value="{{ $tax->id }}">
                                <div class="inputdivs">
                                    <label for="taxName">Tax Name</label>
                                    <input type="text" id="taxName" name="tax_name" value="{{ $tax->tax_name }}"
                                        placeholder="GST, Sales Tax..." required>
                                </div>

                                <div class="inputdivs">
                                    <label for="taxValue">Tax Value</label>
                                    <input type="number" id="taxValue" name="tax_value" value="{{ $tax->tax_value }}"
                                        placeholder="2.5%" min='0' step="0.01" required>
                                </div>
                                <div id="option_button">
                                    <button type="submit">
                                        <i class='bx bxs-edit-alt'></i>
                                    </button>
                                    <button class="deleteTax" type="button"
                                        onclick="showConfirmDelete('{{ route('deleteTax', $tax->id) }}')">
                                        <i class='bx bxs-trash-alt'></i>
                                    </button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                    <form action="{{ route('createTax') }}" method="POST" onsubmit="show_Loader()"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                        <hr>
                        <div id="texFields">
                            <div class="inputdivs">
                                <label for="taxName">Tax Name</label>
                                <input type="text" id="taxName" name="tax_name" placeholder="GST, Sales Tax..."
                                    required>
                            </div>

                            <div class="inputdivs">
                                <label for="taxValue">Tax Value</label>
                                <input type="number" id="taxValue" name="tax_value" placeholder="2.5%" min='0'
                                    step="0.01" required>
                            </div>
                        </div>

                        <div class="forms-btns">
                            <button id="cancel" onclick="closeTax()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @else
                    <form action="{{ route('createTax') }}" method="POST" onsubmit="show_Loader()"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                        <hr>
                        <div id="texFields">
                            <div class="inputdivs">
                                <label for="taxName">Tax Name</label>
                                <input type="text" id="taxName" name="tax_name" placeholder="GST, Sales Tax..."
                                    required>
                            </div>

                            <div class="inputdivs">
                                <label for="taxValue">Tax Value</label>
                                <input type="number" id="taxValue" name="tax_value" placeholder="2.5%" min='0'
                                    step="0.01" required>
                            </div>
                        </div>

                        <div class="forms-btns">
                            <button id="cancel" onclick="closeTax()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @endif
            </div>

            {{--  
            |---------------------------------------------------------------|
            |================== Receipt Settings Overlay ===================|
            |---------------------------------------------------------------|
            --}}

            <button class="opt-buttons" onclick="receiptOverlay()">Receipt Settings</button>
            <div id="receiptOverlay"></div>
            <div id="receipt">
                <h3>Receipt Settings</h3>
                <hr>
                @if (
                    $receipt->receipt_message != null ||
                        $receipt->branch_web_address != null ||
                        $receipt->feedback != null ||
                        $receipt->receipt_tagline != null)
                    <div class="container-fields">
                        @php
                            $message = $receipt->receipt_message;
                            $branch_web_address = $receipt->branch_web_address;
                            $feedback = $receipt->feedback;
                            $receipt_tagline = $receipt->receipt_tagline;
                        @endphp
                        <form action="{{ route('updateReceipt') }}" enctype="multipart/form-data" method="POST"
                            onsubmit="show_Loader()">
                            @csrf
                            <input type="hidden" name="receipt_id" value="{{ $receipt->id }}">

                            <div style="width: 50%; margin:auto;" class="inputdivs">
                                <label for="receipt_message">Update Receipt Message</label>
                                <input name="receipt_message" id="receipt_message" required value="{{ $message }}">
                            </div>
                            <div style="width: 50%; margin:auto;" class="inputdivs">
                                <label for="branch_web_address">Update Branch Web Address</label>
                                <input type="text" name="branch_web_address" id="branch_web_address"
                                    value="{{ $branch_web_address }}">
                            </div>
                            <div style="width: 50%; margin:auto;" class="inputdivs">
                                <label for="feedback">Update Feedback Message</label>
                                <input type="text" name="feedback" id="feedback" value="{{ $feedback }}">
                            </div>
                            <div style="width: 50%; margin:auto;" class="inputdivs">
                                <label for="receipt_tagline">Update Receipt Tag Line</label>
                                <input type="text" name="receipt_tagline" id="receipt_tagline"
                                    value="{{ $receipt_tagline }}">
                            </div>

                            <div class="forms-btns">
                                <button style="height: 40px;" type="button" id="cancel"
                                    onclick="closeReceiptOverlay()">Cancel</button>
                                <button style="height: 40px;" class="add" type="submit">
                                    Update
                                </button>
                                <button style="height: 40px;" class="deleteTax" type="button"
                                    onclick="showConfirmDelete('{{ route('deleteReceipt', $receipt->id) }}')">
                                    Delete
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <form method="POST" onsubmit="show_Loader()" action="{{ route('createReceipt') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">

                        <div style="width: 50%; margin:auto; justify-content:center;" class="inputdivs">
                            <label for="new_receipt_message">Add Receipt Message</label>
                            <textarea name="receipt_message" id="new_receipt_message" style="resize: none;"
                                placeholder="Add Message to Receipt"></textarea>
                        </div>

                        <div style="width: 50%; margin:auto; justify-content:center;" class="inputdivs">
                            <label for="branch_web_address">Add Branch Web Address</label>
                            <input type="text" name="branch_web_address" id="branch_web_address">
                        </div>

                        <div style="width: 50%; margin:auto; justify-content:center;" class="inputdivs">
                            <label for="feedback">Add Feedback Message</label>
                            <input type="text" name="feedback" id="feedback">
                        </div>

                        <div style="width: 50%; margin:auto; justify-content:center;" class="inputdivs">
                            <label for="receipt_tagline">Add Receipt Tag Line</label>
                            <input type="text" name="receipt_tagline" id="receipt_tagline">
                        </div>

                        <div class="forms-btns">
                            <button type="button" id="cancel" onclick="closeReceiptOverlay()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @endif
            </div>
            {{--  
            |---------------------------------------------------------------|
            |=================== Payment Methods Overlay ===================|
            |---------------------------------------------------------------|
            --}}
            <button class="opt-buttons" onclick="paymentMethodOverlay()">Payment Methods Settings</button>
            <div id="paymentMethodOverlay"></div>
            <div id="paymentMethod">
                <h3>Add New Payment Method</h3>
                <hr>
                @if ($paymentMethods)
                    <div class="container-fields">
                        @foreach ($paymentMethods as $method)
                            <form id="texFields" action="{{ route('updatePaymentMethod') }}"
                                enctype="multipart/form-data" method="POST" onsubmit="show_Loader()">
                                @csrf
                                <input type="hidden" name="payment_method_id" value="{{ $method->id }}">
                                <div class="inputdivs">
                                    <label for="paymentMethods">Payment Method</label>
                                    <input type="text" id="paymentMethods" name="payment_method"
                                        value="{{ $method->payment_method }}" placeholder="cash, jazzcash,..." required>
                                </div>

                                <div id="option_button">
                                    <button type="submit">
                                        <i class='bx bxs-edit-alt'></i>
                                    </button>
                                    <button class="deleteTax" type="button"
                                        onclick="showConfirmDelete('{{ route('deletePaymentMethod', $method->id) }}')">
                                        <i class='bx bxs-trash-alt'></i>
                                    </button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                    <form action="{{ route('createPaymentMethod') }}" method="POST" onsubmit="show_Loader()"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                        <hr>
                        <div style="width: 50%; margin:auto" class="inputdivs">
                            <label for="paymentMethods">Payment Method</label>
                            <input type="text" id="paymentMethods" name="payment_method"
                                placeholder="cash , jazzcash ,etc..." required>
                        </div>

                        <div class="forms-btns">
                            <button id="cancel" onclick="closePaymentMethodOverlay()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @else
                    <form action="{{ route('createPaymentMethod') }}" method="POST" onsubmit="show_Loader()"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                        <hr>
                        <div style="width: 50%; margin:auto;" class="inputdivs">
                            <label for="paymentMethods">Payment Method</label>
                            <input type="text" id="paymentMethods" name="payment_method"
                                placeholder="cash , jazzcash ,etc..." required>
                        </div>

                        <div class="forms-btns">
                            <button id="cancel" onclick="closePaymentMethodOverlay()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @endif
            </div>

            {{--  
            |---------------------------------------------------------------|
            |====================  Order Type Overlay ======================|
            |---------------------------------------------------------------|
            --}}
            <button class="opt-buttons" onclick="showOrderTypeOverlay()">Order Type Settings</button>
            <div id="orderTypeOverlay"></div>
            <div id="orderType">
                <h3>Add Order Type</h3>
                <hr>
                @if ($orderTypes)
                    <div class="container-fields">
                        @foreach ($orderTypes as $oType)
                            <form id="texFields" action="{{ route('updateOrderTypes') }}" enctype="multipart/form-data"
                                method="POST" onsubmit="show_Loader()">
                                @csrf
                                <input type="hidden" name="order_type_id" value="{{ $oType->id }}">
                                <div class="inputdivs">
                                    <label for="orderTypes">Order Types</label>
                                    <input type="text" id="orderTypes" name="order_type"
                                        value="{{ $oType->order_type }}" placeholder="dine-in, takeaway..." required>
                                </div>

                                <div id="option_button">
                                    <button type="submit">
                                        <i class='bx bxs-edit-alt'></i>
                                    </button>
                                    <button class="deleteTax" type="button"
                                        onclick="showConfirmDelete('{{ route('deleteOrderTypes', $oType->id) }}')">
                                        <i class='bx bxs-trash-alt'></i>
                                    </button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                    <form action="{{ route('createOrderTypes') }}" method="POST" onsubmit="show_Loader()"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                        <hr>
                        <div style="width: 50%; margin:auto" class="inputdivs">
                            <label for="orderTypes">Order Type</label>
                            <input type="text" id="orderTypes" name="order_type" placeholder="dine-in, takeaway..."
                                required>
                        </div>

                        <div class="forms-btns">
                            <button id="cancel" onclick="closeOrderTypeOverlay()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @else
                    <form action="{{ route('createOrderTypes') }}" method="POST" onsubmit="show_Loader()"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $id }}">
                        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                        <hr>
                        <div style="width: 50%; margin:auto" class="inputdivs">
                            <label for="orderTypes">Order Type</label>
                            <input type="text" id="orderTypes" name="order_type" placeholder="dine-in, takeaway..."
                                required>
                        </div>
                        <div class="forms-btns">
                            <button id="cancel" onclick="closeOrderTypeOverlay()">Cancel</button>
                            <input class="add" type="submit" value="Add">
                        </div>
                    </form>
                @endif
            </div>

            {{--  
            |---------------------------------------------------------------|
            |========================== Theme Overlay ======================|
            |---------------------------------------------------------------|
            --}}

            <button class="opt-buttons" onclick="showThemeSetting()">Theme Settings</button>
            <div id="themeSettingOverlay"></div>
            @if ($settingsData)
                <form action="{{ route('updateThemeSettings') }}" class="themeSetting" id="themeSetting" method="POST"
                    onsubmit="show_Loader()" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                    <input type="hidden" name="setting_id" value="{{ $settingsData->id }}">
                    <h3>Update Logo and Theme</h3>
                    <hr>
                    <div class="inputdivs">
                        <div id="logoDiv">
                            <div class="image" id="imagePreview">
                                <img src="{{ asset('Images/Logos/' . $settingsData->pos_logo) }}">
                            </div>
                            <label id="addImg" for="logoPic">Update Logo</label>
                            <input style="display: none;" type="file" id="logoPic" name="logoPic" accept="image/*"
                                onchange="displayImage()">
                        </div>
                    </div>

                    <div class="inputdivs"
                        style="flex-direction:row; justify-content:flex-start; align-item:start; gap:10px;">
                        <label style="width:40%;" for="primaryColor">Primery Color</label>
                        <input style="width:25%;" type="color" name="primary_color" id="primaryColor"
                            value="{{ $settingsData->pos_primary_color }}">
                    </div>

                    <div class="inputdivs"
                        style="flex-direction:row; justify-content:flex-start; align-item:start; gap:10px;">
                        <label style="width:40%;" for="secondaryColor">Secondary Color</label>
                        <input style="width:25%;" type="color" name="secondary_color" id="secondaryColor"
                            value="{{ $settingsData->pos_secondary_color }}">
                    </div>

                    <div id="form_btns">
                        <button type="button" id="form_btns-close" onclick="closeThemeSetting()">Close</button>
                        <button id="form_btns-update">Update</button>
                        <button id="form_btns-delete" type="button"
                            onclick="showConfirmDelete('{{ route('deleteThemeSettings', $settingsData->id) }}')">Delete</button>
                    </div>
                </form>
            @else
                <form action="{{ route('createThemeSettings') }}" class="themeSetting" id="themeSetting" method="POST"
                    onsubmit="show_Loader()" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                    <h3>Add Logo and Theme</h3>
                    <hr>
                    <div class="inputdivs">
                        <div id="logoDiv">
                            <div class="image" id="imagePreview">
                                <img src="">
                            </div>
                            <label id="addImg" for="logoPic">Update Logo</label>
                            <input style="display: none;" type="file" id="logoPic" name="logoPic" accept="image/*"
                                onchange="displayImage()">
                        </div>
                    </div>

                    <div class="inputdivs">
                        <label for="primaryColor">Primery Color</label>
                        <input type="color" name="primary_color" id="primaryColor">
                    </div>

                    <div class="inputdivs">
                        <label for="secondaryColor">Secondary Color</label>
                        <input type="color" name="secondary_color" id="secondaryColor">
                    </div>

                    <div id="form_btns">
                        <button type="button" id="form_btns-close" onclick="closeThemeSetting()">Close</button>
                        <button id="form_btns-update">Add</button>
                    </div>
                </form>
            @endif

            {{--  
            |---------------------------------------------------------------|
            |========================== Update Profile =====================|
            |---------------------------------------------------------------|
            --}}

            <button class="opt-buttons" onclick="showUpdateProfile()">Update Profile</button>
            <div id="updateProfileOverlay"></div>
            @if ($profile)
                <form action="{{ route('updateProfile') }}" class="updateProfile" id="updateProfile" method="POST"
                    onsubmit="show_Loader()" enctype="multipart/form-data">
                    @csrf
                    <h3>Edit Profile</h3>
                    <hr>
                    <input type="hidden" name="user_id" value="{{ $profile->id }}">
                    <div class="formDiv">
                        <label for="upload-update-file" class="choose-file-btn">
                            <span>Choose File</span>
                            <input type="file" id="upload-update-file" name="updated_profile_picture"
                                accept=".jpg,.jpeg,.png">
                            <p id="filename"></p>
                        </label>
                    </div>

                    <div class="formDiv">
                        <label for="editname">Name</label>
                        <input type="text" id="editname" name="name" value="{{ $profile->name }}" required>
                    </div>

                    <div class="formDiv">
                        <label for="editemail">Email Address</label>
                        <input type="email" id="editemail" name="email" value="{{ $profile->email }}" required
                            readonly>
                    </div>
                    <div class="inputdivs" style="width: 85%; margin: 0.4vw auto;">
                        <label for="password">Password</label>
                        <div class="passwordfield">
                            <input type="password" id="password" name="password" placeholder="**********"
                                oninput="validatePassword()">
                            <i class='bx bxs-show' onclick="showAndHideProfilePswd('password')"></i>
                        </div>
                    </div>

                    <div class="inputdivs" style="width: 85%; margin: 0.4vw auto;">
                        <label for="cnfrmPswd">Confirm Password</label>
                        <div class="passwordfield">
                            <input type="password" id="cnfrmPswd" name="password_confirmation" placeholder="**********"
                                oninput="validatePassword()">
                            <i class='bx bxs-show' onclick="showAndHideProfilePswd('cnfrmPswd')"></i>
                        </div>
                    </div>

                    <div id="message" class="error"></div>

                    <div class="formBtns">
                        <button type="button" id="cancel-profile-update" onclick="hideUpdateProfile()">Cancel</button>
                        <input type="submit" value="Update Profile" id="profile-update">
                    </div>
                </form>
            @endif


            {{--      
            |---------------------------------------------------------------|
            |==================== Sync With Remote DB ======================|
            |---------------------------------------------------------------|
            --}}
            <style>
                .sync_with_remote_db_overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: none;
                    z-index: 999;
                }

                #output {
                    display: none;
                    flex-direction: column;
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background-color: #171717FF;
                    border-radius: 0.25rem;
                    color: #27CA19FF;
                    font-family: monospace;
                    overflow-y: auto;
                    height: 400px;
                    min-width: 400px;
                    max-width: 1000px;
                    padding: 1rem;
                    z-index: 1000;
                }
            </style>
            <div class="sync_with_remote_db_overlay" id="sync_with_remote_db_overlay"></div>
            <div id="output">
                <span>Waiting for sync to start...</span>
            </div>
            <button class="opt-buttons" onclick="syncWithRemoteDB()">Sync With Remote Database</button>

            {{-- <button class="opt-buttons" onclick="syncWithRemoteDB()">Sync With Remote Database</button> --}}


            {{--      
            |---------------------------------------------------------------|
            |==================== Confirm Delete Overlay ===================|
            |---------------------------------------------------------------|
            --}}

            <div id="confirmDeletionOverlay"></div>
            <div class="confirmDeletion" id="confirmDeletion">
                <h3 id="message-text">Are you sure you want to delete.</h3>
                <div class="box-btns">
                    <button id="confirm">Delete</button>
                    <button id="close" onclick="closeConfirmDelete()">Close</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function texOverlay() {
            document.getElementById('texOverlay').style.display = 'block';
            document.getElementById('newTax').style.display = 'flex';
        }

        function closeTax() {
            document.getElementById('texOverlay').style.display = 'none';
            document.getElementById('newTax').style.display = 'none';
        }

        function discountOverlay() {
            document.getElementById('discountOverlay').style.display = 'block';
            document.getElementById('discount').style.display = 'flex';
        }

        function closeDiscountOverlay() {
            document.getElementById('discountOverlay').style.display = 'none';
            document.getElementById('discount').style.display = 'none';
        }

        function receiptOverlay() {
            document.getElementById('receiptOverlay').style.display = 'block';
            document.getElementById('receipt').style.display = 'flex';
        }

        function closeReceiptOverlay() {
            document.getElementById('receiptOverlay').style.display = 'none';
            document.getElementById('receipt').style.display = 'none';
        }

        function paymentMethodOverlay() {
            document.getElementById('paymentMethodOverlay').style.display = 'block';
            document.getElementById('paymentMethod').style.display = 'flex';
        }

        function closePaymentMethodOverlay() {
            document.getElementById('paymentMethodOverlay').style.display = 'none';
            document.getElementById('paymentMethod').style.display = 'none';
        }

        function showDiscountTypeOverlay() {
            document.getElementById('discountTypeOverlay').style.display = 'block';
            document.getElementById('discountType').style.display = 'flex';
        }

        function closePaymentMethodOverlay() {
            document.getElementById('paymentMethodOverlay').style.display = 'none';
            document.getElementById('paymentMethod').style.display = 'none';
        }

        function showDiscountTypeOverlay() {
            document.getElementById('discountTypeOverlay').style.display = 'block';
            document.getElementById('discountType').style.display = 'flex';
        }

        function closeDiscountTypeOverlay() {
            document.getElementById('discountTypeOverlay').style.display = 'none';
            document.getElementById('discountType').style.display = 'none';
        }

        function showOrderTypeOverlay() {
            document.getElementById('orderTypeOverlay').style.display = 'block';
            document.getElementById('orderType').style.display = 'flex';
        }

        function closeOrderTypeOverlay() {
            document.getElementById('orderTypeOverlay').style.display = 'none';
            document.getElementById('orderType').style.display = 'none';
        }

        function showDiscountValueOverlay() {
            document.getElementById('discountValueOverlay').style.display = 'block';
            document.getElementById('discountValue').style.display = 'flex';
        }

        function closeDiscountValueOverlay() {
            document.getElementById('discountValueOverlay').style.display = 'none';
            document.getElementById('discountValue').style.display = 'none';
        }

        function showConfirmDelete(deleteUrl) {
            let confirmDeletionOverlay = document.getElementById('confirmDeletionOverlay');
            let confirmDeletionPopup = document.getElementById('confirmDeletion');
            confirmDeletionOverlay.style.display = 'block';
            confirmDeletionPopup.style.display = 'block';
            document.getElementById('themeSettingOverlay').style.display = 'none';
            document.getElementById('themeSetting').style.display = 'none';
            let confirmButton = document.getElementById('confirm');
            confirmButton.onclick = function() {
                window.location.href = deleteUrl;
            };
        }

        function closeConfirmDelete() {
            let confirmDeletionOverlay = document.getElementById('confirmDeletionOverlay');
            let confirmDeletionPopup = document.getElementById('confirmDeletion');
            confirmDeletionOverlay.style.display = 'none';
            confirmDeletionPopup.style.display = 'none';
            document.getElementById('themeSettingOverlay').style.display = 'block';
            document.getElementById('themeSetting').style.display = 'flex';
        }

        function showThemeSetting() {
            document.getElementById('themeSettingOverlay').style.display = 'block';
            document.getElementById('themeSetting').style.display = 'flex';
        }

        function closeThemeSetting() {
            document.getElementById('themeSettingOverlay').style.display = 'none';
            document.getElementById('themeSetting').style.display = 'none';
        }

        function displayImage() {
            let input = document.getElementById('logoPic');
            const preview = document.getElementById('imagePreview');

            if (input.files?.[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Logo Picture">';
                };
                reader.readAsDataURL(file);
            }
        }

        function showUpdateProfile() {
            document.getElementById('updateProfileOverlay').style.display = 'block';
            document.getElementById('updateProfile').style.display = 'flex';
        }

        function hideUpdateProfile() {
            document.getElementById('updateProfileOverlay').style.display = 'none';
            document.getElementById('updateProfile').style.display = 'none';
            window.location.reload();
        }

        function validatePassword() {
            let password = document.getElementById('password').value;
            let confirmPassword = document.getElementById('cnfrmPswd').value;
            let message = document.getElementById('message');

            if (password.length < 8) {
                message.textContent = "Password must be at least 8 characters long!";
                message.className = "error";
            } else if (password !== confirmPassword) {
                message.textContent = "Passwords do not match!";
                message.className = "error";
            } else {
                message.textContent = "Passwords match!";
                message.className = "success";
            }
        }

        function showAndHideProfilePswd(password) {
            let pswd = document.getElementById(password);
            if (pswd.type === 'password') {
                pswd.type = 'text';
            } else {
                pswd.type = 'password';
            }
        }

        function syncWithRemoteDB() {
            document.getElementById('sync_with_remote_db_overlay').style.display = 'block';
            document.getElementById('output').style.display = 'flex';

            const outputDiv = document.getElementById('output');
            outputDiv.innerHTML = '<span>Starting sync process...</span>';

            fetch('/sync-with-remote-db', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Append the output to the terminal
                    outputDiv.innerHTML = data.output;

                    // Hide the overlay and output div after 1 second
                    setTimeout(() => {
                        hideSyncWithRemoteDB();
                    }, 3000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    outputDiv.innerHTML = '<span style="color: red;">Failed to execute sync.</span>';

                    // Hide the overlay and output div after 1 second
                    setTimeout(() => {
                        hideSyncWithRemoteDB();
                    }, 3000);
                });
        }

        function hideSyncWithRemoteDB() {
            document.getElementById('sync_with_remote_db_overlay').style.display = 'none';
            document.getElementById('output').style.display = 'none';
        }

        const uploadFile = document.getElementById('upload-update-file');
        const filenameSpanNew = document.getElementById('filename');
        uploadFile.addEventListener('change', function(e) {
            const fileName = this.value.split('\\').pop();
            filenameSpanNew.textContent = fileName ? fileName : 'No file chosen';
        });
    </script>
@endsection
