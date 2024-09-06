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

    productDetails = dealProducts
        .map(
            (product) =>
            `${product.product_quantity} ${product.product.productName} (${product.product.productVariation}) `
        )
        .join(", ");
    // console.log(productDetails);
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

    let addons = {
        addonVariation: pizzaAddon,
        addonPrice: pizzaAddonPrice,
    };
    handleSimpleDeals(dealProducts, deal.deal, pizzaVariation, drinkVariation, addons);
    // Create the deal object to store in the cart
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
        type: 'deal',
        originalPrice: deal.dealDiscountedPrice,
        price: deal.dealDiscountedPrice,
        quantity: 1,
        imgSrc: `Images/DealImages/${deal.dealImage}`,
        variation: null,
        variationPrice: null,
        topping: []
    };

    if (existingCartItemIndex !== -1) {
        cartItems[existingCartItemIndex].quantity += 1;
        cartItems[existingCartItemIndex].price =
            parseInt(cartItems[existingCartItemIndex].originalPrice) * cartItems[existingCartItemIndex].quantity;
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
    const dealcartbtn = document.getElementById("dealaddcart");

    const overlay = document.getElementById("overlay");

    popupImg.src = `Images/DealImages/${deal.dealImage}`;
    popupTitle.innerText = deal.dealTitle;
    popupPrice.innerText = `Rs. ${deal.dealDiscountedPrice.replace(
        /pkr\s*/i,
        ""
    )}`;
    document.getElementById("originalprice").textContent = deal.dealDiscountedPrice.replace(/pkr\s*/i, "");
    document.getElementById("cart-price").textContent = `Rs. ${deal.dealDiscountedPrice.replace(/pkr\s*/i, "")}`;

    dealcartbtn.innerHTML = `
        <div>
            <span id="originalpricee" style="display: none;">${deal.dealDiscountedPrice.replace(
                /pkr\s*/i,
                ""
            )}</span>
            <span id="cart-pricee">${deal.dealDiscountedPrice.replace(
                /pkr\s*/i,
                ""
            )}</span>
        </div>
        <div>Add to Cart</div>
    `;

    overlay.style.display = "block";
    popup.style.display = "block";
    document.body.classList.add("no-scroll");
    document.body.style.overflow = "hidden";

    // document.getElementById("closeDealButton").style.display = "block";
    document.querySelector(".popwhole").style.pointerEvents = "none";

    if (
        (pizzaVariation == null || pizzaVariation.length === 0) &&
        (pizzaAddon["addonVariation"] == null ||
            pizzaAddon["addonVariation"].length === 0) &&
        (drinkVariation == null || drinkVariation.length === 0)
    ) {
        // showAlert("No deal to show.");
        document.getElementById("extra_55").style.display = "none";
        document.getElementById("extra_45").style.display = "none";
        document.getElementById("extra_65").style.display = "none";
    } else {
        if (
            pizzaVariation != null &&
            pizzaVariation.length > 0 &&
            pizzaAddon != null &&
            pizzaAddon.addonVariation.length > 0
        ) {
            dealPizzaVariation(pizzaVariation);
            dealPizzaAddons(pizzaAddon);
            dealDrinks(drinkVariation);

            document.getElementById("extra_55").style.display = "flex";
            document.getElementById("extra_45").style.display = "flex";
            document.getElementById("extra_65").style.display = "flex";
        } else if (drinkVariation != null && drinkVariation.length > 0) {
            dealDrinks(drinkVariation);
            document.getElementById("extra_55").style.display = "none";
            document.getElementById("extra_45").style.display = "none";
            document.getElementById("extra_65").style.display = "flex";
        }
    }

    // const dealObject = {
    //     dealName: deal.dealTitle,
    //     dealPrice: deal.dealDiscountedPrice,
    //     dealOriginalPrice: deal.dealDiscountedPrice,
    //     dealProduct: productDetails,
    //     dealQuantity: quantity,
    // };

    // let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

    // // Create a cartItem specifically for the deal
    // const cartItem = {
    //     name: null, // Nullify regular item properties
    //     originalPrice: null,
    //     price: null,
    //     quantity: null,
    //     imgSrc: null,
    //     variation: null,
    //     variationPrice: null,
    //     topping: null,
    //     deal: dealObject // Store the deal object here
    // };

    // cartItems.push(cartItem);
    // console.log("carted items : ", cartItem);
    // localStorage.setItem("cartItems", JSON.stringify(cartItems));
}

