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

/*
|---------------------------------------------------------------|
|================== Product Function Handling ==================|
|---------------------------------------------------------------|
*/

function showProductAddToCart(product, allProducts, addons) {
    let productVariations = [];
    allProducts.forEach((element) => {
        if (element.productName === product.productName) {
            productVariations.push(element.productVariation);
        }
    });
    console.log(productVariations.length);
    if (productVariations.length >= 2) {
        openProductPopup(product, allProducts, addons); // Open popup for multiple variations
    } else if (productVariations.length === 1) {
        handleVariationLessProduct(product); // Directly add variation-less product
    } else {
        console.warn("Invalid Product");
    }
}

function showSearchProduct(element) {
    let product = JSON.parse(element.getAttribute("data-product"));
    let allProducts = JSON.parse(element.getAttribute("data-all-products"));
    let addons = JSON.parse(element.getAttribute("data-addons"));

    showProductAddToCart(product, allProducts, addons);
}

function handleVariationLessProduct(Product) {
    let ProductsInCart =
        JSON.parse(localStorage.getItem("ProductsInCart")) || [];
    let existingCartItemIndex = -1;

    ProductsInCart.forEach((item, index) => {
        if (item.name === Product.productName) {
            existingCartItemIndex = index;
        }
    });

    const ProductInCart = {
        name: Product.productName,
        product_id: Product.id,
        type: "Variation-less product",
        originalPrice: parseInt(Product.productPrice),
        price: parseInt(Product.productPrice),
        quantity: 1,
        type: "Variation less product",
        variation: null,
        variationPrice: null,
        topping: null,
    };

    if (existingCartItemIndex !== -1) {
        ProductsInCart[existingCartItemIndex].quantity += 1;
        ProductsInCart[existingCartItemIndex].price =
            ProductsInCart[existingCartItemIndex].originalPrice *
            ProductsInCart[existingCartItemIndex].quantity;
    } else {
        ProductsInCart.push(ProductInCart);
    }

    localStorage.setItem("ProductsInCart", JSON.stringify(ProductsInCart));
    displayProducts();
    showMessage();
}

function openProductPopup(product, allProducts, addons) {
    const overlay = document.getElementById("overlay");
    const popup = document.getElementById("addToCart");
    const prodName = document.getElementById("prodName");
    const prodPriceSpan = document.getElementById("prodPrice");
    const productId = document.getElementById("product_id");
    const price = document.getElementById("price");
    const totalPrice = document.getElementById("totalprice");
    const prodVariation = document.getElementById("prodVariation");
    const addonsSelectLabel = document.getElementById("addOnsLabel");
    const addonsSelect = document.getElementById("addons");
    const quantity = document.getElementById("prodQuantity");

    const productArray = Object.values(allProducts);
    const getAddons = Object.values(addons);

    productId.value = product.id;

    let productPrices = [];
    let productVariations = [];

    prodVariation.innerHTML = "";
    addonsSelect.innerHTML = "";

    // Populate product variations and prices
    productArray.forEach((element) => {
        if (element.productName === product.productName) {
            productPrices.push(element.productPrice);
            productVariations.push(element.productVariation);
        }
    });

    // Populate variation dropdown
    productVariations.forEach((variation, index) => {
        const option = document.createElement("option");
        option.value = productPrices[index];
        option.text = `${variation} - Rs. ${productPrices[index]}`;
        option.setAttribute("data-variation", variation);
        prodVariation.appendChild(option);
    });

    // Handle variation change and update price and addons
    prodVariation.onchange = function () {
        const selectedPrice = parseFloat(prodVariation.value);
        const selectedVariation =
            prodVariation.options[prodVariation.selectedIndex].getAttribute(
                "data-variation"
            );
        price.value = selectedPrice;
        totalPrice.value = selectedPrice * quantity.value;

        allProducts.forEach((prod) => {
            if (
                product.productName === prod.productName &&
                prod.productVariation == selectedVariation
            ) {
                id = prod.id;
            }
        });

        productId.value = id;

        // Check if the selected product category is Pizza, only then filter addons
        if (product.category_name === "Pizza") {
            filterAddons(selectedVariation);
        } else {
            addonsSelectLabel.style.display = "none";
            addonsSelect.style.display = "none";
        }
    };

    // Populate add-ons based on the selected variation
    function filterAddons(selectedVariation) {
        addonsSelect.innerHTML = "";
        let hasMatchingAddon = false;
        const option = document.createElement("option");
        option.value = "";
        option.text = "Select Variation.";
        addonsSelect.appendChild(option);
        getAddons.forEach((addon) => {
            if (
                addon.productVariation &&
                Array.isArray(addon.productVariation)
            ) {
                if (addon.productVariation.includes(selectedVariation)) {
                    addAddonToSelect(addon);
                    hasMatchingAddon = true;
                }
            } else if (addon.productVariation === selectedVariation) {
                addAddonToSelect(addon);
                hasMatchingAddon = true;
            }
        });

        // Show or hide based on whether a matching addon was found
        if (hasMatchingAddon) {
            addonsSelectLabel.style.display = "flex";
            addonsSelect.style.display = "flex";

            // Add event listener to update total price when an addon is selected
            addonsSelect.onchange = function () {
                const addonPrice = parseFloat(addonsSelect.value);
                const basePrice = parseFloat(price.value);
                totalPrice.value =
                    basePrice * quantity.value + (addonPrice || 0);
            };
        } else {
            addonsSelectLabel.style.display = "none";
            addonsSelect.style.display = "none";
        }
    }

    function addAddonToSelect(addon) {
        const option = document.createElement("option");
        option.value = addon.productPrice;
        option.text = `${addon.productName} - Rs. ${addon.productPrice}`;
        addonsSelect.appendChild(option);
    }

    if (product.category_name === "Pizza") {
        addonsSelectLabel.style.display = "flex";
        addonsSelect.style.display = "flex";
    } else {
        addonsSelectLabel.style.display = "none";
        addonsSelect.style.display = "none";
    }

    prodName.value = product.productName;
    price.value = product.productPrice;
    totalPrice.value = product.productPrice * quantity.value;

    popup.style.display = "flex";
    overlay.style.display = "block";
    document.body.style.overflow = "hidden";
}

