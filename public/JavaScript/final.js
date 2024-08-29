function updateCartItemPrice1() {
    const quantity = parseInt(document.getElementById("quantity").innerText);
    const selectedOption = document.querySelector(
        'input[name="option"]:checked'
    );
    const variationPrice = selectedOption ?
        parseInt(selectedOption.getAttribute("value")) :
        0;

    // Calculate the topping price
    let toppingPrice = 0;
    const toppingCheckboxes = document.querySelectorAll(
        'input[name="addon"]:checked'
    );
    toppingCheckboxes.forEach((checkbox) => {
        toppingPrice += parseInt(checkbox.value);
    });

    // Calculate the total price
    const totalPrice = (variationPrice + toppingPrice) * quantity;
    document.getElementById("cart-price").innerText = `Rs. ${totalPrice.toFixed(
        0
    )}`;
}

// Function to increase the quantity of an item
function increaseQuantity() {
    const quantityElement = document.getElementById("quantity");
    let quantity = parseInt(quantityElement.innerText);
    quantity += 1;
    quantityElement.innerText = quantity;
    updateCartItemPrice1(); // Update price based on new quantity
}

// Function to decrease the quantity of an item
function decreaseQuantity() {
    const quantityElement = document.getElementById("quantity");
    let quantity = parseInt(quantityElement.innerText);
    if (quantity > 1) {
        quantity -= 1;
        quantityElement.innerText = quantity;
        updateCartItemPrice1(); // Update price based on new quantity
    }
}

// Function to handle adding items to the cart
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

    // Gather selected toppings
    const selectedToppings = [];
    const toppingCheckboxes = document.querySelectorAll(
        'input[name="addon"]:checked'
    );
    toppingCheckboxes.forEach((checkbox) => {
        const toppingName = checkbox
            .closest(".dropdown_2")
            .querySelector(".sizee")
            .innerText.trim();
        const toppingPrice = parseInt(checkbox.value);
        selectedToppings.push({
            name: toppingName,
            price: toppingPrice,
        });
    });

    const toppingsToStore =
        selectedToppings.length > 0 ? selectedToppings : null;

    let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

    // Check if the item already exists in the cart
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
        topping: toppingsToStore,
    };

    if (existingCartItemIndex !== -1) {
        // If the item already exists, update its quantity and toppings
        cartItems[existingCartItemIndex].quantity += quantity;
        cartItems[existingCartItemIndex].price =
            cartItems[existingCartItemIndex].variationPrice *
            cartItems[existingCartItemIndex].quantity;
        cartItems[existingCartItemIndex].topping = toppingsToStore;
    } else {
        cartItems.push(cartItem);
    }

    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    showMessage();
    closeAddToCart();
}

// Function to toggle the visibility of the cart
function toggleCart() {
    const cart = document.getElementById("cart");
    if (cart.classList.contains("active")) {
        cart.classList.remove("active");
        document.body.classList.remove("no-scroll");
        document.querySelector(".whole").style.pointerEvents = "auto";
        // document.querySelector(".whole").style.filter = "initial";
        document.getElementById("overlay").style.display = "none";
    } else {
        openCartSidebar();
        cart.classList.add("active");
        document.body.classList.add("no-scroll");
        document.body.style.overflow = "hidden";

        document.getElementById("overlay").style.display = "block";
        document.querySelector(".whole").style.pointerEvents = "none";
        // document.querySelector(".whole").style.filter = "blur(2px)";
    }
}

function openCartSidebar() {
    updateCartUI();
}

// Function to update the cart UI based on local storage
function updateCartUI() {
    const cartItemsContainer = document.querySelector("#cart .cartitems");
    cartItemsContainer.innerHTML = ""; // Clear current items
    var cart = document.querySelector(".cart_clear");
    cart.style.display = "block";
    const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

    if (cartItems.length === 0) {
        document.querySelector(".emptycart").style.display = "block";
        document.querySelector(".total").style.display = "none";
        document.querySelector(".cart_clear").style.display = "none";
    } else {
        document.querySelector(".emptycart").style.display = "none";
        document.querySelector(".total").style.display = "flex";
    }

    const img = `${window.location.origin}/Images/OnlineOrdering/delete.png`;

    cartItems.forEach((item, index) => {
        const cartItem = document.createElement("div");
        cartItem.className = "cart-item";

        // Create the cart item HTML
        let toppingsHTML = "";
        if (item.topping && item.topping.length > 0) {
            toppingsHTML = item.topping
                .map(
                    (topping) =>
                    `<div>${topping.name}: Rs ${topping.price}</div>`
                )
                .join("");
        } else {
            toppingsHTML = "<div></div>";
        }

        cartItem.innerHTML = `
            <img src="${item.imgSrc}" alt="${item.name}" class="cart-item-img">
            <div class="cart-item-info">
                <span class="cart-item-name">${item.name}</span>
                <span class="cart-item-price" data-original-price="${item.originalPrice}">${item.price}</span>
                <span class="variation">Variation: ${item.variation}</span>
                <div class="cart-items-toppings">
                    ${toppingsHTML}
                </div>
                <div class="cart-items-quantity">
                    <div class="cart-quantity">
                        <button class="q_btn decrease" data-index="${index}">-</button>
                        <div class="quantity">${item.quantity}</div>
                        <button class="q_btn increase" data-index="${index}">+</button>
                    </div>
                    <div class="delbtn">
                        <img class="delete_img" src="${img}" alt="">
                    </div>
                </div>
            </div>
        `;

        cartItemsContainer.appendChild(cartItem);
        attachDeleteHandler(cartItem, index);
    });

    document.body.classList.add("no-scroll");
    // document.querySelector(".whole").style.filter = "blur(2px)";
    updateCartTotals();

    // Add event listeners to quantity buttons
    document.querySelectorAll(".q_btn.decrease").forEach((button) => {
        button.addEventListener("click", decreaseCartItemQuantity);
    });
    document.querySelectorAll(".q_btn.increase").forEach((button) => {
        button.addEventListener("click", increaseCartItemQuantity);
    });
}
let selectedPrice = 0;
let selectedToppingPrice = 0;

