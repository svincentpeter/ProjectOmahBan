<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Modules\Expense\Entities\Expense;
use Modules\Expense\Entities\ExpenseCategory;
use Modules\Expense\Http\Requests\StoreExpenseRequest;
use Modules\Expense\Http\Requests\UpdateExpenseRequest;

class ExpenseController extends Controller
{
    /**
     * List & filter pengeluaran (tanggal dari–sampai, kategori) + ringkasan total.
     */
    public function index(Request $request)
    {
        $query = Expense::with('category');

        // ========== Handle Quick Filters ==========
        $from = null;
        $to = null;

        switch ($request->get('quick_filter')) {
            case 'yesterday':
                $from = $to = now()->subDay()->toDateString();
                break;
            case 'this_week':
                $from = now()->startOfWeek()->toDateString();
                $to = now()->toDateString();
                break;
            case 'this_month':
                $from = now()->startOfMonth()->toDateString();
                $to = now()->toDateString();
                break;
            case 'last_month':
                $from = now()->subMonth()->startOfMonth()->toDateString();
                $to = now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'all':
                // No date filter
                break;
            default:
                // Default: Hari ini (atau custom range dari request)
                $from = $request->filled('from') ? $request->from : now()->toDateString();
                $to = $request->filled('to') ? $request->to : now()->toDateString();
        }

        // Apply filters
        if ($from && $request->get('quick_filter') !== 'all') {
            $query->whereDate('date', '>=', $from);
        }

        if ($to && $request->get('quick_filter') !== 'all') {
            $query->whereDate('date', '<=', $to);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sort & paginate
        $query->latest('date')->latest('id');
        $expenses = $query->paginate(20)->withQueryString();

        // Calculate total
        $total = (clone $query)->sum('amount');

        // Categories
        $categories = ExpenseCategory::orderBy('category_name')->get();

        return view('expense::expenses.index', compact('expenses', 'categories', 'total', 'from', 'to'));
    }

    /**
     * Halaman create.
     */
    public function create()
    {
        abort_if(Gate::denies('create_expenses'), 403);

        $categories = ExpenseCategory::orderBy('category_name')->get();
        return view('expense::expenses.create', compact('categories'));
    }

    /**
     * Simpan pengeluaran baru.
     * - Reference dihasilkan otomatis via Expense::nextReference($date)
     * - Simpan user_id (pemilik input)
     * - Dukung payment_method (Tunai/Transfer) + bank_name (hanya jika Transfer)
     * - Dukung unggah lampiran ke storage publik
     */
    public function store(StoreExpenseRequest $request)
    {
        abort_if(Gate::denies('create_expenses'), 403);

        $date = Carbon::parse($request->date);

        $expense = Expense::create([
            'category_id' => $request->category_id,
            'date' => $date,
            'reference' => Expense::nextReference($date),
            'details' => $request->details,
            'amount' => (int) $request->amount,
            'user_id' => auth()->id(),
            'payment_method' => $request->payment_method, // contoh: 'Tunai' | 'Transfer'
            'bank_name' => $request->payment_method === 'Transfer' ? $request->bank_name : null,
        ]);

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments/expenses', 'public');
            $expense->update(['attachment_path' => $path]);
        }

        // Gunakan toast jika tersedia; fallback ke session flash biasa pun aman.
        if (function_exists('toast')) {
            toast('Pengeluaran tersimpan.', 'success');
        }

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran tersimpan.');
    }

    /**
     * Halaman edit.
     */
    public function edit(Expense $expense)
    {
        abort_if(Gate::denies('edit_expenses'), 403);

        $categories = ExpenseCategory::orderBy('category_name')->get();
        return view('expense::expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update pengeluaran.
     * - Reference tidak diubah (tetap auto-generate saat create)
     * - Ganti lampiran: hapus file lama jika ada
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        abort_if(Gate::denies('edit_expenses'), 403);

        $date = Carbon::parse($request->date);

        $expense->update([
            'category_id' => $request->category_id,
            'date' => $date,
            'details' => $request->details,
            'amount' => (int) $request->amount,
            'payment_method' => $request->payment_method,
            'bank_name' => $request->payment_method === 'Transfer' ? $request->bank_name : null,
        ]);

        if ($request->hasFile('attachment')) {
            if ($expense->attachment_path) {
                Storage::disk('public')->delete($expense->attachment_path);
            }
            $path = $request->file('attachment')->store('attachments/expenses', 'public');
            $expense->update(['attachment_path' => $path]);
        }

        if (function_exists('toast')) {
            toast('Pengeluaran diperbarui.', 'info');
        }

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran diperbarui.');
    }

    /**
     * Hapus pengeluaran + lampiran (jika ada).
     */
    public function destroy(Expense $expense)
    {
        abort_if(Gate::denies('delete_expenses'), 403);

        if ($expense->attachment_path) {
            Storage::disk('public')->delete($expense->attachment_path);
        }

        $expense->delete();

        if (function_exists('toast')) {
            toast('Pengeluaran dihapus.', 'warning');
        }

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran dihapus.');
    }
}
