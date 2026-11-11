<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));
$id_user = (int) ($_SESSION['id'] ?? 0);
$nama_login = $_SESSION['name'] ?? 'Guru';
$halaman = basename($_SERVER['PHP_SELF']);

if (!in_array($role, ['admin', 'guru'])) {
    echo "Anda tidak memiliki akses";
    exit;
}

// TAHUN AJARAN AKTIF

$query_tahun = mysqli_query($koneksi, "
    SELECT id
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    LIMIT 1
");

$data_tahun = mysqli_fetch_assoc($query_tahun);
$id_tahun = $data_tahun['id'] ?? 0;

// DATA KELAS WALI

$kelas_wali = [];
$id_kelas_wali = [];
$id_guru = 0;
$daftar_kelas = '';

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

    if (count($id_kelas_wali) == 0) {
        echo "<script>
            alert('Akses ditolak! Anda bukan wali kelas pada tahun ajaran aktif.');
            window.location.href = 'dashboard.php';
        </script>";
        exit;
    }

    $daftar_kelas = implode(',', array_map('intval', $id_kelas_wali));
}

// NAMA KELAS WALI

$nama_kelas_wali = [];

foreach ($kelas_wali as $kelas) {
    $nama_kelas_wali[] = $kelas['nama_kelas'];
}

$kelas_wali_text = implode(', ', $nama_kelas_wali);

// FILTER KELAS GURU

$filter_kelas = "";

if ($role == 'guru') {
    $filter_kelas = " AND ps.kelas_id IN ($daftar_kelas)";
}

// PENCARIAN OTOMATIS SISWA

