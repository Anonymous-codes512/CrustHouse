function showAddToCart(product, deals, allProducts) {
    const overlay = document.getElementById("overlay");
    const popup = document.getElementById("addToCart");

    if (!overlay || !popup) return;

    const drinkFlavour = document.getElementById("drinkFlavour");
    const prodName = document.getElementById("prodName");
    const headTitle = document.getElementById("headTitle");
    const prodPriceSpan = document.getElementById("prodPrice");
    const productId = document.getElementById("product_id");
    const price = document.getElementById("price");
    const totalPrice = document.getElementById("totalprice");
    const prodVariation = document.getElementById("prodVariation");
    const addons = document.getElementById("addons");
    const addOnsLabel = document.getElementById("addOnsLabel");
    const prodVariationLabel = document.getElementById("prodVariationLabel");

    if (product.category_name) {
        if (drinkFlavour) drinkFlavour.style.display = "none";
        if (productId) productId.value = product.id;
        if (prodName)
            prodName.value = `${product.productVariation} ${product.productName}`;
        headTitle.textContent = `${product.productName}`;
        prodPriceSpan.textContent = `${product.productVariation}`;
        if (price) price.value = `Rs. ${product.productPrice}`;
        if (totalPrice) totalPrice.value = `Rs. ${product.productPrice}`;

        if (product.category_name.toLowerCase() != "others") {
            if (prodVariation) prodVariation.style.display = "block";
            if (addons) addons.style.display = "block";
            updateProductSizeDropdown(product, allProducts);
        } else {
            if (prodName) prodName.textContent = product.productName;
            if (addons) addons.style.display = "none";
            if (addOnsLabel) addOnsLabel.style.display = "none";
            if (prodVariation) prodVariation.style.display = "none";
            if (prodVariationLabel) prodVariationLabel.style.display = "none";
        }
    } else {
        if (prodName) prodName.value = product.deal.dealTitle;
        headTitle.textContent = product.deal.dealTitle;
        if (productId) productId.value = product.deal_id;
        if (price) price.value = `Rs. ${product.deal.dealDiscountedPrice}`;
        if (totalPrice)
            totalPrice.value = `Rs. ${product.deal.dealDiscountedPrice}`;
        if (drinkFlavour) drinkFlavour.style.display = "block";
        updateDealsDropdown(product, deals, allProducts);
    }

    overlay.style.display = "block";
    popup.style.display = "flex";
}

/* 
|---------------------------------------------------------------|
|================ Deal's DropdownFunctions =====================|
|---------------------------------------------------------------|
*/

function updateDealsDropdown(deal, deals, allProducts) {
    console.log(deal);
    deal_price = deal.deal.dealDiscountedPrice;

    let pizzaFlavourDropdown = document.getElementById("prodVariation");
    let addOnsDropdown = document.getElementById("addons");
    const drinkFlavourDropdown = document.getElementById("drinkFlavour");

    clearDropdown(pizzaFlavourDropdown);
    clearDropdown(drinkFlavourDropdown);
    clearDropdown(addOnsDropdown);

    let addOnsArray = [];
    let pizzaFlavour = [];
    let drinkFlavour = [];
    let dealProductName = [];
    let dealProductVariations = [];

    let isPizza = false;
    let categories = [];

    deals.forEach((element) => {
        if (element.deal_id === deal.deal_id) {
            const product = element.product;
            if (product) {
                categories.push(product.category_name);
                dealProductName.push(product.productName);
                dealProductVariations.push(product.productVariation);
            } else {
                console.warn("Deal", deal.id, "has no associated product");
            }
        }
    });

    allProducts.forEach((element) => {
        if (
            element.category_name.toLowerCase() === "addons" &&
            element.productName.includes("Topping")
        ) {
            addOnsArray.push(
                `${element.productName} ${element.productVariation} (Rs. ${element.productPrice})`
            );
        }
    });

    deals.forEach((element) => {
        if (deal.deal_id === element.deal_id) {
            const product = element.product;
            if (product.category_name.toLowerCase() == "pizza") {
                isPizza = true;
            }
        }
    });

    console.log(isPizza);
    let pizza_name;
    for (let i = 0; i < dealProductName.length; i++) {
        let found = false;
        allProducts.forEach((element) => {
            if (
                element.category_name.toLowerCase() === "pizza" &&
                element.productVariation === dealProductVariations[i]
            ) {
                pizza_name = dealProductName[i];
                pizzaFlavour.push(element.productName);
                found = true;
            }
        });
        if (found) break;
    }

    let drink_name;
    let variation;
    for (let i = 0; i < dealProductName.length; i++) {
        let found = false;

        allProducts.forEach((element) => {
            if (element.category_name.toLowerCase() === "drinks") {
                if (element.productName === dealProductName[i]) {
                    variation = dealProductVariations[i];
                }

                if (element.productVariation === variation) {
                    drink_name = dealProductName[i];
                    drinkFlavour.push(element.productName);
                    found = true;
                }
            }
        });

        if (found) break;
    }

    if (isPizza) {
        PizzaFlavourDropdown(pizzaFlavour, pizzaFlavourDropdown, pizza_name);
        DrinkFlavourDropdown(drinkFlavour, drinkFlavourDropdown, drink_name);
        addOnsDealDropdown(addOnsArray, addOnsDropdown, deal_price);
    } else {
        pizzaFlavourDropdown.style.display = "none";
        document.getElementById("prodVariationLabel").style.display = "none";
        addOnsDropdown.style.display = "none";
        document.getElementById("addOnsLabel").style.display = "none";
        DrinkFlavourDropdown(drinkFlavour, drinkFlavourDropdown, drink_name);
    }
}