function dealPizzaVariation(pizzaVariation) {
    // console.log(pizzaVariation);

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
            document
                .querySelectorAll('input[name="addon"]')
                .forEach((checkbox) => {
                    checkbox.checked = false;
                });
            updatePricee(radioInput);
        };
        if (index === 0) {
            updatePricee(radioInput);
        }
        radioDiv.appendChild(radioInput);
        dropdown2.appendChild(radioDiv);

        const dropdown3 = document.createElement("div");
        dropdown3.className = "dropdown_3";

        const sizeSpan = document.createElement("span");
        sizeSpan.className = "sizee";
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
        // checkboxInput.value = addonPriceDeal[index];
        checkboxInput.dataset.price = addonPriceDeal[index];
        checkboxInput.onclick = () => {
            updatePricee(checkboxInput);
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
    // console.log(drinkFlavour);
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
        checkboxInput.onclick = () => {
            updatePricee(checkboxInput);
        };
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

function increaseQuantityy() {
    quantity++;
    updateQuantityDisplay();
    updatePricee(); // Update price based on new quantity
}

function decreaseQuantityy() {
    if (quantity > 1) {
        // Prevent quantity from going below 1
        quantity--;
        updateQuantityDisplay();
        updatePricee(); // Update price based on new quantity
    }
}

function updateQuantityDisplay() {
    document.getElementById("quantityy").textContent = quantity;
}

function updatePricee() {
    // Get the base price from the element
    const basePrice = parseInt(
        document.getElementById("originalpricee").textContent
    );
    let totalPrice = basePrice * quantity;

    // Add price of selected pizza variation
    const selectedPizzaVariation = document.querySelector(
        'input[name="option"]:checked'
    );
    if (selectedPizzaVariation) {
        totalPrice +=
            parseInt(selectedPizzaVariation.dataset.price || 0) * quantity;
    }

    // Add price of selected pizza addons
    const selectedAddons = document.querySelectorAll(
        'input[name="addon"]:checked'
    );
    if (selectedAddons.length > 0) {
        selectedAddons.forEach((addon) => {
            totalPrice += parseFloat(addon.dataset.price || 0) * quantity;
        });
    } else {
        // No addons selected, default to 0
        totalPrice += 0;
    }
    // Add price of selected drinks
    const selectedDrink = document.querySelector(
        'input[name="drink_flavour"]:checked'
    );
    if (selectedDrink) {
        totalPrice += parseInt(selectedDrink.dataset.price || 0) * quantity;
    }
    // Update the total price display
    document.getElementById("cart-pricee").textContent = `Rs. ${totalPrice}`;
}

// function handleDealCartButtonClick() {
//     const name = document.getElementById("deal_popup-dealName").innerText;
//     const title_value = document.getElementById("deal_popup-title").innerText;
//     const imageSrc = document.getElementById("deal_popup-img").src;

//     const pizzaVariationElement = document.querySelector('input[name="pizza_variation"]:checked');
//     const pizzaAddonElement = document.querySelector('input[name="addon_option"]:checked');
//     const drinkFlavourElement = document.querySelector('input[name="drink_flavour"]:checked');

//     const pizza_variation = pizzaVariationElement ? pizzaVariationElement.value : '';
//     const pizza_topping = pizzaAddonElement ? pizzaAddonElement.value : '';
//     const drink_flavour = drinkFlavourElement ? drinkFlavourElement.value : '';

//     const [topping_name, topping_price] = pizza_topping.split("-");
//     const topping_price_int = parseInt(topping_price) || 0;

//     let dealPrice = document.getElementById('cart-pricee');
//     let dealOriginalPrice = document.getElementById('originalpricee');
//     let currentOriginalPrice = parseInt(dealOriginalPrice.textContent);
//     dealOriginalPrice.textContent = currentOriginalPrice + topping_price_int;

//     let dealCurrentPrice = parseInt(dealPrice.textContent.replace("Rs. ", ''));
//     dealPrice.textContent = dealCurrentPrice + topping_price_int;

//     function replacePlaceholdersAndFormatSize(originalName, pizzaVariation, drinkVariation) {
//         let products = originalName.split(',').map(item => item.trim());
//         let updatedProducts = products.map((item) => {
//             const [quantity, ...nameParts] = item.split(' ');

//             let nameAndSize = nameParts.join(' ');
//             const sizeMatch = nameAndSize.match(/\(([^)]+)\)/);
//             const size = sizeMatch ? sizeMatch[1] : '';
//             const name = nameAndSize.replace(/\s*\([^)]+\)/, '').trim();

//             let updatedName;

//             if (variationsArray.includes(name)) {
//                 updatedName = `${quantity} ${size} ${pizzaVariation}`;
//                 if (!pizzaVariation.toLowerCase().includes('pizza')) {
//                     updatedName += ' pizza';
//                 }
//             } else if (drinksArray.includes(name)) {
//                 updatedName = `${quantity} ${size} ${drinkVariation}`;
//             } else {
//                 updatedName = `${quantity} ${size} ${name}`;
//             }

//             return updatedName;
//         });
//         return updatedProducts.join(', ');
//     }

//     const updatedName = replacePlaceholdersAndFormatSize(name, pizza_variation, drink_flavour);

//     const price = parseInt(document.getElementById("cart-price").innerText.replace("Rs. ", "").replace(",", ""));
//     const OriginalPrice = dealPrice.textContent;
//     const quantity = parseInt(document.getElementById("quantity").innerText);

//     let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

//     let existingCartItemIndex = -1;
//     cartItems.forEach((item, index) => {
//         if (item.name === updatedName && (item.variation === pizza_variation || item.variation === topping_name)) {
//             existingCartItemIndex = index;
//         }
//     });

//     const cartItem = {
//         name: updatedName,
//         type: 'deal',
//         originalPrice: OriginalPrice,
//         price: price + topping_price_int,
//         quantity: quantity,
//         imgSrc: imageSrc,
//         variation: topping_name,
//         variationPrice: topping_price_int,
//         topping: []
//     };

//     if (existingCartItemIndex !== -1) {
//         cartItems[existingCartItemIndex].quantity += quantity;
//         cartItems[existingCartItemIndex].price =
//             cartItems[existingCartItemIndex].variationPrice * cartItems[existingCartItemIndex].quantity;
//     } else {
//         cartItems.push(cartItem);
//     }

//     localStorage.setItem("cartItems", JSON.stringify(cartItems));
//     showMessage();
//     closeDealAddToCart();
// }

function handleDealCartButtonClick() {
    // Check if required options are selected
    const pizzaVariationElement = document.querySelector('input[name="pizza_variation"]:checked');
    const pizzaAddonElement = document.querySelector('input[name="addon_option"]:checked');
    const drinkFlavourElement = document.querySelector('input[name="drink_flavour"]:checked');

    const isPizzaRequired = variationsArray.length > 0; // Assume that if variationsArray is not empty, pizza variation is required
    const isDrinkRequired = drinksArray.length > 0; // Assume that if drinksArray is not empty, drink flavour is required
    let message;
    // If pizza variation is required but not selected
    if (isPizzaRequired && !pizzaVariationElement) {
        message = "Please select a pizza variation.";
        showAlert(message);
        return; // Prevent proceeding
    }

    // If pizza addons are available but none selected
    if (pizzaAddonElement && !document.querySelector('input[name="addon_option"]:checked')) {
        message = "Please select at least one pizza addon.";
        showAlert(message);
        return; // Prevent proceeding
    }

    // If drink flavour is required but not selected
    if (isDrinkRequired && !drinkFlavourElement) {
        message = "Please select a drink flavour.";
        showAlert(message);
        return; // Prevent proceeding
    }

    const name = document.getElementById("deal_popup-dealName").innerText;
    const title_value = document.getElementById("deal_popup-title").innerText;
    const imageSrc = document.getElementById("deal_popup-img").src;

    const pizza_variation = pizzaVariationElement ? pizzaVariationElement.value : '';
    const pizza_topping = pizzaAddonElement ? pizzaAddonElement.value : '';
    const drink_flavour = drinkFlavourElement ? drinkFlavourElement.value : '';

    const [topping_name, topping_price] = pizza_topping.split("-");
    const topping_price_int = parseInt(topping_price) || 0;

    let dealPrice = document.getElementById('cart-pricee');
    let dealOriginalPrice = document.getElementById('originalpricee');
    let currentOriginalPrice = parseInt(dealOriginalPrice.textContent);
    dealOriginalPrice.textContent = currentOriginalPrice + topping_price_int;

    let dealCurrentPrice = parseInt(dealPrice.textContent.replace("Rs. ", ''));
    dealPrice.textContent = dealCurrentPrice + topping_price_int;

    function replacePlaceholdersAndFormatSize(originalName, pizzaVariation, drinkVariation) {
        let products = originalName.split(',').map(item => item.trim());
        let updatedProducts = products.map((item) => {
            const [quantity, ...nameParts] = item.split(' ');

            let nameAndSize = nameParts.join(' ');
            const sizeMatch = nameAndSize.match(/\(([^)]+)\)/);
            const size = sizeMatch ? sizeMatch[1] : '';
            const name = nameAndSize.replace(/\s*\([^)]+\)/, '').trim();

            let updatedName;

            if (variationsArray.includes(name)) {
                updatedName = `${quantity} ${size} ${pizzaVariation}`;
                if (!pizzaVariation.toLowerCase().includes('pizza')) {
                    updatedName += ' pizza';
                }
            } else if (drinksArray.includes(name)) {
                updatedName = `${quantity} ${size} ${drinkVariation}`;
            } else {
                updatedName = `${quantity} ${size} ${name}`;
            }

            return updatedName;
        });
        return updatedProducts.join(', ');
    }

    const updatedName = replacePlaceholdersAndFormatSize(name, pizza_variation, drink_flavour);

    const price = parseInt(document.getElementById("cart-price").innerText.replace("Rs. ", "").replace(",", ""));
    const OriginalPrice = dealPrice.textContent;
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
        type: 'deal',
        originalPrice: OriginalPrice,
        price: price + topping_price_int,
        quantity: quantity,
        imgSrc: imageSrc,
        variation: topping_name,
        variationPrice: topping_price_int,
        topping: []
    };

    if (existingCartItemIndex !== -1) {
        cartItems[existingCartItemIndex].quantity += quantity;
        cartItems[existingCartItemIndex].price =
            parseInt(cartItems[existingCartItemIndex].originalPrice) * cartItems[existingCartItemIndex].quantity;
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

    // Reset all topping checkboxes
    document.querySelectorAll('input[name="addon_option"]').forEach((checkbox) => {
        checkbox.checked = false;
    });

    document.querySelectorAll('input[name="drink_flavour"]').forEach((checkbox) => {
        checkbox.checked = false;
    });

    // Reset labels to "Requires"
    document.querySelectorAll(".required").forEach((labelSpan) => {
        labelSpan.innerText = "Required";
        labelSpan.style.backgroundColor = "rgb(220, 53, 69)"; // Set to the original color
    });
    document.getElementById("overlay").style.display = "none";
    document.getElementById("dealPopup").style.display = "none";
    document.querySelector(".popwhole").style.pointerEvents = "auto";
    document.body.style.overflow = "initial";
}