<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password</title>
    <link rel="shortcut icon" href="{{ asset('Images/icon-512.png')}}" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");
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

.error-message {
    color: #f45b69;
    font-size: 0.8rem;
    margin: 0px 20px;
}


/* forgot Password */

#overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: block;
    z-index: 999;
}

#forgotOverlay {
    display: flex;
    flex-direction: row;
    background-color: #ffffff;
    position: absolute;
    border-radius: 10px;
    width: 80vw;
    height: 90vh;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
}

#form,
#image {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 50%;
    height: 95%;
    margin: auto;
}

#form {
    height: 100%;
    align-items: flex-start;
    background-color: #ececec;
    font-size: 1.2rem;
    border-radius: 10px 0 0 10px;
}

#form form {
    border: none;
    background-color: #ececec;
    box-shadow: none;
    margin-left: 20px;
    width: 90%;
}

#form form h2,
h5 {
    width: 90%;
    padding: 0;
    margin: 5px 10px;
}

#email-div {
    padding: 0 10px 0;
    margin: 5px 0;
    display: flex;
    flex-direction: column;
    width: 90%;
    font-size: 1rem;
}

#email {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #353535;
    border-radius: 0.5rem;
    font-size: 16px;
    background-color: #fbfbfb;
    background-position: 10px 10px;
    background-size: 20px;
    background-repeat: no-repeat;
    padding: 12px 20px 12px 40px;
}

#backBtn {
    display: flex;
    color: #444A5B;
    padding: 0;
    margin: 10px;
    width: 90%;
    font-size: 0.9rem;
    text-decoration: none;
    transition: color 0.3s ease-in-out, font-size 0.3s ease-in-out;
}

#backBtn:hover {
    color: #ffbb00;
    font-size: 1.1rem;
    text-decoration: underline;
    cursor: pointer;
}

#fgt-btn {
    width: 90%;
    display: flex;
    justify-content: center;
    align-items: center;
}

#fgt-btn button {
    display: flex;
    font-size: 1.2vw;
    margin: 0.5vw;
    border: none;
    color: #000;
    background-color: #ffbb00;
    text-decoration: none;
    font-weight: 400;
    white-space: nowrap;
    text-align: center;
    vertical-align: middle;
    border-radius: 0.25rem;
    cursor: pointer;
    padding: .75rem 2.5rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

#fgt-btn button:hover {
    background-color: #a97e06;
    color: #ffffff
}

#image img {
    display: flex;
    width: 90%;
}
</style>
<body>
    <div class="container">
        <div id="overlay"></div>
        <div id="forgotOverlay">

            <div id="form">
                <form action="{{ route('customerSendPasswordReset') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <h2>Forgot Password</h2>
                    <h5>We'll email you instructions on how to reset your password.</h5>

                    <div id="email-div">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="admin@gmail.com..."
                            style="background-image: url('{{ asset('Images/OnlineOrdering/message.png') }}');">
                    </div>

                    @if (session('error'))
                        <div id="error"class="error-message">
                            {{ session('error') }}
                        </div>
                        <script>
                            setTimeout(() => {
                                document.getElementById('error').classList.add('alert-hide');
                            }, 2000);

                            setTimeout(() => {
                                document.getElementById('error').style.display = 'none';
                            }, 3000);
                        </script>
                    @endif

                    <a href="{{ route('onlineOrderPage') }}" id="backBtn">Return to Home Screen.</a>
                    <div id='fgt-btn'>
                        <button type="submit">Reset</button>
                    </div>
                </form>
            </div>
            <div id="image">
                <img src="{{ asset('Images/OnlineOrdering/undraw_Forgot_password_re_hxwm.png') }}" alt="">
            </div>
        </div>
</body>

</html>
