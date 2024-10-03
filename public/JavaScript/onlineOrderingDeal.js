function addDealToCart(deal, deals, allProducts) {
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

    let productDetails;

    productDetails = dealProducts.map((product) =>
        `${product.product_quantity} ${product.product.productName} (${product.product.productVariation}) `).join(", ");
    document.getElementById("deal_popup-dealName").innerHTML = productDetails;

    dealProducts.forEach((product) => {
        if (product.product.category_name.toLowerCase() == "pizza") {
            dealPizzaVariation = product.product.productVariation;
        }

        if (product.product.category_name.toLowerCase() == "drinks") {
            dealDrink = product.product.productVariation;
        }
    });

    allProducts.forEach((element) => {
        if (element.category_name.toLowerCase() == "pizza" && element.productVariation == dealPizzaVariation) {
            pizzaVariation.push(element.productName);
        }

        if (element.category_name.toLowerCase() == "addons" && element.productVariation == dealPizzaVariation) {
            pizzaAddon.push(element.productName);
            pizzaAddonPrice.push(element.productPrice);
        }

        if (element.category_name.toLowerCase() == "drinks" && element.productVariation == dealDrink) {
            drinkVariation.push(element.productName);
        }
    });

    let addons = { addonVariation: pizzaAddon, addonPrice: pizzaAddonPrice };
    handleSimpleDeals(dealProducts, deal.deal, pizzaVariation, drinkVariation, addons);
}

function handleSimpleDeals(dealProducts, deal, pizzaVariation, drinkVariation, pizzaAddon, productDetails) {
    if (pizzaVariation.length === 0 && drinkVariation.length === 0 && pizzaAddon["addonVariation"].length === 0) {
        productDetails = dealProducts.map((product) => {
            if (product.product.productName.includes("Burger")) {
                return `${product.product_quantity} ${product.product.productName}`;
            } else {
                return `${product.product_quantity} ${product.product.productVariation} ${product.product.productName}`;
            }
        }).join(", ");
        addSimpleDeal(deal, productDetails);
    } else {
        updateDealOption(deal, pizzaVariation, drinkVariation, pizzaAddon, productDetails);
    }
}

