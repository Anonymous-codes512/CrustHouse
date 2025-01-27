<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crust-House</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="{{ asset('Images/icon-512.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('CSS/OnlineOrdering/main.css') }}" class="css">
    <link rel="stylesheet" href="{{ asset('CSS/OnlineOrdering/popups.css') }}" class="css">
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
            {{ session('Order_Place_Success') }}
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
        $taxes = $taxes;

        $addons = $AllProducts->whereIn('category_name', ['Addons', 'addons', 'Addon', 'addon']);

    @endphp

    <header>
        <div class="header-div left-header-div">
            <div class="location-img-div" onclick="ShowLocationPopup()">
                <img src="{{ asset('Images/OnlineOrdering/locationpin.png') }}" alt="">
            </div>
            <div class="location-div">
                <p>Deliver To</p>
                <p class="truncate-text-address" id="addr"></p>
            </div>
        </div>
        <div class="header-div central-header-div">
            <div class="central-logo-div">
                <img src="{{ asset('Images/OnlineOrdering/image-1.png') }}" alt="Online Ordering">
            </div>
        </div>
        <div class="header-div right-header-div">
            <div id="search_bar_div" class="search_bar_div">
                <input type="text" id="search_bar" name="search" placeholder="Search by product name..."
                    style="background-image: url('{{ asset('Images/search.png') }}');">
            </div>
            <div class="icons-div">
                <hr>
                <div class="header-menu-icon" id="search-menu-icon">
                    <img onclick="toggleSearchBar()" src="{{ asset('Images/OnlineOrdering/search.png') }}"
                        alt="Search Icon">
                    <span class="icon-text">Search</span>
                </div>
                <hr>

                <div id="profile-image-name" class="header-menu-icon profile-container">
                    <div id="profile-image-name" onclick="checkProfile(event)">
                        <img id="profileImg" src="{{ asset('Images/OnlineOrdering/profile.png') }}" alt="Profile Icon">
                    </div>
                    <span class="icon-text">Profile</span>
                    <div id="dropdownMenu" class="dropdownMenu-content">
                        <span id="username" class="truncate-text">Customer</span>
                        <a onclick="openProfilePopup('{{ route('deleteCustomer', ':customer_id') }}')">Profile</a>
                        <a onclick="logout()">Logout</a>
                    </div>
                </div>

                <hr>
                <div class="header-menu-icon" onclick="toggleCart()">
                    <img id="locimgg" src="{{ asset('Images/OnlineOrdering/cart.png') }}" alt="Cart Icon">
                    <span class="icon-text">Cart</span>
                </div>
                <hr>
            </div>

            <div class="toggle-menu-icon" onclick="toggleMenu()">
                <i id="toggleMenu" class='bx bx-menu'></i>
            </div>
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
        </div>
    </header>

    <main>
        <div id="categories-icon" onclick="toggleNav()"><i class='bx bx-menu-alt-left'></i></div>
        <nav id="categories">
            <div id="closeNav" onclick="toggleNav()"> <i class="bx bx-x"></i></div>
            @foreach ($Categories as $key => $Category)
                <a href="#section{{ $key + 1 }}">
                    <button>{{ $Category->categoryName }}</button>
                </a>
            @endforeach
            <a href="#section0">
                <button>Deals</button>
            </a>
        </nav>

        <div class="sections-container">
            @foreach ($Categories as $key => $Category)
                <section id="section{{ $key + 1 }}" class="section">
                    <h1>{{ $Category->categoryName }}</h1>
                    <div class="category-items">
                        @php
                            $CategoryProducts = $Products->where('category_id', $Category->id);
                            $displayedProductNames = [];
                        @endphp
                        @foreach ($CategoryProducts as $Product)
                            @if (!in_array($Product->productName, $displayedProductNames))
                                @php
                                    $displayedProductNames[] = $Product->productName;
                                @endphp
                                <div class="card">
                                    <div class="card-img"
                                        onclick="addToCart({{ json_encode($Product) }}, {{ json_encode($CategoryProducts) }}, {{ json_encode($addons) }})">
                                        <img src="{{ asset('Images/ProductImages/' . $Product->productImage) }}"
                                            alt="Product">
                                    </div>

                                    <div class="card-info">
                                        <p class="product_name truncate-text">{{ $Product->productName }}</p>
                                        <div class="space"></div>
                                        <div class="card-control">
                                            <div>
                                                <p class="product_price">Rs. {{ $Product->productPrice }}</p>
                                            </div>
                                            <div class="card-btn">
                                                <button class="cart-btn"
                                                    onclick="addToCart({{ json_encode($Product) }}, {{ json_encode($CategoryProducts) }}, {{ json_encode($addons) }})">Add
                                                    To Cart</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endforeach

            <div id="section0" class="section">
                @php
                    $displayedDealTitles = [];
                @endphp
                <h1>Deals</h1>
                <div class="category-items">
                    @foreach ($Deals as $Deal)
                        @if (!in_array($Deal->deal->dealTitle, $displayedDealTitles))
                            @php
                                $displayedDealTitles[] = $Deal->deal->dealTitle;
                            @endphp
                            @if ($Deal->deal->dealStatus != 'not active')
                                <div class="card">
                                    <div class="card-img"
                                        onclick="addDealToCart({{ json_encode($Deal) }}, {{ json_encode($Deals) }}, {{ json_encode($AllProducts) }})">
                                        <img src="{{ asset('Images/DealImages/' . $Deal->deal->dealImage) }}"
                                            alt="Deals">
                                    </div>
                                    <div class="card-info">
                                        <p class="product_name truncate-text">{{ $Deal->deal->dealTitle }}</p>
                                        <div class="space"></div>
                                        <div class="card-control">
                                            <div>
                                                <p class="product_price">Rs.
                                                    {{ $Deal->deal->dealDiscountedPrice }}</p>
                                            </div>
                                            <div class="card-btn">
                                                <button class="cart-btn"
                                                    onclick="addDealToCart({{ json_encode($Deal) }}, {{ json_encode($Deals) }}, {{ json_encode($AllProducts) }})">
                                                    Add #sectionTo Cart </button>
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

    </main>

    <footer>
        <div class="upper-footer-div">
            <div class="upper-left-div">
                <div class="footer-img-div">
                    <img src="{{ asset('Images/OnlineOrdering/logo-2.png') }}" alt="Logo">
                </div>
                <div class="contact-div">
                    <h3>Contact Us</h3>
                    <div class="phone-number-div">
                        <i class='bx bxs-phone'></i>
                        <span>03003336669</span>
                    </div>
                    <div class="email-address-div">
                        <i class='bx bxs-envelope'></i>
                        <span>support@crusthouse.com.pk</span>
                    </div>
                </div>
            </div>
            <div class="upper-right-div">
                <div class="timing-div">
                    <h4>Our Timing:</h4>
                    <ul>
                        <li><span>Monday - Thursday</span><span>11:00 AM - 03:00 AM</span></li>
                        <li><span>Friday</span><span>02:00 PM - 03:00 AM</span></li>
                        <li><span>Saturday - Sunday</span><span>11:00 AM - 03:00 AM</span></li>
                    </ul>
                </div>
                <div class="social-media-div">
                    <h4>Follow us:</h4>
                    <div class="social-icons">
                        <a href="#"><img src="{{ asset('/Images/OnlineOrdering/facebook.png') }}"
                                alt="Facebook"></a>
                        <a href="#"><img src="{{ asset('/Images/OnlineOrdering/instagram.png') }}"
                                alt="Instagram"></a>
                    </div>
                </div>
            </div>

        </div>
        <div class="below-footer-div">
            <p>&copy; 2024 CrustHouse. All rights reserved.
                <a href="https://tachyontechs.com/" target="_blank"><img class="logo"
                        src="{{ asset('Images/OnlineOrdering/logo.png') }}" alt=""></a>
            </p>
        </div>
    </footer>

    <!-- popup coding starts from here -->

    <div class="popupOverlay" id="popupOverlay"></div>

    <!-- Location Popup -->
    <div class="selectLocationPopup" id="selectLocationPopup">
        <div class="location-container">
            <div class="logo-div">
                <img src="{{ asset('Images/OnlineOrdering/logo-2.png') }}" alt="">
            </div>

            <div class="location-center-div">
                <p>Please Select your Location</p>
                <button class="select-location-button" onclick="findMyLocation()">
                    <i class='bx bx-current-location'></i>
                    <span>Use Current Location</span>
                </button>
            </div>

            <div class="location-input-fields">
                <input type="text" name="location-district" id="district" placeholder="Select City / Region "
                    required readonly>
                <input type="text" name="location-address" id="address" placeholder="Select Area / Sub Region"
                    required readonly>
                <div id="location-message" class="error-message" style="display: none;"></div>
                <button id="submit-btn" class="select-btn" onclick="SelectLocation()">Select</button>
            </div>
        </div>
    </div>

    <!-- Cart Popup -->
    <div class="cartPopupOverlay" id="cartPopupOverlay">
        <div class="cart_part1">
            <div class="cart-name">
                <span class="cart_text">Your Cart</span>
                <span class="cart_clear" id="clear-cart"><a onclick="clearCart()">Clear Cart</a></span>

            </div>

            <div class="cart-container" id="cart-container">
                <div class="cart-item">

                </div>

            </div>

            <div class="payment-detail-container">
                <div class="subtotal payment-div">
                    <span>Sub Total</span>
                    <span>Rs 0.00</span>
                </div>
                <div class="delichrge payment-div">
                    <span>Delivery Charges</span>
                    <span>Rs 0.00</span>
                </div>
                <div class="grandtotal payment-div">
                    <span>Grand Total</span>
                    <span>Rs 0.00</span>
                </div>
                <button class="checkout-btn" onclick="checkOut()">Checkout</button>
            </div>
        </div>

        <div class="clear-cart-container" id="clear-cart-container">
            <span>Are you sure?</span>
            <div class="clear_cart">
                <button class="cancel" onclick="closeClearCart()">cancel</button>
                <button class="clear-btn" onclick="confirmClear()">Clear Cart</button>
            </div>
        </div>

        <div class="empty-cart" id="empty-cart">
            <span><img src="{{ asset('Images/OnlineOrdering/cart-empty.png') }}" alt=""></span>
            <span style="color: rgb(131, 143, 155);">Your Cart is Empty</span>
        </div>
    </div>

    <!-- login popup -->
    <div id="loginPopup" class="loginPopup">
        <h2>Login</h2>
        <form onsubmit="event.preventDefault(); loginUser('{{ route('customerLogin') }}');"
            enctype="multipart/form-data">
            @csrf

            <span class="instruction">Please enter email and confirm your country code and enter your mobile
                number</span>

            <div class="email-div">
                <input type="email" id="loginEmail" name="email" required placeholder="Enter your email">
            </div>

            <div class="passwordField">
                <input type="password" id="password" name="password" placeholder="Password" autocomplete="off"
                    required>
                <i class='bx bxs-show' onclick="showAndHidePswd('password')"></i>
            </div>

            <div id="login-response-message" class="error-message" style="display: none;"></div>

            <div class="privacy-policy">This site is protected by reCAPTCHA and the Google
                <a href="https://policies.google.com/privacy" target="_blank">Privacy Policy</a>
                and
                <a href="https://policies.google.com/terms" target="_blank">Terms of Service</a>
                apply.
            </div>

            <div class="login-popup-btn">
                <button type="submit" class="login-btn">Login</button>
                <a id="forgotPassword" class="forgot-password" href="{{ route('customerForgotPassword') }}">Forgot
                    Password?</a>

                <div class="option-div">
                    <span>OR</span>
                </div>

                <button class="sign-up-btn" onclick="showSignupPopup()">Sign-up</button>
                <button class="close-login-popup-btn" onclick="hideLoginPopup()">Close</button>
                
                <div class="option-div">
                    <span>OR</span>
                </div>

                <a id="guest-account" class="guest-account" onclick="guestAccount()">Continue as a Guest</a>
            </div>

        </form>
    </div>

    <!-- signup popup -->
    <div id="signUpPopup" class="signUpPopup">
        <h2>Register</h2>
        <form action="{{ route('customerSignup') }}" method="POST" enctype="multipart/form-data"
            onsubmit="return updateLoginStatus()">
            @csrf

            <div class="input-div">
                <label>Enter Name</label>
                <input type="text" id="name" name="name" maxlength="25" placeholder="Enter your name"
                    required>
            </div>

            <div class="input-div">
                <label>Enter Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email"
                    oninput="validateEmail()" required>
                <div id="email-error-message" class="error-message" style="display: none;"></div>
            </div>

            <div class="input-div">
                <label>Mobile Number</label>
                <div class="phone-number-div">
                    <input id="countryCode" type="tel" name="phonePrefix" value="+92" max="3"
                        readonly>
                    <input id="phoneNumber" type="tel" name="phone_number" maxlength="10" pattern="\d{10}"
                        title="Please enter a 10-digit phone number" placeholder="Enter phone number" required>
                </div>
            </div>

            <div class="input-div">
                <label for="signup-password">Password</label>
                <div class="passwordField" id="passwordField">
                    <input type="password" id="signup-password" name="password" placeholder="Password"
                        autocomplete="off" required>
                    <i class='bx bxs-show' onclick="showAndHidePswd('signup-password')"></i>
                </div>
            </div>

            <div class="input-div">
                <label for="signup-password">Confirm Password</label>
                <div class="passwordField CnfrmPswdField" id="passwordField">
                    <input type="password" id="cnfrmPswd" name="password_confirmation"
                        placeholder="Confirm Password" autocomplete="off" oninput="validatePassword()" required>
                    <i class='bx bxs-show' onclick="showAndHidePswd('cnfrmPswd')"></i>
                </div>
            </div>

            <div id="password-error-message" class="error-message" style="display: none;"></div>

            <div class="signUp-popup-btn">
                <button id="reg-btn">Register</button>
                <a class="login-link" onclick="redirectToLogin()">Already have an account?</a>
                <button type="button" class="close-signUp-popup" onclick="hideSignupPopup()">Close</button>
            </div>
            <div class="option-div">
                <span>OR</span>
            </div>

            <a id="guest-account" class="guest-account" onclick="guestAccount()">Continue as a Guest</a>

        </form>
    </div>

    <!-- Deal Customization Popup -->
    <div class="productCustomizationPopup" id="dealPopup">

        <div class="image-title-div">
            <div class="img-div">
                <img class="popup-img" id="deal_popup-img" alt="Product Image">
            </div>
            <div class="title-div">
                <span class="popup_title" id="deal_popup-title"></span>
                <span class="popup_name" id="deal_popup-dealName"></span>
                <span class="popup_price" id="deal_popup-price"></span>
            </div>
        </div>

        <div class="customization-options-div">
            <div class="options">
                <div id="pizza-variation-dropdown" class="option" onclick="toggleDropdown(this)">
                    <span class="size">Pizza Variation</span>
                    <span class="required">Required</span>
                    <div class="dropdown">
                        <i class='bx bxs-chevron-down'></i>
                    </div>
                </div>

                <div class="dropdown-content">
                    <div class="dropdown_1">
                        <div>
                            <div class="dealDrop">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="option" id="topping-dropdown" onclick="toggleDropdown(this)">
                    <span class="size">Toppings</span>
                    <span id="deal-optional" class="required">Optional</span>
                    <div class="dropdown">
                        <i class='bx bxs-chevron-down'></i>
                    </div>
                </div>

                <div class="dropdown-content">
                    <div class="dropdown_1">
                        <div>
                            <div class="dealAddonDrop">

                            </div>
                        </div>
                    </div>
                </div>

                <div id="drink-dropdown" class="option" onclick="toggleDropdown(this)">
                    <span class="size">Drink Flavour</span>
                    <span class="required">Required</span>
                    <div class="dropdown">
                        <i class='bx bxs-chevron-down'></i>
                    </div>
                </div>

                <div class="dropdown-content">
                    <div class="dropdown_1">
                        <div>
                            <div class="dealDrinkDrop">

                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="bottom">
                <div class="bottom-left">
                    <button onclick="decreaseDealQuantity()" id="decrease">
                        <i class='bx bx-minus'></i>
                    </button>
                    <span class="count" id="quantity">1</span>

                    <button onclick="increaseDealQuantity()" id="increase">
                        <i class='bx bx-plus'></i>
                    </button>

                </div>
                <div class="bottom-right">
                    <button type="button" onclick="closeDealAddToCart()">Close</button>
                    <button id="deal-add-cart" onclick="handleDealCartButtonClick()">
                        <div style="margin-left: 0.75rem;">Add to Cart</div>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Product Customization Popup -->
    <div class="productCustomizationPopup" id="productPopup">

        <div class="image-title-div">
            <div class="img-div">
                <img class="popup-img" id="popup-img" alt="Product Image">
            </div>
            <div class="title-div">
                <span class="popup_title" id="popup-title"></span>
                <span class="popup_price" id="popup-price"></span>
            </div>
        </div>

        <div class="customization-options-div">
            <div class="options">
                <div class="option" onclick="toggleDropdown(this)">
                    <span class="size">Select Variation</span>
                    <span class="required">Required</span>
                    <div class="dropdown">
                        <i class='bx bxs-chevron-down'></i>
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

                <div class="option" id="top-d-d" onclick="toggleDropdown(this)">
                    <span class="size">Toppings</span>
                    <span id="pizza-optional" class="required">Optional</span>
                    <div class="dropdown">
                        <i class='bx bxs-chevron-down'></i>
                    </div>
                </div>

                <div id="dropdown-content1" class="dropdown-content">
                    <div class="dropdown_1">
                        <div>
                            <div class="toppingDrop">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bottom">
                <div class="bottom-left">
                    <button onclick="decreaseQuantity()" id="decrease">
                        <i class='bx bx-minus'></i>
                    </button>
                    <span class="count" id="Quantity">1</span>

                    <button onclick="increaseQuantity()" id="increase">
                        <i class='bx bx-plus'></i>
                    </button>

                </div>
                <div class="bottom-right">
                    <button type="button" onclick="closeAddToCart()">Close</button>
                    <button onclick="handleCartButtonClick()">
                        <div>
                            <span id="originalprice" style="display: none;"></span>
                            <span id="cart-price"></span>
                        </div>
                        &nbsp;&nbsp; <span>Add to Cart</span>
                    </button>
                </div>
            </div>
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

    <!-- Profile update popup -->
    <div id="profilePopup">
        <h2>Profile</h2>
        <form id="profileFields" action="{{ route('updateCustomerProfile') }}" method="POST"
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
            <div class="Profile-btn">
                <button type="button" onclick="closeProfilePopup()" class="profileCloseBtn">Close</button>
                <button type="submit" class="profileUpdateBtn">Update</button>
            </div>
        </form>
        <div class="deleteProfile">
            <a id="deleteCustomerProfile" onclick="confirmationDelete()">Delete Account</a>
        </div>
    </div>

    <!-- Confirm delete popup -->
    <div class="confirmDeletion" id="confirmDeletion">
        <h3 id="message-text">Are you sure you want to delete this Branch</h3>
        <div class="confirm-delete-fields">
            <label style="margin-bottom:1rem;" for="formRandomString">Enter this Code:
                <span
                    style="font-family:consolas; background-color:#000; padding:5px; color:#fff;border-radius:0.25rem; font-size:1rem; letter-spacing:5px;"
                    id="rndom"></span>
            </label>
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

    <!-- Cheak out popup -->
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
                            required>
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
                        <label for="COD" class="payment-option"
                            onclick="selectPaymentOption(this, {{ json_encode($taxes) }})">
                            <input type="radio" class="payment" id="COD" name="paymentMethod"
                                value="Cash On Delivery">
                            <span class="custom-radio"></span>
                            <img src="{{ asset('Images/cash-on-delivery.png') }}" alt="Cash On Delivery">
                            Cash On Delivery
                        </label>

                        <!-- Credit Card -->
                        <label for="CreditCard" class="payment-option"
                            onclick="selectPaymentOption(this, {{ json_encode($taxes) }})">
                            <input type="radio" class="payment" id="CreditCard" name="paymentMethod"
                                value="Credit Card">
                            <span class="custom-radio"></span>
                            <img src="{{ asset('Images/atm-card.png') }}" alt="Credit Card">
                            Credit Card
                        </label>
                    </div>

                </div>
            </div>

            <div id="rightSide">
                <h4>Your Cart</h4>
                <div id="center-div">

                </div>

                <div class="payment-detail-container">
                    <div class="subtotal payment-div">
                        <span>Sub Total</span>
                        <span id="subtotal-amount">Rs 0.00</span>
                        <input type="hidden" name="subTotal" id="subTotal">

                    </div>
                    <div class="delichrge payment-div">
                        <span>Delivery Charges</span>
                        <span id="delivery-charge">Rs 0.00</span>
                        <input type="hidden" name="deliveryCharge" id="deliveryCharge">
                    </div>
                    <div class="tax payment-div">
                        <span>Tax</span>
                        <span id="tax-amount">0%</span>
                        <input type="hidden" name="taxAmount" id="taxAmount" value="0">
                    </div>
                    <div class="grandtotal payment-div">
                        <span>Grand Total</span>
                        <span id="grand-total">Rs 0.00</span>
                        <input type="hidden" name="grandTotal" id="grandTotal">
                    </div>
                    <div class="form-btns">
                        <button type="button" class="closeBtn" onclick="closeCheckOutDivPopup()">Close</button>
                        <button type="submit" class="checkOutBtn">Proceed</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Cheak out popup -->
    <div id="alert">
        <i class='bx bxs-error'></i>
        <p id="alert-message">No alert message to show.</p>
        <i class='bx bx-x' onclick="closeAlert()"></i>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="{{ asset('JavaScript/frontend.js') }}"></script>
    <script src="{{ asset('JavaScript/location.js') }}"></script>
    <script src="{{ asset('JavaScript/final.js') }}"></script>
