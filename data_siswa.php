<?php

session_start();
include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['simpan'])) {

    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jk = $_POST['jenis_kelamin'];
    $tanggal = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];

    mysqli_query($koneksi, "
        INSERT INTO t_siswa
        (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
        VALUES
        ('$nis', '$nisn', '$nama', '$jk', '$tanggal', '$alamat', 1)
    ");

    header("Location: siswa.php");
    exit;
}

$data = mysqli_query($koneksi, "
    SELECT * FROM t_siswa
    ORDER BY id DESC
");

?>

<h2>Kelola Siswa</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<h3>Tambah Siswa</h3>

<form method="POST">

    NIS:
    <input type="text" name="nis" required>
    <br><br>

    NISN:
    <input type="text" name="nisn" required>
    <br><br>

    Nama:
    <input type="text" name="nama" required>
    <br><br>

    Jenis Kelamin:
    <select name="jenis_kelamin">
        <option value="L">Laki-laki</option>
        <option value="P">Perempuan</option>
    </select>
    <br><br>

    Tanggal Lahir:
    <input type="date" name="tanggal_lahir" required>
    <br><br>

    Alamat:
    <textarea name="alamat"></textarea>
    <br><br>

    <button type="submit" name="simpan">
        Simpan
    </button>

</form>

<h3>Data Siswa</h3>

<table border="1" cellpadding="8">

<tr>
    <th>NIS</th>
    <th>NISN</th>
    <th>Nama</th>
    <th>Jenis Kelamin</th>
    <th>Alamat</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($data)) { ?>

<tr>

    <td><?= $row['nis']; ?></td>
    <td><?= $row['nisn']; ?></td>
    <td><?= $row['nama']; ?></td>
    <td><?= $row['jenis_kelamin']; ?></td>
    <td><?= $row['alamat']; ?></td>

</tr>

<?php } ?>

</table>