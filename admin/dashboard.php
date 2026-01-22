<?php 
session_start();
if($_SESSION['role'] != 'admin'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

// Hitung Data Cepat
$jml_siswa = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM users WHERE role='siswa'"));
$jml_guru = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM users WHERE role='guru'"));
$jml_materi = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM materi"));
$jml_tugas = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tugas"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - ALC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary: #3b82f6; --dark-navy: #0f172a; --slate: #64748b; --light-bg: #f1f5f9; --white: #ffffff; }
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; background-color: var(--light-bg); display: flex; }
        
        /* SIDEBAR STYLE */
        .sidebar { width: 260px; height: 100vh; background-color: var(--dark-navy); color: var(--white); position: fixed; top: 0; left: 0; display: flex; flex-direction: column; overflow-y: auto; }
        .sidebar-brand { padding: 25px; font-size: 1.2rem; font-weight: 700; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); letter-spacing: 1px; }
        
        .sidebar-header { font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; padding: 20px 25px 10px; font-weight: 700; letter-spacing: 0.5px; }
        
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 12px 25px; color: #cbd5e1; text-decoration: none; transition: 0.3s; font-weight: 500; font-size: 0.95rem; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: rgba(255,255,255,0.05); color: var(--white); border-left: 4px solid var(--primary); }
        .sidebar-menu li a i { width: 25px; margin-right: 10px; text-align: center; }

        /* CONTENT STYLE */
        .main-content { margin-left: 260px; padding: 30px; width: 100%; }
        .header-box { margin-bottom: 30px; }
        .header-title { font-size: 1.5rem; font-weight: 700; color: var(--dark-navy); margin: 0; }
        .header-subtitle { color: var(--slate); font-size: 0.9rem; margin-top: 5px; }

        /* CARDS */
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card-stat { background: var(--white); padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border-left: 5px solid var(--primary); }
        .card-stat h3 { font-size: 2rem; margin: 0; color: var(--dark-navy); }
        .card-stat p { margin: 5px 0 0; color: var(--slate); font-size: 0.9rem; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-shield-alt"></i> ADMIN PANEL</div>
        
        <div class="sidebar-header">Data Master</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Kelola User</a></li>
            <li><a href="kelas.php"><i class="fas fa-chalkboard"></i> Kelola Kelas</a></li>
            <li><a href="mapel.php"><i class="fas fa-book"></i> Kelola Mapel</a></li>
        </ul>

        <div class="sidebar-header">Monitoring Pembelajaran</div>
        <ul class="sidebar-menu">
            <li><a href="mon_materi.php"><i class="fas fa-video"></i> Semua Materi</a></li>
            <li><a href="mon_tugas.php"><i class="fas fa-tasks"></i> Semua Tugas</a></li>
            <li><a href="mon_presensi.php"><i class="fas fa-clock"></i> Rekap Presensi</a></li>
        </ul>

        <div class="sidebar-header">Akun</div>
        <ul class="sidebar-menu">
            <li><a href="../logout.php" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header-box">
            <h1 class="header-title">Dashboard Administrator</h1>
            <p class="header-subtitle">Pantau seluruh aktivitas pembelajaran di sini.</p>
        </div>

        <div class="card-grid">
            <div class="card-stat">
                <h3><?php echo $jml_siswa; ?></h3>
                <p>Siswa Aktif</p>
            </div>
            <div class="card-stat" style="border-left-color: #10b981;">
                <h3><?php echo $jml_guru; ?></h3>
                <p>Guru Pengajar</p>
            </div>
            <div class="card-stat" style="border-left-color: #f59e0b;">
                <h3><?php echo $jml_materi; ?></h3>
                <p>Total Materi Video</p>
            </div>
            <div class="card-stat" style="border-left-color: #ec4899;">
                <h3><?php echo $jml_tugas; ?></h3>
                <p>Tugas Diberikan</p>
            </div>
        </div>
    </div>

</body>
</html>