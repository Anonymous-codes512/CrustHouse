@extends('Components/Rider')
@section('main')
    @php
        $id = $rider_id;
        $user_id = $user_id;
        $branch_id = $branch_id;
        $branch = $branch;
        $user = $branch->users->where('id', $user_id)->first();
    @endphp
    <main class="dashboard-content">
        <!-- Profile Photo Section -->
        <h2>Profile Photo</h2>
        <section class="profile-photo-section">
            <form action="{{ route('updateProfilePicture') }}" method="POST" id="profile-photo-form"
                enctype="multipart/form-data" onsubmit="show_Loader()">
                @csrf
                <input type="hidden" name="rider_id" value="{{ $user_id }}">
                <div class="profile-photo-container">
                    <!-- Display Current Profile Photo -->
                    @if ($user->profile_picture)
                        <img id="current-profile-photo" src="{{ asset('Images/UsersImages/' . $user->profile_picture) }}"
                            alt="Profile Photo" width="100">
                    @else
                        <p>No profile photo available</p>
                    @endif
                    <!-- "Change" Button to Show File Input -->
                    <div>

                        <button type="button" id="change-photo-btn"
                            onclick="document.getElementById('profile-photo').click();">Change</button>
                        <button type="submit">Update</button>
                    </div>
                    <input type="file" id="profile-photo" name="profile_photo" accept="image/*" style="display:none;"
                        onchange="updateImagePreview()">
                </div>
            </form>
        </section>

        <h2>Rider Profile</h2>
        <section class="profile-section">
            <form action="{{ route('updateRiderProfile') }}" method="POST" id="profile-form" enctype="multipart/form-data" onsubmit="show_Loader()">
                @csrf
                <input type="hidden" name="rider_id" value="{{ $user_id }}">

                <div class="form-grid">
                    <div>
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ $user->name }}" readonly>
                    </div>
                    <div>
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ $user->email }}" readonly>
                    </div>
                    <div>
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone_number" value="{{ $user->phone_number }}"
                            placeholder="+923000000000" maxlength="13" pattern="\+923[0-9]{9}" required>
                    </div>
                    <div>
                        <label for="vehicle">Motorbike Number</label>
                        <input type="text" id="vehicle" name="motorbike_number"
                            value="{{ $user->rider->motorbike_number ?? '' }}" maxlength="9" pattern="^[A-Z]{2,4}-\d{2,4}$"
                            placeholder="LED-1024" required>
                    </div>
                    <div>
                        <label for="license">License Number</label>
                        <input type="text" id="license" name="license_number" maxlength="10"
                            pattern="^[A-Z]{3}-\d{4,7}$" value="{{ $user->rider->license_number ?? '' }}" placeholder="DD-2244"
                            required>
                    </div>
                </div>
                <button type="submit">Save Changes</button>
            </form>
        </section>

    </main>

    <script>
        function updateImagePreview() {
            const fileInput = document.getElementById('profile-photo');
            const file = fileInput.files[0];
            const imagePreview = document.getElementById('current-profile-photo');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
