function toggleCancelForm(assignmentId) {
    const cancelForm = document.getElementById(`cancel-form-${assignmentId}`);
    if (cancelForm.style.display === 'none') {
        cancelForm.style.display = 'table-row';
    } else {
        cancelForm.style.display = 'none';
    }
}

function toggleTopicDescription(topicId) {
    const detailsRow = document.getElementById(`description-${topicId}`);

    if (detailsRow.style.display === 'none') {
        detailsRow.style.display = 'table-row';
    } else {
        detailsRow.style.display = 'none';
    }
}

function toggleDetails(assignmentId) {
    const detailsRow = document.getElementById(`details-${assignmentId}`);

    if (detailsRow.style.display === 'none') {
        detailsRow.style.display = 'table-row';
    } else {
        detailsRow.style.display = 'none';
    }
}

function toggleEditForm(topicId) {
    const editForm = document.getElementById(`edit-form-${topicId}`);
    if (editForm.style.display === 'none') {
        editForm.style.display = 'table-row';
    } else {
        editForm.style.display = 'none';
    }
}

function viewNoteContent(noteId) {
    const noteForm = document.getElementById(`note-content-${noteId}`);
    if (noteForm.style.display === 'none') {
        noteForm.style.display = 'table-row';
    } else {
        noteForm.style.display = 'none';
    }
}

function exportAssignments(status, file_format) {
    fetch(`../../api/assignments/downloadassignments.php?status=${status}&file_format=${file_format}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = file_format === 'csv' ? 'data.csv' : 'data.json';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Σφάλμα κατά τη λήψη του αρχείου.');
        });
}

// Load thesis list when that tab is clicked
document.getElementById('list-tab').addEventListener('click', function () {
    loadThesisList();
});

// Load data for assignment form when that tab is clicked
document.getElementById('assign-tab').addEventListener('click', function () {
    loadAssignmentFormData();
});

// Load assignments when that tab is clicked
document.getElementById('assignments-tab').addEventListener('click', function () {
    loadAssignments();
});
// Set up invitations
document.getElementById('invitations-tab').addEventListener('click', function () {
    loadInvitations();
});
document.getElementById('stats-tab').addEventListener('click', function () {
    loadStats();
});
// Set up the thesis form submission
document.getElementById('thesisForm').addEventListener('submit', function (e) {
    e.preventDefault();
    submitThesis();
});

// Set up the assignment form submission
document.getElementById('assignThesisForm').addEventListener('submit', function (e) {
    e.preventDefault();
    // initializeQuillEditor();
    assignThesis();
});

// Set up assignment status filter
document.getElementById('assignmentStatusFilter').addEventListener('change', function () {
    loadAssignments();
});

document.getElementById('supervisorStatusFilter').addEventListener('change', function () {
    loadAssignments();
});

document.getElementById('statsStatusFilter').addEventListener('change', function () {
    loadStats();
});

function loadStats() {
    const statsCharts = document.getElementById('statsCharts');
    const statsValue = document.getElementById('statsStatusFilter').value;

    fetch(`../../api/stats/get_stats.php?stats=${statsValue}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(stats_material => {
            displayCharts(stats_material);
        })
        .catch(error => {
            statsCharts.innerHTML = '<div class="alert alert-danger">Σφάλμα φόρτωσης δεδομένων. Παρακαλώ ανανεώστε τη σελίδα.</div>';
            console.error('Fetch error:', error);
        });
}

function loadThesisList() {
    const thesisList = document.getElementById('thesisList');

    fetch('../../api/thesis_topics/get_thesis_list.php?thesis_topic_dropdown=false')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(topics => {
            displayThesisList(topics);
        })
        .catch(error => {
            thesisList.innerHTML = '<div class="alert alert-danger">Σφάλμα φόρτωσης δεδομένων. Παρακαλώ ανανεώστε τη σελίδα.</div>';
            console.error('Fetch error:', error);
        });
}

function loadInvitations() {
    const invitationsList = document.getElementById("invitationsList");
    fetch('../../api/committee/get_3member_invitations.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(invitations => {
            displayInvitations(invitations);
        })
        .catch(error => {
            invitationsList.innerHTML = '<div class="alert alert-danger">Σφάλμα φόρτωσης δεδομένων. Παρακαλώ ανανεώστε τη σελίδα.</div>';
            console.error('Fetch error:', error);
        });
}

