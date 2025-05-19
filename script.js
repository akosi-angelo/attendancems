document.addEventListener('DOMContentLoaded', function() {
    const teacherCalendarContainer = document.getElementById('teacher-calendar-container');
    const tooltip = document.getElementById('attendance-marker-tooltip');
    let currentClickedDayCell = null; // To store the cell that was clicked

    if (teacherCalendarContainer && tooltip) {
        const currentStudentId = teacherCalendarContainer.dataset.currentStudentId;

        teacherCalendarContainer.addEventListener('click', function(event) {
            const dayCell = event.target.closest('.teacher-clickable-day:not(.empty)');
            if (!dayCell) {
                // Hide tooltip if clicked outside a valid day cell
                if (!tooltip.contains(event.target)) {
                    tooltip.style.display = 'none';
                }
                return;
            }
            
            currentClickedDayCell = dayCell; // Store the clicked cell
            const date = dayCell.dataset.date;

            // Position and show tooltip
            const rect = dayCell.getBoundingClientRect();
            tooltip.style.display = 'flex'; // Use flex as defined in CSS
            
            // Position tooltip below the cell
            tooltip.style.left = `${rect.left + window.scrollX}px`;
            tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`; // 5px offset

            // Ensure data for buttons is set
            tooltip.dataset.date = date;
            tooltip.dataset.studentId = currentStudentId;
        });

        tooltip.addEventListener('click', function(event) {
            const button = event.target.closest('button');
            if (!button) return;

            const status = button.classList.contains('btn-present') ? 'present' : 'absent';
            const date = tooltip.dataset.date;
            const studentId = tooltip.dataset.studentId;

            if (!date || !studentId) {
                console.error('Missing date or studentId for attendance update.');
                tooltip.style.display = 'none';
                return;
            }

            updateAttendance(studentId, date, status, currentClickedDayCell);
            tooltip.style.display = 'none';
        });

        // Optional: Hide tooltip if clicking outside
        document.addEventListener('click', function(event) {
            if (teacherCalendarContainer && tooltip) {
                if (!teacherCalendarContainer.contains(event.target) && !tooltip.contains(event.target)) {
                    tooltip.style.display = 'none';
                }
            }
        });
    }

    function updateAttendance(studentId, date, status, dayCell) {
        const formData = new FormData();
        formData.append('student_id', studentId);
        formData.append('date', date);
        formData.append('status', status);

        fetch('update_attendance.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the cell UI
                if (dayCell) {
                    const statusDiv = dayCell.querySelector('.day-status');
                    dayCell.classList.remove('present', 'absent'); // Remove old classes
                    
                    if (status === 'present') {
                        dayCell.classList.add('present');
                        if (statusDiv) statusDiv.innerHTML = '<span class="icon">✅</span>';
                    } else if (status === 'absent') {
                        dayCell.classList.add('absent');
                        if (statusDiv) statusDiv.innerHTML = '<span class="icon">❌</span>';
                    } else {
                         if (statusDiv) statusDiv.innerHTML = ''; // Clear if neither
                    }
                }
                // Optionally, show a success message (e.g., using a toast notification library)
                // console.log(data.message);
            } else {
                alert('Error: ' + data.message); // Simple alert for error
            }
        })
        .catch(error => {
            console.error('Error updating attendance:', error);
            alert('An error occurred. Please try again.');
        });
    }

    // Auto-dismiss messages after a few seconds (optional)
    const errorMessages = document.querySelectorAll('.error-message');
    const successMessages = document.querySelectorAll('.success-message');

    const autoDismiss = (element) => {
        if (element) {
            setTimeout(() => {
                element.style.transition = 'opacity 0.5s ease';
                element.style.opacity = '0';
                setTimeout(() => element.remove(), 500);
            }, 5000); // 5 seconds
        }
    };

    errorMessages.forEach(autoDismiss);
    successMessages.forEach(autoDismiss);
});