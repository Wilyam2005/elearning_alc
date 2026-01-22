<?php 
session_start();
include '../config/koneksi.php';
$id_siswa = $_SESSION['id_user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tugas & Ujian</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --bg: #f3f4f6; --sidebar: #ffffff; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; display: flex; }
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar); position: fixed; border-right: 1px solid #e5e7eb; }
        .brand { padding: 25px; font-size: 1.4rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .menu { list-style: none; padding: 10px 20px; }
        .menu a { display: flex; align-items: center; padding: 12px 15px; color: #6b7280; text-decoration: none; border-radius: 10px; font-weight: 500; margin-bottom: 5px; }
        .menu a:hover, .menu a.active { background: #eef2ff; color: var(--primary); }
        .menu i { width: 25px; font-size: 1.1rem; }
        .main { margin-left: 260px; padding: 40px; width: 100%; }
        
        .task-card { background: white; padding: 20px; border-radius: 16px; border: 1px solid #e5e7eb; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s; }
        .task-card:hover { transform: translateY(-3px); border-color: var(--primary); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        
        .task-info h4 { margin: 0 0 5px 0; color: #1f2937; font-size: 1.1rem; }
        .task-meta { font-size: 0.85rem; color: #6b7280; }
        .badge-type { background: #e0e7ff; color: var(--primary); padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.7rem; margin-right: 8px; }
        
        .btn-do { padding: 10px 20px; background: var(--primary); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 0.9rem; }
        .btn-done { padding: 10px 20px; background: #d1fae5; color: #065f46; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 0.9rem; pointer-events: none; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><i class="fas fa-graduation-cap"></i> ALC STUDENT</div>
        <ul class="menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="materi.php"><i class="fas fa-book-open"></i> Materi Belajar</a></li>
            <li><a href="tugas.php" class="active"><i class="fas fa-tasks"></i> Tugas & Ujian</a></li>
            <li><a href="chat.php"><i class="fas fa-comments"></i> Forum Kelas</a></li>
            <li><a href="profil.php"><i class="fas fa-user-circle"></i> Profil Saya</a></li>
            <li style="margin-top: 30px;"><a href="../logout.php" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <h1 style="color:#1f2937;">Daftar Tugas</h1>
        
        <?php 
        $query = "SELECT tugas.*, mapel.nama_mapel FROM tugas JOIN mapel ON tugas.mapel_id = mapel.id ORDER BY tugas.id DESC";
        $data = mysqli_query($koneksi, $query);
        while($d = mysqli_fetch_array($data)){
            // Cek apakah sudah dikerjakan
            $cek = mysqli_query($koneksi, "SELECT * FROM pengumpulan WHERE tugas_id='".$d['id']."' AND siswa_id='$id_siswa'");
            $sudah = mysqli_num_rows($cek);
        ?>
        <div class="task-card">
            <div class="task-info">
                <div style="margin-bottom:5px;">
                    <span class="badge-type"><?php echo strtoupper($d['tipe_tugas']); ?></span>
                    <span style="font-size:0.8rem; color:#6b7280;"><?php echo $d['nama_mapel']; ?></span>
                </div>
                <h4><?php echo $d['judul']; ?></h4>
                <div class="task-meta">
                    <i class="far fa-clock"></i> Deadline: <span style="color:#ef4444; font-weight:600;"><?php echo date('d M, H:i', strtotime($d['deadline'])); ?></span>
                </div>
            </div>
            <div>
                <?php if($sudah > 0){ ?>
                    <a href="#" class="btn-done"><i class="fas fa-check-circle"></i> Selesai</a>
                <?php } else { ?>
                    <a href="tugas_kerjakan.php?id=<?php echo $d['id']; ?>" class="btn-do">Kerjakan</a>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</body>
</html>