<?php 
session_start();
include '../layout/header.php';
include '../layout/sidebar.php';
include '../config/koneksi.php';

$id_siswa = $_SESSION['id_user'];
// Misal kita ambil ID Mapel dari URL (?id_mapel=1)
$id_mapel = isset($_GET['id_mapel']) ? $_GET['id_mapel'] : 1; 

// 1. LOGIKA PRESENSI
if(isset($_POST['absen_hadir'])){
    $presensi_id = $_POST['presensi_id'];
    $cek_absen = mysqli_query($koneksi, "SELECT * FROM detail_presensi WHERE presensi_id='$presensi_id' AND siswa_id='$id_siswa'");
    
    if(mysqli_num_rows($cek_absen) == 0){
        mysqli_query($koneksi, "INSERT INTO detail_presensi (presensi_id, siswa_id, status, waktu_absen) VALUES ('$presensi_id', '$id_siswa', 'Hadir', NOW())");
        echo "<script>alert('Berhasil Absen!');</script>";
    } else {
        echo "<script>alert('Anda sudah absen sebelumnya!');</script>";
    }
}

// 2. LOGIKA UPLOAD TUGAS
if(isset($_POST['upload_tugas'])){
    $tugas_id = $_POST['tugas_id'];
    
    // Validasi Deadline
    $cek_tugas = mysqli_query($koneksi, "SELECT deadline FROM tugas WHERE id='$tugas_id'");
    $dt = mysqli_fetch_assoc($cek_tugas);
    $sekarang = date('Y-m-d H:i:s');

    if($sekarang > $dt['deadline']){
        echo "<script>alert('Maaf, waktu pengumpulan sudah habis!');</script>";
    } else {
        // Proses Upload File
        $filename = $_FILES['file_tugas']['name'];
        $tmp_name = $_FILES['file_tugas']['tmp_name'];
        $new_name = date('dmYHis')."_".$filename;
        
        move_uploaded_file($tmp_name, "../uploads/tugas/".$new_name);
        
        mysqli_query($koneksi, "INSERT INTO pengumpulan (tugas_id, siswa_id, file_siswa) VALUES ('$tugas_id', '$id_siswa', '$new_name')");
        echo "<script>alert('Tugas Berhasil Dikirim!');</script>";
    }
}
?>

<div class="container-fluid">
    
    <div class="card bg-primary text-white rounded-3 mb-4 p-4 shadow">
        <h2>Matematika - Kelas X IPA 1</h2>
        <p class="mb-0">Selamat belajar, jangan lupa absen dan kerjakan tugas tepat waktu.</p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <h5 class="fw-bold mb-3"><i class="fas fa-play-circle me-2"></i> Materi Pembelajaran</h5>
            
            <?php 
            $materi = mysqli_query($koneksi, "SELECT * FROM materi WHERE mapel_id='$id_mapel' ORDER BY id DESC LIMIT 1");
            if(mysqli_num_rows($materi) > 0){
                $m = mysqli_fetch_assoc($materi);
            ?>
            <div class="card card-custom p-0 overflow-hidden mb-4">
                <div class="ratio ratio-16x9">
                    <iframe src="<?php echo $m['link_video_drive']; ?>" allowfullscreen></iframe>
                </div>
                <div class="p-3">
                    <h4 class="fw-bold"><?php echo $m['judul']; ?></h4>
                    <p class="text-muted"><?php echo $m['deskripsi']; ?></p>
                </div>
            </div>
            <?php } else { echo "<div class='alert alert-info'>Belum ada materi.</div>"; } ?>
        </div>

        <div class="col-md-4">
            
            <div class="card card-custom p-4 mb-4 border-start border-5 border-success">
                <h5 class="fw-bold mb-3">Presensi Hari Ini</h5>
                <?php 
                $today = date('Y-m-d');
                $cek_sesi = mysqli_query($koneksi, "SELECT * FROM presensi WHERE mapel_id='$id_mapel' AND tanggal='$today' AND status_aktif='Y'");
                
                if(mysqli_num_rows($cek_sesi) > 0){
                    $sesi = mysqli_fetch_assoc($cek_sesi);
                    // Cek apakah siswa sudah absen
                    $sudah_absen = mysqli_query($koneksi, "SELECT * FROM detail_presensi WHERE presensi_id='$sesi[id]' AND siswa_id='$id_siswa'");
                    
                    if(mysqli_num_rows($sudah_absen) > 0){
                        echo "<button class='btn btn-success w-100 disabled'><i class='fas fa-check'></i> Sudah Hadir</button>";
                    } else {
                ?>
                    <form method="POST">
                        <input type="hidden" name="presensi_id" value="<?php echo $sesi['id']; ?>">
                        <button type="submit" name="absen_hadir" class="btn btn-primary-custom w-100">
                            KLIK UNTUK HADIR
                        </button>
                    </form>
                <?php 
                    }
                } else {
                    echo "<div class='text-muted small'>Presensi belum dibuka oleh Guru.</div>";
                }
                ?>
            </div>

            <div class="card card-custom p-4 border-start border-5 border-warning">
                <h5 class="fw-bold mb-3">Tugas Terbaru</h5>
                <?php 
                // Ambil tugas yg deadline-nya belum lewat atau baru lewat
                $tugas = mysqli_query($koneksi, "SELECT * FROM tugas WHERE mapel_id='$id_mapel' ORDER BY id DESC LIMIT 1");
                $t = mysqli_fetch_assoc($tugas);
                
                if($t){
                    $sekarang = date('Y-m-d H:i:s');
                    $is_expired = ($sekarang > $t['deadline']);
                ?>
                    <h6><?php echo $t['judul']; ?></h6>
                    <small class="text-danger d-block mb-2">Deadline: <?php echo date('d M Y, H:i', strtotime($t['deadline'])); ?></small>
                    
                    <?php if(!$is_expired){ ?>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="tugas_id" value="<?php echo $t['id']; ?>">
                            <input type="file" name="file_tugas" class="form-control mb-2" required>
                            <button type="submit" name="upload_tugas" class="btn btn-warning w-100 text-white">
                                <i class="fas fa-upload"></i> Kirim Tugas
                            </button>
                        </form>
                    <?php } else { ?>
                        <div class="alert alert-secondary p-2 small text-center">Waktu Habis</div>
                    <?php } ?>

                <?php } else { echo "Tidak ada tugas aktif."; } ?>
            </div>

        </div>
    </div>
</div>
<?php include '../layout/footer.php'; ?>