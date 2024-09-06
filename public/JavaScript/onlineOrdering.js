let RegisteredCustomer = {
    name: null,
    "Phone Number": null,
    Email: null,
};

function addToCart(product, allProducts, addons) {
    const productArray = Object.values(allProducts);
    let productVariations = [];
    productArray.forEach((element) => {
        if (element.productName === product.productName) {
            productVariations.push(element.productVariation);
        }
    });
    if (productVariations.length >= 2) {
        openProductPopup(product, allProducts, addons);
    } else if (productVariations.length === 1) {
        handleVariationLessProduct(product);
    } else {
        console.warn("Invalid Product");
    }
}

function handleVariationLessProduct(Product) {
    let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

    let existingCartItemIndex = -1;
    cartItems.forEach((item, index) => {
        if (item.name === Product.productName) {
            existingCartItemIndex = index;
        }
    });

    const cartItem = {
        name: Product.productName,
        type: "Variation less product",
        originalPrice: Product.productPrice,
        price: Product.productPrice,
        quantity: 1,
        imgSrc: `Images/ProductImages/${Product.productImage}`,
        variation: null,
        variationPrice: null,
        topping: null,
    };

    if (existingCartItemIndex !== -1) {
        cartItems[existingCartItemIndex].quantity += 1;
        cartItems[existingCartItemIndex].price =
            cartItems[existingCartItemIndex].price *
            cartItems[existingCartItemIndex].quantity;
    } else {
        cartItems.push(cartItem);
    }

    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    showMessage();
}

function openProductPopup(product, allProducts, addons) {
    const popup = document.getElementById("popup");
    const popupImg = document.getElementById("popup-img");
    const popupTitle = document.getElementById("popup-title");
    const popupPrice = document.getElementById("popup-price");
    const dropdownContainer = document.querySelector(".drop");
    const extra4Div = document.getElementById("extra_4");
    const overlay = document.getElementById("overlay");
    const productArray = Object.values(allProducts);
    const getAddons = Object.values(addons);

    let productPrices = [];
    let productVariations = [];
    let addOnPrices = [];

    dropdownContainer.innerHTML = "";

    productArray.forEach((element) => {
        if (element.productName === product.productName) {
            productPrices.push(element.productPrice);
            productVariations.push(element.productVariation);
        }
    });

    productVariations.forEach((variation, index) => {
        const dropdown2 = document.createElement("div");
        dropdown2.className = "dropdown_2";

        const radioDiv = document.createElement("div");
        const radioInput = document.createElement("input");
        radioInput.type = "radio";
        radioInput.name = "option";
        radioInput.value = productPrices[index];
        radioInput.id = `variation-${index}`;
        radioInput.onclick = () => {
            document
                .querySelectorAll('input[name="addon"]')
                .forEach((checkbox) => {
                    checkbox.checked = false;
                });
            updatePrice(radioInput);

            filterAddons(variation); // Filter addons based on selected variation only if checked
        };
        if (index === 0) {
            updatePrice(radioInput);
            // Do not filter addons here to delay the update until user interacts
        }
        radioDiv.appendChild(radioInput);
        dropdown2.appendChild(radioDiv);

        const dropdown3 = document.createElement("div");
        dropdown3.className = "dropdown_3";

        const sizeSpan = document.createElement("span");
        sizeSpan.className = "sizee";
        sizeSpan.innerText = variation;

        const priceSpan = document.createElement("span");
        priceSpan.className = "rs";
        priceSpan.innerText = `Rs. ${productPrices[index]}`;

        dropdown3.appendChild(sizeSpan);
        dropdown3.appendChild(priceSpan);
        dropdown2.appendChild(dropdown3);

        dropdownContainer.appendChild(dropdown2);
    });
    const toppingShow = document.getElementById("extra_4");
    const addonsSection = document.getElementById("dropdown-content1");
    const addonsContainer = addonsSection.querySelector(".drop");

    // Function to filter addons based on selected variation
    function filterAddons(selectedVariation) {
        addonsContainer.innerHTML = ""; // Clear previous addons options

        getAddons.forEach((addon) => {
            // Check if the addon’s variations array includes the selected variation
            if (
                addon.productVariation &&
                Array.isArray(addon.productVariation)
            ) {
                if (addon.productVariation.includes(selectedVariation)) {
                    addAddonToContainer(addon);
                }
            } else if (addon.productVariation === selectedVariation) {
                addAddonToContainer(addon);
            }
        });
    }

    // Function to add addon to container
    function addAddonToContainer(addon) {
        const dropdown2 = document.createElement("div");
        dropdown2.className = "dropdown_2";

        const radioDiv = document.createElement("div");
        const radioInput = document.createElement("input");
        radioInput.type = "radio";
        radioInput.name = "addon";
        radioInput.value = addon.productPrice; // Assuming addons have prices
        radioInput.onclick = () => toppingPrice(radioInput); // Function to update price with addons

        radioDiv.appendChild(radioInput);
        dropdown2.appendChild(radioDiv);

        const dropdown3 = document.createElement("div");
        dropdown3.className = "dropdown_3";

        const addonSpan = document.createElement("span");
        addonSpan.className = "sizee";
        addonSpan.innerText = addon.productName + " " + addon.productVariation;

        const addonPriceSpan = document.createElement("span");
        addonPriceSpan.className = "rs";
        addonPriceSpan.innerText = `Rs. ${addon.productPrice}`;

        dropdown3.appendChild(addonSpan);
        dropdown3.appendChild(addonPriceSpan);
        dropdown2.appendChild(dropdown3);
        addonsContainer.appendChild(dropdown2);
    }

    if (product.category_name === "Pizza") {
        toppingShow.style.display = "block";
        // Initialize addons based on the first variation when the user interacts
    } else {
        toppingShow.style.display = "none"; // Hide addons section if no addons are available
    }

    popupImg.src = `Images/ProductImages/${product.productImage}`;
    popupTitle.innerText = product.productName;
    popupPrice.innerText = productPrices[0];
    document.getElementById("originalprice").textContent = product.productPrice;
    document.getElementById(
        "cart-price"
    ).textContent = `Rs. ${product.productPrice}`;

    overlay.style.display = "block";
    popup.style.display = "block";
    document.body.classList.add("no-scroll");
    document.body.style.overflow = "hidden";

    // document.getElementById("closeButton").style.display = "block";
    document.querySelector(".popwhole").style.pointerEvents = "none";
}

