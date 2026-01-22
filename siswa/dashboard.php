<?php 
session_start();
if($_SESSION['role'] != 'siswa'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

$id_siswa = $_SESSION['id_user'];

// AMBIL DATA SISWA (Termasuk Info Kelas & Paket)
// Gunakan LEFT JOIN agar jika kelas_id NULL, data user tetap terambil
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT users.*, kelas.nama_kelas, kelas.tingkatan FROM users LEFT JOIN kelas ON users.kelas_id = kelas.id WHERE users.id='$id_siswa'"));

// Cek apakah punya kelas
$punya_kelas = !empty($user['kelas_id']);
$kelas_siswa = $user['kelas_id'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; --surface: #ffffff; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; padding-bottom: 80px; }
        
        /* SIDEBAR DESKTOP */
        .sidebar { display:none; }
        @media (min-width: 769px) {
            .sidebar { display:block; width: 260px; height: 100vh; background: white; position: fixed; border-right: 1px solid #e2e8f0; z-index: 100; }
            .sidebar-brand { padding: 25px; font-weight: 800; font-size: 1.2rem; color: var(--primary); display: flex; align-items: center; gap: 10px; }
            .sidebar-menu { list-style: none; padding: 10px 20px; margin: 0; }
            .sidebar-menu a { display: flex; align-items: center; padding: 12px 15px; color: #64748b; text-decoration: none; border-radius: 10px; font-weight: 600; margin-bottom: 5px; transition: 0.3s; }
            .sidebar-menu a:hover, .sidebar-menu a.active { background: #eef2ff; color: var(--primary); }
            .sidebar-menu i { width: 25px; font-size: 1.1rem; }
            .main { margin-left: 260px; padding: 30px; }
        }

        /* BOTTOM NAV MOBILE */
        .bottom-nav { position: fixed; bottom: 0; width: 100%; background: white; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-around; padding: 12px 0; z-index: 999; }
        .bottom-nav a { text-align: center; color: #94a3b8; text-decoration: none; font-size: 0.75rem; font-weight: 600; }
        .bottom-nav a i { display: block; font-size: 1.3rem; margin-bottom: 4px; }
        .bottom-nav a.active { color: var(--primary); }
        @media (min-width: 769px) { .bottom-nav { display: none; } }

        /* CONTENT STYLE */
        .main { padding: 20px; }
        .banner { background: linear-gradient(135deg, #4f46e5, #4338ca); color: white; padding: 30px; border-radius: 20px; margin-bottom: 25px; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2); }
        
        .card { background: white; padding: 20px; border-radius: 16px; margin-bottom: 15px; border: 1px solid #f1f5f9; box-shadow: 0 2px 5px rgba(0,0,0,0.03); transition: 0.3s; }
        .card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }

        .alert-box { background: #fffbeb; border: 1px solid #fcd34d; color: #b45309; padding: 20px; border-radius: 12px; display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-graduation-cap"></i> ALC SISWA</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="materi.php"><i class="fas fa-play-circle"></i> Materi Belajar</a></li>
            <li><a href="tugas.php"><i class="fas fa-clipboard-list"></i> Tugas Saya</a></li>
            <li><a href="profil.php"><i class="fas fa-user-circle"></i> Profil</a></li>
            <li style="margin-top: 30px;"><a href="../logout.php" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="bottom-nav">
        <a href="dashboard.php" class="active"><i class="fas fa-home"></i>Home</a>
        <a href="materi.php"><i class="fas fa-play-circle"></i>Materi</a>
        <a href="tugas.php"><i class="fas fa-clipboard-list"></i>Tugas</a>
        <a href="profil.php"><i class="fas fa-user"></i>Profil</a>
    </div>

    <div class="main">
        
        <div class="banner">
            <h2 style="margin:0; font-size:1.8rem;">Halo, <?php echo explode(' ', $user['nama_lengkap'])[0]; ?>! 👋</h2>
            <p style="margin:10px 0 0; opacity:0.9;">
                <?php if($punya_kelas){ ?>
                    Kamu terdaftar di <b><?php echo $user['tingkatan']; ?></b> (<?php echo $user['nama_kelas']; ?>).
                <?php } else { ?>
                    Selamat datang di Aplikasi E-Learning Kesetaraan.
                <?php } ?>
            </p>
        </div>

        <?php if(!$punya_kelas): ?>
            <div class="alert-box">
                <i class="fas fa-exclamation-triangle" style="font-size:1.5rem; margin-top:2px;"></i>
                <div>
                    <h4 style="margin:0 0 5px;">Akun Belum Aktif Sepenuhnya</h4>
                    <p style="margin:0; font-size:0.9rem;">
                        Halo! Akun kamu berhasil dibuat, tetapi Admin belum memasukkan kamu ke dalam <b>Kelas/Paket (A, B, atau C)</b>.
                        <br><br>
                        Silakan hubungi Admin atau Guru untuk segera diaktifkan agar materi pelajaran bisa muncul di sini.
                    </p>
                </div>
            </div>
        <?php else: ?>

            <h3 style="color:#1e293b; margin-bottom:15px;">Materi Terbaru (<?php echo $user['tingkatan']; ?>)</h3>
            
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:15px;">
                <?php 
                // Filter Materi Sesuai Paket Siswa
                // Karena single teacher, semua materi yang diupload guru akan difilter by 'kelas_id'
                // Namun jika logika single teacher mau "Semua Materi Paket A", maka filternya berdasarkan tingkatan kelas
                
                // OPSI 1: Filter by ID Kelas spesifik (Sesuai assignment admin)
                $query = "SELECT materi.*, mapel.nama_mapel 
                          FROM materi 
                          JOIN mapel ON materi.mapel_id = mapel.id 
                          WHERE materi.kelas_id = '$kelas_siswa' 
                          ORDER BY materi.id DESC LIMIT 6";
                
                $data = mysqli_query($koneksi, $query);
                
                if(mysqli_num_rows($data) > 0){
                    while($d = mysqli_fetch_array($data)){
                ?>
                <div class="card">
                    <div style="display:flex; justify-content:space-between; align-items:start;">
                        <span style="font-size:0.75rem; font-weight:700; background:#e0e7ff; color:var(--primary); padding:4px 8px; border-radius:6px;">
                            <?php echo $d['nama_mapel']; ?>
                        </span>
                        <small style="color:#94a3b8;"><?php echo date('d M', strtotime($d['tanggal_upload'])); ?></small>
                    </div>
                    
                    <h4 style="margin:10px 0; font-size:1.1rem; color:#1e293b;"><?php echo $d['judul']; ?></h4>
                    
                    <a href="<?php echo $d['link_video_drive']; ?>" target="_blank" style="display:block; text-align:center; background:var(--primary); color:white; text-decoration:none; padding:10px; border-radius:8px; font-weight:600; margin-top:15px;">
                        <i class="fas fa-play"></i> Tonton Video
                    </a>
                </div>
                <?php 
                    } 
                } else { 
                    echo "<div style='grid-column: 1/-1; text-align:center; padding:40px; color:#64748b; background:white; border-radius:16px;'>
                            <i class='fas fa-box-open' style='font-size:3rem; margin-bottom:10px; color:#cbd5e1;'></i>
                            <p>Belum ada materi yang diupload untuk kelas ini.</p>
                          </div>"; 
                } 
                ?>
            </div>

        <?php endif; ?>

    </div>

</body>
</html>