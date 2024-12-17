<?php
require 'vendor/autoload.php'; // Load PhpSpreadsheet
require 'connect.php'; // File koneksi database

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil data hanya dengan status 'Approved'
$query = "SELECT * FROM vrf WHERE status = 'Approved'";
$result = mysqli_query($conn, $query);

// Buat objek spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header Kolom
$headers = [
    'ID VRF',
    'Visit Date',
    'Visit Time',
    'Duration',
    'Applicant Name',
    'Applicant Position',
    'Institution',
    'Website',
    'Email',
    'Phone',
    'Fax',
    'Institution Description',
    'Visit Purpose',
    'Discussion Field',
    'Interpreter',
    'Status',
    'Persons to Meet',
    'Delegation',
    'Undana Contact'
];
$sheet->fromArray($headers, NULL, 'A1');

// Isi Data
$rowIndex = 2;
while ($row = mysqli_fetch_assoc($result)) {
    // Data utama VRF
    $vrfData = [
        $row['id_vrf'],
        $row['tgl_kunjungan'],
        $row['waktu_kunjungan'],
        $row['durasi_kunjungan'],
        $row['nama_pemohon'],
        $row['posisi_pemohon'],
        $row['institusi_pemohon'],
        $row['website_pemohon'],
        $row['email_pemohon'],
        $row['telepon_pemohon'],
        $row['faks_pemohon'],
        $row['deskripsi_institusi'],
        $row['tujuan_kunjungan'],
        $row['bidang_pembahasan'],
        $row['interpreter'],
        $row['status']
    ];

    // Data dari tabel `orang`
    $orangQuery = mysqli_query($conn, "SELECT nama_orang FROM orang WHERE id_vrf = '{$row['id_vrf']}'");
    $orangNames = [];
    while ($orang = mysqli_fetch_assoc($orangQuery)) {
        $orangNames[] = $orang['nama_orang'];
    }

    // Data dari tabel `delegasi`
    $delegasiQuery = mysqli_query($conn, "SELECT CONCAT(gelar, ' ', nama_depan, ' ', nama_belakang, ' (', posisi, ')') AS delegasi FROM delegasi WHERE id_vrf = '{$row['id_vrf']}'");
    $delegasiList = [];
    while ($delegasi = mysqli_fetch_assoc($delegasiQuery)) {
        $delegasiList[] = $delegasi['delegasi'];
    }

    // Data dari tabel `kontak_undana`
    $kontakQuery = mysqli_query($conn, "SELECT CONCAT(gelar_kontak, ' ', nama_depan_kontak, ' ', nama_belakang_kontak, ' (', posisi_kontak, ')') AS kontak FROM kontak_undana WHERE id_vrf = '{$row['id_vrf']}'");
    $kontakList = [];
    while ($kontak = mysqli_fetch_assoc($kontakQuery)) {
        $kontakList[] = $kontak['kontak'];
    }

    // Gabungkan data menjadi satu baris
    $sheet->fromArray(
        array_merge(
            $vrfData,
            [implode("\n", $orangNames), implode("\n", $delegasiList), implode("\n", $kontakList)]
        ),
        NULL,
        'A' . $rowIndex
    );
    $rowIndex++;
}

// Set header untuk download file
$filename = 'Approved_VRF_Report.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>