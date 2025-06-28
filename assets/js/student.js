// student/js/student.js
document.addEventListener("DOMContentLoaded", () => {
    // setupLogin();
    setupLogout();
    loadStudentInfo();
    document.getElementById("view-profile").addEventListener("click", (e) => {
        hideAllSections();
        e.preventDefault();
        renderProfile();
        updateProfileForm(); // Ενημέρωση της φόρμας ενημέρωσης προφίλ
    });
    document.getElementById("view-theses").addEventListener("click", (e) => {
        e.preventDefault();
        hideAllSections();
        document.getElementById("availableThesesSection").style.display = "block";
        loadStudentTheses();
    });

});


function setupLogout() {
    const logoutBtn = document.getElementById("logoutBtn");
    if (!logoutBtn) return;

    logoutBtn.addEventListener('click', function () {
        window.location.href = "../../includes/auth/logout.php";
    });
}

function loadStudentInfo() {
    const studentNameElement = document.getElementById("studentName");
    if (!studentNameElement) return;

    fetch("../../api/student/get_student_info.php")
        .then(res => res.json())
        .then(data => {

            studentNameElement.innerHTML = data[0].name;
        })
        .catch(err => {
            console.error('Σφάλμα κατά την επικοινωνία με το server:', err);
        });
}

function loadStudentTheses() {
    const thesisListContainer = document.getElementById("thesisList");
    if (!thesisListContainer) return;

    fetch("../../api/student/get_available_theses.php")
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                thesisListContainer.innerHTML = `<p class="error">${data.error}</p>`;
                return;
            }

            if (data.length === 0) {
                thesisListContainer.innerHTML = `<p>Δεν έχετε δηλώσει κάποια διπλωματική</p>`;
                return;
            }



            // Call renderThesisList from within the fetch completion handler
            renderThesisList(data, thesisListContainer);
        })
        .catch(err => {
            console.error("Σφάλμα κατά την ανάκτηση των διπλωματικών:", err);
            thesisListContainer.innerHTML = `<p class="error">Προέκυψε σφάλμα κατά τη φόρτωση.</p>`;
        });
}
function toggleDetails() {
    const details = document.getElementById("thesis-details");
    console.log("Toggling details visibility");
    if (!details) return;
    if (details.style.display === "none" || details.style.display === "") {
        details.style.display = "block";
    } else {
        details.style.display = "none";
    }
}
function setupNavbarLinks() {
    const viewThesesBtn = document.getElementById("view-theses");
    if (!viewThesesBtn) return;

    viewThesesBtn.addEventListener("click", (e) => {
        e.preventDefault();

        document.querySelectorAll("#content-area > div").forEach(div => div.style.display = 'none');

        //section με τις διπλωματικές
        document.getElementById("availableThesesSection").style.display = "block";

        // Φόρτωσε ξανά τη λίστα διπλωματικών
        loadStudentTheses();
    });
}



function renderProfile() {
    hideAllSections();
    document.getElementById("profileSection").style.display = "block";

    fetch("../../api/student/get_profile.php")
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById("studentNameInput").value = data.profile.name;
                document.getElementById("studentAreaInput").value = data.profile.area;
                document.getElementById("studentEmailInput").value = data.profile.email;
                document.getElementById("studentPhoneInput").value = data.profile.mobile_phone;
                
            } else {
                alert('Σφάλμα κατά την ανάκτηση του προφίλ.');
            }
        })
        .catch(err => console.error("Σφάλμα στο προφίλ:", err));

}
function updateProfileForm() {
    const profileForm = document.getElementById('studentProfileForm');
    const profileMessage = document.getElementById('profileMessage');
    if (!profileForm) {
        console.error("Το στοιχείο studentProfileForm δεν βρέθηκε.");
        return;
    }
    profileForm.onsubmit = function (e) {
        e.preventDefault(); // Σταματάει το κανονικό submit
        const formData = new FormData(this);
        fetch('../../api/student/update_profile.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    profileMessage.classList.remove('text-danger');
                    profileMessage.classList.add('text-success');
                    profileMessage.textContent = "Το προφίλ ενημερώθηκε επιτυχώς!";
                    setTimeout(renderProfile,1000); // Επαναφόρτωση του προφίλ για να εμφανιστούν οι αλλαγές
                } else {
                    profileMessage.classList.remove('text-success');
                    profileMessage.classList.add('text-danger');
                    profileMessage.textContent = "Σφάλμα κατά την ενημέρωση του προφίλ: " + (data.message || "Άγνωστο σφάλμα");
                }
            })
            .catch(error => {
                console.error('Σφάλμα:', error);
                alert('Σφάλμα σύνδεσης.');
            });
    };
}


