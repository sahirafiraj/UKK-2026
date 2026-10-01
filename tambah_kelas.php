<?php
include 'includes/cek_session.php'; 
if ($_SESSION['role'] != 'admin') {
    echo 'Anda tidak bisa memiliki akses';
    exit;
}
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kelas</title>
</head>
<body>
    <h1>Tambah Data Kelas</h1>    
    <form action="proses_tambah_kelas.php" method="post">

    <label>Nama Kelas</label><br>
    <input type="text" name="nama" required>

    <br><br>

     <label>Tingkat</label><br>
    <select name="tingkat" required>
        <option value="">-- Pilih Tingkat --</option>
        <option value="X">X</option>
        <option value="XI">XI</option>
        <option value="XII">XII</option>
    </select>

    <br><br>

    <label>Jurusan</label><br>
    <input type="text" name="jurusan" required>

    <br><br>

    <button type="submit" name="simpan">Simpan</button>

</form>

<br>

<a href="data_kelas.php">Kembali</a>

</body>
</html>