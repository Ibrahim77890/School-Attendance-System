<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTable extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = ['name', 'user_id'];

    // Relationship with Teacher (User)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with Students (through ClassesStudent)
    public function students()
    {
        return $this->belongsToMany(User::class, 'classes_students');
    }

    // Relationship with ClassSessions
    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }
}