function addProductToCart() {
    const productId = document.getElementById("product_id").value;
    const productName = document.getElementById("prodName").value;
    const productPrice = parseFloat(document.getElementById("price").value);
    const variation = document.getElementById("prodVariation").value;
    const variationText =
        document.getElementById("prodVariation").options[
            document.getElementById("prodVariation").selectedIndex
        ].text;
    const addons = document.getElementById("addons").value;
    const addonsText =
        document.getElementById("addons").options[
            document.getElementById("addons").selectedIndex
        ]?.text || "No Addons";
    const quantity = parseInt(document.getElementById("prodQuantity").value);
    const totalPrice = parseFloat(document.getElementById("totalprice").value);

    let ProductsInCart =
        JSON.parse(localStorage.getItem("ProductsInCart")) || [];
    let existingCartItemIndex = -1;

    // Check if the product already exists in the cart based on product name and variation
    ProductsInCart.forEach((item, index) => {
        if (item.name === productName && item.variation === variationText) {
            existingCartItemIndex = index;
        }
    });

    // Create a product object to store in the cart
    const ProductInCart = {
        name: productName,
        product_id: productId,
        type: "product",
        originalPrice: productPrice + (addons ? parseFloat(addons) : 0),
        price: totalPrice,
        quantity: quantity,
        variation: variationText || "No Variation",
        variationPrice: variation ? productPrice : null,
        addons: addonsText || "No Addons",
        addonsPrice: addons ? parseFloat(addons) : 0,
    };

    // If the product exists, update the quantity and price, otherwise, add it to the cart
    if (existingCartItemIndex !== -1) {
        ProductsInCart[existingCartItemIndex].quantity += quantity;
        ProductsInCart[existingCartItemIndex].price =
            ProductsInCart[existingCartItemIndex].originalPrice *
            ProductsInCart[existingCartItemIndex].quantity;
    } else {
        ProductsInCart.push(ProductInCart);
    }

    // Save updated cart to localStorage
    localStorage.setItem("ProductsInCart", JSON.stringify(ProductsInCart));

    closeAddToCart();
    displayProducts();
    showMessage();
}

function closeAddToCart() {
    document.getElementById("addOnsLabel").style.display = "none";
    document.getElementById("addons").style.display = "none";
    document.getElementById("addToCart").style.display = "none";
    document.getElementById("overlay").style.display = "none";
    document.getElementById("prodQuantity").value = 1;
    document.getElementById("totalprice").value = 0;
}

function increaseQuantity() {
    let quantityElement = document.getElementById("prodQuantity");
    let quantity = parseInt(quantityElement.value);
    let totalPrice = document.getElementById("totalprice");
    let addonsPrice = parseFloat(document.getElementById("addons").value);
    let basePrice = parseFloat(document.getElementById("price").value);
    quantity += 1;
    quantityElement.value = quantity;
    if (!addonsPrice || isNaN(addonsPrice)) {
        addonsPrice = 0;
    }
    totalPrice.value = ((basePrice + addonsPrice) * quantity).toFixed(2);
}

function decreaseQuantity() {
    let quantityElement = document.getElementById("prodQuantity");
    let quantity = parseInt(quantityElement.value);
    let totalPrice = document.getElementById("totalprice");
    let addonsPrice = parseFloat(document.getElementById("addons").value);
    let basePrice = parseFloat(document.getElementById("price").value);
    if (!addonsPrice || isNaN(addonsPrice)) {
        addonsPrice = 0;
    }
    if (quantity > 1) {
        quantity -= 1;
        quantityElement.value = quantity;
        totalPrice.value = ((basePrice + addonsPrice) * quantity).toFixed(2);
    }
}

/*
|---------------------------------------------------------------|
|==================== Deals Function Handling ==================|
|---------------------------------------------------------------|
*/

function showDealAddToCart(deal, deals, allProducts) {
    let dealProducts = [];
    let pizzaVariation = [];
    let drinkVariation = [];
    let pizzaAddon = [];
    let pizzaAddonPrice = [];
    let dealPizzaVariation = null;
    let dealDrink = null;

    deals.forEach((element) => {
        if (element.deal_id === deal.deal_id) {
            dealProducts.push({
                product: element.product,
                product_quantity: element.product_quantity,
            });
        }
    });

    dealProducts.forEach((product) => {
        if (product.product.category_name.toLowerCase() == "pizza") {
            dealPizzaVariation = product.product.productVariation;
        }

        if (product.product.category_name.toLowerCase() == "drinks") {
            dealDrink = product.product.productVariation;
        }
    });

    allProducts.forEach((element) => {
        if (
            element.category_name.toLowerCase() == "pizza" &&
            element.productVariation == dealPizzaVariation
        ) {
            pizzaVariation.push(element.productName);
        }

        if (
            element.category_name.toLowerCase() == "addons" &&
            element.productVariation == dealPizzaVariation
        ) {
            pizzaAddon.push(element.productName);
            pizzaAddonPrice.push(element.productPrice);
        }

        if (
            element.category_name.toLowerCase() == "drinks" &&
            element.productVariation == dealDrink
        ) {
            drinkVariation.push(element.productName);
        }
    });

    let addons = { addonVariation: pizzaAddon, addonPrice: pizzaAddonPrice };
    handleSimpleDeals(
        dealProducts,
        deal.deal,
        pizzaVariation,
        drinkVariation,
        addons
    );
}

