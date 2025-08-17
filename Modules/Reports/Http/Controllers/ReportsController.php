<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{

    public function profitLossReport() {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::profit-loss.index');
    }

    public function paymentsReport() {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::payments.index');
    }

    public function salesReport() {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::sales.index');
    }

    public function purchasesReport() {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::purchases.index');
    }

    public function salesReturnReport() {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::sales-return.index');
    }

    public function purchasesReturnReport() {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::purchases-return.index');
    }

    public function ringkas(Request $request)
{
    abort_if(Gate::denies('access_reports'), 403);

    // default: hari ini
    $from = $request->input('from', now()->toDateString());
    $to   = $request->input('to',   now()->toDateString());

    // hanya yang selesai & (opsional) lunas — silakan atur kebijakan
    $q = DB::table('sales')
        ->selectRaw('
            COUNT(*) as trx_count,
            COALESCE(SUM(total_amount),0)  as omset,
            COALESCE(SUM(total_hpp),0)     as total_hpp,
            COALESCE(SUM(total_profit),0)  as total_profit
        ')
        ->whereBetween('date', [$from, $to])
        ->where('status', 'Completed');

    // jika mau hanya Paid, aktifkan:
    if ($request->boolean('only_paid', true)) {
        $q->where('payment_status', 'Paid');
    }

    $sum = (array) $q->first();

    // breakdown metode bayar (opsional)
    $byMethod = DB::table('sales')
        ->select('payment_method')
        ->selectRaw('COUNT(*) as count')
        ->selectRaw('COALESCE(SUM(total_amount),0) as amount')
        ->whereBetween('date', [$from, $to])
        ->where('status', 'Completed')
        ->when($request->boolean('only_paid', true), fn($qq) => $qq->where('payment_status', 'Paid'))
        ->groupBy('payment_method')
        ->get();

    return view('reports::ringkas.index', compact('from','to','sum','byMethod'));
}

public function ringkasPerKasir(Request $request)
{
    abort_if(Gate::denies('access_reports'), 403);

    $from = $request->input('from', now()->toDateString());
    $to   = $request->input('to',   now()->toDateString());
    $userId = $request->input('user_id'); // optional

    $q = DB::table('sales as s')
        ->leftJoin('users as u', 'u.id', '=', 's.user_id')
        ->whereBetween('s.date', [$from, $to])
        ->where('s.status', 'Completed')
        ->when($request->boolean('only_paid', true), fn($qq) => $qq->where('s.payment_status', 'Paid'))
        ->when($userId, fn($qq) => $qq->where('s.user_id', $userId))
        ->groupBy('s.user_id','u.name')
        ->select([
            DB::raw('s.user_id'),
            DB::raw('COALESCE(u.name, "Tidak diketahui") as cashier'),
            DB::raw('COUNT(*) as trx_count'),
            DB::raw('COALESCE(SUM(s.total_amount),0)  as omset'),
            DB::raw('COALESCE(SUM(s.total_hpp),0)     as total_hpp'),
            DB::raw('COALESCE(SUM(s.total_profit),0)  as total_profit'),
        ]);

    $rows = $q->get();

    // total baris ringkasan
    $grand = [
        'trx_count'    => $rows->sum('trx_count'),
        'omset'        => $rows->sum('omset'),
        'total_hpp'    => $rows->sum('total_hpp'),
        'total_profit' => $rows->sum('total_profit'),
    ];

    // list kasir untuk filter
    $cashiers = DB::table('users')->select('id','name')->orderBy('name')->get();

    return view('reports::ringkas.cashier', compact('from','to','rows','grand','cashiers','userId'));
}
}