function clearDropdown(dropdown) {
    if (dropdown) dropdown.innerHTML = "";
}

function PizzaFlavourDropdown(optionsArray, dropdown, name) {
    if (!dropdown) return;
    dropdown.innerHTML = "";
    const label = document.getElementById("prodVariationLabel");
    if (label) label.textContent = "Select Pizza Flavour";

    let defaultOption = document.createElement("option");
    defaultOption.text = "Default";
    defaultOption.value = name;
    dropdown.add(defaultOption);

    optionsArray.forEach((optionText) => {
        let option = document.createElement("option");
        option.text = optionText;
        option.value = optionText;
        dropdown.add(option);
    });
}

function DrinkFlavourDropdown(optionsArray, dropdown, name) {
    if (!dropdown) return;
    dropdown.innerHTML = "";
    const label = document.getElementById("drinkFlavourLabel");

    if (dropdown) dropdown.style.display = "block";
    if (label) label.style.display = "block";

    if (!name) {
        if (dropdown) dropdown.style.display = "none";
        if (label) label.style.display = "none";
        return;
    }

    dropdown.innerHTML = "";
    if (label) label.textContent = "Select Drink Flavour";

    let defaultOption = document.createElement("option");
    defaultOption.text = "Default";
    defaultOption.value = name;
    dropdown.add(defaultOption);

    optionsArray.forEach((optionText) => {
        let option = document.createElement("option");
        option.text = optionText;
        option.value = optionText;
        dropdown.add(option);
    });
}

function addOnsDealDropdown(optionsArray, dropdown, deal_price) {
    if (!dropdown) return;
    dropdown.innerHTML = "";
    const label = document.getElementById("addOnsLabel");
    if (label) label.textContent = "Select Extra Topping";

    let defaultOption = document.createElement("option");
    defaultOption.text = "None";
    defaultOption.value = "";
    dropdown.add(defaultOption);

    optionsArray.forEach((optionText) => {
        let option = document.createElement("option");
        option.text = optionText;
        option.value = optionText;
        dropdown.add(option);
    });

    dropdown.addEventListener("change", () => {
        const quantityElement = document.getElementById("prodQuantity");
        const totalPriceElement = document.getElementById("totalprice");
        const priceElement = document.getElementById("price");

        if (quantityElement) quantityElement.value = "1";

        let totalPrice = 0;
        let selectedOption = dropdown.options[dropdown.selectedIndex];
        let addonPrice =
            parseFloat(
                (selectedOption.value.match(/Rs\. (\d+)/) || [0, 0])[1]
            ) || 0;
        let variationPrice = parseFloat(deal_price.replace("Rs. ", "")) || 0;

        totalPrice = variationPrice + addonPrice;

        if (totalPriceElement)
            totalPriceElement.value = "Rs. " + totalPrice.toFixed(2) + " Pkr";
        if (priceElement)
            priceElement.value = "Rs. " + totalPrice.toFixed(2) + " Pkr";
    });
}

