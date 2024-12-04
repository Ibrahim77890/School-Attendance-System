<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassesStudent extends Model
{
    use HasFactory;

    protected $table = 'classes_students';

    protected $fillable = ['class_id', 'student_id'];

    // Relationship with Class
    public function classModel()
    {
        return $this->belongsTo(ClassTable::class, 'class_id');
    }

    // Relationship with Student (User)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