function displayCharts(material) {
    const charts = document.getElementById('statsCharts');
    const statsStatusFilter = document.getElementById('statsStatusFilter');
    if (!material || material.length === 0) {
        charts.innerHTML = '<div class="alert alert-info">Μη επαρκη δεδομένα αναφορικά του αιτήματος σας.</div>';
        return;
    }

    switch (statsStatusFilter.value) {
        case 'thesis_quantity':
            charts.innerHTML = `<canvas id="myChart" style="width:100%;max-width:700px"></canvas>`;
            const xValues = ["Διπλωματικές υπό επιτήρηση", "Διπλωματικές ως μη επιτηρητής"];
            const yValues = [material.supervisor_assignments, material.committee_assignments];
            const barColors = ["#b91d47", "#00aba9"];
            new Chart("myChart", {
                type: "pie",
                data: {
                    labels: xValues,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: "Συμμετοχή στις διπλωματικές"
                    }
                }
            });
            break;
        case 'average_thesis_mark':
            charts.innerHTML = `<canvas id="averageThesisChart" style="width:100%;max-width:700px"></canvas>`;
            const yValuesMarks = ["Βαθμός Ως Επιβλέπων", "Βαθμός Ως Μέλος Επιτροπής"];
            const xValuesMarks = [material.supervisor_average, material.committee_average];
            new Chart('averageThesisChart', {
                type: "horizontalBar",
                data: {
                    labels: yValuesMarks,
                    datasets: [{
                        backgroundColor: ['red', 'blue'],
                        data: xValuesMarks
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: 'Μέσος Όρος Σας Διπλωματικών Εργασιών'
                    },
                    legend: {
                        display: false
                    }
                }
            });
            break;
        case 'thesis_average_time':
            charts.innerHTML = `<canvas id="averageCompletionTimeChart" style="width:100%;max-width:700px"></canvas>`;
            const xValuesCompletion = ["Μέσος χρόνος περάτωσης ως επιβλέπων", "Μέσος χρόνος περάτωσης ως μέλος επιτροπής"];
            const yValuesCompletion = [material.committee_member_completion, material.supervisor_completion];
            new Chart('averageCompletionTimeChart', {
                type: "bar",
                data: {
                    labels: xValuesCompletion,
                    datasets: [{
                        backgroundColor: ['red', 'blue'],
                        data: yValuesCompletion
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: 'Μέσος Χρόνικο Διάστημα Ολοκλήρωσης Διπλωματικών Εργασιών σας'
                    },
                    legend: {
                        display: false
                    }
                }
            })
            break;
    }


}

function displayThesisList(topics) {
    const thesisList = document.getElementById('thesisList');

    if (!topics || topics.length === 0) {
        thesisList.innerHTML = '<div class="alert alert-info">Δεν έχετε υποβάλει ακόμα θέματα διπλωματικών.</div>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-striped table-hover">';
    html += '<thead><tr>';
    html += '<th>ID</th>';
    html += '<th>Τίτλος</th>';
    html += '<th>PDF</th>';
    html += '<th>Ενέργειες</th>';
    html += '</tr></thead><tbody>';

    topics.forEach(topic => {
        html += '<tr>';
        html += `<td>${topic.id}</td>`;
        html += `<td>${topic.title || ''}</td>`;
        html += `<td>`;

        if (topic.pdf_file_path) {
            // Display both the PDF link and the file path
            html += `<div>
                    <a href="${topic.pdf_file_path}" target="_blank" class="btn btn-sm btn-primary mb-2">
                        <i class="bi bi-file-pdf"></i> Προβολή PDF
                    </a>
                    <div class="small text-muted" style="word-break: break-all;">
                        ${topic.pdf_file_path}
                    </div>
                </div>`;
        } else {
            html += 'Δεν υπάρχει PDF';
        }

        html += `</td>`;
        html += `<td>
                <button class="btn btn-sm btn-warning" onclick="toggleEditForm(${topic.id})">
                    <i class="bi bi-pencil-square"></i> Επεξεργασία
                </button>
                <button class="btn btn-sm btn-secondary" onclick="toggleTopicDescription(${topic.id})">
                    <i class="bi bi-arrows-angle-expand"></i> Επέκταση
                </button>
            </td>`;
        html += '</tr>';
        html += `<tr id="description-${topic.id}" style="display: none;">
                <td colspan="5">
                    <h6>Περιγραφή του θέματος</h6>
                    <div class="p-3 bg-light">
                        <p class="text-muted">${topic.description}</p>
                    </div>
                </td>
            </tr>`;
        html += `<tr id="edit-form-${topic.id}" style="display: none;">
                <td colspan="5">
                    <form id="editThesisForm-${topic.id}">
                        <input type="hidden" name="id" value="${topic.id}">
                        <div class="mb-3">
                            <label for="title-${topic.id}" class="form-label">Τίτλος Θέματος</label>
                            <input type="text" class="form-control" id="title-${topic.id}" name="title" value="${topic.title}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description-${topic.id}" class="form-label">Περιγραφή</label>
                            <textarea class="form-control" id="description-${topic.id}" name="description" rows="5">${topic.description}</textarea>
                        </div>
                        <div class="mb-4">
                            <label for="pdf_file-${topic.id}" class="form-label">PDF αρχείο</label>
                            <input class="form-control" type="file" id="pdf_file-${topic.id}" name="pdf_file" accept=".pdf">
                            <div class="form-text">Προαιρετικά: Ανεβάστε ένα PDF με αναλυτικές πληροφορίες (αν δε θέλετε να το αλλάξετε, αφήστε το πεδίο ΚΕΝΟ).</div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Αποθήκευση Αλλαγών</button>
                        </div>
                        <div id="formMessage-${topic.id}">
                        </div>
                    </form>
                </td>
            </tr>`;

        // Add event listener for form submission after the form is added to the DOM
        setTimeout(() => {
            document.getElementById(`editThesisForm-${topic.id}`).addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch(`../../api/thesis_topics/edit_thesis_topic.php?id=${topic.id}`, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`formMessage-${topic.id}`).innerHTML = `
                                    <div class="alert alert-success mt-3">
                                        Οι αλλαγές αποθηκεύτηκαν επιτυχώς!
                                    </div>`;

                            setTimeout(() => {
                                loadThesisList();
                            }, 3000);

                        } else {
                            document.getElementById(`formMessage-${topic.id}`).innerHTML = `
                                    <div class="alert alert-failure mt-3">
                                        Απροσδόκητο λάθος
                                    </div>`;
                        }
                    })
                    .catch(error => {
                        document.getElementById(`formMessage-${topic.id}`).innerHTML = `
                                    <div class="alert alert-failure mt-3">
                                        Απροσδόκητο λάθος:${error}
                                    </div>`;
                    });
            });
        }, 100);
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    thesisList.innerHTML = html;
}

