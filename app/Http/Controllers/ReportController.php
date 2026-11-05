<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = StockTransaction::with(['product.category', 'user'])
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('transaction_date', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('transaction_date', '<=', $endDate);
            })
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        $transactions = $query->paginate(5)->withQueryString();

        $totalTransactions = $transactions->count();

        $totalIn = $transactions
            ->where('type', 'masuk')
            ->sum('quantity');

        $totalOut = $transactions
            ->where('type', 'keluar')
            ->sum('quantity');

        return view('reports.index', compact(
            'transactions',
            'totalTransactions',
            'totalIn',
            'totalOut',
            'type',
            'startDate',
            'endDate'
        ));
    }
}