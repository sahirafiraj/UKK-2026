<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $tingkat = $_POST['tingkat'];
    $jurusan = $_POST['jurusan'];

    mysqli_query($koneksi, " INSERT INTO t_kelas (nama, tingkat, jurusan, status_aktif)
    VALUES ('$nama', '$tingkat', '$jurusan', 1)
    ");

    header("Location: kelas.php");
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY id desc ");

?>

<h2>Kelola Kelas</h2>

<a href=dashboard.php>Kembali ke Dashboard</a>
<br><br>
<form method="POST">
    Nama Kelas:
    <input type="text" name="nama" required>
    <br><br>

    Tingkat:
    <input type="text" name="tingkat" required>
    <br><br>

    Jurusan:
    <input type="text" name="jurusan" required>
    <br><br>

    <button type="submit" value="simpan"> Simpan
    </button>

</form>

<h3>Data Kelas</h3>
<table border="1" cellpadding="8">
    <tr>
        <th>Nama</th>
        <th>Tingkat</th>
        <th>Jurusan</th>
    </tr>

<?php while ($row = mysqli_fetch_assoc($data)) { ?>
    <tr>
        <td><?= $row['nama']; ?></td>
        <td><?= $row['tingkat']; ?></td>
        <td><?= $row['jurusan']; ?></td>
    </tr>
<?php } ?>
</table>