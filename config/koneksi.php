<?php

$host = 'sql107.infinityfree.com';
$user = 'if0_43413215';
$password = 'RKon5IjYHT5';
$database = 'if0_43413215_SAHIRA';

$koneksi = mysqli_connect($host, $user, $password, $database);
if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}
?>