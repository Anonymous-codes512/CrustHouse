<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Page</title>
    <link rel="stylesheet" href= "{{ asset('CSS/Owner/Login.css') }}">
    {{-- <link rel="shortcut icon" href="{{ asset('Images/favicon.ico')}}" type="image/x-icon"> --}}
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    @include('Components.Loader')
    @if (session('success'))
        <div id="success" class="alert alert-success">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('success').classList.add('alert-hide');
            }, 1500);

            setTimeout(() => {
                document.getElementById('success').style.display = 'none';
            }, 2000);
        </script>
    @endif

    @if (session('error'))
        <div id="error" class="alert alert-danger">
            {{ session('error') }}
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('error').classList.add('alert-hide');
            }, 1500);

            setTimeout(() => {
                document.getElementById('error').style.display = 'none';
            }, 2000);
        </script>
    @endif
    <div class="container">
        @error('forgot')
            <div class="error-message">{{ session('email') }}</div>
        @enderror
        <form action="{{ route('login') }}" method="POST" enctype="multipart/form-data"  onsubmit="show_Loader()">
            @csrf
            <div class="heading">
                <h1>Login Panel</h1>
            </div>
            <hr>
            <p>Sign in to start your session</p>
            <div class="inputfields">
                <div class="emailfield">
                    <input type="email" name="email" placeholder="Email" autocomplete="off" required>
                    <i class='bx bxs-envelope'></i>
                </div>

                <div class="passwordfield">
                    <input type="password" id="password" name="password" placeholder="Password" autocomplete="off"
                        required>
                    <i class='bx bxs-show' onclick="showAndHidePswd()"></i>
                </div>

                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="fgtpswd">
                <a href="{{ route('viewForgotPassword') }}">I forgot my Password.</a>
                @if (!$users)
                    <a href="{{ route('viewRegisterPage') }}">Don't have an account?</a>
                @endif
            </div>

            <div class="btn">
                <input type="submit" value="Login">
            </div>
        </form>
    </div>
    <script src="{{ asset('JavaScript/index.js') }}"></script>
</body>

<script>
    function showForgotPassword() {
        document.getElementById('overlay').style.display = "block";
        document.getElementById('forgotOverlay').style.display = "flex";
    }

    function closeForgotPassword() {
        document.getElementById('overlay').style.display = "none";
        document.getElementById('forgotOverlay').style.display = "none";
        window.location.reload();
    }
    function show_Loader() {
            document.getElementById('loaderOverlay').style.display = 'block'; // Show the overlay
            document.getElementById('loaderOverlay').style.zIndex = '9999'; 
            document.getElementById('loader').style.display = 'flex'; 
            document.getElementById('loader').style.zIndex = '10000'; 
        }
</script>

</html>
