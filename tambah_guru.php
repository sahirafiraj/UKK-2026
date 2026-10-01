<?php

include "config/koneksi.php";
include "includes/cek_session.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

?>

<h2>Tambah Guru</h2>

<form method="POST" action="proses_tambah_guru.php">

    NIP:
    <br>
    <input type="text" name="nip" required>

    <br><br>

    Nama:
    <br>
    <input type="text" name="nama" required>

    <br><br>

    Email:
    <br>
    <input type="email" name="email" required>

    <br><br>

    Status:
    <br>
    <select name="status_aktif" required>
        <option value="1">Aktif</option>
        <option value="0">Tidak Aktif</option>
    </select>

    <br><br>

    User ID:
    <br>
    <input type="number" name="user_id" required>

    <br><br>

    <button type="submit" name="simpan">
        Simpan
    </button>

</form>

<br>

<a href="data_guru.php">Kembali</a>