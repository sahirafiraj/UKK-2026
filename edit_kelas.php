<?php 
 
include "config/koneksi.php"; 
include "includes/cek_session.php"; 
 
if ($_SESSION['role'] != 'admin') { 
    echo "Anda tidak memiliki akses"; 
    exit; 
} 
 
$id = $_GET['id']; 
 
$query = mysqli_query($koneksi,  
    "SELECT * FROM t_kelas WHERE id='$id'" 
); 
 
$data = mysqli_fetch_assoc($query); 
?> 
 
<!DOCTYPE html>
<html>
<head>
    <title>Edit Kelas</title>
</head>

<body>

<h2>Edit Data Kelas</h2>

<form action="proses_edit_kelas.php" method="POST">

    <input type="hidden" name="id" value="<?= $data['id']; ?>">

    <label>Nama Kelas</label><br>
    <input type="text"
           name="nama"
           value="<?= $data['nama']; ?>"
           required>

    <br><br>

    <label>Tingkat</label><br>

    <select name="tingkat" required>

        <option value="X"
            <?= $data['tingkat'] == 'X' ? 'selected' : ''; ?>>
            X
        </option>

        <option value="XI"
            <?= $data['tingkat'] == 'XI' ? 'selected' : ''; ?>>
            XI
        </option>

        <option value="XII"
            <?= $data['tingkat'] == 'XII' ? 'selected' : ''; ?>>
            XII
        </option>

    </select>

    <br><br>

    <label>Jurusan</label><br>

    <input type="text"
           name="jurusan"
           value="<?= $data['jurusan']; ?>"
           required>

    <br><br>

    <label>Status</label><br>

    <select name="status_aktif">

        <option value="1"
            <?= $data['status_aktif'] == 1 ? 'selected' : ''; ?>>
            Aktif
        </option>

        <option value="0"
            <?= $data['status_aktif'] == 0 ? 'selected' : ''; ?>>
            Tidak Aktif
        </option>

    </select>

    <br><br>

    <button type="submit" name="update">
        Update
    </button>

</form>

<br>

<a href="data_kelas.php">Kembali</a>

</body>
</html>