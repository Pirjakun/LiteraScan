<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'student_id',
        'book_id',
        'type',
        'borrowed_at',
        'returned_at',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
