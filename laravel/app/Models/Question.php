<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Question extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    public $timestamps = true;
    public $incrementing = false;
    protected $fillable = ['form_id', 'no', 'status', 'required', 'description', 'content', 'remarks', 'type'];

    public function constraint() : MorphTo
    {
        return $this->morphTo()->withDefault(null);
    }

    public function tableColumns()
    {
        return $this->hasOne(TableColumns::class);
    }

    public function checkboxOptions()
    {
        return $this->hasMany(CheckboxOption::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
