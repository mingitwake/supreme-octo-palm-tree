<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Response extends Model
{
    use HasFactory, HasUuids;
    public $timestamps = true;
    public $incrementing = false;
    protected $fillable = ['form_id', 'remarks', 'status'] ;
}