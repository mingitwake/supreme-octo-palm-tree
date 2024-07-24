<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Chatlog extends Model
// {
//     use HasFactory;
// }

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'chatlogs';
    protected $primaryKey = 'logid';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = ['title', 'uid', 'created_at', 'updated_at', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'logid', 'logid');
    }
}
