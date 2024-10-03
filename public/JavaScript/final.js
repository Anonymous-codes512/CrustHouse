function toggleCart() {
    const overlay = document.getElementById("popupOverlay");
    const cartOverlay = document.getElementById("cartPopupOverlay");
    if (cartOverlay.style.display === "flex") {
        cartOverlay.style.display = "none";
        document.body.style.overflow = "auto";
        overlay.style.display = "none";
    } else {
        updateCartUI();
        cartOverlay.style.display = "flex";
        document.body.style.overflow = "hidden";
        overlay.style.display = "block";
    }
}

function updateCartUI() {
    const cartItemsContainer = document.getElementById("cart-container");
    cartItemsContainer.innerHTML = "";

    const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

    if (cartItems.length === 0) {
        document.getElementById("empty-cart").style.display = "block";
        document.querySelector(".payment-detail-container").style.display = "none";
    } else {
        document.getElementById("empty-cart").style.display = "none";
        document.querySelector(".payment-detail-container").style.display = "flex";
        document.getElementById("clear-cart").style.display = "flex";

        cartItems.forEach((item, index) => {
            const cartItem = document.createElement("div");
            cartItem.className = "cart-item";
            cartItem.innerHTML = `
            <img src="${item.imgSrc}" alt="${item.name}"class="cart-item-img">
                <div class="cart-item-info">
                    <span class="cart-item-name">${item.name}</span>
                    <span class="cart-item-price" data-original-price="${item.originalPrice}">${item.price}</span>
                    <span class="variation">${item.variation ? `Variation: ${item.variation}` : ""}</span>
                    <div class="cart-items-toppings">${item.topping || ""}</div>
                    <div class="cart-items-quantity">
                        <div class="cart-quantity">
                            <button class="q_btn decrease" data-index="${index}" onclick="decreaseCartQuantity(${index})"><i class='bx bx-minus'></i></button>
                            <div class="quantity">${item.quantity}</div>
                            <button class="q_btn increase" data-index="${index}" onclick="increaseCartQuantity(${index})"> <i class='bx bx-plus'></i></button>
                        </div>
                        <div class="item-delete-btn">
                            <i class='bx bxs-trash' onclick="deleteItem(${index})" data-index="${index}"></i>
                        </div>
                    </div>
                </div>`;
            cartItemsContainer.appendChild(cartItem);
        });
    }

    updateCartTotals();
}

function updateCartTotals() {
    const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
    let subtotal = 0;

    cartItems.forEach((item) => {
        subtotal += item.originalPrice * item.quantity;
    });

    document.querySelector(".subtotal span:last-child").textContent = `Rs ${subtotal.toFixed(2)}`;
    document.querySelector(".grandtotal span:last-child").textContent = `Rs ${subtotal.toFixed(2)}`;
}

function clearCart() {
    document.getElementById("cartPopupOverlay").style.backgroundColor = "#1E201E";
    document.getElementById("clear-cart-container").style.display = "flex";
}

function closeClearCart() {
    document.getElementById("cartPopupOverlay").style.backgroundColor = "#e9ecef";
    document.getElementById("clear-cart-container").style.display = "none";
}

function confirmClear() {
    closeClearCart();
    localStorage.removeItem("cartItems");
    updateCartUI();
}

updateCartTotals();

function updateCartItemInLocalStorage(index, updatedItem) {
    const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
    cartItems[index] = updatedItem;
    localStorage.setItem("cartItems", JSON.stringify(cartItems));
}

function increaseCartQuantity(index) {
    const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
    const cartItem = cartItems[index];

    cartItem.quantity += 1;
    cartItem.price = parseInt(cartItem.originalPrice) * cartItem.quantity;
    updateCartItemInLocalStorage(index, cartItem);
    updateCartUI();
}

function decreaseCartQuantity(index) {
    const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
    const cartItem = cartItems[index];
    if (cartItem.quantity > 1) {
        cartItem.quantity -= 1;
        cartItem.price = parseInt(cartItem.originalPrice) * cartItem.quantity;
        updateCartItemInLocalStorage(index, cartItem);
        updateCartUI();
    }
}

function deleteItem(index) {
    let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
    cartItems.splice(index, 1);
    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    updateCartUI();
}