function handleSimpleDeals(
    dealProducts,
    deal,
    pizzaVariation,
    drinkVariation,
    pizzaAddon,
    productDetails
) {
    if (
        pizzaVariation.length === 0 &&
        drinkVariation.length === 0 &&
        pizzaAddon["addonVariation"].length === 0
    ) {
        productDetails = dealProducts
            .map((product) => {
                if (product.product.productName.includes("Burger")) {
                    return `${product.product_quantity} ${product.product.productName}`;
                } else {
                    return `${product.product_quantity} ${product.product.productVariation} ${product.product.productName}`;
                }
            })
            .join(", ");
        addSimpleDeal(deal, productDetails);
    } else {
        updateDealOption(deal, pizzaVariation, drinkVariation, pizzaAddon);
    }
}

function addSimpleDeal(deal, productDetails) {
    let cartItems = JSON.parse(localStorage.getItem("ProductsInCart")) || [];

    let existingCartItemIndex = -1;
    cartItems.forEach((item, index) => {
        if (item.name === productDetails) {
            existingCartItemIndex = index;
        }
    });

    const cartItem = {
        name: productDetails,
        product_id: deal.id,
        type: "variation-less-deal",
        originalPrice: deal.dealDiscountedPrice.replace(" Pkr", ""),
        price: deal.dealDiscountedPrice.replace(" Pkr", ""),
        quantity: 1,
        variation: null,
        variationPrice: null,
        topping: [],
    };

    if (existingCartItemIndex !== -1) {
        cartItems[existingCartItemIndex].quantity += 1;
        cartItems[existingCartItemIndex].price =
            parseInt(cartItems[existingCartItemIndex].originalPrice) *
            cartItems[existingCartItemIndex].quantity;
    } else {
        cartItems.push(cartItem);
    }

    localStorage.setItem("ProductsInCart", JSON.stringify(cartItems));
    displayProducts();
    showMessage();
}

let variationsArray = [];
let drinksArray = [];

function updateDealOption(deal, pizzaVariation, drinkVariation, pizzaAddon) {
    variationsArray = pizzaVariation;
    drinksArray = drinkVariation;
    const overlay = document.getElementById("overlay");
    const dealId = document.getElementById("deal_id");
    const popup = document.getElementById("addDealToCart");
    const popupTitle = document.getElementById("dealName");
    const popupPrice = document.getElementById("dealPrice");
    const dealPrice = document.getElementById("totalDealPrice");

    dealId.value = deal.id;
    popupTitle.value = deal.dealTitle;
    popupPrice.value = `Rs. ${deal.dealDiscountedPrice.replace(/pkr\s*/i, "")}`;
    dealPrice.value = deal.dealDiscountedPrice.replace(/pkr\s*/i, "");

    overlay.style.display = "block";
    popup.style.display = "flex";

    if (
        (pizzaVariation == null || pizzaVariation.length === 0) &&
        (pizzaAddon["addonVariation"] == null ||
            pizzaAddon["addonVariation"].length === 0) &&
        (drinkVariation == null || drinkVariation.length === 0)
    ) {
        alert("No deal to show.");
        document.getElementById("pizzaVariationLabel").style.display = "none";
        document.getElementById("pizzaVariation").style.display = "none";
        document.getElementById("toppingLabel").style.display = "none";
        document.getElementById("topping").style.display = "none";
        document.getElementById("drinkFlavourLabel").style.display = "none";
        document.getElementById("drinkFlavour").style.display = "none";
    } else {
        if (
            pizzaVariation != null &&
            pizzaVariation.length > 0 &&
            pizzaAddon["addonVariation"] != null &&
            pizzaAddon["addonVariation"].length > 0 &&
            (drinkVariation == null || drinkVariation.length === 0)
        ) {
            dealPizzaVariation(pizzaVariation);
            dealPizzaAddons(pizzaAddon);
            document.getElementById("pizzaVariationLabel").style.display =
                "flex";
            document.getElementById("pizzaVariation").style.display = "flex";
            document.getElementById("toppingLabel").style.display = "flex";
            document.getElementById("topping").style.display = "flex";
            document.getElementById("drinkFlavourLabel").style.display = "none";
            document.getElementById("drinkFlavour").style.display = "none";
        } else if (
            pizzaVariation != null &&
            pizzaVariation.length > 0 &&
            pizzaAddon != null &&
            pizzaAddon.addonVariation.length > 0
        ) {
            dealPizzaVariation(pizzaVariation);
            dealPizzaAddons(pizzaAddon);
            dealDrinks(drinkVariation);
            document.getElementById("pizzaVariationLabel").style.display =
                "flex";
            document.getElementById("pizzaVariation").style.display = "flex";
            document.getElementById("toppingLabel").style.display = "flex";
            document.getElementById("topping").style.display = "flex";
            document.getElementById("drinkFlavourLabel").style.display = "flex";
            document.getElementById("drinkFlavour").style.display = "flex";
        } else if (drinkVariation != null && drinkVariation.length > 0) {
            dealDrinks(drinkVariation);
            document.getElementById("pizzaVariationLabel").style.display =
                "none";
            document.getElementById("pizzaVariation").style.display = "none";
            document.getElementById("toppingLabel").style.display = "none";
            document.getElementById("topping").style.display = "none";
            document.getElementById("drinkFlavourLabel").style.display = "flex";
            document.getElementById("drinkFlavour").style.display = "flex";
        }
    }
}

