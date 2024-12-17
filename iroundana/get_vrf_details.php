<?php
require 'connect.php';

if (isset($_GET['id'])) {
    $id_vrf = $_GET['id'];

    $query = "
        SELECT 
            vrf.id_vrf,
            vrf.tgl_kunjungan,
            DATE_FORMAT(vrf.waktu_kunjungan, '%H:%i') AS waktu_kunjungan,
            vrf.durasi_kunjungan,
            vrf.nama_pemohon,
            vrf.posisi_pemohon,
            vrf.institusi_pemohon,
            vrf.website_pemohon,
            vrf.email_pemohon,
            vrf.telepon_pemohon,
            vrf.faks_pemohon,
            vrf.deskripsi_institusi,
            vrf.tujuan_kunjungan,
            GROUP_CONCAT(DISTINCT orang.nama_orang SEPARATOR ', ') AS orang_ditemui,
            vrf.bidang_pembahasan,
            COALESCE(
                GROUP_CONCAT(
                    DISTINCT NULLIF(
                        CONCAT(
                            IFNULL(kontak_undana.nama_depan_kontak, ''),
                            ' ',
                            IFNULL(kontak_undana.nama_belakang_kontak, ''),
                            ' ',
                            IFNULL(kontak_undana.gelar_kontak, ''),
                            ' ',
                            IFNULL(kontak_undana.posisi_kontak, '')
                        ),
                        '    '
                    )
                    SEPARATOR '<br>'
                ),
                'No Undana Contact'
            ) AS kontak_undana,
            GROUP_CONCAT(DISTINCT CONCAT(delegasi.gelar, ' ', delegasi.nama_depan, ' ', delegasi.nama_belakang, ' - ', delegasi.posisi) SEPARATOR '<br>') AS delegasi,
            vrf.interpreter,
            vrf.status
        FROM 
            vrf
        LEFT JOIN 
            orang ON vrf.id_vrf = orang.id_vrf
        LEFT JOIN 
            kontak_undana ON vrf.id_vrf = kontak_undana.id_vrf
        LEFT JOIN 
            delegasi ON vrf.id_vrf = delegasi.id_vrf
        WHERE 
            vrf.id_vrf = ?
        GROUP BY 
            vrf.id_vrf;
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_vrf);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'VRF details not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid VRF ID']);
}
?>