<?php

use Illuminate\Support\Facades\Route;

use Modules\Expense\Http\Controllers\ExpenseController;
use Modules\Expense\Http\Controllers\ExpenseCategoriesController; // <- sesuai file kamu (plural)

// ------------------------------
// EXPENSES (Pengeluaran Harian)
// ------------------------------
Route::middleware(['web', 'auth'])
    ->prefix('expenses')
    ->name('expenses.')
    ->group(function () {
        Route::get('/',                 [ExpenseController::class, 'index'])->middleware('can:access_expenses')->name('index');
        Route::get('/create',           [ExpenseController::class, 'create'])->middleware('can:create_expenses')->name('create');
        Route::post('/',                [ExpenseController::class, 'store'])->middleware('can:create_expenses')->name('store');
        Route::get('/{expense}/edit',   [ExpenseController::class, 'edit'])->middleware('can:edit_expenses')->name('edit');
        Route::put('/{expense}',        [ExpenseController::class, 'update'])->middleware('can:edit_expenses')->name('update');
        Route::delete('/{expense}',     [ExpenseController::class, 'destroy'])->middleware('can:delete_expenses')->name('destroy');
    });

// --------------------------------------
// EXPENSE CATEGORIES (Kategori Expense)
// --------------------------------------
Route::middleware(['web', 'auth'])
    ->prefix('expense-categories')
    ->name('expense-categories.')
    ->group(function () {
        Route::get('/',                 [ExpenseCategoriesController::class, 'index'])->middleware('can:access_expense_categories')->name('index');
        Route::get('/create',           [ExpenseCategoriesController::class, 'create'])->middleware('can:create_expense_categories')->name('create');
        Route::post('/',                [ExpenseCategoriesController::class, 'store'])->middleware('can:create_expense_categories')->name('store');
        Route::get('/{category}/edit',  [ExpenseCategoriesController::class, 'edit'])->middleware('can:edit_expense_categories')->name('edit');
        Route::put('/{category}',       [ExpenseCategoriesController::class, 'update'])->middleware('can:edit_expense_categories')->name('update');
        Route::delete('/{category}',    [ExpenseCategoriesController::class, 'destroy'])->middleware('can:delete_expense_categories')->name('destroy');
    });