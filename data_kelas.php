<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kelas</title>
</head>
<body>

<h2>Kelola Kelas</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>
<br><br>

<a href="tambah_kelas.php">+ Tambah Kelas</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Nama Kelas</th>
        <th>Tingkat</th>
        <th>Jurusan</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;
    while ($row = mysqli_fetch_assoc($data)) {
    ?>

    <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($row['nama']); ?></td>
        <td><?= htmlspecialchars($row['tingkat']); ?></td>
        <td><?= htmlspecialchars($row['jurusan']); ?></td>

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
            <a href="edit_kelas.php?id=<?= $row['id']; ?>">Edit</a> |
            <a href="hapus_kelas.php?id=<?= $row['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus data kelas ini?')">
                Hapus
            </a>
        </td>
    </tr>

    <?php } ?>

</table>

</body>
</html>