document.addEventListener("DOMContentLoaded", function() {
    const overlay = document.getElementById("overlay");
    if (overlay) {
        overlay.addEventListener("click", () => {
            const cart = document.getElementById("cart");
            if (cart.classList.contains("active")) {
                cart.classList.remove("active");
                document.body.classList.remove("no-scroll");
                document.querySelector(".whole").style.pointerEvents = "auto";
                // document.querySelector(".whole").style.filter = "initial";
                document.body.style.overflow = "initial";
                overlay.style.display = "none";
            }
            document.body.classList.add("no-scroll");
        });
    } else {
        console.error("Overlay element not found.");
    }
});

function showLoginPopup() {
    const cart = document.getElementById("cart");
    cart.classList.remove("active");
    document.querySelector(".whole").style.pointerEvents = "auto";

    document.getElementById("overlay").style.display = "block";
    document.getElementById("logincomponent").style.display = "flex";
    toggleClass(".whole", "active");
    document.querySelector(".temp").style.display = "block";
    document.querySelector(".temp").style.pointerEvents = "none";
    document.body.style.overflow = "hidden";
}

function hideLoginPopup() {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("logincomponent").style.display = "none";
    toggleClass(".whole", "active");
    document.querySelector(".temp").style.pointerEvents = "auto";
    document.body.style.overflow = "initial";
}

function showSignupPopup() {
    const cart = document.getElementById("cart");
    cart.classList.remove("active");
    document.querySelector(".whole").style.pointerEvents = "auto";
    toggleClass(".whole", "active");
    document.getElementById("overlay").style.display = "block";
    document.getElementById("signupcomponent").style.display = "flex";
    document.getElementById("logincomponent").style.display = "none";
    document.body.classList.add("no-scroll");
    document.querySelector(".temp").style.display = "block";
    document.querySelector(".temp").style.pointerEvents = "none";
    document.body.style.overflow = "hidden";
}

