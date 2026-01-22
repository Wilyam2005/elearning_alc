<?php 
session_start();
if($_SESSION['role'] != 'guru'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';
$id_guru = $_SESSION['id_user'];

if(isset($_POST['buka_presensi'])){
    $mapel_id = $_POST['mapel_id']; $kelas_id = $_POST['kelas_id']; 
    $tanggal = $_POST['tanggal']; $mulai = $_POST['jam_mulai']; $selesai = $_POST['jam_selesai'];
    $query = "INSERT INTO presensi (mapel_id, kelas_id, tanggal, jam_mulai, jam_selesai, status_aktif) VALUES ('$mapel_id', '$kelas_id', '$tanggal', '$mulai', '$selesai', 'Y')";
    mysqli_query($koneksi, $query); echo "<script>window.location='presensi.php';</script>";
}
if(isset($_GET['tutup'])){
    $id = $_GET['tutup']; mysqli_query($koneksi, "UPDATE presensi SET status_aktif='N' WHERE id='$id'"); echo "<script>window.location='presensi.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Presensi Siswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --sidebar-bg: #1e1b4b; --bg-body: #f3f4f6; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-body); margin: 0; display: flex; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); color: white; position: fixed; top: 0; left: 0; z-index: 100; }
        .sidebar-brand { padding: 30px; font-size: 1.4rem; font-weight: 800; background: rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px; }
        .sidebar-menu { list-style: none; padding: 20px 15px; margin: 0; }
        .sidebar-menu a { display: flex; align-items: center; padding: 14px 20px; color: #a5b4fc; text-decoration: none; border-radius: 12px; transition: 0.3s; font-weight: 500; margin-bottom: 5px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: var(--primary); color: white; transform: translateX(5px); box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4); }
        .sidebar-menu i { width: 25px; font-size: 1.1rem; }

        .main-content { margin-left: 260px; padding: 40px; width: 100%; min-height: 100vh; }
        
        .content-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .card { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #4b5563; }
        .form-input { width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; box-sizing: border-box; }
        .form-input:focus { border-color: var(--primary); outline: none; }
        .btn-primary { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        
        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        th { text-align: left; padding: 0 15px; color: #6b7280; font-size: 0.85rem; text-transform: uppercase; }
        td { background: white; padding: 20px 15px; }
        tr td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        tr td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
        
        .badge-open { background: #d1fae5; color: #065f46; padding: 5px 10px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; }
        .badge-closed { background: #fee2e2; color: #991b1b; padding: 5px 10px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-rocket"></i> ALC TEACHER</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="materi.php"><i class="fas fa-play-circle"></i> Video Materi</a></li>
            <li><a href="tugas.php"><i class="fas fa-clipboard-check"></i> Tugas & G-Form</a></li>
            <li><a href="soal.php"><i class="fas fa-file-alt"></i> Bank Soal</a></li>
            <li><a href="presensi.php" class="active"><i class="fas fa-clock"></i> Presensi</a></li>
            <li style="margin-top: 30px;"><a href="../logout.php" style="color:#f87171;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #1f2937; margin-bottom: 30px;">Kelola Presensi</h1>
        
        <div class="content-grid">
            <div class="card">
                <h3 style="margin-top:0;">Buka Sesi Baru</h3>
                <form method="POST">
                    <div style="margin-bottom:15px;">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="mapel_id" class="form-input" required>
                            <?php 
                            $mapel = mysqli_query($koneksi, "SELECT * FROM mapel WHERE guru_id='$id_guru'");
                            while($m = mysqli_fetch_array($mapel)){ echo "<option value='".$m['id']."'>".$m['nama_mapel']."</option>"; }
                            ?>
                        </select>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-input" required>
                            <?php 
                            $kelas = mysqli_query($koneksi, "SELECT * FROM kelas");
                            while($k = mysqli_fetch_array($kelas)){ echo "<option value='".$k['id']."'>".$k['nama_kelas']."</option>"; }
                            ?>
                        </select>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div style="display:flex; gap:10px; margin-bottom:15px;">
                        <div style="flex:1;">
                            <label class="form-label">Mulai</label>
                            <input type="time" name="jam_mulai" class="form-input" required>
                        </div>
                        <div style="flex:1;">
                            <label class="form-label">Selesai</label>
                            <input type="time" name="jam_selesai" class="form-input" required>
                        </div>
                    </div>
                    <button type="submit" name="buka_presensi" class="btn-primary">Buka Absen</button>
                </form>
            </div>

            <div>
                <h3 style="margin-top:0; margin-bottom:15px; color:#4b5563;">Riwayat Sesi</h3>
                <table>
                    <thead><tr><th>Tgl</th><th>Mapel/Kelas</th><th>Jam</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php 
                        $query = "SELECT presensi.*, mapel.nama_mapel, kelas.nama_kelas FROM presensi JOIN mapel ON presensi.mapel_id = mapel.id JOIN kelas ON presensi.kelas_id = kelas.id WHERE mapel.guru_id = '$id_guru' ORDER BY presensi.id DESC";
                        $data = mysqli_query($koneksi, $query);
                        while($d = mysqli_fetch_array($data)){
                        ?>
                        <tr>
                            <td><?php echo date('d/m', strtotime($d['tanggal'])); ?></td>
                            <td><b><?php echo $d['nama_mapel']; ?></b><br><small><?php echo $d['nama_kelas']; ?></small></td>
                            <td><?php echo $d['jam_mulai'].'-'.$d['jam_selesai']; ?></td>
                            <td><?php echo ($d['status_aktif']=='Y') ? '<span class="badge-open">OPEN</span>' : '<span class="badge-closed">CLOSED</span>'; ?></td>
                            <td>
                                <?php if($d['status_aktif']=='Y'){ ?>
                                    <a href="presensi.php?tutup=<?php echo $d['id']; ?>" style="color:#ef4444; font-weight:700; text-decoration:none;">Tutup</a>
                                <?php } else { echo "-"; } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>