function displayInvitations(invitations) {
    const invitations_list = document.getElementById("invitationsList");
    if (!invitations || invitations.length === 0) {
        invitations_list.innerHTML = '<div class="alert alert-info">Δεν σας έχουν αποστάλει ακόμα προσκλήσεις για συμμετοχή στη 3μελη απο μαθητές.</div>';
        return;
    }
    let html = '<div class="table-responsive"><table class="table table-striped table-hover">';
    html += '<thead><tr>';
    html += '<th>ID</th>';
    html += '<th>Όνομα Επιβλέπων</th>';
    html += '<th>Όνομα Μαθητή</th>'
    html += '<th>Περιγραφή</th>';
    html += '<th>Ενέργειες</th>';
    html += '</tr></thead><tbody>';

    invitations.forEach(invitation => {
        html += '<tr>';
        html += `<td>${invitation.invitation_id}</td>`;
        html += `<td>${invitation.supervisor_name || ''}</td>`;
        html += `<td>${invitation.student_name || ''}</td>`;
        html += `<td>${invitation.title || ''}</td>`;
        if (invitation.status === 'invited') {
            html += `
                    <td id="invitation-actions-${invitation.invitation_id}">
                        <button class="btn btn-sm btn-success me-1" onclick="updateInvitationStatus(${invitation.invitation_id}, 'accepted')">
                            <i class="bi bi-check-circle"></i> Αποδοχή
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="updateInvitationStatus(${invitation.invitation_id}, 'rejected')">
                            <i class="bi bi-x-circle"></i> Απόρριψη
                        </button>
                    </td>
                `;
        }
        html += `<td>`;

        html += `</td>`;
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    invitations_list.innerHTML = html;
}

function loadAssignmentFormData() {
    // Load thesis topics for the dropdown
    fetchThesisTopicsForDropdown();

    // Load students for the dropdown
    fetchStudents();
}
function fetchThesisTopicsForDropdown() {
    const topicSelect = document.getElementById('topic_id');
    // Clear previous options except the placeholder
    topicSelect.innerHTML = `<option value="" selected disabled>Επιλέξτε Θέμα Διπλωματικής</option>`;
    fetch('../../api/thesis_topics/get_thesis_list.php?thesis_topic_dropdown=true')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(topics => {
            // Clear previous options except the placeholder
            while (topicSelect.options.length > 1) {
                topicSelect.remove(1);
            }

            // dropdown for each topic
            topics.forEach(topic => {
                topicSelect.innerHTML += `<option value="${topic.title}">${topic.title}</option>`;
            });
        })
        .catch(error => {
            console.error('Error loading thesis topics:', error);
        });
}
function fetchStudents() {
    const studentSelect = document.getElementById('student_id');
    // Clear previous options except the placeholder
    studentSelect.innerHTML = `<option value="" selected disabled>Επιλέξτε φοιτητή</option>`;
    fetch('../../api/thesis_topics/get_students.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(students => {
            // Clear previous options except the placeholder
            while (studentSelect.options.length > 1) {
                studentSelect.remove(1);
            }

            // dropdown for each student
            students.forEach(student => {
                studentSelect.innerHTML += `<option value="${student.name}">${student.name}</option>`;
            });
        })
        .catch(error => {
            console.error('Error loading students:', error);
        });
}

function assignThesis() {
    const assignmentResult = document.getElementById('assignmentResult');
    assignmentResult.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Υποβολή ανάθεσης...</p>
            </div>
        `;

    const formData = new FormData(document.getElementById('assignThesisForm'));

    fetch('../../api/assignments/assign_thesis.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {

                document.getElementById('assignThesisForm').reset();

                assignmentResult.innerHTML = `
                    <div class="alert alert-success">
                        ${'Η ανάθεση δημιουργήθηκε με επιτυχία!'}
                    </div>
                `;


            } else {
                assignmentResult.innerHTML = `
                    <div class="alert alert-danger">
                        ${'Υπήρξε ένα σφάλμα κατά την ανάθεση.'}
                    </div>
                `;
            }
        })
        .catch(error => {
            assignmentResult.innerHTML = '<div class="alert alert-danger">Αποτυχία υποβολής. Παρακαλώ προσπαθήστε ξανά.</div>';
            console.error('Fetch error:', error);

        });
    console.log("printed");

    // Reload assignments after submission
    fetchStudents();
    console.log("fetching students");
    fetchThesisTopicsForDropdown();
    console.log("fetching topics");
}

function loadAssignments() {
    const assignmentsList = document.getElementById('assignmentsList');
    const status = document.getElementById('assignmentStatusFilter').value;
    const isSupervisor = document.getElementById('supervisorStatusFilter').value;
    assignmentsList.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Φόρτωση αναθέσεων...</p>
            </div>
        `;

    fetch(`../../api/assignments/get_assignments.php?status=${status}&isSupervisor=${isSupervisor}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(assignments => {
            displayAssignments(assignments);
        })
        .catch(error => {
            assignmentsList.innerHTML = '<div class="alert alert-danger">Αποτυχία φόρτωσης αναθέσεων. Παρακαλώ ανανεώστε τη σελίδα.</div>';
            console.error('Fetch error:', error);
        });
}




function displayAssignments(assignments) {
    const assignmentsList = document.getElementById('assignmentsList');
    const status = document.getElementById('assignmentStatusFilter').value;
    const is_supervisor = document.getElementById('supervisorStatusFilter').value;
    // Render the assignments list with no data initially or the data from the previous load(get_assignments.php)
    let html = generateAssignmentsHTML(assignments, status, is_supervisor);


    assignmentsList.innerHTML = html;

    // Directly call data fetching functions to populate the placeholders
    assignments.forEach(assignment => {
        // Populate timeline for all assignments
        fetchTimelineData(assignment.thesis_assignment_id);

        // Populate status-specific data
        switch (assignment.status) {
            case 'Pending':
                fetchInvitationsData(assignment.thesis_assignment_id);
                break;

            case 'Active':
                // Initialize Quill editor
                initializeQuillEditor(assignment.thesis_assignment_id);

                // Fetch notes and committee data
                fetchNotesData(assignment.thesis_assignment_id);
                fetchCommitteeData(assignment.thesis_assignment_id);

                // Set up notes form submission
                setupNotesForm(assignment.thesis_assignment_id);

                // Set up cancel form for active assignments
                setupCancelForm(assignment.thesis_assignment_id);
                break;
            // the draft text was fetched before
            case 'Under Examination':
                if (assignment.is_supervisor) {
                    console.log("is supervisor");
                    setupPresentationForm(assignment.thesis_assignment_id);
                }
                //fetch sumplementary data
                fetchAdditionalMaterial(assignment.thesis_assignment_id);
                fetchGradesData(assignment.thesis_assignment_id, assignment.status);
                break;
            case 'Under Grading':
                fetchGradesData(assignment.thesis_assignment_id, 'Under Grading');
                fetchGradesFormOrReport(assignment.thesis_assignment_id);
                // Fetch additional material if needed
                fetchAdditionalMaterial(assignment.thesis_assignment_id);
                break;
            case 'Completed':
                // Fetch grades data
                fetchGradesData(assignment.thesis_assignment_id, assignment.status);
                break;
        }
    });
}


// Generate the complete HTML structure for assignments
function generateAssignmentsHTML(assignments, status, is_supervisor) {
    // Start with the export buttons
    let html = generateExportButtonsHTML(status);

    // Add the table header
    html += generateTableHeaderHTML();

    // Add table body with all assignments
    html += '<tbody>';

    // Generate rows for each assignment
    assignments.forEach(assignment => {
        html += generateAssignmentRowHTML(assignment, is_supervisor);
        if (assignment.status === 'Active') {
            html += generateCancelFormHTML(assignment);
        }
        html += generateDetailsRowHTML(assignment, is_supervisor);
    });

    html += '</tbody></table></div>';
    return html;
}

// Generate export buttons HTML
function generateExportButtonsHTML(status) {
    return `<div class="d-flex justify-content-center my-3">
<button class="btn btn-outline-primary me-2" onclick="exportAssignments('${status}', 'json')">Εξαγωγή σαν JSON</button>
<button class="btn btn-outline-secondary" onclick="exportAssignments('${status}', 'csv')">Εξαγωγή σαν CSV</button>
</div>
<div class="table-responsive"><table class="table table-striped table-hover">`;
}

// Generate table header HTML
function generateTableHeaderHTML() {
    return `<thead><tr>
<th>ID</th>
<th>Θέμα</th>
<th>Φοιτητής</th>
<th>Ημερομηνία Ανάθεσης</th>
<th>Κατάσταση</th>
<th>Ενέργειες</th>
<th></th>
</tr></thead>`;
}

// Generate HTML for a single assignment row
function generateAssignmentRowHTML(assignment) {
    // Generate status badge
    let statusBadge = '';
    switch (assignment.status) {
        case 'Pending':
            statusBadge = '<span class="badge bg-warning text-dark">Υπό Ανάθεση</span>';
            break;
        case 'Active':
            statusBadge = '<span class="badge bg-primary">Ενεργή</span>';
            break;
        case 'Under Examination':
            statusBadge = '<span class="badge bg-info">Υπό Εξέταση</span>';
            break;
        case 'Under Grading':
            statusBadge = '<span class="badge bg-info">Υπό Βαθμολόγηση</span>';
            break;
        case 'Completed':
            statusBadge = '<span class="badge bg-success">Ολοκληρωμένη</span>';
            break;
        case 'Cancelled':
            statusBadge = '<span class="badge bg-danger">Ακυρωμένη</span>';
            break;
        default:
            statusBadge = '<span class="badge bg-secondary">Άγνωστη</span>';
    }

    // Start the row
    let html = '<tr>';
    html += `<td>${assignment.thesis_assignment_id}</td>`;
    html += `<td>${assignment.title}</td>`;
    html += `<td>${assignment.name}</td>`;
    html += `<td>${assignment.assignment_date}</td>`;
    html += `<td>${statusBadge}</td>`;
    html += `<td>`;

    // Show different action buttons based on status
    if (assignment.status === 'Pending') {
        html += `<button class="btn btn-sm btn-danger me-1" onclick="updateAssignmentStatus(${assignment.thesis_assignment_id}, 'Cancelled')">
    <i class="bi bi-x-circle"></i> Ακύρωση
</button>`;
    } else if (assignment.status === 'Active' && assignment.is_supervisor) {
        html += `<button class="btn btn-sm btn-info" onclick="updateAssignmentStatus(${assignment.thesis_assignment_id}, 'Under Examination')">
    <i class="bi bi-clipboard-check"></i> Προς Εξέταση
</button>`;
        html += `<button class="btn btn-sm btn-danger me-1" onclick="toggleCancelForm(${assignment.thesis_assignment_id})" title='για ειδικούς λόγους που περιγράφονται από τον κανονισμό'>
    <i class="bi bi-x-circle"></i> Ακύρωση
</button>`;
    } else if (assignment.status === 'Under Examination' && assignment.is_supervisor) {
        html += `<button class="btn btn-sm btn-outline-info" onclick="updateAssignmentStatus(${assignment.thesis_assignment_id}, 'Under Grading')" title="Με βάση τα κριτηρία βαθολόγησης του κανονισμού διπλωματικών ceid">
    <i class="bi bi-clipboard-check"></i> Υπό Βαθμολόγηση
</button>`;
    }

    html += `</td>`;
    html += `<td>`;
    html += `<button class="btn btn-sm btn-secondary" onclick="toggleDetails(${assignment.thesis_assignment_id})">
<i class="bi bi-arrows-angle-expand" id="icon-${assignment.thesis_assignment_id}"></i>
</button>`;
    html += `</td>`;
    html += '</tr>';

    return html;
}

// Generate HTML for cancel form (hidden by default)
function generateCancelFormHTML(assignment) {

    return `
<tr id="cancel-form-${assignment.thesis_assignment_id}" style="display: none;">
<td colspan="7">
    <form id="cancelAssignmentForm-${assignment.thesis_assignment_id}">
        <div class="mb-3">
            <label for="cancelAssembly-${assignment.thesis_assignment_id}" class="form-label">Αριθμός ΓΣ:</label>
            <input class="form-control" type="number" id="cancelAssembly-${assignment.thesis_assignment_id}" name="cancelAssembly" rows="3" required />
            <label for="yearAssembly-${assignment.thesis_assignment_id}" class="form-label">Έτος ΓΣ:</label>
            <input class="form-control" type="number" id="yearAssembly-${assignment.thesis_assignment_id}" name="yearAssembly" rows="3" required />
        </div>
        <div class="alert alert-warning" role="alert">
            Όταν η εκπόνηση της Δ.Ε. δεν ολοκληρωθεί επιτυχώς μέσα σε δύο ημερολογιακά έτη (από την επίσημη ημερομηνία ανάθεσης του θέματος), ο επιβλέπων μπορεί να διακόψει την ανάθεση, μετά από αντίστοιχο αίτημα του, στη Συνέλευση του Τμήματος.
        </div>
        <div id="cancelMessage-${assignment.thesis_assignment_id}" class="mt-3"></div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-danger">Υποβολή Ακύρωσης</button>
        </div>
    </form>
</td>
</tr>
<tr></tr>`;
}

// Generate HTML for details row (hidden by default)
function generateDetailsRowHTML(assignment, is_supervisor) {
    let html = `<tr id="details-${assignment.thesis_assignment_id}" style="display: none;">`;
    html += `<td colspan="7">`;
    html += `<div class="card">`;
    html += `<div class="card-body">`;
    html += `<h5 class="card-title">Λεπτομέρειες Ανάθεσης</h5>`;

    // Timeline section (common for all statuses)
    html += generateTimelineHTML(assignment.thesis_assignment_id);

    // Status-specific content
    switch (assignment.status) {
        case 'Pending':
            html += generatePendingDetailsHTML(assignment);
            break;
        case 'Active':
            html += generateActiveDetailsHTML(assignment);
            break;
        case 'Under Examination':

        case 'Under Grading':
        case 'Completed':
            html += generateExaminationDetailsHTML(assignment, is_supervisor);
            break;
        default:
            html += `<p class="text-muted">Under construction</p>`;
    }

    html += `</div>`; // End of card-body
    html += `</div>`; // End of card
    html += `</td>`;
    html += `</tr>`;

    return html;
}

// Generate timeline HTML (common for all assignments)
function generateTimelineHTML(assignmentId) {
    return `<h5>Χρονολόγιο Ενεργειών</h5>
<div class="table-responsive">
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Αλλαγή</th>
            <th>Ημερομηνία</th>
            <th>Ώρα</th>
        </tr>
    </thead>
    <tbody id="timelineTableBody-${assignmentId}">
        <tr>
            <td colspan="3" class="text-center">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Φόρτωση...</span>
                </div>
                Φόρτωση δεδομένων...
            </td>
        </tr>
    </tbody>
</table>
</div>`;
}

// Generate HTML for Pending status details
function generatePendingDetailsHTML(assignment) {
    return `<p><strong>Μέλη Τριμελούς:</strong></p>
        <div class="table-responsive text-center"><table class="table table-striped table-hover">
        <thead><tr>
            <th>Όνομα Καθηγητή</th>
            <th>Ημερομηνία Πρόσκλησης</th>
            <th>Ημερομηνία Απάντησης</th>
        <th>Απάντηση</th>
        </tr></thead>
        <tbody id="invitation-table-${assignment.thesis_assignment_id}">
        <tr><td colspan="4" class="text-center">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Φόρτωση...</span>
            </div> 
            Φόρτωση δεδομένων...
        </td></tr>
        </tbody>
        </table></div>`;
}

// Generate HTML for Active status details
function generateActiveDetailsHTML(assignment) {
    // Notes editor section
    let html = `<div class="row">
        <div class="col-md-12">
            <h5>Υποβολή Σημειώσεων</h5>
            <form id="notesForm-${assignment.thesis_assignment_id}">
                <div class="mb-3">
                    <label for="title-${assignment.thesis_assignment_id}" class="form-label">Τίτλος</label>
                    <input type="text" class="form-control" id="title-${assignment.thesis_assignment_id}" name="title" required>
                </div>
                <div class="mb-3" id="notesEditor-${assignment.thesis_assignment_id}">
                </div>
                <div class="mb-3" id="apiMessage-${assignment.thesis_assignment_id}">
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Υποβολή Σημειώσεων</button>
                </div>
            </form>
        </div>
        </div>`;

    // Notes display section
    html += `<div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Προβολή Σημειώσεων</h5>
                        <div id="notesDisplay-${assignment.thesis_assignment_id}" class="border p-3">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Τίτλος</th>
                                            <th>Ημερομηνία Δημιουργίας</th>
                                            <th>Ενέργειες</th>
                                        </tr>
                                    </thead>
                                    <tbody id="notesTableBody-${assignment.thesis_assignment_id}">
                                        <tr><td colspan="3" class="text-center">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Φόρτωση...</span>
                                            </div> 
                                            Φόρτωση δεδομένων...
                                        </td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>`;

    // Committee members section
    html += `<div class="row mt-4">
<div class="col-md-12">
    <h5>Τριμελής Επιτροπή</h5>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Όνομα Καθηγητή</th>
                </tr>
            </thead>
            <tbody id="committeeTableBody-${assignment.thesis_assignment_id}">
                <tr><td colspan="4" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Φόρτωση...</span>
                    </div> 
                    Φόρτωση δεδομένων...
                </td></tr>
            </tbody>
        </table>
    </div>
</div>
</div>`;

    return html;
}

// Generate HTML for Examination/Grading/Completed status details
function generateExaminationDetailsHTML(assignment, is_supervisor) {
    console.log("Generating examination details for assignment:", assignment.thesis_assignment_id);
    // Thesis text section
    let html = '';
    if (assignment.status != 'Completed') {
        html = `<div class="row">
            <div>
            <h5>Κείμενο Διπλωματικής</h5>`;

        if (assignment.thesis_student_text) {
            html += `<a href="${assignment.thesis_student_text}" target="_blank" class="btn btn-primary">Προβολή Κειμένου Διπλωματικής</a>`;
        } else {
            html += `<p>Δεν έχει υποβληθεί ακόμα κείμενο διπλωματικής.</p>`;
        }

        html += `</div></div>`;
        html += `<div class="row mt-4">
            <div class="col-12">
                <h5>Συμπληρωματικό Υλικό Μαθητή</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Όνομα Αρχείου</th>
                            </tr>
                        </thead>
                        <tbody id="studentMaterialTableBody-${assignment.thesis_assignment_id}">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>`;
    }

    // Completed-specific sections
    if (assignment.status === 'Completed') {
        html += `<div class="row mt-4">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <i class="bi bi-check-circle-fill me-2"></i> Ολοκληρωμένη Διπλωματική Εργασία
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="card-subtitle mb-2"><i class="bi bi-journal-richtext me-2"></i>Τελικό Αποθετήριο Βιβλιοθήκης:</h6>
                                    ${assignment.nemertis_link ?
                `<a href="${assignment.nemertis_link}" target="_blank" class="btn btn-outline-primary mt-2">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Προβολή στο Αποθετήριο
                                         </a>` :
                `<p class="text-muted"><i class="bi bi-exclamation-circle me-2"></i>Δεν έχει καταχωρηθεί ακόμα σύνδεσμος</p>`
            }
                                </div>
                                <div class="col-md-6">
                                    <h6 class="card-subtitle mb-2"><i class="bi bi-file-earmark-text me-2"></i>Πρακτικό Εξέτασης:</h6>
                                    ${assignment.exam_protocol ?
                `<a href="${assignment.exam_protocol}" target="_blank" class="btn btn-outline-secondary mt-2">
                                            <i class="bi bi-file-pdf me-1"></i> Προβολή Πρακτικού
                                         </a>` :
                `<p class="text-muted"><i class="bi bi-exclamation-circle me-2"></i>Δεν έχει καταχωρηθεί ακόμα πρακτικό</p>`
            }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
    }

    // Presentation form for supervisors if under examination and the student has submitted their thesis text
    if (assignment.is_supervisor && assignment.status === 'Under Examination' && assignment.thesis_student_text) {
        console.log("Supervisor presentation announcement for assignment:", assignment.thesis_assignment_id);
        if (!assignment.supervisor_presentation_announcement) {
            html += `<div>
        <h5>Φόρμα Ανακοίνωσης Παρουσίασης Διπλωματικής</h5>
        <form id="presentationForm-${assignment.thesis_assignment_id}" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="presentation-${assignment.thesis_assignment_id}" class="form-label">Ανέβασμα Παρουσίασης</label>
                <input type="file" class="form-control" id="presentation-${assignment.thesis_assignment_id}" name="presentation" accept=".pdf,.ppt,.pptx" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Ανέβασμα Ανακοίνωσης Παρουσίασης</button>
            </div>
            <div id="presentationFormResult-${assignment.thesis_assignment_id}"></div>
        </form>
    </div>`;
        } else {
            html += `<div id="presentationDisplay-${assignment.thesis_assignment_id}" class="mt-3">
        <h6>Παρουσίαση Διπλωματικής:</h6>
        <a href="${assignment.supervisor_presentation_announcement}" target="_blank" class="btn btn-outline-primary">
            <i class="bi bi-file-earmark-pdf"></i> Προβολή Παρουσίασης
        </a>
    </div>`;
        }
    }

    // Committee members and grades section
    const showGrades = assignment.status === 'Under Grading' || assignment.status === 'Completed';
    const gradesTitle = showGrades ? 'Βαθμολογίες Τριμελούς Επιτροπής' : 'Τριμελής Επιτροπή';

    html += `<div class="row mt-4">
<div class="col-md-12">
    <h5 id="gradesTitle-${assignment.thesis_assignment_id}">${gradesTitle}</h5>
    <div id="gradesDisplay-${assignment.thesis_assignment_id}" class="border p-3">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr id="gradesTableHead-${assignment.thesis_assignment_id}">
                        <th>Όνομα</th>
                        ${showGrades ? '<th>Βαθμός</th>' : ''}
                    </tr>
                </thead>
                <tbody id="gradesTableBody-${assignment.thesis_assignment_id}">
                    <tr>
                        <td colspan="${showGrades ? 2 : 1}" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Φόρτωση...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="display_avg_mark-${assignment.thesis_assignment_id}" class="mt-3">
            <h6>Μέσος Βαθμός:</h6>
            <p class="text-muted">Μη διαθέσιμος μέσος βαθμός</p>
        </div>
    </div>
</div>
</div>`;

    // Grade form container for Under Grading status
    if (assignment.status === 'Under Grading') {
        html += `<div class="mt-4" id="gradeReport-${assignment.thesis_assignment_id}">
    <!-- This will be populated dynamically -->
</div>`;
    }

    return html;
}
// Fetch timeline data and update the UI
function fetchTimelineData(assignmentId) {
    const timelineTableBody = document.getElementById(`timelineTableBody-${assignmentId}`);
    if (!timelineTableBody) return;

    fetch(`../../api/assignments/get_timeline.php?assignment_id=${assignmentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(timeline => {
            if (timeline && timeline.length > 0) {
                let timelineHtml = '';
                timeline.forEach(entry => {
                    timelineHtml += `<tr>
                            <td>${entry.change_log}</td>
                            <td>${entry.change_date}</td>
                            <td>${entry.change_time}</td>
                        </tr>`;
                });
                timelineTableBody.innerHTML = timelineHtml;
            } else {
                timelineTableBody.innerHTML = `<tr>
                        <td colspan="3" class="text-center text-muted">Δεν υπάρχουν διαθέσιμες ενέργειες.</td>
                    </tr>`;
            }
        })
        .catch(() => {
            timelineTableBody.innerHTML = `<tr>
                    <td colspan="3" class="text-center text-danger">Σφάλμα φόρτωσης δεδομένων.</td>
                </tr>`;
        });
}

// Fetch invitations data and update the UI
function fetchInvitationsData(assignmentId) {
    const invitationTable = document.getElementById(`invitation-table-${assignmentId}`);
    if (!invitationTable) return;

    fetch(`../../api/committee/get_invitations.php?assignmentId=${assignmentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(invitations => {
            if (invitations && invitations.length > 0) {
                let invitationsHtml = '';
                invitations.forEach(invitation => {
                    invitationsHtml += `<tr>
                            <td>${invitation.teacher_name}</td>
                            <td>${invitation.invitation_date}</td>
                            <td>${invitation.response_date || 'Δεν έχει απαντήσει ακόμα'}</td>
                            <td>${invitation.answer}</td>
                        </tr>`;
                });
                invitationTable.innerHTML = invitationsHtml;
            } else {
                invitationTable.innerHTML = `<tr>
                        <td colspan="4" class="text-center text-muted">Δεν έχουν οριστεί ακόμα μέλη τριμελούς επιτροπής</td>
                    </tr>`;
            }
        })
        .catch(() => {
            invitationTable.innerHTML = `<tr>
                    <td colspan="4" class="text-center text-danger">Σφάλμα φόρτωσης δεδομένων</td>
                </tr>`;
        });
}

// Fetch notes data and update the UI
function fetchNotesData(assignmentId) {
    const notesTableBody = document.getElementById(`notesTableBody-${assignmentId}`);
    if (!notesTableBody) return;

    fetch(`../../api/teacher_notes/get_notes.php?assignment_id=${assignmentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(notes => {
            if (notes && notes.length > 0) {
                let notesHtml = '';
                notes.forEach(note => {
                    notesHtml += `<tr>
                            <td>${note.title}</td>
                            <td>${note.date_created}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewNoteContent(${note.prof_note})">
                                    <i class="bi bi-eye"></i> Προβολή
                                </button>
                            </td>
                        </tr>`;
                    notesHtml += `<tr id="note-content-${note.prof_note}" style="display:none;">
                            <td colspan="3" class="p-3 bg-light">
                                <div class="card" style="word-wrap: break-word; max-width: 100%;">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted">Περιεχόμενο Σημείωσης</h6>
                                        <div style="white-space: pre-wrap; word-break: break-word;">${note.note_content}</div>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                });
                notesTableBody.innerHTML = notesHtml;
            } else {
                notesTableBody.innerHTML = `<tr>
                        <td colspan="3" class="text-center text-muted">Δεν υπάρχουν σημειώσεις.</td>
                    </tr>`;
            }
        })
        .catch(() => {
            notesTableBody.innerHTML = `<tr>
                    <td colspan="3" class="text-center text-danger">Αποτυχία φόρτωσης σημειώσεων.</td>
                </tr>`;
        });
}

