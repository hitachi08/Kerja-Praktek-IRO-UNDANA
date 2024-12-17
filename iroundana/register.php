<?php
include('connect.php');

if (isset($_POST['register'])) {
    // Mengambil data dari form
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Memeriksa apakah email sudah terdaftar
    $email_check_query = "SELECT * FROM user WHERE email_user = '$email' LIMIT 1";
    $result = mysqli_query($conn, $email_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $toast_type = 'danger';
        $toast_message = 'Email is already registered, please use a different email!';
    } elseif ($password != $confirm_password) {
        $toast_type = 'warning';
        $toast_message = 'Passwords do not match!';
    } else {
        // Mengenkripsi password menggunakan bcrypt
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        // Menyimpan data ke database
        $query = "INSERT INTO user (nama_user, email_user, password) VALUES ('$first_name $last_name', '$email', '$password_hashed')";

        if (mysqli_query($conn, $query)) {
            $toast_type = 'success';
            $toast_message = 'Registration successful! Redirecting to login in 5 seconds.';
            $redirect = true;
        } else {
            $toast_type = 'danger';
            $toast_message = 'Error: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IRO UNDANA</title>
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/png">
    <link rel="apple-touch-icon" href="style/image/Logo_Undana.png">
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/x-icon">

    <meta name="theme-color" content="#ffffff">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/register.css">
</head>

<body>
    <!-- Toast -->
    <?php if (isset($toast_message)): ?>
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="liveToast" class="toast align-items-center text-bg-<?php echo $toast_type; ?>" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $toast_message; ?>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <?php if (isset($redirect)): ?>
            <script>
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 5000);
            </script>
        <?php endif; ?>
    <?php endif; ?>
    <div class="container-fluid pb-5">
        <!-- Logo -->
        <div class="nav-left col-12">
            <img src="user/images/Logo_Undana.png" alt="Logo Undana" width="50rem" class="m-0" />
            <div class="divider d-none d-lg-block"
                style="border-left: 3px solid #805f03; height: 3rem; margin: 0px 1rem;">
            </div>
            <div>
                <div class="d-none d-lg-block" style="color: white;">
                    <span style="font-size: 1.2rem; font-weight: 500">International Relations</span><br>
                    <span style="font-size: 1rem;">Office UNDANA</span>
                </div>
                <div class="d-block d-lg-none">
                    <h1 class="my-0 site-logo"
                        style="padding: 0.5rem 0px 0px 0.5rem; color: white; font-size: 1.2rem; font-weight: 600;">
                        IROUNDANA
                    </h1>
                </div>
            </div>
        </div>

        <!-- Tampilkan waktu -->
        <div class="clock row d-none d-lg-flex justify-content-center mb-4">
            <div class="col-auto text-center current-time">
                <div id="day-name"></div>
                <div id="date"></div>
                <div id="time"></div>
            </div>
        </div>

        <!-- Register Form Container -->
        <div class="row w-80 mx-0">
            <!-- Left Container with Image -->
            <div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-start container_left">
                <div class="text-light text-center p-5">
                    <span class="text-welcome">Get Started with IRO UNDANA <br> Sign Up Today</span>
                </div>
            </div>

            <!-- Right Container (Form) -->
            <div class="col-12 col-lg-6 bg-white p-4 shadow-sm">
                <span class="log_text fs-4 d-block text-center mb-4">Please Register</span>

                <!-- Registration Form -->
                <form id="registerForm" action="register.php" method="POST">
                    <!-- Fullname -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" id="first_name" name="first_name" placeholder="First Name"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" id="last_name" name="last_name" placeholder="Last Name"
                                class="form-control" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <input type="email" id="email" name="email" placeholder="Email" class="form-control" required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <input type="password" id="password" name="password" placeholder="Password" class="form-control"
                            required>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <input type="password" id="confirm_password" name="confirm_password"
                            placeholder="Confirm Password" class="form-control" required>
                    </div>

                    <!-- Show Password Checkbox -->
                    <div class="form-check mb-3">
                        <input type="checkbox" id="show-password" name="show-password" class="form-check-input">
                        <label for="show-password" class="form-check-label">Show Password</label>
                    </div>

                    <!-- Error Messages -->
                    <div id="email-error-message" style="display: none; color: orange;">Email is already
                        registered, please use a different email!</div>
                    <div id="error-message" style="display: none; color: red;">Passwords do not match!
                    </div>
                    <div id="password-validation-message" class="text-warning" style="display: none;">Password must be
                        at least 8 characters long.</div>

                    <!-- Register Button -->
                    <button type="submit" name="register" class="btn btn-primary w-100 mb-3">Register Now</button>

                    <!-- Register Link -->
                    <div class="text-center">
                        <span class="register-label">Already have an account? <a href="login.php">Sign
                                in</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="js/register.js"></script>

    <!-- Bootstrap and Script -->
    <script>
        // Menampilkan Toast jika ada
        const toastEl = document.getElementById('liveToast');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    </script>

    <!-- Show Error if PHP Flag is Set -->
    <?php if (isset($email_exists) && $email_exists): ?>
        <script>
            // Menampilkan pesan error
            document.getElementById('email-error-message').style.display = 'block';

            // Menyembunyikan pesan error setelah 5 detik
            setTimeout(function () {
                document.getElementById('email-error-message').style.display = 'none';
            }, 5000); // 5000 milidetik = 5 detik
        </script>
    <?php endif; ?>
</body>

</html>