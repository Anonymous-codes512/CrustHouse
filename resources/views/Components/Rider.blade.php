<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="dynamic-title">@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('CSS/Rider/rider.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="{{ asset('Images/Web_Images/chlogo.png') }}" type="image">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    @stack('styles')
</head>

<body>
    @if (session('success'))
        <script type="text/javascript">
            $(document).ready(function() {
                toastr.success('{{ session('success') }}');
            });
        </script>
    @endif

    @if (session('error'))
        <script type="text/javascript">
            $(document).ready(function() {
                toastr.error('{{ session('error') }}');
            });
        </script>
    @endif
    @php
        $rider_id = $rider_id;
        $user_id = $user_id;
        $branch_id = $branch_id;
        $posLogo = false;
        $profile_pic = false;
        $user_name = false;
        if ($ThemeSettings) {
            $posLogo = $ThemeSettings->pos_logo;
            $branch = $ThemeSettings->branch;

            $currentUser = $branch->users->first(function ($user) use ($user_id) {
                return $user->id == $user_id && $user->role === 'rider';
            });

            if ($currentUser) {
                $profile_pic = $currentUser->profile_picture;
                $user_name = $currentUser->name;
            }
        }

    @endphp
    @if ($ThemeSettings)
        <input type="hidden" id="branch_name" value="{{ $branch->branch_name . ' - ' . $branch->branch_city }}">
    @endif
    <div class="container">
        <header>
            <div class="logo">
                @if ($posLogo)
                    <img src="{{ asset('Images/Logos/' . $posLogo) }}" alt="Logo Here">
                @else
                    <img src="{{ asset('Images/image-1.png') }}" alt="Logo Here">
                @endif
            </div>
            <div class="profile">
                <div class="picture">
                    @if ($profile_pic)
                        <img src="{{ asset('Images/UsersImages/' . $profile_pic) }}" alt="Profile Picture">
                    @else
                        <img src="{{ asset('Images/Rectangle 3463281.png') }}" alt="Profile Picture">
                    @endif
                </div>
                @if ($user_name)
                    <span class="username">{{ $user_name }}</span>
                @else
                    <span class="username">John Doe</span>
                @endif

                @php
                    $count = $notifications ? $notifications->count() : 0;
                @endphp

                {{-- <div id="notify-logout"> --}}
                <div class="notification">
                    <i class='bx bxs-bell' title="notifications" onclick="toggleNotification()"></i>
                    <span id="message-circle">
                        <p>{{ $count }}</p>
                    </span>
                    <div id="notificationBox" class="notificationBox">
                        <p id="heading">Notifications</p>
                        @if (!$notifications || $notifications->isEmpty())
                            <div class="message">
                                <p>No new notifications</p>
                            </div>
                        @else
                            @foreach ($notifications as $notification)
                                <div class="message">

                                    <p>{{ $notification['message'] }}</p>
                                    <div class="buttons">
                                        <a href="{{ route('markAsRead', $notification['orderNumber']) }}">
                                            <i class='bx bxs-book-reader' title="Read"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <i class='bx bx-menu' id="menuIcon" onclick="toggleMenu()"></i>
        </header>

        <div class="dashboard-container">
            <aside id="sidebar" class="sidebar">
                <nav>
                    {{-- <i class='bx bx-menu' id="menuIcon" onclick="toggleMenu()"></i> --}}
                    <div class="menu" id="menu">
                        <div class="menuItems active" id="menu1">
                            <i class="bi bi-house"></i>
                            <a href="{{ route('rider_dashboard', ['rider_id' => $user_id, 'branch_id' => $branch_id]) }}"
                                style="text-decoration: none;" onclick="setActiveMenu('menu1')">
                                <p class="link">Dashboard</p>
                            </a>
                        </div>

                        <div class="menuItems" id="menu2">
                            <i class="bi bi-clipboard2-plus"></i>
                            <a href="{{ route('riderOrders', ['rider_id' => $user_id, 'branch_id' => $branch_id]) }}"
                                style="text-decoration: none;" onclick="setActiveMenu('menu2')">
                                <p class="link">Orders</p>
                            </a>
                        </div>
                        <div class="menuItems" id="menu3">
                            <i class="bi bi-person"></i>
                            <a href="{{ route('riderProfile', ['rider_id' => $user_id, 'branch_id' => $branch_id]) }}"
                                style="text-decoration: none;" onclick="setActiveMenu('menu3')">
                                <p class="link">Profile</p>
                            </a>
                        </div>
                    </div>

                    <div class="menuItems logout" id="menuLogout">
                        <i class="bi bi-box-arrow-right"></i>
                        <a onclick="logout('{{ route('logout') }}')" style="text-decoration: none;">
                            <p>Logout</p>
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

                        document.addEventListener('DOMContentLoaded', (event) => {
                            const activeMenu = getActiveMenu();
                            if (activeMenu) {
                                document.querySelectorAll('.menuItems').forEach(item => {
                                    item.classList.remove('active');
                                });
                                document.getElementById(activeMenu).classList.add('active');
                            }
                        });

                        function getActiveMenu() {
                            const value = `; ${document.cookie}`;
                            const parts = value.split(`; activeMenu=`);
                            if (parts.length === 2) return parts.pop().split(';').shift();
                        }
                    </script>

                </nav>
            </aside>
            @yield('main')
        </div>
    </div>

    <script>
        function toggleMenu() {
            const menu = document.getElementById("sidebar");
            menuIcon = document.getElementById('menuIcon');
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'flex';
                menuIcon.classList.remove('bx-menu');
                menuIcon.classList.add('bx-x');
            } else {
                menu.style.display = 'none';
                menuIcon.classList.remove('bx-x');
                menuIcon.classList.add('bx-menu');
            }
        }

        function toggleNotification() {
            const notificationBox = document.getElementById('notificationBox');
            if (notificationBox.style.display === "flex") {
                notificationBox.style.display = "none";
            } else {
                notificationBox.style.display = "flex";
            }
        }

        function logout(route) {
            const cookies = document.cookie.split(";");
            for (let i = 0; i < cookies.length; i++) {
                const cookie = cookies[i];
                const eqPos = cookie.indexOf("=");
                const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
                document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
            }
            window.location.href = route;
        }

        function adjustMenuVisibility() {
            const menu = document.getElementById("sidebar");
            const menuIcon = document.getElementById("menuIcon");

            if (window.innerWidth > 480) {
                menu.style.display = 'flex';
                menuIcon.style.display = 'none';
            } else {
                menu.style.display = 'none';
                menuIcon.style.display = 'block';
            }
        }
        window.addEventListener('resize', adjustMenuVisibility);
        adjustMenuVisibility();
    </script>


    @stack('scripts')
</body>

</html>
