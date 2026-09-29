<?php 
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>

<h1>Sistem Informasi Pelanggaran Siswa</h1>
<p>Selamat datang, <?= $_SESSION['name']; ?></p>

<?php
if ($_SESSION['role'] == 'admin') {
    ?>
    <h3>Menu admin</h3>

    <a href="kelas.php">Kelola Kelas</a><br>
    <a href="siswa.php">Kelola Siswa</a><br>
    <a href="guru.php">Kelola Guru</a><br>
    <a href="tahun_ajaran.php">Kelola Tahun Ajaran</a><br>
    <a href="penempatan_siswa.php">Penempatan Siswa</a><br>
    <a href="wali_kelas.php">Kelola Wali Kelas</a><br>
    <a href="kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a><br>
    <a href="jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a><br>
    <a href="cetak_export.php">Cetak / Export</a><br>
<?php

} else if ($_SESSION['role'] == 'guru') {
    ?>
    <h3>Menu Guru</h3>

    <a href="catat_pelanggaran.php">Catat Pelanggaran</a><br>
    <a href="tindakan.php">Tindakan</a><br>
    <a href="laporan.php">Laporan</a><br>
    <a href="riwayat.php">Riwayat</a><br>
    <a href="rekap_poin.php">Rekap Poin</a><br>

<?php
}

?>
<br>
<a href="logout.php">Logout</a>