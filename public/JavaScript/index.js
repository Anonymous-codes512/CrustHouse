function toggleMenu() {
    let menu = document.getElementById('menu')
    let icon = document.getElementById('menuIcon')
    if (menu.style.display === 'none') {
        menu.style.display = 'flex'
        icon.classList.remove('bx-menu')
        icon.classList.add('bx-x')
    } else {
        menu.style.display = 'none'
        icon.classList.remove('bx-x')
        icon.classList.add('bx-menu')
    }
}

var resizeCanvas = function() {
    var graphContainers = document.querySelectorAll('.orderGraph, .revenueGraph');
    graphContainers.forEach(function(container) {
        var canvas = container.querySelector('canvas');
        var width = container.offsetWidth;
        canvas.style.height = (width * 0.8) + 'px';
    });
};
window.addEventListener('resize', resizeCanvas);
resizeCanvas();

// My Staff
let texts = document.getElementsByClassName('status');
Array.from(texts).forEach(text => {
    if (text.textContent.toLowerCase() === "active") {
        text.style.color = '#3FC28A';
    } else if (text.textContent.toLowerCase() === "inactive") {
        text.style.color = '#F45B69';
    }
});

// Add new Member
function popup() {
    let overlay = document.getElementById('overlay');
    let popup = document.getElementById('newRider');

    overlay.style.display = 'block';
    popup.style.display = 'flex';
}

function closePopup() {
    let overlay = document.getElementById('overlay');
    let popup = document.getElementById('newRider');

    overlay.style.display = 'none';
    popup.style.display = 'none';
}

// Add new table
function addTable() {
    let overlay = document.getElementById('overlay');
    let popup = document.getElementById('newTable');

    overlay.style.display = 'block';
    popup.style.display = 'flex';
}

function closeAddTable() {
    let overlay = document.getElementById('overlay');
    let popup = document.getElementById('newTable');

    overlay.style.display = 'none';
    popup.style.display = 'none';
}

// Dining Table
let avaliable = document.getElementsByClassName('table-avaliablity');
Array.from(avaliable).forEach(avaliablity => {
    if (avaliablity.textContent.toLowerCase() === "avaliable") {
        avaliablity.style.color = '#3FC28A';
    } else if (avaliablity.textContent.toLowerCase() === "not avaliable") {
        avaliablity.style.color = '#F45B69';
    }
});

function showAndHidePswd() {
    let pswd = document.getElementById('password');
    if (pswd.type === 'password') {
        pswd.type = 'text';
    } else {
        pswd.type = 'password';
    }
}

function showAndHideCnfrmPswd() {
    let cnfrmPswd = document.getElementById('cnfrmPswd');
    if (cnfrmPswd.type === 'password') {
        cnfrmPswd.type = 'text';
    } else {
        cnfrmPswd.type = 'password';
    }
}