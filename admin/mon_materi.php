<?php 
session_start();
if($_SESSION['role'] != 'admin'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

// HAPUS MATERI
if(isset($_GET['hapus'])){
    mysqli_query($koneksi, "DELETE FROM materi WHERE id='$_GET[hapus]'");
    echo "<script>window.location='mon_materi.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Materi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* GUNAKAN CSS YANG SAMA DENGAN USERS.PHP DI ATAS AGAR KONSISTEN */
        :root { --primary: #3b82f6; --dark: #0f172a; --bg: #f1f5f9; --text: #334155; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; display: flex; color: var(--text); }
        .sidebar { width: 260px; height: 100vh; background: var(--dark); color: white; position: fixed; z-index: 100; overflow-y: auto; transition: 0.3s; }
        .sidebar-brand { padding: 25px; font-weight: 800; font-size: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header { padding: 25px 25px 10px; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu a { display: flex; align-items: center; padding: 12px 25px; color: #cbd5e1; text-decoration: none; transition: 0.3s; font-size: 0.95rem; font-weight: 500; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid var(--primary); }
        .sidebar-menu i { width: 25px; text-align: center; margin-right: 10px; }
        .main-content { margin-left: 260px; padding: 30px; width: 100%; transition: 0.3s; }
        
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; padding: 20px; } }
        
        .card { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: #64748b; font-size: 0.85rem; font-weight: 700; border-bottom: 1px solid #e2e8f0; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; }
        .btn-link { color: #3b82f6; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-shield-alt"></i> ADMIN PANEL</div>
        
        <div class="sidebar-header">Data Master</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Kelola User</a></li>
            <li><a href="kelas.php"><i class="fas fa-chalkboard"></i> Kelola Kelas</a></li>
            <li><a href="mapel.php"><i class="fas fa-book"></i> Kelola Mapel</a></li>
        </ul>

        <div class="sidebar-header">Monitoring Pembelajaran</div>
        <ul class="sidebar-menu">
            <li><a href="mon_materi.php" class="active"><i class="fas fa-video"></i> Semua Materi</a></li>
            <li><a href="mon_tugas.php"><i class="fas fa-tasks"></i> Semua Tugas</a></li>
            <li><a href="mon_presensi.php"><i class="fas fa-clock"></i> Rekap Presensi</a></li>
        </ul>

        <div class="sidebar-header">Akun</div>
        <ul class="sidebar-menu">
            <li><a href="../logout.php" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Monitoring: Semua Materi</h2>
        
        <div class="card table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Mapel & Paket</th>
                        <th>Kelas</th>
                        <th>Judul Materi</th>
                        <th>Link</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT materi.*, mapel.nama_mapel, kelas.nama_kelas, kelas.tingkatan 
                              FROM materi 
                              JOIN mapel ON materi.mapel_id = mapel.id 
                              JOIN kelas ON materi.kelas_id = kelas.id 
                              ORDER BY materi.id DESC";
                    $data = mysqli_query($koneksi, $query);
                    while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($d['tanggal_upload'])); ?></td>
                        <td>
                            <b><?php echo $d['nama_mapel']; ?></b><br>
                            <span style="font-size:0.8rem; color:#64748b;"><?php echo $d['tingkatan']; ?></span>
                        </td>
                        <td><?php echo $d['nama_kelas']; ?></td>
                        <td><?php echo $d['judul']; ?></td>
                        <td><a href="<?php echo $d['link_video_drive']; ?>" target="_blank" class="btn-link">Lihat Video</a></td>
                        <td>
                            <a href="mon_materi.php?hapus=<?php echo $d['id']; ?>" onclick="return confirm('Hapus materi ini?')" style="color:#ef4444;"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>