function updatePrice(selectedRadio) {
    selectedPrice = parseInt(selectedRadio.value);
    selectedToppingPrice = 0;
    document.querySelectorAll('input[name="addon"]').forEach((checkbox) => {
        checkbox.checked = false;
    });
    // Update the total price with the selected variation and topping prices
    updateTotalPrice();

    // Reset quantity to 1 when variation changes
    document.getElementById("quantity").innerText = "1";
}

function toppingPrice(selectedToppingRadio) {
    selectedToppingPrice = parseInt(selectedToppingRadio.value);
    updateTotalPrice();
}

function updateTotalPrice() {
    const totalPrice = selectedPrice + selectedToppingPrice;
    document.getElementById("cart-price").innerText = "Rs. " + totalPrice;
}

// clear button code
document.querySelector(".cart_clear").onclick = function() {
    document.querySelector(".clear").style.display = "block";
    document.querySelector(".cart_part1").style.filter = "blur(3px)";
};

document.addEventListener("DOMContentLoaded", function() {
    var clearButton = document.querySelector(".clear_Cart_itens");
    var cancelButton = document.querySelector(".cancel");
    document.querySelector(".clearbtn").addEventListener("click", function() {
        localStorage.removeItem("cartItems"); // Remove all cart items from local storage
        updateCartUI(); // Update UI to reflect changes
        document.querySelector(".clear").style.display = "none";
        document.querySelector(".cart_clear").style.display = "none";
        document.querySelector(".emptycart").style.display = "flex";
        document.querySelector(".total").style.display = "none";
        document.querySelector(".cart_part1").style.filter = "initial";
    });
    document.querySelector(".cancel").addEventListener("click", function() {
        document.querySelector(".clear").style.display = "none";
        document.querySelector(".cart_part1").style.filter = "initial";
    });
});

function updateCartTotals() {
    var subtotal = 0;
    document.querySelectorAll("#cart .cart-item").forEach(function(cartItem) {
        var itemPrice = parseFloat(
            cartItem
            .querySelector(".cart-item-price")
            .innerText.replace(/[^0-9.]/g, "")
        );
        var quantity = parseInt(cartItem.querySelector(".quantity").innerText);
        subtotal += itemPrice;
    });

    var deliveryCharges = 0; // Adjust delivery charges if applicable
    var grandTotal = subtotal + deliveryCharges;

    document.querySelector(".subtotal span:last-child").innerText =
        "Rs " + subtotal.toFixed(2);
    document.querySelector(".delichrge span:last-child").innerText =
        "Rs " + deliveryCharges.toFixed(2);
    document.querySelector(".grandtotal span:last-child").innerText =
        "Rs " + grandTotal.toFixed(2);

    var cart = document.querySelector(".cart_clear");
    var emptyCart = document.querySelector(".emptycart");
    if (subtotal > 0) {
        document.querySelector(".total").style.display = "flex";
        if (emptyCart) {
            emptyCart.style.display = "none";
        }
    } else {
        document.querySelector(".total").style.display = "none";
        if (emptyCart) {
            emptyCart.style.display = "flex";
        }
    }
}

function decreaseCartItemQuantity(event) {
    const index = event.target.dataset.index;
    const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
    const cartItem = cartItems[index];

    if (cartItem.quantity > 1) {
        cartItem.quantity -= 1;
        // Update price based on variation and topping
        cartItem.price =
            (cartItem.variationPrice +
                (cartItem.topping ?
                    cartItem.topping.reduce(
                        (total, topping) => total + topping.price,
                        0
                    ) :
                    0)) *
            cartItem.quantity;
        updateCartItemInLocalStorage(index, cartItem);
        updateCartUI();
    }
}

function increaseCartItemQuantity(event) {
    const index = event.target.dataset.index;
    const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
    const cartItem = cartItems[index];

    cartItem.quantity += 1;
    // Update price based on variation and topping
    cartItem.price =
        (cartItem.variationPrice +
            (cartItem.topping ?
                cartItem.topping.reduce(
                    (total, topping) => total + topping.price,
                    0
                ) :
                0)) *
        cartItem.quantity;
    updateCartItemInLocalStorage(index, cartItem);
    updateCartUI();
}

function updateCartItemInLocalStorage(index, updatedItem) {
    const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
    cartItems[index] = updatedItem;
    localStorage.setItem("cartItems", JSON.stringify(cartItems));
}

function attachDeleteHandler(cartItem, index) {
    var deleteButton = cartItem.querySelector(".delete_img");
    deleteButton.classList.add("cart-delete-btn");
    deleteButton.addEventListener("click", function() {
        let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
        // Remove item at the given index
        cartItems.splice(index, 1);
        localStorage.setItem("cartItems", JSON.stringify(cartItems));
        updateCartUI(); // Refresh the cart UI
    });
}