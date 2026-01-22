<?php 
session_start();
if($_SESSION['role'] != 'guru'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';
// $id_guru = $_SESSION['id_user']; // Tidak dipakai lagi untuk filter mapel

if(isset($_POST['simpan'])){
    $judul = $_POST['judul']; $mapel = $_POST['mapel_id']; $kelas = $_POST['kelas_id']; $link = $_POST['link']; $desk = $_POST['deskripsi'];
    mysqli_query($koneksi, "INSERT INTO materi (mapel_id, kelas_id, judul, deskripsi, link_video_drive) VALUES ('$mapel', '$kelas', '$judul', '$desk', '$link')");
    echo "<script>window.location='materi.php';</script>";
}
if(isset($_GET['hapus'])){
    mysqli_query($koneksi, "DELETE FROM materi WHERE id='$_GET[hapus]'");
    echo "<script>window.location='materi.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materi Guru</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Menggunakan style Indigo yang sama */
        :root { --primary: #4f46e5; --sidebar-bg: #1e1b4b; --bg-body: #f3f4f6; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-body); margin: 0; display: flex; }
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); color: white; position: fixed; transition:0.3s; z-index:100; }
        .sidebar-menu { padding: 20px 0; list-style:none; }
        .sidebar-menu a { display:block; padding:15px 20px; color:#a5b4fc; text-decoration:none; font-weight:500; display:flex; align-items:center; gap:10px;}
        .sidebar-menu a.active { background:var(--primary); color:white; }
        .main-content { margin-left: 260px; padding: 30px; width: 100%; transition:0.3s; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; padding: 20px; } }
        .card { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; } 
        th, td { padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: left; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div style="padding:25px; font-weight:800;">ALC TEACHER</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="materi.php" class="active"><i class="fas fa-video"></i> Materi</a></li>
            <li><a href="tugas.php"><i class="fas fa-tasks"></i> Tugas</a></li>
            <li><a href="soal.php"><i class="fas fa-edit"></i> Bank Soal</a></li>
            <li><a href="presensi.php"><i class="fas fa-clock"></i> Presensi</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Upload Materi</h2>
        
        <div class="card">
            <form method="POST">
                <label>Judul Materi</label>
                <input type="text" name="judul" class="form-control" required>
                
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px;">
                    <div>
                        <label>Mapel (Semua Paket)</label>
                        <select name="mapel_id" class="form-control" required>
                            <?php 
                            // LOGIC BARU: Tampilkan SEMUA MAPEL (Tanpa Filter Guru)
                            $mapel = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY tingkatan ASC, nama_mapel ASC");
                            while($m = mysqli_fetch_array($mapel)){ 
                                echo "<option value='".$m['id']."'>".$m['nama_mapel']." [".$m['tingkatan']."]</option>"; 
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label>Target Kelas & Paket</label>
                        <select name="kelas_id" class="form-control" required>
                            <?php 
                            $kelas = mysqli_query($koneksi, "SELECT * FROM kelas ORDER BY tingkatan ASC");
                            while($k = mysqli_fetch_array($kelas)){ 
                                echo "<option value='".$k['id']."'>[".$k['tingkatan']."] ".$k['nama_kelas']."</option>"; 
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <label>Link Video</label>
                <input type="text" name="link" class="form-control" required>
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control"></textarea>
                <button type="submit" name="simpan" class="btn">Upload Sekarang</button>
            </form>
        </div>

        <div class="card table-responsive">
            <h3>Riwayat Materi</h3>
            <table>
                <thead><tr><th>Paket/Mapel</th><th>Judul</th><th>Link</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php 
                    // Tampilkan SEMUA Materi (Tanpa filter guru)
                    $data = mysqli_query($koneksi, "SELECT materi.*, kelas.nama_kelas, kelas.tingkatan, mapel.nama_mapel 
                                                    FROM materi 
                                                    JOIN kelas ON materi.kelas_id = kelas.id 
                                                    JOIN mapel ON materi.mapel_id = mapel.id 
                                                    ORDER BY materi.id DESC");
                    while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                        <td>
                            <small style="color:#4f46e5; font-weight:700;"><?php echo $d['tingkatan']; ?></small><br>
                            <?php echo $d['nama_mapel']; ?>
                        </td>
                        <td>
                            <b><?php echo $d['judul']; ?></b><br>
                            <small style="color:#6b7280;">Kelas: <?php echo $d['nama_kelas']; ?></small>
                        </td>
                        <td><a href="<?php echo $d['link_video_drive']; ?>" target="_blank">Buka</a></td>
                        <td><a href="materi.php?hapus=<?php echo $d['id']; ?>" style="color:red;">Hapus</a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>