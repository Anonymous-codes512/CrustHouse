<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crust-House</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="{{ asset('Images/icon-512.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('CSS/OnlineOrdering/layer.css') }}" class="css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
</head>
<script src="{{ asset('JavaScript/onlineOrdering.js') }}"></script>
<script src="{{ asset('JavaScript/onlineOrderingDeal.js') }}"></script>
<script>
    if (performance.navigation.type === 1) {
        window.location.href = "{{ route('onlineOrderPage') }}";
    }
</script>

<body>
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

    @if (session('Order_Place_Success'))
        <div id="success" class="alert alert-success">
            {{ session('success') }}
        </div>
        <script>
            localStorage.removeItem("cartItems");
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

    @if (session('deleteSucceed'))
        <script>
            localStorage.clear();
        </script>
    @endif

    @php
        $Products = $Products;
        $Deals = $Deals;
        $Categories = $Categories;
        $count = $Categories->count();
        $AllProducts = $AllProducts;

        $addons = $AllProducts->whereIn('category_name', ['Addons', 'addons', 'Addon', 'addon']);

    @endphp

    <div class="overlay" id="overlay" style="display: none;"></div>
    <div class="frontmodel" id='frontModel' style="display: none;">
        <div class="maincontainer">
            <div class="mainp1">
                <span class="imgmain">
                    <img src="{{ asset('Images/OnlineOrdering/image-1.png') }}" alt="">
                </span>
            </div>

            {{-- <div class="btndelivery">
                <button class="btndel">Delivery</button>
            </div> --}}
            <div class="sub_items1">
                <span class="text1">Please Select your Location</span>
                <div class="btnlocation">
                    <button style="cursor: pointer;" id="useCurrentLocation" class="btnloc"><img class="crosshair"
                            src="{{ asset('Images/OnlineOrdering/crosshair.png') }}" alt="">Use Current
                        Location</button>
                </div>
            </div>
            <div class="fields">
                <div class="inputfields">
                    <input class="subin_fields" type="text" name="" id="district"
                        placeholder="Select City / Region " required readonly>
                    <input class="subin_fields" type="text" name="" id="address"
                        placeholder="Select Area / Sub Region" required readonly>
                    <div id="location-message" class="error-message" style="display: none;"></div>
                    <button id="submit-btn" class="btnn" onclick="SelectLocation()">Select</button>
                </div>
            </div>
            {{-- <div class="close-button">
                <button class="closebtn" onclick="closeFrontModel()">&times;</button>
            </div> --}}
        </div>

    </div>
    <!-- end of frontmodel -->

    <div class="whole">
        <div class="temp">
            <div class="popwhole">
                <div class="nav">
                    <div class="location" onclick="changeLocation()">
                        <img class="locimg" src="{{ asset('Images/OnlineOrdering/locationpin.png') }}" alt="">
                        <div class="locelement">
                            <span class="ele1">Deliver to</span>
                            <span class="ele1 truncate-text-address" id="addr"></span>
                        </div>
                    </div>
                    <div class="mainimage" onclick="window.location.href='{{ route('onlineOrderPage') }}'" style="cursor: pointer;">
                        <img class="imgelement" src="{{ asset('Images/OnlineOrdering/image-1.png') }}" alt="Online Ordering">
                    </div>
                    
                    <div class="icons">
                        <div id="search_bar_div" class="search_bar_div">
                            <input type="text" id="search_bar" name="search" placeholder="Search by product name..."
                                style="background-image: url('{{ asset('Images/search.png') }}');">
                        </div>
                        <div class="icons-btns">
                            <hr>
                            <img class="locimgg" onclick="toggleSearchBar()"
                                src="{{ asset('Images/OnlineOrdering/search.png') }}" alt="">
                            <script>
                                function toggleSearchBar() {
                                    let searchBarDiv = document.getElementById('search_bar_div');

                                    if (searchBarDiv) {
                                        searchBarDiv.style.display = (searchBarDiv.style.display === 'flex') ? 'none' : 'flex';
                                    } else {
                                        console.error('Element with id "search_bar_div" not found.');
                                    }
                                }
                            </script>
                            <hr>
                            <div class="profile-container">
                                <div id="profile-image-name" onclick="checkProfile(event)">
                                    <img id="profileImg" class="locimgg"
                                        src="{{ asset('Images/OnlineOrdering/profile.png') }}" alt="">
                                    <span id="username" class="truncate-text"></span>
                                </div>
                                <div id="dropdownMenu" class="dropdownMenu-content">
                                    <a
                                        onclick="openProfilePopup('{{ route('deleteCustomer', ':customer_id') }}')">Profile</a>
                                    <a onclick="logout()">Logout</a>
                                </div>
                            </div>
                            <hr>
                            <img onclick="toggleCart()" id="locimgg" class="locimgg"
                                src="{{ asset('Images/OnlineOrdering/cart.png') }}" alt="">
                            <hr>

                        </div>
                    </div>
                </div>

                <div class="section">
                    @foreach ($Categories as $key => $Category)
                        <a href="#section{{ $key + 1 }}">
                            <button class="botn">{{ $Category->categoryName }}</button>
                        </a>
                    @endforeach
                    <a href="#section0">
                        <button class="botn">Deals</button>
                    </a>
                </div>

                <div class="root">
                    @foreach ($Categories as $key => $Category)
                        <div id="section{{ $key + 1 }}" class="content-section">
                            <h1>{{ $Category->categoryName }}</h1>
                            <div class="items">
                                @php
                                    $CategoryProducts = $Products->where('category_id', $Category->id);
                                    $displayedProductNames = [];
                                @endphp
                                @foreach ($CategoryProducts as $Product)
                                    @if (!in_array($Product->productName, $displayedProductNames))
                                        @php
                                            $displayedProductNames[] = $Product->productName;
                                        @endphp
                                        <div class="imgbox">
                                            <div class="imgstyle">
                                                <img class="cardimg"
                                                    src="{{ asset('Images/ProductImages/' . $Product->productImage) }}"
                                                    alt="Product">
                                            </div>
                                            <div class="infoo">
                                                <p class="product_name">{{ $Product->productName }}</p>
                                                <div class="space"></div>
                                                <div class="mini">
                                                    <div>
                                                        <p class="product_price">Rs. {{ $Product->productPrice }}</p>
                                                    </div>
                                                    <div class="btn">
                                                        <button
                                                            onclick="addToCart({{ json_encode($Product) }}, {{ json_encode($CategoryProducts) }}, {{ json_encode($addons) }})"
                                                            class="cartbtn">Add To Cart</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div id="section0" class="content-section">
                        @php
                            $displayedDealTitles = [];
                        @endphp
                        <h1>Deals</h1>
                        <div class="items">
                            @foreach ($Deals as $Deal)
                                @if (!in_array($Deal->deal->dealTitle, $displayedDealTitles))
                                    @php
                                        $displayedDealTitles[] = $Deal->deal->dealTitle;
                                    @endphp
                                    @if ($Deal->deal->dealStatus != 'not active')
                                        <div class="imgbox">
                                            <div class="imgstyle">
                                                <img class="cardimg"
                                                    src="{{ asset('Images/DealImages/' . $Deal->deal->dealImage) }}"
                                                    alt="Deals">
                                            </div>
                                            <div class="infoo">
                                                <p class="product_name">{{ $Deal->deal->dealTitle }}</p>
                                                <div class="space"></div>
                                                <div class="mini">
                                                    <div>
                                                        <p class="product_price">Rs.
                                                            {{ $Deal->deal->dealDiscountedPrice }}</p>
                                                    </div>
                                                    <div class="btn">
                                                        <button class="cartbtn"
                                                            onclick="addDealToCart({{ json_encode($Deal) }}, {{ json_encode($Deals) }}, {{ json_encode($AllProducts) }})">Add
                                                            To
                                                            Cart</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="gapp"></div>

                <div class="footer_main">
                    <div class="footer">
                        <div class="footer_hlf">
                            <div class="footergrid">
                                <div class="footer_1">
                                    <img class="imgg" src="{{ asset('Images/OnlineOrdering/image-1.png') }}"
                                        alt="Logo">
                                </div>
                                <div class="footergrid2">
                                    <div>
                                        <span class="cont_us">Contact Us:</span>
                                    </div>
                                    <div class="cu">
                                        <ul>
                                            <li><img class="contact"
                                                    src="{{ asset('Images/OnlineOrdering/phone.png') }}"
                                                    alt="">03090555415
                                            </li>
                                            <li><img class="contact"
                                                    src="{{ asset('Images/OnlineOrdering/email.png') }}"
                                                    alt="">support@crusthouse.com</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="foot_timing">
                                <div class="timingsetting">
                                    <span>Our Timing:</span>
                                </div>
                                <div class="time">
                                    <ul class="sub_time">
                                        <li class="fontt">Monday - Thursday</li>
                                        <li class="fontt">Friday</li>
                                        <li class="fontt">Saturday - Sunday</li>
                                    </ul>
                                    <ul class="sub_time">
                                        <li class="fontt">11:00 AM - 03:00 AM</li>
                                        <li class="fontt">02:00 PM - 03:00 AM</li>
                                        <li class="fontt">11:00 AM - 03:00 AM</li>
                                    </ul>
                                </div>
                                <div>
                                    <span class="timingsetting">Follow us:</span>
                                </div>
                                <div class="follow">

                                    <div class="followico">
                                        <a href=""><img class="iconsize"
                                                src="{{ asset('/Images/OnlineOrdering/facebook.png') }}"
                                                alt=""></a>
                                        <a href=""><img class="iconsize"
                                                src="{{ asset('/Images/OnlineOrdering/instagram.png') }}"
                                                alt=""></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="hr">
                        <div class="footer_hlf1">
                            <div class="ele_hlf1">
                                <span class="ele_hlf2">© 2024 Powered by:
                                    <a href="https://tachyontechs.com/" target="_blank"><img class="logo"
                                            src="{{ asset('Images/OnlineOrdering/logo.png') }}" alt=""></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- end of main page -->

    <!-- openpop -->
    <div class="openitems" id="popup" style="display: none; ">
        <div class="itemss" id="itemss">
            <div class="hlfcontainer">
                <div class="mini_halfcontainer">
                    <div class="mini_items">
                        <span class="img_item">
                            <img class="popup-img" id="popup-img" src="" alt="Product Image">
                        </span>
                    </div>
                    <div class="textshow">
                        <span class="popup_title" id="popup-title"></span>
                        <span class="popup_price" id="popup-price"></span>
                    </div>
                </div>
                <div class="extra_items">
                    <div class="extra_1" style="display: flex; flex-direction: column; gap: 1vw;">
                        <div class="extra_4" onclick="toggleDropdown(this)">
                            <div class="extra_5">
                                <div class="extra_6">
                                    <span class="size">Variation</span>
                                    <span class="required">Required</span>
                                </div>
                                <div class="arrow" onclick="toggleDropdown()">
                                    <img class="up" src="{{ asset('Images/OnlineOrdering/up.png') }}"
                                        alt="">
                                    <img class="down" src="{{ asset('Images/OnlineOrdering/down.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                        <div id="dropdown-content" class="dropdown-content">
                            <div class="dropdown_1">
                                <div>
                                    <div class="drop">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="extra_4" class="extra_4" onclick="toggleDropdown(this)">
                            <div class="extra_5">
                                <div class="extra_6">
                                    <span class="size">Toppings</span>
                                    <span class="required">Required</span>
                                </div>
                                <div class="arrow" onclick="toggleDropdown1()">
                                    <img class="up" src="{{ asset('Images/OnlineOrdering/up.png') }}"
                                        alt="">
                                    <img class="down" src="{{ asset('Images/OnlineOrdering/down.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                        <div id="dropdown-content1" class="dropdown-content">
                            <div class="dropdown_1">
                                <div>
                                    <div class="drop">

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="bottom">
                        <div class="bottom_1">
                            <button onclick="decreaseQuantity()" class="bottom_2" id="decrease">
                                -
                            </button>
                            <span class="count" id="quantity">1</span>

                            <button onclick="increaseQuantity()" class="bottom_2" id="increase">
                                +
                            </button>

                        </div>
                        <div>
                            <button type="button" onclick="closeAddToCart()" class="close_btn">Close</button>
                            <button class="addcart" onclick="handleCartButtonClick() ">
                                <div>
                                    <span id="originalprice" style="display: none;"></span>
                                    <span id="cart-price"></span>
                                </div>
                                <div>Add to Cart</div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="openitems" id="dealPopup" style="display: none; ">
        <div class="itemss" id="itemss">
            <div class="hlfcontainer">
                <div class="mini_halfcontainer">
                    <div class="mini_items">
                        <span class="img_item">
                            <img class="popup-img" id="deal_popup-img" src="" alt="Product Image">
                        </span>
                    </div>
                    <div class="textshow">
                        <span class="popup_title" id="deal_popup-title"></span>
                        <span class="popup_price" id="deal_popup-dealName"></span>
                        <span class="popup_price" id="deal_popup-price"></span>
                    </div>
                </div>
                <div class="extra_items">
                    <div class="extra_1" style="display: flex; flex-direction: column; gap: 1vw;">
                        <div id="extra_55" class="extra_4" onclick="toggleDropdown(this)">
                            <div class="extra_5">
                                <div class="extra_6">
                                    <span class="size">Pizza Variation</span>
                                    <span class="required">Required</span>
                                </div>
                                <div class="arrow" onclick="toggleDropdown()">
                                    <img class="up" src="{{ asset('Images/OnlineOrdering/up.png') }}"
                                        alt="">
                                    <img class="down" src="{{ asset('Images/OnlineOrdering/down.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                        <div id="dropdown-content" class="dropdown-content">
                            <div class="dropdown_1">
                                <div>
                                    <div class="dealDrop">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="extra_45" class="extra_4" onclick="toggleDropdown(this)">
                            <div class="extra_5">
                                <div class="extra_6">
                                    <span class="size">Toppings</span>
                                    <span class="required">Required</span>
                                </div>
                                <div class="arrow" onclick="toggleDropdown1()">
                                    <img class="up" src="{{ asset('Images/OnlineOrdering/up.png') }}"
                                        alt="">
                                    <img class="down" src="{{ asset('Images/OnlineOrdering/down.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                        <div id="dropdown-content1" class="dropdown-content">
                            <div class="dropdown_1">
                                <div>
                                    <div class="dealAddonDrop">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="extra_65" class="extra_4" onclick="toggleDropdown(this)">
                            <div class="extra_5">
                                <div class="extra_6">
                                    <span class="size">Drink Flavour</span>
                                    <span class="required">Required</span>
                                </div>
                                <div class="arrow" onclick="toggleDropdown1()">
                                    <img class="up" src="{{ asset('Images/OnlineOrdering/up.png') }}"
                                        alt="">
                                    <img class="down" src="{{ asset('Images/OnlineOrdering/down.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                        <div id="dropdown-content1" class="dropdown-content">
                            <div class="dropdown_1">
                                <div>
                                    <div class="dealDrinkDrop">

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="bottom">
                        <div class="bottom_1">
                            <button onclick="decreaseQuantityy()" class="bottom_2" id="decrease">
                                -
                            </button>
                            <span class="count" id="quantityy">1</span>

                            <button onclick="increaseQuantityy()" class="bottom_2" id="increase">
                                +
                            </button>

                        </div>
                        <div>
                            <button type="button" onclick="closeDealAddToCart()" class="close_btn">Close</button>
                            <button id="dealaddcart" class="addcart" onclick="handleDealCartButtonClick()">
                                <div>Add to Cart</div>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of popup product -->


    <!-- login component -->
    <div id="logincomponent" class="logincomponent">
        <form class="popup-content" onsubmit="event.preventDefault(); loginUser('{{ route('customerLogin') }}');"
            enctype="multipart/form-data">
            @csrf
            <div class="textt">
                <span class="details">Login</span>
            </div>
            <div class="phone-input">
                <span class="select">Please enter email and confirm your country code and enter your mobile
                    number</span>
                <div class="lo_email">
                    <input class="l_input" type="email" id="loginEmail" name="email" required
                        placeholder="Enter your email">
                </div>
            </div>
            <div class="passwordField">
                <input type="password" id="password" name="password" placeholder="Password" autocomplete="off"
                    required>
                <i class='bx bxs-show' onclick="showAndHidePswd('password')"></i>
            </div>
            <div id="login-response-message" class="error-message" style="display: none;"></div>
            <div class="privacy">This site is protected by reCAPTCHA and the Google
                <a href="https://policies.google.com/privacy" target="_blank">Privacy Policy</a>
                and
                <a href="https://policies.google.com/terms" target="_blank">Terms of Service</a>
                apply.
            </div>
            <div class="loginbtn">
                <button type="submit" class="lobtn">Login</button>
                <a id="forgotPassword" class="forgot-password" href="{{ route('customerForgotPassword') }}">Forgot
                    Password?</a>
                <div class="orline">
                    <span class="or">OR</span>
                </div>
                <button class="signbtn" onclick="showSignupPopup()">Sign-up</button>
            </div>
        </form>
        <span class="close" onclick="hideLoginPopup()">&times;</span>
    </div>

    <div id="signupcomponent" class="signupcomponent">
        <form class="main" action="{{ route('customerSignup') }}" method="POST" enctype="multipart/form-data"
            onsubmit="return updateLoginStatus()">
            @csrf
            <div class="regtext">
                <h2 class="r_text">Register</h2>
            </div>
            <div class="userinfo">
                <div class="fields">
                    <div class="name">
                        <label class="s_label">Enter Name</label>
                        <div class="gap">
                            <input class="s_input" type="text" id="name" name="name" maxlength="25"
                                placeholder="Enter your name" required>
                        </div>
                    </div>

                    <div class="email">
                        <label class="s_label">Enter Email</label>
                        <div class="gap">
                            <input class="s_input" type="email" id="email" name="email"
                                placeholder="Enter your email" oninput = "validateEmail()" required>
                        </div>
                        <div id="email-error-message" class="error-message" style="display: none;"></div>
                    </div>

                    {{-- <div class="gend-dob">
                        {{-- <div class="main-GenDob"> --}}
                    {{-- <div class="row1">
                                <label class="s_label" for="gender">Gender</label>
                                <select name="" id="gender">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div> --/}}

                            <div class="row2">
                                <label class="s_label">Date of Birth</label>
                                <div>
                                    <input type="date" id="dob" placeholder="dd-mm-yyyy">
                                </div>
                                <script>
                                    const today = new Date();
                                    today.setDate(today.getDate() - 1);
                                    const previousDay = today.toISOString().split('T')[0];
                                    document.getElementById('dob').setAttribute('max', previousDay);
                                </script>
                            </div>
                        {{-- </div> --/}}
                    </div> --}}

                    <div id="mobileno" class="mobileno">
                        <label class="s_label">Mobile Number</label>
                        <div class="codee">
                            <input id="countryCodee" type="tel" name="phonePrefix" value="+92"
                                max="3" readonly>
                            <input id="phoneNumberr" type="tel" name="phone_number" maxlength="10"
                                pattern="\d{10}" title="Please enter a 10-digit phone number"
                                placeholder="Enter phone number" required>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction:column;">
                        <label for="signup-password">Password</label>
                        <div class="passwordField">
                            <input type="password" id="signup-password" name="password" placeholder="Password"
                                autocomplete="off" required>
                            <i class='bx bxs-show' onclick="showAndHidePswd('signup-password')"></i>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction:column;">
                        <label for="signup-password">Confirm Password</label>
                        <div class="passwordField CnfrmPswdField">
                            <input type="password" id="cnfrmPswd" name="password_confirmation"
                                placeholder="Confirm Password" autocomplete="off" oninput="validatePassword()"
                                required>
                            <i class='bx bxs-show' onclick="showAndHidePswd('cnfrmPswd')"></i>
                        </div>
                    </div>
                    <div id="password-error-message" class="error-message" style="display: none;"></div>
                    <div class="registerbtn">
                        <button class="regbtn" id="regBtn">Register</button>
                    </div>

                    <div class="alreadyacc">
                        <a class="alraccount" onclick="redirectToLogin()">Already have an account?</a>
                    </div>
                </div>
            </div>
        </form>
        <span class="closee" onclick="hideSignupPopup()">&times;</span>
    </div>

    <!-- cart div -->
    <div id="cart" class="cart">
        <div class="cart_part1">
            <div class="cartname">
                <span class="cart_text">Your Cart</span>
                <span class="cart_clear"><a class="cart_textt">Clear Cart</a></span>

            </div>

            <div class="cartitems">

            </div>
            <div class="total" style="display: none;">
                <div class="main_total">
                    <div class="subtotal">
                        <span>Sub Total</span>
                        <span>Rs 0.00</span>
                    </div>
                    <div class="delichrge">
                        <span>Delivery Charges</span>
                        <span>Rs 0.00</span>
                    </div>
                    <div class="grandtotal">
                        <span>Grand Total</span>
                        <span>Rs 0.00</span>
                    </div>
                    <button class="checkoutbtn" onclick="checkOut()">Checkout</button>
                </div>

            </div>

        </div>
        <div class="clear" style="display: none;">
            <div class="clear_Cart_itens">
                <span style="color: #fff; font-size:1.5rem;">Are you sure?</span>
                <div class="clear_cart">
                    <button class="cancel">cancel</button>
                    <button class="clearbtn">Clear Cart</button>
                </div>
            </div>

        </div>
        <div class="emptycart">
            <span><img src="{{ asset('Images/OnlineOrdering/cart-empty.png') }}" alt=""></span>
            <span style="color: rgb(131, 143, 155);">Your Cart is Empty</span>
        </div>
    </div>
    <div class="msg">
        <div class="msg1">
            <div class="msg_img">
                <img src="{{ asset('Images/OnlineOrdering/addedcart.png') }}" alt="">
            </div>
            <div class="msg_text">
                <span>Item Added to Cart</span>
            </div>
        </div>
    </div>

    {{-- <div class="popupOverlay" id="popupOverlay" style="block"></div> --}}
    <div class="checkOutDiv" id="checkOutDiv">
        <form action="{{ route('addToCart') }}" method="POST" enctype="multipart/form-data"
            onsubmit="return checkPaymentMethod()">
            @csrf
            <div id="leftSide">
                <div id="data-row">
                    <div class="input-div">
                        <label for="userName">Full Name</label>
                        <input type="text" name="name" id="userName" placeholder="Enter your name" required>
                    </div>
                    <div class="input-div">
                        <label for="userPhone">Mobile Number</label>
                        <input type="tel" name="phone_number" id="userPhone" placeholder="+923000000000"
                            maxlength="13" pattern="\+923[0-9]{9}" required>
                    </div>
                    <div class="input-div">
                        <label for="userEmail">Email Address</label>
                        <input type="text" name="email" id="userEmail" placeholder="Enter you email address"
                            required readonly>
                    </div>
                    <div class="input-address-div">
                        <label for="userAddress">Your Address</label>
                        <textarea type="text" name="address" id="userAddress" placeholder="Enter street address" required></textarea>
                    </div>
                </div>
                <div id="payment-row">
                    <p>Select Payment Method</p>

                    <div class="payment-methods">
                        <!-- Cash On Delivery -->
                        <label for="COD" class="payment-option" onclick="selectPaymentOption(this)">
                            <input type="radio" class="payment" id="COD" name="paymentMethod"
                                value="Cash On Delivery">
                            <span class="custom-radio"></span>
                            <img src="{{ asset('Images/cash-on-delivery.png') }}" alt="Cash On Delivery">
                            Cash On Delivery
                        </label>

                        <!-- Credit Card -->
                        <label for="CreditCard" class="payment-option" onclick="selectPaymentOption(this)">
                            <input type="radio" class="payment" id="CreditCard" name="paymentMethod"
                                value="Credit Card">
                            <span class="custom-radio"></span>
                            <img src="{{ asset('Images/atm-card.png') }}" alt="Credit Card">
                            Credit Card
                        </label>

                        <!-- PayPal -->
                        {{-- <label for="PayPal" class="payment-option">
                            <input type="radio" class="payment" id="PayPal" name="paymentMethod" value="PayPal">
                            <span class="custom-radio"></span>
                            <img src="{{ asset('Images/paypal.png') }}" alt="PayPal">
                            PayPal
                        </label> --}}
                    </div>

                </div>
            </div>

            <div id="rightSide">
                <h4>Your Cart</h4>
                <div id="center-div">
                    {{-- <div id="carted-item-div">
                        <input type="hidden" name="carteditem1" id="carteditem1">
                        <div id="item-img">
                            <img src="{{ asset('Images/3293465.jpg') }}" alt="">
                        </div>
                        <div id="item-data">
                            <h5>Product Name</h5>
                            <p>Product Variation</p>
                            <p>Product Topping</p>
                            <div id="quantity-price">
                                <span class="quantity-bdr">19</span> <span>Rs. 12300.00</span>
                            </div>
                        </div>
                    </div>
                    <div id="carted-item-div">
                        <input type="hidden" name="carteditem1" id="carteditem1">
                        <div id="item-img">
                            <img src="{{ asset('Images/3293465.jpg') }}" alt="">
                        </div>
                        <div id="item-data">
                            <h5>Product Name</h5>
                            <p>Product Variation</p>
                            <p>Product Topping</p>
                            <div id="quantity-price">
                                <span class="quantity-bdr">19</span> <span>Rs. 12300.00</span>
                            </div>
                        </div>
                    </div>
                    <div id="carted-item-div">
                        <input type="hidden" name="carteditem1" id="carteditem1">
                        <div id="item-img">
                            <img src="{{ asset('Images/3293465.jpg') }}" alt="">
                        </div>
                        <div id="item-data">
                            <h5>Product Name</h5>
                            <p>Product Variation</p>
                            <p>Product Topping</p>
                            <div id="quantity-price">
                                <span class="quantity-bdr">19</span> <span>Rs. 12300.00</span>
                            </div>
                        </div>
                    </div>
                    <div id="carted-item-div">
                        <input type="hidden" name="carteditem1" id="carteditem1">
                        <div id="item-img">
                            <img src="{{ asset('Images/3293465.jpg') }}" alt="">
                        </div>
                        <div id="item-data">
                            <h5>Product Name</h5>
                            <p>Product Variation</p>
                            <p>Product Topping</p>
                            <div id="quantity-price">
                                <span class="quantity-bdr">19</span> <span>Rs. 12300.00</span>
                            </div>
                        </div>
                    </div>
                    <div id="carted-item-div">
                        <input type="hidden" name="carteditem1" id="carteditem1">
                        <div id="item-img">
                            <img src="{{ asset('Images/3293465.jpg') }}" alt="">
                        </div>
                        <div id="item-data">
                            <h5>Product Name</h5>
                            <p>Product Variation</p>
                            <p>Product Topping</p>
                            <div id="quantity-price">
                                <span class="quantity-bdr">19</span> <span>Rs. 12300.00</span>
                            </div>
                        </div>
                    </div> --}}
                </div>
                <div class="total">
                    <div class="main_total">
                        <div class="subtotal">
                            <span>Subtotal</span>
                            <span id="subtotal-amount">Rs 0.00</span>
                            <input type="hidden" name="subTotal" id="subTotal">
                        </div>
                        <div class="delichrge">
                            <span>Delivery Charges</span>
                            <span id="delivery-charge">Rs 0.00</span>
                            <input type="hidden" name="deliveryCharge" id="deliveryCharge">
                        </div>
                        <div class="grandtotal">
                            <span>Garnd Total</span>
                            <span id="grand-total">Rs 0.00</span>
                            <input type="hidden" name="grandTotal" id="grandTotal">
                        </div>
                        <div class="form-btns">
                            <button type="button" class="closeBtn" onclick="closeCheckOut()">Close</button>
                            <button type="submit" class="checkOutBtn">Proceed</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Profile Update --}}
    <div id="profilePopup">
        <h2>Profile</h2>
        <form id="profileFileds" action="{{ route('updateCustomerProfile') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="customer_id" name="customer_id">
            <label for="edit_name">Full Name</label>
            <input name="name" id="edit_name" type="text" maxlength="25" required>
            <label for="edit_email">Email</label>
            <input name="email" id="edit_email" type="email" readonly>
            <label for="edit_country_code">Phone Number</label>
            <div class="profilePhone">
                <input name="phonePrefix" id="edit_country_code" class="phoneCode" type="text" readonly>
                <input name="phone_number" id="edit_phone_number" class="phoneNum" type="text" required>
            </div>
            <div class="Profilebtn">
                <button type="button" onclick="closeProfilePopup()" class="profileCloseBtn">Close</button>
                <button type="submit" class="profileUpdateBtn">Update</button>
            </div>
        </form>
        <div class="deleteProfile">
            <a id="deleteCustomerProfile" onclick="confirmationDelete()">Delete Account</a>
        </div>
    </div>

    <div class="popupOverlay" id="popupOverlay"></div>
    <div id="alert">
        <i class='bx bxs-error'></i>
        <p id="alert-message">No alert message to show.</p>
        <i class='bx bx-x' onclick="closeAlert()"></i>

    </div>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="{{ asset('JavaScript/frontend.js') }}"></script>
    <script src="{{ asset('JavaScript/location.js') }}"></script>
    <script src="{{ asset('JavaScript/final.js') }}"></script>


</body>

</html>