/*
|---------------------------------------------------------------|
|================ others Dropdown Functions ====================|
|---------------------------------------------------------------|
*/

function updateProductSizeDropdown(product, allProducts) {
    let productVariationDropdown = document.getElementById("prodVariation");
    let drinkFlavourDropdown = document.getElementById("addons");
    let addOnsLabel = document.getElementById("addOnsLabel");
    let prodVariationLabel = document.getElementById("prodVariationLabel");

    productVariationDropdown.innerHTML = "";
    drinkFlavourDropdown.innerHTML = "";
    addOnsLabel.style.display = "none";
    prodVariationLabel.style.display = "none";

    if (product.category_name.toLowerCase() === "pizza") {
        handlePizzaCategory(
            product,
            allProducts,
            productVariationDropdown,
            drinkFlavourDropdown
        );
    } else if (product.category_name.toLowerCase() === "drinks") {
        handleDrinksCategory(
            product,
            allProducts,
            productVariationDropdown,
            drinkFlavourDropdown
        );
    } else {
        handleOtherCategories(
            product,
            allProducts,
            productVariationDropdown,
            drinkFlavourDropdown
        );
    }
}

function handlePizzaCategory(
    product,
    allProducts,
    productVariationDropdown,
    drinkFlavourDropdown
) {
    product.value;
    let productVariations = [];
    let addOnsArray = [];

    document.getElementById("prodVariationLabel").style.display = "block";

    allProducts.forEach((element, key) => {
        if (
            element.category_name.toLowerCase() === "addons" &&
            element.productName.includes("Topping")
        ) {
            addOnsArray.push(
                `${element.productName}  (Rs. ${element.productPrice})`
            );
        }
        if (element.productName.toLowerCase() === product.productName.toLowerCase()) {
            productVariations.push(
                `${element.id}-${element.productVariation} (Rs. ${element.productPrice})`
            );
        }
    });

    addOnsDropdown(addOnsArray, drinkFlavourDropdown, "Extra Topping");
    addOptionsToDropdown(productVariations, productVariationDropdown);
}

function handleDrinksCategory(
    product,
    allProducts,
    productVariationDropdown,
    drinkFlavourDropdown
) {
    let productVariations = [];
    let uniqueDrinkFlavours = new Set();

    document.getElementById("addOnsLabel").style.display = "none";
    document.getElementById("addons").style.display = "none";
    document.getElementById("drinkFlavourLabel").style.display = "none";
    document.getElementById("prodVariationLabel").style.display = "block";

    allProducts.forEach((element) => {
        if (element.category_name.toLowerCase() === "drinks") {
            uniqueDrinkFlavours.add(element.productName);
        }
        if (
            element.productName.toLowerCase() ===
            product.productName.toLowerCase()
        ) {
            productVariations.push(
                `${element.id}-${element.productVariation} (Rs. ${element.productPrice})`
            );
        }
    });

    let drinkFlavour = Array.from(uniqueDrinkFlavours);
    // addOnsDropdown(drinkFlavour, drinkFlavourDropdown, 'Drink Flavour');
    addOptionsToDropdown(productVariations, productVariationDropdown);

    drinkFlavourDropdown.addEventListener("change", () => {
        let selectedFlavour = drinkFlavourDropdown.value;
        let filteredVariations = allProducts.filter(
            (product) =>
            product.category_name.toLowerCase() === "drinks" &&
            product.productName === selectedFlavour
        );

        if (filteredVariations.length > 0) {
            let selectedProduct = filteredVariations[0];
            let variationOptions = filteredVariations.map(
                (product) =>
                `${product.productVariation} (Rs. ${product.productPrice})`
            );
            document.getElementById("price").value =
                "Rs. " + selectedProduct.productPrice;
            document.getElementById("totalprice").value =
                "Rs. " + selectedProduct.productPrice;
            addOptionsToDropdown(variationOptions, productVariationDropdown);
        }
    });
}

