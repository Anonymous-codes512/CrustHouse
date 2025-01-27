const http = new XMLHttpRequest();
const apiKey = "4b18909ba78c4a439b61458413f4f159";

let formattedAddress = "";
let city = "";
let state = "";
let district = "";

// Show and hide location popup
function ShowLocationPopup() {
    document.getElementById("popupOverlay").style.display = "block";
    document.getElementById("selectLocationPopup").style.display = "flex";
    document.body.style.overflow = "hidden";
}

function hideLocationPopup() {
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("selectLocationPopup").style.display = "none";
    document.body.style.overflow = "auto";
}

// Select location and validate input
function SelectLocation() {
    const districtInput = document.getElementById("district").value.trim();
    const addressInput = document.getElementById("address").value.trim();

    if (addressInput) {
        document.getElementById("location-message").style.display = "none";
        selectLocation(formattedAddress);
        hideLocationPopup();
    } else {
        showError("Please select the location first.");
    }
}

// Find user's current location using Geolocation API
function findMyLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                const { latitude, longitude } = position.coords;
                const apiURL = `https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key=${apiKey}`;
                getAPI(apiURL);
            },
            err => {
                console.error("Error:", err.message);
                alert("Error: " + err.message);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

// Make API request to get location data
function getAPI(apiURL) {
    http.open("GET", apiURL);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState === 4) {
            if (this.status === 200) {
                const result = JSON.parse(this.responseText);
                if (result.results && result.results.length > 0) {
                    const locationData = result.results[0].components;
                    district = locationData.county;
                    city = locationData.municipality;
                    state = locationData.state;
                    formattedAddress = result.results[0].formatted;
                    updateLocationFields(district, formattedAddress);
                } else {
                    console.log("No results found");
                    showError("Unable to find location details.");
                }
            } else {
                console.error("API request failed:", this.statusText);
                showError("Failed to fetch location. Please try again.");
            }
        }
    };
}

// Update input fields with location data
function updateLocationFields(district, formattedAddress) {
    console.log(district);
    console.log(formattedAddress);
    
    document.querySelector("#district").value = district || "";
    document.querySelector("#address").value = formattedAddress || "";
}

// Show error message to user
function showError(message) {
    const errorMessage = document.getElementById("location-message");
    errorMessage.style.display = "block";
    errorMessage.style.fontSize = "1rem";
    errorMessage.innerText = message;
    setTimeout(() => {
        errorMessage.style.display = "none";
    }, 1500);
}

// Save location and update UI
function selectLocation(formattedAddress) {
    const locationDisplay = document.querySelector("#addr");
    locationDisplay.textContent = formattedAddress;
    localStorage.setItem("savedLocation", formattedAddress);
    initializeLoginData();
}

// Initialize login data in localStorage
function initializeLoginData() {
    const loginData = {
        loginStatus: false,
        signupStatus: false,
        isGuest : null,
        email: null,
        LoginTime: null
    };
    localStorage.setItem("LoginStatus", JSON.stringify(loginData));
}

// Check if there's a saved location in localStorage
function checkSavedLocation() {
    return localStorage.getItem("savedLocation");
}

// Handle page load
document.addEventListener("DOMContentLoaded", function () {
    const savedLocation = checkSavedLocation();
    if (!savedLocation) {
        ShowLocationPopup();
    } else {
        hideLocationPopup();
        document.querySelector("#addr").textContent = savedLocation;
    }
});

