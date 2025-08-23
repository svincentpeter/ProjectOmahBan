<?php

namespace Modules\Expense\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable = ['category_name', 'category_description'];

    public function expenses() {
        return $this->hasMany(Expense::class, 'category_id', 'id');
    }
}
