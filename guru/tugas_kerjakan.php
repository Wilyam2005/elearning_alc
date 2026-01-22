<?php 
session_start();
if($_SESSION['role'] != 'siswa'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

$id_siswa = $_SESSION['id_user'];
$id_tugas = $_GET['id'];

// AMBIL DATA TUGAS
$query_tugas = mysqli_query($koneksi, "SELECT * FROM tugas WHERE id='$id_tugas'");
$tugas = mysqli_fetch_assoc($query_tugas);

// LOGIKA KIRIM TUGAS (Moodle Style)
if(isset($_POST['kirim_tugas'])){
    $tipe = $tugas['tipe_tugas'];
    $file_upload = null;
    $jawaban_teks = null;
    $jawaban_link = null;
    $now = date('Y-m-d H:i:s');

    // 1. Jika Tipe FILE
    if($tipe == 'file'){
        $filename = $_FILES['file_siswa']['name'];
        $tmp = $_FILES['file_siswa']['tmp_name'];
        $file_upload = date('dmYHis')."_".$filename;
        move_uploaded_file($tmp, "../uploads/tugas/".$file_upload);
    }
    // 2. Jika Tipe TEKS
    elseif($tipe == 'teks'){
        $jawaban_teks = mysqli_real_escape_string($koneksi, $_POST['jawaban_teks']);
    }
    // 3. Jika Tipe LINK
    elseif($tipe == 'link'){
        $jawaban_link = mysqli_real_escape_string($koneksi, $_POST['jawaban_link']);
    }

    $catatan = $_POST['catatan_siswa'];

    // Cek apakah sudah pernah kirim? (Bisa update atau insert baru)
    $cek = mysqli_query($koneksi, "SELECT * FROM pengumpulan WHERE tugas_id='$id_tugas' AND siswa_id='$id_siswa'");
    if(mysqli_num_rows($cek) > 0){
        // Kalau mau sistem overwrite (timpa)
        $query_kirim = "UPDATE pengumpulan SET file_tugas='$file_upload', jawaban_teks='$jawaban_teks', jawaban_link='$jawaban_link', catatan_siswa='$catatan', waktu_upload='$now' WHERE tugas_id='$id_tugas' AND siswa_id='$id_siswa'";
    } else {
        $query_kirim = "INSERT INTO pengumpulan (tugas_id, siswa_id, file_tugas, jawaban_teks, jawaban_link, catatan_siswa, waktu_upload) 
                        VALUES ('$id_tugas', '$id_siswa', '$file_upload', '$jawaban_teks', '$jawaban_link', '$catatan', '$now')";
    }
    
    mysqli_query($koneksi, $query_kirim);
    echo "<script>alert('Tugas Berhasil Dikirim!'); window.location='tugas_saya.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kerjakan Tugas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; padding: 30px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 700px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .badge { padding: 5px 10px; border-radius: 4px; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; }
        .btn-submit { width: 100%; padding: 12px; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .btn-submit:hover { background: #1d4ed8; }
        .desc-box { background: #f8fafc; padding: 15px; border-left: 4px solid #3b82f6; margin: 20px 0; color: #475569; }
    </style>
</head>
<body>

    <div class="container">
        <a href="tugas_saya.php" style="text-decoration:none; color:#64748b; font-size:0.9rem;"><i class="fas fa-arrow-left"></i> Kembali</a>
        
        <div style="margin-top:20px; display:flex; align-items:center; justify-content:space-between;">
            <h2 style="margin:0; color:#0f172a;"><?php echo $tugas['judul']; ?></h2>
            <?php 
            if($tugas['tipe_tugas'] == 'file') echo '<span class="badge" style="background:#dbeafe; color:#1e40af;">UPLOAD FILE</span>';
            elseif($tugas['tipe_tugas'] == 'teks') echo '<span class="badge" style="background:#fce7f3; color:#be185d;">TEKS ONLINE</span>';
            else echo '<span class="badge" style="background:#d1fae5; color:#065f46;">KIRIM LINK</span>';
            ?>
        </div>

        <div class="desc-box">
            <b>Instruksi:</b><br>
            <?php echo nl2br($tugas['deskripsi']); ?>
            <br><br>
            <small style="color:#ef4444;"><i class="fas fa-clock"></i> Deadline: <?php echo date('d M Y, H:i', strtotime($tugas['deadline'])); ?></small>
        </div>

        <form method="POST" enctype="multipart/form-data">
            
            <?php if($tugas['tipe_tugas'] == 'file') { ?>
                <div class="form-group">
                    <label class="form-label">Upload File Jawaban (PDF/Doc/Img)</label>
                    <input type="file" name="file_siswa" class="form-control" required>
                </div>
            
            <?php } elseif($tugas['tipe_tugas'] == 'teks') { ?>
                <div class="form-group">
                    <label class="form-label">Tulis Jawaban Anda Disini</label>
                    <textarea name="jawaban_teks" class="form-control" rows="10" placeholder="Ketik jawaban..." required></textarea>
                </div>

            <?php } elseif($tugas['tipe_tugas'] == 'link') { ?>
                <div class="form-group">
                    <label class="form-label">Masukkan Link Tugas (G-Drive / Canva / Youtube)</label>
                    <input type="url" name="jawaban_link" class="form-control" placeholder="https://..." required>
                </div>
            <?php } ?>

            <div class="form-group">
                <label class="form-label">Catatan Tambahan (Opsional)</label>
                <textarea name="catatan_siswa" class="form-control" rows="2" placeholder="Pesan untuk guru..."></textarea>
            </div>

            <button type="submit" name="kirim_tugas" class="btn-submit">Kirim Jawaban</button>
        </form>
    </div>

</body>
</html>