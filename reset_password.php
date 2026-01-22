<?php
// Hubungkan ke database
include 'config/koneksi.php';

// Password yang ingin kita pakai
$password_baru = "123456";

// Buat Hash Password yang Valid (Kunci Masalahnya Disini)
$password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

// Hapus User Lama (Opsional, biar bersih)
mysqli_query($koneksi, "DELETE FROM users WHERE username IN ('admin', 'guru1', 'siswa1')");

// Masukkan User Baru dengan Hash yang BENAR
$query_admin = "INSERT INTO users (nama_lengkap, username, password, role) VALUES ('Administrator', 'admin', '$password_hash', 'admin')";
$query_guru  = "INSERT INTO users (nama_lengkap, username, password, role) VALUES ('Pak Budi', 'guru1', '$password_hash', 'guru')";
$query_siswa = "INSERT INTO users (nama_lengkap, username, password, role) VALUES ('Ani Siswa', 'siswa1', '$password_hash', 'siswa')";

if(mysqli_query($koneksi, $query_admin) && mysqli_query($koneksi, $query_guru) && mysqli_query($koneksi, $query_siswa)){
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
    echo "<h1 style='color:green;'>BERHASIL! ✅</h1>";
    echo "<p>Password untuk <b>admin</b>, <b>guru1</b>, dan <b>siswa1</b> sudah di-reset menjadi: <b>123456</b></p>";
    echo "<br><a href='index.php' style='background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>KLIK DISINI UNTUK LOGIN</a>";
    echo "</div>";
} else {
    echo "Gagal: " . mysqli_error($koneksi);
}
?>