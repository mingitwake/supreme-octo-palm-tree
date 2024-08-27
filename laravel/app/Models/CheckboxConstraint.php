<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckboxConstraint extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    public $timestamps = true;
    public $incrementing = false;
    protected $fillable = ['question_id', 'minselect', 'maxselect', 'others'];

    public function question()
    {
        return $this->morphOne(Question::class, 'constraint');
    }
}
