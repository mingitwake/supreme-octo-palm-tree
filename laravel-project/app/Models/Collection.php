<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory, HasUuids;
    public $timestamps = true;
    public $incrementing = false;
    protected $fillable = ['name', 'status'] ;
}
