<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
    body {
        background-color: lightblue;
    }
    .class-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .class-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.2s;
    }

    .class-card:hover {
        transform: translateY(-5px);
    }

    .class-card.selected {
        border: 2px solid var(--primary-color);
    }

    .class-card h3 {
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }

    .attendance-stats {
        margin-top: 1rem;
        font-size: 0.9rem;
    }

    .status-present {
        color: green;
        font-weight: bold;
    }

    .status-absent {
        color: red;
        font-weight: bold;
    }
</style>

<div class="container">
    <h2>Your Attendance Sessions</h2>

    <div class="class-cards">
    @foreach ($sessions as $session)
        <div class="class-card">
            <h3>
                @if ($session->class_name)
                    {{ $session->class_name }}
                @else
                    Class not available
                @endif
            </h3>
            <div class="attendance-stats">
                <p><strong>Start Time:</strong> {{ $session->start_time }}</p>
                <p><strong>End Time:</strong> {{ $session->end_time }}</p>
                <p>
                    <strong>Status:</strong>
                    @if (!empty($session->attendance) && $session->is_present)
                        <span class="status-present">Present</span>
                    @else
                        <span class="status-absent">Absent</span>
                    @endif
                </p>
            </div>
        </div>
    @endforeach
</div>


</div>

</body>
</html>

