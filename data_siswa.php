<?php

session_start();

include "config/koneksi.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$data = mysqli_query($koneksi, "
    SELECT * FROM t_siswa
    ORDER BY id DESC
");

?>

<h2>Kelola Siswa</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<a href="tambah_siswa.php">+ Tambah Siswa</a>

<h3>Data Siswa</h3>

<table border="1" cellpadding="8" cellspacing="0">

<tr>
    <th>No</th>
    <th>NIS</th>
    <th>NISN</th>
    <th>Nama</th>
    <th>Jenis Kelamin</th>
    <th>Tanggal Lahir</th>
    <th>Alamat</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php

$no = 1;

while ($row = mysqli_fetch_assoc($data)) {

?>

<tr>

    <td><?= $no++; ?></td>

    <td><?= $row['nis']; ?></td>

    <td><?= $row['nisn']; ?></td>

    <td><?= $row['nama']; ?></td>

    <td>
        <?php
        if ($row['jenis_kelamin'] == 'L') {
            echo "Laki-laki";
        } else {
            echo "Perempuan";
        }
        ?>
    </td>

    <td><?= $row['tanggal_lahir']; ?></td>

    <td><?= $row['alamat']; ?></td>

    <td>
        <?php
        if ($row['status_aktif'] == 1) {
            echo "Aktif";
        } else {
            echo "Tidak Aktif";
        }
        ?>
    </td>

    <td>

        <a href="edit_siswa.php?id=<?= $row['id']; ?>">
            Edit
        </a>

        |

        <a href="hapus_siswa.php?id=<?= $row['id']; ?>"
           onclick="return confirm('Yakin ingin menghapus data ini?')">
            Hapus
        </a>

    </td>

</tr>

<?php } ?>

</table>