function dealPizzaVariation(pizzaVariation) {
    const pizzaVariationSelect = document.getElementById("pizzaVariation");
    pizzaVariationSelect.innerHTML = "";

    pizzaVariation.forEach((variation) => {
        const option = document.createElement("option");
        option.value = variation;
        option.text = variation;
        pizzaVariationSelect.appendChild(option);
    });
}

function dealPizzaAddons(pizzaAddon) {
    const toppingSelect = document.getElementById("topping");
    const dealPrice = document.getElementById("dealPrice");
    const totalDealPrice = document.getElementById("totalDealPrice");

    toppingSelect.innerHTML = "";

    const defaultOption = document.createElement("option");
    defaultOption.value = "none";
    defaultOption.text = "None";
    toppingSelect.appendChild(defaultOption);

    pizzaAddon.addonPrice.forEach((price, index) => {
        const variation = pizzaAddon.addonVariation[index];
        const option = document.createElement("option");
        option.value = price;
        option.text = `${variation} - Rs. ${price}`;
        toppingSelect.appendChild(option);
    });

    toppingSelect.onchange = function () {
        const selectedToppingPrice = parseFloat(toppingSelect.value) || 0;
        const baseDealPrice =
            parseFloat(dealPrice.value.replace("Rs. ", "")) || 0;
        totalDealPrice.value = baseDealPrice + selectedToppingPrice;
        document.getElementById("dealQuantity").value = 1;
    };
}

function dealDrinks(drinkFlavour) {
    const drinkFlavourSelect = document.getElementById("drinkFlavour");

    drinkFlavourSelect.innerHTML = "";

    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.text = "Select Drink";
    drinkFlavourSelect.appendChild(defaultOption);

    drinkFlavour.forEach((flavour) => {
        const option = document.createElement("option");
        option.value = flavour;
        option.text = flavour;
        drinkFlavourSelect.appendChild(option);
    });
}

function closeDealAddToCart() {
    document.getElementById("pizzaVariationLabel").style.display = "none";
    document.getElementById("pizzaVariation").style.display = "none";
    document.getElementById("toppingLabel").style.display = "none";
    document.getElementById("topping").style.display = "none";
    document.getElementById("drinkFlavourLabel").style.display = "none";
    document.getElementById("drinkFlavour").style.display = "none";
    document.getElementById("overlay").style.display = "none";
    document.getElementById("addDealToCart").style.display = "none";
    document.getElementById("dealQuantity").value = 1;
    document.getElementById("totalDealPrice").value = 0;
}

function addDealToCart() {
    const dealId = document.getElementById("deal_id").value;
    const productName = document.getElementById("dealName").value;
    const productPrice = parseFloat(
        document.getElementById("dealPrice").value.replace("Rs. ", "")
    );
    const variation = document.getElementById("pizzaVariation").value;
    const toppingSelect = document.getElementById("topping");
    const selectedAddonValue = toppingSelect.value;
    const selectedOption = toppingSelect.options[toppingSelect.selectedIndex]; // Reference the select element
    const selectedAddonText = selectedOption
        ? selectedOption.text.replace(/ - Rs\. \d+/g, "").trim()
        : "";
    const drink = document.getElementById("drinkFlavour").value;
    const quantity = parseInt(document.getElementById("dealQuantity").value);
    const totalPrice = parseFloat(
        document.getElementById("totalDealPrice").value
    );

    let ProductsInCart =
        JSON.parse(localStorage.getItem("ProductsInCart")) || [];
    let existingCartItemIndex = -1;

    // Check if the product already exists in the cart based on product name and variation
    ProductsInCart.forEach((item, index) => {
        if (item.name === productName && item.variation === variation) {
            existingCartItemIndex = index;
        }
    });

    // Create a product object to store in the cart
    const ProductInCart = {
        name: productName,
        product_id: dealId,
        type: "deal",
        originalPrice:
            productPrice +
            (selectedAddonValue ? parseFloat(selectedAddonValue) : 0),
        price: totalPrice,
        quantity: quantity,
        variation: variation || "No Variation",
        variationPrice: productPrice,
        drink: drink,
        addons: selectedAddonText || "No Addons",
        addonsPrice: selectedAddonValue ? parseFloat(selectedAddonValue) : 0,
    };

    // If the product exists, update the quantity and price, otherwise, add it to the cart
    if (existingCartItemIndex !== -1) {
        ProductsInCart[existingCartItemIndex].quantity += quantity;
        ProductsInCart[existingCartItemIndex].price =
            ProductsInCart[existingCartItemIndex].originalPrice *
            ProductsInCart[existingCartItemIndex].quantity;
    } else {
        ProductsInCart.push(ProductInCart);
    }

    // Save updated cart to localStorage
    localStorage.setItem("ProductsInCart", JSON.stringify(ProductsInCart));

    closeDealAddToCart();
    displayProducts();
    showMessage();
}

function increaseDealQuantity() {
    let quantityElement = document.getElementById("dealQuantity");
    let quantity = parseInt(quantityElement.value);
    let totalPrice = document.getElementById("totalDealPrice");
    let addonsPrice = parseFloat(document.getElementById("topping").value);
    let basePrice = parseFloat(
        document.getElementById("dealPrice").value.replace("Rs. ", "")
    );
    quantity += 1;
    quantityElement.value = quantity;
    if (!addonsPrice || isNaN(addonsPrice)) {
        addonsPrice = 0;
    }
    totalPrice.value = ((basePrice + addonsPrice) * quantity).toFixed(2);
}

