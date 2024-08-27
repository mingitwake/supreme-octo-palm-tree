<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelectedCheckbox extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    public $timestamps = true;
    public $incrementing = false;
    protected $fillable = ['answer_id', 'checkbox_option_id', 'remarks'] ;

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }
    public function checkboxOption()
    {
        return $this->belongsTo(CheckboxOption::class);
    }
}
