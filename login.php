<?php
// index.php (Login Page)
require_once 'includes/auth.php';
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') header("Location: admin/dashboard.php");
    else if ($_SESSION['role'] === 'extension_worker') header("Location: extension-worker/dashboard.php");
    else header("Location: farmer/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADSSU Farmers Extension Services - Login</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Poppins & Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-green: #22C55E;
            --dark-green: #166534;
            --light-bg: #F8FAFC;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
        }
        .login-image {
            background: linear-gradient(rgba(22, 101, 52, 0.8), rgba(34, 197, 94, 0.8)), url('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') center/cover;
            width: 50%;
            padding: 3rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-form-container {
            width: 50%;
            padding: 3rem;
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
        @media (max-width: 768px) {
            .login-card { flex-direction: column; max-width: 400px; margin: 1rem; }
            .login-image { display: none; }
            .login-form-container { width: 100%; padding: 2rem; }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-image">
        <h2 class="mb-4" style="font-family: 'Poppins', sans-serif; font-weight:700;">Cultivating Growth Together.</h2>
        <p class="lead">Empowering our local farmers through modern extension services, continuous education, and seamless agricultural assistance.</p>
    </div>
    <div class="login-form-container">
        <div class="text-center mb-4">
            <h3 class="brand-title">ADSSU FESMS</h3>
            <p class="text-muted">Welcome back! Please login to your account.</p>
        </div>

        <form id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="mb-3">
                <label for="username" class="form-label text-muted small fw-bold">Username</label>
                <input type="text" class="form-control" id="username" name="username" required placeholder="Enter your username">
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label text-muted small fw-bold">Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn btn-primary w-100" id="loginBtn">
                <span class="spinner-border spinner-border-sm d-none" id="loginSpinner" role="status" aria-hidden="true"></span>
                Login
            </button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        let btn = $('#loginBtn');
        let spinner = $('#loginSpinner');
        
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        
        $.ajax({
            url: 'ajax/auth.php',
            type: 'POST',
            data: $(this).serialize() + '&action=login',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful',
                        text: 'Redirecting to your dashboard...',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            },
            error: function() {
                Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                btn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });
});
</script>

</body>
</html>
