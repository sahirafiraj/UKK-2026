<?php

include "config/koneksi.php";
include "includes/cek_session.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM t_guru ORDER BY id DESC");

?>

<h2>Data Guru</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<a href="tambah_guru.php">+ Tambah Guru</a>

<br><br>

<table border="1" cellpadding="8">

    <tr>
        <th>No</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Status</th>
        <th>User ID</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;

    while ($row = mysqli_fetch_assoc($data)) {
    ?>

    <tr>
        <td><?= $no++; ?></td>
        <td><?= $row['nip']; ?></td>
        <td><?= $row['nama']; ?></td>
        <td><?= $row['email']; ?></td>
        <td>
            <?= $row['status_aktif'] == 1 ? 'Aktif' : 'Tidak Aktif'; ?>
        </td>
        <td><?= $row['user_id']; ?></td>
        <td>
            <a href="edit_guru.php?id=<?= $row['id']; ?>">
                Edit
            </a>
            |
            <a href="hapus_guru.php?id=<?= $row['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus data guru ini?')">
                Hapus
            </a>
        </td>
    </tr>
    <?php } ?>
</table>