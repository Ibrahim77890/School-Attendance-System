<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;

    use HasRoles;
    protected $fillable = ['fullName', 'email', 'password', 'role_id'];
    // Relationship with Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relationship with Classes (through ClassesStudent)
    public function classes()
    {
        return $this->belongsToMany(ClassTable::class, 'classes_students');
    }

    // Relationship with Attendance
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }
}
