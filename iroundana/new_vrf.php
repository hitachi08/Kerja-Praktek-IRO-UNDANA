<?php
// Sertakan file koneksi database
include('connect.php');

if (!isset($_SESSION['id_user'])) {
    header("Location: login_user.php");
    exit;
}
$id_user = $_SESSION['id_user'];

// Verifikasi apakah id_user valid di tabel user
$query_check_user = "SELECT id_user FROM user WHERE id_user = '$id_user'";
$result_check_user = mysqli_query($conn, $query_check_user);

if (mysqli_num_rows($result_check_user) == 0) {
    die("Error: id_user from session not found in user table.");
}

// Cek apakah ada parameter id_vrf (pengajuan lama)
$id_vrf = isset($_GET['id_vrf']) ? $_GET['id_vrf'] : null;
$parent_vrf_id = null; // Default parent_vrf_id adalah NULL

$data_lama = null;
$orang_list = [];
$kontak_undana_list = [];
$delegasi_list = [];
$interpreter_value = '';

// Jika id_vrf ada, ambil data pengajuan lama
if ($id_vrf) {
    $query_check_parent = "SELECT * FROM vrf WHERE id_vrf = '$id_vrf' AND id_user = '$id_user'";
    $result_check_parent = mysqli_query($conn, $query_check_parent);

    if (mysqli_num_rows($result_check_parent) > 0) {
        $data_lama = mysqli_fetch_assoc($result_check_parent);
        $parent_vrf_id = $data_lama['id_vrf']; // Tetapkan parent_vrf_id jika ditemukan

        // Ambil data orang yang ditemui
        $query_orang = "SELECT * FROM orang WHERE id_vrf = '$id_vrf'";
        $result_orang = mysqli_query($conn, $query_orang);
        $orang_list = mysqli_fetch_all($result_orang, MYSQLI_ASSOC);

        // Ambil data kontak undana
        $query_kontak_undana = "SELECT * FROM kontak_undana WHERE id_vrf = '$id_vrf'";
        $result_kontak_undana = mysqli_query($conn, $query_kontak_undana);
        $kontak_undana_list = mysqli_fetch_all($result_kontak_undana, MYSQLI_ASSOC);

        // Ambil data delegasi
        $query_delegasi = "SELECT * FROM delegasi WHERE id_vrf = '$id_vrf'";
        $result_delegasi = mysqli_query($conn, $query_delegasi);
        $delegasi_list = mysqli_fetch_all($result_delegasi, MYSQLI_ASSOC);

        // Ambil nilai interpreter
        $query_interpreter = "SELECT interpreter FROM vrf WHERE id_vrf = '$id_vrf'";
        $result_interpreter = mysqli_query($conn, $query_interpreter);
        $row_interpreter = mysqli_fetch_assoc($result_interpreter);
        $interpreter_value = $row_interpreter['interpreter'] ?? '';
    } else {
        die("Error: Invalid parent_vrf_id or not authorized to edit this submission.");
    }
}

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $tgl_kunjungan = $_POST['tgl_kunjungan'];
    $waktu_kunjungan = $_POST['waktu_kunjungan'];
    $durasi_kunjungan = $_POST['durasi_kunjungan'];
    $first_name = $_POST['nama_depan'];
    $last_name = $_POST['nama_belakang'];
    $nama_pemohon = $first_name . " " . $last_name;
    $posisi_pemohon = $_POST['posisi_pemohon'];
    $institusi_pemohon = $_POST['institusi_pemohon'];
    $website_pemohon = $_POST['website_pemohon'];
    $email_pemohon = $_POST['email_pemohon'];
    $telepon_pemohon = $_POST['telepon_pemohon'];
    $faks_pemohon = $_POST['faks_pemohon'];
    $deskripsi_institusi = $_POST['deskripsi_institusi'];
    $tujuan_kunjungan = $_POST['tujuan_kunjungan'];
    $bidang_pembahasan = $_POST['bidang_pembahasan'];
    $interpreter = $_POST['interpreter'];
    $orang_ditemui = $_POST['orang_ditemui'];
    $status = 'pending';  // Status VRF awal adalah pending
    $id_admin = '1';

    // Memulai transaksi
    mysqli_begin_transaction($conn);
    $isSaved = false;  // Set default ke false

    try {
        // 1. Cek jadwal bentrok sebelum menyimpan data VRF
        $sql_check_jadwal = "SELECT * FROM vrf WHERE tgl_kunjungan = '$tgl_kunjungan' AND waktu_kunjungan = '$waktu_kunjungan' AND status = 'approved'";
        $result_check_jadwal = mysqli_query($conn, $sql_check_jadwal);

        if (mysqli_num_rows($result_check_jadwal) > 0) {
            // Jika jadwal bentrok, tampilkan pop-up error
            echo '<script>
                window.onload = function() {
                    showErrorPopup("The schedule is already taken at the selected time. Please choose another time.");
                }
            </script>';
            throw new Exception("The schedule is already taken at the selected time. Please choose another time.");
        }

        // 2. Simpan data VRF
        $sql_vrf = "INSERT INTO vrf (tgl_kunjungan, waktu_kunjungan, durasi_kunjungan, nama_pemohon, posisi_pemohon, institusi_pemohon, website_pemohon, email_pemohon, telepon_pemohon, faks_pemohon, deskripsi_institusi, tujuan_kunjungan, bidang_pembahasan, interpreter, status, id_user, parent_vrf_id)
        VALUES ('$tgl_kunjungan', '$waktu_kunjungan', '$durasi_kunjungan', '$nama_pemohon', '$posisi_pemohon', '$institusi_pemohon', '$website_pemohon', '$email_pemohon', '$telepon_pemohon', '$faks_pemohon', '$deskripsi_institusi', '$tujuan_kunjungan', '$bidang_pembahasan', '$interpreter', '$status', '$id_user', '$parent_vrf_id')";

        if (!mysqli_query($conn, $sql_vrf)) {
            throw new Exception("Error saving VRF: " . mysqli_error($conn));
        }

        $id_vrf = mysqli_insert_id($conn);

        // 3. Simpan kontak_undana jika ada
        if (!empty($_POST['kontak_undana'])) {
            foreach ($_POST['kontak_undana'] as $kontak) {
                $gelar = $kontak['gelar_kontak'];
                $nama_depan = $kontak['nama_depan_kontak'];
                $nama_belakang = $kontak['nama_belakang_kontak'];
                $posisi = $kontak['posisi_kontak'];

                $sql_kontak = "INSERT INTO kontak_undana (gelar_kontak, nama_depan_kontak, nama_belakang_kontak, posisi_kontak, id_vrf)
                VALUES ('$gelar', '$nama_depan', '$nama_belakang', '$posisi', '$id_vrf')";

                if (!mysqli_query($conn, $sql_kontak)) {
                    throw new Exception("Error saving Kontak Undana: " . mysqli_error($conn));
                }
            }
        }

        // 4. Simpan delegasi jika ada
        if (!empty($_POST['delegasi'])) {
            foreach ($_POST['delegasi'] as $delegasi) {
                $gelar_delegasi = $delegasi['gelar'];
                $nama_depan_delegasi = $delegasi['nama_depan'];
                $nama_belakang_delegasi = $delegasi['nama_belakang'];
                $posisi_delegasi = $delegasi['posisi'];

                $sql_delegasi = "INSERT INTO delegasi (gelar, nama_depan, nama_belakang, posisi, id_vrf)
                VALUES ('$gelar_delegasi', '$nama_depan_delegasi', '$nama_belakang_delegasi', '$posisi_delegasi', '$id_vrf')";

                if (!mysqli_query($conn, $sql_delegasi)) {
                    throw new Exception("Error saving Delegasi: " . mysqli_error($conn));
                }
            }
        }

        // 5. Simpan orang_ditemui jika ada
        if (!empty($_POST['orang_ditemui']) && is_array($_POST['orang_ditemui'])) {
            foreach ($_POST['orang_ditemui'] as $orang_ditemui) {
                if (!empty($orang_ditemui['nama'])) {
                    $nama_orang_ditemui = mysqli_real_escape_string($conn, $orang_ditemui['nama']);

                    $sql_orang_ditemui = "INSERT INTO orang (nama_orang, id_vrf)
                                          VALUES ('$nama_orang_ditemui', '$id_vrf')";

                    if (!mysqli_query($conn, $sql_orang_ditemui)) {
                        throw new Exception("Error saving person met: " . mysqli_error($conn));
                    }
                }
            }
        }

        // Set status menjadi berhasil jika semua proses berhasil
        $isSaved = true;

        // Commit transaksi jika tidak ada error
        mysqli_commit($conn);

        // Tampilkan pop-up sukses jika data berhasil disimpan
        echo '<script>
            window.onload = function() {
                showPopup();
            }
        </script>';

    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($conn);
    }
    
    // Tutup koneksi
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Request Form</title>
    <link rel="stylesheet" href="style/vrf.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <navbar class="navbar-all">
        <div class="navbar-left-all">
            <img src="style/image/Logo_undana.png" alt="Logo Undana" width="40px" class="logo-all">
            <div class="divider-all"></div>
            <div class="text-container-all">
                <span class="iro-all">International Relations</span>
                <small class="iro-subtitle-all">Office UNDANA</small>
            </div>
            <nav class="navbar-menu-all">
                <a href="homepage_user.php" class="menu-item-all">HOME</a>
                <div class="dropdown-all">
                    <button class="menu-item-all dropdown-btn-all letter-all">LETTER<i
                            class="fas fa-caret-down arrow-all"></i></button>
                    <div class="dropdown-content-all">
                        <a href="vrf.php">Visit Request Form</a>
                        <a href="status.php">Submission Status</a>
                        <a href="review.php">Review</a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="navbar-right-all">
            <img src="style/image/teamwork.png" width="50px" id="profile-icon-all">
            <div class="text-user-all">
                <!-- Menampilkan nama pengguna yang diambil dari sesi -->
                <?php
                if (isset($_SESSION['nama_user'])) {
                    // Menampilkan nama pengguna
                    echo "<span class='user-name-all'>" . $_SESSION['nama_user'] . "</span>";
                } else {
                    // Jika tidak ada nama user dalam sesi, tampilkan "Guest"
                    echo "<span>Guest</span>";
                }
                ?>
                <span>Visitors</span>
            </div>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu-all" id="dropdown-menu-all">
                <a href="login_user.php">Logout</a>
            </div>
        </div>
    </navbar>
    <div class="container">
        <!-- Header dengan logo dan informasi kontak -->
        <div class="header">
            <div class="header-left">
                <img src="style/image/Logo_Undana.png" alt="Logo Undana" class="logo">
            </div>
            <div class="header-right">
                <h3>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h3>
                <h2>UNIVERSITAS NUSA CENDANA</h2>
                <p>Address: Jln. Adisucipto Penfui, PO BOX 104, Kupang 85001, NTT</p>
                <p>Phone: (0380) 881580, 8031580; Fax: 0380 – 881674</p>
                <p>Website: <a href="https://www.undana.ac.id" target="_blank">https://www.undana.ac.id</a>; E-mail: <a
                        href="mailto:info@undana.ac.id">info@undana.ac.id</a></p>
            </div>
        </div>

        <h1>New Visit Request Form</h1>
        <!-- Form yang mengirimkan data menggunakan method POST -->
        <form id="vrfForm" method="POST" action="">

            <?php
            // Cek apakah ada data waktu kunjungan dan memformatnya untuk input waktu
            $waktu_kunjungan = isset($data_lama['waktu_kunjungan']) ? substr($data_lama['waktu_kunjungan'], 0, 5) : ''; // Ambil jam dan menit (HH:MM)
            ?>

            <!-- Input Tanggal, Waktu dan Durasi dalam satu baris -->
            <div class="form-group inline-group">
                <label for="tgl_kunjungan">Date and Time of Proposed Visit</label>
                <div class="date-time-container">
                    <input type="date" id="tgl_kunjungan" name="tgl_kunjungan"
                        value="<?php echo isset($data_lama['tgl_kunjungan']) ? $data_lama['tgl_kunjungan'] : ''; ?>"
                        required>

                    <input type="time" id="waktu_kunjungan" name="waktu_kunjungan"
                        value="<?php echo $waktu_kunjungan; ?>" required>

                    <input type="number" id="durasi_kunjungan" name="durasi_kunjungan"
                        value="<?php echo isset($data_lama['durasi_kunjungan']) ? $data_lama['durasi_kunjungan'] : ''; ?>"
                        min="1" placeholder="Duration (hour)" required>

                    <span class="clear-text" onclick="clearDateTime()">Clear</span>
                </div>
            </div>

            <?php
            // Memecah nama pemohon menjadi nama depan dan nama belakang
            $full_name = isset($data_lama['nama_pemohon']) ? $data_lama['nama_pemohon'] : '';
            $name_parts = explode(' ', $full_name);

            // Jika hanya satu kata, maka dianggap sebagai nama depan
            $first_name = isset($name_parts[0]) ? $name_parts[0] : '';
            $last_name = isset($name_parts[1]) ? implode(' ', array_slice($name_parts, 1)) : ''; // Menggabungkan sisa nama belakang
            ?>
            <div class="InformasiPemohonContainer">
                <h2>Person Making the Visit Request</h2>

                <label for="nama_pemohon">Full Name</label>
                <div class="form-group fullname">
                    <!-- Nama depan dan nama belakang -->
                    <input type="text" id="nama_depan" name="nama_depan"
                        value="<?php echo isset($first_name) ? $first_name : ''; ?>" placeholder="First Name" required>
                    <input type="text" id="nama_belakang" name="nama_belakang"
                        value="<?php echo isset($last_name) ? $last_name : ''; ?>" placeholder="Last Name" required>
                </div>

                <div class="form-group">
                    <label for="posisi_pemohon">Position</label>
                    <input type="text" id="posisi_pemohon" name="posisi_pemohon"
                        value="<?php echo isset($data_lama['posisi_pemohon']) ? $data_lama['posisi_pemohon'] : ''; ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="institusi_pemohon">Institution / Organization</label>
                    <input type="text" id="institusi_pemohon" name="institusi_pemohon"
                        value="<?php echo isset($data_lama['institusi_pemohon']) ? $data_lama['institusi_pemohon'] : ''; ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="website_pemohon">Institution Website</label>
                    <input type="url" id="website_pemohon" name="website_pemohon"
                        value="<?php echo isset($data_lama['website_pemohon']) ? $data_lama['website_pemohon'] : ''; ?>">
                </div>
            </div>

            <div class="KontakPemohon">
                <h2>Person Contact Making the Visit Request</h2>

                <div class="form-group">
                    <label for="email_pemohon">E-Mail</label>
                    <input type="email" id="email_pemohon" name="email_pemohon"
                        value="<?php echo isset($data_lama['email_pemohon']) ? $data_lama['email_pemohon'] : ''; ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="telepon_pemohon">Phone / Mobile Phone</label>
                    <input type="tel" id="telepon_pemohon" name="telepon_pemohon"
                        value="<?php echo isset($data_lama['telepon_pemohon']) ? $data_lama['telepon_pemohon'] : ''; ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="faks_pemohon">Facsimile</label>
                    <input type="tel" id="faks_pemohon" name="faks_pemohon"
                        value="<?php echo isset($data_lama['faks_pemohon']) ? $data_lama['faks_pemohon'] : ''; ?>">
                </div>
            </div>

            <div class="DeskripsiPemohon">
                <h2>Institutional Visit and Discussion Plan</h2>

                <div class="form-group">
                    <label for="deskripsi_institusi">Overview of the Institution/Organization :</label>
                    <textarea id="deskripsi_institusi" name="deskripsi_institusi" rows="4" required>
            <?php echo isset($data_lama['deskripsi_institusi']) ? $data_lama['deskripsi_institusi'] : ''; ?>
        </textarea>
                </div>

                <div class="form-group">
                    <label for="tujuan_kunjungan">Purpose of Visit</label>
                    <textarea id="tujuan_kunjungan" name="tujuan_kunjungan" rows="4" required>
            <?php echo isset($data_lama['tujuan_kunjungan']) ? $data_lama['tujuan_kunjungan'] : ''; ?>
        </textarea>
                </div>

                <div id="personMeetContainer">
                    <h2>Person(s) You Would Like To Meet</h2>
                    <div id="personMeetList">
                        <?php
                        // Jika ada data orang yang ditemui
                        if (!empty($orang_list)) {
                            foreach ($orang_list as $index => $orang) {
                                ?>
                                <div class="person-meet-form">
                                    <input type="text" name="orang_ditemui[<?php echo $index; ?>][nama]" placeholder="Name"
                                        value="<?php echo htmlspecialchars(trim($orang['nama_orang'])); ?>" required>
                                </div>
                                <?php
                            }
                        } else {
                            // Jika tidak ada data orang, tampilkan satu input kosong
                            ?>
                            <div class="person-meet-form">
                                <input type="text" name="orang_ditemui[0][nama]" placeholder="Name" value="" required>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="person-meet-actions">
                        <label id="addPersonMeet">Add Input</label>
                        <label id="removePersonMeet" class="disabled" onclick="event.preventDefault()">Remove
                            Input</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bidang_pembahasan" class="area">Specific Areas/Topics of Interest for Discussion</label>
                    <textarea id="bidang_pembahasan" name="bidang_pembahasan" rows="4" required>
            <?php echo isset($data_lama['bidang_pembahasan']) ? $data_lama['bidang_pembahasan'] : ''; ?>
        </textarea>
                </div>
            </div>

            <div id="kontakUndanaContainer">
                <h2>Contact Person at Universitas Nusa Cendana, if any</h2>
                <div id="kontakUndanaList">
                    <?php
                    // Jika ada data kontak undana
                    if (!empty($kontak_undana_list)) {
                        foreach ($kontak_undana_list as $index => $kontak) {
                            ?>
                            <div class="kontak-form">
                                <input type="text" name="kontak_undana[<?php echo $index; ?>][gelar_kontak]" placeholder="Title"
                                    value="<?php echo htmlspecialchars(trim($kontak['gelar_kontak'])); ?>">
                                <input type="text" name="kontak_undana[<?php echo $index; ?>][nama_depan_kontak]"
                                    placeholder="First Name"
                                    value="<?php echo htmlspecialchars(trim($kontak['nama_depan_kontak'])); ?>">
                                <input type="text" name="kontak_undana[<?php echo $index; ?>][nama_belakang_kontak]"
                                    placeholder="Last Name"
                                    value="<?php echo htmlspecialchars(trim($kontak['nama_belakang_kontak'])); ?>">
                                <input type="text" name="kontak_undana[<?php echo $index; ?>][posisi_kontak]"
                                    placeholder="Position"
                                    value="<?php echo htmlspecialchars(trim($kontak['posisi_kontak'])); ?>">
                            </div>
                            <?php
                        }
                    } else {
                        // Jika tidak ada data kontak undana, tampilkan satu form kosong
                        ?>
                        <div class="kontak-form">
                            <input type="text" name="kontak_undana[0][gelar_kontak]" placeholder="Title" value="">
                            <input type="text" name="kontak_undana[0][nama_depan_kontak]" placeholder="First Name" value="">
                            <input type="text" name="kontak_undana[0][nama_belakang_kontak]" placeholder="Last Name"
                                value="">
                            <input type="text" name="kontak_undana[0][posisi_kontak]" placeholder="Position" value="">
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="kontak-actions">
                    <label id="addKontakUndana">Add Input</label>
                    <label id="removeKontakUndana" class="disabled" onclick="event.preventDefault()">Remove
                        Input</label>
                </div>
            </div>

            <div id="delegasiContainer">
                <h2>Names of Delegation/Visitors</h2>
                <div id="delegasiList">
                    <?php
                    // Jika ada data delegasi
                    if (!empty($delegasi_list)) {
                        foreach ($delegasi_list as $index => $delegasi) {
                            ?>
                            <div class="delegasi-form">
                                <input type="text" name="delegasi[<?php echo $index; ?>][gelar]" placeholder="Title"
                                    value="<?php echo htmlspecialchars(trim($delegasi['gelar'])); ?>">
                                <input type="text" name="delegasi[<?php echo $index; ?>][nama_depan]" placeholder="First Name"
                                    value="<?php echo htmlspecialchars(trim($delegasi['nama_depan'])); ?>">
                                <input type="text" name="delegasi[<?php echo $index; ?>][nama_belakang]" placeholder="Last Name"
                                    value="<?php echo htmlspecialchars(trim($delegasi['nama_belakang'])); ?>">
                                <input type="text" name="delegasi[<?php echo $index; ?>][posisi]" placeholder="Position"
                                    value="<?php echo htmlspecialchars(trim($delegasi['posisi'])); ?>">
                            </div>
                            <?php
                        }
                    } else {
                        // Jika tidak ada data delegasi, tampilkan satu form kosong
                        ?>
                        <div class="delegasi-form">
                            <input type="text" name="delegasi[0][gelar]" placeholder="Title" value="">
                            <input type="text" name="delegasi[0][nama_depan]" placeholder="First Name" value="">
                            <input type="text" name="delegasi[0][nama_belakang]" placeholder="Last Name" value="">
                            <input type="text" name="delegasi[0][posisi]" placeholder="Position" value="">
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="delegasi-actions">
                    <label id="addDelegasi">Add Input</label>
                    <label id="remove-delegasi" class="disabled" onclick="event.preventDefault()">Remove Input</label>
                </div>
            </div>

            <div class="form-group">
                <h2>Need Interpreter?</h2>
                <div class="interpreter-form">
                    <input type="radio" id="interpreter_yes" name="interpreter" value="Yes" class="radio-custom-style"
                        <?php echo ($interpreter_value == 'Yes') ? 'checked' : ''; ?> required>
                    <label for="interpreter_yes">Yes</label>
                </div>
                <div class="interpreter-form">
                    <input type="radio" id="interpreter_no" name="interpreter" value="No" class="radio-custom-style"
                        <?php echo ($interpreter_value == 'No') ? 'checked' : ''; ?> required>
                    <label for="interpreter_no">No</label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit">Submit</button>
            </div>

        </form>

        <!-- Popup Success -->
        <div id="successPopup" class="popup hidden">
            <div class="popup-content">
                <span class="popup-close" onclick="closePopup()">&times;</span>
                <div class="popup-icon">
                    <img src="style/image/checklist.png" width="100px" height="100px">
                </div>
                <h2>Success!</h2>
                <p>"Data has been successfully entered.<br />Please wait for further confirmation!"</p>
                <button onclick="closePopup()">Okay</button>
            </div>
        </div>

        <!-- Popup Error -->
        <div id="errorPopup" class="popup hidden">
            <div class="popup-content">
                <span class="popup-close" onclick="closePopup()">&times;</span>
                <div class="popup-icon">
                    <img src="style/image/error.png" width="100px" height="100px">
                </div>
                <h2 style="color: red;">Error!</h2>
                <p id="errorMessage">Error message will be shown here.</p>
                <button onclick="closePopup()">Okay</button>
            </div>
        </div>

        <script src="js/vrf.js"></script>
</body>

</html>