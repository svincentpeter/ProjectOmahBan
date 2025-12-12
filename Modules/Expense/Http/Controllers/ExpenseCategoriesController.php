<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Expense\Entities\ExpenseCategory;

use Modules\Expense\DataTables\ExpenseCategoriesDataTable;

class ExpenseCategoriesController extends Controller
{
    public function index(ExpenseCategoriesDataTable $dataTable)
    {
        $categories_count = ExpenseCategory::count();
        return $dataTable->render('expense::categories.index', compact('categories_count'));
    }

    public function create()
    {
        // >>> inilah method yang hilang
        return view('expense::categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_name' => ['required','string','max:100'],
            'category_description' => ['nullable','string','max:500'],
        ]);

        ExpenseCategory::create($data);

        return redirect()
            ->route('expense-categories.index')
            ->with('success', 'Kategori ditambahkan.');
    }

    public function edit(ExpenseCategory $category)
    {
        return view('expense::categories.edit', compact('category'));
    }

    public function update(Request $request, ExpenseCategory $category)
    {
        $data = $request->validate([
            'category_name' => ['required','string','max:100'],
            'category_description' => ['nullable','string','max:500'],
        ]);

        $category->update($data);

        return redirect()
            ->route('expense-categories.index')
            ->with('success', 'Kategori diperbarui.');
    }

    public function destroy(ExpenseCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Kategori dihapus.');
    }
}
