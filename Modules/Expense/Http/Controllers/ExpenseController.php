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
        abort_if(Gate::denies('access_expenses'), 403);

        $q = Expense::with(['category', 'user'])
            ->when($request->filled('from'), fn ($qq) => $qq->whereDate('date', '>=', $request->from))
            ->when($request->filled('to'),   fn ($qq) => $qq->whereDate('date', '<=', $request->to))
            ->when($request->filled('category_id'), fn ($qq) => $qq->where('category_id', $request->category_id))
            ->latest('date');

        // Ambil data untuk tabel (pagination) + total ringkasan
        $expenses   = $q->paginate(15)->withQueryString();
        $categories = ExpenseCategory::orderBy('category_name')->get();
        $total      = (clone $q)->sum('amount');

        return view('expense::expenses.index', compact('expenses', 'categories', 'total'));
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
            'category_id'    => $request->category_id,
            'date'           => $date,
            'reference'      => Expense::nextReference($date),
            'details'        => $request->details,
            'amount'         => (int) $request->amount,
            'user_id'        => auth()->id(),
            'payment_method' => $request->payment_method, // contoh: 'Tunai' | 'Transfer'
            'bank_name'      => $request->payment_method === 'Transfer' ? $request->bank_name : null,
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
            'category_id'    => $request->category_id,
            'date'           => $date,
            'details'        => $request->details,
            'amount'         => (int) $request->amount,
            'payment_method' => $request->payment_method,
            'bank_name'      => $request->payment_method === 'Transfer' ? $request->bank_name : null,
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