function hideSignupPopup() {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("signupcomponent").style.display = "none";
    toggleClass(".whole", "active");
    document.querySelector(".temp").style.pointerEvents = "auto";
    document.body.classList.remove("no-scroll");
}

function redirectToLogin() {
    document.getElementById("signupcomponent").style.display = "none";
    document.getElementById("logincomponent").style.display = "flex";
    document.body.classList.add("no-scroll");
}

function handleCartButtonClick() {
    const title = document.getElementById("popup-title").innerText;
    const imageSrc = document.getElementById("popup-img").src;
    const price = parseInt(
        document
        .getElementById("cart-price")
        .innerText.replace("Rs. ", "")
        .replace(",", "")
    );
    const Originalprice = parseInt(
        document
        .getElementById("originalprice")
        .innerText.replace("Rs. ", "")
        .replace(",", "")
    );
    const quantity = parseInt(document.getElementById("quantity").innerText);
    const selectedOption = document.querySelector(
        'input[name="option"]:checked'
    );
    const selectedSizeElement = selectedOption
        .closest(".dropdown_2")
        .querySelector(".sizee");
    const selectedVariation = selectedSizeElement ?
        selectedSizeElement.innerText.trim() :
        "No variation selected";
    const variationPrice = parseInt(
        document
        .querySelector('input[name="option"]:checked')
        .getAttribute("value")
    );

    let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

    let existingCartItemIndex = -1;
    cartItems.forEach((item, index) => {
        if (item.name === title && item.variation === selectedVariation) {
            existingCartItemIndex = index;
        }
    });

    const cartItem = {
        name: title,
        originalPrice: Originalprice,
        price: price,
        quantity: quantity,
        imgSrc: imageSrc,
        variation: selectedVariation,
        variationPrice: variationPrice,
    };

    if (existingCartItemIndex !== -1) {
        cartItems[existingCartItemIndex].quantity += quantity;
        cartItems[existingCartItemIndex].price =
            cartItems[existingCartItemIndex].variationPrice *
            cartItems[existingCartItemIndex].quantity;
    } else {
        cartItems.push(cartItem);
    }

    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    showMessage();
    closeAddToCart();
    closeDealAddToCart();
}

function closeAddToCart() {
    document.querySelectorAll('input[name="option"]').forEach((radio) => {
        radio.checked = false;
    });

    // Reset all topping checkboxes
    document.querySelectorAll('input[name="addon"]').forEach((checkbox) => {
        checkbox.checked = false;
    });

    // Reset labels to "Requires"
    document.querySelectorAll(".required").forEach((labelSpan) => {
        labelSpan.innerText = "Required";
        labelSpan.style.backgroundColor = "rgb(220, 53, 69)"; // Set to the original color
    });
    document.getElementById("overlay").style.display = "none";
    document.getElementById("popup").style.display = "none";
    document.querySelector(".popwhole").style.pointerEvents = "auto";
    document.body.style.overflow = "initial";
}

function showMessage() {
    const msgElement = document.querySelector(".msg");

    msgElement.classList.add("show");
    setTimeout(() => {
        msgElement.classList.remove("show");
        msgElement.classList.add("hide");
    }, 1000);

    setTimeout(() => {
        msgElement.classList.remove("hide");
    }, 1000);
}

function checkOut() {
    const LoginStatus = localStorage.getItem("LoginStatus");
    if (LoginStatus) {
        const parsedLoginStatus = JSON.parse(LoginStatus);
        if (
            (parsedLoginStatus.loginStatus === false &&
                parsedLoginStatus.signupStatus === false) ||
            parsedLoginStatus.loginStatus === null
        ) {
            showSignupPopup();
        } else if (
            (parsedLoginStatus.loginStatus === false &&
                parsedLoginStatus.signupStatus === true) ||
            parsedLoginStatus.loginStatus === null
        ) {
            showLoginPopup();
        } else if (
            (parsedLoginStatus.loginStatus === true &&
                parsedLoginStatus.signupStatus === true) ||
            parsedLoginStatus.loginStatus === null
        ) {
            showCheckOutPopup();
        }
    } else {
        showSignupPopup();
    }
}

