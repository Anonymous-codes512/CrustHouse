// Utility function to toggle class
function toggleClass(elementSelector, className) {
    var element = document.querySelector(elementSelector);
    if (element) {
        element.classList.toggle(className);
    } else {
        console.error('Element not found: ' + elementSelector);
    }
}

// Front model functionality
document.addEventListener("DOMContentLoaded", function() {
    window.closeFrontModel = function() {
        var frontModel = document.querySelector(".frontmodel");
        frontModel.classList.add("hidden");
        toggleClass(".whole", "active");
        document.getElementById('overlay').style.display = "none";
        document.body.style.overflow = "initial";
    };

    document.body.style.overflow = "hidden";
});

// Open product in popup
document.addEventListener('DOMContentLoaded', (event) => {
    const imgboxes = document.querySelectorAll('.imgbox');
    const popup = document.getElementById('popup');
    const popupImg = document.getElementById('popup-img');
    const popupTitle = document.getElementById('popup-title');
    const popupPrice = document.getElementById('popup-price');
    // const dropdownRs = document.getElementById('rs');
    // const originalprice = document.getElementById('originalprice');
    // const addtocartprice = document.getElementById('cart-price');
    const overlay = document.createElement('div');
    overlay.id = 'popup-overlay';

    document.body.appendChild(overlay);

    // imgboxes.forEach((imgbox) => {
    //     const addToCartButton = imgbox.querySelector('.cartbtn');

    //     if (addToCartButton) {
    //         addToCartButton.addEventListener('click', (event) => {
    //             event.stopPropagation(); // Prevent the event from bubbling up

    //             const imgSrc = imgbox.querySelector('img').src;
    //             const productName = imgbox.querySelector('.product_name').innerText;
    //             const productPrice = imgbox.querySelector('.product_price').innerText;
    //             document.getElementById('closeButton').style.display = 'block';
    //             document.querySelector(".popwhole").style.filter = "blur(2px)";
    //             document.querySelector(".popwhole").style.pointerEvents = "none";
    //             document.body.style.overflow = "hidden";

    //             popupImg.src = imgSrc;
    //             popupTitle.innerText = productName;
    //             popupPrice.innerText = productPrice;
    //             // dropdownRs.innerText = productPrice;
    //             // originalprice.innerText = productPrice;
    //             // addtocartprice.innerText=productPrice;
    //             popup.style.display = 'block';
    //             overlay.style.display = 'block';
    //         });
    //     }
    // });

    overlay.addEventListener('click', () => {
        popup.style.display = 'none';
        overlay.style.display = 'none';

    });
});

// // Dropdown function
// function toggleDropdown() {
//     const dropdownContent = document.getElementById('dropdown-content');
//     const arrow = document.querySelector('.arrow');

//     if (dropdownContent.classList.contains('active')) {
//         dropdownContent.classList.remove('active');
//         arrow.classList.remove('active');
//     } else {
//         dropdownContent.classList.add('active');
//         arrow.classList.add('active');
//     }
// }
function toggleDropdown(element) {
    const dropdownContent = element.nextElementSibling;
    const arrow = element.querySelector('.arrow');

    // Close all other active dropdowns
    const allDropdowns = document.querySelectorAll('.dropdown-content');
    const allArrows = document.querySelectorAll('.arrow');

    allDropdowns.forEach((content) => {
        if (content !== dropdownContent) {
            content.classList.remove('active');
        }
    });

    allArrows.forEach((otherArrow) => {
        if (otherArrow !== arrow) {
            otherArrow.classList.remove('active');
        }
    });

    // Toggle the selected dropdown
    if (dropdownContent.classList.contains('active')) {
        dropdownContent.classList.remove('active');
        arrow.classList.remove('active');
    } else {
        dropdownContent.classList.add('active');
        arrow.classList.add('active');
    }

    // Close dropdown when a radio button is selected
    dropdownContent.querySelectorAll('input[type="radio"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            dropdownContent.classList.remove('active');
            arrow.classList.remove('active');
            updateLabel(element, radio);
        });
    });
    dropdownContent.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            updateLabelForToppings(element, checkbox); // Update label for toppings
        });
    });
}

function updateLabel(element, radio) {
    const labelSpan = element.querySelector('.required');
    if (labelSpan) {
        labelSpan.innerText = 'Selected';
        labelSpan.style.backgroundColor = 'rgb(139, 195, 74)';
    }
}

function updateLabelForToppings(element, checkbox) {
    const labelSpan = element.querySelector('.required');
    if (labelSpan && checkbox.checked) {
        labelSpan.innerText = 'Selected';
        labelSpan.style.backgroundColor = 'rgb(139, 195, 74)';
    }
}