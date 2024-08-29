<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{

    public function viewOwnerDashboard($owner_id)
    {
        if (!session()->has('owner')) {
            return redirect()->route('viewLoginPage');
        }

        $owner = User::find($owner_id);
        $branches = Branch::with('users')->get();

        $branchData = $branches->map(function ($branch) {
            $branch->branchManager = $branch->users->where('role', 'branchManager')->first();
            $branch->unsetRelation('users');
            return $branch;
        });

        $totalCompanies = $this->totalBranches();
        $totalStaff = $this->totalStaff();
        $totalRevenue = $this->totalRevenue();
        return view('Owner.Dashboard')->with([
            'totalCompanies' => $totalCompanies,
            'branchRevenue' => false,
            'totalStaff' => $totalStaff,
            'totalRevenue' => $totalRevenue,
            'branchData' => $branchData,
            'ownerData' => $owner
        ]);
    }

    public function UpdateOwnerProfile(Request $request)
    {
        $owner_id = $request->input('owner_id');

        $existingEmail = User::where('email', $request->input('email'))
            ->where('id', '!=', $request->input('owner_id'))
            ->first();

        if ($existingEmail) {
            return redirect()->back()->with('error', 'Email already exists');
        }

        $owner = User::find($owner_id);

        if ($request->hasFile('profile_picture')) {
            $existingImagePath = public_path('Images/UsersImages') . '/' . $owner->profile_picture;
            if (File::exists($existingImagePath)) {
                File::delete($existingImagePath);
            }

            $image = $request->file('profile_picture');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Images/UsersImages/'), $imageName);
            $owner->profile_picture = $imageName;
        }

        $owner->name = $request->input('name');
        $owner->email = $request->input('owner_email');
        if ($request->filled('password')) {
            $owner->password = Hash::make($request->input('password'));
        }
        if ($owner->save()) {
            return redirect()->back()->with('success', 'Profile update successfully');
        } else {
            return redirect()->back()->with('error', 'Profile update failed');
        }
    }

    /*
    |---------------------------------------------------------------|
    |======================= Branch Functions ======================|
    |---------------------------------------------------------------|
    */

    public function newBranch(Request $request)
    {
        if (!session()->has('owner')) {
            return redirect()->route('viewLoginPage');
        }

        $existingUser = User::all();
        foreach ($existingUser as $user) {
            if ($user->email == $request->input('manager_email')) {
                return redirect()->back()->with('error', 'Email already exist');
            }
        }

        $riderOption = $request->has('riderOption') ? true : false;
        $onlineDeliveryOption = $request->has('onlineDeliveryOption') ? true : false;
        $diningTableOption = $request->has('diningTableOption') ? true : false;

        $newBranch = new Branch();
        $newBranch->branch_state = $request->input('branch_state');
        $newBranch->branch_city = $request->input('branch_city');
        $newBranch->company_name = $request->input('company_name');
        $newBranch->branch_initials = $request->input('branch_initial');
        $newBranch->branch_name = $request->input('branch_name');
        $newBranch->branch_code = $request->input('branch_code');
        $newBranch->branch_address = $request->input('branch_address');

        $newBranch->riderOption = $riderOption;
        $newBranch->onlineDeliveryOption = $onlineDeliveryOption;
        $newBranch->DiningOption = $diningTableOption;

        if ($newBranch->save()) {
            $newManager = new User();
            $newManager->email = $request->input('manager_email');
            $newManager->password = Hash::make($request->input('password'));
            $newManager->branch_id = $newBranch->id;
            $newManager->save();
            return redirect()->back()->with('success', 'Branch Added Successfully');
        } else {
            return redirect()->back()->with('error', 'Branch not added');
        }
    }
    public function updateBranches(Request $request)
    {
        if (!session()->has('owner')) {
            return redirect()->route('viewLoginPage');
        }
        $existingEmail = User::where('email', $request->input('manager_email'))
            ->where('id', '!=', $request->branch_manager_id)
            ->first();
        if ($existingEmail) {
            return redirect()->back()->with('error', 'Email already exists');
        }
        $riderOption = $request->has('riderOption') ? true : false;
        $onlineDeliveryOption = $request->has('onlineDeliveryOption') ? true : false;
        $diningTableOption = $request->has('diningTableOption') ? true : false;

        $branchData = Branch::find($request->branch_id);
        $branchData->branch_state = $request->input('branch_state');
        $branchData->branch_city = $request->input('branch_city');
        $branchData->company_name = $request->input('company_name');
        $branchData->branch_initials = $request->input('branch_initial');
        $branchData->branch_name = $request->input('branch_name');
        $branchData->branch_code = $request->input('branch_code');
        $branchData->branch_address = $request->input('branch_address');
        $branchData->riderOption = $riderOption;
        $branchData->onlineDeliveryOption = $onlineDeliveryOption;
        $branchData->diningOption = $diningTableOption;
        if ($branchData->save()) {
            $manager = User::find($request->branch_manager_id);
            $manager->email = $request->input('manager_email');
            if ($request->filled('password')) {
                $manager->password = Hash::make($request->input('password'));
            }
            $manager->save();
            return redirect()->back()->with('success', 'Branch Update Successfully');
        } else {
            return redirect()->back()->with('error', 'Branch not updated');
        }
    }
    public function deleteBranch($branch_id)
    {
        if (!session()->has('owner')) {
            return redirect()->route('viewLoginPage');
        }
        $branchData = Branch::find($branch_id);
        $branchData->delete();

        if ($branchData && !$branchData->delete()) {
            return redirect()->back()->with('success', 'Branch Delete Successfully');
        } else {
            return redirect()->back()->with('error', 'Branch not deleted');
        }
    }
    public function showBranchStats($branch_id)
    {
        $branch = Branch::find($branch_id);
        if ($branch->onlineDeliveryOption === 0) {
            return redirect()->back()->with('warning', 'The Online Option for this Branch is disabled.');
        }
        $orders = Order::with('branch')->where('branch_id', $branch_id)->where('ordertype', 'online')->get();
        $orders->branch_details = $branch;
        return redirect()->back()->with('Orders', $orders);
    }

    /*
    |---------------------------------------------------------------|
    |======================== Staff Functions ======================|
    |---------------------------------------------------------------|
    */

    public function totalBranches()
    {
        $totalBranches = Branch::distinct('company_name')->count('company_name');
        return $totalBranches;
    }
    public function totalStaff()
    {
        $totalStaff = User::where('role', '<>', 'owner')->count();
        return $totalStaff;
    }
    public function totalRevenue()
    {
        $TotalRevenues = Order::where('status', 1)->get();
        $totalRevenue = 0;
        foreach ($TotalRevenues as $revenue) {
            $bill = (float) str_replace('Rs. ', '', $revenue->total_bill) + $revenue->taxes - $revenue->discount;
            $totalRevenue += $bill;
        }
        return $totalRevenue;
    }
}