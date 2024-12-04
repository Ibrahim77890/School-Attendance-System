<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'class_session_id', 'is_present'];
    protected $table = 'attendance';

    // Relationship with Student (User)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

}
