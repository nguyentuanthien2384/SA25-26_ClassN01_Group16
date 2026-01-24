<?php

namespace App\Http\Controllers;

use App\Models\Models\Transaction;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $perPage = (int) request()->input('per_page', 8);
        if ($perPage <= 0) {
            $perPage = 8;
        }
        if ($perPage > 60) {
            $perPage = 60;
        }
        $transactions = Transaction::orderBy('id','DESC')
            ->where('tr_user_id', get_data_user('web'))
            ->paginate($perPage);
        $totalTransaction = Transaction::where('tr_user_id', get_data_user('web'))
        ->select('id')->count();
        
        $totalTransactionDone = Transaction::where('tr_user_id', get_data_user('web'))
        ->where('tr_status',Transaction::STATUS_DONE)->select('id')->count();
        $viewData = [
            'totalTransaction'=> $totalTransaction,
            'totalTransactionDone'=> $totalTransactionDone,
            'transactions'=> $transactions
        ];
        return view('user.index',$viewData);
    }
}
