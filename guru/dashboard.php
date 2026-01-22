<?php 
session_start();
if($_SESSION['role'] != 'guru'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';
$id_guru = $_SESSION['id_user'];
// Hitung Data (Pastikan query tidak error jika tabel kosong)
$jml_materi = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM materi JOIN mapel ON materi.mapel_id=mapel.id WHERE mapel.guru_id='$id_guru'"));
$jml_tugas = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tugas JOIN mapel ON tugas.mapel_id=mapel.id WHERE mapel.guru_id='$id_guru'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Guru</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --sidebar-bg: #1e1b4b; --bg-body: #f3f4f6; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-body); margin: 0; display: flex; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); color: white; position: fixed; top: 0; left: 0; z-index: 100; }
        .sidebar-brand { padding: 30px; font-size: 1.4rem; font-weight: 800; background: rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px; }
        .sidebar-menu { list-style: none; padding: 20px 15px; margin: 0; }
        .sidebar-menu a { display: flex; align-items: center; padding: 14px 20px; color: #a5b4fc; text-decoration: none; border-radius: 12px; transition: 0.3s; font-weight: 500; margin-bottom: 5px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: var(--primary); color: white; transform: translateX(5px); }
        .sidebar-menu i { width: 25px; font-size: 1.1rem; }

        .main-content { margin-left: 260px; padding: 40px; width: 100%; min-height: 100vh; }
        
        .hero-card { background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%); border-radius: 24px; padding: 40px; color: white; margin-bottom: 40px; box-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.4); }
        .hero-title { font-size: 2.2rem; font-weight: 800; margin: 0 0 10px 0; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; }
        .stat-card { background: white; border-radius: 20px; padding: 25px; display: flex; align-items: center; gap: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: 0.3s; cursor: pointer; }
        .stat-card:hover { transform: translateY(-5px); border: 1px solid var(--primary); }
        .stat-icon { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; background: #e0e7ff; color: #4f46e5; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-rocket"></i> ALC TEACHER</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="materi.php"><i class="fas fa-play-circle"></i> Video Materi</a></li>
            <li><a href="tugas.php"><i class="fas fa-clipboard-check"></i> Tugas & Ujian</a></li>
            <li><a href="soal.php"><i class="fas fa-file-alt"></i> Bank Soal (Quiz)</a></li>
            <li><a href="presensi.php"><i class="fas fa-clock"></i> Presensi</a></li>
            <li style="margin-top: 30px;"><a href="../logout.php" style="color:#f87171;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="hero-card">
            <h1 class="hero-title">Halo, <?php echo $_SESSION['nama_lengkap']; ?>! 👋</h1>
            <p>Selamat datang di panel Guru. Semua menu kini lebih mudah diakses.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card" onclick="location.href='materi.php'">
                <div class="stat-icon"><i class="fas fa-video"></i></div>
                <div><h3 style="margin:0; font-size:2rem;"><?php echo $jml_materi; ?></h3><p style="margin:0; color:#6b7280;">Video</p></div>
            </div>
            <div class="stat-card" onclick="location.href='tugas.php'">
                <div class="stat-icon" style="background:#fce7f3; color:#db2777;"><i class="fas fa-tasks"></i></div>
                <div><h3 style="margin:0; font-size:2rem;"><?php echo $jml_tugas; ?></h3><p style="margin:0; color:#6b7280;">Tugas</p></div>
            </div>
             <div class="stat-card" onclick="location.href='soal.php'">
                <div class="stat-icon" style="background:#ffedd5; color:#ea580c;"><i class="fas fa-question-circle"></i></div>
                <div><h3 style="margin:0; font-size:2rem;">Soal</h3><p style="margin:0; color:#6b7280;">Bank Quiz</p></div>
            </div>
        </div>
    </div>
</body>
</html>