<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TableColumns extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    public $timestamps = true;
    public $incrementing = false;
    protected $fillable = ['question_id', 'columns', 'delimiter'];
    
    public function questions()
    {
        return $this->belongsTo(Question::class);
    }

    public function table_answers()
    {
        return $this->hasMany(TableAnswer::class);
    }
}
