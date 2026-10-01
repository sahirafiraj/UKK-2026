<?php

session_start();

include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($koneksi, "
    SELECT * FROM t_siswa
    WHERE id='$id'
");

$data = mysqli_fetch_assoc($query);

?>

<h2>Edit Siswa</h2>

<a href="data_siswa.php">Kembali ke Data Siswa</a>

<br><br>

<form method="POST">

    <input type="hidden" name="id"
           value="<?= $data['id']; ?>">

    NIS:
    <br>
    <input type="text"
           name="nis"
           value="<?= $data['nis']; ?>"
           required>

    <br><br>

    NISN:
    <br>
    <input type="text"
           name="nisn"
           value="<?= $data['nisn']; ?>"
           required>

    <br><br>

    Nama:
    <br>
    <input type="text"
           name="nama"
           value="<?= $data['nama']; ?>"
           required>

    <br><br>

    Jenis Kelamin:
    <br>

    <select name="jenis_kelamin">

        <option value="L"
            <?= $data['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>
            Laki-laki
        </option>

        <option value="P"
            <?= $data['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>
            Perempuan
        </option>

    </select>

    <br><br>

    Tanggal Lahir:
    <br>
    <input type="date"
           name="tanggal_lahir"
           value="<?= $data['tanggal_lahir']; ?>"
           required>

    <br><br>

    Alamat:
    <br>
    <textarea name="alamat"><?= $data['alamat']; ?></textarea>

    <br><br>

    Status:
    <br>

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


<?php

if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jk = $_POST['jenis_kelamin'];
    $tanggal = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $status = $_POST['status_aktif'];

    mysqli_query($koneksi, "
        UPDATE t_siswa SET
            nis='$nis',
            nisn='$nisn',
            nama='$nama',
            jenis_kelamin='$jk',
            tanggal_lahir='$tanggal',
            alamat='$alamat',
            status_aktif='$status'
        WHERE id='$id'
    ");

    header("Location: data_siswa.php");
    exit;
}

?>