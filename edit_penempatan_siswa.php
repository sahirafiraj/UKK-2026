
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

$name = $_SESSION['name'] ?? 'Admin';

if (
    !isset($_GET['id']) ||
    !ctype_digit($_GET['id']) ||
    (int) $_GET['id'] <= 0
) {
    header("Location: data_penempatan_siswa.php");
    exit;
}

$id = (int) $_GET['id'];

// Mengambil data penempatan siswa
$stmt = mysqli_prepare($koneksi, "
    SELECT *
    FROM t_kelas_siswa
    WHERE id = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$data) {
    $_SESSION['pesan_error'] = "Data penempatan siswa tidak ditemukan.";
    header("Location: data_penempatan_siswa.php");
    exit;
}

// Mengambil pilihan siswa
$siswa = mysqli_query($koneksi, "
    SELECT id, nis, nama
    FROM t_siswa
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

// Mengambil pilihan tahun ajaran
$tahun = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    ORDER BY id DESC
");

// Mengambil pilihan kelas
$kelas = mysqli_query($koneksi, "
    SELECT id, nama, tingkat, jurusan
    FROM t_kelas
    WHERE status_aktif = 1
    ORDER BY nama ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
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
                    <a href="data_penempatan_siswa.php" class="nav-link active"
                       aria-current="page">
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

            <!-- HEADER -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold">Edit Penempatan Siswa</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($name); ?></b>
                </p>
            </div>

            <!-- PESAN ERROR -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"></button>
                </div>

                <?php unset($_SESSION['pesan_error']); ?>
            <?php } ?>

            <!-- FORM EDIT -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Form Edit Penempatan Siswa</h5>
                </div>

                <div class="card-body p-4">

                    <form action="proses_edit_penempatan.php" method="POST">

                        <input type="hidden" name="id"
                               value="<?= (int) $data['id']; ?>">

                        <div class="row g-3">

                            <!-- SISWA -->
                            <div class="col-md-6">
                                <label class="form-label">Siswa</label>

                                <select name="siswa_id"
                                        class="form-select"
                                        required>

                                    <option value="">-- Pilih Siswa --</option>

                                    <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                                        <option value="<?= (int) $s['id']; ?>"
                                            <?= $data['siswa_id'] == $s['id'] ? 'selected' : ''; ?>>

                                            <?= htmlspecialchars(
                                                $s['nis'] . ' - ' . $s['nama']
                                            ); ?>

                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <!-- TAHUN AJARAN -->
                            <div class="col-md-6">
                                <label class="form-label">Tahun Ajaran</label>

                                <select name="tahun_ajaran_id"
                                        class="form-select"
                                        required>

                                    <option value="">-- Pilih Tahun Ajaran --</option>

                                    <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                        <option value="<?= (int) $ta['id']; ?>"
                                            <?= $data['tahun_ajaran_id'] == $ta['id'] ? 'selected' : ''; ?>>

                                            <?= htmlspecialchars($ta['nama']); ?>

                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <!-- KELAS -->
                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>

                                <select name="kelas_id"
                                        class="form-select"
                                        required>

                                    <option value="">-- Pilih Kelas --</option>

                                    <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                        <option value="<?= (int) $k['id']; ?>"
                                            <?= $data['kelas_id'] == $k['id'] ? 'selected' : ''; ?>>

                                            <?= htmlspecialchars(
                                                $k['nama'] . ' - Tingkat ' .
                                                $k['tingkat'] . ' - ' .
                                                $k['jurusan']
                                            ); ?>

                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <!-- TANGGAL MULAI -->
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>

                                <input type="date"
                                       name="tanggal_mulai"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['tanggal_mulai']); ?>"
                                       required>
                            </div>

                            <!-- TANGGAL SELESAI -->
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>

                                <input type="date"
                                       name="tanggal_selesai"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['tanggal_selesai']); ?>"
                                       required>
                            </div>

                            <!-- STATUS -->
                            <div class="col-md-6">
                                <label class="form-label">Status</label>

                                <select name="status_aktif"
                                        class="form-select"
                                        required>

                                    <option value="1"
                                        <?= $data['status_aktif'] == 1 ? 'selected' : ''; ?>>
                                        Aktif
                                    </option>

                                    <option value="0"
                                        <?= $data['status_aktif'] == 0 ? 'selected' : ''; ?>>
                                        Tidak Aktif
                                    </option>

                                </select>
                            </div>

                            <!-- TOMBOL -->
                            <div class="col-12 mt-4">

                                <button type="submit"
                                        name="simpan"
                                        class="btn btn-primary">
                                    Simpan Perubahan
                                </button>

                                <a href="data_penempatan_siswa.php"
                                   class="btn btn-secondary">
                                    Kembali
                                </a>

                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
