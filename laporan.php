
<?php
session_start();

include "config/koneksi.php";

// Cek hak akses admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Admin';

$siswa_id = isset($_GET['siswa_id']) ? (int) $_GET['siswa_id'] : 0;

// Mengambil daftar siswa
$query_siswa = mysqli_query($koneksi, "
    SELECT id, nis, nama
    FROM t_siswa
    ORDER BY nama ASC
");

// Variabel awal
$data_siswa = null;
$query_laporan = false;

// Mengambil data siswa yang dipilih
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

    // Mengambil riwayat pelanggaran siswa
    if ($data_siswa) {
        $query_laporan = mysqli_query($koneksi, "
            SELECT *
            FROM t_pelanggaran_siswa
            WHERE siswa_id = $siswa_id
            ORDER BY tanggal DESC, id DESC
        ");
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran Siswa</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        .judul-cetak {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden !important;
            }

            #area-cetak,
            #area-cetak * {
                visibility: visible !important;
            }

            #area-cetak {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            .judul-cetak {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
            }

            .judul-cetak h2 {
                font-size: 20px;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .judul-cetak p {
                font-size: 11px;
                margin: 0;
            }

            .identitas-cetak {
                font-size: 12px;
                margin-bottom: 15px;
            }

            .identitas-cetak p {
                margin-bottom: 4px;
            }

            .card,
            .card-header,
            .card-body {
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                background: white !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            .table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 10px !important;
                margin-top: 10px !important;
            }

            .table th,
            .table td {
                border: 1px solid #555 !important;
                color: black !important;
                background: white !important;
                padding: 6px !important;
                vertical-align: top !important;
                overflow-wrap: anywhere;
            }

            .table thead th {
                text-align: center !important;
                font-weight: bold !important;
            }

            tr {
                break-inside: avoid;
            }
        }
    </style>
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

            <!-- HEADER -->
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

                                <?php if ($query_siswa) { ?>
                                    <?php while ($siswa = mysqli_fetch_assoc($query_siswa)) { ?>

                                        <option value="<?= (int) $siswa['id']; ?>"
                                            <?= $siswa_id == $siswa['id'] ? 'selected' : ''; ?>>

                                            <?= htmlspecialchars($siswa['nama']); ?>
                                            - NIS: <?= htmlspecialchars($siswa['nis']); ?>

                                        </option>

                                    <?php } ?>
                                <?php } ?>

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
                            <p>Rekapitulasi Catatan Pelanggaran Siswa</p>
                        </div>

                        <div class="identitas-cetak">
                            <p>
                                <strong>Nama Siswa:</strong>
                                <?= htmlspecialchars($data_siswa['nama']); ?>
                            </p>

                            <p>
                                <strong>NIS:</strong>
                                <?= htmlspecialchars($data_siswa['nis']); ?>
                            </p>

                            <p>
                                <strong>Tanggal Cetak:</strong>
                                <?= date('d-m-Y'); ?>
                            </p>
                        </div>

                        <?php if ($query_laporan && mysqli_num_rows($query_laporan) > 0) { ?>

                            <?php
                            $no = 1;
                            $total_poin = 0;
                            ?>

                            <div class="table-responsive">

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

                                        <?php while ($laporan = mysqli_fetch_assoc($query_laporan)) {
                                            $total_poin += (int) $laporan['poin'];
                                        ?>

                                            <tr>
                                                <td><?= $no++; ?></td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['tanggal']); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['nama_kelas']); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['nama_pelanggaran']); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['nama_guru']); ?>
                                                </td>

                                                <td class="text-center">
                                                    <?= (int) $laporan['poin']; ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['keterangan']); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['tindakan']); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($laporan['status']); ?>
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

                        <?php } else { ?>

                            <div class="alert alert-info mb-0">
                                Belum ada catatan pelanggaran untuk siswa ini.
                            </div>

                        <?php } ?>

                    </div>
                </div>

                <!-- TOMBOL CETAK -->
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
