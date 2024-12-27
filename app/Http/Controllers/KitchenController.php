<?php

namespace App\Http\Controllers;

use App\Models\OnlineNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ThemeSetting;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Log;

class KitchenController extends Controller
{
    public function viewChefDashboard($user_id, $branch_id)
    {
        if (!session()->has('chef')) {
            return redirect()->route('viewLoginPage');
        }

        // $branch = Branch::find($branch_id);
        $settings = ThemeSetting::where('branch_id', $branch_id)->with(['branch.users'])->first();

        $newOrders = Order::with('items')
            ->where('branch_id', $branch_id)
            ->where(function ($query) {
                $query->where('ordertype', 'online')
                    ->where('status', 4)
                    ->orWhere(function ($query) {
                        $query->where('ordertype', '!=', 'online')
                            ->where('status', 2);
                    });
            })
            ->get();

        $completeOrders = Order::with('items')
            ->where('branch_id', $branch_id)
            ->where('status', 1)
            ->get();

        return view('Kitchen.Dashboard', [
            'newOrders' => $newOrders,
            'completeOrders' => $completeOrders,
            'ThemeSettings' => $settings,
            'user_id' => $user_id
        ]);
    }

    public function orderComplete($order_id)
    {
        if (!session()->has('chef')) {
            return redirect()->route('viewLoginPage');
        }

        $order = Order::find($order_id);
        $order->status = ($order->status == 4) ? 5 : 1;
        $order->save();

        $notify = new OnlineNotification();
        $notify->message = "Order is ready by the Chef. Please refresh your page.";
        $notify->toast = 0;
        $notify->save();
        return redirect()->back();
    }
    public function printChefRecipt($order_id)
    {
        if (!session()->has('chef')) {
            return redirect()->route('viewLoginPage');
        }

        $order = Order::with('salesman')->where('id', $order_id)->first();
        $products = OrderItem::where('order_id', $order_id)->get();
        $html = view('KitchenRecipt', ['products' => $products, 'orderData' => $order])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $height = $dompdf->getCanvas()->get_height();
        $dompdf->setPaper([0, 0, 300, $height / 2.5], 'portrait');
        $dompdf->render();
        $dompdf->stream($order->order_number . '.pdf');
    }

    public function getOrderNotification()
    {
        // Fetch the latest orders from the database (e.g., last 5 minutes or based on an updated column)
        $latestOrders = Order::where('created_at', '>', now()->subMinutes(1))->get();
        
        if (!$latestOrders->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'collection' => $latestOrders,
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'collection' => $latestOrders,
            ]);
        }
    }
}
