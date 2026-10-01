<?php

session_start();

include "config/koneksi.php";
include "includes/cek_session.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $status = $_POST['status_aktif'];
    $user_id = $_POST['user_id'];

    mysqli_query($koneksi, "
        UPDATE t_guru SET
            nip='$nip',
            nama='$nama',
            email='$email',
            status_aktif='$status',
            user_id='$user_id'
        WHERE id='$id'
    ");

    header("Location: data_guru.php");
    exit;
}