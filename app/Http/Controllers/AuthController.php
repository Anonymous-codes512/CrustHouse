<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function loginIndex()
    {
        $users = User::where('role', 'owner')->first();
        return view('Auth.Login')->with(['users' => $users]);
    }

    public function registrationIndex()
    {
        return view('Auth.Registration');
    }

    public function register(Request $req)
    {
        $existingChef = User::where('role', 'chef')->where('branch_id', $req->branch)->first();
        if ($existingChef && $existingChef->role == $req->role) {
            return redirect()->back()->with('error', 'Chef Already exist in this branch');
        }

        $existingUser = User::all();
        foreach ($existingUser as $user) {
            if ($user->email == $req->input('email')) {
                return redirect()->back()->with('error', 'Email already exist');
            }
        }
        $user = new User();

        if ($req->hasFile('profile_picture')) {
            $image = $req->file('profile_picture');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Images/UsersImages'), $imageName);
            $user->profile_picture = $imageName;
        } else {
            $user->profile_picture = null;
        }

        $user->name = $req->name;
        $user->email = $req->email;
        $user->role = $req->role;
        $user->branch_id = $req->branch;
        $user->password = Hash::make($req->password);
        $user->save();

        if ($req->role !== 'owner') {
            return redirect()->back()->with('success', 'User registered successfully');
        } else {
            return redirect()->route('viewLoginPage')->with('success', 'User registered successfully');
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session(['username' => $user->name, 'profile_pic' => $user->profile_picture]);

            if ($user->role === 'owner') {
                session()->put('owner', true);
                session()->put('owner_id', $user->id);
                return redirect()->route('dashboard', ['owner_id' => $user->id]);
            } else if ($user->role === 'branchManager') {

                session()->put('id', $user->id);
                session()->put('branch_id', $user->branch_id);
                session()->put('branchManager', true);
                return redirect()->route('managerdashboard', ['id' => $user->id, 'branch_id' => $user->branch_id]);
            } else if ($user->role === 'salesman') {
                session()->put('salesman', true);
                return redirect()->route('salesman_dashboard', ['id' => $user->id, 'branch_id' => $user->branch_id]);
            } else if ($user->role === 'chef') {
                session()->put('chef', true);
                return redirect()->route('chef_dashboard', ['user_id' => $user->id, 'branch_id' => $user->branch_id]);
            }
        } else {
            return redirect()->back()->withErrors(['email' => 'Invalid credentials']);
        }
    }

    public function viewForgotPassword()
    {
        return view('Auth.ForgotPassword');
    }

    public function forgotPassword(Request $request)
    {
        $users = User::where('email', $request->email)->where('role', 'owner')->first();

        if (!$users) {
            return redirect()->back()->with('error', 'Email not found');
        }
        Mail::to($request->email)->send(new ResetPassword($users));
        return redirect()->route('viewLoginPage')->with('success', 'Password reset email send successfully!');
    }

    public function resetPasswordPage($email)
    {
        return view('Auth.ResetPassword')->with(['email' => $email]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed']);

        $user = User::where('email', $request->input('email'))->first();

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('viewLoginPage')->with('success', 'Password reset successfully! You can now log in with your new password.');
    }

    public function logout()
    {
        session()->forget(['username', 'owner', 'owner_id', 'branchManager', 'salesman', 'chef', 'id', 'branch_id']);
        return redirect()->route('viewLoginPage');
    }
}
