<?php

include('connect.php');

// Variabel sesi default
$nama_user = isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Guest';

// Jika tombol login ditekan
if (isset($_POST['login'])) {
    // Ambil data dari form login
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cek apakah email ada di database
    $query = "SELECT * FROM user WHERE email_user = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $hashed_password = $user['password'];

        // Verifikasi password
        if (password_verify($password, $hashed_password)) {
            // Jika email dan password cocok
            $_SESSION['nama_user'] = $user['nama_user']; // Simpan nama user ke sesi
            $_SESSION['id_user'] = $user['id_user'];    // Simpan id_user ke sesi
            $_SESSION['email_user'] = $user['email_user'];    // Simpan id_user ke sesi


            // **Perbarui last_login di database**
            $id_user = $user['id_user'];
            $update_last_login = "UPDATE user SET last_login = CURRENT_TIMESTAMP WHERE id_user = '$id_user'";
            if (!mysqli_query($conn, $update_last_login)) {
                // Tangani jika update gagal
                $error_message = "Failed to update last login: " . mysqli_error($conn);
            }

            // Redirect ke halaman dashboard
            header("Location: index.php?page=home");
            mysqli_close($conn); // Tutup koneksi sebelum redirect
            exit;
        } else {
            // Jika password salah
            $error_message = "Incorrect password.";
        }
    } else {
        // Jika email tidak ditemukan
        $error_message = "Email not registered.";
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IRO UNDANA</title>
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/png">
    <link rel="apple-touch-icon" href="style/image/Logo_Undana.png">
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/x-icon">

    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="style/login_user.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <div class="container-fluid">
        <!-- Logo -->
        <div class="nav-left col-6">
            <img src="user/images/Logo_Undana.png" alt="Logo Undana" width="50rem" class="m-0" />
            <div class="divider d-none d-lg-block"
                style="border-left: 3px solid #805f03; height: 3rem; margin: 0px 1rem;"></div>
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

        <!-- Login Section -->
        <div class="row container-all">

            <!-- Container kiri -->
            <div
                class="col-lg-6 d-none d-lg-flex align-items-start justify-content-center container_left bg-light text-center">
                <span class="text-center text-welcome">Welcome Back! <?php echo htmlspecialchars($nama_user); ?> <br>
                    Log In to IRO
                    UNDANA</span>
            </div>
            <!-- End of Container kiri -->

            <!-- Container kanan -->
            <div class="col-12 col-lg-6 container_right d-flex justify-content-center align-items-center">
                <div class="form_input w-100">
                    <span class="log_text d-block text-center mb-4">Please Log In</span>
                    <div class="form_input">
                        <form id="loginForm" action="" method="POST" class="px-3">

                            <!-- Email -->
                            <input type="email" id="email" name="email" placeholder="Email" class="form-control mb-3"
                                required>
                                
                            <!-- Password -->
                            <input type="password" id="password" name="password" placeholder="Password"
                                class="form-control mb-3" required>

                            <!-- Show Password Checkbox -->
                            <div class="form-check mb-3">
                                <input type="checkbox" id="show-password" name="show-password" class="form-check-input">
                                <label for="show-password" class="form-check-label">Show Password</label>
                            </div>

                            <!-- Login Button -->
                            <button type="submit" name="login" class="btn btn-primary w-100 mb-3">Login Now</button>

                            <!-- Register Link -->
                            <div class="text-center">
                                <span class="register-label">Don't have an account? <a href="register.php">Sign
                                        up</a></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End of Container kanan -->
        </div>
    </div>

    <!-- Toast Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <!-- Pesan error akan diisi dinamis -->
                    <?php if (isset($error_message)): ?>
                        <span><?php echo htmlspecialchars($error_message); ?></span>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Cek jika terdapat error_message di PHP
            <?php if (isset($error_message)): ?>
                var toastElement = document.getElementById('errorToast');
                var toast = new bootstrap.Toast(toastElement);
                toast.show();
            <?php endif; ?>
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="js/login_user.js"></script>
</body>

</html>