<?php
$server = "localhost";
$user = "root";
$pass = "";
$database = "elearning_alc";

$koneksi = mysqli_connect($server, $user, $pass, $database);

if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}
?>