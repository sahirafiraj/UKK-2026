
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// CEK LOGIN DAN HAK AKSES
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Pengguna';
$role = strtolower(trim($_SESSION['role'] ?? ''));
$id_user = (int) ($_SESSION['id'] ?? 0);
$halaman = basename($_SERVER['PHP_SELF']);

if ($role != 'admin' && $role != 'guru') {
    echo "Anda tidak memiliki akses";
    exit;
}

// CEK STATUS WALI KELAS
$status_wali_kelas = false;
<<<<<<< HEAD

if ($role == 'guru') {

    $query_guru = mysqli_query($koneksi, "
        SELECT id
        FROM t_guru
        WHERE user_id = '$id_user'
        AND status_aktif = 1
        LIMIT 1
    ");

    if ($query_guru && mysqli_num_rows($query_guru) > 0) {

        $data_guru = mysqli_fetch_assoc($query_guru);
        $guru_id = $data_guru['id'];

        // CEK TAHUN AJARAN AKTIF
        $query_tahun_ajaran = mysqli_query($koneksi, "
            SELECT id
            FROM t_tahun_ajaran
            WHERE status_aktif = 1
            LIMIT 1
        ");

        if ($query_tahun_ajaran && mysqli_num_rows($query_tahun_ajaran) > 0) {

            $data_tahun_ajaran = mysqli_fetch_assoc($query_tahun_ajaran);
            $tahun_ajaran_id = $data_tahun_ajaran['id'];

            // CEK APAKAH GURU MERUPAKAN WALI KELAS
            $query_wali_kelas = mysqli_query($koneksi, "
                SELECT id
                FROM t_wali_kelas
                WHERE guru_id = '$guru_id'
                AND tahun_ajaran_id = '$tahun_ajaran_id'
                AND status_aktif = 1
                LIMIT 1
            ");

            if ($query_wali_kelas && mysqli_num_rows($query_wali_kelas) > 0) {
                $status_wali_kelas = true;
            }
        }
    }
}

// ADMIN: SEMUA PELANGGARAN SELESAI
if ($role == 'admin') {
=======
>>>>>>> b21012ec88879e0955064b556fa42916a769c862

if ($role == 'guru') {

    $query_guru = mysqli_query($koneksi, "
        SELECT id
        FROM t_guru
        WHERE user_id = '$id_user'
        AND status_aktif = 1
        LIMIT 1
    ");

    if ($query_guru && mysqli_num_rows($query_guru) > 0) {

<<<<<<< HEAD
    // GURU: PELANGGARAN MILIK GURU LOGIN
    $nama_guru_login = mysqli_real_escape_string($koneksi, $nama_login);
=======
        $data_guru = mysqli_fetch_assoc($query_guru);
        $guru_id = $data_guru['id'];
>>>>>>> b21012ec88879e0955064b556fa42916a769c862

        // CEK TAHUN AJARAN AKTIF
        $query_tahun_ajaran = mysqli_query($koneksi, "
            SELECT id
            FROM t_tahun_ajaran
            WHERE status_aktif = 1
            LIMIT 1
        ");

        if ($query_tahun_ajaran && mysqli_num_rows($query_tahun_ajaran) > 0) {

            $data_tahun_ajaran = mysqli_fetch_assoc($query_tahun_ajaran);
            $tahun_ajaran_id = $data_tahun_ajaran['id'];

            // CEK APAKAH GURU MERUPAKAN WALI KELAS
            $query_wali_kelas = mysqli_query($koneksi, "
                SELECT id
                FROM t_wali_kelas
                WHERE guru_id = '$guru_id'
                AND tahun_ajaran_id = '$tahun_ajaran_id'
                AND status_aktif = 1
                LIMIT 1
            ");

            if ($query_wali_kelas && mysqli_num_rows($query_wali_kelas) > 0) {
                $status_wali_kelas = true;
            }
        }
    }
}

// QUERY REKAP POIN
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

            <h4 class="text-white mb-4">
                Sistem Pelanggaran
            </h4>

            <ul class="nav nav-pills flex-column gap-1">

                <?php if ($role == 'admin') { ?>

                    <!-- MENU ADMIN -->
                    <li class="nav-item">
                        <a href="dashboard.php"
                           class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : 'text-white'; ?>">
                            Dashboard
                        </a>
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

                    <!-- LAPORAN ADMIN -->
                    <li class="nav-item">
                        <a href="laporan.php"
                           class="nav-link <?= $halaman == 'laporan.php' ? 'active' : 'text-white'; ?>">
                            Laporan
                        </a>
                    </li>

                <?php } else { ?>

                    <!-- MENU GURU -->
                    <li class="nav-item">
                        <a href="dashboard.php"
                           class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : 'text-white'; ?>">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="catat_pelanggaran.php" class="nav-link text-white">Catat Pelanggaran</a>
                    </li>

                    <li class="nav-item">
                        <a href="tindakan.php" class="nav-link text-white">Tindakan</a>
                    </li>

                    <li class="nav-item">
<<<<<<< HEAD
                        <a href="riwayat.php"
                           class="nav-link <?= $halaman == 'riwayat.php' ? 'active' : 'text-white'; ?>">
                            Riwayat
                        </a>
=======
                        <a href="riwayat.php" class="nav-link text-white">Riwayat</a>
>>>>>>> b21012ec88879e0955064b556fa42916a769c862
                    </li>

                    <li class="nav-item">
                        <a href="rekap_poin.php"
                           class="nav-link <?= $halaman == 'rekap_poin.php' ? 'active' : 'text-white'; ?>">
                            Rekap Poin
                        </a>
                    </li>

                    <!-- LAPORAN KHUSUS WALI KELAS -->
                    <?php if ($status_wali_kelas) { ?>
                    <li class="nav-item">
                        <a href="laporan.php"
                           class="nav-link <?= $halaman == 'laporan.php' ? 'active' : 'text-white'; ?>">
                            Laporan
                        </a>
                    </li>
                    <?php } ?>

                <?php } ?>

                <!-- ABOUT ME -->
                <li class="nav-item">
                    <a href="about_me.php"
                       class="nav-link <?= $halaman == 'about_me.php' ? 'active' : 'text-white'; ?>">
                        About Me
                    </a>
                </li>

                <hr class="border-white">

                <!-- LOGOUT -->
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

                    <h2 class="fw-bold mb-2">
<<<<<<< HEAD
                        Riwayat Pelanggaran
=======
                        Rekap Poin Pelanggaran
>>>>>>> b21012ec88879e0955064b556fa42916a769c862
                    </h2>

                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($nama_login); ?></b>
                    </p>

                </div>

                <!-- CARD REKAP -->
                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white p-3">
<<<<<<< HEAD

                        <h5 class="fw-bold mb-0">
                            Daftar Pelanggaran
                        </h5>

                    </div>

                    <div class="card-body p-4">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover align-middle"
                                   id="tabelRiwayat">
=======

                        <h5 class="fw-bold mb-0">
                            Data Rekap Poin Siswa
                        </h5>

                    </div>

                    <div class="card-body p-4">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover align-middle"
                                   id="tabelRekapPoin">
>>>>>>> b21012ec88879e0955064b556fa42916a769c862

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

                                    if (mysqli_num_rows($query) > 0) {
                                        while ($data = mysqli_fetch_assoc($query)) {
                                    ?>

<<<<<<< HEAD
                                    <tr>

                                        <td><?= $no++; ?></td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_kategori'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['tanggal'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['poin'] ?? '0'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['keterangan'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['tindakan'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <span class="badge bg-success">
                                                Selesai
                                            </span>
                                        </td>

                                    </tr>
=======
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
>>>>>>> b21012ec88879e0955064b556fa42916a769c862

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

    new DataTable('#tabelRekapPoin', {

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
            emptyTable: 'Belum ada data rekap poin',

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
