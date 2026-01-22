<?php 
session_start();
if($_SESSION['role'] != 'siswa'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

$id_siswa = $_SESSION['id_user'];
// Kita ambil data user untuk tahu nama (opsional)
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id_siswa'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Materi Belajar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* TEMA GLOBAL SISWA (WHITE SIDEBAR) */
        :root { --primary: #4f46e5; --bg: #f3f4f6; --sidebar: #ffffff; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        /* SIDEBAR */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar); position: fixed; border-right: 1px solid #e5e7eb; z-index: 100; }
        .brand { padding: 25px; font-size: 1.4rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .menu { list-style: none; padding: 10px 20px; }
        .menu a { display: flex; align-items: center; padding: 12px 15px; color: #6b7280; text-decoration: none; border-radius: 10px; font-weight: 500; margin-bottom: 5px; transition: 0.3s; }
        .menu a:hover, .menu a.active { background: #eef2ff; color: var(--primary); }
        .menu i { width: 25px; font-size: 1.1rem; }

        /* KONTEN UTAMA */
        .main { margin-left: 260px; padding: 40px; width: 100%; min-height: 100vh; }
        
        .page-header h1 { font-size: 1.8rem; color: #1f2937; margin: 0 0 5px 0; }
        .page-header p { color: #6b7280; margin: 0 0 30px 0; }

        /* GRID MATERI */
        .grid-materi { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        
        .card-materi { 
            background: white; border-radius: 16px; border: 1px solid #e5e7eb; overflow: hidden; 
            transition: 0.3s; display: flex; flex-direction: column;
        }
        .card-materi:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: var(--primary); }
        
        .card-img { 
            height: 140px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); 
            display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 3rem;
        }
        
        .card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        .mapel-badge { 
            background: #dbeafe; color: #1e40af; font-size: 0.75rem; font-weight: 700; 
            padding: 4px 8px; border-radius: 6px; align-self: flex-start; margin-bottom: 10px; 
        }
        .card-title { font-size: 1.1rem; font-weight: 700; color: #1f2937; margin: 0 0 10px 0; line-height: 1.4; }
        .card-desc { font-size: 0.9rem; color: #6b7280; margin-bottom: 20px; flex: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        
        .card-footer { 
            padding-top: 15px; border-top: 1px solid #f3f4f6; 
            display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #6b7280;
        }
        
        .btn-watch { 
            background: var(--primary); color: white; text-decoration: none; 
            padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; transition: 0.2s; 
        }
        .btn-watch:hover { background: #4338ca; }

    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand"><i class="fas fa-graduation-cap"></i> ALC STUDENT</div>
        <ul class="menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="materi.php" class="active"><i class="fas fa-book-open"></i> Materi Belajar</a></li>
            <li><a href="tugas.php"><i class="fas fa-tasks"></i> Tugas & Ujian</a></li>
            <li><a href="chat.php"><i class="fas fa-comments"></i> Forum Kelas</a></li>
            <li><a href="profil.php"><i class="fas fa-user-circle"></i> Profil Saya</a></li>
            <li style="margin-top: 30px;"><a href="../logout.php" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="page-header">
            <h1>Perpustakaan Materi</h1>
            <p>Akses semua video pembelajaran dan modul dari gurumu.</p>
        </div>

        <div class="grid-materi">
            <?php 
            // Query mengambil materi beserta nama mapel dan kelas
            // Diurutkan dari yang terbaru
            $query = "SELECT materi.*, mapel.nama_mapel, kelas.nama_kelas 
                      FROM materi 
                      JOIN mapel ON materi.mapel_id = mapel.id 
                      JOIN kelas ON materi.kelas_id = kelas.id 
                      ORDER BY materi.id DESC";
            
            $data = mysqli_query($koneksi, $query);
            
            if(mysqli_num_rows($data) > 0){
                while($d = mysqli_fetch_array($data)){
            ?>
            <div class="card-materi">
                <div class="card-img">
                    <i class="fas fa-play-circle"></i>
                </div>
                
                <div class="card-body">
                    <span class="mapel-badge"><?php echo $d['nama_mapel']; ?></span>
                    <h3 class="card-title"><?php echo $d['judul']; ?></h3>
                    <p class="card-desc">
                        <?php echo (empty($d['deskripsi'])) ? 'Tidak ada deskripsi tambahan.' : $d['deskripsi']; ?>
                    </p>
                    
                    <div class="card-footer">
                        <span><i class="fas fa-users"></i> <?php echo $d['nama_kelas']; ?></span>
                        <a href="<?php echo $d['link_video_drive']; ?>" target="_blank" class="btn-watch">
                            Tonton <i class="fas fa-arrow-right" style="font-size:0.8rem; margin-left:5px;"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php 
                } 
            } else {
                echo "<p style='color:#6b7280; font-style:italic;'>Belum ada materi yang diupload oleh guru.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>