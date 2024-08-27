<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    public $timestamps = true;
    public $incrementing = false;
    protected $fillable = ['response_id', 'question_id', 'content', 'status'] ;

    public function response()
    {
        return $this->belongsTo(Response::class);
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function selectedCheckboxes()
    {
        return $this->hasMany(SelectedCheckbox::class);
    }
    public function tableRows()
    {
        return $this->hasMany(TableRow::class);
    }
}