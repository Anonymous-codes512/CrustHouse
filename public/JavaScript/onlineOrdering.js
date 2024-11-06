let RegisteredCustomer = {
    name: null,
    "Phone Number": null,
    Email: null,
};

function closeProductPopup() {
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("productPopup").style.display = "none";
    document.body.style.overflow = "auto";
}
function addToCart(product, allProducts, addons) {
    const productArray = Object.values(allProducts); 
    let productVariations = [];

    productArray.forEach((element) => {
        if (element.productName === product.productName) {
            productVariations.push(element.productVariation);
        }
    });

    if (productVariations.length >= 2) {
        openProductPopup(product, allProducts, addons); // Open popup for multiple variations
    } else if (productVariations.length === 1) {
        handleVariationLessProduct(product); // Directly add variation-less product
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
        type: "Variation-less product",
        originalPrice: Product.productPrice,
        price: Product.productPrice,
        quantity: 1,
        type: "Variation less product",
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
    const popup = document.getElementById("productPopup");
    const popupImg = document.getElementById("popup-img");
    const popupTitle = document.getElementById("popup-title");
    const popupPrice = document.getElementById("popup-price");
    const dropdownContainer = document.querySelector(".drop");
    const extraToppingsContainer = document.querySelector(".toppingDrop");

    const productArray = Object.values(allProducts);
    const getAddons = Object.values(addons);

    let productPrices = [];
    let productVariations = [];

    dropdownContainer.innerHTML = "";
    extraToppingsContainer.innerHTML = "";

    productArray.forEach((element) => {
        if (element.productName === product.productName) {
            productPrices.push(element.productPrice);
            productVariations.push(element.productVariation);
        }
    });

    productVariations.forEach((variation, index) => {
        const dropdownOption = document.createElement("div");
        dropdownOption.className = "dropdown-option";

        const radioInput = document.createElement("input");
        radioInput.type = "radio";
        radioInput.name = "variation";
        radioInput.value = productPrices[index];
        radioInput.id = `variation-${index}`;
        radioInput.onclick = () => {
            updateProductPrice(radioInput);
            filterAddons(variation);
        };

        const label = document.createElement("label");
        label.htmlFor = radioInput.id;
        label.innerText = `${variation} - Rs. ${productPrices[index]}`;

        dropdownOption.appendChild(radioInput);
        dropdownOption.appendChild(label);
        dropdownContainer.appendChild(dropdownOption);
    });

    const toppingShow = document.getElementById("top-d-d");

    function filterAddons(selectedVariation) {
        extraToppingsContainer.innerHTML = "";
        getAddons.forEach((addon) => {
            if (addon.productVariation && Array.isArray(addon.productVariation)) {
                if (addon.productVariation.includes(selectedVariation)) {
                    addAddonToContainer(addon);
                }
            } else if (addon.productVariation === selectedVariation) {
                addAddonToContainer(addon);
            }
        });
    }

    function addAddonToContainer(addon) {
        const addonOption = document.createElement("div");
        addonOption.className = "addon-option";

        const radioInput = document.createElement("input");
        radioInput.type = "radio";
        radioInput.name = "addon";
        radioInput.value = addon.productPrice;
        radioInput.onclick = () => toppingPrice(radioInput);

        const label = document.createElement("label");
        label.htmlFor = radioInput.id;
        label.innerText = `${addon.productName} - Rs. ${addon.productPrice}`;

        addonOption.appendChild(radioInput);
        addonOption.appendChild(label);
        extraToppingsContainer.appendChild(addonOption);
    }

    if (product.category_name === "Pizza") {
        toppingShow.style.display = "flex";
    } else {
        toppingShow.style.display = "none";
    }

    popupImg.src = `Images/ProductImages/${product.productImage}`;
    popupTitle.innerText = product.productName;
    popupPrice.innerText = `Rs. ${product.productPrice}`;

    popup.style.display = "flex";
    document.getElementById("popupOverlay").style.display = "block";
    document.body.style.overflow = "hidden";
}

function closeProductCustomizationPopup() {
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("productPopup").style.display = "none";
    document.body.style.overflow = "auto";
}

function increaseQuantity() {
    let quantityElement = document.getElementById("Quantity");
    let quantity = parseInt(quantityElement.innerText);

    const selectedOption = document.querySelector('input[name="variation"]:checked');
    if (selectedOption) {
        quantity += 1;
        quantityElement.innerText = quantity;
    } else {
        showAlert("Please Select the variation first");
    }

    updateCartItemPrice1();
}

function decreaseQuantity() {
    let quantityElement = document.getElementById("Quantity");
    let quantity = parseInt(quantityElement.innerText);
    if (quantity > 1) {
        quantity -= 1;
        quantityElement.innerText = quantity;
        updateCartItemPrice1();
    }
}

function updateCartItemPrice1() {
    const quantity = parseInt(document.getElementById("Quantity").innerText);
    const selectedOption = document.querySelector('input[name="variation"]:checked');
    const variationPrice = selectedOption ? parseInt(selectedOption.getAttribute("value")) : 0;

    let toppingPrice = 0;
    const toppingCheckboxes = document.querySelectorAll('input[name="addon"]:checked');
    toppingCheckboxes.forEach((checkbox) => {
        toppingPrice += parseInt(checkbox.value);
    });

    const totalPrice = (variationPrice + toppingPrice) * quantity;
    document.getElementById("cart-price").innerText = `Rs. ${totalPrice.toFixed(0)}`;
}

function toggleDropdown(element) {
    const dropdownContent = element.nextElementSibling;
    const arrow = element.querySelector(".dropdown i");

    const allDropdowns = document.querySelectorAll(".dropdown-content");
    const allArrows = document.querySelectorAll(".dropdown i");

    allDropdowns.forEach((content) => {
        if (content !== dropdownContent) {
            content.classList.remove("active");
        }
    });

    allArrows.forEach((otherArrow) => {
        if (otherArrow !== arrow) {
            otherArrow.classList.remove("active");
            otherArrow.classList.remove("bxs-chevron-up");
            otherArrow.classList.add("bxs-chevron-down");
        }
    });

    const isActive = dropdownContent.classList.contains("active");
    dropdownContent.classList.toggle("active", !isActive);

    if (isActive) {
        arrow.classList.remove("bxs-chevron-up");
        arrow.classList.add("bxs-chevron-down");
    } else {
        arrow.classList.remove("bxs-chevron-down");
        arrow.classList.add("bxs-chevron-up");
    }

    dropdownContent.querySelectorAll('input[type="radio"]').forEach((radio) => {
        radio.addEventListener("change", () => {
            dropdownContent.classList.remove("active");
            arrow.classList.remove("bxs-chevron-up");
            arrow.classList.add("bxs-chevron-down");
            updateLabel(element, radio);
        });
    });

    dropdownContent.querySelectorAll('input[type="checkbox"]')
        .forEach((checkbox) => {
            checkbox.addEventListener("change", () => {
                updateLabelForToppings(element, checkbox);
            });
        });
}

let selectedPrice = 0;
let selectedToppingPrice = 0;

function updateProductPrice(selectedRadio) {
    selectedPrice = parseInt(selectedRadio.value);
    selectedToppingPrice = 0;
    document.querySelectorAll('input[name="addon"]').forEach((checkbox) => {
        checkbox.checked = false;
    });
    updateTotalPrice();
    document.getElementById("quantity").innerText = "1";
}

function toppingPrice(selectedToppingRadio) {
    selectedToppingPrice = parseInt(selectedToppingRadio.value);
    updateTotalPrice();
}

function updateTotalPrice() {
    const totalPrice = selectedPrice + selectedToppingPrice;
    document.getElementById("cart-price").innerText = "Rs. " + totalPrice;
    document.getElementById("originalprice").innerText = `Rs. ${totalPrice};`;
}

document.addEventListener("DOMContentLoaded", function () {
    const overlay = document.getElementById("popupOverlay");
    const cart = document.getElementById("cartPopupOverlay");

    if (overlay && cart) {
        overlay.addEventListener("click", () => {
            const cartDisplay = window.getComputedStyle(cart).display;
            if (cartDisplay === "flex") {
                cart.style.display = "none";
                overlay.style.display = "none";
                document.body.style.overflow = "auto";
            }
        });
    } else {
        console.error("Overlay or Cart element not found.");
    }
});

function showLoginPopup() {
    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("loginPopup").style.display = "flex";
    document.body.style.overflow = "hidden";
}

function hideLoginPopup() {
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("loginPopup").style.display = "none";
    document.body.style.overflow = "auto";
}

function showSignupPopup() {
    hideLoginPopup();
    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("signUpPopup").style.display = "flex";
    document.body.style.overflow = "hidden";
}

function hideSignupPopup() {
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("signUpPopup").style.display = "none";
    document.body.style.overflow = "auto";
}

function redirectToLogin() {
    hideSignupPopup();
    showLoginPopup();
}

function handleCartButtonClick() {
    const title = document.getElementById("popup-title").innerText;
    const imageSrc = document.getElementById("popup-img").src;
    const price = parseInt(document.getElementById("cart-price").innerText.replace("Rs. ", "").replace(",", ""));
    const Originalprice = parseInt(document.getElementById("originalprice").innerText.replace("Rs. ", "").replace(",", ""));
    const quantity = parseInt(document.getElementById("Quantity").innerText);
    const selectedOption = document.querySelector('input[name="variation"]:checked');
    if (selectedOption) {

        const inputId = selectedOption.id;
        const connectedLabel = document.querySelector(`label[for="${inputId}"]`).textContent;
        let index = connectedLabel.indexOf('-') - 1;
        let selectedVariation = connectedLabel.substring(0, index);
        const variationPrice = parseInt(document.querySelector('input[name="variation"]:checked').value);
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
            type: "product",
            imgSrc: imageSrc,
            variation: selectedVariation,
            variationPrice: variationPrice,
        };

        if (existingCartItemIndex !== -1) {
            cartItems[existingCartItemIndex].quantity += quantity;
            cartItems[existingCartItemIndex].price = cartItems[existingCartItemIndex].variationPrice * cartItems[existingCartItemIndex].quantity;
        } else {
            cartItems.push(cartItem);
        }

        localStorage.setItem("cartItems", JSON.stringify(cartItems));
        showMessage();
        closeAddToCart();
    } else {
        showAlert("Please Select the variation first");
    }
}

