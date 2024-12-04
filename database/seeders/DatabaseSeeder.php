<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\ClassModel; // Assuming the model for class is ClassModel
use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\ClassesStudent;
use App\Models\ClassTable;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles with explicit ids
        $teacherRole = Role::create([
            'id' => 1,  // Role ID 1 for Teacher
            'name' => 'Teacher'
        ]);

        $studentRole = Role::create([
            'id' => 2,  // Role ID 2 for Student
            'name' => 'Student'
        ]);

        // Create users with role_id referencing the roles table
        $teacher = User::create([
            'fullName' => 'Test Teacher',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'), // Ensure you hash the password
            'role_id' => $teacherRole->id
        ]);

        $student1 = User::create([
            'fullName' => 'Test Student 1',
            'email' => 'student1@example.com',
            'password' => bcrypt('password'),
            'role_id' => $studentRole->id
        ]);

        $student2 = User::create([
            'fullName' => 'Test Student 2',
            'email' => 'student2@example.com',
            'password' => bcrypt('password'),
            'role_id' => $studentRole->id
        ]);

        // Create classes with teacher_id referencing the teacher
        $class1 = ClassTable::create([
            'name' => 'Mathematics 101',
            'user_id' => $teacher->id, // Reference to the teacher (user)
        ]);

        $class2 = ClassTable::create([
            'name' => 'History 101',
            'user_id' => $teacher->id, // Reference to the teacher (user)
        ]);

        // Create class_student records (assign students to classes)
        ClassesStudent::create([
            'class_id' => $class1->id,
            'student_id' => $student1->id
        ]);

        ClassesStudent::create([
            'class_id' => $class1->id,
            'student_id' => $student2->id
        ]);

        ClassesStudent::create([
            'class_id' => $class2->id,
            'student_id' => $student1->id
        ]);

        ClassesStudent::create([
            'class_id' => $class2->id,
            'student_id' => $student2->id
        ]);

        // Create class sessions (sessions for each class)
        $classSession1 = ClassSession::create([
            'class_id' => $class1->id,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'attendance_marked' => false,
        ]);

        $classSession2 = ClassSession::create([
            'class_id' => $class2->id,
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
            'attendance_marked' => false,
        ]);

        // Create attendance records for each student in each class session
        Attendance::create([
            'student_id' => $student1->id,
            'class_session_id' => $classSession1->id,
            'is_present' => true
        ]);

        Attendance::create([
            'student_id' => $student2->id,
            'class_session_id' => $classSession1->id,
            'is_present' => false
        ]);

        Attendance::create([
            'student_id' => $student1->id,
            'class_session_id' => $classSession2->id,
            'is_present' => true
        ]);

        Attendance::create([
            'student_id' => $student2->id,
            'class_session_id' => $classSession2->id,
            'is_present' => true
        ]);

        ClassSession::where('id', $classSession1->id)
            ->update(['attendance_marked' => true]);

        ClassSession::where('id', $classSession2->id)
        ->update(['attendance_marked' => true]);
    }
}