function handleOtherCategories(
    product,
    allProducts,
    productVariationDropdown,
    drinkFlavourDropdown
) {
    let productVariations = [];
    document.getElementById("addons").style.display = "none";
    document.getElementById("drinkFlavourLabel").style.display = "none";
    allProducts.forEach((element) => {
        if (
            element.productName.toLowerCase() ===
            product.productName.toLowerCase()
        ) {
            productVariations.push(
                `${element.id}-${element.productVariation} (Rs. ${element.productPrice})`
            );
        }
    });

    addOptionsToDropdown(productVariations, productVariationDropdown);
}

function addOptionsToDropdown(optionsArray, dropdown) {
    dropdown.innerHTML = "";
    let label = document.getElementById("prodVariationLabel");
    label.textContent = "Select Variation";
    let productId = document.getElementById("product_id");
    let parts, variation, product_names;
    let prodName = document.getElementById("prodName");
    let product_names_array = prodName.value.split(" ");

    // let defaultOption = document.createElement("option");
    // defaultOption.text = product_names_array[0];
    // defaultOption.value = optionsArray[1];
    // dropdown.add(defaultOption);

    for (let i = 0; i < optionsArray.length; i++) {
        let option = document.createElement("option");
        parts = optionsArray[i].split("-");
        variation = parts[1].split(" ");
        option.text = parts[1];
        option.value = parts.join("-");
        dropdown.add(option);
    }

    dropdown.addEventListener("change", () => {
        document.getElementById("prodQuantity").value = "1";
        let selectedOption = dropdown.options[dropdown.selectedIndex];
        let selectedOptionParts = selectedOption.value.split("-");
        let selectedvariation = selectedOptionParts[1].split(" ")[0];

        product_names_array[0] = product_names_array[0].replace(
            product_names_array[0].split(" ")[0],
            selectedvariation
        );

        product_names = product_names_array.join(" ");
        prodName.value = product_names;
        productId.value = selectedOptionParts[0];
        document.getElementById("prodPrice").textContent =
            product_names_array[0];
        let totalPriceElement = document.getElementById("totalprice");
        let match = selectedOption.value.match(/Rs\. (\d+)/);
        let price = match ? match[1] : 0;

        let addonsoption = document.getElementById("addons");
        let price1 = 0;
        if (addonsoption.value != "") {
            let match1 = addonsoption.value.match(/Rs\. (\d+)/);
            price1 = match1 ? match1[1] : 0;
        }

        const order_price = parseFloat(price) + parseFloat(price1);
        totalPriceElement.value = "Rs. " + order_price;
        document.getElementById("price").value = "Rs. " + order_price;
    });
}

function addOnsDropdown(optionsArray, dropdown, labeltext) {
    dropdown.innerHTML = "";

    let label = document.getElementById("addOnsLabel");
    label.style.display = "block";
    label.textContent = labeltext;

    let defaultOption = document.createElement("option");
    defaultOption.text = `Select ${labeltext}`;
    defaultOption.value = "";
    defaultOption.disabled = true;
    defaultOption.selected = true;
    dropdown.add(defaultOption);

    for (let i = 0; i < optionsArray.length; i++) {
        let option = document.createElement("option");
        option.text = optionsArray[i];
        option.value = optionsArray[i];
        dropdown.add(option);
    }

    dropdown.addEventListener("click", () => {
        document.getElementById("prodQuantity").value = "1";
        let totalPriceElement = document.getElementById("totalprice");
        let selectedOption = dropdown.options[dropdown.selectedIndex];
        let match = selectedOption.value.match(/Rs\. (\d+)/);
        let price = match ? match[1] : 0;
        if (selectedOption.value == "") {
            price = 0;
        }
        let variationprice = document.getElementById("prodVariation");
        let match1 = variationprice.value.match(/Rs\. (\d+)/);
        let price1 = match1 ? match1[1] : 0;

        const order_price = parseFloat(price) + parseFloat(price1);
        totalPriceElement.value = "Rs. " + order_price;
        document.getElementById("price").value = "Rs. " + order_price;
    });
}

/*
|---------------------------------------------------------------|
|==================== Add to Cart Functions ====================|
|---------------------------------------------------------------|
*/

// let allAddedProducts = [];
// let index = 1;