function updateLoginStatus() {
    let email = document.getElementById("email").value;
    let Data = { loginStatus: false, signupStatus: true, email: email };
    localStorage.setItem("LoginStatus", JSON.stringify(Data));
    return true;
}

function validateEmail() {
    let email = document.getElementById("email").value.trim();
    let emailErrorMessage = document.getElementById("email-error-message");
    let submitBtn = document.getElementById("regBtn");

    if (!email.endsWith(".com")) {
        emailErrorMessage.style.display = "block";
        emailErrorMessage.textContent = "Email must end with '.com'.";
        submitBtn.disabled = true;
        submitBtn.classList.remove("regbtn");
        submitBtn.classList.add("disable-btn");
        return;
    }
    var invalidChars = /[\*\/=\-+]/;
    if (invalidChars.test(email)) {
        emailErrorMessage.style.display = "block";
        emailErrorMessage.textContent =
            "Email contains invalid characters like *, /, =.";
        submitBtn.style.color = "#fff";
        submitBtn.classList.remove("regbtn");
        submitBtn.classList.add("disable-btn");
        submitBtn.disabled = true;
        return;
    }

    submitBtn.classList.add("regbtn");
    submitBtn.classList.remove("disable-btn");
    emailErrorMessage.style.display = "none";
    submitBtn.disabled = false;
}

function validatePassword() {
    let password = document.getElementById("signup-password").value;
    let confirmPassword = document.getElementById("cnfrmPswd").value;
    let message = document.getElementById("password-error-message");

    if (password.length < 8) {
        message.textContent = "Password must be at least 8 characters long!";
        message.className = "error-message";
        message.style.display = "block";
    } else if (password !== confirmPassword) {
        message.textContent = "Passwords do not match!";
        message.className = "error-message";
        message.style.display = "block";
    } else {
        message.textContent = "Passwords match!";
        message.className = "success-message";
        setTimeout(() => {
            message.style.display = "none";
        }, 1000);
    }
}

function showAndHidePswd(password_field_id) {
    let pswd = document.getElementById(password_field_id);
    if (pswd.type === "password") {
        pswd.type = "text";
    } else {
        pswd.type = "password";
    }
}

function showCheckOutPopup() {
    updateCartAndTotals();
    const cart = document.getElementById("cart");
    cart.classList.remove("active");
    document.querySelector(".whole").style.pointerEvents = "auto";

    document.getElementById("overlay").style.display = "block";
    document.getElementById("checkOutDiv").style.display = "flex";

    document.body.classList.add("no-scroll");
    document.body.style.overflow = "hidden";
    toggleClass(".whole", "active");
    document.querySelector(".temp").style.display = "block";
    document.querySelector(".temp").style.pointerEvents = "none";
    document.body.style.overflow = "hidden";
}

function closeCheckOut() {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("checkOutDiv").style.display = "none";

    document.body.classList.remove("no-scroll");
    document.body.style.overflow = "initial";
    toggleClass(".whole", "active");
    document.querySelector(".temp").style.display = "block";
    document.querySelector(".temp").style.pointerEvents = "initial";
}