// Fetch committee members data and update the UI
function fetchCommitteeData(assignmentId) {
    const committeeTableBody = document.getElementById(`committeeTableBody-${assignmentId}`);
    if (!committeeTableBody) return;

    fetch(`../../api/committee/get_committee_members.php?assignment_id=${assignmentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(members => {
            if (members && members.length > 0) {
                let committeeHtml = '';
                members.forEach(member => {
                    committeeHtml += `<tr>
                            <td>${member.teacher_name}${member.is_supervisor ? ' (επιβλέπων)' : ''}</td>
                        </tr>`;
                });
                committeeTableBody.innerHTML = committeeHtml;
            } else {
                committeeTableBody.innerHTML = `<tr>
                        <td class="text-center text-muted">Δεν έχουν οριστεί ακόμα μέλη τριμελούς επιτροπής</td>
                    </tr>`;
            }
        })
        .catch(() => {
            committeeTableBody.innerHTML = `<tr>
                    <td class="text-center text-danger">Σφάλμα φόρτωσης δεδομένων</td>
                </tr>`;
        });
}

// Fetch grades data and update the UI
function fetchGradesData(assignmentId, status) {
    const gradesTableBody = document.getElementById(`gradesTableBody-${assignmentId}`);
    const gradesTableHead = document.getElementById(`gradesTableHead-${assignmentId}`);
    const gradesTitle = document.getElementById(`gradesTitle-${assignmentId}`);
    const avgMarkDisplay = document.getElementById(`display_avg_mark-${assignmentId}`);

    if (!gradesTableBody) return;

    const showGrades = status === 'Under Grading' || status === 'Completed';

    fetch(`../../api/committee/get_committee_members.php?assignment_id=${assignmentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(members => {
            if (members && members.length > 0) {
                // Update table title if needed
                if (gradesTitle && showGrades) {
                    gradesTitle.innerHTML = 'Βαθμολογίες Τριμελούς Επιτροπής';
                }

                // Add grade column to header if needed
                if (gradesTableHead && showGrades && !gradesTableHead.querySelector('th:nth-child(2)')) {
                    gradesTableHead.innerHTML += '<th>Βαθμός</th>';
                }

                // Create table rows
                let gradesHtml = '';
                members.forEach(member => {
                    gradesHtml += `<tr>
                            <td>${member.teacher_name}${member.is_supervisor ? ' (επιβλέπων)' : ''}</td>`;

                    if (showGrades) {
                        gradesHtml += `<td>${member.mark || 'προς βαθμολόγηση'}</td>`;
                    }

                    gradesHtml += `</tr>`;
                });
                gradesTableBody.innerHTML = gradesHtml;

                // Update average mark if available
                if (avgMarkDisplay) {
                    if (members[0].avg_mark) {
                        avgMarkDisplay.innerHTML = `<h6>Μέσος Βαθμός: ${members[0].avg_mark}</h6>`;
                    } else {
                        avgMarkDisplay.innerHTML = `<h6>Μέσος Βαθμός: περιμένετε να βαθμολογήσουν και οι 3</h6>`;
                    }
                }
            } else {
                gradesTableBody.innerHTML = `<tr>
                        <td colspan="${showGrades ? 2 : 1}" class="text-center text-muted">
                            Δεν έχουν οριστεί ακόμα μέλη τριμελούς επιτροπής
                        </td>
                    </tr>`;
            }
        })
        .catch(() => {
            gradesTableBody.innerHTML = `<tr>
                    <td colspan="${showGrades ? 2 : 1}" class="text-center text-danger">
                        Σφάλμα φόρτωσης δεδομένων
                    </td>
                </tr>`;
        });
}

// Fetch grades form or report and update the UI
function fetchGradesFormOrReport(assignmentId) {
    const gradeReportContainer = document.getElementById(`gradeReport-${assignmentId}`);
    if (!gradeReportContainer) return;

    fetch(`../../api/examination/get_grades.php?assignment_id=${assignmentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response error: ${response.status}`);
            }
            return response.json();
        })
        .then(grades => {
            if (grades && grades.length > 0) {
                // Show grade report if already graded
                gradeReportContainer.innerHTML = `
                        <h5>Η βαθμολογία σας:</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Κριτήριο</th>
                                        <th>Βαθμός</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Ποιότητα της Δ.Ε. και βαθμός εκπλήρωσης των στόχων της (60%)</td>
                                        <td>${grades[0].targets_fulfiled}</td>
                                    </tr>
                                    <tr>
                                        <td>Ποιότητα και πληρότητα του κειμένου της εργασίας και των υπολοίπων παραδοτέων της (15%)</td>
                                        <td>${grades[0].quality_completeness}</td>
                                    </tr>
                                    <tr>
                                        <td>Συνολική εικόνα της παρουσίασης (10%)</td>
                                        <td>${grades[0].readable_thesis}</td>
                                    </tr>
                                    <tr>
                                        <td>Χρονικό διάστημα εκπόνησής της (10%)</td>
                                        <td>${grades[0].time_satisfied}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>`;
            } else {
                // Show grade form if not yet graded
                gradeReportContainer.innerHTML = `
                        <div class="mt-4" id="grades-${assignmentId}">
                            <h5>Βάλτε τη βαθμολογία σας:</h5>
                            <form id="gradeForm-${assignmentId}">
                                <div class="mb-3">
                                    <label for="quality_targets_grade-${assignmentId}" class="form-label">
                                        Ποιότητα της Δ.Ε. και βαθμός εκπλήρωσης των στόχων της (60%)
                                    </label>
                                    <input type="number" step="0.1" class="form-control" 
                                        id="quality_targets_grade-${assignmentId}" name="quality_grade" min="0" max="10" required>
                                </div>
                                <div class="mb-3">
                                    <label for="quality_completeness_grade-${assignmentId}" class="form-label">
                                        Ποιότητα και πληρότητα του κειμένου της εργασίας και των υπολοίπων παραδοτέων της (15%)
                                    </label>
                                    <input type="number" step="0.1" class="form-control" 
                                        id="quality_completeness_grade-${assignmentId}" name="quality_completeness_grade" min="0" max="10" required>
                                </div>
                                <div class="mb-3">
                                    <label for="readable_thesis_grade-${assignmentId}" class="form-label">
                                        Συνολική εικόνα της παρουσίασης(10%)
                                    </label>
                                    <input type="number" step="0.1" class="form-control" 
                                        id="readable_thesis_grade-${assignmentId}" name="readable_thesis_grade" min="0" max="10" required>
                                </div>
                                <div class="mb-3">
                                    <small class="form-text text-muted">
                                        Το κριτήριο "Χρονικό διάστημα εκπόνησής(15%)" βαθμολογείται με άριστα μόνον 
                                        όταν η Δ.Ε. έχει εκπονηθεί σε διάστημα μικρότερο του 1.5 έτους 
                                        (εκτός αν υπάρχουν λόγοι ανωτέρας βίας ή αν έχει παραταθεί σε συμφωνία με τον διδάσκοντα
                                        (σε αυτή τη περίπτωση συνεννοειθείτε με τον υπεύθυνο του συστήματος κ. Μανωλάτο τηλ.6988280719)).
                                    </small>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Υποβολή Βαθμολογίας</button>
                                </div>
                            </form>
                        </div>`;

                // Set up grade form submission
                setupGradeForm(assignmentId);
            }
        })
        .catch(() => {
            gradeReportContainer.innerHTML = '<p class="text-danger">Σφάλμα φόρτωσης βαθμολογιών.</p>';
        });
}
function fetchAdditionalMaterial(assignmentId) {
    const studentMaterialTableBody = document.getElementById(`studentMaterialTableBody-${assignmentId}`);
    if (!studentMaterialTableBody) return;

    fetch(`../../api/examination/get_student_material.php?assignment_id=${assignmentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(materials => {
            if (materials && materials.length > 0) {
                let materialHtml = '';
                materials.forEach(material => {
                    materialHtml += `<tr>
                            <td><a href="${material.st_material_link}" target="_blank">${material.description}</a></td>
                        </tr>`;
                });
                studentMaterialTableBody.innerHTML = materialHtml;
            } else {
                studentMaterialTableBody.innerHTML = `<tr>
                        <td class="text-center text-muted">Δεν υπάρχουν διαθέσιμα συμπληρωματικά υλικά.</td>
                    </tr>`;
            }
        })
        .catch(() => {
            studentMaterialTableBody.innerHTML = `<tr>
                    <td class="text-center text-danger">Σφάλμα φόρτωσης συμπληρωματικού υλικού.</td>
                </tr>`;
        });
}
/**
 * Form Setup and Submission Functions
 */

// Initialize Quill editor
function initializeQuillEditor(assignmentId) {
    setTimeout(() => {
        const editorElement = document.getElementById(`notesEditor-${assignmentId}`);
        if (editorElement) {
            try {
                new Quill(`#notesEditor-${assignmentId}`, {
                    theme: 'snow'
                });
            } catch (error) {
                console.error(`Failed to initialize Quill editor:`, error);
            }
        }

    }, 800);
}
// function initializeQuillEditor(){
//     c
//     const topic_description = document.getElementById("description");
//         if(topic_description){
//             try{
//                 new Quill(topic_description,{
//                     theme:'snow'
//                 });
//             }catch(error){
//                 console.error(error);
//             }
//         }
//         else{console.log("not found");}
// }
// Set up notes form submission
function setupNotesForm(assignmentId) {
    const form = document.getElementById(`notesForm-${assignmentId}`);
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        // Get the Quill editor content
        const editorElement = document.getElementById(`notesEditor-${assignmentId}`);
        const quill = Quill.find(editorElement);

        if (quill && quill.root) {
            formData.append('notes', quill.root.innerHTML);
        } else {
            console.error('Quill editor not initialized properly');
            formData.append('notes', 'Editor content unavailable');
        }

        fetch(`../../api/teacher_notes/submit_notes.php?assignment_id=${assignmentId}`, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(response => {
                const apiMessage = document.getElementById(`apiMessage-${assignmentId}`);

                if (response.success) {
                    if (apiMessage) {
                        apiMessage.innerHTML = '<div class="alert alert-success">Η σημείωση καταχωρήθηκε επιτυχώς!</div>';
                    }

                    // Clear form
                    form.reset();
                    if (quill) {
                        quill.root.innerHTML = '';
                    }

                    // Reload notes
                    fetchNotesData(assignmentId);
                } else {
                    if (apiMessage) {
                        apiMessage.innerHTML = '<div class="alert alert-danger">Υπήρξε ένα σφάλμα κατά την υποβολή των σημειώσεων.</div>';
                    }
                }
            })
            .catch(() => {
                const apiMessage = document.getElementById(`apiMessage-${assignmentId}`);
                if (apiMessage) {
                    apiMessage.innerHTML = '<div class="alert alert-danger">Αποτυχία υποβολής σημειώσεων. Παρακαλώ προσπαθήστε ξανά.</div>';
                }
            });
    });
}

