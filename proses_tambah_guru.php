<?php

include "config/koneksi.php";
include "includes/cek_session.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['simpan'])) {

    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $status = $_POST['status_aktif'];
    $user_id = $_POST['user_id'];

    mysqli_query($koneksi, "
        INSERT INTO t_guru
        (nip, nama, email, status_aktif, user_id)
        VALUES
        ('$nip', '$nama', '$email', '$status', '$user_id')
    ");

    header("Location: data_guru.php");
    exit;
}