function decreaseDealQuantity() {
    let quantityElement = document.getElementById("dealQuantity");
    let quantity = parseInt(quantityElement.value);
    let totalPrice = document.getElementById("totalDealPrice");
    let addonsPrice = parseFloat(document.getElementById("topping").value);
    let basePrice = parseFloat(
        document.getElementById("dealPrice").value.replace("Rs. ", "")
    );
    if (!addonsPrice || isNaN(addonsPrice)) {
        addonsPrice = 0;
    }
    if (quantity > 1) {
        quantity -= 1;
        quantityElement.value = quantity;
        totalPrice.value = ((basePrice + addonsPrice) * quantity).toFixed(2);
    }
}

/*
|---------------------------------------------------------------|
|========================= Cart Handling =======================|
|---------------------------------------------------------------|
*/

let total_bill = 0;
function displayProducts() {
    total_bill = 0;
    const selectedProductsDiv = document.getElementById("selectedProducts");
    selectedProductsDiv.innerHTML = "";
    const storedProducts = localStorage.getItem("ProductsInCart");

    let finalizeProducts = [];

    if (storedProducts) {
        const parsedProducts = JSON.parse(storedProducts);
        if (Array.isArray(parsedProducts)) {
            finalizeProducts = parsedProducts;
        } else {
            finalizeProducts = [parsedProducts];
        }
    }
    finalizeProducts.forEach((value, index) => {
        const price = parseFloat(value.price);
        total_bill += price;
        const productDiv = document.createElement("div");
        productDiv.id = "productdiv";
        const productNameDiv = document.createElement("div");
        productNameDiv.className = "product_name";
        const productNameText = document.createElement("p");
        productNameText.className = "product_name";
        productNameText.id = "product-name";
        if (value.type === "product") {
            productNameText.textContent = `${value.variation
                .replace(/ - Rs\. \d+/g, "")
                .trim()} ${value.name}`;
            if (
                value.addons != "No Addons" &&
                value.addons != "Select Variation."
            ) {
                productNameText.textContent += ` with ${value.addons
                    .replace(/ - Rs\. \d+/g, "")
                    .trim()}`;
            }
        } else {
            productNameText.textContent = value.name;
        }
        const productPriceSpan = document.createElement("span");
        productPriceSpan.id = `product_price${value.id}`;
        productPriceSpan.textContent = value.price;

        productNameDiv.appendChild(productNameText);
        productNameDiv.appendChild(productPriceSpan);
        productDiv.appendChild(productNameDiv);

        const variationDiv = document.createElement("p");
        variationDiv.className = "product_name";

        if (value.type === "deal") {
            variationDiv.textContent = `${value.variation}  with  ${
                value.topping || "No Topping"
            } and ${value.drink}`;
        } else if (value.type === "variation-less-deal") {
            variationDiv.style.display = "none";
        }
        productDiv.appendChild(variationDiv);

        const productControlsDiv = document.createElement("div");
        productControlsDiv.className = "product-controls";

        const removeButton = document.createElement("button");
        removeButton.id = "remove-product";
        removeButton.innerHTML = "<i class='bx bxs-trash'></i>";
        removeButton.onclick = () => {
            removeProduct(index);
        };

        const quantityControlDiv = document.createElement("div");
        quantityControlDiv.className = "quantity-control";

        const decreaseButton = document.createElement("a");
        decreaseButton.className = "quantity-decrease-btn";
        decreaseButton.innerHTML = "<i class='bx bxs-checkbox-minus'></i>";
        decreaseButton.onclick = () => {
            decreaseCartedItemQuantity(index);
        };

        const quantityInput = document.createElement("input");
        quantityInput.className = "quantity-display-field";
        quantityInput.type = "text";
        quantityInput.name = `prodQuantity${value.id}`;
        quantityInput.id = `product_quantity${value.id}`;
        quantityInput.value = value.quantity;
        quantityInput.readOnly = true;

        const increaseButton = document.createElement("a");
        increaseButton.className = "quantity-increase-btn";
        increaseButton.innerHTML = "<i class='bx bxs-plus-square'></i>";
        increaseButton.onclick = () => {
            increaseCartedItemQuantity(index);
        };
        // Append elements to quantity control
        quantityControlDiv.appendChild(decreaseButton);
        quantityControlDiv.appendChild(quantityInput);
        quantityControlDiv.appendChild(increaseButton);
        productControlsDiv.appendChild(removeButton);
        productControlsDiv.appendChild(quantityControlDiv);
        productDiv.appendChild(productControlsDiv);
        selectedProductsDiv.appendChild(productDiv);
    });
    let bill_tax = 0;

    taxes.forEach((tax) => {
        if (tax.tax_name === "GST ON CASH") {
            bill_tax = total_bill * (parseFloat(tax.tax_value) / 100);
        }
    });

    document.getElementById("totaltaxes").value = parseInt(bill_tax);
    document.getElementById("totalbill").value = parseInt(
        total_bill + bill_tax
    );
}

function removeProduct(index) {
    const finalizeProducts =
        JSON.parse(localStorage.getItem("ProductsInCart")) || [];
    finalizeProducts.splice(index, 1);
    localStorage.setItem("ProductsInCart", JSON.stringify(finalizeProducts));
    displayProducts();
}

function decreaseCartedItemQuantity(index) {
    const finalizeProducts =
        JSON.parse(localStorage.getItem("ProductsInCart")) || [];
    if (finalizeProducts[index].quantity > 1) {
        finalizeProducts[index].quantity--;
        finalizeProducts[index].price =
            finalizeProducts[index].originalPrice *
            finalizeProducts[index].quantity;
    } else {
        alert("Quantity cannot be less than 1.");
    }
    localStorage.setItem("ProductsInCart", JSON.stringify(finalizeProducts));
    displayProducts();
}

