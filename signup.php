<?php
// signup.php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') header("Location: admin/dashboard.php");
    else if ($_SESSION['role'] === 'extension_worker') header("Location: extension-worker/dashboard.php");
    else header("Location: farmer/dashboard.php");
    exit();
}
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADSSU Farmers Extension Services - Sign Up</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-green: #22C55E;
            --dark-green: #166534;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .signup-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 600px;
            padding: 3rem;
            margin: 1rem;
        }
        .brand-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--dark-green);
        }
        .btn-primary {
            background-color: var(--primary-green);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: var(--dark-green);
            transform: translateY(-2px);
        }
        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.25);
            border-color: var(--primary-green);
        }
    </style>
</head>
<body>

<div class="signup-card">
    <div class="text-center mb-4">
        <h3 class="brand-title">ADSSU FESMS</h3>
        <p class="text-muted">Create your farmer account today.</p>
    </div>

    <form id="signupForm">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="mb-3">
            <label for="full_name" class="form-label text-muted small fw-bold">Full Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="full_name" name="full_name" required placeholder="Enter your full name">
        </div>

        <div class="mb-3">
            <label for="username" class="form-label text-muted small fw-bold">Username <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="username" name="username" required placeholder="Choose a username">
        </div>
        
        <div class="mb-4">
            <label for="password" class="form-label text-muted small fw-bold">Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Create a password">
        </div>
        
        <button type="submit" class="btn btn-primary w-100" id="signupBtn">
            <span class="spinner-border spinner-border-sm d-none" id="signupSpinner" role="status" aria-hidden="true"></span>
            Sign Up
        </button>
        
        <div class="text-center mt-4">
            <span class="text-muted small">Already have an account? <a href="login.php" class="text-success text-decoration-none fw-bold">Login here</a></span>
            <br>
            <span class="text-muted small"><a href="index.php" class="text-success text-decoration-none fw-bold">Return to Home</a></span>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#signupForm').on('submit', function(e) {
        e.preventDefault();
        let btn = $('#signupBtn');
        let spinner = $('#signupSpinner');
        
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        
        $.ajax({
            url: 'ajax/auth.php',
            type: 'POST',
            data: $(this).serialize() + '&action=signup',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Account Created!',
                        text: 'You can now login with your credentials.',
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            },
            error: function() {
                Swal.fire('Error', 'Server error occurred.', 'error');
                btn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });
});
</script>

</body>
</html>