// Set up grade form submission
function setupGradeForm(assignmentId) {
    const form = document.getElementById(`gradeForm-${assignmentId}`);
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
    
        fetch(`../../api/examination/submit_grades.php?assignment_id=${assignmentId}`, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(response => {
                if (response.success) {
                    document.getElementById(`gradeReport-${assignmentId}`).innerHTML = `
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> Η βαθμολογία υποβλήθηκε με επιτυχία!
                </div>`;

                    fetchGradesData(assignmentId, 'Under Grading');

                    setTimeout(() => {
                        fetchGradesFormOrReport(assignmentId);
                    }, 1000);
                } else {
                    document.getElementById(`gradeReport-${assignmentId}`).innerHTML = `
                    <div class="alert alert-danger">
                        Δεν έχει γίνει επίσημη ανάθεση θέματος για αυτή τη διπλωματική εργασία.
                        Παρακαλώ επικοινβνήστε με την γραμματεία.
                    </div>`;
                }
            })
            .catch(() => {
                document.getElementById(`gradeReport-${assignmentId}`).innerHTML = `
                    <div class="alert alert-danger">
                        Δεν έχει γίνει επίσημη ανάθεση θέματος για αυτή τη διπλωματική εργασία.
                        Παρακαλώ επικοινωνήστε με την γραμματεία.
                    </div>`;
            });
    });
}

