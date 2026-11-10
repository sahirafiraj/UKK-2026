
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));
$id_user = $_SESSION['id'] ?? 0;
$name = $_SESSION['name'] ?? 'Pengguna';

if (!in_array($role, ['admin', 'guru'])) {
    echo "Anda tidak memiliki akses";
    exit;
}

// ========================================
// TAHUN AJARAN AKTIF
// ========================================

$query_tahun = mysqli_query($koneksi, "
    SELECT id
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    LIMIT 1
");

$data_tahun = mysqli_fetch_assoc($query_tahun);
$id_tahun = $data_tahun['id'] ?? 0;

// ========================================
// DATA GURU DAN KELAS WALI
// ========================================

$id_guru = 0;
$kelas_wali = [];
$id_kelas_wali = [];

if ($role == 'guru') {

    $query_guru = mysqli_query($koneksi, "
        SELECT id
        FROM t_guru
        WHERE user_id = '$id_user'
        LIMIT 1
    ");

    $data_guru = mysqli_fetch_assoc($query_guru);
    $id_guru = $data_guru['id'] ?? 0;

    if ($id_guru > 0 && $id_tahun > 0) {

        $query_wali = mysqli_query($koneksi, "
            SELECT DISTINCT k.id, k.nama AS nama_kelas
            FROM t_wali_kelas wk
            JOIN t_kelas k ON k.id = wk.kelas_id
            WHERE wk.guru_id = '$id_guru'
            AND wk.tahun_ajaran_id = '$id_tahun'
            AND wk.status_aktif = 1
        ");

        while ($data_wali = mysqli_fetch_assoc($query_wali)) {
            $kelas_wali[] = $data_wali;
            $id_kelas_wali[] = $data_wali['id'];
        }
    }
}

// ========================================
// FILTER TANGGAL
// ========================================

$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

$filter_tanggal = "";

if ($tanggal_awal != '') {
    $filter_tanggal .= " AND ps.tanggal >= '$tanggal_awal'";
}

if ($tanggal_akhir != '') {
    $filter_tanggal .= " AND ps.tanggal <= '$tanggal_akhir'";
}

// ========================================
// FILTER KELAS WALI
// ========================================

$filter_kelas = "";

if ($role == 'guru') {

    if (count($id_kelas_wali) > 0) {

        $daftar_kelas = implode(',', $id_kelas_wali);

        $filter_kelas = "
            AND ps.kelas_id IN ($daftar_kelas)
        ";

    } else {
        $filter_kelas = " AND 1 = 0";
    }
}

// ========================================
// REKAPITULASI SISWA PELANGGAR
// ========================================

$query_rekap = mysqli_query($koneksi, "
    SELECT
        s.id,
        s.nis,
        s.nama,
        k.nama AS nama_kelas,
        COUNT(ps.id) AS jumlah_pelanggaran,
        COALESCE(SUM(p.poin), 0) AS total_poin
    FROM t_pelanggaran_siswa ps
    JOIN t_siswa s ON s.id = ps.siswa_id
    LEFT JOIN t_kelas k ON k.id = ps.kelas_id
    JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
    WHERE 1 = 1
    $filter_tanggal
    $filter_kelas
    GROUP BY s.id, s.nis, s.nama, k.nama
    ORDER BY jumlah_pelanggaran DESC, s.nama ASC
");

// ========================================
// DETAIL LAPORAN SISWA
// ========================================

$siswa_id = $_GET['siswa_id'] ?? '';
$data_siswa = null;
$data_detail = [];
$total_poin = 0;
$jumlah_detail = 0;

if ($siswa_id != '') {

    $siswa_id = mysqli_real_escape_string($koneksi, $siswa_id);

    $filter_siswa_wali = "";

    if ($role == 'guru') {

        if (count($id_kelas_wali) > 0) {

            $daftar_kelas = implode(',', $id_kelas_wali);

            $filter_siswa_wali = "
                AND ps.kelas_id IN ($daftar_kelas)
            ";

        } else {
            $filter_siswa_wali = " AND 1 = 0";
        }
    }

    // DATA IDENTITAS SISWA
    $query_siswa = mysqli_query($koneksi, "
        SELECT
            s.id,
            s.nis,
            s.nama
        FROM t_siswa s
        WHERE s.id = '$siswa_id'
        LIMIT 1
    ");

    $data_siswa = mysqli_fetch_assoc($query_siswa);

    // DETAIL PELANGGARAN
    if ($data_siswa) {

        $query_detail = mysqli_query($koneksi, "
            SELECT
                ps.id,
                ps.tanggal,
                k.nama AS nama_kelas,
                p.nama AS nama_pelanggaran,
                p.poin,
                g.nama AS nama_guru,
                ps.keterangan,
                ps.tindakan,
                ps.status
            FROM t_pelanggaran_siswa ps
            JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
            LEFT JOIN t_kelas k ON k.id = ps.kelas_id
            LEFT JOIN t_guru g ON g.id = ps.guru_id
            WHERE ps.siswa_id = '$siswa_id'
            $filter_tanggal
            $filter_siswa_wali
            ORDER BY ps.tanggal ASC, ps.id ASC
        ");

        while ($data = mysqli_fetch_assoc($query_detail)) {
            $data_detail[] = $data;
            $total_poin += $data['poin'];
            $jumlah_detail++;
        }

        // GURU TIDAK BOLEH MELIHAT SISWA DI LUAR KELAS WALI
        if ($role == 'guru' && $jumlah_detail == 0) {
            $data_siswa = null;
        }
    }
}

// ========================================
// EXPORT EXCEL
// ========================================

if (isset($_GET['export']) && $_GET['export'] == 'excel' && $data_siswa && $jumlah_detail > 0) {

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Pelanggaran_Siswa.xls");

    echo "<table border='1'>";

    echo "<tr><th colspan='7'>LAPORAN PELANGGARAN SISWA</th></tr>";
    echo "<tr><td colspan='7'>Nama Siswa: " . htmlspecialchars($data_siswa['nama']) . "</td></tr>";
    echo "<tr><td colspan='7'>NIS: " . htmlspecialchars($data_siswa['nis']) . "</td></tr>";

    echo "<tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Kelas</th>
        <th>Jenis Pelanggaran</th>
        <th>Poin</th>
        <th>Guru</th>
        <th>Status</th>
    </tr>";

    $no = 1;

    foreach ($data_detail as $detail) {

        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($detail['tanggal']) . "</td>";
        echo "<td>" . htmlspecialchars($detail['nama_kelas'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($detail['nama_pelanggaran']) . "</td>";
        echo "<td>" . htmlspecialchars($detail['poin']) . "</td>";
        echo "<td>" . htmlspecialchars($detail['nama_guru'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($detail['status'] ?? '-') . "</td>";
        echo "</tr>";
    }

    echo "<tr>
        <td colspan='4'><b>Total Poin</b></td>
        <td><b>$total_poin</b></td>
        <td colspan='2'></td>
    </tr>";

    echo "</table>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan - Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <?php if ($role == 'admin') { ?>

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
                        <a href="catat_pelanggaran.php" class="nav-link text-white">Catat Pelanggaran</a>
                    </li>

                    <li class="nav-item">
                        <a href="tindakan.php" class="nav-link text-white">Tindakan</a>
                    </li>

                    <li class="nav-item">
                        <a href="riwayat.php" class="nav-link text-white">Riwayat</a>
                    </li>

                    <li class="nav-item">
                        <a href="rekap_poin.php" class="nav-link text-white">Rekap Poin</a>
                    </li>

                <?php } ?>

                <li class="nav-item">
                    <a href="laporan.php" class="nav-link active">Laporan</a>
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

        <!-- KONTEN LAPORAN -->
        <main class="content-main">

            <div class="dashboard-content">

                <!-- HEADER -->
                <div class="dashboard-header">

                    <h2>Laporan Pelanggaran Siswa</h2>

                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($name); ?></b>
                    </p>

                    <?php if ($role == 'guru' && count($kelas_wali) > 0) { ?>

                        <p>
                            Kelas Wali:
                            <b>
                                <?php
                                $nama_kelas = [];

                                foreach ($kelas_wali as $kelas) {
                                    $nama_kelas[] = $kelas['nama_kelas'];
                                }

                                echo htmlspecialchars(implode(', ', $nama_kelas));
                                ?>
                            </b>
                        </p>

                    <?php } ?>

                </div>

                <!-- FILTER TANGGAL -->
                <div class="card-data mt-4">

                    <div class="card-header">
                        <div class="card-header-content">
                            <h5 class="card-title">Filter Laporan</h5>
                        </div>
                    </div>

                    <div class="card-body">

                        <form method="GET" class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tanggal_awal" class="form-control"
                                    value="<?= htmlspecialchars($tanggal_awal); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control"
                                    value="<?= htmlspecialchars($tanggal_akhir); ?>">
                            </div>

                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    Tampilkan
                                </button>

                                <a href="laporan.php" class="btn btn-secondary">
                                    Reset
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

                <!-- REKAPITULASI -->
                <div class="card-data mt-4">

                    <div class="card-header">
                        <div class="card-header-content">
                            <h5 class="card-title">Rekapitulasi Pelanggaran Siswa</h5>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped table-hover">

                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jumlah Pelanggaran</th>
                                        <th>Total Poin</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                <?php
                                $no = 1;

                                if (mysqli_num_rows($query_rekap) > 0) {

                                    while ($rekap = mysqli_fetch_assoc($query_rekap)) {
                                ?>

                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($rekap['nis']); ?></td>
                                        <td><?= htmlspecialchars($rekap['nama']); ?></td>
                                        <td><?= htmlspecialchars($rekap['nama_kelas'] ?? '-'); ?></td>
                                        <td><?= $rekap['jumlah_pelanggaran']; ?></td>
                                        <td><?= $rekap['total_poin']; ?></td>
                                        <td>
                                            <a href="laporan.php?siswa_id=<?= $rekap['id']; ?>&tanggal_awal=<?= urlencode($tanggal_awal); ?>&tanggal_akhir=<?= urlencode($tanggal_akhir); ?>"
                                                class="btn btn-primary btn-sm">
                                                Lihat Laporan
                                            </a>
                                        </td>
                                    </tr>

                                <?php
                                    }

                                } else {
                                ?>

                                    <tr>
                                        <td colspan="7" class="text-center">
                                            Tidak ada data pelanggaran siswa.
                                        </td>
                                    </tr>

                                <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>

                <!-- DETAIL LAPORAN -->
                <?php if ($data_siswa && $jumlah_detail > 0) { ?>

                <div class="card-data mt-4" id="detail-laporan">

                    <div class="card-header">
                        <div class="card-header-content">

                            <h5 class="card-title">Detail Laporan Pelanggaran</h5>

                            <div class="card-actions">

                                <button type="button" onclick="window.print()" class="btn btn-primary">
                                    Print
                                </button>

                                <a href="laporan.php?siswa_id=<?= $data_siswa['id']; ?>&tanggal_awal=<?= urlencode($tanggal_awal); ?>&tanggal_akhir=<?= urlencode($tanggal_akhir); ?>&export=excel"
                                    class="btn btn-success">
                                    Export Excel
                                </a>

                            </div>

                        </div>
                    </div>

                    <div class="card-body">

                        <!-- IDENTITAS SISWA -->
                        <div class="identitas-laporan">

                            <h4 class="text-center mb-3">LAPORAN PELANGGARAN SISWA</h4>

                            <table class="table table-borderless">

                                <tr>
                                    <td width="150">Nama Siswa</td>
                                    <td>: <?= htmlspecialchars($data_siswa['nama']); ?></td>
                                </tr>

                                <tr>
                                    <td>NIS</td>
                                    <td>: <?= htmlspecialchars($data_siswa['nis']); ?></td>
                                </tr>

                                <tr>
                                    <td>Tanggal Laporan</td>
                                    <td>: <?= date('d-m-Y'); ?></td>
                                </tr>

                            </table>

                        </div>

                        <!-- TABEL DETAIL -->
                        <div class="table-responsive">

                            <table class="table table-bordered table-striped">

                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kelas</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Poin</th>
                                        <th>Guru</th>
                                        <th>Keterangan</th>
                                        <th>Tindakan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                <?php
                                $no = 1;

                                foreach ($data_detail as $detail) {
                                ?>

                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= date('d-m-Y', strtotime($detail['tanggal'])); ?></td>
                                        <td><?= htmlspecialchars($detail['nama_kelas'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($detail['nama_pelanggaran']); ?></td>
                                        <td><?= htmlspecialchars($detail['poin']); ?></td>
                                        <td><?= htmlspecialchars($detail['nama_guru'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($detail['keterangan'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($detail['tindakan'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($detail['status'] ?? '-'); ?></td>
                                    </tr>

                                <?php } ?>

                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Total Poin</th>
                                        <th><?= $total_poin; ?></th>
                                        <th colspan="4"></th>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                        <!-- TANDA TANGAN -->
                        <div class="tanda-tangan-laporan">

                            <p class="text-end">
                                Tasikmalaya, <?= date('d F Y'); ?>
                            </p>

                            <div class="row text-center mt-4">

                                <div class="col-md-4">
                                    <p>Siswa</p>
                                    <br><br><br>
                                    <p><b><?= htmlspecialchars($data_siswa['nama']); ?></b></p>
                                </div>

                                <div class="col-md-4">
                                    <p>Orang Tua</p>
                                    <br><br><br>
                                    <p><b>(................................)</b></p>
                                </div>

                                <div class="col-md-4">
                                    <p>Wali Kelas</p>
                                    <br><br><br>
                                    <p><b>(................................)</b></p>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <?php } ?>

            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