function increaseCartedItemQuantity(index) {
    const finalizeProducts =
        JSON.parse(localStorage.getItem("ProductsInCart")) || [];
    finalizeProducts[index].quantity++;
    finalizeProducts[index].price =
        finalizeProducts[index].originalPrice *
        finalizeProducts[index].quantity;
    localStorage.setItem("ProductsInCart", JSON.stringify(finalizeProducts));
    displayProducts();
}

function clearCartedItems() {
    localStorage.removeItem("ProductsInCart");
    displayProducts();
}

/*
|---------------------------------------------------------------|
|==================== Payment and Tax Handling =================|
|---------------------------------------------------------------|
*/

let billWithTax = 0;

document.addEventListener("DOMContentLoaded", function () {
    displayProducts();

    let togglePaymentMethod = document.getElementById("paymentmethod");
    const falsetext1 = document.getElementById("false-option0").textContent;
    const truetext1 = document.getElementById("true-option0").textContent;
    let paymentMethodSelect = document.getElementById("paymentMethod");
    let removedCashOption = null;
    let bill = parseInt(document.getElementById("totalbill").value);

    function updatePaymentMethod() {
        document.getElementById("discount").value = "";
        document.getElementById("totalbill").value = bill;
        let taxAmount;
        if (togglePaymentMethod.checked) {
            paymentMethodSelect.style.display = "flex";
            for (let i = paymentMethodSelect.options.length - 1; i >= 0; i--) {
                if (
                    paymentMethodSelect.options[i].value.toLowerCase() ===
                    "cash"
                ) {
                    removedCashOption = paymentMethodSelect.options[i];
                    paymentMethodSelect.remove(i);
                }
            }
            paymentMethodSelect.value = truetext1;
            if (!paymentMethodSelect.value) {
                paymentMethodSelect.selectedIndex = 0;
            }
        } else {
            let selectedTax;
            taxes.forEach((tax) => {
                if (tax.tax_name === "GST ON CASH") {
                    selectedTax = tax.tax_value;
                }
            });
            taxAmount = total_bill + parseInt((selectedTax / 100) * total_bill);

            document.getElementById("totalbill").value = parseInt(taxAmount);
            document.getElementById("totaltaxes").value = parseInt(
                (selectedTax / 100) * total_bill
            );
            paymentMethodSelect.style.display = "none";
            paymentMethodSelect.value = falsetext1;
            if (removedCashOption) {
                let cashOption = document.createElement("option");
                cashOption.value = removedCashOption.value;
                cashOption.textContent = removedCashOption.textContent;
                paymentMethodSelect.add(
                    cashOption,
                    paymentMethodSelect.options[0]
                );
                removedCashOption = null;
            }
        }
        billWithTax = taxAmount;
    }

    updatePaymentMethod();

    togglePaymentMethod.addEventListener("change", function () {
        updatePaymentMethod();
    });
});

function adjustTax() {
    let paymentMethod = document.getElementById("paymentMethod").value;
    document.getElementById("recievecash").value = null;
    document.getElementById("change").value = null;
    document.getElementById("discount").value = "";
    let bill_with_tax = 0;
    if (paymentMethod.toLowerCase() === "card") {
        taxes.forEach((tax) => {
            if (tax.tax_name.toUpperCase() === "GST ON CARD") {
                bill_with_tax = total_bill * (tax.tax_value / 100);
                document.getElementById("totaltaxes").value =
                    parseInt(bill_with_tax);
            }
        });
    } else {
        taxes.forEach((tax) => {
            if (tax.tax_name.toUpperCase() === "GST ON CASH") {
                bill_with_tax = total_bill * (tax.tax_value / 100);
                document.getElementById("totaltaxes").value =
                    parseInt(bill_with_tax);
            }
        });
    }
    billWithTax = total_bill + bill_with_tax;
    document.getElementById("totalbill").value = parseInt(
        total_bill + bill_with_tax
    );
}

/*
|---------------------------------------------------------------|
|==================== Order Type Handling ======================|
|---------------------------------------------------------------|
*/

function EnableFields() {
    document.getElementById("recievecash").disabled = false;
    document.getElementById("recievecash").style.backgroundColor = "#fff";
    document.getElementById("paymentmethod").disabled = false;
    document.querySelector(".slider").style.backgroundColor = "#fff";
    document.getElementById("discountEnableDisable").disabled = false;
}

function DisableFields() {
    document.getElementById("recievecash").disabled = true;
    document.getElementById("recievecash").style.backgroundColor = "#e2e2e2";
    document.getElementById("paymentmethod").disabled = true;
    document.querySelector(".slider").style.backgroundColor = "#d3d3d3";
    document.getElementById("discountEnableDisable").disabled = true;
}

document.addEventListener("DOMContentLoaded", function () {
    let toggle = document.getElementById("order_type");
    const falsetext = document.getElementById("false-option").textContent;
    const truetext = document.getElementById("true-option").textContent;
    let orderTypeHidden = document.getElementById("orderTypeHidden");

    function updateHiddenInput() {
        orderTypeHidden.value = toggle.checked ? truetext : falsetext;
        if (orderTypeHidden.value.trim() == "Dine-In") {
            document.getElementById("TablesList").style.display = "flex";
            // document.getElementById('ServingTables').style.display = 'flex';
            DisableFields();
        } else {
            document.getElementById("TablesList").style.display = "none";
            // document.getElementById('ServingTables').style.display = 'none';
            EnableFields();
        }
    }

    updateHiddenInput();

    toggle.addEventListener("change", function () {
        updateHiddenInput();
    });
});

