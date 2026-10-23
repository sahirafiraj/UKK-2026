
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Pengguna';
$role = strtolower(trim($_SESSION['role'] ?? ''));
$halaman = basename($_SERVER['PHP_SELF']);

if ($role != 'admin' && $role != 'guru') {
    echo "Anda tidak memiliki akses";
    exit;
}

$query = mysqli_query($koneksi, "
    SELECT
        siswa_id,
        nama_siswa,
        nama_kelas,
        SUM(poin) AS total_poin,
        COUNT(*) AS jumlah_pelanggaran
    FROM t_pelanggaran_siswa
    GROUP BY siswa_id, nama_siswa, nama_kelas
    ORDER BY total_poin DESC
");

if (!$query) {
    die("Query gagal: " . htmlspecialchars(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rekap Poin - Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/3.1.4/css/dataTables.dataTables.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <?php if ($role == 'admin') { ?>

                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a href="data_siswa.php" class="nav-link text-white">Data Siswa</a>
                    </li>

                    <li class="nav-item">
                        <a href="data_guru.php" class="nav-link text-white">Data Guru</a>
                    </li>

                    <li class="nav-item">
                        <a href="data_kelas.php" class="nav-link text-white">Data Kelas</a>
                    </li>

                    <li class="nav-item">
                        <a href="data_tahun_ajaran.php" class="nav-link text-white">Tahun Ajaran</a>
                    </li>

                    <li class="nav-item">
                        <a href="data_penempatan_siswa.php" class="nav-link text-white">Penempatan Siswa</a>
                    </li>

                    <li class="nav-item">
                        <a href="data_wali_kelas.php" class="nav-link text-white">Data Wali Kelas</a>
                    </li>

                    <li class="nav-item">
                        <a href="data_kategori_pelanggaran.php" class="nav-link text-white">Kategori Pelanggaran</a>
                    </li>

                    <li class="nav-item">
                        <a href="data_jenis_pelanggaran.php" class="nav-link text-white">Jenis Pelanggaran</a>
                    </li>

                    <li class="nav-item">
                        <a href="laporan.php" class="nav-link text-white">Laporan</a>
                    </li>

                <?php } else { ?>

                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a href="catat_pelanggaran.php" class="nav-link text-white">Catat Pelanggaran</a>
                    </li>

                    <li class="nav-item">
                        <a href="tindakan.php" class="nav-link text-white">Tindakan</a>
                    </li>

                    <li class="nav-item">
                        <a href="riwayat.php" class="nav-link text-white">Riwayat</a>
                    </li>

                    <li class="nav-item">
                        <a href="rekap_poin.php" class="nav-link active">Rekap Poin</a>
                    </li>

                <?php } ?>

                <li class="nav-item">
                    <a href="about_me.php" class="nav-link text-white">About Me</a>
                </li>

                <hr class="border-white">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-white">Logout</a>
                </li>

            </ul>
        </div>

        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold">Rekap Poin Pelanggaran</h2>

                <p class="text-secondary mb-0">
                    Ringkasan jumlah pelanggaran dan total poin setiap siswa.
                </p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">Data Rekap Poin Siswa</h5>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle"
                               id="tabelRekapPoin">

                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jumlah Pelanggaran</th>
                                    <th>Total Poin</th>
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
                                            <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['jumlah_pelanggaran'] ?? '0'); ?>
                                        </td>

                                        <td>
                                            <span class="badge bg-primary">
                                                <?= htmlspecialchars($data['total_poin'] ?? '0'); ?> poin
                                            </span>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            Belum ada data rekap poin.
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

            <p class="text-secondary text-center mt-4 small">
                © 2026 Sistem Pelanggaran Siswa
            </p>

        </main>

    </div>
</div>

<script src="https://cdn.datatables.net/3.1.4/js/dataTables.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    new DataTable('#tabelRekapPoin', {
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        layout: {
            topStart: 'info',
            bottom: 'paging',
            bottomStart: null,
            bottomEnd: null
        },
        language: {
            search: 'Cari:',
            searchPlaceholder: 'Cari nama siswa...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada data rekap poin',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Berikutnya',
                previous: 'Sebelumnya'
            }
        },
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ]
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