function hideAllSections() {
    document.querySelectorAll("#content-area > div").forEach(div => div.style.display = 'none');
}

function renderThesisList(data) {
    const thesisListContainer = document.getElementById("thesisList");
    thesisListContainer.innerHTML = "";
    let thesisCard = "";
    switch (data[0].status) {
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
    thesisCard = `
                    <h4>${data[0].title}</h4>
                    <p><strong>Περιγραφή:</strong> ${data[0].description}</p>
                    <p><strong>Επιβλέπων:</strong> ${data[0].supervisor_name}</p>
                    <p><strong>Κατάσταση:</strong> ${statusBadge}</p>
                    <p><strong>Eπίσημη Ημερομηνία Ανάθεσης(καταχώρησης ΓΣ από την γραμματεία):</strong> ${data[0].official_assignment_date||"ΔΕΝ ΕΧΕΙ ΟΡΙΣΤΕΙ!"}</p>
                    <a href="${data[0].pdf_file_path}" class="btn btn-primary view-details-btn" target="_blank">Περιγραφή Θέματος</a>`;
    if (data[0].status === "Pending") {
        thesisCard += `
                <button class="btn btn-primary" onclick="showThesisDetails()">Επιλογή Μελών Επιτροπής</button>
                <form id="chooseMembers" class="mb-3" style="display:none;">
                <h6>Επιλέξτε μέλη της τριμελούς επιτροπής:</h6>
                <select id="chooseCommitteeMember1" class="form-select" name="committee_member1">
                    <option value="">-- Επιλέξτε μέλος --</option>
                    <!--placeholder options, these should be dynamically generated based on available members-->
                </select>
                <select id="chooseCommitteeMember2" class="form-select mt-2" name="committee_member2">
                    <option value="">-- Επιλέξτε μέλος --</option>
                    <!-- placeholder options, these should be dynamically generated based on available members -->
                </select>
                <button type="submit" class="btn btn-success mt-2">Υποβολή Επιλογής</button>
                <div id="chooseMembersFormMessage" class="mb-3"></div>
                </form>
                
            `;
        
        
       
    }
    if (data[0].status !== "Pending") {
        thesisCard += `
                            <button class="view-details-btn" onclick="toggleDetails()">Προβολή Τριμελούς Επιτροπής</button>
                            <div id="thesis-details" style="display:none;">
                            <h5>Τριμελής Επιτροπή:</h5>
                            <table class="table table-bordered mb-3">
                                <thead>
                                    <th>Μέλος Επιτροπής</th>
                                </thead>
                                <tbody id="committeeMembers">
                                    <tr>
                                        <td colspan="1" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tdody>
                            </table>
                        </div>
                        `;
                     

    }
    if (data[0].status === "Under Examination") {

        thesisCard += `
                <button class="btn btn-primary" onclick="showDraftTextForm()">Υπόβολη Πρόχειρου Κείμενου Διπλωματικής</button>
                <button class="btn btn-primary" onclick="showAdditionalMaterialForm()">Υποβολή Πρόσθετου Υλικού</button>
                <button class="btn btn-primary" onclick="showExamAppointment()">Ραντεβού Εξέτασης</button>
                <div id="draftTextForm" style="display:none;">
                    <h5>Υποβολή Πρόχειρου Κείμενου Διπλωματικής</h5>
                    <form id="submitDraftText" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="draftTextFile" class="form-label">Επιλέξτε αρχείο:</label>
                            <input type="file" class="form-control" id="draftTextFile" name="draft_text_file" accept=".pdf,.doc,.docx" required>          
                    </div>
                    <button type="submit" class="btn btn-primary">Υποβολή</button>
                    <div id="draftTextMessage" class="mt-2"></div>
                    </form>
                </div>
                <div id="additionalMaterialForm" style="display:none;">
                    <h5>Υποβολή Πρόσθετου Υλικού</h5>
                    <form id="submitAdditionalMaterial" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="additionalMaterialFile" class="form-label">Σύντομη περιγραφή πρόσθετου υλικού:</label>
                        <input type="text" class="form-control" id="additionalMaterialDescription" name="additional_material_description" required>
                        <label for="additionalMaterialFile" class="form-label">Εισάγετε πρόσθετο αρχείο:</label>
                        <input type="url" name="additional_material_file" class="form-control" id="additionalMaterialFile" accept=".pdf,.doc,.docx" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Υποβολή</button>
                    <div id="additionalMaterialMessage" class="mt-2"></div>
                    </form>
                </div>
                <div id="examTypeForm" class="mt-3" style="display:none;">
                    <h5>Επιλογή Τρόπου Εξέτασης</h5>
                    <form id="selectExamTypeForm">
                        <button type="button" class="btn btn-outline-primary me-2" id="inPersonBtn" onclick="toggleLiveExamination()">Δια Ζώσης</button>
                        <button type="button" class="btn btn-outline-secondary" id="remoteBtn" onclick="toggleRemoteExamination()">Εξ αποστάσεως</button>
                    </form>
                </div>
                <div id="liveExaminationForm" style="display:none;">
                    <h5>Δια Ζώσης Εξέταση</h5>
                    <form id="submitLiveExamination" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="liveExamDate" class="form-label">Ημερομηνία Εξέτασης:</label>
                            <input type="date" class="form-control" id="liveExamDate" name="live_exam_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="liveExamTime" class="form-label">Ώρα Εξέτασης:</label>
                            <input type="time" class="form-control" id="liveExamTime" name="live_exam_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="liveExamLocation" class="form-label">Τοποθεσία:</label>
                            <input type="text" class="form-control" id="liveExamLocation" name="live_exam_location" required>
                        </div>
                        <div id="liveExamMessage" class="mt-2"></div>
                        <button type="submit" class="btn btn-primary">Υποβολή</button>
                    </form>
                </div>
                <div id="remoteExaminationForm" style="display:none;">
                    <h5>Εξ Αποστάσεως Εξέταση</h5>
                    <form id="submitRemoteExamination" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="remoteExamDate" class="form-label">Ημερομηνία Εξέτασης:</label>
                            <input type="date" class="form-control" id="remoteExamDate" name="remote_exam_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="remoteExamTime" class="form-label">Ώρα Εξέτασης:</label>
                            <input type="time" class="form-control" id="remoteExamTime" name="remote_exam_time" required>
                        <div class="mb-3">
                            <label for="remoteExamLink" class="form-label">Σύνδεσμος Εξέτασης (π.χ. Zoom/Teams):</label>
                            <input type="url" class="form-control" id="remoteExamLink" name="remote_exam_link" required>
                        </div>
                        <div id="remoteExamMessage" class="mt-2"></div>
                        <button type="submit" class="btn btn-primary">Υποβολή</button>
                    </form>
                </div>
                      
                `;
       
    }
    // load nemertis link form and grades from api
    if (data[0].status === "Under Grading") {
        thesisCard += `
        <button class="btn btn-primary" onclick="showNemertisLinkSection()">Υποβολή Συνδέσμου Nemertis</button>
        <div id="practicalExamLinkSection">
            <a id="practicalExamLink" class="btn btn-primary">Προβολή Πρακτικού Εξέτασης</a>
        </div>
        <div id="nemertisLinkSection" style="display:none;">
            <h5>Σύνδεσμος Nemertis</h5>
            <form id="nemertisLinkForm">
                <div class="mb-3">
                    <label for="nemertisLinkInput" class="form-label">Εισάγετε το σύνδεσμο Nemertis:</label>
                    <input type="url" class="form-control" id="nemertisLinkInput" name="nemertis_link" placeholder="https://nemertes.library.upatras.gr/items/(κάτι)" required>
                </div>
                <button type="submit" class="btn btn-primary">Αποθήκευση Συνδέσμου</button>
            </form>
            <div id="nemertisLinkMessage" class="mt-2"></div>
        </div>
    `;


    }
    if (data[0].status === "Completed") {
        thesisCard += `
            <p class="text-success">Η διπλωματική σας εργασία έχει ολοκληρωθεί.</p>
            <button class="btn btn-primary" onclick="showChangeLog()">Προβολή Ιστορικού Αλλαγών</button>
            <button class="btn btn-primary" onclick="showExamPractical()">Προβολή Πρακτικού Εξέτασης</button>
            <div id="changeLogSection" style="display:none;">
                <h5>Χρονικό Αλλαγών</h5>
                <table class="table table-bordered mb-4" >
                <thead>
                    <tr>
                    <th>Ημερομηνία</th>
                    <th>Περιγραφή Αλλαγής</th>
                    </tr>
                </thead>
                <tbody id="changeLogTableBody">
                    <!-- Empty table body -->
                </tbody>
                </table>
            </div>
            <a id="examPracticalLink" href="#" class="btn btn-outline-secondary" target="_blank" style="display:none;">Πρακτικό Εξέτασης</a>
        `;
        // Load change log and practical exam link
    }

    thesisListContainer.innerHTML = thesisCard;
    // dynamically add event listeners for the buttons
    if(data[0].status!== "Pending"){
        fetchCommitteeMembers(data[0].thesis_assignment_id);
    }
    if(data[0].status === "Pending"){
        fetchTeachers();
        sendInvitation();
    }
    if(data[0].status === "Under Examination"){
        renderDraftTextForm();
        renderAdditionalMaterialForm();
        submitAppointmentLive();
        submitAppointmentRemote();
    }
    if(data[0].status === "Under Grading"){
        submitNemertisLink();
        loadPracticalExamPdf();
    }
    if(data[0].status === "Completed"){
        loadChangeLog();
        loadExamPracticalLink();
    }
}
function fetchCommitteeMembers(thesisAssignmentId) {
    const committeeMembersTable = document.getElementById("committeeMembers");
    if (!committeeMembersTable) return;
    committeeMembersTable.innerHTML = '';
    fetch(`../../api/student/get_committee_members.php?thesis_id=${thesisAssignmentId}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                committeeMembersTable.innerHTML = `<tr><td colspan="1" class="text-center text-danger">Σφάλμα κατά την ανάκτηση των μελών της επιτροπής.</td></tr>`;
                return;
            }
            if (data.length === 0) {
                committeeMembersTable.innerHTML = `<tr><td colspan="1" class="text-center">Δεν υπάρχουν διαθέσιμα μέλη επιτροπής.</td></tr>`;
                return;
            }
            data.forEach(member => {
                let role = '';
                if(member.is_supervisor){
                    role = " (Επιβλέπων)";
                }
                const row = document.createElement("tr");
                row.innerHTML = `<td>${member.name} ${role || ""}</td>`;
                committeeMembersTable.appendChild(row);
            });
        })
        .catch(err => {
            console.error("Σφάλμα κατά την ανάκτηση των μελών της επιτροπής:", err);
            committeeMembersTable.innerHTML = `<tr><td colspan="1" class="text-center text-danger">Σφάλμα κατά την ανάκτηση των μελών της επιτροπής.</td></tr>`;
        });
}

function submitAppointmentLive() {
        const liveExaminationForm = document.getElementById("submitLiveExamination");
        const liveExamMessage = document.getElementById("liveExamMessage");
        if (!liveExaminationForm) return;
        console.log("Setting up event listener for liveExaminationForm");
        liveExaminationForm.addEventListener("submit", function (e) {
            e.preventDefault();
            console.log("Form submitted, preparing to send live examination appointment");
            const formData = new FormData(this);
            fetch("../../api/student/submit_live_examination.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        liveExamMessage.classList.add('text-success');
                        liveExamMessage.textContent = "Το ραντεβού δια ζώσης εξέτασης υποβλήθηκε με επιτυχία!";
                    } else {
                        liveExamMessage.classList.add('text-danger');
                        liveExamMessage.textContent = "Σφάλμα κατά την υποβολή του ραντεβού δια ζώσης εξέτασης: " + data.message;
                    }
                })
                .catch(err => {
                    console.error("Σφάλμα κατά την υποβολή του ραντεβού δια ζώσης εξέτασης:", err);
                    liveExamMessage.classList.add('text-danger');
                    liveExamMessage.textContent = "Σφάλμα κατά την υποβολή των δεδομένων. Παρακαλώ δοκιμάστε ξανά.";
                });
        });
}
function submitAppointmentRemote() {
        const remoteExaminationForm = document.getElementById("submitRemoteExamination");
        const remoteExamMessage = document.getElementById("remoteExamMessage");
        if (!remoteExaminationForm) return;
        console.log("Setting up event listener for remoteExaminationForm");
        remoteExaminationForm.addEventListener("submit", function (e) {
            e.preventDefault();
            console.log("Form submitted, preparing to send remote examination appointment");
            const formData = new FormData(this);
            fetch("../../api/student/submit_remote_examination.php", {
                method: "POST",
                body: formData
            })

                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        remoteExamMessage.classList.add('text-success');
                        remoteExamMessage.textContent = "Το ραντεβού εξ αποστάσεως εξέτασης υποβλήθηκε με επιτυχία!";
                        // Clear the form after successful submission
                        remoteExaminationForm.reset();
                    } else {
                        remoteExamMessage.classList.add('text-danger');
                        remoteExamMessage.textContent = "Σφάλμα κατά την υποβολή του ραντεβού εξ αποστάσεως εξέτασης: " + data.message;
                    }
                })
                .catch(err => {
                    console.error("Σφάλμα κατά την υποβολή του ραντεβού εξ αποστάσεως εξέτασης:", err);
                    remoteExamMessage.classList.add('text-danger');
                    remoteExamMessage.textContent = "Σφάλμα κατά την υποβολή των δεδομένων. Παρακαλώ δοκιμάστε ξανά.";
                });
        });
}



function showNemertisLinkSection() {
    const nemertisLinkSection = document.getElementById("nemertisLinkSection");
    if (!nemertisLinkSection) return;
    if (nemertisLinkSection.style.display === "none" || nemertisLinkSection.style.display === "") {
        nemertisLinkSection.style.display = "block"; // Show the section
    }
    else {
        nemertisLinkSection.style.display = "none"; // Hide the section
    }
}
function submitNemertisLink() {
    const nemertisLinkForm = document.getElementById("nemertisLinkForm");
    console.log("Setting up event listener for nemertisLinkForm");
    nemertisLinkForm.addEventListener("submit", function (e) {
        e.preventDefault();
        console.log("Form submitted, preparing to send Nemertis link");
        const nemertisLinkInput = document.getElementById("nemertisLinkInput").value;
        const nemertisLinkMessage = document.getElementById("nemertisLinkMessage");
        //test regex for nemertis link
        const nemertisLinkRegex = /^https?:\/\/nemertes\.library\.upatras\.gr\/items\/[a-zA-Z0-9\-._~:/?#@!$&'()*+,;=%]+$/;
        if (!nemertisLinkRegex.test(nemertisLinkInput)) {
            nemertisLinkMessage.classList.remove('text-success');
            nemertisLinkMessage.classList.add('text-danger');
            nemertisLinkMessage.textContent = "Παρακαλώ εισάγετε έναν έγκυρο σύνδεσμο Nemertes (https://nemertes.library.upatras.gr/items/...).";
            return;
        }

        const bodyFormData = new FormData(this);
        fetch("../../api/student/submit_nemertis_link.php", {
            method: "POST",
            body: bodyFormData
        })
            .then(res => res.json())
            .then(text => {
                if(text.success) {
                    nemertisLinkMessage.classList.remove('text-danger');
                    nemertisLinkMessage.classList.add('text-success');
                    nemertisLinkMessage.textContent = "Ο σύνδεσμος Nemertes υποβλήθηκε με επιτυχία!";
                    // Clear the input field after successful submission
                    document.getElementById("nemertisLinkInput").value = "";
                }
                else {
                    nemertisLinkMessage.classList.remove('text-success');
                    nemertisLinkMessage.classList.add('text-danger');
                    nemertisLinkMessage.textContent = "Έχετε ήδη υποβάλει τον σύνδεσμο Nemertes ή υπάρχει κάποιο σφάλμα.";
                }

            })
            .catch(err => {
                console.error("Σφάλμα κατά την αποστολή:", err);
                alert("Σφάλμα κατά την αποστολή των δεδομένων. Παρακαλώ δοκιμάστε ξανά.");
            });
    });
}
function loadPracticalExamPdf() {
    const practicalExamLink = document.getElementById("practicalExamLink");
    const practicalExamLinkSection = document.getElementById("practicalExamLinkSection");
    if (!practicalExamLink) return;
    fetch("../../api/student/get_practical_exam_link.php")
        .then(res => res.json())
        .then(data => {
            if (!data[0].protocol || data[0].protocol === "") {
                practicalExamLinkSection.innerHTML = `<p class="text-danger">Δεν υπάρχει πρακτικό εξέτασης διαθέσιμο. Περιμένετε να βαθμολογήσουν και οι 3.</p>`;
            }
            else {
                practicalExamLink.href = data[0].protocol;
            }
        })
        .catch(err => console.error("Σφάλμα κατά την ανάκτηση του πρακτικού εξέτασης:", err));
}


function showChangeLog() {
    const changeLogTableBody = document.getElementById("changeLogSection");
    if (!changeLogTableBody) return;
    if (changeLogTableBody.style.display === "none" || changeLogTableBody.style.display === "") {
        changeLogTableBody.style.display = "table-row-group"; // Show the table body
    }
    else {
        changeLogTableBody.style.display = "none"; // Hide the table body
    }
}
function showExamPractical() {
    const examPracticalLink = document.getElementById("examPracticalLink");
    if (!examPracticalLink) return;
    if (examPracticalLink.style.display === "none" || examPracticalLink.style.display === "") {
        examPracticalLink.style.display = "inline-block"; // Show the link
    } else {
        examPracticalLink.style.display = "none"; // Hide the link
    }
}

function loadChangeLog() {
        const changeLogTableBody = document.getElementById("changeLogTableBody");
        
        fetch("../../api/student/get_change_log.php")
            .then(res => res.json())
            .then(data => {
                data.forEach(change => {
                    changeLogTableBody.innerHTML += `<tr>
                        <td>${change.change_timestamp}</td>
                        <td>${change.change_log}</td>
                    </tr>`;
                });
            })
            .catch(err => console.error("Σφάλμα κατά την ανάκτηση του ιστορικού αλλαγών:", err));
}
function loadExamPracticalLink() {
    const examPracticalLink = document.getElementById("examPracticalLink");
    fetch("../../api/student/get_practical_exam_link.php")
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            examPracticalLink.href = data[0].protocol;
        })
        .catch(err => console.error("Σφάλμα κατά την ανάκτηση του πρακτικού εξέτασης:", err));
}
function showThesisDetails() {
    const details = document.getElementById("chooseMembers");
    if (details.style.display === "none" || details.style.display === "") {
        details.style.display = "block";
    } else {
        details.style.display = "none";
    }
}
function fetchTeachers() {
    fetch("../../api/student/get_available_teachers.php")
        .then(res => res.json())
        .then(data => {
            const committeeMember1 = document.getElementById("chooseCommitteeMember1");
            const committeeMember2 = document.getElementById("chooseCommitteeMember2");

            if (data.error) {
                console.error(data.error);
                return;
            }

            data.forEach(teacher => {
                const option1 = document.createElement("option");
                option1.value = teacher.teacher_id;
                option1.textContent = teacher.name;
                committeeMember1.appendChild(option1);

                const option2 = document.createElement("option");
                option2.value = teacher.teacher_id;
                option2.textContent = teacher.name;
                committeeMember2.appendChild(option2);
            });
        })
        .catch(err => console.error("Σφάλμα κατά την ανάκτηση των διδασκόντων:", err));
}
function sendInvitation() {
    const chooseMembersForm = document.getElementById("chooseMembers");
    const chooseMembersFormMessage = document.getElementById("chooseMembersFormMessage");
    if (!chooseMembersForm) return;
    console.log("Setting up event listener for chooseMembersForm");
    chooseMembersForm.addEventListener("submit", function (e) {
        e.preventDefault();
        console.log("Form submitted, preparing to send invitation");
        const member1 = document.getElementById("chooseCommitteeMember1").value;
        const member2 = document.getElementById("chooseCommitteeMember2").value;

        if (member1 === "" || member2 === "" || member1 === member2) {
            chooseMembersFormMessage.classList.add('text-danger');
            chooseMembersFormMessage.textContent = "Παρακαλώ επιλέξτε δύο διαφορετικά μέλη της επιτροπής.";
            console.error("Invalid selection: Please select two different committee members.");
            member1.value = "";
            member2.value = "";
            return;
        }
        const bodyFormData = new FormData(this);
        fetch("../../api/student/send_invitation.php", {
            method: "POST",
            body: bodyFormData
        })
            .then(res => res.json())
            .then(text => {
                try {
                    if (text.success) {
                        chooseMembersFormMessage.classList.remove('text-danger');
                        chooseMembersFormMessage.classList.add('text-success');
                        chooseMembersFormMessage.textContent = "Η πρόσκληση εστάλη με επιτυχία!";
                        // Clear the form after successful submission
                        document.getElementById("chooseCommitteeMember1").value = "";
                        document.getElementById("chooseCommitteeMember2").value = "";
                        setTimeout(() => {
                            document.getElementById("view-theses").click();
                        },1000);
                    } else {
                        chooseMembersFormMessage.classList.remove('text-success');
                        chooseMembersFormMessage.classList.add('text-danger');
                        chooseMembersFormMessage.textContent = "Έχετε ήδη στείλει πρόσκληση σε ένα από τα μέλη της επιτροπής";
                    }
                } catch (err) {
                    console.error("Σφάλμα κατά το parsing:", err);
                    chooseMembersFormMessage.classList.add('text-danger');
                    chooseMembersFormMessage.textContent = "Σφάλμα κατά την υποβολή των δεδομένων. Παρακαλώ δοκιμάστε ξανά.";
                }
            })
            .catch(err => {
                console.error("Σφάλμα κατά την αποστολή:", err);
                chooseMembersFormMessage.classList.add('text-danger');
                chooseMembersFormMessage.textContent = "Σφάλμα κατά την υποβολή των δεδομένων. Παρακαλώ δοκιμάστε ξανά.";
            });
            
    });
    
}
function renderDraftTextForm() {
        const draftTextForm = document.getElementById("submitDraftText");
        const draftTextMessage = document.getElementById("draftTextMessage");
        console.log("Setting up event listener for draftTextForm");

        draftTextForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(`../../api/student/submit_draft_text.php`, {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        draftTextMessage.classList.add('text-success');
                        draftTextMessage.textContent = "Το πρόχειρο κείμενο υποβλήθηκε με επιτυχία!";
                        // Clear the form after successful submission
                        draftTextForm.reset();
                    } else {
                        draftTextMessage.classList.add('text-danger');
                        draftTextMessage.textContent = "Σφάλμα κατά την υποβολή του πρόχειρου κειμένου: " + data.message;
                    }
                })
                .catch(err => {
                    console.error("Σφάλμα κατά την υποβολή του πρόχειρου κειμένου:", err);
                    draftTextMessage.classList.add('text-danger');
                    draftTextMessage.textContent = "Σφάλμα κατά την υποβολή των δεδομένων. Παρακαλώ δοκιμάστε ξανά.";
                });
        });
}


function renderAdditionalMaterialForm() {
        const additionalMaterialForm = document.getElementById("submitAdditionalMaterial");
        const additionalMaterialMessage = document.getElementById("additionalMaterialMessage");
        if (additionalMaterialForm) {
            additionalMaterialForm.addEventListener("submit", function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch("../../api/student/submit_additional_material.php", {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            additionalMaterialMessage.classList.add('text-success');
                            additionalMaterialMessage.textContent = "Το πρόσθετο υλικό υποβλήθηκε με επιτυχία!";
                            // Clear the form after successful submission
                            additionalMaterialForm.reset();
                        } else {
                            additionalMaterialMessage.classList.add('text-danger');
                            additionalMaterialMessage.textContent = "Σφάλμα κατά την υποβολή του πρόσθετου υλικού: " + data.message;
                        }
                    })
                    .catch(err => {
                        console.error("Σφάλμα κατά την υποβολή του πρόσθετου υλικού:", err);
                        additionalMaterialMessage.classList.add('text-danger');
                        additionalMaterialMessage.textContent = "Σφάλμα κατά την υποβολή των δεδομένων. Παρακαλώ δοκιμάστε ξανά.";
                    });
            });
        }
}
function showDraftTextForm() {
    console.log("Toggling draft text form visibility");
    const draftTextForm = document.getElementById("draftTextForm");
    if (draftTextForm.style.display === "none" || draftTextForm.style.display === "") {
        draftTextForm.style.display = "block";
    } else {
        draftTextForm.style.display = "none";
    }
}
function showAdditionalMaterialForm() {
    console.log("Toggling additional material form visibility");
    const additionalMaterialForm = document.getElementById("additionalMaterialForm");
    if (additionalMaterialForm.style.display === "none" || additionalMaterialForm.style.display === "") {
        additionalMaterialForm.style.display = "block";
    } else {
        additionalMaterialForm.style.display = "none";
    }
}
function showExamAppointment() {

    const examTypeForm = document.getElementById("examTypeForm");
    const liveExaminationForm = document.getElementById("liveExaminationForm");
    const remoteExaminationForm = document.getElementById("remoteExaminationForm");
    const examTypeMessage = document.getElementById("examTypeMessage");
    if (examTypeForm.style.display === "none" || examTypeForm.style.display === "") {
        examTypeForm.style.display = "block";
    } else {
        examTypeForm.style.display = "none";
        liveExaminationForm.style.display = "none";
        remoteExaminationForm.style.display = "none";
        examTypeMessage.textContent = "";
    }

}
function toggleLiveExamination() {
    console.log("Toggling live examination form visibility");
    const liveExaminationForm = document.getElementById("liveExaminationForm");
    const remoteExaminationForm = document.getElementById("remoteExaminationForm");
    const examTypeMessage = document.getElementById("examTypeMessage");

    if (liveExaminationForm.style.display === "none" || liveExaminationForm.style.display === "") {
        liveExaminationForm.style.display = "block";
        remoteExaminationForm.style.display = "none";
        examTypeMessage.textContent = "Επιλέξατε δια ζώσης εξέταση.";
    } else {
        liveExaminationForm.style.display = "none";
        examTypeMessage.textContent = "";
    }
}
function toggleRemoteExamination() {
    console.log("Toggling remote examination form visibility");
    const liveExaminationForm = document.getElementById("liveExaminationForm");
    const remoteExaminationForm = document.getElementById("remoteExaminationForm");
    const examTypeMessage = document.getElementById("examTypeMessage");

    if (remoteExaminationForm.style.display === "none" || remoteExaminationForm.style.display === "") {
        remoteExaminationForm.style.display = "block";
        liveExaminationForm.style.display = "none";
        examTypeMessage.textContent = "Επιλέξατε εξ αποστάσεως εξέταση.";
    } else {
        remoteExaminationForm.style.display = "none";
        examTypeMessage.textContent = "";
    }
}