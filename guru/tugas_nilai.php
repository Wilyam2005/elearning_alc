<?php 
session_start();
if($_SESSION['role'] != 'guru'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

$tugas_id = $_GET['id'];

// LOGIKA INPUT NILAI
if(isset($_POST['simpan_nilai'])){
    $pengumpulan_id = $_POST['pengumpulan_id'];
    $nilai = $_POST['nilai'];
    $komentar = $_POST['komentar'];
    
    mysqli_query($koneksi, "UPDATE pengumpulan SET nilai='$nilai', komentar_guru='$komentar' WHERE id='$pengumpulan_id'");
    echo "<script>alert('Nilai Tersimpan'); window.location='tugas_nilai.php?id=$tugas_id';</script>";
}

// Ambil info tugas
$info_tugas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT judul FROM tugas WHERE id='$tugas_id'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Penilaian Tugas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; padding: 30px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-back { text-decoration: none; color: #64748b; display: flex; align-items: center; gap: 5px; }
        
        table { width: 100%; border-collapse: collapse; }
        table th { text-align: left; padding: 12px; background: #f8fafc; color: #64748b; font-weight: 600; }
        table td { padding: 12px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        
        .form-nilai { display: flex; gap: 10px; align-items: center; }
        .input-nilai { width: 60px; padding: 5px; border: 1px solid #cbd5e1; border-radius: 4px; text-align: center; }
        .btn-save { background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <a href="tugas.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
                <h2 style="margin: 10px 0 0; color:#0f172a;">Penilaian: <?php echo $info_tugas['judul']; ?></h2>
            </div>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Waktu Kirim</th>
                        <th>File Tugas</th>
                        <th>Catatan Siswa</th>
                        <th width="300">Input Nilai & Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT pengumpulan.*, users.nama_lengkap 
                              FROM pengumpulan 
                              JOIN users ON pengumpulan.siswa_id = users.id 
                              WHERE pengumpulan.tugas_id='$tugas_id'
                              ORDER BY pengumpulan.waktu_upload DESC";
                    $data = mysqli_query($koneksi, $query);
                    
                    if(mysqli_num_rows($data) == 0){
                        echo "<tr><td colspan='5' style='text-align:center; padding:30px;'>Belum ada siswa yang mengumpulkan.</td></tr>";
                    }

                    while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                        <td><b><?php echo $d['nama_lengkap']; ?></b></td>
                        <td><?php echo date('d/m H:i', strtotime($d['waktu_upload'])); ?></td>
                        <td>
                            <a href="../uploads/tugas/<?php echo $d['file_tugas']; ?>" target="_blank" style="color:#3b82f6; text-decoration:none;">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                        <td><?php echo $d['catatan_siswa']; ?></td>
                        <td>
                            <form method="POST" class="form-nilai">
                                <input type="hidden" name="pengumpulan_id" value="<?php echo $d['id']; ?>">
                                <input type="number" name="nilai" class="input-nilai" placeholder="0-100" value="<?php echo $d['nilai']; ?>" required>
                                <input type="text" name="komentar" placeholder="Komentar..." value="<?php echo $d['komentar_guru']; ?>" style="padding:5px; border:1px solid #cbd5e1; border-radius:4px;">
                                <button type="submit" name="simpan_nilai" class="btn-save"><i class="fas fa-save"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>