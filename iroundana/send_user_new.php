<?php
include('connect.php');
header('Content-Type: application/json');
if (!isset($_SESSION['id_user'])) {
    echo json_encode([
        "status" => "error",
        "message" => "User not logged in."
    ]);
    exit;
}

$id_user = $_SESSION['id_user'];

// Verifikasi apakah id_user valid di tabel user
$query_check_user = "SELECT id_user FROM user WHERE id_user = '$id_user'";
$result_check_user = mysqli_query($conn, $query_check_user);

if (mysqli_num_rows($result_check_user) == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid user ID."
    ]);
    exit;
}

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $visit_date = $_POST['visit_date'];
    $visit_time = $_POST['visit_time'];
    $visit_duration = $_POST['visit_duration'];
    $request_person_title = $_POST['request_person_title'];
    $request_person_first_name = $_POST['request_person_first_name'];
    $request_person_last_name = $_POST['request_person_last_name'];
    $request_person_position = $_POST['request_person_position'];
    $request_person_institution = $_POST['request_person_institution'];
    $request_person_website = $_POST['request_person_website'];
    $request_person_email = $_POST['request_person_email'];
    $request_person_phone = $_POST['request_person_phone'];
    $request_person_fax = $_POST['request_person_fax'];
    $institution_overview = $_POST['institution_overview'];
    $visit_purpose = $_POST['visit_purpose'];
    $discussion_topics = $_POST['discussion_topics'];
    $interpreter = $_POST['interpreter'];
    $meet_person = $_POST['meet_person'];
    $status = 'pending'; // Status VRF awal adalah pending

    // Memulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // 1. Cek jadwal bentrok sebelum menyimpan data VRF
        $sql_check_jadwal = "SELECT * FROM vrf WHERE tgl_kunjungan = '$visit_date' AND waktu_kunjungan = '$visit_time' AND status = 'approved'";
        $result_check_jadwal = mysqli_query($conn, $sql_check_jadwal);

        if (mysqli_num_rows($result_check_jadwal) > 0) {
            echo json_encode([
                "status" => "error",
                "message" => "The schedule is already taken at the selected time."
            ]);
            exit;
        }

        // Ambil id_vrf dari URL jika ada
        if (isset($_GET['id_vrf'])) {
            $parent_vrf_id = $_GET['id_vrf']; // Gunakan id_vrf dari URL
        } else {
            // Jika id_vrf tidak ada di URL, set parent_vrf_id ke NULL
            $parent_vrf_id = NULL;
        }

        // 3. Simpan data VRF
        $sql_vrf = "INSERT INTO vrf (tgl_kunjungan, waktu_kunjungan, durasi_kunjungan, gelar_pemohon, nama_pemohon, posisi_pemohon, institusi_pemohon, website_pemohon, email_pemohon, telepon_pemohon, faks_pemohon, deskripsi_institusi, tujuan_kunjungan, bidang_pembahasan, interpreter, status, id_user, parent_vrf_id)
        VALUES ('$visit_date', '$visit_time', '$visit_duration', '$request_person_title', CONCAT('$request_person_first_name', ' ', '$request_person_last_name'), '$request_person_position', '$request_person_institution', '$request_person_website', '$request_person_email', '$request_person_phone', '$request_person_fax', '$institution_overview', '$visit_purpose', '$discussion_topics', '$interpreter', '$status', '$id_user', '$parent_vrf_id')";

        if (!mysqli_query($conn, $sql_vrf)) {
            echo json_encode([
                "status" => "error",
                "message" => "Error Saving VRF."
            ]);
            exit;
        }

        $id_vrf = mysqli_insert_id($conn);

        // 4. Simpan orang_ditemui jika ada
        if (!empty($_POST['meet_person'])) {
            foreach ($_POST['meet_person'] as $person) {
                $sql_orang = "INSERT INTO orang (nama_orang, id_vrf) VALUES ('$person', '$id_vrf')";

                if (!mysqli_query($conn, $sql_orang)) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Error Saving Persons to Meet."
                    ]);
                    exit;
                }
            }
        }

        // 5. Simpan kontak_undana jika ada
        if (!empty($_POST['contact_person_title'])) {
            $contact_titles = $_POST['contact_person_title'];
            $contact_first_names = $_POST['contact_person_first_name'];
            $contact_last_names = $_POST['contact_person_last_name'];
            $contact_positions = $_POST['contact_person_position'];

            for ($i = 0; $i < count($contact_titles); $i++) {
                $gelar = $contact_titles[$i];
                $nama_depan = $contact_first_names[$i];
                $nama_belakang = $contact_last_names[$i];
                $posisi = $contact_positions[$i];

                $sql_kontak = "INSERT INTO kontak_undana (gelar_kontak, nama_depan_kontak, nama_belakang_kontak, posisi_kontak, id_vrf)
                VALUES ('$gelar', '$nama_depan', '$nama_belakang', '$posisi', '$id_vrf')";

                if (!mysqli_query($conn, $sql_kontak)) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Error Saving Undana Contact."
                    ]);
                    exit;
                }
            }
        }

        // 6. Simpan delegasi jika ada
        if (!empty($_POST['delegation_title'])) {
            $delegation_titles = $_POST['delegation_title'];
            $delegation_first_names = $_POST['delegation_first_name'];
            $delegation_last_names = $_POST['delegation_last_name'];
            $delegation_positions = $_POST['delegation_position'];

            for ($i = 0; $i < count($delegation_titles); $i++) {
                $gelar_delegasi = $delegation_titles[$i];
                $nama_depan_delegasi = $delegation_first_names[$i];
                $nama_belakang_delegasi = $delegation_last_names[$i];
                $posisi_delegasi = $delegation_positions[$i];

                $sql_delegasi = "INSERT INTO delegasi (gelar, nama_depan, nama_belakang, posisi, id_vrf)
                VALUES ('$gelar_delegasi', '$nama_depan_delegasi', '$nama_belakang_delegasi', '$posisi_delegasi', '$id_vrf')";

                if (!mysqli_query($conn, $sql_delegasi)) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Error Saving Delegation."
                    ]);
                    exit;
                }
            }
        }

        // 7. Validasi Interpreter
        if (empty($_POST['interpreter'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Please select an interpreter option (Yes or No)."
            ]);
            exit;
        } else {
            $interpreter = $_POST['interpreter']; // Ambil nilai interpreter jika ada
        }

        // 8. Commit transaksi jika tidak ada error
        mysqli_commit($conn);

        // Respons sukses
        echo json_encode([
            "id_vrf" => $id_vrf,
            "status" => "success",
            "message" => "Data has been successfully saved."
        ]);

    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($conn);

        // Respons error
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }

    // Tutup koneksi
    mysqli_close($conn);
}
?>