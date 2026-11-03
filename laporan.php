
<?php
session_start();
include "config/koneksi.php";

// CEK HAK AKSES ADMIN
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Admin';
$siswa_id = isset($_GET['siswa_id']) ? (int) $_GET['siswa_id'] : 0;

// MENGAMBIL DAFTAR SISWA
$query_siswa = mysqli_query($koneksi, "
    SELECT id, nis, nama
    FROM t_siswa
    ORDER BY nama ASC
");

$data_siswa = null;
$daftar_laporan = [];
$total_poin = 0;
$error_laporan = false;

// MENGAMBIL DATA SISWA YANG DIPILIH
if ($siswa_id > 0) {

    $query_pilih = mysqli_query($koneksi, "
        SELECT id, nis, nama
        FROM t_siswa
        WHERE id = $siswa_id
        LIMIT 1
    ");

    if ($query_pilih) {
        $data_siswa = mysqli_fetch_assoc($query_pilih);
    }

    // MENGAMBIL RIWAYAT PELANGGARAN
    if ($data_siswa) {

        $query_laporan = mysqli_query($koneksi, "
            SELECT *
            FROM t_pelanggaran_siswa
            WHERE siswa_id = $siswa_id
            ORDER BY tanggal DESC, id DESC
        ");

        if ($query_laporan) {
            while ($laporan = mysqli_fetch_assoc($query_laporan)) {
                $daftar_laporan[] = $laporan;
                $total_poin += (int) ($laporan['poin'] ?? 0);
            }
        } else {
            $error_laporan = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="style/style.css">

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
                    <a href="data_guru.php" class="nav-link text-white">
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
                    <a href="laporan.php" class="nav-link active">
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

            <!-- HEADER WEBSITE -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">

                <h2 class="fw-bold">Laporan Pelanggaran Siswa</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <strong><?= htmlspecialchars($nama_login); ?></strong>.
                    Pilih siswa untuk melihat riwayat pelanggarannya.
                </p>

            </div>

            <!-- FORM PILIH SISWA -->
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Pilih Siswa</h5>
                </div>

                <div class="card-body">

                    <form action="laporan.php" method="GET">

                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>

                            <select name="siswa_id" class="form-select" required>
                                <option value="">-- Pilih Siswa --</option>

                                <?php
                                if ($query_siswa) {
                                    while ($siswa = mysqli_fetch_assoc($query_siswa)) {
                                ?>
                                    <option value="<?= (int) $siswa['id']; ?>"
                                        <?= $siswa_id == $siswa['id'] ? 'selected' : ''; ?>>

                                        <?= htmlspecialchars($siswa['nama']); ?>
                                        - NIS: <?= htmlspecialchars($siswa['nis']); ?>

                                    </option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Lihat Laporan
                        </button>

                        <a href="laporan.php" class="btn btn-secondary">
                            Reset
                        </a>

                    </form>

                </div>
            </div>

            <!-- HASIL LAPORAN -->
            <?php if ($siswa_id > 0 && $data_siswa) { ?>

                <div id="area-cetak" class="card shadow-sm border-0">

                    <!-- HEADER YANG TAMPIL DI WEBSITE -->
                    <div class="card-header bg-white py-3">

                        <h5 class="mb-1">Riwayat Pelanggaran Siswa</h5>

                        <p class="text-secondary mb-0">
                            Nama:
                            <strong><?= htmlspecialchars($data_siswa['nama']); ?></strong>
                            |
                            NIS: <?= htmlspecialchars($data_siswa['nis']); ?>
                        </p>

                    </div>

                    <div class="card-body">

                        <!-- JUDUL KHUSUS CETAK -->
                        <div class="judul-cetak">
                            <h2>LAPORAN PELANGGARAN SISWA</h2>

                            <p>REKAPITULASI CATATAN PELANGGARAN SISWA</p>

                            <div class="garis-judul"></div>
                        </div>

                        <!-- IDENTITAS KHUSUS CETAK -->
                        <div class="identitas-cetak">

                            <h5>IDENTITAS SISWA</h5>

                            <table>
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($data_siswa['nama']); ?></td>
                                </tr>

                                <tr>
                                    <td>NIS</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($data_siswa['nis']); ?></td>
                                </tr>

                                <tr>
                                    <td>Tanggal cetak</td>
                                    <td>:</td>
                                    <td><?= date('d-m-Y'); ?></td>
                                </tr>
                            </table>

                        </div>

                        <?php if ($error_laporan) { ?>

                            <div class="alert alert-danger">
                                Data laporan gagal diambil dari database.
                            </div>

                        <?php } elseif (count($daftar_laporan) > 0) { ?>

                            <!-- TABEL WEBSITE: TETAP 9 KOLOM -->
                            <div class="table-responsive tabel-website">

                                <table class="table table-bordered table-striped table-hover align-middle">

                                    <thead class="table-primary">
                                        <tr>
                                            <th>No.</th>
                                            <th>Tanggal</th>
                                            <th>Kelas</th>
                                            <th>Jenis Pelanggaran</th>
                                            <th>Guru</th>
                                            <th>Poin</th>
                                            <th>Keterangan</th>
                                            <th>Tindakan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $no = 1; ?>

                                        <?php foreach ($daftar_laporan as $laporan) { ?>
                                            <tr>
                                                <td><?= $no++; ?></td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['tanggal'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['nama_kelas'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['nama_pelanggaran'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['nama_guru'] ?? '-'); ?>
                                                </td>

                                                <td class="text-center">
                                                    <?= (int) ($laporan['poin'] ?? 0); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['keterangan'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['tindakan'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['status'] ?? '-'); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-end">
                                                Total Poin
                                            </th>

                                            <th class="text-center">
                                                <?= $total_poin; ?>
                                            </th>

                                            <th colspan="3"></th>
                                        </tr>
                                    </tfoot>

                                </table>

                            </div>

                            <!-- TABEL KHUSUS CETAK: 3 KOLOM -->
                            <div class="tabel-cetak">

                                <table>

                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Pelanggaran</th>
                                            <th>Poin</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($daftar_laporan as $laporan) { ?>
                                            <tr>
                                                <td>
                                                    <?= htmlspecialchars($laporan['tanggal'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['nama_pelanggaran'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= (int) ($laporan['poin'] ?? 0); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="3">
                                                Total Poin: <?= $total_poin; ?>
                                            </td>
                                        </tr>
                                    </tfoot>

                                </table>

                            </div>

                            <!-- TANDA TANGAN KHUSUS CETAK -->
                            <div class="tanda-tangan">

                                <table class="tabel-tanda-tangan">
                                    <tbody>
                                        <tr>
                                            <td>
                                                Siswa

                                                <div class="ruang-tanda-tangan"></div>

                                                <div class="garis-tanda-tangan"></div>

                                                <span class="nama-tanda-tangan">
                                                    <?= htmlspecialchars($data_siswa['nama']); ?>
                                                </span>
                                            </td>

                                            <td>
                                                Orang Tua/Wali

                                                <div class="ruang-tanda-tangan"></div>

                                                <div class="garis-tanda-tangan"></div>

                                                <span class="nama-tanda-tangan">
                                                    (............................)
                                                </span>
                                            </td>

                                            <td>
                                                Wali Kelas

                                                <div class="ruang-tanda-tangan"></div>

                                                <div class="garis-tanda-tangan"></div>

                                                <span class="nama-tanda-tangan">
                                                    (............................)
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                        <?php } else { ?>

                            <div class="alert alert-info mb-0">
                                Belum ada catatan pelanggaran untuk siswa ini.
                            </div>

                        <?php } ?>

                    </div>
                </div>

                <!-- TOMBOL CETAK WEBSITE -->
                <div class="mt-3">
                    <button type="button"
                            onclick="window.print()"
                            class="btn btn-primary">
                        Cetak Laporan
                    </button>
                </div>

            <?php } elseif ($siswa_id > 0) { ?>

                <div class="alert alert-warning">
                    Data siswa tidak ditemukan. Silakan pilih siswa kembali.
                </div>

            <?php } else { ?>

                <div class="card shadow-sm border-0">

                    <div class="card-body text-center py-5">

                        <h5 class="text-secondary">
                            Belum Ada Siswa yang Dipilih
                        </h5>

                        <p class="text-muted mb-0">
                            Pilih nama siswa terlebih dahulu untuk melihat
                            riwayat pelanggarannya.
                        </p>

                    </div>

                </div>

            <?php } ?>

            <!-- KEMBALI KE DASHBOARD -->
            <div class="mt-4">
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
