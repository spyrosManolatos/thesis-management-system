
function fetchStatus(assignment){
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
    return statusBadge;
}


function toggleAssignmentDetails(thesisId) {
    const detailsRow = document.getElementById(`thesisDetails-${thesisId}`);
    if (detailsRow === null) {
        console.error(`Details row for thesis ID ${thesisId} not found.`);
        return;
    }
    if (detailsRow.classList.contains('collapse')) {
        detailsRow.classList.remove('collapse');
    } else {
        detailsRow.classList.add('collapse');
    }
}

function toggleApproveForm(thesisId) {
    const form = document.getElementById(`approveForm-${thesisId}`);
    if (form === null) {
        console.error(`Approve form for thesis ID ${thesisId} not found.`);
        return;
    }
    if (form.classList.contains('collapse')) {
        form.classList.remove('collapse');
    } else {
        form.classList.add('collapse');
    }
}

function toggleDeleteForm(thesisId) {
    const form = document.getElementById(`deleteForm-${thesisId}`);
    if (form === null) {
        console.error(`Delete form for thesis ID ${thesisId} not found.`);
        return;
    }
    if (form.classList.contains('collapse')) {
        form.classList.remove('collapse');
    } else {
        form.classList.add('collapse');
    }
}

function toggleVerificationResults(thesisId) {
    const form = document.getElementById(`UnderGradingResults-${thesisId}`);
    if (form === null) {
        console.error(`Verification results for thesis ID ${thesisId} not found.`);
        return;
    }
    if (form.classList.contains('collapse')) {
        form.classList.remove('collapse');
    } else {
        form.classList.add('collapse');
    }
}

// Load thesis list when the page loads
loadThesisList();

// Load manage thesis list when that tab is clicked
document.getElementById('manage-thesis-tab').addEventListener('click', function () {
    loadManageThesisList();
});

// Set up the import data form submission
document.getElementById('importDataForm').addEventListener('submit', function (e) {
    e.preventDefault();
    importData();
});

function loadThesisList() {
    const thesisList = document.getElementById('thesisList');

    const xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const theses = JSON.parse(xhr.responseText);
                    displayThesisList(theses);
                } catch (e) {
                    thesisList.innerHTML = '<div class="alert alert-danger">Σφάλμα φόρτωσης δεδομένων. Παρακαλώ ανανεώστε τη σελίδα.</div>';
                    console.error('JSON parsing error:', e);
                }
            } else {
                thesisList.innerHTML = '<div class="alert alert-danger">Αποτυχία φόρτωσης διπλωματικών. Παρακαλώ ανανεώστε τη σελίδα.</div>';
            }
        }
    };

    xhr.open('GET', '../../api/secretary/get_assignments.php', true);
    xhr.send();
}

function loadManageThesisList() {
    const manageThesisList = document.getElementById('manageThesisList');

    const xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const theses = JSON.parse(xhr.responseText);
                    displayManageThesisList(theses);
                } catch (e) {
                    manageThesisList.innerHTML = '<div class="alert alert-danger">Σφάλμα φόρτωσης δεδομένων. Παρακαλώ ανανεώστε τη σελίδα.</div>';
                    console.error('JSON parsing error:', e);
                }
            } else {
                manageThesisList.innerHTML = '<div class="alert alert-danger">Αποτυχία φόρτωσης διπλωματικών. Παρακαλώ ανανεώστε τη σελίδα.</div>';
            }
        }
    };

    xhr.open('GET', '../../api/secretary/get_assignments.php', true);
    xhr.send();
}

