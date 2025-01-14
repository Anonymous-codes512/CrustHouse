@extends('Components.Manager')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Manager - Stock';
    });
</script>
@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Manager/stock.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush
<style>
    #stocksTable_paginate,
    #stocksTable_filter,
    .dataTables_length,
    .dataTables_info {
        margin: 0.5vw 6.2vw !important;
        font-size: 0.8rem !important;
    } 

    #stocksTable_next,
    #stocksTable_previous,
    #stocksTable_paginate span a {
        margin: 0.5vw !important;
        padding: 0 !important;
        font-size: 0.8rem
    }

    table.dataTable {
        border-collapse: collapse !important;
    }
</style>
@section('main')
    <main id="stock">
        @php
            $notifications = $notification;
            $stockData = $stockData;
            $branch_id = $branch_id;
            $user_id = $user_id;
        @endphp

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
            <button onclick="addStock()">Add New Stock</button>
            <button onclick="showLoader('{{ route('viewStockHistory', [$branch_id, $user_id]) }}')">Stock History</button>
        </div>

        @if (!empty($notifications))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('shownotify').click();
                });
            </script>
        @endif

        <table id="stocksTable">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Minimum Quantity</th>
                    <th>Unit price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stockData as $stock)
                    <tr>
                        <td>{{ $stock->itemName }}</td>
                        <td>{{ $stock->itemQuantity }}</td>
                        <td>{{ $stock->mimimumItemQuantity }}</td>
                        <td>{{ $stock->unitPrice }}</td>
                        <td>
                            <a onclick="editStock({{ json_encode($stock) }})"><i class='bx bxs-edit-alt'></i></a>
                            <a onclick="showConfirmDelete('{{ route('deleteStock', $stock->id) }}')"><i
                                    class='bx bxs-trash-alt'></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Add New Stock Overlay --}}
        <div id="overlay"></div>
        <form class="newstock" id="newStock" action="{{ route('createStock') }}" method="POST"
            enctype="multipart/form-data" onsubmit="show_Loader()">
            @csrf
            <h3>Add New Stock</h3>
            <hr>
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <div class="inputdivs">
                <label for="itemName">Product Name</label>
                <input type="text" id="itemName" name="itemName" placeholder="Product Name" required>
            </div>

            <div class="inputdivs unitdivs">
                <div class="stockquantity" style="width:75%;">
                    <label for="quantity">Stock Quantity</label>
                    <input type="number" id="quantity" name="stockQuantity" min="1" placeholder="Stock Quantity"
                        step="any" required>
                </div>

                <div class="unitselection"  style="width:25%;">
                    <label for="stockunit">Unit</label>
                    <select name="unit1" id="stockunit" required>
                        <option value="" selected disabled>Select unit</option>
                        <option value="mg">Milligram</option>
                        <option value="g">Gram</option>
                        <option value="kg">Kilogram</option>
                        <option value="ml">Milliliter</option>
                        <option value="liter">liter</option>
                        <option value="lbs">Pound</option>
                        <option value="gal">Gallon</option>
                        <option value="oz">Ounce</option>
                        <option value="dozen">Dozen</option>
                        <option value="piece">Piece</option>
                    </select>
                </div>
            </div>

            <div class="inputdivs unitdivs">
                <div class="stockquantity" style="width:75%;">
                    <label for="minquantity">Minimum Stock Quantity</label>
                    <input type="number" id="minquantity" name="minStockQuantity" min="1"
                        placeholder="Minimum Stock Quantity" step="any" required>
                </div>

                <div class="unitselection" style="width:25%;">
                    <label for="minStockUnit">Unit</label>
                    <select name="unit2" id="minStockUnit" required>
                        <option value="" selected disabled>Select unit</option>
                        <option value="mg">Milligram</option>
                        <option value="g">Gram</option>
                        <option value="kg">Kilogram</option>
                        <option value="ml">Milliliter</option>
                        <option value="liter">liter</option>
                        <option value="lbs">Pound</option>
                        <option value="gal">Gallon</option>
                        <option value="oz">Ounce</option>
                        <option value="dozen">Dozen</option>
                        <option value="piece">Piece</option>
                    </select>
                </div>
            </div>

            <div class="inputdivs">
                <label for="unitprice">Price per unit</label>
                <input type="number" id="unitprice" name="unitPrice" min="1" placeholder="Unit Price" required>
            </div>

            <div class="btns">
                <button type="button" id="cancel" onclick="closeAddStock()">Cancel</button>
                <input type="submit" value="Add Stock">
            </div>
        </form>

        {{-- Edit Stock Overlay --}}
        <div id="editOverlay"></div>
        <form class="editstock" id="editStock" action="{{ route('updateStock') }}" method="POST"
            enctype="multipart/form-data" onsubmit="show_Loader()">
            @csrf
            <h3>Edit Stock</h3>
            <hr>

            <input type="hidden" id="sId" name="sId" required>

            <div class="inputdivs">
                <label for="iName">Product Name</label>
                <input type="text" id="iName" name="itemName" required>
            </div>

            <div class="inputdivs unitdivs" >
                <div class="stockquantity" style="width:75%;">
                    <label for="iQuantity">Stock Quantity</label>
                    <input type="number" id="iQuantity" name="stockQuantity" step="any" min="1" required>
                </div>

                <div class="unitselection" style="width:25%;">
                    <label for="iQUnit">Unit</label>
                    <select name="unit1" id="iQUnit" required>
                        <option value="" selected disabled>Select unit</option>
                        <option value="mg">Milligram</option>
                        <option value="g">Gram</option>
                        <option value="kg">Kilogram</option>
                        <option value="ml">Milliliter</option>
                        <option value="liter">Liter</option>
                        <option value="gal">Gallon</option>
                        <option value="lbs">Pound</option>
                        <option value="oz">Ounce</option>
                        <option value="dozen">Dozen</option>
                        <option value="piece">Piece</option>
                    </select>
                </div>
            </div>

            <div class="inputdivs unitdivs">
                <div class="stockquantity" style="width:75%;">
                    <label for="mQuantity">Minimum Stock Quantity</label>
                    <input type="number" id="mQuantity" name="minStockQuantity" step="any" min="1"
                        required>
                </div>

                <div class="unitselection" style="width:25%;">
                    <label for="mQUnit">Unit</label>
                    <select name="unit2" id="mQUnit" required>
                        <option value="" selected disabled>Select unit</option>
                        <option value="mg">Milligram</option>
                        <option value="g">Gram</option>
                        <option value="kg">Kilogram</option>
                        <option value="ml">Milliliter</option>
                        <option value="liter">Liter</option>
                        <option value="gal">Gallon</option>
                        <option value="lbs">Pound</option>
                        <option value="oz">Ounce</option>
                        <option value="dozen">Dozen</option>
                        <option value="piece">Piece</option>
                    </select>
                </div>
            </div>

            <div class="inputdivs">
                <label for="UPrice">Price per unit</label>
                <input type="number" id="UPrice" name="unitPrice" min="1" required>
            </div>

            <div class="btns">
                <button type="button" id="cancel" onclick="closeEditStock()">Cancel</button>
                <input type="submit" value="Update">
            </div>
        </form>

        {{-- Notification Popup --}}
        @if ($notifications || !$notifications == null)
            <div id="notificationOverlay"></div>
            <div id="notification">
                @foreach ($notifications as $notify)
                    <p>{{ $notify }}</p>
                @endforeach
                <div>
                    <button id="shownotify" type="button" style="display: none" onclick="showNotification()"></button>
                    <button type="button" onclick="closeNotification()">Ok</button>
                </div>
            </div>
        @endif

        {{--   
            |---------------------------------------------------------------|
            |===================== Confirm deletion ========================|
            |---------------------------------------------------------------|
        --}}

        <div id="confirmDeletionOverlay"></div>
        <div class="confirmDeletion" id="confirmDeletion">
            <h3 id="message-text">Are you sure you want to delete this Stock Item</h3>
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
            $('#stocksTable').DataTable({
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


        function addStock() {
            let overlay = document.getElementById('overlay');
            let popup = document.getElementById('newStock');

            overlay.style.display = 'block';
            popup.style.display = 'flex';
        }

        function closeAddStock() {
            let overlay = document.getElementById('overlay');
            let popup = document.getElementById('newStock');

            overlay.style.display = 'none';
            popup.style.display = 'none';
        }

        function showNotification() {
            let overlay = document.getElementById('notificationOverlay');
            let popup = document.getElementById('notification');

            overlay.style.display = 'block';
            popup.style.display = 'flex';
        }

        function closeNotification() {
            let overlay = document.getElementById('notificationOverlay');
            let popup = document.getElementById('notification');

            overlay.style.display = 'none';
            popup.style.display = 'none';
        }

        function editStock(stock) {
            let overlay = document.getElementById('editOverlay');
            let popup = document.getElementById('editStock');

            document.getElementById('sId').value = stock.id;
            document.getElementById('iName').value = stock.itemName;

            let quantity = '';
            let quantityUnit = '';
            if (stock.itemQuantity) {
                const quantityAndUnit = stock.itemQuantity.match(/([\d.]+)\s*(\D+)/);
                if (quantityAndUnit && quantityAndUnit.length > 1) {
                    quantity = parseFloat(quantityAndUnit[1]);
                }
                if (quantityAndUnit && quantityAndUnit.length > 2) {
                    quantityUnit = quantityAndUnit[2].trim();
                }
            }
            document.getElementById('iQuantity').value = quantity;

            // Set selected unit in the unit select field
            let unitSelect = document.getElementById('iQUnit');
            for (let i = 0; i < unitSelect.options.length; i++) {
                if (unitSelect.options[i].value === quantityUnit.toLowerCase()) {
                    unitSelect.options[i].selected = true;
                    break;
                }
            }

            let minQuantity = '';
            let minQuantityUnit = '';
            if (stock.mimimumItemQuantity) {
                const minQuantityAndUnit = stock.mimimumItemQuantity.match(/([\d.]+)\s*(\D+)/);
                if (minQuantityAndUnit && minQuantityAndUnit.length > 1) {
                    minQuantity = parseFloat(minQuantityAndUnit[1]);
                }
                if (minQuantityAndUnit && minQuantityAndUnit.length > 2) {
                    minQuantityUnit = minQuantityAndUnit[2].trim();
                }
            }
            document.getElementById('mQuantity').value = minQuantity;

            let minUnitSelect = document.getElementById('mQUnit');
            for (let i = 0; i < minUnitSelect.options.length; i++) {
                if (minUnitSelect.options[i].value === minQuantityUnit.toLowerCase()) {
                    minUnitSelect.options[i].selected = true;
                    break;
                }
            }

            document.getElementById('UPrice').value = parseFloat(stock.unitPrice);

            overlay.style.display = 'block';
            popup.style.display = 'flex';
        }

        function closeEditStock() {
            let overlay = document.getElementById('editOverlay');
            let popup = document.getElementById('editStock');

            overlay.style.display = 'none';
            popup.style.display = 'none';
        }
    </script>
@endsection
