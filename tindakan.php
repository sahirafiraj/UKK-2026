
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// CEK LOGIN
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

// STATUS WALI KELAS
$status_wali_kelas = false;

if ($role == 'guru') {

    $id_user = (int) ($_SESSION['id'] ?? 0);

    // MENGAMBIL DATA GURU LOGIN
    $query_guru = mysqli_query($koneksi, "
        SELECT id
        FROM t_guru
        WHERE user_id = '$id_user'
        AND status_aktif = 1
        LIMIT 1
    ");

    $data_guru = mysqli_fetch_assoc($query_guru);

    if ($data_guru) {

        $guru_id = (int) $data_guru['id'];

        // MENGAMBIL TAHUN AJARAN AKTIF
        $query_tahun = mysqli_query($koneksi, "
            SELECT id
            FROM t_tahun_ajaran
            WHERE status_aktif = 1
            LIMIT 1
        ");

        $data_tahun = mysqli_fetch_assoc($query_tahun);

        if ($data_tahun) {

            $tahun_ajaran_id = (int) $data_tahun['id'];

            // CEK APAKAH GURU MENJADI WALI KELAS
            $query_wali = mysqli_query($koneksi, "
                SELECT id
                FROM t_wali_kelas
                WHERE guru_id = '$guru_id'
                AND tahun_ajaran_id = '$tahun_ajaran_id'
                AND status_aktif = 1
                LIMIT 1
            ");

            if ($query_wali) {
                $status_wali_kelas = mysqli_num_rows($query_wali) > 0;
            }
        }
    }
}

// MENGAMBIL DATA PELANGGARAN
$query = mysqli_query($koneksi, "
    SELECT
        ps.*,
        pk.nama AS nama_kategori
    FROM t_pelanggaran_siswa ps
    LEFT JOIN t_pelanggaran_kategori pk
        ON ps.pelanggaran_kategori_id = pk.id
    WHERE ps.status IN ('Pending', 'Belum', 'Diproses')
    ORDER BY ps.id DESC
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

    <title>Tindakan - Sistem Pelanggaran</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DATATABLES -->
    <link href="https://cdn.datatables.net/3.1.4/css/dataTables.dataTables.min.css" rel="stylesheet">

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

                <?php } else { ?>

                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a href="catat_pelanggaran.php" class="nav-link text-white">Catat Pelanggaran</a>
                    </li>

                    <li class="nav-item">
                        <a href="tindakan.php"
                           class="nav-link <?= $halaman == 'tindakan.php' ? 'active' : 'text-white'; ?>">
                            Tindakan
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="riwayat.php" class="nav-link text-white">Riwayat</a>
                    </li>

                    <li class="nav-item">
                        <a href="rekap_poin.php" class="nav-link text-white">Rekap Poin</a>
                    </li>

                <?php } ?>

                <!-- LAPORAN KHUSUS ADMIN DAN WALI KELAS -->

                <?php if ($role == 'admin' || ($role == 'guru' && $status_wali_kelas)) { ?>

                    <li class="nav-item">
                        <a href="laporan.php"
                           class="nav-link <?= $halaman == 'laporan.php' ? 'active' : 'text-white'; ?>">
                            Laporan
                        </a>
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

        <!-- KONTEN -->
        <main class="content-main">
            <div class="page-content">

                <!-- HEADER SAMA SEPERTI RIWAYAT -->
                <div class="bg-white rounded shadow-sm p-4 mb-4">

                    <h2 class="fw-bold mb-2">
                        Tindakan
                    </h2>

                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($nama_login); ?></b>
                    </p>

                </div>

                <!-- CARD SAMA SEPERTI RIWAYAT -->
                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white p-3">

                        <h5 class="fw-bold mb-0">
                            Daftar Pelanggaran
                        </h5>

                    </div>

                    <div class="card-body p-4">

                        <div class="table-responsive">

                            <table id="tabelTindakan"
                                   class="table table-bordered table-hover align-middle">

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
                                        <th>Aksi</th>
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
                                            <?php if (($data['status'] ?? '') == 'Selesai') { ?>

                                                <span class="badge bg-success">Selesai</span>

                                            <?php } elseif (($data['status'] ?? '') == 'Diproses') { ?>

                                                <span class="badge bg-warning text-dark">Diproses</span>

                                            <?php } else { ?>

                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($data['status'] ?? 'Pending'); ?>
                                                </span>

                                            <?php } ?>
                                        </td>

                                        <td>
                                            <a href="beri_tindakan.php?id=<?= urlencode($data['id']); ?>"
                                               class="btn btn-primary btn-sm">
                                                Beri Tindakan
                                            </a>
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

<script>
document.addEventListener('DOMContentLoaded', function () {

    new DataTable('#tabelTindakan', {

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