// Set up presentation form submission
function setupPresentationForm(assignmentId) {
        const form = document.getElementById(`presentationForm-${assignmentId}`);
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(`../../api/examination/submit_presentation_announcement.php?assignment_id=${assignmentId}`, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const resultContainer = document.getElementById(`presentationFormResult-${assignmentId}`);
                    if (data.success) {
                        resultContainer.innerHTML = `
                    <div class="alert alert-success">
                    Η ανακοίνωση παρουσίασης υποβλήθηκε με επιτυχία!
                    </div>`;
                        setTimeout(() => {
                            // Reload the assignments tab after success
                            document.getElementById('assignments-tab').click();
                        }, 1000);
                    } else {
                        resultContainer.innerHTML = `
                    <div class="alert alert-danger">
                    ${data.message || 'Ο μαθητής δεν έχει ανεβάσει στοιχεία για την παρουσίαση του'}
                    </div>`;
                    }
                })
                .catch(() => {
                    const resultContainer = document.getElementById(`presentationFormResult-${assignmentId}`);
                    resultContainer.innerHTML = `
                <div class="alert alert-danger">
                    Αποτυχία υποβολής ανακοίνωσης παρουσίασης. Παρακαλώ προσπαθήστε ξανά.
                </div>`;
                });
        });
}