function closeAddToCart() {
    document.querySelectorAll('input[name="option"]').forEach((radio) => {
        radio.checked = false;
    });

    document.querySelectorAll('input[name="addon"]').forEach((checkbox) => {
        checkbox.checked = false;
    });
    document.querySelectorAll(".required").forEach((labelSpan) => {
        labelSpan.innerText = "Required";
        labelSpan.style.backgroundColor = "rgb(220, 53, 69)";
    });

    document.getElementById('pizza-optional').style.backgroundColor = "#ffbb00";
    document.getElementById('Quantity').textContent = 1;

    document.getElementById("deal-optional").style.backgroundColor = "#ffbb00";
    document.getElementById("quantity").textContent = 1;

    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("productPopup").style.display = "none";
    document.body.style.overflow = "auto";
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
    const CartPopup = document.getElementById('cartPopupOverlay');
    if (LoginStatus) {
        const parsedLoginStatus = JSON.parse(LoginStatus);
        if ((parsedLoginStatus.loginStatus === false && parsedLoginStatus.signupStatus === false) || parsedLoginStatus.loginStatus === null) {
            CartPopup.style.display = "none";
            showSignupPopup();
        } else if ((parsedLoginStatus.loginStatus === false && parsedLoginStatus.signupStatus === true) || parsedLoginStatus.loginStatus === null) {
            CartPopup.style.display = "none";
            showLoginPopup();
        } else if ((parsedLoginStatus.loginStatus === true && parsedLoginStatus.signupStatus === true) || parsedLoginStatus.loginStatus === null) {
            CartPopup.style.display = "none";
            showCheckOutPopup();
        } else if ((parsedLoginStatus.loginStatus === true && parsedLoginStatus.signupStatus === false) || parsedLoginStatus.loginStatus === null) {
            CartPopup.style.display = "none";
            showCheckOutPopup();
        } else {
            CartPopup.style.display = "none";
            showSignupPopup();
        }
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
        emailErrorMessage.textContent = "Email contains invalid characters like *, /, =.";
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
    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("checkOutDiv").style.display = "flex";
    document.body.style.overflow = "hidden";
}

function closeCheckOutDivPopup() {
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("checkOutDiv").style.display = "none";
    document.body.style.overflow = "auto";
}

function updateCartAndTotals() {
    const RegisteredCustomer = JSON.parse(localStorage.getItem("RegisteredCustomer"));
    document.getElementById("userName").value = RegisteredCustomer["name"];
    document.getElementById("userPhone").value = RegisteredCustomer["Phone Number"];
    document.getElementById("userEmail").value = RegisteredCustomer["Email"];

    const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
    const centerDiv = document.getElementById("center-div");
    centerDiv.innerHTML = "";

    let subTotal = 0;
    let deliveryCharge = 0;
    cartItems.forEach((item, key) => {
        subTotal += parseInt(item.price);

        const cartedItemDiv = document.createElement("div");
        cartedItemDiv.id = "carted-item-div";

        let toppingsHTML = "";
        if (item.topping && item.topping.length > 0) {
            toppingsHTML = item.topping.map((topping) => `<p>${topping.name}`);
        }

        cartedItemDiv.innerHTML =
            `<input type="hidden" name="cartedItem${key}" id="cartedItem${key}" value='${JSON.stringify(item)}'>
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
        </div>`;
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

function selectPaymentOption(element, taxes) {
    let selectedRadio = element.querySelector('input[type="radio"]');

    if (!selectedRadio.checked) {
        let paymentOptions = document.querySelectorAll(".payment-option");

        paymentOptions.forEach(function (option) {
            option.classList.remove("active");
        });

        element.classList.add("active");
        selectedRadio.checked = true;

        let total_bill_span = document.getElementById("subtotal-amount");
        let total_bill = parseInt(total_bill_span.innerText.replace("Rs ", "").replace(/,/g, ""));
        let delivery_charges = parseInt(document.getElementById("delivery-charge").innerText.replace("Rs ", "").replace(/,/g, ""));
        let tax_span = document.getElementById("tax-amount");
        let tax = 0;
        let tax_value = 0;

        if (selectedRadio.value === "Cash On Delivery") {
            taxes.forEach((taxObj) => {
                if (taxObj.tax_name.toUpperCase() === "GST ON CASH") {
                    tax_value = parseInt(taxObj.tax_value);
                    tax = (tax_value / 100) * total_bill;
                }
            });
            tax_span.textContent = tax_value + "%";
            document.getElementById("taxAmount").value = tax_value;
        } else if (selectedRadio.value === "Credit Card") {
            taxes.forEach((taxObj) => {
                if (taxObj.tax_name.toUpperCase() === "GST ON CARD") {
                    tax_value = parseInt(taxObj.tax_value);
                    tax = (tax_value / 100) * total_bill;
                }
            });
            tax_span.textContent = tax_value + "%";
            document.getElementById("taxAmount").value = tax_value;
        }

        let grand_total_bill_span = document.getElementById("grand-total");
        let grand_total = total_bill + tax + delivery_charges;
        grand_total_bill_span.textContent = "Rs " + parseInt(grand_total);
        document.getElementById("grandTotal").value = parseInt(grand_total);
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    let loginData = localStorage.getItem("LoginStatus");
    if (loginData) {
        loginData = JSON.parse(loginData);
        await fetch("/registeredCustomer", { method: "GET", })
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
                    localStorage.setItem("LoginStatus", JSON.stringify(updatedLoginData));
                    console.log("Email Not Verified");
                }
            })
            .catch((error) => console.error("Error:", error));

        if (loginData.loginStatus == true) {
            const RegisteredCustomer = JSON.parse(localStorage.getItem("RegisteredCustomer"));
            document.getElementById("username").textContent = RegisteredCustomer["name"];
            document.getElementById("username").style.width = "90px";
        }
    } else {
        console.error("LoginStatus is not available in localStorage.");
    }
});

function checkPaymentMethod() {
    const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
    let selected = false;
    paymentMethods.forEach((method) => {
        if (method.checked) {
            selected = true;
        }
    });
    if (!selected) {
        alert("Please select a payment method.");
        return false;
    }

    return true;
}

function loginUser(route) {
    const email = document.getElementById("loginEmail").value;
    const password = document.getElementById("password").value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    let loginData = localStorage.getItem("LoginStatus");
    let cartedItems = localStorage.getItem("cartItems");
    loginData = JSON.parse(loginData);
    fetch(route, {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken, },
        body: JSON.stringify({ email: email, password: password, }),
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
                localStorage.setItem("LoginStatus", JSON.stringify(updatedLoginData));
                RegisteredCustomer["name"] = data.user.name;
                RegisteredCustomer["Phone Number"] = data.user.phone_number;
                RegisteredCustomer["Email"] = data.user.email;
                localStorage.setItem("RegisteredCustomer", JSON.stringify(RegisteredCustomer));
                document.getElementById("username").textContent = data.user.name;
                if (cartedItems) {
                    showCheckOutPopup();
                }
            } else {
                let login_response_message = document.getElementById("login-response-message");
                login_response_message.style.display = "block";
                login_response_message.textContent = data.message;

                setTimeout(() => {
                    login_response_message.style.display = "none";
                }, 50000);
            }
        })
        .catch((error) => {
            let login_response_message = document.getElementById("login-response-message");
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

    event.stopPropagation();

    if (!parsedLoginStatus || parsedLoginStatus.loginStatus === false) {
        showLoginPopup();
    } else {
        let profileContainer = document.querySelector(".profile-container");
        let dropdown = document.getElementById("dropdownMenu");

        if (profileContainer.classList.contains("active")) {
            profileContainer.classList.remove("active");
            dropdown.style.display = "none";
            document.removeEventListener("click", closeDropdownOnClickOutside);
        } else {
            profileContainer.classList.add("active");
            dropdown.style.display = "flex";
            setTimeout(() => {
                dropdown.style.opacity = 1;
            }, 10);
            document.addEventListener("click", closeDropdownOnClickOutside);
        }
    }
}

function closeDropdownOnClickOutside(event) {
    let profileContainer = document.querySelector(".profile-container");
    let dropdown = document.getElementById("dropdownMenu");
    if (!profileContainer.contains(event.target)) {
        dropdown.style.opacity = 0;
        setTimeout(() => {
            dropdown.style.display = "none";
            profileContainer.classList.remove("active");
        }, 300);
        document.removeEventListener("click", closeDropdownOnClickOutside);
    } else {
        event.stopPropagation();
    }
}

function logout() {
    var dropdown = document.getElementById("dropdownMenu");
    if (dropdown.style.display === "flex") {
        dropdown.style.display = "none";
    } else {
        dropdown.style.display = "flex";
    }
    window.location.reload();
    localStorage.clear();
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
});

