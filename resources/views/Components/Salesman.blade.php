<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="An POS fro a salesman for ordering in place orders.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="dynamic-title"></title>
    <link rel="stylesheet" href="{{ asset('CSS/Salesman/salesman.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="{{ asset('Images/Web_Images/chlogo.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('styles')
</head>

<body>
    @php
        $posLogo = false;
        $profile_pic = false;
        $user_name = false;
        if ($ThemeSettings) {
            $posLogo = $ThemeSettings->pos_logo;

            $branch = $ThemeSettings->branch;
            $branchName = $branch->branch_name . ' - ' . $branch->branch_city;
            $users = $branch->users;
            foreach ($users as $user) {
                if ($user->id == $staff_id) {
                    $profile_pic = $user->profile_picture;
                    $user_name = $user->name;
                }
            }
        }
    @endphp
    <div class="container"> 
        <header id="header">
            <div class="logo">
                @if ($posLogo)
                    <img src="{{ asset('Images/Logos/' . $posLogo) }}" alt="Logo Here">
                @else
                    <img src="{{ asset('Images/image-1.png') }}" alt="Logo Here">
                @endif
            </div>
            <input type="hidden" id="branch_name" value="{{ $branchName }}">
            <div id="centerDiv">
                <button id="dineIn-btn" type="button" onclick="showDineInOrders()">Dine-In Orders</button>
                <button id="online-btn" type="button" onclick="showOnlineOrders()">Online Orders</button>
                <button id="allOrders-btn" type="button" onclick="showAllOrders()">All Orders</button>
                <div class="search_bar_div">
                    <input type="text" id="search_bar" name="search" placeholder="Search on current page."
                        style="background-image: url('{{ asset('Images/search.png') }}');">
                </div>
            </div>

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
                    @endif
                </div>

                {{-- <div class="notification">
                    <i class='bx bx-bell'></i>
                </div> --}}

                <div class="logout">
                    <a href="{{ route('logout') }}"><i class='bx bx-log-out' onclick="logout()"></i></a>
                </div>

                {{-- <div class="theme">
                    <i class='bx bx-moon' id="theme" onclick="toggleTheme()"></i>
                </div> --}}
            </div>
        </header>

        @yield('main')

    </div>
    <script>
        function logout() {
            document.cookie = "selected_category=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT";
            window.location.href = "{{ route('logout') }}";
        }
    </script>
    <script src="{{ asset('JavaScript/index.js') }}"></script>
    @stack('scripts')

</body>

</html>