// Set up cancel form submission
function setupCancelForm(assignmentId) {
    const form = document.getElementById(`cancelAssignmentForm-${assignmentId}`);
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(`../../api/assignments/two_years_cancellation.php?assignment_id=${assignmentId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response error: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const cancelMessage = document.getElementById(`cancelMessage-${assignmentId}`);

            if (data.success) {
                cancelMessage.innerHTML = `<div class="alert alert-success">Η ακύρωση υποβλήθηκε με επιτυχία!</div>`;
                // Reload assignments after success
                document.getElementById('assignments-tab').click();
            } else if (data.success === false && data.reason === 'No assembly found') {
                cancelMessage.innerHTML = `<div class="alert alert-danger">Δε βρέθηκε απόφαση ΓΣ. Παρακαλώ επικοινωνήστε με τον διαχειριστή της σελίδας κ. Μανωλάτο (τηλ:6988280719) για την καταχώρηση της ΓΣ στο σύστημα.</div>`;
                document.getElementById(`cancelAssembly-${assignmentId}`).value = '';
                document.getElementById(`yearAssembly-${assignmentId}`).value = '';
            } else {
                cancelMessage.innerHTML = `<div class="alert alert-danger">Δεν έχει παρέλθει πάροδος 2 ετών από την ενεργοποίηση της ανάθεσης.</div>`;
            }
        })
        .catch(() => {
            const cancelMessage = document.getElementById(`cancelMessage-${assignmentId}`);
            cancelMessage.innerHTML = `<div class="alert alert-danger">Αποτυχία υποβολής ακύρωσης. Παρακαλώ προσπαθήστε ξανά.</div>`;
        });
    });
}

function submitThesis() {
    const thesisSubmitResult = document.getElementById('thesisSubmitResult');
    thesisSubmitResult.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Υποβολή θέματος...</p>
            </div>
        `;

    const formData = new FormData(document.getElementById('thesisForm'));

    fetch('../../api/thesis_topics/submit_thesis.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            if (response.success) {
                thesisSubmitResult.innerHTML = `
                    <div class="alert alert-success">
                        ${response.message || 'Το θέμα υποβλήθηκε με επιτυχία!'}
                    </div>
                `;
                document.getElementById('thesisForm').reset();
            } else {
                thesisSubmitResult.innerHTML = `
                    <div class="alert alert-danger">
                        ${response.message || 'Υπήρξε ένα σφάλμα κατά την υποβολή.'}
                    </div>
                `;
            }
        })
        .catch(error => {
            thesisSubmitResult.innerHTML = '<div class="alert alert-danger">Αποτυχία υποβολής. Παρακαλώ προσπαθήστε ξανά.</div>';
            console.error('Fetch error:', error);
        });
}


