<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crust - House | Tachyon - Dashboard</title>
    <link rel="stylesheet" href="{{ asset('CSS/Owner/owner.css') }}">
    <link rel="icon" href="{{ asset('Images/Web_Images/chlogo.png') }}" type="image/png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>

<body>
    @include('Components.Loader')

    @php
        $owner_id = session('owner_id');
        $posLogo = false;
        $profile_pic = null;
        $user_name = null;
        if (session()->has('OwnerSettings')) {
            $OwnerSettings = session('OwnerSettings');
            $posLogo = $OwnerSettings->pos_logo;
        }
        if ($ownerData) {
            $profile_pic = $ownerData->profile_picture;
            $user_name = $ownerData->name;
        }
    @endphp
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
    <header id="header">
        <div class="logo">
            <img src="{{ asset('Images/logo.png') }}" alt="Logo Here"
                onclick="setActiveMenus('menu11','{{ route('dashboard', $owner_id) }}')" style="cursor: pointer;">
        </div>

        <div class="profilepanel">
            <div class="profile">
                <div class="profilepic">
                    @if ($profile_pic)
                        <img src="{{ asset('Images/UsersImages/' . $profile_pic) }}" alt="Profile Picture"
                            onclick="updateProfile({{ json_encode($ownerData) }})" style="cursor: pointer;">
                    @else
                        <img src="{{ asset('Images/Rectangle 3463281.png') }}" alt="Profile Picture"
                            onclick="updateProfile({{ json_encode($ownerData) }})" style="cursor: pointer;">
                    @endif
                </div>
                @if ($user_name)
                    <p class="profilename" onclick="updateProfile({{ json_encode($ownerData) }})"
                        style="cursor: pointer;">
                        {{ $user_name }}</p>
                @else
                    <p class="profilename" onclick="updateProfile({{ json_encode($ownerData) }})"
                        style="cursor: pointer;">
                        Tachyon</p>
                @endif
            </div>

            <a onclick="showLoader('{{ route('logout') }}')" class="logout">
                <i class='bx bx-log-out-circle' title="logout"></i>
            </a>
        </div>
    </header>
    <div class="container">
        <nav>
            <div class="menuItems active" id="menu11">
                <i class='bx bxs-dashboard'></i>
                <a href="{{ route('dashboard', $owner_id) }}" onclick="setActiveMenu('menu11')"
                    style="text-decoration: none;">
                    <p class="link">Dashboard</p>
                </a>
            </div>

            <script>
                function setActiveMenus(menuId, route) {
                    document.cookie = "activeMenu=" + menuId + "; path=/";
                    document.querySelectorAll('.menuItems').forEach(item => {
                        item.classList.remove('active');
                    });
                    document.getElementById(menuId).classList.add('active');
                    window.location.href = route;
                }

                function setActiveMenu(menuId) {
                    document.cookie = "activeMenu=" + menuId + "; path=/";
                    document.querySelectorAll('.menuItems').forEach(item => {
                        item.classList.remove('active');
                    });
                    document.getElementById(menuId).classList.add('active');
                }

                // Function to get the active menu from cookies
                function getActiveMenu() {
                    const value = `; ${document.cookie}`;
                    const parts = value.split(`; activeMenu=`);
                    if (parts.length === 2) return parts.pop().split(';').shift();
                }

                // Set the active menu from the cookie on page load
                document.addEventListener('DOMContentLoaded', () => {
                    const activeMenu = getActiveMenu();
                    if (activeMenu) {
                        document.querySelectorAll('.menuItems').forEach(item => {
                            item.classList.remove('active');
                        });
                        document.getElementById(activeMenu).classList.add('active');
                    }
                });

                function showLoader(route) {
                    document.getElementById('loaderOverlay').style.display = 'block'; // Show the overlay
                    document.getElementById('loaderOverlay').style.zIndex = '9999';
                    document.getElementById('loader').style.display = 'flex'; // Show the loader spinner
                    document.getElementById('loader').style.zIndex = '10000';
                    window.location.href = route; // Redirect after showing the loader
                }

                function show_Loader() {
                    document.getElementById('loaderOverlay').style.display = 'block'; // Show the overlay
                    document.getElementById('loaderOverlay').style.zIndex = '9999';
                    document.getElementById('loader').style.display = 'flex';
                    document.getElementById('loader').style.zIndex = '10000';
                }

                function hide_Loader() {
                    document.getElementById('loaderOverlay').style.display = 'none';
                    document.getElementById('loader').style.display = 'none';
                }

                window.addEventListener("beforeunload", function() {
                    show_Loader();
                });

                document.addEventListener("DOMContentLoaded", function() {
                    hide_Loader()
                });
            </script>
        </nav>
        <div class="rgtPnl">
            @yield('main')
        </div>
    </div>
</body>

</html>
