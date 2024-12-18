<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Rider;
use App\Models\ThemeSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class RiderController extends Controller
{
    public function viewDashboard($rider_id, $branch_id)
    {
        if (!session()->has('rider')) {
            return redirect()->route('viewLoginPage');
        }

        $branch = Branch::with('users.rider')->find($branch_id);
        $user = $branch->users->where('id', $rider_id)->first();

        if ($user === null || $user->phone_number === null) {
            return redirect()->back()->with('error', 'Please complete the profile first');
        }

        $settings = ThemeSetting::where('branch_id', $branch_id)->with(['branch.users'])->first();
        $orders = Order::where('assign_to_rider', $user->rider->id)->with('customers')->get();

        return view("Rider/Dashboard")->with([
            'user_id' => $rider_id,
            'rider_id' => $user->rider->id,
            'branch_id' => $branch_id,
            'branch' => $branch,
            'ThemeSettings' => $settings,
            'orders'=> $orders,
            'rider' => $user
        ]);
    }

    public function deliveryCompleted($order_number, $rider_id){
        if (!session()->has('rider')) {
            return redirect()->route('viewLoginPage');
        }
        $order = Order::where('order_number', $order_number)->where('assign_to_rider', $rider_id)->first();
        if(!$order){
            return redirect()->back()->with('error', 'Order not Found');
        }
        $rider = Rider::find( $rider_id);
        if(!$rider){
            return redirect()->back()->with('error', 'Rider not Found');
        }

        $order->delivery_status = 1;
        $order->save();

        $rider->status = 1;
        $rider->save();

        return redirect()->back()->with('success','Order successfully marked as delivery completed.');
    }

    public function deliveryCancelled($order_number, $rider_id){
        if (!session()->has('rider')) {
            return redirect()->route('viewLoginPage');
        }
        $order = Order::where('order_number', $order_number)->where('assign_to_rider', $rider_id)->first();
        if(!$order){
            return redirect()->back()->with('error', 'Order not Found');
        }
        $rider = Rider::find( $rider_id);
        if(!$rider){
            return redirect()->back()->with('error', 'Rider not Found');
        }

        $order->delivery_status = -1;
        $order->save();

        $rider->status = 1;
        $rider->save();

        return redirect()->back()->with('success','Order successfully marked as delivery cancelled.');
    }
    
    public function viewOrderDetails($order_number, $rider_id){
        if (!session()->has('rider')) {
            return redirect()->route('viewLoginPage');
        }
        $order = Order::where('order_number', $order_number)->where('assign_to_rider', $rider_id)->with('items')->first();
       
        if(!$order){
            return redirect()->back()->with('error', 'Order not Found');
        }
       
        return redirect()->back()->with('order_details',$order);
    }


    public function viewOrders($rider_id, $branch_id)
    {
        if (!session()->has('rider')) {
            return redirect()->route('viewLoginPage');
        }
        $branch = Branch::with('users.rider')->find($branch_id);
        $user = $branch->users->where('id', $rider_id)->first();

        if ($user === null || $user->phone_number === null) {
            return redirect()->back()->with('error', 'Please complete the profile first');
        }

        $settings = ThemeSetting::where('branch_id', $branch_id)->with(['branch.users'])->first();
        $orders = Order::where('assign_to_rider', $user->rider->id)->with('customers')->get();

        return view("Rider/Orders")->with([
            'user_id' => $rider_id,
            'rider_id' => $user->rider->id,
            'branch_id' => $branch_id,
            'branch' => $branch,
            'ThemeSettings' => $settings,
            'orders'=> $orders,
            'rider' => $user
        ]);
    }


    public function viewProfile($rider_id, $branch_id)
    {
        if (!session()->has('rider')) {
            return redirect()->route('viewLoginPage');
        }
        $branch = Branch::with('users.rider')->find($branch_id);
        $user = $branch->users->where('id', $rider_id)->first();
        $settings = ThemeSetting::where('branch_id', $branch_id)->first();
        return view('Rider/profile')->with([
            'user_id' => $rider_id,
            'rider_id' => $user->rider->id,
            'branch_id' => $branch_id,
            'branch' => $branch,
            'ThemeSettings' => $settings
        ]);
    }

    public function updateProfilePicture(Request $request)
    {
        if (!session()->has('rider')) {
            return redirect()->route('viewLoginPage');
        }

        $rider = User::find($request->rider_id);
        if ($rider) {
            if ($request->hasFile('profile_photo')) {

                $imageName = null;
                $existingImagePath = public_path('Images/UsersImages') . '/' . $rider->profile_picture;
                File::delete($existingImagePath);

                $image = $request->file('profile_photo');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('Images/UsersImages'), $imageName);
                $rider->profile_picture = $imageName;
            }
            $rider->save();
            return redirect()->back()->with('success', 'Profile Updated Successfully.');
        } else {
            return redirect()->back()->with('error', 'Profile Not Updated.');
        }
    }
    public function updateProfile(Request $request)
    {
        if (!session()->has('rider')) {
            return redirect()->route('viewLoginPage');
        }
        $user = User::find($request->rider_id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
        
        $user->phone_number = $request->phone_number;
        $user->save();

        $rider = Rider::where('user_id', $user->id)->first();
        
        if ($rider) {
            $rider->license_number = $request->license_number;
            $rider->motorbike_number = $request->motorbike_number;
            $rider->save();
            
            return redirect()->back()->with('success', 'Rider profile updated successfully.');
        } else {
            $newRider = new  Rider();
            $newRider->user_id  = $user->id;
            $newRider->license_number = $request->license_number;
            $newRider->motorbike_number = $request->motorbike_number;
            $newRider->save();
            return redirect()->back()->with('success', 'Rider profile created successfully.');
        }
    }
}