<?php

namespace App\Http\Controllers;

use App\Mail\CustomerResetPassword;
use App\Mail\EmailConfirmation;
use App\Mail\ResetPassword;
use App\Models\Branch;
use App\Models\Category;
use App\Models\OnlineNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\handler;
use App\Models\Product;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class OnlineOrdersController extends Controller
{

    public function viewOnlinePage()
    {
        $branches = Branch::whereIn('company_name', ['CrustHouse', 'crusthouse, Crust-House', 'crust-house'])->get();
        $branch_ids = [];
        foreach ($branches as $branch) {
            $branch_ids[] = $branch->id;
        }

        $branch_ids[] = [implode(',', $branch_ids)];

        $products = Product::whereIn('branch_id', $branch_ids)->get();

        $categories = Category::whereIn('branch_id', $branch_ids)->get();

        $deals = handler::where(function ($query) use ($branch_ids) {
            $query->whereHas('product', function ($query) use ($branch_ids) {
                $query->whereIn('branch_id', $branch_ids);
            })
                ->orWhereHas('deal', function ($query) use ($branch_ids) {
                    $query->whereIn('branch_id', $branch_ids);
                });
        })
            ->with([
                'product' => function ($query) use ($branch_ids) {
                    $query->whereIn('branch_id', $branch_ids);
                }
            ])
            ->with('deal')
            ->get();

        $filteredCategories = $categories->filter(function ($category) use ($products) {
            return $products->contains('category_id', $category->id) && $category->categoryName !== 'Addons';
        });

        $filteredProducts = $products->reject(function ($product) {
            return $product->category_name === 'Addons';
        });

        return view('OnlineOrdering.layer')->with([
            'Products' => $filteredProducts,
            'Deals' => $deals,
            'Categories' => $filteredCategories,
            'AllProducts' => $products,
        ]);
    }

    public function customerSignup(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phonePrefix') . $request->input('phone_number');
        $password = $request->input('password');

        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            if ($existingUser->email_verified_at == null) {
                $existingUser->phone_number = $phone;
                $existingUser->remember_token = Str::random(32);
                $existingUser->save();
                $confirmationUrl = route('customerEmailConfirmation', ['token' => $existingUser->remember_token]);
                Mail::to($email)->send(new EmailConfirmation($existingUser, $confirmationUrl));
                return redirect()->back()->with('error', 'Email already exists check you inbox for email verification');
            } else {
                return redirect()->back()->with('error', 'Email already exists');
            }
        }

        $newCustomer = new User();
        $newCustomer->name = $name;
        $newCustomer->email = $email;
        $newCustomer->phone_number = $phone;
        $newCustomer->role = 'customer';
        $newCustomer->password = Hash::make($password);
        $newCustomer->remember_token = Str::random(32);

        if ($newCustomer->save()) {
            $confirmationUrl = route('customerEmailConfirmation', ['token' => $newCustomer->remember_token]);
            Mail::to($email)->send(new EmailConfirmation($newCustomer, $confirmationUrl));
            return redirect()->back()->with('success', 'Confirmation email sent successfully. Please check your inbox.');
        } else {
            return redirect()->back()->with('error', 'Sign-in failed');
        }
    }
    public function customerEmailConfirmation($token)
    {
        $user = User::where('remember_token', $token)->first();

        if ($user) {
            $user->email_verified_at = now();
            $user->remember_token = null;
            $user->save();
            return view('Auth.ConfirmationEmailPage');
        }
    }

    public function customerLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if ($user->email_verified_at !== null) {
                    return response()->json(['status' => 'success', 'message' => 'Login successful', 'user' => $user]);
                } else {
                    $user->remember_token = Str::random(32);
                    $user->save();
                    $confirmationUrl = route('customerEmailConfirmation', ['token' => $user->remember_token]);
                    Mail::to($user->email)->send(new EmailConfirmation($user, $confirmationUrl));
                    return response()->json(['status' => 'error', 'message' => 'Please verify your email address.']);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid credentials']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'User not found. Please register your account first.']);
        }
    }
    public function registeredCustomer()
    {
        $users = User::where('role', 'customer')->get();
        return response()->json($users);
    }
    public function addToCart(Request $request)
    {
        $products = [];
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'cartedItem') === 0) {
                $cartItem = json_decode($value, true);
                $products[$key] = $cartItem;
            }
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone_number');
        $orderAddress = $request->input('address');
        $paymentMethod = $request->input('paymentMethod');
        $deliveryCharge = $request->input('deliveryCharge');
        $totalBill = 'Rs.' . $request->input('grandTotal');
        $orderType = 'online';
        $order_initial = "OL-ORD";
        $newOrderNumber = 0;

        $user = User::where('email', $email)->where('phone_number', $phone)->first();
        $user_id = $user->id;

        $lastOnlineOrder = Order::where('ordertype', 'online')->orderBy('id', 'desc')->first();
        if ($lastOnlineOrder) {
            $lastOrderNumber = intval(substr($lastOnlineOrder->order_number, 7));
            $newOrderNumber = $order_initial . '-' . sprintf('%03d', $lastOrderNumber + 1);
        } else {
            $newOrderNumber = "$order_initial-100";
        }

        $newOrder = new Order();

        $newOrder->order_number = $newOrderNumber;
        $newOrder->customer_id = $user_id;
        $newOrder->total_bill = $totalBill;
        $newOrder->delivery_charge = $deliveryCharge;
        $newOrder->payment_method = $paymentMethod;
        $newOrder->ordertype = $orderType;
        $newOrder->order_address = $orderAddress;
        $newOrder->save();
        foreach ($products as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $newOrder->id;
            $orderItem->order_number = $newOrderNumber;
            $orderItem->product_name = $item['name'];
            $orderItem->product_variation = $item['variation'];
            $orderItem->product_price = $item['originalPrice'];
            $orderItem->product_quantity = $item['quantity'];
            $orderItem->total_price = $item['price'];
            $toppingNames = '';
            if (isset($item['topping']) && is_array($item['topping'])) {
                $toppingNames = implode(', ', array_column($item['topping'], 'name'));
            }
            $orderItem->addons = $toppingNames;
            $orderItem->save();
        }

        $notify = new OnlineNotification();
        $notify->message = "A new online order has been placed. Please refresh your page.";
        $notify->toast = 0;
        $notify->save();

        return redirect()->back()->with('Order_Place_Success', 'order placed Successfully');
    }

    public function Profile($email)
    {
        $user = User::where('email', $email)->first();
        return response()->json(['status' => 'success', 'user' => $user]);
    }

    public function updateCustomerProfile(request $request)
    {
        $customer_id = $request->input('customer_id');
        $user = User::find($customer_id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phonePrefix') . $request->input('phone_number');
        if ($user->save()) {
            return redirect()->back()->with('success', "Profile updated successfully.");
        } else {
            return redirect()->back()->with('error', "Profile not update.");
        }
    }

    public function deleteCustomer($customer_id)
    {
        $user = User::find($customer_id);
        $user->delete();
        return redirect()->back()->with('deleteSucceed', 'User deleted');
    }

    public function customerForgotPassword()
    {
        return view('OnlineOrdering.CustomerForgotPassword');
    }

    public function customerSendPasswordReset(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->role === 'customer') {
                Mail::to($request->email)->send(new CustomerResetPassword($user));
                return redirect()->route('onlineOrderPage')->with('success', 'Password reset email sent successfully!');
            } else if ($user->role === 'owner') {
                return redirect()->back()->with('error', 'Owner is not allowed to reset password from here.');
            } else if ($user->role === 'branchManager') {
                return redirect()->back()->with('error', 'Branch managers are not allowed to reset password.');
            } else if ($user->role === 'salesman') {
                return redirect()->back()->with('error', 'Salesmen are not allowed to reset password.');
            } else if ($user->role === 'chef') {
                return redirect()->back()->with('error', 'Chefs are not allowed to reset password.');
            }
        } else {
            return redirect()->back()->with('error', 'Email not found');
        }
    }

    public function customerResetPasswordPage($email)
    {
        return view('OnlineOrdering.ResetPassword')->with('email', $email);
    }

    public function customerResetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->route('onlineOrderPage');
    }
}
