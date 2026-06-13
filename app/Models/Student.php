<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'rfid_uid',
        'nim',
        'name',
        'major',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
