<?php
include 'connect.php'; // Koneksi ke database
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer autoload

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_vrf = intval($_POST['id_vrf']); // Validasi input ID
    $reason = htmlspecialchars($_POST['reason']); // Escape karakter berbahaya
    $reschedule_date = $_POST['reschedule_date']; // Tanggal reschedule
    $reschedule_time = $_POST['reschedule_time']; // Waktu reschedule

    // Ambil detail VRF dan email user dari database
    $query = "SELECT email_pemohon, nama_pemohon, institusi_pemohon FROM vrf WHERE id_vrf = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_vrf);
    $stmt->execute();
    $result = $stmt->get_result();
    $vrf = $result->fetch_assoc();

    if ($vrf) {
        $email = $vrf['email_pemohon'];
        $applicantName = $vrf['nama_pemohon'];
        $institution = $vrf['institusi_pemohon'];

        // Generate PDF menggunakan TCPDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('IRO UNDANA');
        $pdf->SetTitle('Reschedule Notification');
        $pdf->SetMargins(30, 30, 30);
        $pdf->AddPage();

        // Logo di kiri atas
        $pdf->Image(__DIR__ . '/style/image/Logo_Undana.png', 30, 20, 30, 30); // Gambar logo

        // Set posisi dan font untuk teks header agar di sebelah kanan logo
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(61, 20); // Posisi horizontal untuk teks setelah logo
        $pdf->Cell(0, 10, 'KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI', 0, 1, 'L');

        // Nama Universitas di bawah teks header
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(90, 25); // Posisi untuk nama universitas
        $pdf->Cell(0, 10, 'UNIVERSITAS NUSA CENDANA', 0, 1, 'L');

        // Informasi kontak
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetXY(70, 30); // Posisi untuk informasi kontak
        $pdf->Cell(0, 10, 'Alamat: Jln. Adisucipto Penfui, PO BOX 104, Kupang 85001, NTT', 0, 1, 'L');
        $pdf->SetXY(78, 35);
        $pdf->Cell(0, 10, 'Telepon: (0380) 881580, 8031580; Fax: (0380) 881674', 0, 1, 'L');
        $pdf->SetXY(72, 40);
        $pdf->Cell(0, 10, 'Website: https://www.undana.ac.id; E-mail: info@undana.ac.id', 0, 1, 'L');

        // Garis pembatas
        $pdf->Line(30, $pdf->GetY() + 5, 190, $pdf->GetY() + 5); // Garis tipis atas
        $pdf->SetLineWidth(0.8); // Menetapkan ketebalan garis menjadi 0.8mm
        $pdf->Line(30, $pdf->GetY() + 6, 190, $pdf->GetY() + 6); // Garis tebal bawah

        $pdf->Ln(10); // Jarak vertikal untuk elemen berikutnya

        // Konten PDF setelah Header
        // Judul
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Rescheduling of Visit', 0, 1, 'C'); // Teks Judul di tengah
        $pdf->Ln(5); // Spasi vertikal

        // Teks "To :"
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetX(29); // Set posisi kiri untuk label
        $pdf->Cell(20, 6, 'To :', 0, 0, 'L'); // Label "To :"

        // Nama pemohon sejajar dengan "To :"
        $pdf->SetX(40);
        $pdf->Cell(0, 6, $applicantName, 0, 1, 'L'); // Nama pemohon di baris yang sama
        $pdf->SetX(40); // Set posisi ke kiri untuk baris kedua
        $pdf->Cell(0, 6, $institution, 0, 1, 'L'); // Nama institusi sejajar ke bawah

        $pdf->Ln(10);

        // Teks body
        $pdf->SetFont('helvetica', '', 12);
        $pdf->MultiCell(
            0,
            6,
            "Dear $applicantName,\n\nBased on your visit request to the International Relations Office (IRO) Universitas Nusa Cendana, we regret to inform you that your visit needs to be rescheduled due to:\n\n" .
            "$reason\n\n" .
            "The new schedule for your visit has been set as follows:\n\n" .
            "Date: $reschedule_date\n" .
            "Time: $reschedule_time\n\n" .
            "Please adjust your agenda accordingly. If you have any issues regarding the new schedule, kindly contact us at your earliest convenience.\n\n" .
            "Thank you for your understanding and cooperation.\n\nSincerely,\n" .
            "On behalf of the Rector,\n" .
            "Head of International Relations Office,\n"
        );
        
        $pdf->Image('style/image/stamp.png', 20, $pdf->GetY(), 40); 
        $pdf->Image('style/image/signature.png', 20, $pdf->GetY(), 70); 

        $pdf->Ln(25); // Memberikan jarak setelah tanda tangan dan cap

        // Melanjutkan teks setelah tanda tangan dan cap
        $pdf->SetFont('helvetica', '', 12);
        $pdf->MultiCell(
            0,
            6,
            "Ir. Maria Lobo, M.Maths.Sc., Ph.D.\n"
        );

        // Simpan file PDF di server
        $pdfFileName = "reschedule_vrf_$id_vrf.pdf";
        $pdf->Output(__DIR__ . "/$pdfFileName", 'F');

        // Kirim email dengan PDF sebagai lampiran
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'beniliufeto08@gmail.com'; // Email pengirim
            $mail->Password = 'xydw svra lhqs zeag'; // Password email pengirim
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('beniliufeto08@gmail.com', 'International Relations Office UNDANA');
            $mail->addAddress($email, $applicantName);

            // Tautan untuk pengajuan baru
            $rescheduleFormLink = "http://localhost/iroundana/new_submission.php?id_vrf=$id_vrf";

            // Konten email
            $mail->isHTML(true);
            $mail->Subject = 'Reschedule Notification';
            $mail->Body = "<p>Dear $applicantName,</p>
                <p>Your VRF request has been rescheduled for the following reason:</p>
                <strong>$reason</strong>
                <p>The new schedule has been set as:</p>
                <ul>
                    <li><strong>Date:</strong> $reschedule_date</li>
                    <li><strong>Time:</strong> $reschedule_time</li>
                </ul>
                <p>Please find the detailed letter attached and submit a new schedule using the link below:</p>
                <p><a href='$rescheduleFormLink'>Submit New Schedule</a></p>
                <p>Thank you,</p>
                <p>VRF Approval Team</p>";

            // Lampirkan PDF
            $mail->addAttachment(__DIR__ . "/$pdfFileName");

            $mail->send();

            // Setelah email terkirim, update status ke 'Reschedule' di database
            $update_query = "UPDATE vrf SET status = 'Reschedule', alasan_reschedule = ?, reschedule_date = ?, reschedule_time = ? WHERE id_vrf = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param('sssi', $reason, $reschedule_date, $reschedule_time, $id_vrf);

            if ($update_stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Email sent, PDF attached, and Reschedule status updated successfully.',
                    'pdf_file' => $pdfFileName
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update Reschedule status in the database.'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Email sending failed: ' . $mail->ErrorInfo
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'VRF not found or invalid ID.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST is allowed.'
    ]);
}
?>