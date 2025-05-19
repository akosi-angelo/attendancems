<?php
// This file will contain functions to generate calendar HTML and fetch attendance
// It will be included in student_dashboard.php and teacher_dashboard.php

function getAttendanceData($pdo, $student_id, $month, $year) {
    $attendance_data = [];
    try {
        $stmt = $pdo->prepare("SELECT DAY(attendance_date) as day, status FROM attendance WHERE student_user_id = :student_id AND MONTH(attendance_date) = :month AND YEAR(attendance_date) = :year");
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($records as $record) {
            $attendance_data[$record['day']] = $record['status'];
        }
    } catch (PDOException $e) {
        // Log error or handle appropriately
        error_log("Error fetching attendance: " . $e->getMessage());
    }
    return $attendance_data;
}

function generateCalendar($month, $year, $student_id_for_view, $is_teacher_view = false, $pdo = null) {
    if ($pdo === null) { global $pdo_global; $pdo = $pdo_global; } // Hack for simplicity if not passed explicitly
    if ($pdo === null) { return "<p>Database connection not available for calendar.</p>";}

    $attendance = [];
    if ($student_id_for_view) {
        $attendance = getAttendanceData($pdo, $student_id_for_view, $month, $year);
    }

    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday']; // 0 (Sun) - 6 (Sat)

    $calendar = "<div class='calendar-controls'>";
    $calendar .= "<h2>" . htmlspecialchars($monthName) . " " . htmlspecialchars($year) . "</h2>";
    // Add prev/next month controls if desired here
    $calendar .= "</div>";
    
    $calendar .= "<table class='calendar'>";
    $calendar .= "<thead><tr>";
    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='calendar-header'>" . htmlspecialchars($day) . "</th>";
    }
    $calendar .= "</tr></thead>";
    $calendar .= "<tbody><tr>";

    if ($dayOfWeek > 0) {
        for ($k = 0; $k < $dayOfWeek; $k++) {
            $calendar .= "<td class='calendar-day empty'></td>";
        }
    }

    $currentDay = 1;
    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $dateStr = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($currentDay, 2, '0', STR_PAD_LEFT);
        $class = 'calendar-day';
        $content = htmlspecialchars($currentDay);
        $status_icon = '';

        if (isset($attendance[$currentDay])) {
            if ($attendance[$currentDay] == 'present') {
                $class .= ' present';
                $status_icon = ' <span class="icon">✅</span>';
            } elseif ($attendance[$currentDay] == 'absent') {
                $class .= ' absent';
                $status_icon = ' <span class="icon">❌</span>';
            }
        }
        
        $data_attributes = "data-date='" . htmlspecialchars($dateStr) . "'";
        if ($is_teacher_view && $student_id_for_view) {
             $data_attributes .= " data-student-id='" . htmlspecialchars($student_id_for_view) . "'";
             $class .= ' teacher-clickable-day'; // Make it clear it's clickable for teacher
        }

        $calendar .= "<td class='" . htmlspecialchars($class) . "' " . $data_attributes . ">";
        $calendar .= "<div class='day-number'>" . htmlspecialchars($currentDay) . "</div>";
        $calendar .= "<div class='day-status'>" . $status_icon . "</div>";
        $calendar .= "</td>";

        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        for ($i = 0; $i < $remainingDays; $i++) {
            $calendar .= "<td class='calendar-day empty'></td>";
        }
    }

    $calendar .= "</tr></tbody></table>";

    if ($is_teacher_view) {
        $calendar .= "<div id='attendance-marker-tooltip' class='attendance-marker-tooltip' style='display:none;'>
                        <button class='btn-present'>Mark Present</button>
                        <button class='btn-absent'>Mark Absent</button>
                      </div>";
    }
    return $calendar;
}
?>