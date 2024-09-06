// 33.786080201690936, 72.72

const http = new XMLHttpRequest();
const apiKey = "4b18909ba78c4a439b61458413f4f159";

document.querySelector(".btnloc").addEventListener("click", () => {
    findmylocation();
});

let formattedAddress = "";
let city = "";
let state = "";
let district = "";

function changeLocation() {
    document.getElementById("overlay").style.display = "block";
    let frontModel = document.getElementById("frontModel");
    frontModel.classList.remove("hidden");
    frontModel.style.display = "flex";
    document.body.style.overflow = "hidden";
}

function SelectLocation() {
    var district = document.getElementById("district").value.trim();
    var address = document.getElementById("address").value.trim();

    if (district && address) {
        var frontModel = document.querySelector(".frontmodel");
        frontModel.classList.add("hidden");
        toggleClass(".whole", "active");
        document.body.style.overflow = "initial";
        selectLocation(formattedAddress);
        document.getElementById("location-message").style.display = "none";
        document.getElementById("overlay").style.display = "none";
    } else {
        let error_message = document.getElementById("location-message");
        error_message.style.display = "block";
        error_message.style.fontSize = "1.1rem";
        error_message.style.margin = "5px";
        error_message.innerText = "Please select the location first.";
        setTimeout(() => {
            error_message.style.display = "none";
        }, 1500);
    }
}

function findmylocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const Api = `https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key=${apiKey}`;
                getAPI(Api);
            },
            (err) => {
                console.error("Error:", err.message);
                alert("Error: " + err.message);
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0,
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function getAPI(Api) {
    http.open("GET", Api);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const result = JSON.parse(this.responseText);
            console.log(result);
            if (result.results && result.results.length > 0) {
                district = result.results[0].components.county;
                city = result.results[0].components.municipality;
                state = result.results[0].components.state;
                formattedAddress = result.results[0].formatted;
                document.querySelector("#district").value = district;
                document.querySelector("#address").value = formattedAddress;
            } else {
                console.log("No results found");
            }
        } else if (this.readyState == 4) {
            console.error("API request failed:", this.statusText);
        }
    };
}

function selectLocation(formattedAddress) {
    const hello = document.querySelector("#addr");
    hello.textContent = formattedAddress;
    localStorage.setItem("savedLocation", formattedAddress);
    let loginData = { loginStatus: false, signupStatus: false, email: null, LoginTime: null };
    localStorage.setItem("LoginStatus", JSON.stringify(loginData));
}

function closeFrontModel() {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("frontModel").style.display = "none";
}

window.onload = async function() {
    const savedLocation = await checkSavedLocation();
    if (!savedLocation) {
        document.getElementById("overlay").style.display = "block";
        document.getElementById("frontModel").style.display = "flex";
    } else {
        closeFrontModel();
        document.querySelector("#addr").textContent = savedLocation;
    }
};

async function checkSavedLocation() {
    return new Promise((resolve) => {
        const savedLocation = localStorage.getItem("savedLocation");
        resolve(savedLocation);
    });
}