// function add(allProducts) {
//     let productName = document.getElementById('prodName').textContent.trim();
//     let product = productName.split(" ");
//     let prod = product[0];
//     productName = productName.replace(prod, "");

//     let productVariation = document.getElementById('prodVariation').value;
//     let addOns = document.getElementById('addons').value;
//     let productPrice = parseFloat(document.getElementById('totalprice').textContent.replace('Rs. ', ''));
//     let quantity = document.getElementById('prodQuantity').value;

//     let extractedText;

//     let pTag = document.createElement('p');
//     pTag.style.borderBottom = '1px solid #000';
//     pTag.id = 'order' + index;

//     let textarea = document.createElement('textarea');
//     textarea.readOnly = true;
//     textarea.style.resize = 'none';
//     textarea.rows = '3';
//     textarea.cols = '4';
//     textarea.style.width = "95%";
//     textarea.style.height = "auto";
//     textarea.style.border = 'none';

//     let divQuantity = document.createElement('div');
//     divQuantity.style.display = "flex";
//     divQuantity.style.alignItems = "center";
//     divQuantity.style.marginBottom = "5px";

//     let quantityInput = document.createElement('input');
//     quantityInput.type = 'number';
//     quantityInput.id = 'OrderQuantity' + index;
//     quantityInput.name = 'OrderQuantity' + index;
//     quantityInput.style.width = '30px';
//     quantityInput.style.textAlign = 'center';
//     quantityInput.value = quantity;

//     let increaseIcon = document.createElement('i');
//     increaseIcon.style.fontSize = '2vw';
//     increaseIcon.style.color = '#d40000';
//     increaseIcon.className = 'bx bxs-plus-square';

//     let decreaseIcon = document.createElement('i');
//     decreaseIcon.style.fontSize = '2.5vw';
//     decreaseIcon.style.color = '#d40000';
//     decreaseIcon.className = 'bx bxs-checkbox-minus';

//     divQuantity.appendChild(decreaseIcon);
//     divQuantity.appendChild(quantityInput);
//     divQuantity.appendChild(increaseIcon);

//     pTag.appendChild(textarea);
//     pTag.appendChild(divQuantity);

//     document.getElementById('selectedProducts').appendChild(pTag);

//     if (!addOns) {
//         let productDetails = productVariation.replace(/\s+/g, '') + productName;
//         extractedText = productDetails.replace(/\(.*?\)/, '');
//         extractedText = extractedText.trim();

//     } else {
//         let productDetails = productVariation.replace(/\s+/g, '') + productName + ' with extra ' + addOns.replace(/\s*\(Rs\.\s*\d+\)\s*/, "");
//         allProducts.forEach(element => {
//             if (element.productName == addOns) {
//                 if (element.category_name.toLowerCase() == 'drinks') {
//                     productName = addOns;
//                     productDetails = productVariation.replace(/\s+/g, '') + ' ' + addOns;
//                 }
//             }
//         });
//         extractedText = productDetails.replace(/\(.*?\)/, '');
//         extractedText = extractedText.trim();
//     }

//     textarea.textContent = productName.replace(/^ /, "") + '\n' + extractedText;
//     extractedText = '';

//     let totalSpan = document.createElement('span');
//     totalSpan.style.marginLeft = '3rem';
//     totalSpan.id = 'orderItemPrice' + index;
//     totalSpan.style.fontSize = '0.8rem';
//     totalSpan.textContent = 'Total: Rs. ' + productPrice.toFixed(2);
//     divQuantity.appendChild(totalSpan);

//     let totalBillString = document.getElementById('totalbill').value;
//     let totalBillValue;

//     if (totalBillString.startsWith("Total Bill:")) {
//         totalBillValue = parseFloat(totalBillString.split("Rs. ")[1]);
//     } else {
//         totalBillValue = parseFloat(totalBillString);
//     }

//     let variationName = (productVariation && productVariation.match(/^[^\(]+/)) ? productVariation.match(/^[^\(]+/)[0].trim() : '';
//     let productObj = {
//         name: productName,
//         variation: variationName,
//         addons: addOns.replace(/\s*\(Rs\.\s*\d+\)\s*/, ""),
//         price: productPrice,
//         quantity: quantityInput.value.replace(/\s+/g, ' ')
//     };

