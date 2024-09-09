@extends('Components.Owner')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush
<style>
    #branchTable_paginate,
    #branchTable_filter,
    .dataTables_length,
    .dataTables_info {
        margin: 0.5vw 4.5vw !important;
        font-size: 1rem !important;
    }

    #branchTable_next,
    #branchTable_previous,
    #branchTable_paginate span a {
        margin: 0.5vw !important;
        padding: 0 !important;
        font-size: 0.8rem
    }

    table.dataTable {
        border-collapse: collapse !important;
    }
</style>
@section('main')
    @include('Components.Loader')
    <main id="dashboard">

        <div style="margin-top:1vw;" class="stat">
            <div class="totalRevenue" id="totalrevenue">
                <div class="icon">
                    <i class='bx bx-dollar-circle'></i>
                </div>
                <div class="disc">
                    <p>Total Revenue</p>
                    @if ($totalRevenue)
                        <h3>Rs. {{ $totalRevenue }}</h3>
                    @else
                        <h3>Nil</h3>
                    @endif
                </div>
            </div>

            <div class="totalBranch" id="totalmenu">
                <div class="icon">
                    <i class='bx bx-package'></i>
                </div>
                <div class="disc">
                    @if ($totalCompanies)
                        <p>Total Companies</p>
                        <h3>{{ $totalCompanies }}</h3>
                    @else
                        <h3>Nil</h3>
                    @endif
                </div>
            </div>

            <div class="totalStaff" id="totalstaff">
                <div class="icon">
                    <i class='bx bxs-group'></i>
                </div>
                <div class="disc">
                    <p>Total Staff</p>
                    @if ($totalStaff)
                        <h3>{{ $totalStaff }}</h3>
                    @else
                        <h3>Nil</h3>
                    @endif
                </div>
            </div>
        </div>
        <div class="branch-table">
            <div class="add-New-Branch">
                <button type="button" onclick="addNewBranch()">Add New Branch</button>
            </div>
            <table id="branchTable">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Branch City</th>
                        <th>Branch Name</th>
                        <th>Branch Code</th>
                        <th>Branch Address</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $renderedCompany = [];
                    @endphp
                    @foreach ($branchData as $branch)
                        @php
                            if (isset($renderedCompany[$branch->company_name])) {
                                continue;
                            }

                            $branchId = [];
                            $branchCity = [];
                            $branchName = [];
                            $branchCode = [];
                            $branchAddress = [];
                            foreach ($branchData as $data) {
                                if ($data->company_name === $branch->company_name) {
                                    $branchId[] = $data->id;
                                    $branchCity[] = $data->branch_city;
                                    $branchName[] = $data->branch_name;
                                    $branchCode[] = $data->branch_code;
                                    $branchAddress[] = $data->branch_address;
                                }
                            }
                            $renderedCompany[$branch->company_name] = true;
                        @endphp
                        <tr style="border-bottom: 1px solid #000;">
                            <td class="truncate-text" title="{{ $branch->company_name }}">{{ $branch->company_name }}</td>
                            <td>
                                <div style="display: flex; flex-direction:column;">
                                    @foreach ($branchCity as $city)
                                        <p class="truncate-text" title="{{ $city }}">{{ $city }}</p>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction:column;">
                                    @foreach ($branchName as $name)
                                        <p class="truncate-text" title="{{ $name }}">{{ $name }}</p>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction:column;">
                                    @foreach ($branchCode as $code)
                                        <p class="truncate-text" title="{{ $code }}">{{ $code }}</p>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction:column;">
                                    @foreach ($branchAddress as $address)
                                        <p class="truncate-text" title="{{ $address }}">{{ $address }}</p>
                                    @endforeach
                                </div>
                            </td>
                            <td style="text-align:center; align-items:center;justify-content:center">
                                @foreach ($branchId as $id)
                                    <div
                                        style="display: flex; flex-direction:row; justify-content:space-evenly; margin:2vw 0">
                                        <a class="edit" title="Edit"
                                            onclick="editBranch({{ json_encode($branchData) }}, {{ json_encode($id) }})"><i
                                                class='bx bxs-edit-alt'></i></a>
                                        <a class="delete" title="Delete"
                                            onclick="showConfirmDelete('{{ route('deleteBranch', $id) }}')"><i
                                                class='bx bxs-trash-alt'></i></a>
                                        <a class="statistics" onclick="showLoader('{{ route('showBranchStats', $id) }}')"
                                            title="Statistics"><i class='bx bx-stats'></i></a>
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if (session()->has('Orders'))
            @php
                $Orders = session('Orders');
                $completesOrders = $Orders->where('status', 1);
                $cancelOrders = $Orders->where('status', 3);

                $branchName = $Orders->branch_details->company_name . ' - ' . $Orders->branch_details->branch_city;

                $totalOrders = $Orders->count();
                $totalCompletedOrders = $completesOrders->count();
                $totalCancelledOrders = $cancelOrders->count();

                $totalRevenue = 0;
                $discount = 0;
                $taxes = 0;
                $total_bill = 0;

                foreach ($completesOrders as $order) {
                    $discount += $order->discount;
                    $taxes += $order->taxes;
                    $total_bill += (float) str_replace('Rs. ', '', $order->total_bill);
                }
                $totalRevenue = $total_bill + $taxes - $discount;
            @endphp

            <div id="statisticsPopUpOverlay"></div>
            <div id="statisticsPopUp">
                <h3>{{ $branchName }} Stats</h3>
                <div id=statisticsDiv>
                    <div class="Order">
                        <div class="text-side">
                            <h4>Total Revenue</h4>
                            <p class="data">Rs. {{ $totalRevenue }}</p>
                        </div>
                        <div class="logo-side"><img src="{{ asset('Images/total-revenue.png') }}" alt=""></div>
                    </div>
                    <div class="Order">
                        <div class="text-side">
                            <h4>Total Orders</h4>
                            <p class="data">{{ $totalOrders }}</p>
                        </div>
                        <div class="logo-side"><img src="{{ asset('Images/total-orders.png') }}" alt=""></div>
                    </div>
                    <div class="Order">
                        <div class="text-side">
                            <h4>Completed Orders</h4>
                            <p class="data">{{ $totalCompletedOrders }}</p>
                        </div>
                        <div class="logo-side"><img src="{{ asset('Images/completed-orders.png') }}" alt=""></div>
                    </div>
                    <div class="Order">
                        <div class="text-side">
                            <h4>Cancel Orders</h4>
                            <p class="data">{{ $totalCancelledOrders }}</p>
                        </div>
                        <div class="logo-side"><img src="{{ asset('Images/cancel-orders.png') }}" alt=""></div>
                    </div>
                </div>
                <button id="statistics-close" type="button" onclick="hideStatisticsPopUp()">Close</button>
                <script>
                    function hideStatisticsPopUp() {
                        document.getElementById('statisticsPopUpOverlay').style.display = 'none';
                        document.getElementById('statisticsPopUp').style.display = 'none';
                    };
                </script>
            </div>
        @endif

        @if (session('warning'))
            <div id="warningOverlay" class="warningOverlay"></div>
            <div id="warning" class="warning">
                <p style="text-align:center;">
                    {{ session('warning') }}
                </p>
                <button id="warning-close" type="button" onclick="hideWarning()">Ok</button>
            </div>
            <script>
                function hideWarning() {
                    document.getElementById('warningOverlay').style.display = 'none';
                    document.getElementById('warning').style.display = 'none';
                };
            </script>
        @endif
        {{--  
            |---------------------------------------------------------------|
            |================== Add new Branch Overlay =====================|
            |---------------------------------------------------------------|
        --}}

        <div id="addNewBranchOverlay"></div>
        <form id="addNewBranch" action="{{ route('storeNewBranchData') }}" method="Post" enctype="multipart/form-data">
            @csrf
            <h3>Add New Branch</h3>
            <hr>

            <div class="states-cities inputdivs">
                <div class="states">
                    <label for="branchstate">Select State</label>
                    <select name="branch_state" id="branchstate" required
                        onchange="updateCityOptions('branchstate', 'brancharea')">
                        <option value="" selected>Select State</option>
                        <option value="Capital">Capital</option>
                        <option value="Punjab">Punjab</option>
                        <option value="Sindh">Sindh</option>
                        <option value="KPK">KPK</option>
                        <option value="Balochistan">Balochistan</option>
                    </select>
                </div>

                <div class="cities">
                    <label for="brancharea">Select City</label>
                    <select name="branch_city" id="brancharea" required
                        onchange="BranchCode(this.value, {{ json_encode($branchData) }})">
                    </select>
                </div>

            </div>

            <div style="display: flex; width: 85%;margin: 0.4vw auto;">
                <div class="inputdivs" style="width: 49%;margin:0; margin-right:1vw;">
                    <label for="companyName">Company Name</label>
                    <input type="text" name="company_name" id="companyName" placeholder="CrustHouse, Yums, etc"
                        required>
                </div>

                <div class="inputdivs" style="width: 49%; margin:0;">
                    <label for="branchInitial">Branch Initial</label>
                    <input type="text" name="branch_initial" id="branchInitial"
                        placeholder="Crust-House CH, Tahzeeb TB, etc" required>
                </div>
            </div>

            <div style="display: flex; width: 85%;margin: 0.4vw auto;">
                <div class="inputdivs" style="width: 49%;margin:0; margin-right:1vw;">
                    <label for="branchname">Branch Name</label>
                    <input type="text" name="branch_name" id="branchname" placeholder="KFC, NFC, etc" required>
                </div>

                <div class="inputdivs" style="width: 49%; margin:0;">
                    <label for="branchcode">Branch Code</label>
                    <input type="text" name="branch_code" id="branchcode" placeholder="Branch Code" required readonly
                        style="background-color: #dadada">
                </div>
            </div>

            <div class="inputdivs">
                <label for="address">Branch Address</label>
                <input type="text" name="branch_address" id="address" placeholder="Address" required>
            </div>

            <div class="inputdivs">
                <label for="manager_Email">Branch Manager</label>
                <input type="email" name="manager_email" id="manager_Email" placeholder="email@gmail.com"
                    oninput="validateEmail()" required>
            </div>
            <div id="email-error-message" class="error-message" style="display: none;"></div>
            <div class="inputdivs">
                <label for="password">Password</label>
                <div class="passwordField">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-show' onclick="showAndHideOwnerPswd()"></i>
                </div>
            </div>

            <div class="pswdBtns">
                <button type="button" class="generate" onclick="GeneratePassword()">Generate</button>
                <button type="button" class="copy" onclick="copyPassword()">Copy</button>
            </div>

            <div class="inputdivs">
                <label for="options">Select Additional Options</label>
                <div id="options" class="options">
                    <div class="opt" style="border-right: 1px solid #000;">
                        <p class="opt-txt">Rider</p>
                        <label class="switch">
                            <input type="checkbox" name="riderOption">
                        </label>
                    </div>

                    <div class="opt" style="border-right: 1px solid #000;">
                        <p class="opt-txt">Online</p>
                        <label class="switch">
                            <input type="checkbox" name="onlineDeliveryOption">
                        </label>
                    </div>

                    <div class="opt">
                        <p class="opt-txt">Dine-In</p>
                        <label class="switch">
                            <input type="checkbox" name="diningTableOption" checked>
                        </label>
                    </div>
                </div>
            </div>

            <div class="btns">
                <button type="button" id="cancel" onclick="closeAddNewBranch()">Cancel</button>
                <input type="submit" id="submit-btn" value="Add Now">
            </div>
        </form>

        {{--  
            |---------------------------------------------------------------|
            |================== Edit new Branch Overlay ====================|
            |---------------------------------------------------------------|
        --}}

        <div id="editBranchOverlay"></div>
        <form id="editBranch" action="{{ route('updateBranches') }}" method="Post" enctype="multipart/form-data">
            @csrf
            <h3>Edit Branch</h3>
            <hr>

            <input type="hidden" name="branch_id" id="branch_id">

            <div class="states-cities inputdivs">
                <div class="states">
                    <label for="editbranchstate">Select State</label>
                    <select name="branch_state" id="editbranchstate" required
                        onchange="updateCityOptions('editbranchstate', 'editbrancharea')">
                        <option value="" selected>Select State</option>
                        <option value="Capital">Capital</option>
                        <option value="Punjab">Punjab</option>
                        <option value="Sindh">Sindh</option>
                        <option value="KPK">KPK</option>
                        <option value="Balochistan">Balochistan</option>
                    </select>
                </div>

                <div class="cities">
                    <label for="editbrancharea">Select City</label>
                    <select name="branch_city" id="editbrancharea"
                        onchange="BranchCode(this.value, {{ json_encode($branchData) }})" required>
                        <option value="" selected></option>
                    </select>
                </div>
            </div>
            <div style="display: flex; width: 85%;margin: 0.4vw auto;">
                <div class="inputdivs" style="width: 49%;margin:0; margin-right:1vw;">
                    <label for="editCompanyName">Company Name</label>
                    <input type="text" name="company_name" id="editCompanyName" placeholder="CrustHouse, Yums, etc"
                        required>
                </div>

                <div class="inputdivs" style="width: 49%; margin:0;">
                    <label for="editBranchInitial">Branch Initial</label>
                    <input type="text" name="branch_initial" id="editBranchInitial"
                        placeholder="Crust-House CH, Tahzeeb TB, etc" required>
                </div>
            </div>

            <div style="display: flex; width: 85%;margin: 0.4vw auto;">
                <div class="inputdivs" style="width: 49%;margin:0; margin-right:1vw;">
                    <label for="branchname">Branch Name</label>
                    <input type="text" name="branch_name" id="editBranchName" placeholder="KFC, NFC, etc" required>
                </div>

                <div class="inputdivs" style="width: 49%; margin:0;">
                    <label for="branchcode">Branch Code</label>
                    <input type="text" name="branch_code" id="editBranchCode" placeholder="Branch Code" readonly
                        required style="background-color: #dadada">
                </div>
            </div>

            <div class="inputdivs">
                <label for="editaddress">Branch Address</label>
                <input type="text" name="branch_address" id="editaddress" placeholder="Address" required>
            </div>
            <input type="hidden" id="brnachManagerId" name="branch_manager_id">
            <div class="inputdivs">
                <label for="manager_Email">Branch Manager</label>
                <input type="email" name="manager_email" id="editManagerEmail" placeholder="email@gmail.com"
                    oninput="validateEditEmail()" required>
            </div>
            <div id="edit-email-error-message" class="error-message" style="display: none;"></div>
            <div class="inputdivs">
                <label for="password">Password</label>
                <div class="passwordField">
                    <input type="password" id="editPassword" name="password" placeholder="Password">
                    <i class='bx bxs-show' onclick="showAndHideEditPswd()"></i>
                </div>
            </div>

            <div class="pswdBtns">
                <button type="button" class="generate" onclick="GeneratePassword()">Generate</button>
                <button type="button" class="copy" onclick="copyPassword()">Copy</button>
            </div>

            <div class="inputdivs">
                <label for="editoptions">Select Additional Options</label>
                <div id="editoptions" class="options">
                    <div class="opt">
                        <p class="opt-txt">Rider</p>
                        <label class="switch">
                            <input type="checkbox" name="riderOption" id="editriderOption">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="opt">
                        <p class="opt-txt">Online</p>
                        <label class="switch">
                            <input type="checkbox" name="onlineDeliveryOption" id="editonlineDeliveryOption">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="opt">
                        <p class="opt-txt">Dine-In</p>
                        <label class="switch">
                            <input type="checkbox" name="diningTableOption" id="editdiningTableOption">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="btns">
                <button type="button" id="editcancel" onclick="closeEditBranch()">Cancel</button>
                <input type="submit" id="update-btn" value="Update Now">
            </div>
        </form>

        {{--   
            |---------------------------------------------------------------|
            |===================== Confirm deletion ========================|
            |---------------------------------------------------------------|
        --}}
        <div id="confirmDeletionOverlay"></div>
        <div class="confirmDeletion" id="confirmDeletion">
            <h3 id="message-text">Are you sure you want to delete this Branch</h3>
            <div class="inputdivs">
                <label style="margin-bottom:15px;" for="formRandomString">Enter this Code: <span
                        style="font-family:consolas; background-color:#000; padding:5px; color:#fff;border-radius:7px; font-size:18px; letter-spacing:5px;"
                        id="rndom"></span></label>
                <input type="text" id="formRandomString" name="random_string" autocomplete="off" required>
            </div>
            <div class="box-btns">
                <button id="confirm">Delete</button>
                <button id="close" onclick="closeConfirmDelete()">Close</button>
            </div>
            <script>
                document.getElementById("formRandomString").addEventListener("input", function() {
                    let enteredString = this.value.trim();
                    let randomString = document.getElementById("rndom").textContent;
                    let deleteButton = document.getElementById("confirm");

                    if (enteredString === randomString) {
                        deleteButton.disabled = false;
                        deleteButton.style.background = '#b52828';
                        deleteButton.style.cursor = 'pointer';
                    } else {
                        deleteButton.style.background = '#ed7680';
                        deleteButton.disabled = true;
                    }
                });
            </script>
        </div>

        {{--   
            |---------------------------------------------------------------|
            |===================== Edit Owner Profile ======================|
            |---------------------------------------------------------------|
        --}}
        <div id="editOwnerProfileOverlay"></div>
        <form id="editOwnerProfile" action="{{ route('UpdateOwnerProfile') }}" method="Post"
            enctype="multipart/form-data">
            @csrf
            <h3>Edit Owner Profile</h3>
            <hr>

            <input type="hidden" name="owner_id" id="owner_id">

            <div class="inputdivs">
                <label for="upload-file" class="choose-file-btn">
                    <span>Choose File</span>
                    <input type="file" id="upload-file" name="profile_picture" accept=".jpg,.jpeg,.png">
                    <p id="filename"></p>
                </label>
            </div>

            <div class="inputdivs">
                <label for="full_name">Full Name</label>
                <input type="text" name="name" id="full_name" placeholder="Full name" required>
            </div>

            <div class="inputdivs">
                <label for="owner_email">Email Address</label>
                <input type="email" name="owner_email" id="owner_email" placeholder="email@gmail.com"
                    oninput="validateOwnerEmail()" required>
            </div>
            <div id="edit-error-message" class="error-message" style="display: none;"></div>
            <div class="inputdivs">
                <label for="password">Password</label>
                <div class="passwordField">
                    <input type="password" id="ownerPassword" name="password" oninput="validatePassword()"
                        placeholder="Password">
                    <i class='bx bxs-show' onclick="showAndHideOwnerPswd()"></i>
                </div>
            </div>
            <div id="password-error-message" class="error-message" style="display: none;"></div>

            <div class="btns">
                <button type="button" id="editOwnerCancel" onclick="closeUpdateProfile()">Cancel</button>
                <input type="submit" id="updateOwner-btn" value="Update Profile">
            </div>
        </form>

    </main>

    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#branchTable').DataTable({
                "paging": true,
                "lengthMenu": [5, 10, 25, 50, 100]
            });
        });

        const statesWithCitiesAndInitials = [{
                stateName: "Capital",
                cities: [{
                    city: "Islamabad",
                    initials: "ISB"
                }]
            },
            {
                stateName: "Punjab",
                cities: [{
                        city: "Ahmed Nager Chatha",
                        initials: "ANC"
                    },
                    {
                        city: "Ahmadpur East",
                        initials: "ADE"
                    },
                    {
                        city: "Ali Khan Abad",
                        initials: "AKA"
                    },
                    {
                        city: "Alipur",
                        initials: "ALP"
                    },
                    {
                        city: "Arifwala",
                        initials: "ARF"
                    },
                    {
                        city: "Attock",
                        initials: "ATK"
                    },
                    {
                        city: "Bhera",
                        initials: "BHE"
                    },
                    {
                        city: "Bhalwal",
                        initials: "BWL"
                    },
                    {
                        city: "Bahawalnagar",
                        initials: "WGB"
                    },
                    {
                        city: "Bahawalpur",
                        initials: "BHV"
                    },
                    {
                        city: "Bhakkar",
                        initials: "BKR"
                    },
                    {
                        city: "Burewala",
                        initials: "BRW"
                    },
                    {
                        city: "Chillianwala",
                        initials: "CHI"
                    },
                    {
                        city: "Chakwal",
                        initials: "CKW"
                    },
                    {
                        city: "Chichawatni",
                        initials: "CCE"
                    },
                    {
                        city: "Chiniot",
                        initials: "CHI"
                    },
                    {
                        city: "Chishtian",
                        initials: "CSI"
                    },
                    {
                        city: "Daska",
                        initials: "DKA"
                    },
                    {
                        city: "Darya Khan",
                        initials: "DYA"
                    },
                    {
                        city: "Dera Ghazi Khan",
                        initials: "DEA"
                    },
                    {
                        city: "Dhaular",
                        initials: "DHA"
                    },
                    {
                        city: "Dina",
                        initials: "DIN"
                    },
                    {
                        city: "Dinga",
                        initials: "DIN"
                    },
                    {
                        city: "Dipalpur",
                        initials: "DIP"
                    },
                    {
                        city: "Faisalabad",
                        initials: "LYP"
                    },
                    {
                        city: "Ferozewala",
                        initials: "FER"
                    },
                    {
                        city: "Fateh Jhang",
                        initials: "FAT"
                    },
                    {
                        city: "Ghakhar Mandi",
                        initials: "GHK"
                    },
                    {
                        city: "Gojra",
                        initials: "GOJ"
                    },
                    {
                        city: "Gujranwala",
                        initials: "GRW"
                    },
                    {
                        city: "Gujrat",
                        initials: "GRT"
                    },
                    {
                        city: "Gujar Khan",
                        initials: "GKN"
                    },
                    {
                        city: "Hafizabad",
                        initials: "HFD"
                    },
                    {
                        city: "Haroonabad",
                        initials: "HAR"
                    },
                    {
                        city: "Hasilpur",
                        initials: "HAS"
                    },
                    {
                        city: "Haveli Lakha",
                        initials: "HAV"
                    },
                    {
                        city: "Jatoi",
                        initials: "JAT"
                    },
                    {
                        city: "Jalalpur",
                        initials: "JLP"
                    },
                    {
                        city: "Jattan",
                        initials: "JAT"
                    },
                    {
                        city: "Jampur",
                        initials: "JAM"
                    },
                    {
                        city: "Jaranwala",
                        initials: "JNW"
                    },
                    {
                        city: "Jhang",
                        initials: "JNG"
                    },
                    {
                        city: "Jhelum",
                        initials: "JMR"
                    },
                    {
                        city: "Kalabagh",
                        initials: "KAL"
                    },
                    {
                        city: "Karor Lal Esan",
                        initials: "KLE"
                    },
                    {
                        city: "Kasur",
                        initials: "KUS"
                    },
                    {
                        city: "Kamalia",
                        initials: "KML"
                    },
                    {
                        city: "Kamoke",
                        initials: "KAM"
                    },
                    {
                        city: "Khanewal",
                        initials: "KWL"
                    },
                    {
                        city: "Khanpur",
                        initials: "KPR"
                    },
                    {
                        city: "Kharian",
                        initials: "KHA"
                    },
                    {
                        city: "Khushab",
                        initials: "KHB"
                    },
                    {
                        city: "Kot Addu",
                        initials: "ADK"
                    },
                    {
                        city: "Jauharabad",
                        initials: "JAB"
                    },
                    {
                        city: "Lahore",
                        initials: "LHE"
                    },
                    {
                        city: "Lalamusa",
                        initials: "LAL"
                    },
                    {
                        city: "Layyah",
                        initials: "LAY"
                    },
                    {
                        city: "Liaquat Pur",
                        initials: "LQP"
                    },
                    {
                        city: "Lodhran",
                        initials: "LON"
                    },
                    {
                        city: "Malakwal",
                        initials: "MLK"
                    },
                    {
                        city: "Mamoori",
                        initials: "MAM"
                    },
                    {
                        city: "Mailsi",
                        initials: "MAI"
                    },
                    {
                        city: "Mandi Bahauddin",
                        initials: "MBD"
                    },
                    {
                        city: "Mian Channu",
                        initials: "MYH"
                    },
                    {
                        city: "Mianwali",
                        initials: "MWD"
                    },
                    {
                        city: "Multan",
                        initials: "MUX"
                    },
                    {
                        city: "Murree",
                        initials: "MUR"
                    },
                    {
                        city: "Muridke",
                        initials: "MDK"
                    },
                    {
                        city: "Mianwali Bangla",
                        initials: "MWB"
                    },
                    {
                        city: "Muzaffargarh",
                        initials: "MZJ"
                    },
                    {
                        city: "Narowal",
                        initials: "NRW"
                    },
                    {
                        city: "Nankana Sahib",
                        initials: "NNS"
                    },
                    {
                        city: "Okara",
                        initials: "OKR"
                    },
                    {
                        city: "Renala Khurd",
                        initials: "REN"
                    },
                    {
                        city: "Pakpattan",
                        initials: "PAK"
                    },
                    {
                        city: "Pattoki",
                        initials: "PTO"
                    },
                    {
                        city: "Pir Mahal",
                        initials: "PMH"
                    },
                    {
                        city: "Qaimpur",
                        initials: "QAI"
                    },
                    {
                        city: "Qila Didar Singh",
                        initials: "QDS"
                    },
                    {
                        city: "Rabwah",
                        initials: "RAB"
                    },
                    {
                        city: "Raiwind",
                        initials: "RND"
                    },
                    {
                        city: "Rajanpur",
                        initials: "RJP"
                    },
                    {
                        city: "Rahim Yar Khan",
                        initials: "RYK"
                    },
                    {
                        city: "Rawalpindi",
                        initials: "RWP"
                    },
                    {
                        city: "Sadiqabad",
                        initials: "SAD"
                    },
                    {
                        city: "Safdarabad",
                        initials: "SAB"
                    },
                    {
                        city: "Sahiwal",
                        initials: "SWN"
                    },
                    {
                        city: "Sangla Hill",
                        initials: "SLL"
                    },
                    {
                        city: "Sarai Alamgir",
                        initials: "SAR"
                    },
                    {
                        city: "Sargodha",
                        initials: "SGI"
                    },
                    {
                        city: "Shakargarh",
                        initials: "SHA"
                    },
                    {
                        city: "Sheikhupura",
                        initials: "SKP"
                    },
                    {
                        city: "Sialkot",
                        initials: "SKT"
                    },
                    {
                        city: "Sohawa",
                        initials: "SOH"
                    },
                    {
                        city: "Soianwala",
                        initials: "SOI"
                    },
                    {
                        city: "Siranwali",
                        initials: "SIR"
                    },
                    {
                        city: "Talagang",
                        initials: "TLG"
                    },
                    {
                        city: "Taxila",
                        initials: "TXA"
                    },
                    {
                        city: "Toba Tek Singh",
                        initials: "TTS"
                    },
                    {
                        city: "Vehari",
                        initials: "VHR"
                    },
                    {
                        city: "Wah Cantonment",
                        initials: "WAH"
                    },
                    {
                        city: "Wazirabad",
                        initials: "WZA"
                    }
                ]
            },
            // Sindh
            {
                stateName: "Sindh",
                cities: [{
                        city: "Badin",
                        initials: "BDN"
                    },
                    {
                        city: "Bhirkan",
                        initials: "BHI"
                    },
                    {
                        city: "Rajo Khanani",
                        initials: "RAJ"
                    },
                    {
                        city: "Chak",
                        initials: "CHK"
                    },
                    {
                        city: "Dadu",
                        initials: "DDU"
                    },
                    {
                        city: "Digri",
                        initials: "DIG"
                    },
                    {
                        city: "Diplo",
                        initials: "DIP"
                    },
                    {
                        city: "Dokri",
                        initials: "DOK"
                    },
                    {
                        city: "Ghotki",
                        initials: "GHT"
                    },
                    {
                        city: "Haala",
                        initials: "HAA"
                    },
                    {
                        city: "Hyderabad",
                        initials: "HDD"
                    },
                    {
                        city: "Islamkot",
                        initials: "ISL"
                    },
                    {
                        city: "Jacobabad",
                        initials: "JAG"
                    },
                    {
                        city: "Jamshoro",
                        initials: "JAM"
                    },
                    {
                        city: "Jungshahi",
                        initials: "JGS"
                    },
                    {
                        city: "Kandhkot",
                        initials: "KAN"
                    },
                    {
                        city: "Kandiaro",
                        initials: "KAN"
                    },
                    {
                        city: "Karachi",
                        initials: "KHI"
                    },
                    {
                        city: "Kashmore",
                        initials: "KAS"
                    },
                    {
                        city: "Keti Bandar",
                        initials: "KTB"
                    },
                    {
                        city: "Khairpur",
                        initials: "KHP"
                    },
                    {
                        city: "Kotri",
                        initials: "KOT"
                    },
                    {
                        city: "Larkana",
                        initials: "LRK"
                    },
                    {
                        city: "Matiari",
                        initials: "MAT"
                    },
                    {
                        city: "Mehar",
                        initials: "MEH"
                    },
                    {
                        city: "Mirpur Khas",
                        initials: "MPK"
                    },
                    {
                        city: "Mithani",
                        initials: "MIT"
                    },
                    {
                        city: "Mithi",
                        initials: "MIT"
                    },
                    {
                        city: "Mehrabpur",
                        initials: "MEB"
                    },
                    {
                        city: "Moro",
                        initials: "MOR"
                    },
                    {
                        city: "Nagarparkar",
                        initials: "NAG"
                    },
                    {
                        city: "Naudero",
                        initials: "NAU"
                    },
                    {
                        city: "Naushahro Feroze",
                        initials: "NSF"
                    },
                    {
                        city: "Naushara",
                        initials: "NAU"
                    },
                    {
                        city: "Nawabshah",
                        initials: "NAW"
                    },
                    {
                        city: "Nazimabad",
                        initials: "NAZ"
                    },
                    {
                        city: "Qambar",
                        initials: "QAM"
                    },
                    {
                        city: "Qasimabad",
                        initials: "QAS"
                    },
                    {
                        city: "Ranipur",
                        initials: "RAN"
                    },
                    {
                        city: "Ratodero",
                        initials: "RAT"
                    },
                    {
                        city: "Rohri",
                        initials: "ROH"
                    },
                    {
                        city: "Sakrand",
                        initials: "SAK"
                    },
                    {
                        city: "Sanghar",
                        initials: "SAN"
                    },
                    {
                        city: "Shahbandar",
                        initials: "SHB"
                    },
                    {
                        city: "Shahdadkot",
                        initials: "SHD"
                    },
                    {
                        city: "Shahdadpur",
                        initials: "SDP"
                    },
                    {
                        city: "Shahpur Chakar",
                        initials: "SPC"
                    },
                    {
                        city: "Shikarpaur",
                        initials: "SHK"
                    },
                    {
                        city: "Sukkur",
                        initials: "SUK"
                    },
                    {
                        city: "Tangwani",
                        initials: "TAN"
                    },
                    {
                        city: "Tando Adam Khan",
                        initials: "TAK"
                    },
                    {
                        city: "Tando Allahyar",
                        initials: "TAI"
                    },
                    {
                        city: "Tando Muhammad Khan",
                        initials: "TMK"
                    },
                    {
                        city: "Thatta",
                        initials: "THA"
                    },
                    {
                        city: "Umerkot",
                        initials: "UME"
                    },
                    {
                        city: "Warah",
                        initials: "WAR"
                    }
                ]
            },
            // KPK
            {
                stateName: "KPK",
                cities: [{
                        city: "Abbottabad",
                        initials: "ABT"
                    },
                    {
                        city: "Adezai",
                        initials: "ADE"
                    },
                    {
                        city: "Alpuri",
                        initials: "ALP"
                    },
                    {
                        city: "Akora Khattak",
                        initials: "AKK"
                    },
                    {
                        city: "Ayubia",
                        initials: "AYU"
                    },
                    {
                        city: "Banda Daud Shah",
                        initials: "BDS"
                    },
                    {
                        city: "Bannu",
                        initials: "BAN"
                    },
                    {
                        city: "Batkhela",
                        initials: "BTK"
                    },
                    {
                        city: "Battagram",
                        initials: "BTT"
                    },
                    {
                        city: "Birote",
                        initials: "BIO"
                    },
                    {
                        city: "Chakdara",
                        initials: "CKD"
                    },
                    {
                        city: "Charsadda",
                        initials: "CHA"
                    },
                    {
                        city: "Chitral",
                        initials: "CHI"
                    },
                    {
                        city: "Daggar",
                        initials: "DAG"
                    },
                    {
                        city: "Dargai",
                        initials: "DAR"
                    },
                    {
                        city: "Darya Khan",
                        initials: "DYA"
                    },
                    {
                        city: "Dera Ismail Khan",
                        initials: "DIK"
                    },
                    {
                        city: "Doaba",
                        initials: "DOA"
                    },
                    {
                        city: "Dir",
                        initials: "DIR"
                    },
                    {
                        city: "Drosh",
                        initials: "DRO"
                    },
                    {
                        city: "Hangu",
                        initials: "HANG"
                    },
                    {
                        city: "Haripur",
                        initials: "HAR"
                    },
                    {
                        city: "Karak",
                        initials: "KAR"
                    },
                    {
                        city: "Kohat",
                        initials: "KOH"
                    },
                    {
                        city: "Kulachi",
                        initials: "KUL"
                    },
                    {
                        city: "Lakki Marwat",
                        initials: "LMW"
                    },
                    {
                        city: "Latamber",
                        initials: "LAT"
                    },
                    {
                        city: "Madyan",
                        initials: "MAD"
                    },
                    {
                        city: "Mansehra",
                        initials: "MAN"
                    },
                    {
                        city: "Mardan",
                        initials: "MRD"
                    },
                    {
                        city: "Mastuj",
                        initials: "MAS"
                    },
                    {
                        city: "Mingora",
                        initials: "MNG"
                    },
                    {
                        city: "Nowshera",
                        initials: "NOW"
                    },
                    {
                        city: "Paharpur",
                        initials: "PAH"
                    },
                    {
                        city: "Pabbi",
                        initials: "PAB"
                    },
                    {
                        city: "Peshawar",
                        initials: "PES"
                    },
                    {
                        city: "Saidu Sharif",
                        initials: "SSP"
                    },
                    {
                        city: "Shorkot",
                        initials: "SHK"
                    },
                    {
                        city: "Shewa Adda",
                        initials: "SHA"
                    },
                    {
                        city: "Swabi",
                        initials: "SWA"
                    },
                    {
                        city: "Swat",
                        initials: "SW"
                    },
                    {
                        city: "Tangi",
                        initials: "TAN"
                    },
                    {
                        city: "Tank",
                        initials: "TANK"
                    },
                    {
                        city: "Thall",
                        initials: "THA"
                    },
                    {
                        city: "Timergara",
                        initials: "TIM"
                    },
                    {
                        city: "Tordher",
                        initials: "TORD"
                    }
                ]
            },
            // Balochistan
            {
                stateName: "Balochistan",
                cities: [{
                        city: "Awaran",
                        initials: "AWN"
                    },
                    {
                        city: "Barkhan",
                        initials: "BKH"
                    },
                    {
                        city: "Chagai",
                        initials: "CHG"
                    },
                    {
                        city: "Dera Bugti",
                        initials: "DEB"
                    },
                    {
                        city: "Gwadar",
                        initials: "GWD"
                    },
                    {
                        city: "Harnai",
                        initials: "HRN"
                    },
                    {
                        city: "Jafarabad",
                        initials: "JAF"
                    },
                    {
                        city: "Jhal Magsi",
                        initials: "JHA"
                    },
                    {
                        city: "Kacchi",
                        initials: "KAC"
                    },
                    {
                        city: "Kalat",
                        initials: "KAL"
                    },
                    {
                        city: "Kech",
                        initials: "KEC"
                    },
                    {
                        city: "Kharan",
                        initials: "KHA"
                    },
                    {
                        city: "Khuzdar",
                        initials: "KHD"
                    },
                    {
                        city: "Killa Abdullah",
                        initials: "KAB"
                    },
                    {
                        city: "Killa Saifullah",
                        initials: "KSA"
                    },
                    {
                        city: "Kohlu",
                        initials: "KOH"
                    },
                    {
                        city: "Lasbela",
                        initials: "LAS"
                    },
                    {
                        city: "Lehri",
                        initials: "LEH"
                    },
                    {
                        city: "Loralai",
                        initials: "LOR"
                    },
                    {
                        city: "Mastung",
                        initials: "MAS"
                    },
                    {
                        city: "Musakhel",
                        initials: "MUS"
                    },
                    {
                        city: "Nasirabad",
                        initials: "NAS"
                    },
                    {
                        city: "Nushki",
                        initials: "NUS"
                    },
                    {
                        city: "Panjgur",
                        initials: "PAN"
                    },
                    {
                        city: "Pishin Valley",
                        initials: "PIS"
                    },
                    {
                        city: "Quetta",
                        initials: "QUE"
                    },
                    {
                        city: "Sherani",
                        initials: "SHR"
                    },
                    {
                        city: "Sibi",
                        initials: "SIB"
                    },
                    {
                        city: "Sohbatpur",
                        initials: "SOH"
                    },
                    {
                        city: "Washuk",
                        initials: "WAS"
                    },
                    {
                        city: "Zhob",
                        initials: "ZHB"
                    },
                    {
                        city: "Ziarat",
                        initials: "ZIA"
                    }
                ]
            }
        ];

        function addNewBranch() {
            const OVERLAY = document.getElementById('addNewBranchOverlay');
            const POPUP = document.getElementById('addNewBranch');
            OVERLAY.style.display = 'block';
            POPUP.style.display = 'flex';
        }

        function closeAddNewBranch() {
            const OVERLAY = document.getElementById('addNewBranchOverlay');
            const POPUP = document.getElementById('addNewBranch');
            OVERLAY.style.display = 'none';
            POPUP.style.display = 'none';
        }

        function updateProfile(owner) {
            document.getElementById('editOwnerProfileOverlay').style.display = 'block';
            document.getElementById('editOwnerProfile').style.display = 'flex';
            document.getElementById('owner_id').value = owner.id;
            document.getElementById('full_name').value = owner.name;
            document.getElementById('owner_email').value = owner.email;

        }

        function closeUpdateProfile() {
            document.getElementById('editOwnerProfileOverlay').style.display = 'none';
            document.getElementById('editOwnerProfile').style.display = 'none';
        }

        function editBranch(Branches, branch_id) {
            const OVERLAY = document.getElementById('editBranchOverlay');
            const POPUP = document.getElementById('editBranch');
            Branches.forEach(Branch => {
                if (Branch.id == branch_id) {
                    document.getElementById('branch_id').value = Branch.id;
                    document.getElementById('editbranchstate').value = Branch.branch_state;
                    updateCityOptions('editbranchstate', 'editbrancharea');
                    document.getElementById('editbrancharea').value = Branch.branch_city;
                    document.getElementById('editCompanyName').value = Branch.company_name;
                    document.getElementById('editBranchInitial').value = Branch.branch_initial;
                    document.getElementById('editBranchName').value = Branch.branch_name;
                    document.getElementById('editBranchCode').value = Branch.branch_code;
                    document.getElementById('editaddress').value = Branch.branch_address;
                    document.getElementById('editManagerEmail').value = Branch.branchManager.email;

                    document.getElementById('brnachManagerId').value = Branch.branchManager.id;

                    document.getElementById('editriderOption').checked = Branch.riderOption == 1;
                    document.getElementById('editonlineDeliveryOption').checked = Branch.onlineDeliveryOption == 1;
                    document.getElementById('editdiningTableOption').checked = Branch.DiningOption == 1;


                }
            })
            OVERLAY.style.display = 'block';
            POPUP.style.display = 'flex';
        }

        function closeEditBranch() {
            const OVERLAY = document.getElementById('editBranchOverlay');
            const POPUP = document.getElementById('editBranch');
            OVERLAY.style.display = 'none';
            POPUP.style.display = 'none';
        }

        function showConfirmDelete(deleteUrl) {
            let confirmDeletionOverlay = document.getElementById('confirmDeletionOverlay');
            let confirmDeletionPopup = document.getElementById('confirmDeletion');
            confirmDeletionOverlay.style.display = 'block';
            confirmDeletionPopup.style.display = 'flex';
            let deleteButton = document.getElementById("confirm");
            deleteButton.disabled = true;
            deleteButton.style.background = '#ed7680';

            rndom.textContent = Math.random().toString(36).slice(2, 6).toUpperCase();

            let confirmButton = document.getElementById('confirm');
            confirmButton.onclick = function() {
                document.getElementById('loaderOverlay').style.display = 'block';
                document.getElementById('loader').style.display = 'flex';
                confirmDeletionOverlay.style.display = 'none';
                confirmDeletionPopup.style.display = 'none';
                window.location.href = deleteUrl;
            };
        }

        function closeConfirmDelete() {
            let confirmDeletionOverlay = document.getElementById('confirmDeletionOverlay');
            let confirmDeletionPopup = document.getElementById('confirmDeletion');
            confirmDeletionOverlay.style.display = 'none';
            confirmDeletionPopup.style.display = 'none';
            document.getElementById('formRandomString').value = '';
        }

        function updateCityOptions(stateId, cityId) {
            const stateSelect = document.getElementById(stateId);
            const citySelect = document.getElementById(cityId);
            const selectedState = stateSelect.value;

            citySelect.innerHTML = '<option value="" selected>Select Location</option>';

            const state = statesWithCitiesAndInitials.find(s => s.stateName === selectedState);

            if (state) {
                state.cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.city;
                    option.textContent = city.city;
                    citySelect.appendChild(option);
                });
            }
        }

        updateCityOptions('branchstate', 'brancharea');

        function showAndHideEditPswd() {
            let pswd = document.getElementById('editPassword');
            if (pswd.type === 'password') {
                pswd.type = 'text';
            } else {
                pswd.type = 'password';
            }
        }

        function showAndHideOwnerPswd() {
            let pswd = document.getElementById('ownerPassword');
            if (pswd.type === 'password') {
                pswd.type = 'text';
            } else {
                pswd.type = 'password';
            }
        }

        function BranchCode(value, branchData) {
            let cityInitials;
            statesWithCitiesAndInitials.forEach(state => {
                state.cities.forEach(city => {
                    if (city.city == value) {
                        cityInitials = city.initials;
                    }
                });
            });

            let min = 1;
            let max = 999;
            let number = Math.floor(Math.random() * (max - min + 1)) + min;
            let codeNumber = number.toString().padStart(3, '0');

            let branch_Code = `${cityInitials}-${codeNumber}`;
            
            if (branchData.length > 0) {
                branchData.forEach(branch => {
                    if (branch.branch_code == branch_Code) {
                        BranchCode(value, branchData);
                    } else {
                        document.getElementById('branchcode').value = branch_Code;
                        document.getElementById('editBranchCode').value = branch_Code;
                    }
                })
            } else {
                document.getElementById('branchcode').value = branch_Code;
            }
        }
        
        function GeneratePassword() {
            let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!@#$%^&*()~`{}[];:.,></?\|";
            let generatedPassword = "";
            let charactersLength = characters.length;
            for (let i = 0; i < 10; i++) {
                let rendomIndex = Math.floor(Math.random() * charactersLength);
                generatedPassword += characters[rendomIndex];
            }
            document.getElementById('password').value = generatedPassword;
            document.getElementById('editPassword').value = generatedPassword;
        }

        function copyPassword() {
            const passwordField = document.getElementById('password');
            passwordField.select();
            text = document.execCommand('copy');
            alert('Password copied to clipboard!', text);
        }

        function showLoader(route) {
            document.getElementById('loaderOverlay').style.display = 'block';
            document.getElementById('loader').style.display = 'flex';
            window.location.href = route;
        }

        function validateEmail() {
            let email = document.getElementById("manager_Email").value.trim();
            let emailErrorMessage = document.getElementById('email-error-message');
            let submitBtn = document.getElementById('submit-btn');
            if (email == '') {
                emailErrorMessage.style.display = 'none';
                return;
            }
            if (!email.endsWith(".com")) {
                emailErrorMessage.style.display = 'block';
                emailErrorMessage.textContent = "Email must end with '.com'.";
                submitBtn.disabled = true;
                submitBtn.style.backgroundColor = '#034a6c';
                return;
            }
            var invalidChars = /[\*\/=\-+]/;
            if (invalidChars.test(email)) {
                emailErrorMessage.style.display = 'block';
                emailErrorMessage.textContent = "Email contains invalid characters like *, /, =.";
                submitBtn.disabled = true;
                submitBtn.style.backgroundColor = '#034a6c';
                return;
            }
            emailErrorMessage.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.style.backgroundColor = '#008fd3';
        }

        function validateEditEmail() {
            let email = document.getElementById("editManagerEmail").value.trim();
            let emailErrorMessage = document.getElementById('edit-email-error-message');
            let submitBtn = document.getElementById('update-btn');
            if (email == '') {
                emailErrorMessage.style.display = 'none';
                return;
            }
            if (!email.endsWith(".com")) {
                emailErrorMessage.style.display = 'block';
                emailErrorMessage.textContent = "Email must end with '.com'.";
                submitBtn.disabled = true;
                submitBtn.style.backgroundColor = '#034a6c';
                return;
            }
            var invalidChars = /[\*\/=\-+]/;
            if (invalidChars.test(email)) {
                emailErrorMessage.style.display = 'block';
                emailErrorMessage.textContent = "Email contains invalid characters like *, /, =.";
                submitBtn.disabled = true;
                submitBtn.style.backgroundColor = '#034a6c';
                return;
            }
            submitBtn.disabled = false;
            emailErrorMessage.style.display = 'none';
            submitBtn.style.backgroundColor = '#008fd3';
        }

        function validateOwnerEmail() {
            let email = document.getElementById("owner_email").value.trim();
            let emailErrorMessage = document.getElementById('edit-error-message');
            let submitBtn = document.getElementById('updateOwner-btn');
            if (email == '') {
                emailErrorMessage.style.display = 'none';
                return;
            }
            if (!email.endsWith(".com")) {
                emailErrorMessage.style.display = 'block';
                emailErrorMessage.textContent = "Email must end with '.com'.";
                submitBtn.disabled = true;
                submitBtn.style.cursor = 'not-allowed';
                return;
            }
            var invalidChars = /[\*\/=\-+]/;
            if (invalidChars.test(email)) {
                emailErrorMessage.style.display = 'block';
                emailErrorMessage.textContent = "Email contains invalid characters like *, /, =.";
                submitBtn.disabled = true;
                return;
            }
            submitBtn.style.cursor = 'pointer';
            submitBtn.disabled = false;
            emailErrorMessage.style.display = 'none';
        }

        function validatePassword() {
            let password = document.getElementById('ownerPassword').value;
            let message = document.getElementById('password-error-message');

            if (password.length < 8) {
                message.textContent = "Password must be at least 8 characters long!";
                message.style.display = 'block';
            } else {
                message.textContent = "Valid Password";
                message.style.color = '#146c43';
                setTimeout(() => {
                    message.style.display = 'none';
                }, 1000);
            }
        }
        const uploadFile = document.getElementById('upload-file');
        const filenameSpanNew = document.getElementById('filename');
        uploadFile.addEventListener('change', function(e) {
            const fileName = this.value.split('\\').pop();
            filenameSpanNew.textContent = fileName ? fileName : 'No file chosen';
        });
    </script>
@endsection
