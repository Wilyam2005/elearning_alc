<?php 
session_start();
include 'config/koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
$cek = mysqli_num_rows($query);

if($cek > 0){
    $data = mysqli_fetch_assoc($query);
    // Verifikasi Password Hash
    if(password_verify($password, $data['password'])){
        $_SESSION['username'] = $username;
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['id_user'] = $data['id'];
        $_SESSION['status'] = "login";

        // Redirect sesuai Role
        if($data['role'] == "admin"){
            header("location:admin/dashboard.php");
        } else if($data['role'] == "guru"){
            header("location:guru/dashboard.php");
        } else if($data['role'] == "siswa"){
            header("location:siswa/dashboard.php");
        }
    } else {
        header("location:index.php?pesan=gagal");
    }
} else {
    header("location:index.php?pesan=gagal");
}
?>