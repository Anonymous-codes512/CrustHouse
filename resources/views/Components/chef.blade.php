<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="dynamic-title"></title>
    <link rel="stylesheet" href="{{ asset('CSS/Chef/chef.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="{{ asset('Images/Web_Images/chlogo.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>

<body>
    @include('Components.Loader')
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
                if ($user->id == $user_id) {
                    $profile_pic = $user->profile_picture;
                    $user_name = $user->name;
                }
            }
        }
    @endphp

    <div class="containers">
        <header id="header">
            <div class="logo">
                @if ($posLogo)
                    <img src="{{ asset('Images/Logos/' . $posLogo) }}" alt="Logo Here">
                @else
                    <img src="{{ asset('Images/image-1.png') }}" alt="Logo Here">
                @endif
            </div>
            
            <input type="hidden" id="branch_name" value="{{ $branchName }}">

            <div class="profilepanel">
                <div class="profile">
                    <div class="profilepic">
                        @if ($profile_pic)
                            <img src="{{ asset('Images/UsersImages/' . $profile_pic) }}"
                                alt="Profile Picture">
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
                    <a onclick="showLoader('{{ route('logout') }}')" style="cursor: pointer;"><i class='bx bx-log-out'></i></a>
                </div>

            </div>
        </header>

        @yield('main')

    </div>

    <script>
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
    <script src="{{ asset('JavaScript/index.js') }}"></script>
    @stack('scripts')

</body>

</html>