function updateCartAndTotals() {
    document.getElementById("userName").value = RegisteredCustomer["name"];
    document.getElementById("userPhone").value =
        RegisteredCustomer["Phone Number"];
    console.log(document.getElementById("userPhone").value);
    console.log(RegisteredCustomer["Phone Number"]);

    document.getElementById("userEmail").value = RegisteredCustomer["Email"];

    const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
    const centerDiv = document.getElementById("center-div");
    centerDiv.innerHTML = "";

    let subTotal = 0;
    let deliveryCharge = 0;
    cartItems.forEach((item, key) => {
                subTotal += parseInt(item.price);
                console.log(item.price);
                console.log(subTotal);

                const cartedItemDiv = document.createElement("div");
                cartedItemDiv.id = "carted-item-div";

                // Handling toppings
                let toppingsHTML = "";
                if (item.topping && item.topping.length > 0) {
                    toppingsHTML = item.topping.map((topping) => `<p>${topping.name}`);
                }

                cartedItemDiv.innerHTML = `
        <input type="hidden" name="cartedItem${key}" id="cartedItem${key}" value='${JSON.stringify(
            item
        )}'>
        <div id="item-img">
            <img src="${item.imgSrc}" alt="${item.name}">
        </div>
        <div id="item-data">
            <h5>${item.name}</h5>
            ${item.variation ? `<p>${item.variation}</p>` : ""}
            ${toppingsHTML}
            <div id="quantity-price">
                <span class="quantity-bdr">${item.quantity}</span> 
                <span>Rs. ${item.price.toLocaleString()}</span>
            </div>
        </div>
        `;

        centerDiv.appendChild(cartedItemDiv);
    });

    const grandTotal = subTotal + deliveryCharge;
    const subtotalElement = document.getElementById("subtotal-amount");
    const deliveryChargeElement = document.getElementById("delivery-charge");
    const grandTotalElement = document.getElementById("grand-total");

    document.getElementById("subTotal").value = subTotal;
    document.getElementById("deliveryCharge").value = deliveryCharge;
    document.getElementById("grandTotal").value = grandTotal;

    if (subtotalElement) {
        subtotalElement.textContent = `Rs ${subTotal.toLocaleString()}`;
    }
    if (deliveryChargeElement) {
        deliveryChargeElement.textContent = `Rs ${deliveryCharge.toLocaleString()}`;
    }
    if (grandTotalElement) {
        grandTotalElement.textContent = `Rs ${grandTotal.toLocaleString()}`;
    }
}

function selectPaymentOption(element) {
    var paymentOptions = document.querySelectorAll(".payment-option");
    paymentOptions.forEach(function (option) {
        option.classList.remove("active");
    });
    element.classList.add("active");
    element.querySelector('input[type="radio"]').checked = true;
}

document.addEventListener("DOMContentLoaded", async () => {
    let loginData = localStorage.getItem("LoginStatus");
    if (loginData) {
        loginData = JSON.parse(loginData);
        await fetch("/registeredCustomer", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) => {
                let userFound = false;

                data.forEach((user) => {
                    if (user.email === loginData.email) {
                        RegisteredCustomer["name"] = user.name;
                        RegisteredCustomer["Phone Number"] = user.phone_number;
                        RegisteredCustomer["Email"] = user.email;
                        userFound = true;
                    }
                });
                if (!userFound) {
                    const updatedLoginData = {
                        loginStatus: loginData.loginStatus,
                        signupStatus: false,
                        email: loginData.email,
                        LoginTime: null,
                    };
                    localStorage.setItem(
                        "LoginStatus",
                        JSON.stringify(updatedLoginData)
                    );
                    console.log("Email Not Verified");
                }
            })
            .catch((error) => console.error("Error:", error));

        if (loginData.loginStatus == true) {
            document.getElementById("username").textContent =
                RegisteredCustomer["name"];
            document.getElementById("username").style.width = "90px";
        }
    } else {
        console.error("LoginStatus is not available in localStorage.");
    }
});

