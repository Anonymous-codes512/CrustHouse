<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap");

        body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            user-select: none;
            background-color: #ececec;
            font-family: "Poppins", sans-serif;
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100vh;
        }

        form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            box-shadow: 0px 1px 3px #3a3a3a;
            padding: 20px;
            width: 90%;
            max-width: 500px;
            border-radius: 0.50rem;
        }

        form h2 {
            display: flex;
            width: 90%;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #ffbb00;
        }

        .passwordfield {
            display: flex;
            margin: 10px 0;
            font-family: "Poppins", sans-serif;
            border: 1px solid #8d8d8d;
            border-radius: 5px;
            width: 100%;
        }

        .passwordfield input {
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            width: 95%;
            line-height: 2;
        }

        .passwordfield i {
            border-left: 1px solid #8d8d8d;
            padding: 5px 10px;
            line-height: 2;
            background-color: #cdcdcd;
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
        }

        #fgt-btn {
            width: 90%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        #fgt-btn button {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1rem;
            margin: 10px auto;
            width: 95%;
            border: none;
            color: #fbfbfb;
            background-color: #ffbb00;
            font-weight: 400;
            text-align: center;
            border-radius: 0.25rem;
            cursor: pointer;
            padding: .75rem 2.5rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
        }

        #fgt-btn button:hover {
            background-color: #b08309;
        }

        .error-message {
            color: #f45b69;
            font-size: 0.8rem;
            margin: 0px;
        }

        .success-message {
            color: #1e9752d7;
            font-size: 0.8rem;
            margin: 0px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            form {
                width: 80%;
            }

            #fgt-btn button {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            form {
                width: 75%;
                padding: 15px;
            }

            form h2 {
                font-size: 1.5rem;
            }

            #fgt-btn button {
                font-size: 1rem;
            }

            .passwordfield input {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <form action="{{ route('customerResetPassword') }}" method="post" enctype="multipart/form-data">
            @csrf
            <h2>Reset password</h2>
            <input type="hidden" value="{{ $email }}" name="email">
            <div style="display: flex; flex-direction: column; width:85%; margin:auto;">
                <label for="password">Password&nbsp;<span style="color: red">*</span></label>
                <div class="passwordfield">
                    <input type="password" id="password" name="password" placeholder="Password" autocomplete="off"
                        required oninput="validatePassword()">
                    <i class='bx bxs-show' onclick="showAndHidePswd()"></i>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; width:85%; margin:auto;">
                <label for="cnfrmPswd">Confirm Password&nbsp;<span style="color: red">*</span></label>
                <div class="passwordfield CnfrmPswdField">
                    <input type="password" id="cnfrmPswd" name="password_confirmation" placeholder="Confirm Password"
                        autocomplete="off" oninput="validatePassword()" required>
                    <i class='bx bxs-show' onclick="showAndHideCnfrmPswd()"></i>
                </div>
                <div id="password-error-message" class="error-message" style="display: none;"></div>
            </div>
            <div id='fgt-btn'>
                <button id="reset-btn" type="submit">Reset</button>
            </div>
        </form>
    </div>
</body>

<script>
    function validatePassword() {
        let password = document.getElementById('password').value;
        let confirmPassword = document.getElementById('cnfrmPswd').value;
        let message = document.getElementById('password-error-message');
        let btn = document.getElementById('reset-btn');

        if (password.length < 8) {
            message.textContent = "Password must be at least 8 characters long!";
            message.className = "error-message";
            message.style.display = "block";
            btn.disabled = true;
            btn.style.cursor = 'not-allowed';
        } else if (password !== confirmPassword) {
            message.textContent = "Passwords do not match!";
            message.className = "error-message";
            message.style.display = "block";
            btn.disabled = true;
            btn.style.cursor = 'not-allowed';
        } else {
            message.textContent = "Passwords match!";
            message.className = "success-message";
            message.style.display = "block";
            btn.disabled = false;
            btn.style.cursor = 'pointer';
            setTimeout(() => {
                message.style.display = "none";
            }, 1000);
        }
    }

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
</script>

</html>
