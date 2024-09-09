<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration Page</title>
    <link rel="stylesheet" href= "{{ asset('CSS/Owner/Login.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="container">
        <form action="{{ route('storeRegistrationData') }} " method="POST" enctype="multipart/form-data">
            @csrf
            <div class="heading">
                <h1>Registration Panel</h1>
            </div>
            <hr>
            <div class="inputfields">
                <input type="hidden" name="role" value="owner">
                <div class="namefield">
                    <input type="text" name="name" placeholder="Enter your full name" autocomplete="off" required>
                    <i class='bx bxs-user'></i>
                </div>

                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <div class="emailfield">
                    <input type="email" name="email" placeholder="Email" id="email" autocomplete="off"
                        oninput="validateEmail()" required>
                    <i class='bx bxs-envelope'></i>
                </div>

                <div id="email-error-message" class="error-message" style="display: none;"></div>

                <div class="passwordfield">
                    <input type="password" id="password" name="password" placeholder="Password" autocomplete="off"
                        required oninput="validatePassword()">
                    <i class='bx bxs-show' onclick="showAndHidePswd()"></i>
                </div>

                <div class="passwordfield CnfrmPswdField">
                    <input type="password" id="cnfrmPswd" name="password_confirmation" placeholder="Confirm Password"
                        autocomplete="off" oninput="validatePassword()" required>
                    <i class='bx bxs-show' onclick="showAndHideCnfrmPswd()"></i>
                </div>
                <div id="password-error-message" class="error-message" style="display: none;"></div>
            </div>

            <div class="fgtpswd">
                <a href="{{ route('viewLoginPage') }}">Already have an account?</a>
            </div>

            <div class="btn">
                <input id="submit-btn" type="submit" value="Register">
            </div>
        </form>

    </div>
    <script src="{{ asset('JavaScript/index.js') }}"></script>
    <script>
        function validateEmail() {
            let email = document.getElementById("email").value.trim();
            let emailErrorMessage = document.getElementById('email-error-message');
            let submitBtn = document.getElementById('submit-btn');
            if (!email.endsWith(".com")) {
                emailErrorMessage.style.display = 'block';
                emailErrorMessage.textContent = "Email must end with '.com'.";
                submitBtn.disabled = true;
                submitBtn.style.backgroundColor = '#c19b32';
                return;
            }
            var invalidChars = /[\*\/=\-+]/;
            if (invalidChars.test(email)) {
                emailErrorMessage.style.display = 'block';
                emailErrorMessage.textContent = "Email contains invalid characters like *, /, =.";
                submitBtn.disabled = true;
                return;
            }
            emailErrorMessage.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.style.backgroundColor = '#ffbb00';
        }

        function validatePassword() {
            let password = document.getElementById('password').value;
            let confirmPassword = document.getElementById('cnfrmPswd').value;
            let message = document.getElementById('password-error-message');

            if (password.length < 8) {
                message.textContent = "Password must be at least 8 characters long!";
                message.className = "error-message";
                message.style.display = "block";
            } else if (password !== confirmPassword) {
                message.textContent = "Passwords do not match!";
                message.className = "error-message";
                message.style.display = "block";
            } else {
                message.textContent = "Passwords match!";
                message.className = "success-message";
                setTimeout(() => {
                    message.style.display = "none";
                }, 1000);
            }
        }
    </script>
</body>

</html>