function displayThesisList(theses) {
    const thesisList = document.getElementById('thesisList');

    if (!theses || theses.length === 0) {
        thesisList.innerHTML = '<div class="alert alert-info">Δεν υπάρχουν διαθέσιμες διπλωματικές εργασίες.</div>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-striped table-hover">';
    html += '<thead><tr>';
    html += '<th>ID</th>';
    html += '<th>Τίτλος</th>';
    html += '<th>Φοιτητής</th>';
    html += '<th>Κατάσταση</th>';
    html += '<th>Λεπτομέρειες</th>';
    html += '</tr></thead><tbody>';

    theses.forEach(thesis => {
        // Fetch status badge
        const statusBadge = fetchStatus(thesis);
        html += '<tr>';
        html += `<td>${thesis.thesis_assignment_id}</td>`;
        html += `<td>${thesis.title}</td>`;
        html += `<td>${thesis.name}</td>`;
        html += `<td>${statusBadge}</td>`;
        html += `<td>
                        <button class="btn btn-sm btn-info" onclick="toggleAssignmentDetails(${thesis.thesis_assignment_id})">
                            <i class="bi bi-arrows-expand"></i> Επέκταση
                        </button>
                    </td>`;
        html += '</tr>';
        // Add a collapsible row with thesis details
        html += `<tr id="thesisDetails-${thesis.thesis_assignment_id}" class="collapse">
                        <td colspan="5">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Περιγραφή Θέματος</h5>
                                            <p>${thesis.description || 'Δεν υπάρχει διαθέσιμη περιγραφή.'}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Τριμελής Επιτροπή</h5>
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Όνομα</th>
                                                        <th>Ρόλος</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="committeeMembers-${thesis.thesis_assignment_id}">
                                                    <tr>
                                                        <td>Μη ορισμένος</td>
                                                        <td>Επιβλέπων</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Μη ορισμένος</td>
                                                        <td>Μέλος 1</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Μη ορισμένος</td>
                                                        <td>Μέλος 2</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>`;
        thesisList.innerHTML = html;
        fetchCommitteeMembers(thesis.thesis_assignment_id);
    });

    html += '</tbody></table></div>';

}

function displayManageThesisList(theses) {
    const manageThesisList = document.getElementById('manageThesisList');

    if (!theses || theses.length === 0) {
        manageThesisList.innerHTML = '<div class="alert alert-info">Δεν υπάρχουν διαθέσιμες διπλωματικές εργασίες.</div>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-striped table-hover">';
    html += '<thead><tr>';
    html += '<th>ID</th>';
    html += '<th>Τίτλος</th>';
    html += '<th>Φοιτητής</th>';
    html += '<th>Κατάσταση</th>';
    html += '<th>Ενέργειες</th>';
    html += '</tr></thead><tbody>';

    theses.forEach(thesis => {
        // Fetch status badge
        const statusBadge = fetchStatus(thesis);
        html += '<tr>';
        html += `<td>${thesis.thesis_assignment_id}</td>`;
        html += `<td>${thesis.title}</td>`;
        html += `<td>${thesis.name}</td>`;
        html += `<td>${statusBadge}</td>`;
        if (thesis.status == 'Active') {
            html += `<td>
                            <button class="btn btn-sm btn-primary" onclick="toggleApproveForm(${thesis.thesis_assignment_id})">
                                <i class="bi bi-pencil-square"></i> Έγκριση Ανάθεσης
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="toggleDeleteForm(${thesis.thesis_assignment_id})">
                                <i class="bi bi-trash"></i> Ακύρωση Ανάθεσης
                            </button>
                        </td>`;
        } else if (thesis.status == 'Under Grading') {
            html += `<td>
                            <button class="btn btn-sm btn-primary" onclick="toggleVerificationResults(${thesis.thesis_assignment_id})">
                                <i class="bi bi-pencil-square"></i> Ολοκλήρωση Ανάθεσης
                            </button>
                        </td>`;
        } else {
            html += `<td>
                            <button class="btn btn-sm btn-secondary" disabled>
                                <i class="bi bi-lock"></i> Μη διαθέσιμο
                            </button>
                        </td>`;
        }
        html += '</tr>';

        // Add forms as collapsed rows
        if (thesis.status === 'Active') {
            html += `
                <tr id="approveForm-${thesis.thesis_assignment_id}" class="collapse">
                    <td colspan="5">
                        <h6>Φόρμα Έγκριση Ανάθεσης</h6>
                        <form id="approveSubmitForm-${thesis.thesis_assignment_id}">
                            <div class="mb-3">
                                <label for="approveAssemblyNumber-${thesis.thesis_assignment_id}" class="form-label">Αριθμός ΓΣ</label>
                                <input type="number" class="form-control" id="approveAssemblyNumber-${thesis.thesis_assignment_id}" name="assemblyNumber" required>
                                <label for="approveAssemblyYear-${thesis.thesis_assignment_id}" class="form-label">Έτος ΓΣ</label>
                                <input type="number" class="form-control" id="approveAssemblyYear-${thesis.thesis_assignment_id}" name="assemblyYear" required>
                            </div>
                            <button type="submit" class="btn btn-success">Έγκριση Ανάθεσης</button>
                            <div id="formMessageApprove-${thesis.thesis_assignment_id}" class="mt-2"></div>
                        </form>
                    </td>
                </tr>
                <tr id="deleteForm-${thesis.thesis_assignment_id}" class="collapse">
                    <td colspan="5">
                        <h6>Φόρμα Ακύρωση Ανάθεσης</h6>
                        <form id="deleteSubmitForm-${thesis.thesis_assignment_id}">
                            <div class="mb-3">
                                <label for="deleteAssemblyNumber-${thesis.thesis_assignment_id}" class="form-label">Αριθμός ΓΣ</label>
                                <input type="number" class="form-control" id="deleteAssemblyNumber-${thesis.thesis_assignment_id}" name="assemblyNumber" required>
                                <label for="deleteAssemblyYear-${thesis.thesis_assignment_id}" class="form-label">Έτος ΓΣ</label>
                                <input type="number" class="form-control" id="deleteAssemblyYear-${thesis.thesis_assignment_id}" name="assemblyYear" required>
                            </div>
                            <button type="submit" class="btn btn-success">Ακύρωση Ανάθεσης</button>
                            <div id="formMessageCancel-${thesis.thesis_assignment_id}" class="mt-2"></div>
                        </form>
                    </td>
                </tr>
            `;
            // officialSubmitAssignment(thesis.thesis_assignment_id);
            // officialWithdrawAssignment(thesis.thesis_assignment_id);
        } else if (thesis.status === 'Under Grading') {
            html += `
                            <tr id="UnderGradingResults-${thesis.thesis_assignment_id}" class="collapse">
                                <td colspan="5">
                                    <h6>Ολοκλήρωση Ανάθεσης</h6>
                                    <form id="gradingSubmitForm-${thesis.thesis_assignment_id}">
                                        <input type="submit" class="btn btn-success" value="Ολοκλήρωση Ανάθεσης"/>
                                    
                                    </form>
                                    <div id="completeFormMessageGrading-${thesis.thesis_assignment_id}" class="mt-2"></div>
                                </td>
                            </tr>
                        `;
            // completeAssignment(thesis.thesis_assignment_id);
        }
    });
    // Load HTML into the manageThesisList
    manageThesisList.innerHTML = html;
    // When HTML is loaded, attach event listeners to the forms
    theses.forEach(thesis => {
        switch (thesis.status) {
            case 'Active':
                officialSubmitAssignment(thesis.thesis_assignment_id);
                officialWithdrawAssignment(thesis.thesis_assignment_id);
                break;
            case 'Under Grading':
                completeAssignment(thesis.thesis_assignment_id);
                break;
            default:
                console.warn(`No actions available for thesis ID ${thesis.thesis_assignment_id} with status ${thesis.status}`);
        }
    }
    );
    html += '</tbody></table></div>';
    

}
function completeAssignment(thesisId) {
        const form = document.getElementById(`gradingSubmitForm-${thesisId}`);
        const formMessage = document.getElementById(`completeFormMessageGrading-${thesisId}`);
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(`../../api/secretary/complete_assignment.php?thesis_id=${thesisId}`, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        formMessage.innerHTML = '<div class="alert alert-success">Η ανάθεση ολοκληρώθηκε με επιτυχία!</div>';
                        loadManageThesisList(); // Reload the list to reflect changes
                    } else {
                        formMessage.innerHTML = '<div class="alert alert-danger">Σφάλμα κατά την ολοκλήρωση της ανάθεσης: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error completing assignment:', error);
                    formMessage.innerHTML = '<div class="alert alert-danger">Σφάλμα κατά την ολοκλήρωση της ανάθεσης. Παρακαλώ προσπαθήστε ξανά.</div>';
                });
        });
}
function officialSubmitAssignment(thesisId) {
    const form = document.getElementById(`approveSubmitForm-${thesisId}`);
    const formMessage = document.getElementById(`formMessageApprove-${thesisId}`);
    
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const assemblyYear = document.getElementById(`approveAssemblyYear-${thesisId}`).value;
        if(parseInt(assemblyYear) > new Date().getFullYear()){
            formMessage.innerHTML = '<div class="alert alert-danger">Το έτος της ΓΣ δεν μπορεί να είναι μεγαλύτερο από το τρέχον έτος.</div>';
            return;
        }
        const formData = new FormData(this);
        fetch(`../../api/secretary/submit_assignment.php?thesis_id=${thesisId}`, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    formMessage.innerHTML = '<div class="alert alert-success">Η ανάθεση υποβλήθηκε με επιτυχία!</div>';
                    setTimeout(() => loadManageThesisList(), 1000); // Reload the list to reflect changes
                } else {
                    formMessage.innerHTML = '<div class="alert alert-danger">Σφάλμα κατά την υποβολή της ανάθεσης: Μήπως έχετε αναθέσει ήδη το θέμα;</div>';
                }
            })
            .catch(error => {
                console.error('Error submitting assignment:', error);
                formMessage.innerHTML = '<div class="alert alert-danger">Σφάλμα κατά την υποβολή της ανάθεσης. Παρακαλώ προσπαθήστε ξανά.</div>';
            });
    });
}
function officialWithdrawAssignment(thesisId) {
    const form = document.getElementById(`deleteSubmitForm-${thesisId}`);
    const formMessage = document.getElementById(`formMessageCancel-${thesisId}`);
    
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const assemblyYear = document.getElementById(`deleteAssemblyYear-${thesisId}`).value;
        if(parseInt(assemblyYear) > new Date().getFullYear()){
            formMessage.innerHTML = '<div class="alert alert-danger">Το έτος της ΓΣ δεν μπορεί να είναι μεγαλύτερο από το τρέχον έτος.</div>';
            return;
        }
        const formData = new FormData(this);
        fetch(`../../api/secretary/withdraw_assignment.php?thesis_id=${thesisId}`, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    formMessage.innerHTML = '<div class="alert alert-success">Η ανάθεση ακυρώθηκε με επιτυχία!</div>';
                    setTimeout(() => loadManageThesisList(), 1000); // Reload the list to reflect changes
                } else {
                    formMessage.innerHTML = '<div class="alert alert-danger">Σφάλμα κατά την ακύρωση της ανάθεσης: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error withdrawing assignment:', error);
                formMessage.innerHTML = '<div class="alert alert-danger">Σφάλμα κατά την ακύρωση της ανάθεσης. Παρακαλώ προσπαθήστε ξανά.</div>';
            });
    });
}
function fetchCommitteeMembers(thesisId) {
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const members = JSON.parse(xhr.responseText);
                    const detailsRow = document.getElementById(`committeeMembers-${thesisId}`);
                    detailsRow.innerHTML = ''; // Clear existing rows
                    if (detailsRow) {
                        members.forEach(member => {
                            if (member.is_supervisor) {
                                role = 'Επιβλέπων';
                            }
                            else {
                                role = 'Μέλος';
                            }
                            const row = document.createElement('tr');
                            row.innerHTML = `<td>${member.name}</td><td>${role}</td>`;
                            detailsRow.appendChild(row);
                        });

                    }
                } catch (e) {
                    console.error('Error parsing committee members:', e);
                }
            } else {
                console.error('Failed to fetch committee members:', xhr.statusText);
            }
        }
    };
    xhr.open('GET', `../../api/secretary/get_committee_members.php?thesis_id=${thesisId}`, true);
    xhr.send();
}
function importData() {
    const importResult = document.getElementById('importResult');
    importResult.innerHTML = `
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Εισαγωγή δεδομένων...</p>
                    </div>
                `;

    const formData = new FormData(document.getElementById('importDataForm'));

    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        importResult.innerHTML = `
                                        <div class="alert alert-success">
                                            ${response.message || 'Τα δεδομένα εισήχθησαν με επιτυχία!'}
                                        </div>
                                    `;
                        document.getElementById('importDataForm').reset();
                    } else {
                        importResult.innerHTML = `
                                        <div class="alert alert-danger">
                                            ${response.message || 'Υπήρξε ένα σφάλμα κατά την εισαγωγή δεδομένων.'}
                                        </div>
                                    `;
                    }
                } catch (e) {
                    importResult.innerHTML = '<div class="alert alert-danger">Σφάλμα επεξεργασίας απάντησης.</div>';
                    console.error('JSON parsing error:', e);
                }
            } else {
                importResult.innerHTML = '<div class="alert alert-danger">Αποτυχία εισαγωγής δεδομένων. Παρακαλώ προσπαθήστε ξανά.</div>';
            }
        }
    };

    xhr.open('POST', '../../api/secretary/import_data.php', true);
    xhr.send(formData);
}