function handleTableChange() {
    var selectedTable = document.getElementById("tables_list").value;

    if (selectedTable === "0") {
        EnableFields();
    } else {
        DisableFields();
    }
}

function validateNumericInput(input) {
    let sanitizedValue = input.value.match(/^\d*(?:\.\d*)?$/);
    if (sanitizedValue) {
        sanitizedValue = sanitizedValue[0];
    } else {
        sanitizedValue = "";
    }
    input.value = sanitizedValue;
    calculateChange(input.value);
}

function calculateChange(receivedBill) {
    let totalBill = parseInt(document.getElementById("totalbill").value);

    receivedBill = parseInt(receivedBill);

    if (isNaN(receivedBill)) {
        document.getElementById("change").value = "";
    }

    if (isNaN(totalBill)) {
        totalBill = 0;
    }
    let change = receivedBill - totalBill;
    document.getElementById("proceed").disabled = change < 0;
    document.getElementById("change").value = change.toFixed(2);
}

/*
|---------------------------------------------------------------|
|====================== Discount Handling ======================|
|---------------------------------------------------------------|
*/

function toggleDiscount() {
    document.getElementById("recievecash").value = null;
    document.getElementById("change").value = null;
    document.getElementById("totalbill").value = billWithTax;

    togglebtn = document.getElementById("discountEnableDisable").checked;
    if (togglebtn == true) {
        document.getElementById("toggle-text").textContent = "Disable Discount";
        document.getElementById("toggle-text").style.width = "250px";
        document.getElementById("discount").disabled = false;
        document.getElementById("discount_reason").disabled = false;
        document.getElementById("discountType").disabled = false;

        document.getElementById("discount-Type-div").style.display = "flex";
        document.getElementById("discountFieldDiv").style.display = "flex";
        document.getElementById("discountReasonDiv").style.display = "flex";
        document.getElementById("discountReasonDiv").required = true;
        document.getElementById("discountTypeDiv").style.display = "flex";
    } else {
        document.getElementById("toggle-text").textContent = "Enable Discount";
        document.getElementById("toggle-text").style.width = "200px";
        document.getElementById("discount").disabled = true;
        document.getElementById("discount_reason").disabled = true;
        document.getElementById("discountType").disabled = true;

        document.getElementById("discount-Type-div").style.display = "none";
        document.getElementById("discountFieldDiv").style.display = "none";
        document.getElementById("discountReasonDiv").style.display = "none";
        document.getElementById("discountTypeDiv").style.display = "none";
    }
}

function validateDiscount() {
    const discountEnabled = document.getElementById(
        "discountEnableDisable"
    ).checked;
    const discountReason = document.getElementById("discount_reason").value;
    const finalizeProducts =
        JSON.parse(localStorage.getItem("ProductsInCart")) || [];
    document.getElementById("cartItems").value =
        JSON.stringify(finalizeProducts);

    let order_type = document.getElementById("orderTypeHidden").value.trim();
    if (order_type.toLowerCase() === "takeaway") {
        showOrderTypeConfirmationPopUp();
        return false;
    }

    let productDiv = document.getElementById("productdiv");
    if (productDiv === null) {
        alert("Select the Product First.");
        return false;
    }

    if (discountEnabled && !discountReason) {
        alert("Please select a reason for the discount.");
        return false;
    }
    return true;
}
let discountTypeInput = document.getElementById("discountType");
let toggleDiscountType = document.getElementById("discounttype");

function updateTotalONSwitchChange(total, discountLimit, discountType) {
    let discount = parseInt(document.getElementById("discount"));
    let discountAmount = parseInt(discount.value);

    let totalBill = parseInt(total);
    let discountLimitValue = parseInt(discountLimit);
    if (isNaN(discountAmount)) {
        document.getElementById("totalbill").value = totalBill;
        return;
    }
    let fixedDiscountAmount = parseInt((discountLimitValue / 100) * total);

    if (discountType == "%" && discountAmount > discountLimitValue) {
        alert(
            `Discount in Percentage should be less than or equal to ${discountLimitValue}.`
        );
        discount.value = discountLimitValue;
        discountAmount = discountLimitValue;
    }

    if (discountType == "-" && discountAmount > fixedDiscountAmount) {
        alert(
            `Discount amount should be less than or equal to ${fixedDiscountAmount} (${discountLimitValue}% of total bill.)`
        );
        discount.value = fixedDiscountAmount;
        discountAmount = fixedDiscountAmount;
    }

    if (discountType == "%") {
        let discountedBill = parseInt(
            totalBill - (discountAmount / 100) * totalBill
        );
        document.getElementById("totalbill").value = discountedBill;
    }

    if (discountType == "-") {
        let discountedBill = parseInt(totalBill - discountAmount);
        document.getElementById("totalbill").value = discountedBill;
    }
}