document.addEventListener("DOMContentLoaded", () => {
    const search_input = document.getElementById("search_bar");
    const sections = document.querySelectorAll(".section");

    search_input.addEventListener("input", () => {
        const filter_text = search_input.value.toLowerCase();
        sections.forEach(function (section) {
            const items = section.querySelectorAll(".card");
            let sectionHasVisibleItems = false;

            items.forEach(function (item) {
                const product_name_element = item.querySelector(".product_name");
                const product_name = product_name_element ? product_name_element.textContent.toLowerCase() : "";

                if (product_name.includes(filter_text)) {
                    item.style.display = "";
                    sectionHasVisibleItems = true;
                } else {
                    item.style.display = "none";
                }
            });

            section.style.display = sectionHasVisibleItems ? "" : "none";
        });
    });
});

function openProfilePopup(route) {
    getProfileData(route);
    const dropdown = document.getElementById("dropdownMenu");
    dropdown.style.display = "none";

    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("profilePopup").style.display = "flex";
    document.body.style.overflow = "hidden";
}

function closeProfilePopup() {
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("profilePopup").style.display = "none";
    document.body.style.overflow = "auto";
}

async function getProfileData(route) {
    const Email = JSON.parse(localStorage.getItem("LoginStatus"));
    await fetch(`/profile/${Email.email}`)
        .then((response) => response.json())
        .then((data) => {
            const phoneNumber = data.user.phone_number;
            const countryCode = "03";
            const number = phoneNumber.slice(3);
            document.getElementById("customer_id").value = data.user.id;
            document.getElementById("edit_name").value = data.user.name;
            document.getElementById("edit_email").value = data.user.email;
            document.getElementById("edit_country_code").value = countryCode;
            document.getElementById("edit_phone_number").value = number;

            route = route.replace(":customer_id", data.user.id);
            document.getElementById("deleteCustomerProfile").setAttribute("data-route", route);
        })
        .catch((error) => {
            console.error("Error fetching customer data:", error);
        });
}

