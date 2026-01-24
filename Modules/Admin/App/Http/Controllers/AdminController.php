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
    private function buildDashboardData(): array
    {
        $ratings = Rating::with('product:id,pro_name', 'user:id,name,phone')
            ->orderBy('id', 'asc')
            ->limit(10)
            ->get();
        $contacts = Contact::orderBy('id', 'asc')->limit(10)->get();

        $completedTransaction = Transaction::whereMonth('updated_at', date('m'))
            ->where('tr_status', Transaction::STATUS_DONE)
            ->pluck('id');
        $orders = Order::whereIn('od_transaction_id', $completedTransaction)
            ->select('od_product_id', 'od_quantity')
            ->get();
        $totalCost = 0;
        foreach ($orders as $order) {
            $importPrice = ImportGoods::where('product_id', $order->od_product_id)->value('price');
            $totalCost += $importPrice * $order->od_quantity;
        }

        $moneyDay = Transaction::whereDay('updated_at', date('d'))
            ->where('tr_status', Transaction::STATUS_DONE)
            ->sum('tr_total');
        $moneyMonth = Transaction::whereMonth('updated_at', date('m'))
            ->where('tr_status', Transaction::STATUS_DONE)
            ->sum('tr_total');
        $moneyYear = Transaction::whereYear('updated_at', date('Y'))
            ->where('tr_status', Transaction::STATUS_DONE)
            ->sum('tr_total');
        $profit = $moneyMonth - $totalCost;
        $profitVat = $moneyMonth / 1.1;
        $profitCost = $profitVat - $totalCost;
        $dataMoney = [
            [
                "name" => "Doanh thu ngày",
                "y" => (int) $moneyDay,
            ],
            [
                "name" => "Doanh thu tháng",
                "y" => (int) $moneyMonth,
            ],
            [
                "name" => "Doanh thu năm",
                "y" => (int) $moneyYear,
            ],
            [
                "name" => "Lợi nhuận Tháng",
                "y" => (int) $profit,
            ],
            [
                "name" => "Lợi nhuận sau thuế",
                "y" => (int) $profitCost,
            ],
        ];

        $transactionNews = Transaction::with('user:id,name')
            ->orderBy('id', 'ASC')
            ->simplePaginate(8);

        return [
            'contacts' => $contacts,
            'ratings' => $ratings,
            'moneyDay' => $moneyDay,
            'moneyMonth' => $moneyMonth,
            'moneyYear' => $moneyYear,
            'dataMoney' => $dataMoney,
            'profit' => $profit,
            'profitCost' => $profitCost,
            'transactionNews' => $transactionNews,
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $viewData = $this->buildDashboardData();
        return view('admin::index', $viewData);
    }

    public function dashboardData()
    {
        $data = $this->buildDashboardData();

        $transactions = $data['transactionNews']->map(function ($transaction) {
            $statusText = 'Đang chờ';
            $statusClass = 'label-default';
            $statusUrl = route('admin.get.active.transaction', $transaction->id);
            if ((int) $transaction->tr_status === 1) {
                $statusText = 'Đã xử lý';
                $statusClass = 'label-success';
                $statusUrl = null;
            } elseif ((int) $transaction->tr_status === 2) {
                $statusText = 'Đã huỷ';
                $statusClass = 'label-danger';
                $statusUrl = null;
            }

            return [
                'id' => $transaction->id,
                'name' => optional($transaction->user)->name ?? '[N/A]',
                'phone' => $transaction->tr_phone,
                'total' => number_format($transaction->tr_total, 0, ',', '.') . ' VND',
                'status_text' => $statusText,
                'status_class' => $statusClass,
                'status_url' => $statusUrl,
            ];
        });

        $contacts = $data['contacts']->map(function ($contact) {
            return [
                'id' => $contact->id,
                'name' => $contact->con_name,
                'phone' => $contact->con_phone,
                'email' => $contact->con_email,
                'title' => $contact->con_title,
                'message' => $contact->con_message,
            ];
        });

        $ratings = $data['ratings']->map(function ($rating) {
            return [
                'id' => $rating->id,
                'name' => optional($rating->user)->name ?? '[N/A]',
                'product' => optional($rating->product)->pro_name ?? '[N/A]',
                'content' => $rating->ra_content,
                'number' => $rating->ra_number,
            ];
        });

        return response()->json([
            'dataMoney' => $data['dataMoney'],
            'transactions' => $transactions,
            'contacts' => $contacts,
            'ratings' => $ratings,
        ]);
    }
}
