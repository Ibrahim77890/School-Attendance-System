<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = ['class_id', 'start_time', 'end_time', 'attendance_marked'];

    // Relationship with Class
    public function classModel()
    {
        return $this->belongsTo(ClassTable::class, 'class_id');
    }

    // Relationship with Attendance
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'class_session_id');
    }
}