//     allAddedProducts.push(productObj);
//     let hiddenInput = document.createElement('input');
//     hiddenInput.type = 'hidden';
//     hiddenInput.id = 'hidden-field ' + index;
//     hiddenInput.name = 'product' + index;
//     hiddenInput.value = JSON.stringify(productObj);
//     document.getElementById('cart').appendChild(hiddenInput);

//     let productId = generateProductId();

//     let currentTotal = totalBillValue + productPrice;
//     document.getElementById('totalbill').value = "Total Bill:\t\t Rs. " + currentTotal.toFixed(2);
//     index++;

//     increaseIcon.setAttribute('onclick', `increaseWhole('${productId}', '${quantityInput.id}', '${totalSpan.id}', '${hiddenInput.id}')`);
//     decreaseIcon.setAttribute('onclick', `decreaseWhole('${pTag.id}', '${productId}', '${quantityInput.id}', '${totalSpan.id}', '${hiddenInput.id}')`);

//     closeAddToCart();

//     document.getElementById('prodVariation').value = '';
//     document.getElementById('addons').value = '';
//     document.getElementById('prodQuantity').value = '1';

//     sessionStorage.setItem('selectedProducts', JSON.stringify(allAddedProducts));
// }

// function generateProductId() {
//     const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
//     const length = 8;
//     let productId = '';
//     for (let i = 0; i < length; i++) {
//         productId += characters.charAt(Math.floor(Math.random() * characters.length));
//     }
//     return productId;
// }

/*
|---------------------------------------------------------------|
|============== Increment Decrement Functions ==================|
|---------------------------------------------------------------|
*/

function increase() {
    let quantityInput = document.getElementById("prodQuantity");
    let currentValue = parseInt(quantityInput.value);
    currentValue = currentValue + 1;
    quantityInput.value = currentValue; // Update the input value

    let productPriceElement = document.getElementById("price");
    let numericValue = productPriceElement.value.match(/\d+(\.\d+)?/);
    let totalPrice = parseFloat(numericValue[0]);
    totalPrice = totalPrice * currentValue;

    let totalPriceElement = document.getElementById("totalprice");
    totalPriceElement.value = "Rs. " + totalPrice.toFixed(2);
}

function decrease() {
    let quantityInput = document.getElementById("prodQuantity");
    let currentValue = parseInt(quantityInput.value);

    if (currentValue <= 1) {
        // alert('Minimum quantity should be 1.');
    } else {
        currentValue = currentValue - 1;
        quantityInput.value = currentValue;

        let productPriceElement = document.getElementById("price");
        let numericValue = productPriceElement.value.match(/\d+(\.\d+)?/);
        let unitPrice = parseFloat(numericValue[0]);

        let totalPrice = unitPrice * currentValue;

        let totalPriceElement = document.getElementById("totalprice");
        totalPriceElement.value = "Rs. " + totalPrice.toFixed(2);
    }
}

/*
|---------------------------------------------------------------|
|================ Print and Proceed Functions ==================|
|---------------------------------------------------------------|
*/

function printReceipt() {
    let heading = document.getElementById("heading").cloneNode(true);
    let selectedProducts = document
        .getElementById("selectedProducts")
        .cloneNode(true);
    let totalbill = document.getElementById("totalbill").cloneNode(true);
    totalbill.style.fontSize = "3vw";
    document.body.querySelectorAll("*").forEach((element) => {
        if (
            element.id !== "heading" &&
            element.id !== "selectedProducts" &&
            element.id !== "totalbill"
        ) {
            element.style.display = "none";
        }
    });
    document.body.appendChild(heading);
    document.body.appendChild(selectedProducts);
    document.body.appendChild(totalbill);
    window.print();
    heading.remove();
    selectedProducts.remove();
    totalbill.remove();
    document.body.querySelectorAll("*").forEach((element) => {
        element.style.display = "";
    });
}

function closeAddToCart() {
    const overlay = document.getElementById("overlay");
    const popup = document.getElementById("addToCart");
    const quantityElement = document.getElementById("prodQuantity");
    if (quantityElement) quantityElement.value = "1";
    if (overlay) overlay.style.display = "none";
    if (popup) popup.style.display = "none";
}