<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Models\Article;
use App\Models\Models\Contact;
use App\Models\Models\ImportGoods;
use App\Models\Models\Order;
use App\Models\Models\Rating;
use App\Models\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ratings = Rating::with('product:id,pro_name', 'user:id,name,phone')->orderBy('id', 'desc')->limit(10)->get();
        $contacts = Contact::orderBy('id', 'desc')->limit(10)->get();
        
   
        $completedTransaction = Transaction::whereMonth('updated_at', date('m'))
        ->where('tr_status', Transaction::STATUS_DONE)->pluck('id');
        $orders = Order::whereIn('od_transaction_id', $completedTransaction)
        ->select('od_product_id', 'od_quantity')->get();
        $totalCost = 0;
        foreach ($orders as $order) {
            $importPrice = ImportGoods::where('product_id', $order->od_product_id)->value('price');
            $totalCost += $importPrice * $order->od_quantity;
    }

        $moneyDay = Transaction::whereDay('updated_at', date('d'))
        ->where('tr_status',Transaction::STATUS_DONE)->sum('tr_total');
        $moneyMonth = Transaction::whereMonth('updated_at', date('m'))
        ->where('tr_status',Transaction::STATUS_DONE)->sum('tr_total');
        $moneyYear = Transaction::whereYear('updated_at', date('Y'))
        ->where('tr_status',Transaction::STATUS_DONE)->sum('tr_total');
        $profit = $moneyMonth - $totalCost;
        $profitVat = $moneyMonth / 1.1 ;
        $profitCost = $profitVat - $totalCost;
        $dataMoney =[
            [
                "name"=>"Doanh thu ngày",
                "y" =>(int)$moneyDay
            ],
            [
                "name"=>"Doanh thu tháng",
                "y" =>(int)$moneyMonth
            ],
            [
                "name"=>"Doanh thu năm",
                "y" =>(int)$moneyYear
            ],
            [
                "name"=>"Lợi nhuận Tháng",
                "y" =>(int)$profit
            ],
            [
                "name"=>"Lợi nhuận sau thuế",
                "y" =>(int)$profitCost
            ]
        ];
        $transactionNews = Transaction::orderBy('id', 'DESC')->simplePaginate(8);
        $viewData = [
            'contacts' => $contacts,
            'ratings'=> $ratings,
            'moneyDay' =>  $moneyDay,
            'moneyMonth' =>  $moneyMonth,
            'moneyYear' =>  $moneyYear,
            'dataMoney'  => $dataMoney,
            'profit' => $profit,
            'profitCost' => $profitCost,
            'transactionNews' => $transactionNews
        ];
        return view('admin::index', $viewData);
    }   
}
