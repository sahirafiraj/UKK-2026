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

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    echo "ID pelanggaran tidak valid.";
    exit;
}

$query = mysqli_query($koneksi, "
    SELECT *
    FROM t_pelanggaran_siswa
    WHERE id = $id
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data pelanggaran tidak ditemukan.";
    exit;
}

$error = '';

if (isset($_POST['simpan'])) {
    $tindakan = mysqli_real_escape_string($koneksi, trim($_POST['tindakan'] ?? ''));
    $status = mysqli_real_escape_string($koneksi, trim($_POST['status'] ?? ''));

    $status_valid = ['Belum', 'Dipantau', 'Pembinaan', 'Teguran', 'Selesai', 'Ditindaklanjuti'];

    if ($tindakan == '') {
        $error = 'Tindakan wajib diisi.';
    } elseif (!in_array($status, $status_valid)) {
        $error = 'Status tidak valid.';
    } else {
        $query_update = mysqli_query($koneksi, "
            UPDATE t_pelanggaran_siswa
            SET tindakan = '$tindakan',
                status = '$status'
            WHERE id = $id
        ");

        if ($query_update) {
            header("Location: tindakan.php");
            exit;
        } else {
            $error = 'Gagal menyimpan tindakan.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Beri Tindakan - Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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

                    <li class="nav-item">
                        <a href="laporan.php" class="nav-link text-white">Laporan</a>
                    </li>

                <?php } else { ?>

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
                        <a href="tindakan.php"
                           class="nav-link <?= ($halaman == 'tindakan.php' || $halaman == 'beri_tindakan.php') ? 'active' : 'text-white'; ?>">
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

            <h4 class="fw-bold mb-4">Tindakan Pelanggaran Siswa</h4>

            <div class="bg-white rounded shadow-sm mb-4">

                <div class="border-bottom px-4 py-3">
                    <h5 class="fw-bold mb-0">Berikan Tindakan</h5>
                </div>

                <div class="p-4">

                    <?php if ($error != '') { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Siswa</label>
                                <input type="text" class="form-control"
                                       value="<?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>"
                                       readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelas</label>
                                <input type="text" class="form-control"
                                       value="<?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>"
                                       readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pelanggaran</label>
                                <input type="text" class="form-control"
                                       value="<?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?>"
                                       readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Poin</label>
                                <input type="text" class="form-control"
                                       value="<?= htmlspecialchars($data['poin'] ?? '-'); ?>"
                                       readonly>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" rows="4" readonly><?= htmlspecialchars($data['keterangan'] ?? '-'); ?></textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="tindakan" class="form-label">Tindakan</label>
                                <textarea name="tindakan" id="tindakan"
                                          class="form-control" rows="4"
                                          required><?= htmlspecialchars($data['tindakan'] ?? ''); ?></textarea>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="status" class="form-label">Status</label>

                                <select name="status" id="status" class="form-select" required>
                                    <?php
                                    $pilihan_status = [
                                        'Belum',
                                        'Dipantau',
                                        'Pembinaan',
                                        'Teguran',
                                        'Selesai',
                                        'Ditindaklanjuti'
                                    ];

                                    $status_saat_ini = $data['status'] ?? 'Belum';

                                    foreach ($pilihan_status as $pilihan) {
                                        $selected = ($status_saat_ini == $pilihan) ? 'selected' : '';
                                    ?>
                                        <option value="<?= htmlspecialchars($pilihan); ?>" <?= $selected; ?>>
                                            <?= htmlspecialchars($pilihan); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                        </div>

                        <button type="submit" name="simpan" class="btn btn-primary">
                            Simpan Tindakan
                        </button>

                        <a href="tindakan.php" class="btn btn-secondary">
                            Batal
                        </a>

                    </form>

                </div>
            </div>

            <p class="text-secondary text-center mt-4 small">
                ©Sofinurjanah
            </p>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>