function checkPaymentMethod() {
    const paymentMethods = document.querySelectorAll(
        'input[name="paymentMethod"]'
    );
    let selected = false;
    paymentMethods.forEach((method) => {
        if (method.checked) {
            selected = true;
        }
    });
    if (!selected) {
        alert("Please select a payment method.");
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}

function loginUser(route) {
    const email = document.getElementById("loginEmail").value;
    const password = document.getElementById("password").value;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    let loginData = localStorage.getItem("LoginStatus");
    let cartedItems = localStorage.getItem("cartItems");

    loginData = JSON.parse(loginData);
    fetch(route, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({
            email: email,
            password: password,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                hideLoginPopup();
                let login_time = new Date().toISOString();
                const updatedLoginData = {
                    loginStatus: true,
                    signupStatus: true,
                    email: data.user.email,
                    LoginTime: login_time,
                };
                localStorage.setItem(
                    "LoginStatus",
                    JSON.stringify(updatedLoginData)
                );
                RegisteredCustomer["name"] = data.user.name;
                RegisteredCustomer["Phone Number"] = data.user.phone_number;
                RegisteredCustomer["Email"] = data.user.email;
                document.getElementById("username").textContent =
                    data.user.name;
                if (cartedItems) {
                    showCheckOutPopup();
                }
            } else {
                let login_response_message = document.getElementById(
                    "login-response-message"
                );
                login_response_message.style.display = "block";
                login_response_message.textContent = data.message;

                setTimeout(() => {
                    login_response_message.style.display = "none";
                }, 50000);
            }
        })
        .catch((error) => {
            let login_response_message = document.getElementById(
                "login-response-message"
            );
            login_response_message.style.display = "block";
            login_response_message.textContent = error;

            setTimeout(() => {
                login_response_message.style.display = "none";
            }, 50000);
        });
}

function checkProfile(event) {
    const LoginStatus = localStorage.getItem("LoginStatus");
    const parsedLoginStatus = JSON.parse(LoginStatus);

    if (!parsedLoginStatus || parsedLoginStatus.loginStatus === false) {
        showLoginPopup();
    } else {
        var dropdown = document.getElementById("dropdownMenu");
        if (dropdown.style.display === "block") {
            dropdown.style.display = "none";
            document.removeEventListener("click", closeDropdownOnClickOutside);
        } else {
            dropdown.style.display = "block";
            document.addEventListener("click", closeDropdownOnClickOutside);
        }
    }

    event.stopPropagation();
}

function closeDropdownOnClickOutside(event) {
    var dropdown = document.getElementById("dropdownMenu");
    if (!dropdown.contains(event.target)) {
        dropdown.style.display = "none";
        document.removeEventListener("click", closeDropdownOnClickOutside);
    }
}

function logout() {
    localStorage.clear();
    var dropdown = document.getElementById("dropdownMenu");
    if (dropdown.style.display === "block") {
        dropdown.style.display = "none";
    } else {
        dropdown.style.display = "block";
    }
    window.location.reload();
}

let interval = 3600000;

function checkAndRemoveData() {
    let cartedItems = localStorage.getItem("cartItems");
    let LoginStatus = localStorage.getItem("LoginStatus");
    let savedLocation = localStorage.getItem("savedLocation");

    if (cartedItems || LoginStatus || savedLocation) {
        setTimeout(() => {
            localStorage.removeItem("cartItems");
            localStorage.removeItem("LoginStatus");
            localStorage.removeItem("savedLocation");
            window.location.reload();
        }, interval);
    } else {
        console.log("No data found.");
    }
}

document.addEventListener("DOMContentLoaded", function () {
    checkAndRemoveData();
    // checkIfSessionExpired();
});
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search_bar");
    const sections = Array.from(document.querySelectorAll(".content-section"));

    const debounce = (func, delay) => {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(null, args), delay);
        };
    };

    const filterItems = () => {
        const filterText = searchInput.value.toLowerCase();
        sections.forEach((section) => {
            const items = section.querySelectorAll(".imgbox");
            let shouldDisplaySection = false;

            items.forEach((item) => {
                const nameElement = item.querySelector(".product_name");
                const nameText = nameElement
                    ? nameElement.textContent.toLowerCase()
                    : "";
                const isVisible = nameText.includes(filterText);

                if (isVisible) {
                    shouldDisplaySection = true;
                    item.style.display = ""; // Reset to default
                } else {
                    item.style.display = "none"; // Hide item
                }
            });

            section.style.display = shouldDisplaySection ? "" : "none"; // Show or hide section
        });
    };

    searchInput.addEventListener("input", debounce(filterItems, 300));
});

function openProfilePopup(route) {
    getProfileData(route);
    const dropdown = document.getElementById("dropdownMenu");
    dropdown.style.display = "none";
    const cart = document.getElementById("cart");
    cart.classList.remove("active");
    document.querySelector(".whole").style.pointerEvents = "auto";

    document.getElementById("overlay").style.display = "block";
    document.getElementById("profilePopup").style.display = "flex";

    document.body.classList.add("no-scroll");
    document.body.style.overflow = "hidden";
    toggleClass(".whole", "active");
    document.querySelector(".temp").style.display = "block";
    document.querySelector(".temp").style.pointerEvents = "none";
    document.body.style.overflow = "hidden";
}