function showAlert(message) {
    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("popupOverlay").style.zIndex = "10001";
    document.getElementById("alert").style.display = "flex";
    document.getElementById("alert").style.zIndex = "10002";
    document.getElementById("alert-message").textContent = message;
    document.body.style.overflow = "hidden";
}

function closeAlert() {
    document.getElementById("popupOverlay").style.zIndex = "9999";
    document.getElementById("alert").style.display = "none";
    document.getElementById("alert").style.zIndex = "10000";
    document.body.style.overflow = "auto";
}

function confirmationDelete() {
    let route = document.getElementById("deleteCustomerProfile").getAttribute("data-route");
    closeProfilePopup();
    let confirmDeletionOverlay = document.getElementById("popupOverlay");
    let confirmDeletionPopup = document.getElementById("confirmDeletion");
    confirmDeletionOverlay.style.display = "block";
    confirmDeletionPopup.style.display = "flex";

    let deleteButton = document.getElementById("confirm");
    deleteButton.disabled = true;
    deleteButton.style.background = "#ed7680";

    rndom.textContent = Math.random().toString(36).slice(2, 6).toUpperCase();

    let confirmButton = document.getElementById("confirm");
    confirmButton.onclick = function () {
        // document.getElementById("loaderOverlay").style.display = "block";
        // document.getElementById("loader").style.display = "flex";
        confirmDeletionOverlay.style.display = "none";
        confirmDeletionPopup.style.display = "none";
        window.location.href = route;
    };
}

function closeConfirmDelete() {
    let confirmDeletionOverlay = document.getElementById("popupOverlay");
    let confirmDeletionPopup = document.getElementById("confirmDeletion");
    confirmDeletionOverlay.style.display = "none";
    confirmDeletionPopup.style.display = "none";
    document.getElementById("formRandomString").value = "";
    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("profilePopup").style.display = "flex";
}

function checkIfSessionExpired() {
    let savedLoginTime = localStorage.getItem("loginStatus");

    if (savedLoginTime) {
        let savedTime = savedLoginTime.LoginTime;
        let currentTime = new Date().toISOString();
        let timeElapsed = currentTime - savedTime;

        console.log(currentTime);
        console.log(timeElapsed);

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
