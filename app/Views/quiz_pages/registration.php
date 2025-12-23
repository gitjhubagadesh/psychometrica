<?= $this->include('quiz_pages/header'); ?> <!-- Include header -->

<style>
    .registration-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background-color: #f8f9fa;
        padding: 20px;
    }

    .form-wrapper {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 900px;
    }

    .form-control, .form-select {
        border-radius: 6px;
        height: 40px;
        font-size: 14px;
    }

    .btn-save {
        background-color: #ff9900;
        color: white;
        font-weight: bold;
        width: 100%;
        padding: 10px;
        border-radius: 6px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control::placeholder {
        color: #aaa;
        font-style: italic;
    }

    .form_logo {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 30px auto 0 auto;
        background-color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 90%;
        width: fit-content;
    }

    .form_logo img {
        max-width: 100%;
        height: auto;
        display: block;
    }
    /* Custom style for date picker */
    input[type="date"].styled-date {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: #fff;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 6px;
        font-size: 14px;
        color: #333;
        width: 100%;
    }

    input[type="date"].styled-date:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
    }

</style>
<?php
$companyName = isset($user_data['company_name']) ? $user_data['company_name'] : '';
$isReadonly = !empty($companyName) ? 'readonly' : '';
?>
<div class="form_logo">
    <a href="<?= base_url(); ?>">
        <img src="<?= base_url('assets/images/logo/logo.png'); ?>" alt="Psychometrica Logo">
    </a>
</div>
<div class="registration-container">
    <div class="form-wrapper">



        <h4 class="fw-bold text-center mb-4">Registration</h4>

        <!-- Display Error Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <strong>Error!</strong> <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('save-registration'); ?>" method="POST">
            <div class="row">
                <!-- Column 1 -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">User ID</label>
                        <input type="text" class="form-control" id="user_id" name="user_id" value="<?= $user_data['user_id']; ?>" placeholder="User ID" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Middle Name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email ID</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email ID" required>
                    </div>
                    <div class="mb-3">
                        <label for="designation" class="form-label">Designation/Rank</label>
                        <input type="text" class="form-control" id="designation" name="designation" placeholder="Designation" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="male" value="Male" required>
                            <label class="form-check-label" for="male">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="female" value="Female" required>
                            <label class="form-check-label" for="female">Female</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-select" id="country" name="country" required>
                            <option value="">Select Country</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= $country['id']; ?>"><?= $country['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Column 2 -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_type" class="form-label">ID Type</label>
                        <select class="form-select" id="id_type" name="id_type" required>
                            <option value="">Select ID Type</option>
                            <option value="passport">Passport</option>
                            <option value="national_id">National ID</option>
                            <option value="driver_license">Driver’s License</option>
                            <option value="employee_id">Employee ID</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_number" class="form-label">ID Number</label>
                        <input type="text" class="form-control" id="id_number" name="id_number" placeholder="Enter ID Number" required>
                        <div id="id_error" class="text-danger small mt-1" style="display:none;"></div>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" placeholder="Date of Birth" required>
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" placeholder="City" required>
                    </div>
                    <div class="mb-3">
                        <label for="company" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company" name="company" 
                               placeholder="Company Name"
                               value="<?= esc($companyName) ?>"
                               <?= $isReadonly ?>>
                    </div>
                    <div class="mb-3">
                        <label for="experience" class="form-label">Work Experience</label>
                        <select class="form-select" id="experience" name="experience" required>
                            <option value="">Select</option>
                            <?php for ($i = 1; $i <= 50; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> Years</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn-save">Save</button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const idType = document.getElementById('id_type');
        const idNumber = document.getElementById('id_number');
        const idError = document.getElementById('id_error');

        function validateID() {
            const type = idType.value;
            const value = idNumber.value.trim();
            let isValid = true;
            let message = '';

            switch (type) {
                case 'passport':
                    isValid = /^[a-zA-Z0-9]{6,9}$/.test(value);
                    message = 'Passport must be 6–9 alphanumeric characters.';
                    break;
                case 'national_id':
                    isValid = /^[0-9]{12}$/.test(value);
                    message = 'National ID must be exactly 12 digits.';
                    break;
                case 'driver_license':
                    isValid = /^[a-zA-Z0-9]{8,16}$/.test(value);
                    message = 'Driver’s License must be 8–16 alphanumeric characters.';
                    break;
                case 'employee_id':
                    isValid = /^[a-zA-Z0-9]{4,12}$/.test(value);
                    message = 'Employee ID must be 4–12 alphanumeric characters.';
                    break;
                case 'other':
                    isValid = value.length > 0;
                    message = 'Please enter an ID number.';
                    break;
                default:
                    isValid = false;
                    message = 'Please select an ID type.';
            }

            if (!isValid) {
                idError.textContent = message;
                idError.style.display = 'block';
                idNumber.classList.add('is-invalid');
            } else {
                idError.style.display = 'none';
                idNumber.classList.remove('is-invalid');
            }

            return isValid;
        }

        // Validate on blur or change
        idNumber.addEventListener('blur', validateID);
        idType.addEventListener('change', validateID);

        // Optional: validate on form submit
        const form = idNumber.closest('form');
        form.addEventListener('submit', function (e) {
            if (!validateID()) {
                e.preventDefault();
            }
        });
    });
</script>

<?= $this->include('quiz_pages/footer'); ?> <!-- Include footer -->
