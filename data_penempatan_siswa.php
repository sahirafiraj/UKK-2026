<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$name = $_SESSION['name'];
$halaman = basename($_SERVER['PHP_SELF']);

// Ambil daftar siswa
$siswa = mysqli_query($koneksi, "SELECT id, nis, nama FROM t_siswa WHERE status_aktif = 1 ORDER BY nama ASC");

// Ambil daftar tahun ajaran
$tahun = mysqli_query($koneksi, "SELECT id, nama FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY id DESC");

// Ambil daftar kelas
$kelas = mysqli_query($koneksi, "SELECT id, nama, tingkat, jurusan FROM t_kelas WHERE status_aktif = 1 ORDER BY nama ASC");

// Ambil data penempatan siswa
$sql = "SELECT
            ks.id,
            s.nis,
            s.nama AS nama_siswa,
            ta.nama AS tahun_ajaran,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan,
            ks.tanggal_mulai,
            ks.tanggal_selesai,
            ks.status_aktif
        FROM t_kelas_siswa ks
        LEFT JOIN t_siswa s ON ks.siswa_id = s.id
        LEFT JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
        LEFT JOIN t_kelas k ON ks.kelas_id = k.id
        ORDER BY ks.id DESC";

$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/3.1.4/css/dataTables.dataTables.min.css">
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
                    <a href="data_penempatan_siswa.php" class="nav-link active">Penempatan Siswa</a>
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

                <li class="nav-item">
                    <a href="about_me.php" class="nav-link text-white">About Me</a>
                </li>

                <hr class="border-white">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-white">Logout</a>
                </li>

            </ul>
        </div>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <!-- HEADER -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold">Kelola Data Penempatan Siswa</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($name); ?></b>
                </p>
            </div>

            <!-- PESAN SUKSES -->
            <?php if (isset($_SESSION['pesan_sukses'])) { ?>

                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['pesan_sukses']); ?>

                    <button type="button" class="btn-close"
                            data-bs-dismiss="alert"></button>
                </div>

                <?php unset($_SESSION['pesan_sukses']); ?>

            <?php } ?>

            <!-- PESAN ERROR -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>

                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>

                    <button type="button" class="btn-close"
                            data-bs-dismiss="alert"></button>
                </div>

                <?php unset($_SESSION['pesan_error']); ?>

            <?php } ?>

            <!-- FORM PENEMPATAN -->
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Form Penempatan Siswa</h5>
                </div>

                <div class="card-body">

                    <form action="proses_simpan_penempatan.php" method="POST">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Siswa</label>

                                <select name="siswa_id" class="form-select" required>
                                    <option value="">-- Pilih Siswa --</option>

                                    <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                                        <option value="<?= $s['id']; ?>">
                                            <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tahun Ajaran</label>

                                <select name="tahun_ajaran_id" class="form-select" required>
                                    <option value="">-- Pilih Tahun Ajaran --</option>

                                    <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                        <option value="<?= $ta['id']; ?>">
                                            <?= htmlspecialchars($ta['nama']); ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>

                                <select name="kelas_id" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>

                                    <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                        <option value="<?= $k['id']; ?>">
                                            <?= htmlspecialchars($k['nama'] . ' - Tingkat ' . $k['tingkat'] . ' - ' . $k['jurusan']); ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>

                                <input type="date"
                                       name="tanggal_mulai"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>

                                <input type="date"
                                       name="tanggal_selesai"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>

                                <select name="status_aktif" class="form-select" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    Simpan Penempatan
                                </button>
                            </div>

                        </div>

                    </form>

                </div>
            </div>

            <!-- TABEL DATA PENEMPATAN -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <h5 class="mb-0 fw-bold">
                            Daftar Data Penempatan Siswa
                        </h5>

                        <a href="dashboard.php" class="btn btn-secondary btn-sm">
                            Kembali ke Dashboard
                        </a>

                    </div>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelPenempatan" class="table table-bordered table-striped table-hover align-middle">

                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Kelas</th>
                                    <th>Tingkat</th>
                                    <th>Jurusan</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $no = 1;

                                if ($hasil && mysqli_num_rows($hasil) > 0) {
                                    while ($row = mysqli_fetch_assoc($hasil)) {
                                ?>

                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nis'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['tingkat'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['jurusan'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_mulai'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_selesai'] ?? '-'); ?></td>

                                    <td>
                                        <?php
                                        if ($row['status_aktif'] == 1) {
                                            echo '<span class="badge bg-success">Aktif</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">Tidak Aktif</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <?php
                                    }
                                } else {
                                ?>

                                <tr>
                                    <td colspan="10" class="text-center">
                                        Belum ada data penempatan siswa.
                                    </td>
                                </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/3.1.4/js/dataTables.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    new DataTable('#tabelPenempatan', {
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
            searchPlaceholder: 'Cari data...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada data',
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