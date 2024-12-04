<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .tabs {
            margin-bottom: 2rem;
        }

        .tab-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .tab-button {
            padding: 0.75rem 1.5rem;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }

        .tab-button.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .class-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .class-card,
        .class-card-attendance {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .class-card:hover,
        .class-card-attendance:hover {
            transform: translateY(-5px);
        }

        .class-card.selected,
        .class-card-attendance.selected {
            border: 2px solid var(--primary-color);
        }

        .sessions-table,
        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .sessions-table th,
        .sessions-table td,
        .students-table th,
        .students-table td {
            padding: 1rem;
            border: 1px solid #ddd;
        }

        .attendance-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .attendance-btn.present {
            background: var(--success-color);
            color: white;
        }

        .attendance-btn.absent {
            background: var(--danger-color);
            color: white;
        }

        .new-class-form {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .student-list {
            margin-top: 1rem;
        }

        .save-btn {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .action-btn {
            padding: 6px 12px;
            margin: 0 4px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .view-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .view-btn:hover {
            background-color: #0056b3;
        }

        .edit-btn {
            background-color: var(--warning-color);
            color: #000;
        }

        .edit-btn:hover {
            background-color: #e0a800;
        }

        .action-btn i {
            font-size: 12px;
        }

        /* Section Styles */
        .section {
            margin-bottom: 3rem;
        }

        .section-header {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Section 1: Add Class and Students -->
        <div class="section" id="add-class-section">
            <h2 class="section-header">Add Class and Students</h2>
            <form class="new-class-form" id="new-class-form" method="POST" action="{{ route('createClass') }}">
                @csrf
                <div class="form-group">
                    <label>Class Name:</label>
                    <input type="text" name="name" required>
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                </div>

                <!-- Dynamic population of students and then selecting them -->
                <div class="form-group student-list">
                    <h4>Select Students</h4>
                    <ul id="student-list">
                        @foreach ($students as $student)
                        <li>
                            <input type="checkbox" name="students[]" value="{{ $student->id }}">
                            <p>{{ $student->fullName }}</p>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <button type="submit" class="save-btn" style="background-color: #0056b3;">Create Class</button>
            </form>

            <div>
                <h2>Existing Classes - Click to create sessions</h2>
                <div class="class-cards">
                    @foreach ($classes as $class)
                    <div class="class-card" data-class-id="{{ $class->id }}" style="background-color: #e0a800;">
                        <h3>{{ $class->name }}</h3>
                    </div>
                    @endforeach
                </div>
            </div>
            <div id="dynamic-container"></div>

            <div>
                <h2>Existing Sessions - Click to mark attendance</h2>
                <div class="class-cards">
                    @foreach ($sessions as $session)
                        @if (!$session->attendance_marked)
                        <div class="class-card-attendance" data-session-id="{{ $session->session_id }}" style="background-color: #e0a800;">
                            <h3>{{ $session->class_name }}</h3>
                            <h4>{{ $session->start_time }} - {{ $session->end_time }}</h4>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div id="dynamic-container-attendance"></div>

            <div id="sessions-data" data-sessions="{{ json_encode($sessionsWithStudents) }}" style="display:none;"></div>

        </div>

        <script>
            const sessionsWithStudents = JSON.parse(document.getElementById('sessions-data').getAttribute('data-sessions'));
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const classCards = document.querySelectorAll('.class-card');
                const dynamicContainer = document.getElementById('dynamic-container');

                classCards.forEach(card => {
                    card.addEventListener('click', () => {
                        const classId = card.getAttribute('data-class-id');

                        // Check if a div with this ID already exists
                        if (!document.getElementById(`class-${classId}`)) {
                            // Create a new div
                            const newDiv = document.createElement('div');
                            newDiv.id = `class-${classId}`;
                            newDiv.style.border = '1px solid #ccc';
                            newDiv.style.padding = '10px';
                            newDiv.style.margin = '10px 0';
                            newDiv.innerHTML = `
                        <h4>Session Details</h4>
                        <form action="{{ route('createSession') }}" method="POST">
                            @csrf <!-- Blade directive for CSRF token -->
                            <input type="hidden" name="class_id" value="${classId}">
                            <label for="start-time">Start Time:</label>
                            <input type="time" id="start_time" name="start_time" required>
                            <br>
                            <label for="stop-time">Stop Time:</label>
                            <input type="time" id="stop_time" name="stop_time" required>
                            <br><br>
                            <button type="submit">Save Session</button>
                        </form>
                    `;

                            // Append the new div to the dynamic container
                            dynamicContainer.innerHTML = '';
                            dynamicContainer.appendChild(newDiv);
                        } else {
                            alert(`Session for Class ID: ${classId} already exists!`);
                        }
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', () => {
                const classCardAtts = document.querySelectorAll('.class-card-attendance');
                const dynamicAttendanceContainer = document.getElementById('dynamic-container-attendance');

                classCardAtts.forEach(card => {
                    card.addEventListener('click', () => {
                        const sessionId = card.getAttribute('data-session-id');

                        // Check if a div with this session ID already exists
                        if (!document.getElementById(`attendance-form-${sessionId}`)) {
                            // Create a new div for the attendance form
                            const newDiv = document.createElement('div');
                            newDiv.id = `attendance-form-${sessionId}`;
                            newDiv.style.border = '1px solid #ccc';
                            newDiv.style.padding = '10px';
                            newDiv.style.margin = '10px 0';

                            const session = sessionsWithStudents.find(session => session.session_id === parseInt(sessionId));
                            let studentCheckboxes = '';

                            if (session) {
                                // Iterate through the students of the selected session
                                session.students.forEach(student => {
                                    studentCheckboxes += `
                            <div>
                                <input
                                    type="checkbox" 
                                    id="student-${student.student_id}" 
                                    name="students[${student.student_id}]" 
                                    value="1"
                                    data-student-id="${student.student_id}">
                                <label for="student-${student.student_id}">${student.student_name}</label>
                                <input 
                                    type="hidden" 
                                    id="hidden-student-${student.student_id}" 
                                    name="students[${student.student_id}]" 
                                    value="0">
                            </div>
                        `;
                                });
                            } else {
                                studentCheckboxes = '<p>No students found for this session.</p>';
                            }

                            // Set the inner HTML of the form
                            newDiv.innerHTML = `
                    <h4>Attendance</h4>
                    <form action="{{ route('markAttendance') }}" method="POST" id="attendance-form-${sessionId}">
                        @csrf <!-- Blade directive for CSRF token -->
                        <input type="hidden" name="class_session_id" value="${sessionId}">
                        <label>Select Students:</label>
                        <div id="students-checkboxes">
                            ${studentCheckboxes}
                        </div>
                        <br><br>
                        <button type="submit">Submit Attendance</button>
                    </form>
                `;

                            // Append the new div to the dynamic container
                            dynamicAttendanceContainer.innerHTML = ''; // Clear previous content
                            dynamicAttendanceContainer.appendChild(newDiv);

                            // Add event listeners to checkboxes
                            const checkboxes = newDiv.querySelectorAll('input[type="checkbox"]');
                            checkboxes.forEach(checkbox => {
                                checkbox.addEventListener('change', () => {
                                    const hiddenInput = document.getElementById(`hidden-student-${checkbox.dataset.studentId}`);
                                    if (checkbox.checked) {
                                        hiddenInput.disabled = true; // Disable hidden input if checkbox is checked
                                    } else {
                                        hiddenInput.disabled = false; // Enable hidden input if checkbox is unchecked
                                    }
                                });
                            });
                        } else {
                            alert(`Attendance form for Session ID: ${sessionId} already exists!`);
                        }
                    });
                });
            });


            // JavaScript to handle dynamic interactions, such as adding classes, students, and marking attendance
            document.querySelector('[data-tab="existing-classes"]').addEventListener('click', () => {
                document.getElementById('existing-classes').classList.add('active');
                document.getElementById('add-class').classList.remove('active');
            });

            // Handling attendance marking logic
            document.querySelectorAll('.attendance-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const studentId = this.dataset.studentId;
                    const status = this.classList.contains('present') ? 'present' : 'absent';
                    // Here you can update attendance status logic (e.g., via AJAX or form submission)
                    console.log(`Student ID: ${studentId}, Status: ${status}`);
                });
            });

            const studentsAvailable = document.getElementById('student-list');

            const studentsDynamic = "{{route('students')}}";

            alert(studentsDynamic);

            // Fetch students using GET request
            fetch("students", {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Check if students data exists
                    if (data && data.students) {
                        // Loop through the students array and create list items
                        data.students.forEach(student => {
                            const li = document.createElement('li');
                            li.textContent = student.fullname; // Assuming student has a 'fullname' property
                            li.dataset.studentId = student.id; // Optionally, store the student ID in a data attribute
                            li.classList.add('student-item');

                            // Append the new 'li' element to the 'student-list' ul
                            studentsAvailable.appendChild(li);
                        });
                    } else {
                        console.error('No students data available');
                    }
                })
                .catch(error => {
                    console.error('Error fetching students:', error);
                });
        </script>
</body>

</html>