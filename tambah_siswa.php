<?php

session_start();

include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

?>

<h2>Tambah Siswa</h2>

<a href="data_siswa.php">Kembali ke Data Siswa</a>

<br><br>

<form action="proses_tambah_siswa.php" method="POST">

    NIS:
    <br>
    <input type="text" name="nis" required>

    <br><br>

    NISN:
    <br>
    <input type="text" name="nisn" required>

    <br><br>

    Nama:
    <br>
    <input type="text" name="nama" required>

    <br><br>

    Jenis Kelamin:
    <br>

    <select name="jenis_kelamin">

        <option value="L">Laki-laki</option>
        <option value="P">Perempuan</option>

    </select>

    <br><br>

    Tanggal Lahir:
    <br>
    <input type="date" name="tanggal_lahir" required>

    <br><br>

    Alamat:
    <br>
    <textarea name="alamat"></textarea>

    <br><br>

    <button type="submit" name="simpan">
        Simpan
    </button>

</form>