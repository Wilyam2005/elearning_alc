<?php 
session_start();
if($_SESSION['role'] != 'guru'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

if(isset($_POST['simpan'])){
    $mapel = $_POST['mapel_id']; $kelas = $_POST['kelas_id']; 
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']); 
    $tipe = $_POST['tipe_tugas']; $deadline = $_POST['deadline'];
    
    mysqli_query($koneksi, "INSERT INTO tugas (mapel_id, kelas_id, judul, deskripsi, tipe_tugas, deadline) VALUES ('$mapel', '$kelas', '$judul', '$deskripsi', '$tipe', '$deadline')");
    echo "<script>alert('Tugas Diterbitkan!'); window.location='tugas.php';</script>";
}
if(isset($_GET['hapus'])){
    mysqli_query($koneksi, "DELETE FROM tugas WHERE id='$_GET[hapus]'");
    echo "<script>window.location='tugas.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Guru</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
            <li><a href="materi.php"><i class="fas fa-video"></i> Materi</a></li>
            <li><a href="tugas.php" class="active"><i class="fas fa-tasks"></i> Tugas</a></li>
            <li><a href="soal.php"><i class="fas fa-edit"></i> Bank Soal</a></li>
            <li><a href="presensi.php"><i class="fas fa-clock"></i> Presensi</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Kelola Tugas & Ujian</h2>
        
        <div class="card">
            <form method="POST">
                <label>Jenis Tugas</label>
                <select name="tipe_tugas" class="form-control">
                    <option value="file">Upload File</option>
                    <option value="esai">Esai Online</option>
                    <option value="link">Link Eksternal</option>
                </select>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px;">
                    <div>
                        <label>Mapel (Semua Paket)</label>
                        <select name="mapel_id" class="form-control" required>
                            <?php 
                            // LOGIC BARU: Tampilkan SEMUA MAPEL
                            $mapel = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY tingkatan ASC, nama_mapel ASC");
                            while($m = mysqli_fetch_array($mapel)){ 
                                echo "<option value='".$m['id']."'>".$m['nama_mapel']." [".$m['tingkatan']."]</option>"; 
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label>Kelas & Paket</label>
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

                <label>Judul Tugas</label>
                <input type="text" name="judul" class="form-control" required>
                <label>Instruksi / Soal</label>
                <textarea name="deskripsi" class="form-control"></textarea>
                <label>Deadline</label>
                <input type="datetime-local" name="deadline" class="form-control" required>
                
                <button type="submit" name="simpan" class="btn">Terbitkan Tugas</button>
            </form>
        </div>

        <div class="card table-responsive">
            <table>
                <thead><tr><th>Mapel</th><th>Judul & Kelas</th><th>Deadline</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php 
                    // Tampilkan SEMUA Tugas
                    $query = "SELECT tugas.*, mapel.nama_mapel, kelas.nama_kelas, kelas.tingkatan 
                              FROM tugas 
                              JOIN mapel ON tugas.mapel_id = mapel.id 
                              JOIN kelas ON tugas.kelas_id = kelas.id 
                              ORDER BY tugas.id DESC";
                    $data = mysqli_query($koneksi, $query);
                    while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                        <td>
                            <b><?php echo $d['nama_mapel']; ?></b><br>
                            <span style="font-size:0.8rem; color:#4f46e5;"><?php echo $d['tingkatan']; ?></span>
                        </td>
                        <td>
                            <?php echo $d['judul']; ?><br>
                            <small style="color:#6b7280;">Kelas: <?php echo $d['nama_kelas']; ?></small>
                        </td>
                        <td style="color:#ef4444; font-weight:bold;"><?php echo date('d M H:i', strtotime($d['deadline'])); ?></td>
                        <td>
                            <a href="tugas_nilai.php?id=<?php echo $d['id']; ?>" class="btn" style="padding:5px 10px; font-size:0.8rem;">Nilai</a>
                            <a href="tugas.php?hapus=<?php echo $d['id']; ?>" onclick="return confirm('Hapus?')" style="color:red; margin-left:10px;">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>