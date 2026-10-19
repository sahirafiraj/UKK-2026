
<?php
// Memulai session
session_start();

// Menghubungkan ke database
include "config/koneksi.php";

// Memeriksa login dan hak akses admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Mengambil nama pengguna
$halaman = basename($_SERVER['PHP_SELF']);
$nama_login = $_SESSION['name'] ?? 'Admin';

// Mengambil data guru
$query = mysqli_query($koneksi, "SELECT * FROM t_guru ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Data Guru</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR ADMIN -->
        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link text-white">
                        Data Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php" class="nav-link active">
                        Data Guru
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php" class="nav-link text-white">
                        Data Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php" class="nav-link text-white">
                        Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_penempatan_siswa.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php" class="nav-link text-white">
                        Data Wali Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php" class="nav-link text-white">
                        Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php" class="nav-link text-white">
                        Jenis Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="laporan.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php" class="nav-link text-white">
                        About Me
                    </a>
                </li>

                <hr class="border-white">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-white">
                        Logout
                    </a>
                </li>

            </ul>
        </div>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <!-- HEADER HALAMAN -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">

                <h2 class="fw-bold">Kelola Data Guru</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($nama_login); ?></b>
                </p>

            </div>

            <!-- CARD DATA GURU -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <h5 class="mb-0 fw-bold">Daftar Data Guru</h5>

                    <div>
                        <a href="dashboard.php" class="btn btn-secondary">
                            Kembali ke Dashboard
                        </a>

                        <a href="tambah_guru.php" class="btn btn-primary">
                            Tambah Guru
                        </a>
                    </div>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelGuru"
                               class="table table-bordered table-striped table-hover align-middle">

                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>User ID</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $no = 1;

                                if ($query && mysqli_num_rows($query) > 0) {

                                    while ($data = mysqli_fetch_assoc($query)) {
                                ?>

                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td>
                                        <?= htmlspecialchars($data['nama'] ?? ''); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['email'] ?? ''); ?>
                                    </td>

                                    <td>
                                        <?php if (($data['status_aktif'] ?? 0) == 1) { ?>

                                            <span class="badge bg-success">
                                                Aktif
                                            </span>

                                        <?php } else { ?>

                                            <span class="badge bg-secondary">
                                                Tidak Aktif
                                            </span>

                                        <?php } ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['id'] ?? ''); ?>
                                    </td>

                                    <td>
                                        <a href="edit_guru.php?id=<?= urlencode($data['id']); ?>"
                                           class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <a href="hapus_guru.php?id=<?= urlencode($data['id']); ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus data guru ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>

                                <?php
                                    }

                                } else {
                                ?>

                                <tr>
                                    <td colspan="6" class="text-center">
                                        Belum ada data guru.
                                    </td>
                                </tr>

                                <?php
                                }
                                ?>

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        </main>

    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Mengaktifkan DataTables -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const tabel = document.querySelector('#tabelGuru');

    if (tabel && typeof DataTable !== 'undefined') {

        new DataTable(tabel, {
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            order: [],

            language: {
                search: 'Cari:',
                searchPlaceholder: 'Cari data guru...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data guru',

                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya'
                }
            },

            columnDefs: [
                {
                    targets: 5,
                    orderable: false,
                    searchable: false
                }
            ]
        });

    } else {
        console.error('DataTables belum berhasil dimuat.');
    }

});
</script>

</body>
</html>