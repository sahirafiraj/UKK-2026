
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

// ADMIN: semua pelanggaran yang selesai
if ($role == 'admin') {

    $query = mysqli_query($koneksi, "
        SELECT
            ps.*,
            pk.nama AS nama_kategori
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_pelanggaran_kategori pk
            ON ps.pelanggaran_kategori_id = pk.id
        WHERE ps.status = 'Selesai'
        ORDER BY ps.tanggal DESC, ps.id DESC
    ");

} else {

    // GURU: hanya pelanggaran milik guru login yang selesai
    $nama_guru_login = mysqli_real_escape_string(
        $koneksi,
        $nama_login
    );

    $query = mysqli_query($koneksi, "
        SELECT
            ps.*,
            pk.nama AS nama_kategori
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_pelanggaran_kategori pk
            ON ps.pelanggaran_kategori_id = pk.id
        WHERE ps.nama_guru = '$nama_guru_login'
        AND ps.status = 'Selesai'
        ORDER BY ps.tanggal DESC, ps.id DESC
    ");
}

if (!$query) {
    die("Query gagal: " . htmlspecialchars(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Pelanggaran - Sistem Pelanggaran</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DATATABLES CSS -->
    <link href="https://cdn.datatables.net/3.1.4/css/dataTables.dataTables.min.css"
          rel="stylesheet">

    <!-- CSS UTAMA -->
    <link rel="stylesheet" href="style/style.css">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <?php if ($role == 'admin') { ?>

                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : 'text-white'; ?>">Dashboard</a>
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
                        <a href="riwayat.php" class="nav-link <?= $halaman == 'riwayat.php' ? 'active' : 'text-white'; ?>">Riwayat</a>
                    </li>

                    <li class="nav-item">
                        <a href="rekap_poin.php" class="nav-link text-white">Rekap Poin</a>
                    </li>

                <?php } ?>

                <li class="nav-item">
                    <a href="laporan.php" class="nav-link text-white">Laporan</a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php" class="nav-link text-white">About Me</a>
                </li>

                <hr class="border-white">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-white">Logout</a>
                </li>

            </ul>
        </div>

        <!-- KONTEN -->
        <main class="content-main">
            <div class="page-content">

                <!-- HEADER -->
                <div class="bg-white rounded shadow-sm p-4 mb-4">

                    <h2 class="fw-bold">Riwayat Pelanggaran</h2>

                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($nama_login); ?></b>
                    </p>

                </div>

                <!-- CARD RIWAYAT -->
                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-4">Daftar Pelanggaran</h5>

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover align-middle" id="tabelRiwayat">

                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Pelanggaran</th>
                                        <th>Kategori</th>
                                        <th>Tanggal</th>
                                        <th>Poin</th>
                                        <th>Guru Pencatat</th>
                                        <th>Keterangan</th>
                                        <th>Tindakan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    $no = 1;

                                    while ($data = mysqli_fetch_assoc($query)) {
                                    ?>

                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td><?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?></td>

                                        <td><?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?></td>

                                        <td><?= htmlspecialchars($data['nama_kategori'] ?? '-'); ?></td>

                                        <td><?= htmlspecialchars($data['tanggal'] ?? '-'); ?></td>

                                        <td><?= htmlspecialchars($data['poin'] ?? '0'); ?></td>

                                        <td><?= htmlspecialchars($data['nama_guru'] ?? '-'); ?></td>

                                        <td><?= htmlspecialchars($data['keterangan'] ?? '-'); ?></td>

                                        <td><?= htmlspecialchars($data['tindakan'] ?? '-'); ?></td>

                                        <td>
                                            <span class="badge bg-success">
                                                Selesai
                                            </span>
                                        </td>
                                    </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>

                <!-- FOOTER -->
                <p class="text-secondary text-center mt-4 small">
                    © 2026 Sistem Pelanggaran Siswa
                </p>

            </div>
        </main>

    </div>
</div>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DATATABLES JS -->
<script src="https://cdn.datatables.net/3.1.4/js/dataTables.min.js"></script>

<!-- INISIALISASI DATATABLES -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    new DataTable('#tabelRiwayat', {

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [],

        language: {
            search: 'Cari:',
            searchPlaceholder: 'Cari data...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada catatan pelanggaran',

            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Berikutnya',
                previous: 'Sebelumnya'
            }
        }

    });

});
</script>

</body>
</html>