function updateTotalONSwitch(discountLimit) {
    let paymentMTD = document.getElementById("paymentMethod").value;
    let taxOnCard;
    taxes.forEach((tax) => {
        if (tax.tax_name.toUpperCase() === "GST ON CASH") {
            taxOnCard = tax.tax_value;
        }

        if (
            tax.tax_name.toUpperCase() === "GST ON CASH" &&
            paymentMTD.toLowerCase() === "cash"
        ) {
            selectedTax = tax.tax_value;
        } else if (
            tax.tax_name.toUpperCase() === "GST ON CARD" &&
            paymentMTD.toLowerCase() === "card"
        ) {
            selectedTax = tax.tax_value;
        } else {
            selectedTax = taxOnCard;
        }
    });

    let taxAmount = total_bill + parseInt((selectedTax / 100) * total_bill);
    document.getElementById("recievecash").value = null;
    document.getElementById("change").value = null;
    document.getElementById("totalbill").value = parseInt(taxAmount);
    document.getElementById("totaltaxes").value = parseInt(
        (selectedTax / 100) * total_bill
    );

    discountTypeInput.value = toggleDiscountType.checked ? "-" : "%";
    document.getElementById("discount").value = "";
    updateTotalONSwitchChange(
        taxAmount,
        discountLimit,
        discountTypeInput.value
    );
}

function updateTotalONInput(discountLimit) {
    let discount = document.getElementById("discount");
    discount.addEventListener("input", () => {
        let sanitizedValue = discount.value.match(/^\d*(?:\.\d*)?$/);
        if (sanitizedValue) {
            sanitizedValue = sanitizedValue[0];
        } else {
            sanitizedValue = "";
        }
        discount.value = sanitizedValue;
    });

    let discountType = document.getElementById("discountType").value;

    let discountAmount = parseInt(discount.value);
    let taxAmount = parseInt(document.getElementById("totaltaxes").value);
    let totalBill = parseInt(total_bill + taxAmount);

    let discountLimitValue = parseInt(discountLimit);
    if (isNaN(discountAmount)) {
        document.getElementById("totalbill").value = `Rs ${totalBill}`;
        return;
    }
    let fixedDiscountAmount = parseInt(
        (discountLimitValue / 100) * (total_bill + taxAmount)
    );

    if (discountType == "%" && discountAmount > discountLimitValue) {
        alert(
            `Discount in Percentage should be less than or equal to ${discountLimitValue}.`
        );
        discount.value = discountLimitValue;
        discountAmount = discountLimitValue;
    } else if (discountType == "-" && discountAmount > fixedDiscountAmount) {
        alert(
            `Discount amount should be less than or equal to ${fixedDiscountAmount} (${discountLimitValue}% of total bill.)`
        );
        discount.value = fixedDiscountAmount;
        discountAmount = fixedDiscountAmount;
    }

    if (discountType == "%") {
        let discountedBill = parseInt(
            totalBill - (discountAmount / 100) * totalBill
        );
        document.getElementById("totalbill").value = discountedBill;
    } else if (discountType == "-") {
        let discountedBill = parseInt(totalBill - discountAmount);
        document.getElementById("totalbill").value = discountedBill;
    }
}

function addNewProductToDineInOrder(cartedProduct, route) {
    let ProductsInCart =
        JSON.parse(localStorage.getItem("ProductsInCart")) || [];

    cartedProduct.forEach((product) => {
        let existingCartItemIndex = -1;

        // Check if the product already exists in the cart
        ProductsInCart.forEach((item, index) => {
            if (item.name === product.productName) {
                existingCartItemIndex = index;
            }
        });

        const ProductInCart = {
            name: product.productName,
            product_id: product.product_id,
            type: "product",
            originalPrice: parseFloat(product.productPrice),
            price: parseFloat(product.totalPrice),
            quantity: parseInt(product.productQuantity, 10),
            variation: product.productVariation || "No Variation",
            addons: product.productAddon || "No Addons",
            addonsPrice: parseFloat(product.addonPrice),
        };

        if (existingCartItemIndex !== -1) {
            // Update the existing product's quantity and price
            ProductsInCart[existingCartItemIndex].quantity +=
                ProductInCart.quantity;
            ProductsInCart[existingCartItemIndex].price += ProductInCart.price; // Adjust the total price
        } else {
            // Add the new product to the cart
            ProductsInCart.push(ProductInCart);
        }
    });

    // Save the updated cart back to localStorage
    localStorage.setItem("ProductsInCart", JSON.stringify(ProductsInCart));
    displayProducts();
    window.location.href = route; // Use = instead of parentheses
}

function showOrderTypeConfirmationPopUp() {
    document.getElementById("orderTypeConfirmationOverlay").style.display =
        "block";
    document.getElementById("orderTypeConfirmation").style.display = "flex";
}
function hideOrderTypeConfirmationPopUp() {
    document.getElementById("orderTypeConfirmationOverlay").style.display =
        "none";
    document.getElementById("orderTypeConfirmation").style.display = "none";
}

function chnageOrderType() {
    const form = document.getElementById("placeOrder");
    const orderType = document.getElementById("type-confirmation").value;
    if (orderType === "Takeaway - Rider") {
        const requiredFields = document.querySelectorAll(
            "#input-div input[required]"
        );
        let allValid = true;
        requiredFields.forEach((field) => {
            if (!field.value.trim()) {
                allValid = false;
                field.style.border = "1px solid red";
            } else {
                field.style.border = "";
            }
        });

        if (!allValid) {
            alert("Please fill all required fields.");
            return false;
        }
        requiredFields.forEach((field) => {
            hiddenField = document.createElement("input");
            hiddenField.type = "hidden";
            hiddenField.name = field.name;
            hiddenField.value = field.value;
            form.appendChild(hiddenField);
        });
        document.getElementById("orderTypeHidden").value = "online";
    }
    else{
        document.getElementById("orderTypeHidden").value = "Takeaway - self";
    }
    validateDiscount();
    hideOrderTypeConfirmationPopUp();
}

function toggleDetailsFields(selectElement) {
    if (selectElement.value === "Takeaway - Rider") {
        document.getElementById("input-div").style.display = "flex";
    } else {
        document.getElementById("input-div").style.display = "none";
    }
}
