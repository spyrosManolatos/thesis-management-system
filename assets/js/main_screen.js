fetchData();

// Add form submit listener
const dateRangeForm = document.getElementById("dateRangeForm");
if (dateRangeForm) {
    dateRangeForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting normally

        // Get form values
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Call fetchData with the date range
        if (startDate && endDate) {
            fetchData(startDate, endDate);
        } else {
            console.error('Date values are missing');
        }
    });
} else {
    console.error('Form with ID "dateRangeForm" not found');
}


function toggleDetails(studentUsername) {
    const detailsRow = document.getElementById('details-' + studentUsername);
    if (detailsRow) {
        // Toggle visibility
        if (detailsRow.style.display === 'none' || detailsRow.style.display === '') {
            detailsRow.style.display = 'table-row';
            event.currentTarget.textContent = 'Σύμπτυξη';
        } else {
            detailsRow.style.display = 'none';
            event.currentTarget.textContent = 'Επέκταση';
        }
    }
}
function fetchData(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    end.setHours(23, 59, 59, 999); // Set end date to end of day for inclusivity
    let lengthOfAnnouncements = 0;
    const tableBody = document.getElementById('announcementDetails');
    tableBody.innerHTML = ''; // Clear previous content
    fetch(`../../api/public_endpoint/get_announcements.php`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            let html = '';
            if (data && data.length > 0) {
                data.forEach(announcement => {
                    // Filter by date range if provided
                    if (startDate && endDate) {
                        const meetingDate = convertTimestampToDate(announcement.meeting_hour);
                        if (meetingDate < start || meetingDate > end) {
                            return;
                        }
                    }
                    lengthOfAnnouncements++;
                    html += `
                        <tr>
                            <td>${announcement.physical_presense ? 'Ναι' : 'Όχι'}</td>
                            <td>${announcement.meeting_room_or_link}</td>
                            <td>${announcement.student_name}</td>
                            <td>${announcement.meeting_hour}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="toggleDetails('${announcement.thesis_assignment_id}')">
                                    Επέκταση
                                </button>
                            </td>
                        </tr>
                        <tr style="display:none;" id="details-${announcement.thesis_assignment_id}">
                            <td colspan="5" class="bg-light p-3">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Λεπτομέρειες Διπλωματικής Εργασίας</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Τίτλος Θέματος:</strong> ${announcement.title || 'Μη διαθέσιμο'}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>3μελης επιτροπή:</strong>
                                                <table class="table table-sm table-bordered mt-2">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Μέλος Επιτροπής</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="committeeMembers-${announcement.thesis_assignment_id}">
                                                        <tr>
                                                        <td colspan="5" class="text-center">
                                                            <div class="spinner-border text-primary" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tableBody.innerHTML = html;

                // Fetch committee members only for visible announcements
                data.forEach(announcement => {
                    if (!startDate || !endDate) {
                        fetchCommitteeMembers(announcement.thesis_assignment_id);
                    } else {
                        const meetingDate = convertTimestampToDate(announcement.meeting_hour);
                        if (meetingDate >= start && meetingDate <= end) {
                            fetchCommitteeMembers(announcement.thesis_assignment_id);
                        }
                    }
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="5" class="text-center">Δεν υπάρχουν διαθέσιμες ανακοινώσεις.</td>`;
                tableBody.appendChild(row);
            }
            if(lengthOfAnnouncements === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="5" class="text-center">Δεν υπάρχουν διαθέσιμες ανακοινώσεις.</td>`;
                tableBody.appendChild(row);
            }
        })
        .catch(error => {
            console.error('Error fetching announcements:', error);
            const tableBody = document.getElementById('announcementDetails');
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Σφάλμα κατά τη φόρτωση των ανακοινώσεων.</td></tr>`;
        });
        
}
function fetchCommitteeMembers(thesisAssignmentId) {

    fetch(`../../api/public_endpoint/get_committee_members.php?thesis_id=${thesisAssignmentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const committeeMembersTable = document.getElementById(`committeeMembers-${thesisAssignmentId}`);
            committeeMembersTable.innerHTML = ''; // Clear existing members
            let role = "";
            if (data && data.length > 0) {
                data.forEach(member => {
                    if (member.is_supervisor) {
                        role = " (Επιβλέπων)";
                    }
                    const row = document.createElement('tr');
                    row.innerHTML = `<td>${member.name} ${role || ""}</td>`;
                    committeeMembersTable.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = `<td class="text-center">Δεν υπάρχουν μέλη επιτροπής.</td>`;
                committeeMembersTable.appendChild(row);
            }
        })
        .catch(error => {
            console.error('Error fetching committee members:', error);
        });

}
function convertTimestampToDate(timestamp) {
    // Assuming timestamp is in the format "DD/MM/YYYY HH:MM"
    const [datePart, timePart] = timestamp.split(' ');
    const [day, month, year] = datePart.split('/');
    const [hours, minutes] = timePart.split(':');
    const meetingDate = new Date(year, month - 1, day, hours, minutes);
    return meetingDate;
}