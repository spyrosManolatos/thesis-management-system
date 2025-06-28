const loginForm = document.getElementById('loginForm');
const alertContainer = document.getElementById('alertContainer');
loginForm.addEventListener('submit', function (e) {
    e.preventDefault();

    // Clear previous alerts
    alertContainer.innerHTML = '';

    // Get form data
    const formData = new FormData(loginForm);

    fetch('../../includes/auth/check_credentials.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect on successful login
                window.location.href = data.redirect;
            } else {
                // Show error message
                alertContainer.innerHTML = `<div class="alert alert-danger mt-3">Λάθος username ή password</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alertContainer.innerHTML = '<div class="alert alert-danger mt-3">Σφάλμα σύνδεσης. Παρακαλώ προσπαθήστε ξανά.</div>';
        });
});