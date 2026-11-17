
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
$halaman = basename($_SERVER['PHP_SELF']);

if (
    !isset($_GET['id']) ||
    !ctype_digit($_GET['id']) ||
    (int) $_GET['id'] <= 0
) {
    header("Location: data_penempatan_siswa.php");
    exit;
}

$id = (int) $_GET['id'];

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

// DATA SISWA
$siswa = mysqli_query($koneksi, "
    SELECT id, nis, nama
    FROM t_siswa
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

// DATA KELAS
$kelas = mysqli_query($koneksi, "
    SELECT id, nama, tingkat, jurusan
    FROM t_kelas
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

// DATA TAHUN AJARAN
$tahun = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    ORDER BY id DESC
");

$daftar_siswa = [];
if ($siswa) {
    while ($s = mysqli_fetch_assoc($siswa)) {
        $daftar_siswa[] = $s;
    }
}

$daftar_kelas = [];
if ($kelas) {
    while ($k = mysqli_fetch_assoc($kelas)) {
        $daftar_kelas[] = $k;
    }
}

// SISWA TERPILIH
$query_siswa_awal = mysqli_query($koneksi, "
    SELECT id, nis, nama
    FROM t_siswa
    WHERE id = '" . (int) $data['siswa_id'] . "'
    LIMIT 1
");

$siswa_awal = mysqli_fetch_assoc($query_siswa_awal);

// KELAS TERPILIH
$query_kelas_awal = mysqli_query($koneksi, "
    SELECT id, nama, tingkat, jurusan
    FROM t_kelas
    WHERE id = '" . (int) $data['kelas_id'] . "'
    LIMIT 1
");

$kelas_awal = mysqli_fetch_assoc($query_kelas_awal);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="style/style.css">

    <style>
        .pencarian-box {
            position: relative;
        }

        .hasil-pencarian {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            width: 100%;
            z-index: 99999;
            display: none;
            max-height: 250px;
            overflow-y: auto;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,.15);
            border-radius: 6px;
        }

        .hasil-pencarian .list-group-item {
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

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
                    <a href="data_penempatan_siswa.php"
                       class="nav-link <?= $halaman == 'edit_penempatan_siswa.php' ? 'active' : 'text-white'; ?>">
                        Penempatan Siswa
                    </a>
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

        <!-- KONTEN -->
        <main class="content-main">
            <div class="page-content">

                <div class="bg-white rounded shadow-sm p-4 mb-4">
                    <h2 class="fw-bold">Edit Penempatan Siswa</h2>

                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($name); ?></b>
                    </p>
                </div>

                <?php if (isset($_SESSION['pesan_error'])) { ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($_SESSION['pesan_error']); ?>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"></button>
                    </div>

                    <?php unset($_SESSION['pesan_error']); ?>
                <?php } ?>

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Form Edit Penempatan Siswa</h5>
                    </div>

                    <div class="card-body p-4">

                        <form action="proses_edit_penempatan.php" method="POST">

                            <input type="hidden"
                                   name="id"
                                   value="<?= (int) $data['id']; ?>">

                            <div class="row g-3">

                                <!-- PENCARIAN SISWA -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Siswa
                                    </label>

                                    <div class="pencarian-box">

                                        <input type="text"
                                               id="cari_siswa"
                                               class="form-control"
                                               value="<?= htmlspecialchars(($siswa_awal['nis'] ?? '') . ' - ' . ($siswa_awal['nama'] ?? '')); ?>"
                                               placeholder="Ketik nama atau NIS siswa..."
                                               autocomplete="off"
                                               required>

                                        <input type="hidden"
                                               name="siswa_id"
                                               id="siswa_id"
                                               value="<?= (int) $data['siswa_id']; ?>">

                                        <div id="hasil_siswa"
                                             class="list-group hasil-pencarian">
                                        </div>

                                    </div>

                                    <small class="text-secondary d-block mt-2">
                                        Ketik nama atau NIS, lalu pilih siswa dari daftar.
                                    </small>
                                </div>

                                <!-- TAHUN AJARAN -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Tahun Ajaran
                                    </label>

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

                                <!-- PENCARIAN KELAS -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Kelas
                                    </label>

                                    <div class="pencarian-box">

                                        <input type="text"
                                               id="cari_kelas"
                                               class="form-control"
                                               value="<?= htmlspecialchars(($kelas_awal['nama'] ?? '') . ' - Tingkat ' . ($kelas_awal['tingkat'] ?? '') . ' - ' . ($kelas_awal['jurusan'] ?? '')); ?>"
                                               placeholder="Ketik nama, tingkat, atau jurusan..."
                                               autocomplete="off"
                                               required>

                                        <input type="hidden"
                                               name="kelas_id"
                                               id="kelas_id"
                                               value="<?= (int) $data['kelas_id']; ?>">

                                        <div id="hasil_kelas"
                                             class="list-group hasil-pencarian">
                                        </div>

                                    </div>

                                    <small class="text-secondary d-block mt-2">
                                        Ketik nama kelas, lalu pilih dari daftar.
                                    </small>
                                </div>

                                <!-- TANGGAL MULAI -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Tanggal Mulai
                                    </label>

                                    <input type="date"
                                           name="tanggal_mulai"
                                           class="form-control"
                                           value="<?= htmlspecialchars($data['tanggal_mulai']); ?>"
                                           required>
                                </div>

                                <!-- TANGGAL SELESAI -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Tanggal Selesai
                                    </label>

                                    <input type="date"
                                           name="tanggal_selesai"
                                           class="form-control"
                                           value="<?= htmlspecialchars($data['tanggal_selesai']); ?>"
                                           required>
                                </div>

                                <!-- STATUS -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Status
                                    </label>

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

                                <div class="col-12 mt-4">

                                    <button type="submit"
                                            name="update"
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

            </div>
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // DATA SISWA
    const daftarSiswa = <?= json_encode($daftar_siswa, JSON_UNESCAPED_UNICODE); ?>;

    // DATA KELAS
    const daftarKelas = <?= json_encode($daftar_kelas, JSON_UNESCAPED_UNICODE); ?>;

    // =========================================
    // PENCARIAN SISWA
    // =========================================

    const cariSiswa = document.getElementById('cari_siswa');
    const siswaId = document.getElementById('siswa_id');
    const hasilSiswa = document.getElementById('hasil_siswa');

    cariSiswa.addEventListener('input', function () {

        const kataKunci = this.value.toLowerCase().trim();

        siswaId.value = '';

        hasilSiswa.innerHTML = '';
        hasilSiswa.style.display = 'none';

        if (kataKunci === '') {
            return;
        }

        let ditemukan = 0;

        daftarSiswa.forEach(function (siswa) {

            const teksSiswa = (siswa.nis + ' - ' + siswa.nama).toLowerCase();

            if (teksSiswa.includes(kataKunci)) {

                ditemukan++;

                const pilihan = document.createElement('button');

                pilihan.type = 'button';
                pilihan.className = 'list-group-item list-group-item-action';
                pilihan.textContent = siswa.nis + ' - ' + siswa.nama;

                pilihan.addEventListener('click', function () {

                    cariSiswa.value = siswa.nis + ' - ' + siswa.nama;
                    siswaId.value = siswa.id;

                    hasilSiswa.style.display = 'none';

                });

                hasilSiswa.appendChild(pilihan);
            }

        });

        if (ditemukan === 0) {
            hasilSiswa.innerHTML =
                '<div class="list-group-item text-secondary">Siswa tidak ditemukan</div>';
        }

        hasilSiswa.style.display = 'block';

    });

    // =========================================
    // PENCARIAN KELAS
    // =========================================

    const cariKelas = document.getElementById('cari_kelas');
    const kelasId = document.getElementById('kelas_id');
    const hasilKelas = document.getElementById('hasil_kelas');

    cariKelas.addEventListener('input', function () {

        const kataKunci = this.value.toLowerCase().trim();

        kelasId.value = '';

        hasilKelas.innerHTML = '';
        hasilKelas.style.display = 'none';

        if (kataKunci === '') {
            return;
        }

        let ditemukan = 0;

        daftarKelas.forEach(function (kelas) {

            const teksKelas = (
                kelas.nama + ' - Tingkat ' +
                kelas.tingkat + ' - ' +
                kelas.jurusan
            ).toLowerCase();

            if (teksKelas.includes(kataKunci)) {

                ditemukan++;

                const pilihan = document.createElement('button');

                pilihan.type = 'button';
                pilihan.className = 'list-group-item list-group-item-action';

                pilihan.textContent =
                    kelas.nama + ' - Tingkat ' +
                    kelas.tingkat + ' - ' +
                    kelas.jurusan;

                pilihan.addEventListener('click', function () {

                    cariKelas.value =
                        kelas.nama + ' - Tingkat ' +
                        kelas.tingkat + ' - ' +
                        kelas.jurusan;

                    kelasId.value = kelas.id;

                    hasilKelas.style.display = 'none';

                });

                hasilKelas.appendChild(pilihan);
            }

        });

        if (ditemukan === 0) {
            hasilKelas.innerHTML =
                '<div class="list-group-item text-secondary">Kelas tidak ditemukan</div>';
        }

        hasilKelas.style.display = 'block';

    });

    // =========================================
    // TUTUP DROPDOWN
    // =========================================

    document.addEventListener('click', function (event) {

        if (
            !cariSiswa.contains(event.target) &&
            !hasilSiswa.contains(event.target)
        ) {
            hasilSiswa.style.display = 'none';
        }

        if (
            !cariKelas.contains(event.target) &&
            !hasilKelas.contains(event.target)
        ) {
            hasilKelas.style.display = 'none';
        }

    });

    // =========================================
    // VALIDASI
    // =========================================

    const form = document.querySelector('form');

    form.addEventListener('submit', function (event) {

        if (siswaId.value === '') {
            event.preventDefault();
            alert('Silakan pilih siswa dari daftar pencarian.');
            cariSiswa.focus();
            return;
        }

        if (kelasId.value === '') {
            event.preventDefault();
            alert('Silakan pilih kelas dari daftar pencarian.');
            cariKelas.focus();
            return;
        }

    });

});
</script>

</body>
</html>
