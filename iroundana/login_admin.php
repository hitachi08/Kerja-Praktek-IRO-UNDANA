<?php
require 'connect.php';

$error_message = "";

// Proses login
if (isset($_POST['login_admin'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validasi login
    $query = "SELECT * FROM admin WHERE username='$username' AND password_admin='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Login berhasil
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['log'] = 'True';
        $_SESSION['admin_name'] = $admin['username'];
        $_SESSION['admin_id'] = $admin['id_admin'];

        header('Location: admin.php');
        exit();
    } else {
        // Login gagal
        $error_message = "Username or Password is incorrect!";
    }
}

if (isset($_SESSION['log'])) {
    header('Location: admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - IRO UNDANA</title>
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/png">
    <link rel="apple-touch-icon" href="style/image/Logo_Undana.png">
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/x-icon">

    <meta name="theme-color" content="#ffffff">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style/login_admin.css">
</head>

<body>
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-80">
            <!-- Bagian Kiri -->
            <div
                class="col-lg-6 col-md-6 d-none d-md-flex align-items-center justify-content-center left text-white text-center">
                <div>
                    <h1 id="day" class="display-4 fw-bold date-time"></h1>
                    <p id="date" class="lead date-time"></p>
                    <p id="time" class="lead date-time"></p>
                </div>
            </div>

            <!-- Bagian Kanan -->
            <div class="col-lg-6 col-md-6 col-sm-12 right p-4 shadow rounded">
                <div class="container-right">
                    <!-- Logo dan Judul -->
                    <div class="logo-container mb-4">
                        <img src="style/image/Logo_Undana.png" alt="Logo Undana" class="logo img-fluid"
                            style="max-width: 150px;">
                        <div class="divider-all my-3"></div>
                        <div class="text-container">
                            <h2 class="fw-bold">IRO UNDANA</h2>
                            <p class="text-muted d-sm-none">Admin Login</p>
                            <p class="text-muted d-none d-md-block">Admin Login - Manage the System</p>
                        </div>
                    </div>

                    <!-- Form Login -->
                    <form action="login_admin.php" method="POST" class="text-start">
                        <div class="input-form mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" placeholder="Username" class="form-control text-white"
                                autocomplete="off" required>
                        </div>
                        <div class="input-form mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" placeholder="Password"
                                class="form-control text-white" required>
                        </div>

                        <!-- Show Password -->
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="showPassword">
                            <label for="showPassword" class="form-check-label">Show Password</label>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" name="login_admin" class="btn btn-primary btn-block">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <?php if (!empty($error_message)): ?>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
            <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-autohide="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= $error_message; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const toastElement = document.getElementById("errorToast");
            if (toastElement) {
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }
        });

        function updateTime() {
            const now = new Date();

            const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            document.getElementById('day').textContent = days[now.getDay()].toUpperCase();

            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            document.getElementById('date').textContent = now.toLocaleDateString('en-GB', options);

            document.getElementById('time').textContent = now.toLocaleTimeString();
        }

        setInterval(updateTime, 1000);
        updateTime();

        const showPasswordCheckbox = document.getElementById('showPassword');
        const passwordInput = document.getElementById('password');

        showPasswordCheckbox.addEventListener('change', () => {
            passwordInput.type = showPasswordCheckbox.checked ? 'text' : 'password';
        });
    </script>
</body>

</html>