function addSimpleDeal(deal, productDetails) {
    let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

    let existingCartItemIndex = -1;
    cartItems.forEach((item, index) => {
        if (item.name === productDetails) {
            existingCartItemIndex = index;
        }
    });

    const cartItem = {
        name: productDetails,
        type: "deal",
        originalPrice: deal.dealDiscountedPrice,
        price: deal.dealDiscountedPrice,
        quantity: 1,
        imgSrc: `Images/DealImages/${deal.dealImage}`,
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

    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    showMessage();
}

let variationsArray = [];
let drinksArray = [];

function updateDealOption(deal, pizzaVariation, drinkVariation, pizzaAddon) {
    variationsArray = pizzaVariation;
    drinksArray = drinkVariation;
    const popup = document.getElementById("dealPopup");
    const popupImg = document.getElementById("deal_popup-img");
    const popupTitle = document.getElementById("deal_popup-title");
    const popupPrice = document.getElementById("deal_popup-price");
    const dealCartBtn = document.getElementById("deal-add-cart");

    const overlay = document.getElementById("popupOverlay");

    popupImg.src = `Images/DealImages/${deal.dealImage}`;
    popupTitle.innerText = deal.dealTitle;
    popupPrice.innerText = `Rs. ${deal.dealDiscountedPrice.replace(/pkr\s*/i, "")}`;

    dealCartBtn.innerHTML = `
    <div>
        <span id="deal-original-price" style="display: none;">${deal.dealDiscountedPrice.replace(/pkr\s*/i, "")}</span>
        <span id="deal-cart-price">${deal.dealDiscountedPrice.replace(/pkr\s*/i, "")}</span>
    </div>
    &nbsp;&nbsp;<div>Add to Cart</div>`;

    document.getElementById("deal-original-price").textContent = deal.dealDiscountedPrice.replace(/pkr\s*/i, "");
    document.getElementById("deal-cart-price").textContent = `Rs. ${deal.dealDiscountedPrice.replace(/pkr\s*/i, "")}`;

    overlay.style.display = "block";
    popup.style.display = "flex";
    document.body.style.overflow = "hidden";

    if ((pizzaVariation == null || pizzaVariation.length === 0) && (pizzaAddon["addonVariation"] == null || pizzaAddon["addonVariation"].length === 0) && (drinkVariation == null || drinkVariation.length === 0)) {
        alert("No deal to show.");
        document.getElementById("pizza-variation-dropdown").style.display = "none";
        document.getElementById("topping-dropdown").style.display = "none";
        document.getElementById("drink-dropdown").style.display = "none";
    } else {
        if (pizzaVariation != null && pizzaVariation.length > 0 && pizzaAddon["addonVariation"] != null && pizzaAddon["addonVariation"].length > 0 && (drinkVariation == null || drinkVariation.length === 0)) {
            dealPizzaVariation(pizzaVariation);
            dealPizzaAddons(pizzaAddon);
            document.getElementById("pizza-variation-dropdown").style.display = "flex";
            document.getElementById("topping-dropdown").style.display = "flex";
            document.getElementById("drink-dropdown").style.display = "none";
        } else if (pizzaVariation != null && pizzaVariation.length > 0 && pizzaAddon != null && pizzaAddon.addonVariation.length > 0) {
            dealPizzaVariation(pizzaVariation);
            dealPizzaAddons(pizzaAddon);
            dealDrinks(drinkVariation);
            document.getElementById("pizza-variation-dropdown").style.display = "flex";
            document.getElementById("topping-dropdown").style.display = "flex";
            document.getElementById("drink-dropdown").style.display = "flex";
        } else if (drinkVariation != null && drinkVariation.length > 0) {
            dealDrinks(drinkVariation);
            document.getElementById("pizza-variation-dropdown").style.display = "none";
            document.getElementById("topping-dropdown").style.display = "none";
            document.getElementById("drink-dropdown").style.display = "flex";
        }
    }
}

function dealPizzaVariation(pizzaVariation) {
    const dropdownContainer = document.querySelector(".dealDrop");
    dropdownContainer.innerHTML = "";

    pizzaVariation.forEach((variation, index) => {
        const dropdown2 = document.createElement("div");
        dropdown2.className = "dropdown_2";

        const radioDiv = document.createElement("div");
        const radioInput = document.createElement("input");
        radioInput.type = "radio";
        radioInput.name = "pizza_variation";
        radioInput.value = variation;
        radioInput.dataset.price = "0";
        radioInput.id = `variation-${index}`;
        radioInput.onclick = () => {
            document.querySelectorAll('input[name="addon"]').forEach((checkbox) => {
                checkbox.checked = false;
            });
            updatePrice();
        };
        if (index === 0) {
            updatePrice();
        }
        radioDiv.appendChild(radioInput);
        dropdown2.appendChild(radioDiv);

        const dropdown3 = document.createElement("div");
        dropdown3.className = "dropdown_3";

        const sizeSpan = document.createElement("span");
        sizeSpan.className = "size";
        sizeSpan.innerText = variation;
        dropdown3.appendChild(sizeSpan);
        dropdown2.appendChild(dropdown3);
        dropdownContainer.appendChild(dropdown2);
    });
}

function dealPizzaAddons(pizzaAddon) {
    const addonsContainer = document.querySelector(".dealAddonDrop");
    addonsContainer.innerHTML = "";
    let addonVariation = pizzaAddon["addonVariation"];
    let addonPriceDeal = pizzaAddon["addonPrice"];

    addonVariation.forEach((addon, index) => {
        const dropdown2 = document.createElement("div");
        dropdown2.className = "dropdown_2";

        const checkboxDiv = document.createElement("div");
        const checkboxInput = document.createElement("input");
        checkboxInput.type = "radio";
        checkboxInput.value = `${addon}-${addonPriceDeal[index]}`;
        checkboxInput.name = "addon_option";
        checkboxInput.id = `addon-${index}`;
        checkboxInput.dataset.price = addonPriceDeal[index];
        checkboxInput.onclick = () => {
            updatePrice();
        };
        checkboxDiv.appendChild(checkboxInput);
        dropdown2.appendChild(checkboxDiv);

        const dropdown3 = document.createElement("div");
        dropdown3.className = "dropdown_3";

        const addonSpan = document.createElement("span");
        addonSpan.className = "addon-name";
        addonSpan.innerText = addon;

        const priceSpan = document.createElement("span");
        priceSpan.className = "rs";
        priceSpan.innerText = `Rs. ${addonPriceDeal[index]}`;

        dropdown3.appendChild(addonSpan);
        dropdown3.appendChild(priceSpan);
        dropdown2.appendChild(dropdown3);
        addonsContainer.appendChild(dropdown2);
    });
}

function dealDrinks(drinkFlavour) {
    const drinkContainer = document.querySelector(".dealDrinkDrop");
    drinkContainer.innerHTML = "";

    drinkFlavour.forEach((flavour, index) => {
        const dropdown2 = document.createElement("div");
        dropdown2.className = "dropdown_2";

        const checkboxDiv = document.createElement("div");
        const checkboxInput = document.createElement("input");
        checkboxInput.type = "radio";
        checkboxInput.value = flavour;
        checkboxInput.name = "drink_flavour";
        checkboxInput.id = `drink-${index}`;
        checkboxInput.dataset.price = "0";
        checkboxDiv.appendChild(checkboxInput);
        dropdown2.appendChild(checkboxDiv);

        const dropdown3 = document.createElement("div");
        dropdown3.className = "dropdown_3";

        const addonSpan = document.createElement("span");
        addonSpan.className = "addon-name";
        addonSpan.innerText = flavour;
        dropdown3.appendChild(addonSpan);
        dropdown2.appendChild(dropdown3);
        drinkContainer.appendChild(dropdown2);
    });
}

let quantity = 1;

function increaseDealQuantity() {
    quantity++;
    document.getElementById("quantity").textContent = quantity;
    updatePrice();
}

function decreaseDealQuantity() {
    if (quantity > 1) {
        quantity--;
        document.getElementById("quantity").textContent = quantity;
        updatePrice();
    }
}

function updatePrice() {
    const basePrice = parseInt(document.getElementById("deal-original-price").textContent);
    let totalPrice = basePrice * quantity;

    const selectedPizzaVariation = document.querySelector('input[name="pizza_variation"]:checked');
    if (selectedPizzaVariation) {
        totalPrice += parseInt(selectedPizzaVariation.dataset.price || 0) * quantity;
    }

    const selectedAddons = document.querySelectorAll('input[name="addon_option"]:checked');
    if (selectedAddons.length > 0) {
        selectedAddons.forEach((addon) => {
            totalPrice += parseFloat(addon.dataset.price || 0) * quantity;
        });
    }

    const selectedDrink = document.querySelector('input[name="drink_flavour"]:checked');
    if (selectedDrink) {
        totalPrice += parseInt(selectedDrink.dataset.price || 0) * quantity;
    }

    const priceElement = document.getElementById("deal-cart-price");
    if (priceElement) {
        priceElement.textContent = `Rs. ${totalPrice}`;
    } else {
        console.error("Element with id 'deal-cart-price' not found.");
    }
}

function handleDealCartButtonClick() {
    const pizzaVariationElement = document.querySelector('input[name="pizza_variation"]:checked');
    const pizzaAddonElement = document.querySelector('input[name="addon_option"]:checked');
    const drinkFlavourElement = document.querySelector('input[name="drink_flavour"]:checked');

    const isPizzaRequired = variationsArray.length > 0;
    const isDrinkRequired = drinksArray.length > 0;

    if (isPizzaRequired && !pizzaVariationElement) {
        showAlert("Please select a pizza variation.");
        return;
    }

    if (isDrinkRequired && !drinkFlavourElement) {
        showAlert("Please select a drink flavour.");
        return;
    }
    const name = document.getElementById("deal_popup-dealName").innerText;
    const title_value = document.getElementById("deal_popup-title").innerText;
    const imageSrc = document.getElementById("deal_popup-img").src;

    const pizza_variation = pizzaVariationElement ? pizzaVariationElement.value : "";
    const pizza_topping = pizzaAddonElement ? pizzaAddonElement.value : "";
    const drink_flavour = drinkFlavourElement ? drinkFlavourElement.value : "";

    const [topping_name, topping_price] = pizza_topping.split("-");
    const topping_price_int = parseInt(topping_price) || 0;

    let dealPrice = document.getElementById("deal-original-price");
    dealPrice.textContent = parseInt(dealPrice.textContent.replace("Rs. ", ""));
    const OriginalPrice = dealPrice.textContent;

    function replacePlaceholdersAndFormatSize(originalName, pizzaVariation, drinkVariation) {
        let products = originalName.split(",").map((item) => item.trim());
        let updatedProducts = products.map((item) => {
            const [quantity, ...nameParts] = item.split(" ");

            let nameAndSize = nameParts.join(" ");
            const sizeMatch = nameAndSize.match(/\(([^)]+)\)/);
            const size = sizeMatch ? sizeMatch[1] : "";
            const name = nameAndSize.replace(/\s*\([^)]+\)/, "").trim();

            let updatedName;

            if (variationsArray.includes(name)) {
                updatedName = `${quantity} ${size} ${pizzaVariation}`;
                if (!pizzaVariation.toLowerCase().includes("pizza")) {
                    updatedName += " pizza";
                }
            } else if (drinksArray.includes(name)) {
                updatedName = `${quantity} ${size} ${drinkVariation}`;
            } else {
                updatedName = `${quantity} ${size} ${name}`;
            }

            return updatedName;
        });
        return updatedProducts.join(", ");
    }

    const updatedName = replacePlaceholdersAndFormatSize(name, pizza_variation, drink_flavour);

    const price = parseInt(document.getElementById("deal-cart-price").innerText.replace("Rs. ", "").replace(",", ""));
    const quantity = parseInt(document.getElementById("quantity").innerText);

    let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

    let existingCartItemIndex = -1;
    cartItems.forEach((item, index) => {
        if (item.name === updatedName && (item.variation === pizza_variation || item.variation === topping_name)) {
            existingCartItemIndex = index;
        }
    });

    const cartItem = {
        name: updatedName,
        type: "deal",
        originalPrice: parseInt(OriginalPrice) + topping_price_int,
        price: price,
        quantity: quantity,
        imgSrc: imageSrc,
        variation: topping_name,
        variationPrice: topping_price_int,
        topping: [],
    };

    if (existingCartItemIndex !== -1) {
        cartItems[existingCartItemIndex].quantity += quantity;
        cartItems[existingCartItemIndex].price = parseInt(cartItems[existingCartItemIndex].originalPrice) * cartItems[existingCartItemIndex].quantity;
    } else {
        cartItems.push(cartItem);
    }

    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    showMessage();
    closeDealAddToCart();
}

function closeDealAddToCart() {
    document.querySelectorAll('input[name="pizza_variation"]').forEach((radio) => {
            radio.checked = false;
        });

    document.querySelectorAll('input[name="addon_option"]').forEach((checkbox) => {
            checkbox.checked = false;
        });

    document.querySelectorAll('input[name="drink_flavour"]').forEach((checkbox) => {
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
    document.getElementById("deal-original-price").textContent = '';
    document.getElementById("deal-cart-price").textContent = '';

    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("dealPopup").style.display = "none";
    document.body.style.overflow = "auto";
}
