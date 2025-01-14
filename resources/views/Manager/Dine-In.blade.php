@extends('Components.Manager')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Manager - Dine-In';
    });
</script>
@push('styles')
    <link rel="stylesheet" href="{{ asset('/CSS/Manager/dinein.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush
<style>
    #dineInTable_paginate,
    #dineInTable_filter,
    .dataTables_length,
    .dataTables_info {
        margin: 0.5vw 4.2vw !important;
        font-size: 0.8rem !important;
    }

    #dineInTable_next,
    #dineInTable_previous,
    #dineInTable_paginate span a {
        margin: 0.5vw !important;
        padding: 0 !important;
        font-size: 0.8rem
    }

    table.dataTable {
        border-collapse: collapse !important;
    }
</style>
@section('main')
    <main id="dineIn">
        @php
            $branch_id = session('branch_id');
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

        <div class="newTableDiv">
            <button onclick="showAddTableForm()" type="button" id="add-table">Add New Table</button>
        </div>

        <table id='dineInTable'>
            <thead>
                <tr>
                    <th>Table Number</th>
                    <th>Max Capacity</th>
                    <th>Table Status</th>
                    <th>Action</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($dineInTables as $table)
                    <tr class="table-row">
                        <td id="table-number">{{ $table->table_number }}</td>
                        <td>{{ $table->max_sitting_capacity }}</td>
                        <td>{{ $table->table_status == 1 ? 'Available' : 'Occupied' }}</td>
                        <td>
                            <a onclick="showEditTableForm({{json_encode($table)}})" title="Edit Table"><i class='bx bxs-edit-alt'></i></a>
                            <a title="Delete Table" onclick="showConfirmDelete('{{route('deleteTable', $table->id)}}')"><i class='bx bxs-trash-alt'></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{--
        |---------------------------------------------------------------|
        |=========================== Add new Table =====================|
        |---------------------------------------------------------------|
        --}}

        <div id="dineInSettingsOverlay"></div>
        <form class="add-table-form" id="add-table-form" action="{{ route('createTable') }}" method="POST"
            enctype="multipart/form-data" onsubmit="show_Loader()">
            @csrf
            <h3>Add New Table</h3>
            <hr>

            <input type="hidden" name="branch_id" value="{{$branch_id}}">
            
            <div class="tableFormDiv">
                <label for="table-number">Table Number</label>
                <input type="text" id="table-number" name="table_number" placeholder="H1T1, H2T1 etc..." required>
            </div>

            <div class="tableFormDiv">
                <label for="table-max-capacity">Table Maximum Capacity</label>
                <input type="text" id="table-max-capacity" name="table_max_capacity" placeholder="4 Chairs,etc.."
                    required>
            </div>

            <div class="tableFormBtns">
                <button type="button" class="cancelBtn" onclick="hideAddTableForm()">Cancel</button>
                <input type="submit" value="Add Table">
            </div>
        </form>

        {{--
        |---------------------------------------------------------------|
        |=========================== Edit Table ========================|
        |---------------------------------------------------------------|
        --}}

        <form class="edit-table-form" id="edit-table-form" action="{{ route('updateTable') }}" method="POST"
            enctype="multipart/form-data" onsubmit="show_Loader()">
            @csrf
            <h3>Edit Table</h3>
            <hr>

            <input type="hidden" name="branch_id" value="{{$branch_id}}">
            <input type="hidden" name="table_id" id="table_id">

            <div class="tableFormDiv">
                <label for="edit_table-number">Table Number</label>
                <input type="text" id="edit_table-number" name="table_number" placeholder="H1T1, H2T1 etc..." required>
            </div>

            <div class="tableFormDiv">
                <label for="edit_table-max-capacity">Table Maximum Capacity</label>
                <input type="text" id="edit_table-max-capacity" name="table_max_capacity" placeholder="4 Chairs,etc.."
                    required>
            </div>

            {{-- <div class="tableFormDiv">
                <label for="edit_table-status">Table Status</label>
                <input type="text" id="edit_table-status" name="table-status" placeholder="Available, Occupied"
                    required>
            </div> --}}

            <div class="tableFormBtns">
                <button type="button" class="cancelBtn" onclick="hideEditTableForm()">Cancel</button>
                <input type="submit" value="Update Table">
            </div>
        </form>

        {{--   
            |---------------------------------------------------------------|
            |===================== Confirm deletion ========================|
            |---------------------------------------------------------------|
        --}}
        <div class="confirmDeletion" id="confirmDeletion">
            <h3 id="message-text">Are you sure you want to delete</h3>
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
            $('#dineInTable').DataTable({
                "paging": true,
                "lengthMenu": [5, 10, 25, 50, 100]
            });
        });

        function showConfirmDelete(deleteUrl) {
            let confirmDeletionOverlay = document.getElementById('dineInSettingsOverlay');
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
            let confirmDeletionOverlay = document.getElementById('dineInSettingsOverlay');
            let confirmDeletionPopup = document.getElementById('confirmDeletion');
            confirmDeletionOverlay.style.display = 'none';
            confirmDeletionPopup.style.display = 'none';
        }

        function showTablesSettings(branchOptions) {
            if (branchOptions.DiningOption == 1) {
                document.getElementById('tableSettingsOverlay').style.display = 'block';
                document.getElementById('tableSettings').style.display = 'flex';
            } else {
                document.getElementById('warningOverlay').style.display = 'block';
                document.getElementById('warning').style.display = 'flex';
            }
        }

        function showAddTableForm() {
            document.getElementById('dineInSettingsOverlay').style.display = 'block';
            document.getElementById('add-table-form').style.display = 'flex';
        }

        function hideAddTableForm() {
            document.getElementById('dineInSettingsOverlay').style.display = 'none';
            document.getElementById('add-table-form').style.display = 'none';
        }

        function showEditTableForm(dineInTable) {
            document.getElementById('dineInSettingsOverlay').style.display = 'block';
            document.getElementById('edit-table-form').style.display = 'flex';
            
            console.log(dineInTable);
            console.log(dineInTable.table_number);
            console.log(dineInTable.max_sitting_capacity);
            console.log(dineInTable.table_status);

            document.getElementById('table_id').value = dineInTable.id;
            document.getElementById('edit_table-number').value = dineInTable.table_number;
            document.getElementById('edit_table-max-capacity').value = dineInTable.max_sitting_capacity;
            // document.getElementById('edit_table-status').value = dineInTable.table_status == 1 ? 'Available' : 'Occupied';
        }

        function hideEditTableForm() {
            document.getElementById('dineInSettingsOverlay').style.display = 'none';
            document.getElementById('edit-table-form').style.display = 'none';
        }
    </script>
@endsection
