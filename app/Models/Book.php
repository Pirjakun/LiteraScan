<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'rfid_uid',
        'title',
        'author',
        'status',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
