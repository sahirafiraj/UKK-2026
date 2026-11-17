
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Admin';
$halaman = basename($_SERVER['PHP_SELF']);

// Data tahun ajaran aktif
$query_tahun = mysqli_query($koneksi, "
    SELECT * FROM t_tahun_ajaran
    WHERE status_aktif = 1
");

// Data kelas aktif
$query_kelas = mysqli_query($koneksi, "
    SELECT * FROM t_kelas
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

// Data guru aktif
$query_guru = mysqli_query($koneksi, "
    SELECT * FROM t_guru
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

// Menyimpan data kelas ke array
$daftar_kelas = [];

if ($query_kelas) {
    while ($kelas = mysqli_fetch_assoc($query_kelas)) {
        $daftar_kelas[] = $kelas;
    }
}

// Menyimpan data guru ke array
$daftar_guru = [];

if ($query_guru) {
    while ($guru = mysqli_fetch_assoc($query_guru)) {
        $daftar_guru[] = $guru;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Wali Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>

<body class="bg-light">

    <!-- SIDEBAR -->
    <div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

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
                <a href="data_wali_kelas.php" class="nav-link active">
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

    <!-- KONTEN -->
    <main class="content-main">
        <div class="page-content">

            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold mb-2">Tambah Wali Kelas</h2>
                <p class="text-secondary mb-0">
                    Selamat datang, <b><?= htmlspecialchars($nama_login); ?></b>
                </p>
            </div>

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white p-3">
                    <h5 class="mb-0 fw-bold">Form Tambah Wali Kelas</h5>
                </div>

                <div class="card-body p-4">

                    <form action="proses_tambah_wali_kelas.php" method="POST">

                        <!-- TAHUN AJARAN -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tahun Ajaran</label>

                            <select name="tahun_ajaran_id" class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>

                                <?php while ($tahun = mysqli_fetch_assoc($query_tahun)) { ?>
                                    <option value="<?= $tahun['id']; ?>">
                                        <?= htmlspecialchars($tahun['nama']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- PENCARIAN KELAS -->
                        <div class="mb-3">

                            <label class="form-label fw-semibold">Kelas</label>

                            <div style="position: relative;">

                                <input type="text"
                                       id="cari_kelas"
                                       class="form-control"
                                       placeholder="Ketik nama kelas..."
                                       autocomplete="off"
                                       required>

                                <input type="hidden"
                                       name="kelas_id"
                                       id="kelas_id">

                                <div id="hasil_kelas"
                                     class="list-group"
                                     style="
                                        position:absolute;
                                        top:calc(100% + 4px);
                                        left:0;
                                        width:100%;
                                        z-index:99999;
                                        display:none;
                                        max-height:250px;
                                        overflow-y:auto;
                                        background:white;
                                        box-shadow:0 4px 12px rgba(0,0,0,.15);
                                     ">
                                </div>

                            </div>

                            <small class="text-secondary d-block mt-2">
                                Ketik nama kelas, lalu pilih kelas dari daftar yang muncul.
                            </small>

                        </div>

                        <!-- PENCARIAN GURU -->
                        <div class="mb-3">

                            <label class="form-label fw-semibold">Guru</label>

                            <div style="position: relative;">

                                <input type="text"
                                       id="cari_guru"
                                       class="form-control"
                                       placeholder="Ketik nama guru..."
                                       autocomplete="off"
                                       required>

                                <input type="hidden"
                                       name="guru_id"
                                       id="guru_id">

                                <div id="hasil_guru"
                                     class="list-group"
                                     style="
                                        position:absolute;
                                        top:calc(100% + 4px);
                                        left:0;
                                        width:100%;
                                        z-index:99999;
                                        display:none;
                                        max-height:250px;
                                        overflow-y:auto;
                                        background:white;
                                        box-shadow:0 4px 12px rgba(0,0,0,.15);
                                     ">
                                </div>

                            </div>

                            <small class="text-secondary d-block mt-2">
                                Ketik nama guru, lalu pilih guru dari daftar yang muncul.
                            </small>

                        </div>

                        <!-- TANGGAL MULAI -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>

                            <input type="date"
                                   name="tanggal_mulai"
                                   class="form-control"
                                   required>
                        </div>

                        <!-- TANGGAL SELESAI -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>

                            <input type="date"
                                   name="tanggal_selesai"
                                   class="form-control">
                        </div>

                        <!-- STATUS -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>

                            <select name="status_aktif" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <hr>

                        <div class="d-flex gap-2 mt-3">

                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary">
                                Simpan
                            </button>

                            <a href="data_wali_kelas.php"
                               class="btn btn-secondary">
                                Batal
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // DATA KELAS DARI DATABASE
        const dataKelas = <?= json_encode($daftar_kelas); ?>;

        // DATA GURU DARI DATABASE
        const dataGuru = <?= json_encode($daftar_guru); ?>;


        // PENCARIAN KELAS
        const cariKelas = document.getElementById('cari_kelas');
        const kelasId = document.getElementById('kelas_id');
        const hasilKelas = document.getElementById('hasil_kelas');

        cariKelas.addEventListener('input', function () {

            const kata = this.value.toLowerCase().trim();

            // Kosongkan ID jika pencarian diubah
            kelasId.value = '';

            hasilKelas.innerHTML = '';
            hasilKelas.style.display = 'none';

            if (kata == '') {
                return;
            }

            let ditemukan = 0;

            dataKelas.forEach(function (kelas) {

                if (kelas.nama.toLowerCase().includes(kata)) {

                    ditemukan++;

                    const pilihan = document.createElement('button');

                    pilihan.type = 'button';
                    pilihan.className = 'list-group-item list-group-item-action';
                    pilihan.textContent = kelas.nama;

                    pilihan.addEventListener('click', function () {

                        cariKelas.value = kelas.nama;
                        kelasId.value = kelas.id;

                        hasilKelas.style.display = 'none';

                    });

                    hasilKelas.appendChild(pilihan);
                }

            });

            if (ditemukan == 0) {

                const pesan = document.createElement('div');

                pesan.className = 'list-group-item text-secondary';
                pesan.textContent = 'Kelas tidak ditemukan';

                hasilKelas.appendChild(pesan);
            }

            hasilKelas.style.display = 'block';

        });


        // PENCARIAN GURU
        const cariGuru = document.getElementById('cari_guru');
        const guruId = document.getElementById('guru_id');
        const hasilGuru = document.getElementById('hasil_guru');

        cariGuru.addEventListener('input', function () {

            const kata = this.value.toLowerCase().trim();

            // Kosongkan ID jika pencarian diubah
            guruId.value = '';

            hasilGuru.innerHTML = '';
            hasilGuru.style.display = 'none';

            if (kata == '') {
                return;
            }

            let ditemukan = 0;

            dataGuru.forEach(function (guru) {

                if (guru.nama.toLowerCase().includes(kata)) {

                    ditemukan++;

                    const pilihan = document.createElement('button');

                    pilihan.type = 'button';
                    pilihan.className = 'list-group-item list-group-item-action';
                    pilihan.textContent = guru.nama;

                    pilihan.addEventListener('click', function () {

                        cariGuru.value = guru.nama;
                        guruId.value = guru.id;

                        hasilGuru.style.display = 'none';

                    });

                    hasilGuru.appendChild(pilihan);
                }

            });

            if (ditemukan == 0) {

                const pesan = document.createElement('div');

                pesan.className = 'list-group-item text-secondary';
                pesan.textContent = 'Guru tidak ditemukan';

                hasilGuru.appendChild(pesan);
            }

            hasilGuru.style.display = 'block';

        });


        // TUTUP HASIL PENCARIAN JIKA KLIK DI LUAR
        document.addEventListener('click', function (event) {

            if (!cariKelas.contains(event.target) &&
                !hasilKelas.contains(event.target)) {

                hasilKelas.style.display = 'none';
            }

            if (!cariGuru.contains(event.target) &&
                !hasilGuru.contains(event.target)) {

                hasilGuru.style.display = 'none';
            }

        });

    </script>

</body>
</html>
