
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

$id = $_GET['id'] ?? '';

$query = mysqli_query($koneksi, "
    SELECT * FROM t_wali_kelas
    WHERE id = '$id'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data wali kelas tidak ditemukan!";
    exit;
}

$query_tahun = mysqli_query($koneksi, "
    SELECT * FROM t_tahun_ajaran
    WHERE status_aktif = 1 OR id = '{$data['tahun_ajaran_id']}'
");

$query_kelas = mysqli_query($koneksi, "
    SELECT * FROM t_kelas
    WHERE status_aktif = 1 OR id = '{$data['kelas_id']}'
");

$query_guru = mysqli_query($koneksi, "
    SELECT * FROM t_guru
    WHERE status_aktif = 1 OR id = '{$data['guru_id']}'
");

// MENYIMPAN DATA KELAS
$daftar_kelas = [];

while ($kelas = mysqli_fetch_assoc($query_kelas)) {
    $daftar_kelas[] = $kelas;
}

// MENYIMPAN DATA GURU
$daftar_guru = [];

while ($guru = mysqli_fetch_assoc($query_guru)) {
    $daftar_guru[] = $guru;
}

// MENGAMBIL NAMA KELAS DAN GURU YANG SEDANG DIPILIH
$nama_kelas = '';
$nama_guru = '';

foreach ($daftar_kelas as $kelas) {
    if ($kelas['id'] == $data['kelas_id']) {
        $nama_kelas = $kelas['nama'];
    }
}

foreach ($daftar_guru as $guru) {
    if ($guru['id'] == $data['guru_id']) {
        $nama_guru = $guru['nama'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Wali Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>

<body class="bg-light">

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
                <a href="data_penempatan_siswa.php" class="nav-link text-white">Penempatan Siswa</a>
            </li>

            <li class="nav-item">
                <a href="data_wali_kelas.php" class="nav-link active">Data Wali Kelas</a>
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
                <h2 class="fw-bold mb-2">Edit Wali Kelas</h2>
                <p class="text-secondary mb-0">
                    Selamat datang, <b><?= htmlspecialchars($name); ?></b>
                </p>
            </div>

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white p-3">
                    <h5 class="mb-0 fw-bold">Form Edit Wali Kelas</h5>
                </div>

                <div class="card-body p-4">

                    <form action="proses_edit_wali_kelas.php?id=<?= $id; ?>" method="POST" id="formWaliKelas">

                        <!-- TAHUN AJARAN -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tahun Ajaran</label>

                            <select name="tahun_ajaran_id" class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>

                                <?php while ($tahun = mysqli_fetch_assoc($query_tahun)) { ?>
                                    <option value="<?= $tahun['id']; ?>"
                                        <?= $tahun['id'] == $data['tahun_ajaran_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($tahun['nama']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- KELAS -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kelas</label>

                            <input type="text"
                                   id="cari_kelas"
                                   class="form-control"
                                   placeholder="Cari nama kelas..."
                                   value="<?= htmlspecialchars($nama_kelas); ?>"
                                   autocomplete="off"
                                   required>

                            <input type="hidden"
                                   name="kelas_id"
                                   id="kelas_id"
                                   value="<?= $data['kelas_id']; ?>">

                            <div id="hasil_kelas"
                                 class="list-group mt-1"
                                 style="position: relative; z-index: 10;">
                            </div>
                        </div>

                        <!-- GURU -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Guru</label>

                            <input type="text"
                                   id="cari_guru"
                                   class="form-control"
                                   placeholder="Cari nama guru..."
                                   value="<?= htmlspecialchars($nama_guru); ?>"
                                   autocomplete="off"
                                   required>

                            <input type="hidden"
                                   name="guru_id"
                                   id="guru_id"
                                   value="<?= $data['guru_id']; ?>">

                            <div id="hasil_guru"
                                 class="list-group mt-1"
                                 style="position: relative; z-index: 10;">
                            </div>
                        </div>

                        <!-- TANGGAL MULAI -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>

                            <input type="date"
                                   name="tanggal_mulai"
                                   class="form-control"
                                   value="<?= $data['tanggal_mulai']; ?>"
                                   required>
                        </div>

                        <!-- TANGGAL SELESAI -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>

                            <input type="date"
                                   name="tanggal_selesai"
                                   class="form-control"
                                   value="<?= $data['tanggal_selesai']; ?>">
                        </div>

                        <!-- STATUS -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>

                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?= $data['status_aktif'] == 1 ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option value="0" <?= $data['status_aktif'] == 0 ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <hr>

                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" name="update" class="btn btn-primary">
                                Simpan Perubahan
                            </button>

                            <a href="data_wali_kelas.php" class="btn btn-secondary">
                                Kembali
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </main>

    <script>
        // DATA KELAS DAN GURU DARI DATABASE
        const dataKelas = <?= json_encode($daftar_kelas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const dataGuru = <?= json_encode($daftar_guru, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

        // PENCARIAN KELAS
        const cariKelas = document.getElementById('cari_kelas');
        const hasilKelas = document.getElementById('hasil_kelas');
        const kelasId = document.getElementById('kelas_id');

        cariKelas.addEventListener('input', function () {
            const kata = this.value.toLowerCase();

            kelasId.value = '';
            hasilKelas.innerHTML = '';

            if (kata === '') {
                return;
            }

            const hasil = dataKelas.filter(function (kelas) {
                return (kelas.nama || '').toLowerCase().includes(kata);
            });

            if (hasil.length === 0) {
                hasilKelas.innerHTML = `
                    <div class="list-group-item text-secondary">
                        Kelas tidak ditemukan
                    </div>
                `;
                return;
            }

            hasil.forEach(function (kelas) {
                const tombol = document.createElement('button');

                tombol.type = 'button';
                tombol.className = 'list-group-item list-group-item-action';
                tombol.textContent = kelas.nama;

                tombol.addEventListener('click', function () {
                    cariKelas.value = kelas.nama;
                    kelasId.value = kelas.id;
                    hasilKelas.innerHTML = '';
                });

                hasilKelas.appendChild(tombol);
            });
        });

        // PENCARIAN GURU
        const cariGuru = document.getElementById('cari_guru');
        const hasilGuru = document.getElementById('hasil_guru');
        const guruId = document.getElementById('guru_id');

        cariGuru.addEventListener('input', function () {
            const kata = this.value.toLowerCase();

            guruId.value = '';
            hasilGuru.innerHTML = '';

            if (kata === '') {
                return;
            }

            const hasil = dataGuru.filter(function (guru) {
                return (guru.nama || '').toLowerCase().includes(kata);
            });

            if (hasil.length === 0) {
                hasilGuru.innerHTML = `
                    <div class="list-group-item text-secondary">
                        Guru tidak ditemukan
                    </div>
                `;
                return;
            }

            hasil.forEach(function (guru) {
                const tombol = document.createElement('button');

                tombol.type = 'button';
                tombol.className = 'list-group-item list-group-item-action';
                tombol.textContent = guru.nama;

                tombol.addEventListener('click', function () {
                    cariGuru.value = guru.nama;
                    guruId.value = guru.id;
                    hasilGuru.innerHTML = '';
                });

                hasilGuru.appendChild(tombol);
            });
        });

        // CEK SEBELUM FORM DIKIRIM
        document.getElementById('formWaliKelas').addEventListener('submit', function (e) {
            if (kelasId.value === '') {
                e.preventDefault();
                alert('Silakan pilih kelas dari hasil pencarian!');
                cariKelas.focus();
                return;
            }

            if (guruId.value === '') {
                e.preventDefault();
                alert('Silakan pilih guru dari hasil pencarian!');
                cariGuru.focus();
                return;
            }
        });

        // MENUTUP HASIL PENCARIAN JIKA KLIK DI LUAR
        document.addEventListener('click', function (e) {
            if (!cariKelas.contains(e.target) && !hasilKelas.contains(e.target)) {
                hasilKelas.innerHTML = '';
            }

            if (!cariGuru.contains(e.target) && !hasilGuru.contains(e.target)) {
                hasilGuru.innerHTML = '';
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
