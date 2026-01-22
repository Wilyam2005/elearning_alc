<?php 
session_start();
if($_SESSION['role'] != 'admin'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM tugas WHERE id='$id'");
    echo "<script>alert('Tugas dihapus oleh Admin'); window.location='mon_tugas.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Monitoring Tugas - ALC Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #3b82f6; --dark-navy: #0f172a; --slate: #64748b; --light-bg: #f1f5f9; --white: #ffffff; }
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; background-color: var(--light-bg); display: flex; }
        .sidebar { width: 260px; height: 100vh; background-color: var(--dark-navy); color: var(--white); position: fixed; top: 0; left: 0; display: flex; flex-direction: column; overflow-y: auto; }
        .sidebar-brand { padding: 25px; font-size: 1.2rem; font-weight: 700; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header { font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; padding: 20px 25px 10px; font-weight: 700; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 12px 25px; color: #cbd5e1; text-decoration: none; transition: 0.3s; font-weight: 500; font-size: 0.95rem; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: rgba(255,255,255,0.05); color: var(--white); border-left: 4px solid var(--primary); }
        .sidebar-menu li a i { width: 25px; margin-right: 10px; }
        .main-content { margin-left: 260px; padding: 30px; width: 100%; }
        .card { background: var(--white); padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th { text-align: left; padding: 12px; background: #f8fafc; color: var(--slate); font-weight: 600; font-size: 0.9rem; }
        table td { padding: 12px; border-bottom: 1px solid #e2e8f0; color: #334155; }
        .btn-delete { color: #ef4444; text-decoration: none; padding: 5px 10px; background: #fee2e2; border-radius: 4px; font-size: 0.8rem; }
        .badge { padding: 4px 8px; border-radius: 4px; background: #fce7f3; color: #be185d; font-size: 0.75rem; font-weight: 600; }
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
            <li><a href="mon_materi.php"><i class="fas fa-video"></i> Semua Materi</a></li>
            <li><a href="mon_tugas.php" class="active"><i class="fas fa-tasks"></i> Semua Tugas</a></li>
            <li><a href="mon_presensi.php"><i class="fas fa-clock"></i> Rekap Presensi</a></li>
        </ul>
        <div class="sidebar-header">Akun</div>
        <ul class="sidebar-menu">
            <li><a href="../logout.php" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2 style="margin-top:0; color:#0f172a;">Monitoring Tugas & Pengumpulan</h2>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Deadline</th>
                        <th>Mapel</th>
                        <th>Kelas</th>
                        <th>Judul Tugas</th>
                        <th>Jml Pengumpul</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT tugas.*, mapel.nama_mapel, kelas.nama_kelas 
                              FROM tugas 
                              JOIN mapel ON tugas.mapel_id = mapel.id 
                              JOIN kelas ON tugas.kelas_id = kelas.id 
                              ORDER BY tugas.id DESC";
                    $data = mysqli_query($koneksi, $query);
                    while($d = mysqli_fetch_array($data)){
                        // Hitung berapa siswa yg sudah kumpul
                        $id_tugas = $d['id'];
                        $cek_jml = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pengumpulan WHERE tugas_id='$id_tugas'"));
                    ?>
                    <tr>
                        <td><?php echo date('d M Y, H:i', strtotime($d['deadline'])); ?></td>
                        <td><span class="badge"><?php echo $d['nama_mapel']; ?></span></td>
                        <td><b><?php echo $d['nama_kelas']; ?></b></td>
                        <td><?php echo $d['judul']; ?></td>
                        <td><?php echo $cek_jml; ?> Siswa</td>
                        <td>
                            <a href="mon_tugas.php?hapus=<?php echo $d['id']; ?>" class="btn-delete" onclick="return confirm('Hapus tugas ini? Semua pengumpulan siswa juga akan terhapus.')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>