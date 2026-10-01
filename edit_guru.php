<?php

session_start();

include "config/koneksi.php";
include "includes/cek_session.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$id = $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_guru WHERE id='$id'"
);

$data = mysqli_fetch_assoc($query);

?>

<h2>Edit Guru</h2>

<form method="POST" action="proses_edit_guru.php">

    <input type="hidden" name="id" value="<?= $data['id']; ?>">

    NIP:
    <br>
    <input type="text"
           name="nip"
           value="<?= $data['nip']; ?>"
           required>

    <br><br>

    Nama:
    <br>
    <input type="text"
           name="nama"
           value="<?= $data['nama']; ?>"
           required>

    <br><br>

    Email:
    <br>
    <input type="email"
           name="email"
           value="<?= $data['email']; ?>"
           required>

    <br><br>

    Status:
    <br>
    <select name="status_aktif" required>

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

    User ID:
    <br>
    <input type="number"
           name="user_id"
           value="<?= $data['user_id']; ?>"
           required>

    <br><br>

    <button type="submit" name="update">
        Update
    </button>

</form>

<br>

<a href="data_guru.php">Kembali</a>