if (isset($_GET['ajax']) && $_GET['ajax'] == 'cari_siswa') {

    header('Content-Type: application/json; charset=utf-8');

    $kata_kunci = trim($_GET['q'] ?? '');
    $hasil = [];

    if ($kata_kunci != '') {

        $cari_aman = mysqli_real_escape_string($koneksi, $kata_kunci);

        $query_ajax = mysqli_query($koneksi, "
            SELECT DISTINCT
                s.id,
                s.nama,
                s.nis
            FROM t_siswa s
            WHERE (
                s.nama LIKE '$cari_aman%'
                OR s.nis LIKE '$cari_aman%'
            )
            " . ($role == 'guru' ? "
                AND EXISTS (
                    SELECT 1
                    FROM t_pelanggaran_siswa ps
                    WHERE ps.siswa_id = s.id
                    AND ps.kelas_id IN ($daftar_kelas)
                )
            " : "") . "
            ORDER BY s.nama ASC
        ");

        if ($query_ajax) {
            while ($siswa = mysqli_fetch_assoc($query_ajax)) {
                $hasil[] = $siswa;
            }
        }
    }

    echo json_encode($hasil);
    exit;
}

// FILTER LAPORAN

$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
$cari_siswa = trim($_GET['cari_siswa'] ?? '');

$filter_tanggal = "";
$filter_siswa = "";

if ($tanggal_awal != '') {
    $tanggal_awal = mysqli_real_escape_string($koneksi, $tanggal_awal);
    $filter_tanggal .= " AND ps.tanggal >= '$tanggal_awal'";
}

if ($tanggal_akhir != '') {
    $tanggal_akhir = mysqli_real_escape_string($koneksi, $tanggal_akhir);
    $filter_tanggal .= " AND ps.tanggal <= '$tanggal_akhir'";
}

if ($cari_siswa != '') {
    $cari_aman = mysqli_real_escape_string($koneksi, $cari_siswa);

    $filter_siswa = "
        AND (
            s.nama LIKE '%$cari_aman%'
            OR s.nis LIKE '%$cari_aman%'
        )
    ";
}

// DATA REKAP SISWA

$query_rekap = mysqli_query($koneksi, "
    SELECT
        s.id,
        s.nis,
        s.nama,
        MAX(ps.tanggal) AS tanggal,
        GROUP_CONCAT(
            DISTINCT k.nama
            ORDER BY k.nama
            SEPARATOR ', '
        ) AS nama_kelas,
        COUNT(ps.id) AS jumlah_pelanggaran,
        COALESCE(SUM(p.poin), 0) AS total_poin
    FROM t_pelanggaran_siswa ps
    JOIN t_siswa s ON s.id = ps.siswa_id
    LEFT JOIN t_kelas k ON k.id = ps.kelas_id
    JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
    WHERE 1 = 1
    $filter_tanggal
    $filter_kelas
    $filter_siswa
    GROUP BY s.id, s.nis, s.nama
    ORDER BY jumlah_pelanggaran DESC, s.nama ASC
");

// DATA DETAIL SISWA

$siswa_id = $_GET['siswa_id'] ?? '';

$data_siswa = null;
$data_detail = [];
$total_poin = 0;
$jumlah_detail = 0;

if ($siswa_id != '') {

    $siswa_id = (int) $siswa_id;

    $query_siswa = mysqli_query($koneksi, "
        SELECT id, nis, nama
        FROM t_siswa
        WHERE id = '$siswa_id'
        LIMIT 1
    ");

    $data_siswa = mysqli_fetch_assoc($query_siswa);

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
            $filter_kelas
            ORDER BY ps.tanggal ASC, ps.id ASC
        ");

        while ($data = mysqli_fetch_assoc($query_detail)) {
            $data_detail[] = $data;
            $total_poin += $data['poin'];
            $jumlah_detail++;
        }

        if ($jumlah_detail == 0) {
            $data_siswa = null;
        }
    }
}

// DATA SELURUH PELANGGARAN

$query_semua = mysqli_query($koneksi, "
    SELECT
        ps.id,
        ps.tanggal,
        s.nis,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas,
        p.nama AS nama_pelanggaran,
        p.poin,
        g.nama AS nama_guru,
        ps.keterangan,
        ps.tindakan,
        ps.status
    FROM t_pelanggaran_siswa ps
    JOIN t_siswa s ON s.id = ps.siswa_id
    JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
    LEFT JOIN t_kelas k ON k.id = ps.kelas_id
    LEFT JOIN t_guru g ON g.id = ps.guru_id
    WHERE 1 = 1
    $filter_tanggal
    $filter_kelas
    $filter_siswa
    ORDER BY ps.tanggal DESC, ps.id DESC
");

$data_semua = [];
$total_poin_semua = 0;
$jumlah_semua = 0;

while ($data = mysqli_fetch_assoc($query_semua)) {
    $data_semua[] = $data;
    $total_poin_semua += $data['poin'];
    $jumlah_semua++;
}

$jumlah_siswa_rekap = mysqli_num_rows($query_rekap);

// PARAMETER FILTER

$parameter_filter = "&tanggal_awal=" . urlencode($tanggal_awal)
    . "&tanggal_akhir=" . urlencode($tanggal_akhir)
    . "&cari_siswa=" . urlencode($cari_siswa);

// EXPORT EXCEL

if (isset($_GET['export']) && $_GET['export'] == 'excel') {

    $jenis_export = $_GET['jenis'] ?? 'semua';

    header("Content-Type: application/vnd.ms-excel; charset=utf-8");

    if ($jenis_export == 'individu' && $data_siswa) {

        header("Content-Disposition: attachment; filename=Laporan_Individu.xls");

        echo "<html><head><meta charset='UTF-8'></head><body>";
        echo "<h2>SMK MUHAMMADIYAH TASIKMALAYA</h2>";
        echo "<h3>LAPORAN PELANGGARAN SISWA</h3>";
        echo "<p>Nama: " . htmlspecialchars($data_siswa['nama']) . "</p>";
        echo "<p>NIS: " . htmlspecialchars($data_siswa['nis']) . "</p>";

        echo "<table border='1'>";
        echo "<tr>
            <th>No</th><th>Tanggal</th><th>Kelas</th>
            <th>Jenis Pelanggaran</th><th>Poin</th>
            <th>Guru</th><th>Keterangan</th>
            <th>Tindakan</th><th>Status</th>
        </tr>";

        $no = 1;

        foreach ($data_detail as $detail) {
            echo "<tr>
                <td>" . $no++ . "</td>
                <td>" . htmlspecialchars($detail['tanggal']) . "</td>
                <td>" . htmlspecialchars($detail['nama_kelas'] ?? '-') . "</td>
                <td>" . htmlspecialchars($detail['nama_pelanggaran']) . "</td>
                <td>" . $detail['poin'] . "</td>
                <td>" . htmlspecialchars($detail['nama_guru'] ?? '-') . "</td>
                <td>" . htmlspecialchars($detail['keterangan'] ?? '-') . "</td>
                <td>" . htmlspecialchars($detail['tindakan'] ?? '-') . "</td>
                <td>" . htmlspecialchars($detail['status'] ?? '-') . "</td>
            </tr>";
        }

        echo "<tr>
            <th colspan='4'>TOTAL POIN</th>
            <th>$total_poin</th>
            <th colspan='4'></th>
        </tr>";

        echo "</table></body></html>";
        exit;
    }

    header("Content-Disposition: attachment; filename=Rekap_Pelanggaran_Siswa.xls");

    echo "<html><head><meta charset='UTF-8'></head><body>";
    echo "<h2>SMK MUHAMMADIYAH TASIKMALAYA</h2>";
    echo "<h3>REKAPITULASI PELANGGARAN SISWA</h3>";

    echo "<table border='1'>";
    echo "<tr>
        <th>No</th><th>Tanggal</th><th>NIS</th>
        <th>Nama Siswa</th><th>Kelas</th>
        <th>Jenis Pelanggaran</th><th>Poin</th>
        <th>Guru</th><th>Keterangan</th>
        <th>Tindakan</th><th>Status</th>
    </tr>";

    $no = 1;

    foreach ($data_semua as $detail) {
        echo "<tr>
            <td>" . $no++ . "</td>
            <td>" . htmlspecialchars($detail['tanggal']) . "</td>
            <td>" . htmlspecialchars($detail['nis']) . "</td>
            <td>" . htmlspecialchars($detail['nama_siswa']) . "</td>
            <td>" . htmlspecialchars($detail['nama_kelas'] ?? '-') . "</td>
            <td>" . htmlspecialchars($detail['nama_pelanggaran']) . "</td>
            <td>" . $detail['poin'] . "</td>
            <td>" . htmlspecialchars($detail['nama_guru'] ?? '-') . "</td>
            <td>" . htmlspecialchars($detail['keterangan'] ?? '-') . "</td>
            <td>" . htmlspecialchars($detail['tindakan'] ?? '-') . "</td>
            <td>" . htmlspecialchars($detail['status'] ?? '-') . "</td>
        </tr>";
    }

    echo "<tr>
        <th colspan='6'>TOTAL POIN</th>
        <th>$total_poin_semua</th>
        <th colspan='4'></th>
    </tr>";

    echo "</table></body></html>";
    exit;
}

// MODE CETAK

if (isset($_GET['cetak'])) {

    $jenis_cetak = $_GET['cetak'];

    $bulan_indonesia = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    $tanggal_sekarang = date('d') . ' ' .
        $bulan_indonesia[(int) date('n')] . ' ' .
        date('Y');

    $periode_awal = $tanggal_awal != ''
        ? date('d-m-Y', strtotime($tanggal_awal))
        : 'Semua';

    $periode_akhir = $tanggal_akhir != ''
        ? date('d-m-Y', strtotime($tanggal_akhir))
        : 'Semua';

    if ($jenis_cetak == 'individu' && $data_siswa) {
        $judul_cetak = 'LAPORAN PELANGGARAN SISWA';
        $kelas_cetak = 'print-individu';
    } else {
        $judul_cetak = 'REKAPITULASI PELANGGARAN SISWA';
        $kelas_cetak = 'print-semua';
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul_cetak; ?></title>
    <link rel="stylesheet" href="style/style.css">
</head>

<body class="<?= $kelas_cetak; ?> halaman-cetak">

    <div class="header-cetak">
        <h2>SMK MUHAMMADIYAH TASIKMALAYA</h2>
        <h3><?= $judul_cetak; ?></h3>
        <p>SISTEM INFORMASI PELANGGARAN SISWA</p>
    </div>

    <?php if ($jenis_cetak == 'individu' && $data_siswa) { ?>

        <table class="identitas">
            <tr>
                <td width="15%">Nama Siswa</td>
                <td width="35%">: <?= htmlspecialchars($data_siswa['nama']); ?></td>
                <td width="15%">NIS</td>
                <td>: <?= htmlspecialchars($data_siswa['nis']); ?></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: <?= htmlspecialchars($data_detail[0]['nama_kelas'] ?? '-'); ?></td>
                <td>Tanggal</td>
                <td>: <?= date('d-m-Y'); ?></td>
            </tr>
        </table>

        <table class="data">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Tanggal</th>
                    <th width="12%">Kelas</th>
                    <th>Jenis Pelanggaran</th>
                    <th width="7%">Poin</th>
                    <th>Guru</th>
                    <th>Keterangan</th>
                    <th>Tindakan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($data_detail as $detail) { ?>
                    <tr>
                        <td align="center"><?= $no++; ?></td>
                        <td><?= date('d-m-Y', strtotime($detail['tanggal'])); ?></td>
                        <td><?= htmlspecialchars($detail['nama_kelas'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($detail['nama_pelanggaran']); ?></td>
                        <td align="center"><?= $detail['poin']; ?></td>
                        <td><?= htmlspecialchars($detail['nama_guru'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($detail['keterangan'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($detail['tindakan'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($detail['status'] ?? '-'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align: right;">TOTAL POIN</th>
                    <th><?= $total_poin; ?></th>
                    <th colspan="4"></th>
                </tr>
            </tfoot>
        </table>

        <div class="tanggal-cetak">
            Tasikmalaya, <?= $tanggal_sekarang; ?>
        </div>

        <table class="ttd-individu">
            <tr>
                <td>
                    Siswa
                    <div class="garis-bawah"></div>
                    <b><?= htmlspecialchars($data_siswa['nama']); ?></b>
                </td>
                <td>
                    Orang Tua
                    <div class="garis-bawah"></div>
                    (................................)
                </td>
                <td>
                    Wali Kelas
                    <div class="garis-bawah"></div>
                    (................................)
                </td>
            </tr>
        </table>

    <?php } else { ?>

        <div class="periode">
            <b>Periode:</b> <?= $periode_awal; ?> s.d. <?= $periode_akhir; ?>
            <?php if ($cari_siswa != '') { ?>
                <br>
                <b>Pencarian Siswa:</b> <?= htmlspecialchars($cari_siswa); ?>
            <?php } ?>
        </div>

        <table class="data">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="9%">Tanggal</th>
                    <th width="9%">NIS</th>
                    <th width="13%">Nama Siswa</th>
                    <th width="9%">Kelas</th>
                    <th width="19%">Jenis Pelanggaran</th>
                    <th width="6%">Poin</th>
                    <th width="14%">Guru</th>
                    <th width="9%">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($data_semua as $detail) { ?>
                    <tr>
                        <td align="center"><?= $no++; ?></td>
                        <td><?= date('d-m-Y', strtotime($detail['tanggal'])); ?></td>
                        <td><?= htmlspecialchars($detail['nis']); ?></td>
                        <td><?= htmlspecialchars($detail['nama_siswa']); ?></td>
                        <td><?= htmlspecialchars($detail['nama_kelas'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($detail['nama_pelanggaran']); ?></td>
                        <td align="center"><?= $detail['poin']; ?></td>
                        <td><?= htmlspecialchars($detail['nama_guru'] ?? '-'); ?></td>
                        <td align="center"><?= htmlspecialchars($detail['status'] ?? '-'); ?></td>
                    </tr>
                <?php } ?>

                <?php if ($jumlah_semua == 0) { ?>
                    <tr>
                        <td colspan="9" align="center">Tidak ada data pelanggaran siswa.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="ringkasan">
            <div class="ringkasan-item">
                <h4>Jumlah Kejadian</h4>
                <h2><?= $jumlah_semua; ?></h2>
            </div>
            <div class="ringkasan-item">
                <h4>Total Poin</h4>
                <h2><?= $total_poin_semua; ?></h2>
            </div>
        </div>

        <div class="bagian-bawah">
            <div class="dicetak-oleh">
                Dicetak oleh:<br>
                <b>Sistem Pelanggaran Siswa</b>
            </div>

            <div class="tanggal-cetak">
                Tasikmalaya, <?= $tanggal_sekarang; ?><br>
                Mengetahui,<br>
                Kepala Sekolah
                <div class="garis-bawah"></div>
                (................................)
            </div>
        </div>

    <?php } ?>

</body>
</html>

<?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

            <h4 class="text-white mb-4">
                Sistem Pelanggaran
            </h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php"
                       class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : 'text-white'; ?>">
                        Dashboard
                    </a>
                </li>

                <?php if ($role == 'admin') { ?>

                    <li class="nav-item">
                        <a href="data_siswa.php"
                           class="nav-link <?= $halaman == 'data_siswa.php' ? 'active' : 'text-white'; ?>">
                            Data Siswa
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="data_guru.php"
                           class="nav-link <?= $halaman == 'data_guru.php' ? 'active' : 'text-white'; ?>">
                            Data Guru
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="data_kelas.php"
                           class="nav-link <?= $halaman == 'data_kelas.php' ? 'active' : 'text-white'; ?>">
                            Data Kelas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="data_tahun_ajaran.php"
                           class="nav-link <?= $halaman == 'data_tahun_ajaran.php' ? 'active' : 'text-white'; ?>">
                            Tahun Ajaran
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="data_penempatan_siswa.php"
                           class="nav-link <?= $halaman == 'data_penempatan_siswa.php' ? 'active' : 'text-white'; ?>">
                            Penempatan Siswa
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="data_wali_kelas.php"
                           class="nav-link <?= $halaman == 'data_wali_kelas.php' ? 'active' : 'text-white'; ?>">
                            Data Wali Kelas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="data_kategori_pelanggaran.php"
                           class="nav-link <?= $halaman == 'data_kategori_pelanggaran.php' ? 'active' : 'text-white'; ?>">
                            Kategori Pelanggaran
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="data_jenis_pelanggaran.php"
                           class="nav-link <?= $halaman == 'data_jenis_pelanggaran.php' ? 'active' : 'text-white'; ?>">
                            Jenis Pelanggaran
                        </a>
                    </li>

                <?php } else { ?>

                    <li class="nav-item">
                        <a href="catat_pelanggaran.php"
                           class="nav-link <?= $halaman == 'catat_pelanggaran.php' ? 'active' : 'text-white'; ?>">
                            Catat Pelanggaran
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="tindakan.php"
                           class="nav-link <?= $halaman == 'tindakan.php' ? 'active' : 'text-white'; ?>">
                            Tindakan
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="riwayat.php"
                           class="nav-link <?= $halaman == 'riwayat.php' ? 'active' : 'text-white'; ?>">
                            Riwayat
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="rekap_poin.php"
                           class="nav-link <?= $halaman == 'rekap_poin.php' ? 'active' : 'text-white'; ?>">
                            Rekap Poin
                        </a>
                    </li>

                <?php } ?>

                <li class="nav-item">
                    <a href="laporan.php"
                       class="nav-link <?= $halaman == 'laporan.php' ? 'active' : 'text-white'; ?>">
                        Laporan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php"
                       class="nav-link <?= $halaman == 'about_me.php' ? 'active' : 'text-white'; ?>">
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

        <main class="content-main">
            <div class="page-content">

                <div class="bg-white rounded shadow-sm p-4 mb-4">
                    <h2 class="fw-bold">
                        Laporan Pelanggaran Siswa
                    </h2>

                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($nama_login); ?></b>
                    </p>
                </div>

                <div class="card-data filter-card mb-4"
                     style="overflow: visible !important; position: relative; z-index: 10;">

                    <div class="card-header">
                        <div class="card-header-content">
                            <div class="card-title">
                                <h5>Filter Laporan</h5>
                                <p>Filter berdasarkan tanggal dan pencarian siswa</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" style="overflow: visible !important;">

                        <form method="GET" action="laporan.php"
                              class="row g-3 align-items-end">

                            <div class="col-md-3">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date"
                                       name="tanggal_awal"
                                       class="form-control"
                                       value="<?= htmlspecialchars($tanggal_awal); ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date"
                                       name="tanggal_akhir"
                                       class="form-control"
                                       value="<?= htmlspecialchars($tanggal_akhir); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Pencarian Siswa</label>

                                <div style="position: relative;">
                                    <input type="text"
                                           name="cari_siswa"
                                           id="cari_siswa"
                                           class="form-control"
                                           placeholder="Cari nama atau NIS siswa..."
                                           value="<?= htmlspecialchars($cari_siswa); ?>"
                                           autocomplete="off">

                                    <div id="hasil_pencarian"
                                         class="list-group"
                                         style="
                                            position: absolute;
                                            top: calc(100% + 4px);
                                            left: 0;
                                            width: 100%;
                                            z-index: 99999;
                                            display: none;
                                            max-height: 300px;
                                            overflow-y: auto;
                                            background: white;
                                            border: 1px solid #dee2e6;
                                            border-radius: 8px;
                                            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
                                         ">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    Cari
                                </button>

                                <a href="laporan.php" class="btn btn-secondary">
                                    Reset
                                </a>
                            </div>

                        </form>
                    </div>
                </div>

                <div class="card-data rekap-card mb-4">

                    <div class="card-header">
                        <div class="card-header-content">

                            <div class="card-title">
                                <h5>Rekapitulasi Siswa</h5>
                                <p>Daftar siswa yang memiliki catatan pelanggaran</p>
                            </div>

                            <div class="card-actions no-print">

                                <button type="button"
                                        class="btn btn-secondary"
                                        onclick="cetakLaporan('semua')">
                                    Cetak Semua
                                </button>

                                <a href="laporan.php?export=excel&jenis=semua<?= $parameter_filter; ?>"
                                   class="btn btn-success">
                                    Excel Semua
                                </a>

                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Tanggal</th>
                                    <th>Kelas</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Total Poin</th>
                                    <th class="text-center">Aksi</th>
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
                                        <td class="fw-semibold"><?= htmlspecialchars($rekap['nama']); ?></td>
                                        <td>
                                            <?= !empty($rekap['tanggal']) ? date('d-m-Y', strtotime($rekap['tanggal'])) : '-'; ?>
                                        </td>
                                        <td><?= htmlspecialchars($rekap['nama_kelas'] ?? '-'); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">
                                                <?= $rekap['jumlah_pelanggaran']; ?> kali
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark">
                                                <?= $rekap['total_poin']; ?> poin
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="laporan.php?siswa_id=<?= $rekap['id']; ?><?= $parameter_filter; ?>#detail-laporan"
                                               class="btn btn-primary btn-sm">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>

                                <?php
                                    }
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-secondary">
                                            Data siswa tidak ditemukan.
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($data_siswa && $jumlah_detail > 0) { ?>

                    <div class="card-data" id="detail-laporan">

                        <div class="card-header">
                            <div class="card-header-content">

                                <div class="card-title">
                                    <h5>Detail Laporan Siswa</h5>
                                    <p>Rincian catatan pelanggaran siswa</p>
                                </div>

                                <div class="card-actions no-print">

                                    <button type="button"
                                            class="btn btn-primary"
                                            onclick="cetakLaporan('individu', <?= (int) $data_siswa['id']; ?>)">
                                        Cetak Individu
                                    </button>

                                    <a href="laporan.php?siswa_id=<?= $data_siswa['id']; ?>&export=excel&jenis=individu<?= $parameter_filter; ?>"
                                       class="btn btn-success">
                                        Excel Individu
                                    </a>

                                </div>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="mb-4 text-center">
                                <h5 class="fw-bold text-dark">
                                    LAPORAN PELANGGARAN SISWA
                                </h5>
                            </div>

                            <div class="row g-3 mb-4">

                                <div class="col-md-6">
                                    <small class="text-secondary">Nama Siswa</small>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($data_siswa['nama']); ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-secondary">NIS</small>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($data_siswa['nis']); ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-secondary">Kelas</small>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($data_detail[0]['nama_kelas'] ?? '-'); ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-secondary">Tanggal Laporan</small>
                                    <div class="fw-semibold">
                                        <?= date('d-m-Y'); ?>
                                    </div>
                                </div>

                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
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
                                        <?php $no = 1; ?>

                                        <?php foreach ($data_detail as $detail) { ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= date('d-m-Y', strtotime($detail['tanggal'])); ?></td>
                                                <td><?= htmlspecialchars($detail['nama_kelas'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($detail['nama_pelanggaran']); ?></td>
                                                <td class="text-center"><?= $detail['poin']; ?></td>
                                                <td><?= htmlspecialchars($detail['nama_guru'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($detail['keterangan'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($detail['tindakan'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($detail['status'] ?? '-'); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">
                                                Total Poin
                                            </th>
                                            <th class="text-center">
                                                <?= $total_poin; ?>
                                            </th>
                                            <th colspan="4"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php } ?>

                <div class="text-center text-secondary py-3">
                    <small>© 2026 Sistem Pelanggaran Siswa</small>
                </div>

            </div>
        </main>

    </div>
</div>

<iframe id="frameCetak"
        title="Dokumen cetak"
        style="
            position: fixed;
            left: -12000px;
            top: 0;
            width: 1200px;
            height: 900px;
            border: 0;
        ">
</iframe>

<script>
const inputSiswa = document.getElementById('cari_siswa');
const hasilPencarian = document.getElementById('hasil_pencarian');

let waktuPencarian;

inputSiswa.addEventListener('input', function () {

    const kataKunci = this.value.trim();

    clearTimeout(waktuPencarian);

    hasilPencarian.innerHTML = '';
    hasilPencarian.style.display = 'none';

    if (kataKunci.length === 0) {
        return;
    }

    waktuPencarian = setTimeout(function () {

        fetch('laporan.php?ajax=cari_siswa&q=' + encodeURIComponent(kataKunci))
            .then(response => response.json())
            .then(data => {

                hasilPencarian.innerHTML = '';

                if (data.length === 0) {
                    hasilPencarian.innerHTML =
                        '<div class="list-group-item text-secondary">Nama siswa tidak ditemukan</div>';

                    hasilPencarian.style.display = 'block';
                    return;
                }

                data.forEach(function (siswa) {

                    const pilihan = document.createElement('button');

                    pilihan.type = 'button';
                    pilihan.className = 'list-group-item list-group-item-action';
                    pilihan.textContent = siswa.nama + ' - ' + siswa.nis;

                    pilihan.addEventListener('click', function () {
                        inputSiswa.value = siswa.nama;
                        hasilPencarian.style.display = 'none';
                        inputSiswa.form.submit();
                    });

                    hasilPencarian.appendChild(pilihan);
                });

                hasilPencarian.style.display = 'block';

            })
            .catch(error => {
                console.error('Pencarian gagal:', error);
            });

    }, 200);
});

document.addEventListener('click', function (event) {

    if (
        !inputSiswa.contains(event.target) &&
        !hasilPencarian.contains(event.target)
    ) {
        hasilPencarian.style.display = 'none';
    }
});

function cetakLaporan(jenis, siswaId = '') {

    const parameter = new URLSearchParams(window.location.search);

    parameter.delete('ajax');
    parameter.delete('q');
    parameter.delete('export');
    parameter.delete('jenis');
    parameter.delete('cetak');
    parameter.delete('siswa_id');

    parameter.set('cetak', jenis);

    if (jenis === 'individu' && siswaId !== '') {
        parameter.set('siswa_id', siswaId);
    }

    const frame = document.getElementById('frameCetak');

    frame.onload = function () {

        try {
            frame.contentWindow.focus();
            frame.contentWindow.print();
        } catch (error) {
            console.error('Gagal membuka cetak:', error);
            alert('Halaman cetak gagal dimuat. Silakan coba lagi.');
        }
    };

    frame.src = 'laporan.php?' + parameter.toString();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>