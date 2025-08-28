<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Symptom Reporting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Report Your Symptoms</h2>
        <form id="symptomForm">
            <div class="mb-3">
                <label for="patientName" class="form-label">Your Full Name</label>
                <input type="text" class="form-control" id="patientName" name="patientName" required>
            </div>

            <h5>Select Your Symptoms</h5>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="symptoms[]" value="Fever" id="fever">
                <label class="form-check-label" for="fever">Fever</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="symptoms[]" value="Headache" id="headache">
                <label class="form-check-label" for="headache">Headache</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="symptoms[]" value="Cough" id="cough">
                <label class="form-check-label" for="cough">Cough</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="symptoms[]" value="Stomach Pain" id="stomachPain">
                <label class="form-check-label" for="stomachPain">Stomach Pain</label>
            </div>

            <div class="mb-3 mt-3">
                <label for="additionalInfo" class="form-label">Additional Description</label>
                <textarea class="form-control" id="additionalInfo" name="additionalInfo" rows="3" placeholder="Describe your symptoms in detail..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit Symptoms</button>
        </form>
    </div>

    <script>
        document.getElementById('symptomForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('save_symptoms.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Success', 'Your symptoms have been sent to the doctor.', 'success');
                    document.getElementById('symptomForm').reset();
                } else {
                    Swal.fire('Error', 'There was a problem submitting your symptoms.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'An unexpected error occurred.', 'error');
            });
        });
    </script>
</body>
</html>