</body>

<script>
    function toggleMenu() {
        let iconsDiv = document.querySelector('.icons-div');
        let menuIcon = document.getElementById('toggleMenu');
        if (iconsDiv.classList.contains('active')) {
            iconsDiv.classList.remove('active');
            menuIcon.classList.remove('bx-x');
            menuIcon.classList.add('bx-menu');
        } else {
            menuIcon.classList.remove('bx-menu');
            menuIcon.classList.add('bx-x');
            iconsDiv.classList.add('active');
        }
    }

    function toggleNav() {
        const nav = document.getElementById('categories');
        nav.classList.toggle('active'); // Toggle the active class
    }

    const navLinks = document.querySelectorAll('nav a button');

    navLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const sectionId = this.parentElement.getAttribute('href');
            const section = document.querySelector(sectionId);
            const navHeight = document.querySelector('nav').offsetHeight;
            window.scrollTo({
                top: section.offsetTop - navHeight,
                behavior: 'smooth'
            });
            if (window.innerWidth < 480) {
                toggleNav();
            }
        });
    });

    function adjustSectionMargin() {
        const nav = document.querySelector('nav');
        const sectionsContainer = document.querySelector('.sections-container');
        const navHeight = nav.offsetHeight;

        sectionsContainer.style.marginTop = `${navHeight}px`;
    }

    window.onload = adjustSectionMargin;
    window.onresize = adjustSectionMargin;

    document.addEventListener("DOMContentLoaded", function() {
        const overlay = document.getElementById("popupOverlay");
        const cart = document.getElementById("cartPopupOverlay");

        if (overlay && cart) {
            overlay.addEventListener("click", () => {
                const cartDisplay = window.getComputedStyle(cart).display;
                if (cartDisplay === 'flex') {
                    cart.style.display = 'none';
                    overlay.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        } else {
            console.error("Overlay or Cart element not found.");
        }
    });
</script>

</html>