// Function to update assignment status
function updateAssignmentStatus(assignmentId, newStatus) {
    fetch(`../../api/assignments/update_assignment_status.php?assignment_id=${assignmentId}&status=${newStatus}`, {
        method: 'GET'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data.success);
            if (data.success) {
                document.getElementById('assignments-tab').click();
            } else {
                alert("something went terribly wrong");
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Σφάλμα κατά την ενημέρωση της κατάστασης.');
        });
}

function updateInvitationStatus(invitationId, newStatus) {
    const actionsTd = document.getElementById(`invitation-actions-${invitationId}`);
    fetch(`../../api/committee/update_invitation_status.php?invitation_id=${invitationId}&status=${newStatus}`, {
        method: 'GET'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && !data.committee_created) {
                actionsTd.innerText = "Επιτυχής Απάντηση. Παρόλα αυτά δεν έχει δημιουργηθεί ακόμα επιτροπή";
                setTimeout(() => {
                    document.getElementById('invitations-tab').click();
                }, 2000);
            } else if (data.success && data.committee_created) {
                actionsTd.innerText = "Επιτυχής Απάντηση. Δημιουργήθηκε νέα επιτροπή";
                setTimeout(() => {
                    document.getElementById('invitations-tab').click();
                }, 2000);
            } else {
                alert("Error: " + (data.message || "Unexpected error occurred."));
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert("Unexpected error occurred while updating invitation status.");
        });
}