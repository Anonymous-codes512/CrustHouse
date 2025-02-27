<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="dynamic-title">@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('CSS/Manager/manager.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="{{ asset('Images/Web_Images/chlogo.png') }}" type="image">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>

<body>
    @include('Components.Loader')
    <div class="container">
        @php
            $id = session('id');
            $branch_id = session('branch_id');
            $posLogo = false;
            $profile_pic = false;
            $user_name = false;
            if ($ThemeSettings) {
                $posLogo = $ThemeSettings->pos_logo;

                $branch = $ThemeSettings->branch;
                $users = $branch->users;
                foreach ($users as $user) {
                    if ($user->role == 'branchManager') {
                        $profile_pic = $user->profile_picture;
                        $user_name = $user->name;
                    }
                }
            }
        @endphp
        {{--      
            |---------------------------------------------------------------|
            |======================== Warning Overlay ======================|
            |---------------------------------------------------------------|
        --}}
        @if (session()->has('warning'))
            <div id="warningOverlay" class="warningOverlay"></div>
            <div id="warning" class="warning">
                <p style="text-align:center;">
                    {{ session('warning') }}
                </p>
                <button id="warning-close" type="button" onclick="hideWarning()">Ok</button>
            </div>
            <script>
                function hideWarning() {
                    document.getElementById('warningOverlay').style.display = 'none';
                    document.getElementById('warning').style.display = 'none';
                };
            </script>
        @endif
        <nav>
            <div class="logo">
                @if ($posLogo)
                    <img src="{{ asset('Images/Logos/' . $posLogo) }}" alt="Logo Here"
                        onclick="setActiveMenus('menu1','{{ route('managerdashboard', [$id, $branch_id]) }}')"
                        style="cursor: pointer;">
                @else
                    <img src="{{ asset('Images/image-1.png') }}" alt="Logo Here"
                        onclick="setActiveMenus('menu1','{{ route('managerdashboard', [$id, $branch_id]) }}')"
                        style="cursor: pointer;">
                @endif
            </div>

            <div class="menuList">
                <i class='bx bx-menu' id="menuIcon" onclick="toggleMenu()"></i>
                <div class="menu" id="menu">
                    <div class="menuItems active" id="menu1">
                        <i class='bx bxs-dashboard'></i>
                        <a href="{{ route('managerdashboard', [$id, $branch_id]) }}" style="text-decoration: none;"
                            onclick="setActiveMenus('menu1','{{ route('managerdashboard', [$id, $branch_id]) }}')">
                            <p class="link">Dashboard</p>
                        </a>
                    </div>
                    <div class="menuItems" id="menu2">
                        <i class='bx bxs-category'></i>
                        <a {{-- href="{{ route('viewCategoryPage', [$id, $branch_id]) }}" --}} style="text-decoration: none;"
                            onclick="setActiveMenus('menu2','{{ route('viewCategoryPage', [$id, $branch_id]) }}')">
                            <p class="link">Categories</p>
                        </a>
                    </div>
                    <div class="menuItems" id="menu3">
                        <i class='bx bx-package'></i>
                        <a {{-- href="{{ route('viewProductPage', [$id, $branch_id]) }}" --}} style="text-decoration: none;"
                            onclick="setActiveMenus('menu3','{{ route('viewProductPage', [$id, $branch_id]) }}')">
                            <p class="link">Products</p>
                        </a>
                    </div>
                    <div class="menuItems" id="menu4">
                        <i class='bx bxs-dock-bottom bx-rotate-180'></i>
                        <a {{-- href="{{ route('viewDealPage', [$id, $branch_id]) }}"--}} style="text-decoration: none;"
                            onclick="setActiveMenus('menu4','{{ route('viewDealPage', [$id, $branch_id]) }}')">
                            <p class="link">Deals</p>
                        </a>
                    </div>
                    <div class="menuItems" id="menu5">
                        <i class='bx bxs-store'></i>
                        <a {{-- href="{{ route('viewStockPage', [$id, $branch_id]) }}" --}} style="text-decoration: none;"
                            onclick="setActiveMenus('menu5','{{ route('viewStockPage', [$id, $branch_id]) }}')">
                            <p class="link">Stock</p>
                        </a>
                    </div>
                    <div class="menuItems" id="menu6">
                        <i class='bx bx-cookie'></i>
                        <a {{-- href="{{ route('viewRecipePage', [$id, $branch_id]) }}"--}} style="text-decoration: none;"
                            onclick="setActiveMenus('menu6','{{ route('viewRecipePage', [$id, $branch_id]) }}')">
                            <p class="link">Recipe</p>
                        </a>
                    </div>
                    <div class="menuItems" id="menu7">
                        <i class='bx bxs-file-import'></i>
                        <a {{-- href="{{ route('viewOrdersPage', [$id, $branch_id]) }}" --}} style="text-decoration: none;"
                            onclick="setActiveMenus('menu7','{{ route('viewOrdersPage', [$id, $branch_id]) }}')">
                            <p class="link">Orders</p>
                        </a>
                    </div>
                    <div class="menuItems" id="menu8">
                        <i class='bx bxs-group'></i>
                        <a {{-- href="{{ route('viewStaffPage', [$id, $branch_id]) }}"--}} style="text-decoration: none;"
                            onclick="setActiveMenus('menu8','{{ route('viewStaffPage', [$id, $branch_id]) }}')">
                            <p class="link">My Staff</p>
                        </a>
                    </div>

                    <div class="menuItems" id="menu9">
                        <i class='bx bxs-group'></i>
                        <a {{-- href="{{ route('viewDineInPage', $branch_id) }}"--}} style="text-decoration: none;"
                            onclick="setActiveMenus('menu9','{{ route('viewDineInPage', $branch_id) }}')">
                            <p class="link">Dine-In</p>
                        </a>
                    </div>

                    <div class="menuItems" id="menu10">
                        <i class='bx bxs-report'></i>
                        <a {{--href="{{ route('report', $branch_id) }}"--}} style="text-decoration: none;"
                            onclick="setActiveMenus('menu10','{{ route('report',  $branch_id) }}')">
                            <p class="link">Reports</p>
                        </a>
                    </div>

                    <div class="menuItems" id="menu11">
                        <i class='bx bx-dots-vertical-rounded'></i>
                        <a {{--href="{{ route('viewSettingsPage', [$id, $branch_id]) }}"--}} style="text-decoration: none;"
                            onclick="setActiveMenus('menu11','{{ route('viewSettingsPage', [$id, $branch_id]) }}')">
                            <p class="link">More</p>
                        </a>
                    </div>
                </div>
            </div>
            <script>
                function setActiveMenus(menuId, route) {
                    document.cookie = "activeMenu=" + menuId + "; path=/";
                    document.querySelectorAll('.menuItems').forEach(item => {
                        item.classList.remove('active');
                    });
                    document.getElementById(menuId).classList.add('active');
                    // window.location.href = route;
                    showLoader(route);
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

        <div class="rgtpnl">
            <header id="header">
                {{-- <div class="searchbar">
                    <i class='bx bx-search'></i>
                    <input type="text" id="search" value="" placeholder="Search">
                </div> --}}

                <div class="profilepanel">
                    <div class="profile">
                        <div class="profilepic">
                            @if ($profile_pic)
                                <img src="{{ asset('Images/UsersImages/' . $profile_pic) }}" alt="Profile Picture">
                            @else
                                <img src="{{ asset('Images/Rectangle 3463281.png') }}" alt="Profile Picture">
                            @endif
                        </div>
                        @if ($user_name)
                            <p class="profilename">{{ $user_name }}</p>
                        @else
                            <p class="profilename">User Name</p>
                        @endif
                    </div>
                    @if ($ThemeSettings)
                        <input type="hidden" id="branch_name"
                            value="{{ $branch->branch_name . ' - ' . $branch->branch_city }}">
                    @endif
                    @php
                        $notifications = session('Notifications');
                    @endphp
                    {{-- <div id="notify-logout"> --}}
                    @if (!$notifications || $notifications->isEmpty())
                        <div class="notification">
                            <i class='bx bxs-bell' title="notifications" onclick="toggleNotification()"></i>
                            <div id="notificationBox" class="notificationBox">
                                <p id="heading">Notifications</p>
                                <div class="message">
                                    <p>No new notifications</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="notification">
                            <i class='bx bxs-bell-ring bx-tada' style="color: #ffbb00;" title="notifications"
                                onclick="toggleNotification()"></i>
                            <div id="notificationBox" class="notificationBox">
                                <p id="heading">Notifications</p>

                                @foreach ($notifications as $notification)
                                    <div class="message">
                                        <a href="{{ route('redirectNotification', [$id, $branch_id, $notification->id]) }}"
                                            style="text-decoration:none; color:black;">
                                            <p>{{ $notification->message }}</p>
                                        </a>
                                        <div class="buttons">
                                            <a
                                                href="{{ route('readNotification', [$id, $branch_id, $notification->id]) }}"><i
                                                    class='bx bxs-book-reader' title="Read"></i></a>
                                            <a
                                                href="{{ route('deleteNotification', [$id, $branch_id, $notification->id]) }}"><i
                                                    class='bx bxs-trash' title="Delete"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <a class="logout" onclick="logout('{{ route('logout') }}')">
                        <i class='bx bx-log-out-circle' title="logout" style="cursor:pointer;"></i>
                    </a>
                    {{-- </div> --}}
                </div>
            </header>

            <script>
                function logout(route) {
                    const cookies = document.cookie.split(";");
                    for (let i = 0; i < cookies.length; i++) {
                        const cookie = cookies[i];
                        const eqPos = cookie.indexOf("=");
                        const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
                        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
                    }
                    show_Loader();
                    window.location.href = route;
                }
            </script>

            @yield('main')

        </div>
    </div>
    <script src="{{ asset('JavaScript/index.js') }}"></script>
    <script>
        function toggleNotification() {
            const notificationBox = document.getElementById('notificationBox');
            if (notificationBox.style.display === "flex") {
                notificationBox.style.display = "none";
            } else {
                notificationBox.style.display = "flex";
            }
        }

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
    @stack('scripts')
</body>

</html>