function closeProfilePopup() {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("profilePopup").style.display = "none";

    document.body.classList.remove("no-scroll");
    document.body.style.overflow = "initial";
    toggleClass(".whole", "active");
    document.querySelector(".temp").style.display = "block";
    document.querySelector(".temp").style.pointerEvents = "initial";
}

async function getProfileData(route) {
    const Email = JSON.parse(localStorage.getItem("LoginStatus"));
    await fetch(`/profile/${Email.email}`)
        .then((response) => response.json())
        .then((data) => {
            const phoneNumber = data.user.phone_number;
            // const countryCode = phoneNumber.slice(1, 3);
            const countryCode = "03";
            const number = phoneNumber.slice(3);
            document.getElementById("customer_id").value = data.user.id;
            document.getElementById("edit_name").value = data.user.name;
            document.getElementById("edit_email").value = data.user.email;
            document.getElementById("edit_country_code").value = countryCode;
            document.getElementById("edit_phone_number").value = number;

            route = route.replace(":customer_id", data.user.id);
            document
                .getElementById("deleteCustomerProfile")
                .setAttribute("data-route", route);
        })
        .catch((error) => {
            console.error("Error fetching customer data:", error);
        });
}

function showAlert(message) {
    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("popupOverlay").style.zIndex = "1000";
    document.getElementById("alert").style.display = "flex";
    document.getElementById("alert").style.zIndex = "1001";
    document.getElementById("alert-message").textContent = message;
    document.body.classList.add("no-scroll");
    document.body.style.overflow = "hidden";
    toggleClass(".whole", "active");
}

function closeAlert() {
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("popupOverlay").style.zIndex = "999";
    document.getElementById("alert").style.display = "none";
    document.getElementById("alert").style.zIndex = "1000";
    document.getElementById("alert-message").textContent = message;
    document.body.classList.remove("no-scroll");
    document.body.style.overflow = "initial";
}

function confirmationDelete() {
    let route = document
        .getElementById("deleteCustomerProfile")
        .getAttribute("data-route");

    closeProfilePopup();

    let confirmDeletionOverlay = document.getElementById(
        "confirmDeletionOverlay"
    );
    let confirmDeletionPopup = document.getElementById("confirmDeletion");
    confirmDeletionOverlay.style.display = "block";
    confirmDeletionPopup.style.display = "flex";

    let deleteButton = document.getElementById("confirm");
    deleteButton.disabled = true;
    deleteButton.style.background = "#ed7680";

    rndom.textContent = Math.random().toString(36).slice(2, 6).toUpperCase();

    let confirmButton = document.getElementById("confirm");
    confirmButton.onclick = function () {
        // document.getElementById('loaderOverlay').style.display = 'block';
        // document.getElementById('loader').style.display = 'flex';
        confirmDeletionOverlay.style.display = "none";
        confirmDeletionPopup.style.display = "none";
        window.location.href = route;
    };
}

function closeConfirmDelete() {
    let confirmDeletionOverlay = document.getElementById(
        "confirmDeletionOverlay"
    );
    let confirmDeletionPopup = document.getElementById("confirmDeletion");
    confirmDeletionOverlay.style.display = "none";
    confirmDeletionPopup.style.display = "none";
    document.getElementById("formRandomString").value = "";
    document.getElementById("overlay").style.display = "block";
    document.getElementById("profilePopup").style.display = "flex";
}

function checkIfSessionExpired() {
    let savedLoginTime = localStorage.getItem("loginStatus");

    if (savedLoginTime) {
        let savedTime = savedLoginTime.LoginTime; // Convert to milliseconds
        let currentTime = new Date().toISOString();
        let timeElapsed = currentTime - savedTime;

        console.log(currentTime);
        console.log(timeElapsed);

        // Check if 30 minutes (in milliseconds) have passed
        if (timeElapsed >= 30 * 60 * 1000) {
            localStorage.clear(); // Clear local storage
            alert("Session expired. Please log in again.");
        }
    }
}

setInterval(checkIfSessionExpired, 60 * 1000);

// window.addEventListener('beforeunload', function() {
//     localStorage.clear();
// });