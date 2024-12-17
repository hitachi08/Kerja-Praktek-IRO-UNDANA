<?php
//jika belum login

if (isset($_SESSION['log'])) {
    # code...
} else {
    //jika sudah login
    header('location:login_admin.php');
}
?>