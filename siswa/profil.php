<?php 
session_start();
if($_SESSION['role'] != 'siswa'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

$id_siswa = $_SESSION['id_user'];
$notif_type = ""; // Untuk menyimpan tipe notif (success/error)
$notif_msg = "";  // Untuk pesan notifikasi

// --- LOGIKA UPDATE PROFIL ---
if(isset($_POST['update'])){
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    
    // Siapkan folder tujuan (Auto Create jika belum ada)
    $target_dir = "../assets/img/profil/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Cek apakah ada file foto yang diupload
    if(!empty($_FILES['foto']['name'])){
        $filename = $_FILES['foto']['name'];
        $ukuran = $_FILES['foto']['size'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Validasi ekstensi
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
        if(!in_array(strtolower($ext), $ekstensi_diperbolehkan)){
             $notif_type = "error";
             $notif_msg = "Gagal! Hanya file PNG, JPG, atau JPEG yang diperbolehkan.";
        } 
        // Validasi ukuran (maks 2MB)
        elseif($ukuran > 2048000) {
             $notif_type = "error";
             $notif_msg = "Gagal! Ukuran foto terlalu besar (Maks 2MB).";
        }
        else {
            // Nama file unik
            $nama_file_baru = "siswa_".$id_siswa."_".time().".".$ext;
            $tujuan_upload = $target_dir . $nama_file_baru;
            
            // Upload file
            if(move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan_upload)){
                // Hapus foto lama jika ada
                $data_lama = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT foto FROM users WHERE id='$id_siswa'"));
                if($data_lama['foto'] != null && file_exists($target_dir.$data_lama['foto'])){
                    unlink($target_dir.$data_lama['foto']);
                }

                // Update DB
                $query = "UPDATE users SET nama_lengkap='$nama', foto='$nama_file_baru' WHERE id='$id_siswa'";
                if(mysqli_query($koneksi, $query)){
                     $_SESSION['nama_lengkap'] = $nama;
                     $notif_type = "success";
                     $notif_msg = "Profil dan foto berhasil diperbarui!";
                } else {
                     $notif_type = "error";
                     $notif_msg = "Database error: " . mysqli_error($koneksi);
                }
            } else {
                 $notif_type = "error";
                 $notif_msg = "Gagal memindahkan file ke folder tujuan.";
            }
        }
    } else {
        // JIKA TIDAK GANTI FOTO
        $query = "UPDATE users SET nama_lengkap='$nama' WHERE id='$id_siswa'";
        if(mysqli_query($koneksi, $query)){
            $_SESSION['nama_lengkap'] = $nama;
            $notif_type = "success";
            $notif_msg = "Nama lengkap berhasil diperbarui!";
        } else {
            $notif_type = "error";
            $notif_msg = "Gagal update database.";
        }
    }
}

// Ambil data user terbaru
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id_siswa'"));

// Tentukan foto profil
$foto_profil = "../assets/img/profil/default.jpg"; 
if(!empty($user['foto']) && file_exists("../assets/img/profil/".$user['foto'])){
    $foto_profil = "../assets/img/profil/".$user['foto'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Profil Saya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root { --primary: #4f46e5; --bg: #f3f4f6; --sidebar: #ffffff; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; display: flex; }
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar); position: fixed; border-right: 1px solid #e5e7eb; z-index: 100; }
        .brand { padding: 25px; font-size: 1.4rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .menu { list-style: none; padding: 10px 20px; }
        .menu a { display: flex; align-items: center; padding: 12px 15px; color: #6b7280; text-decoration: none; border-radius: 10px; font-weight: 500; margin-bottom: 5px; transition: 0.3s; }
        .menu a:hover, .menu a.active { background: #eef2ff; color: var(--primary); }
        .menu i { width: 25px; font-size: 1.1rem; }
        .main { margin-left: 260px; padding: 40px; width: 100%; min-height: 100vh; }
        
        .card { background: white; padding: 40px; border-radius: 20px; max-width: 550px; margin: 0 auto; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; }
        .profile-header { text-align: center; margin-bottom: 30px; }
        
        .avatar-container { position: relative; width: 130px; height: 130px; margin: 0 auto 15px; }
        .avatar-img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 4px solid #e0e7ff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .avatar-upload-btn { 
            position: absolute; bottom: 5px; right: 5px; background: var(--primary); color: white; 
            width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            cursor: pointer; border: 3px solid white; transition: 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .avatar-upload-btn:hover { background: #4338ca; transform: scale(1.1); }
        #file-input { display: none; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 0.9rem; }
        .form-input { width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; box-sizing: border-box; transition:0.3s; font-family: inherit; }
        .form-input:focus { border-color: var(--primary); outline: none; }
        .form-input:disabled { background: #f9fafb; color: #9ca3af; cursor: not-allowed; }
        
        .btn-save { width: 100%; padding: 14px; background: var(--primary); color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition:0.3s; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2); }
        .btn-save:hover { background: #4338ca; transform: translateY(-2px); box-shadow: 0 8px 12px rgba(79, 70, 229, 0.3); }
        
        .info-box { background: #fffbeb; border: 1px solid #fcd34d; color: #b45309; padding: 12px; border-radius: 10px; font-size: 0.85rem; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand"><i class="fas fa-graduation-cap"></i> ALC STUDENT</div>
        <ul class="menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="materi.php"><i class="fas fa-book-open"></i> Materi Belajar</a></li>
            <li><a href="tugas.php"><i class="fas fa-tasks"></i> Tugas & Ujian</a></li>
            <li><a href="chat.php"><i class="fas fa-comments"></i> Forum Kelas</a></li>
            <li><a href="profil.php" class="active"><i class="fas fa-user-circle"></i> Profil Saya</a></li>
            <li style="margin-top: 30px;"><a href="../logout.php" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <div class="profile-header">
                    <div class="avatar-container">
                        <img src="<?php echo $foto_profil; ?>" class="avatar-img" id="avatar-preview">
                        <label for="file-input" class="avatar-upload-btn" title="Ganti Foto">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" name="foto" id="file-input" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(event)">
                    </div>

                    <h2 style="margin:0; color:#1f2937;"><?php echo $user['nama_lengkap']; ?></h2>
                    <span style="color:#6b7280; background:#f3f4f6; padding:4px 12px; border-radius:20px; font-size:0.8rem; font-weight:600; display:inline-block; margin-top:5px;">SISWA</span>
                </div>
                
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <div>Untuk keamanan, password hanya dapat diubah oleh Admin/Guru.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-input" value="<?php echo $user['username']; ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-input" value="<?php echo $user['nama_lengkap']; ?>" required>
                </div>

                <button type="submit" name="update" class="btn-save">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('avatar-preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

    <?php if($notif_type != ""): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $notif_type; ?>',
            title: '<?php echo ($notif_type == "success") ? "Berhasil!" : "Gagal!"; ?>',
            text: '<?php echo $notif_msg; ?>',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
    <?php endif; ?>

</body>
</html>