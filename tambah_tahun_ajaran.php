
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Memeriksa hak akses admin
if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Mengambil nama pengguna
$name = $_SESSION['name'];

// Menyiapkan pesan dan nilai awal form
$pesan = "";
$nama = "";
$tanggal_mulai = "";
$tanggal_selesai = "";
$status_aktif = 1;

// Memproses data ketika tombol Simpan ditekan
if (isset($_POST['simpan'])) {

    // Mengambil data dari form
    $nama = trim($_POST['nama']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = (int) $_POST['status_aktif'];

    // Memastikan semua data wajib diisi
    if ($nama == "" || $tanggal_mulai == "" || $tanggal_selesai == "") {

        $pesan = "Semua data harus diisi!";

    // Memeriksa tanggal mulai dan tanggal selesai
    } elseif ($tanggal_selesai < $tanggal_mulai) {

        $pesan = "Tanggal selesai tidak boleh sebelum tanggal mulai!";

    } else {

        // Membersihkan nama tahun ajaran yang dimasukkan
        // Contoh: 2026/2027 menjadi 20262027
        // Tanda garis miring, strip, dan spasi akan diabaikan
        $nama_cek = preg_replace('/[^0-9]/', '', $nama);

        // Memastikan nama berisi angka tahun ajaran
        if ($nama_cek == "") {

            $pesan = "Nama tahun ajaran tidak valid!";

        } else {

            // Mengambil semua nama tahun ajaran yang sudah tersimpan
            $query_cek = mysqli_query(
                $koneksi,
                "SELECT nama FROM t_tahun_ajaran"
            );

            // Memastikan query pengecekan berhasil
            if (!$query_cek) {

                $pesan = "Gagal memeriksa tahun ajaran: "
                       . mysqli_error($koneksi);

            } else {

                // Nilai awal: belum ditemukan data yang sama
                $duplikat = false;

                // Memeriksa setiap tahun ajaran yang sudah tersimpan
                while ($data_cek = mysqli_fetch_assoc($query_cek)) {

                    // Mengubah nama lama menjadi angka saja
                    $nama_lama = preg_replace(
                        '/[^0-9]/',
                        '',
                        trim($data_cek['nama'])
                    );

                    // Membandingkan nama yang dimasukkan dengan data lama
                    if ($nama_cek == $nama_lama) {

                        // Menandai bahwa tahun ajaran sudah ada
                        $duplikat = true;

                        // Menghentikan pemeriksaan
                        break;
                    }
                }

                // Jika ditemukan nama yang sama, batalkan penyimpanan
                if ($duplikat == true) {

                    $pesan = "Tahun ajaran tersebut sudah terdaftar!";

                } else {

                    // Mengamankan nama sebelum digunakan dalam SQL
                    $nama_db = mysqli_real_escape_string(
                        $koneksi,
                        $nama
                    );

                    // Mengamankan tanggal mulai
                    $tanggal_mulai_db = mysqli_real_escape_string(
                        $koneksi,
                        $tanggal_mulai
                    );

                    // Mengamankan tanggal selesai
                    $tanggal_selesai_db = mysqli_real_escape_string(
                        $koneksi,
                        $tanggal_selesai
                    );

                    // Menyimpan data hanya jika belum ada duplikat
                    $simpan = mysqli_query(
                        $koneksi,
                        "INSERT INTO t_tahun_ajaran
                        (nama, tanggal_mulai, tanggal_selesai, status_aktif)
                        VALUES (
                            '$nama_db',
                            '$tanggal_mulai_db',
                            '$tanggal_selesai_db',
                            '$status_aktif'
                        )"
                    );

                    // Memeriksa hasil penyimpanan
                    if ($simpan) {

                        // Jika berhasil, kembali ke daftar tahun ajaran
                        header("Location: data_tahun_ajaran.php");
                        exit;

                    } else {

                        // Menampilkan pesan jika penyimpanan gagal
                        $pesan = "Data tahun ajaran gagal disimpan: "
                               . mysqli_error($koneksi);
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Tahun Ajaran</title>

    <!-- Bootstrap 5.3.8 -->
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

                <!-- Menu tahun ajaran yang sedang dibuka -->
                <li class="nav-item">
                    <a href="data_tahun_ajaran.php" class="nav-link active">
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

            <!-- Header halaman -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">

                <h2 class="fw-bold">Tambah Tahun Ajaran</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($name); ?></b>
                </p>

            </div>

            <!-- Kartu form -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        Form Tambah Tahun Ajaran
                    </h5>
                </div>

                <div class="card-body">

                    <!-- Menampilkan pesan kesalahan -->
                    <?php if ($pesan != "") { ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>

                    <?php } ?>

                    <form method="POST">

                        <!-- Nama tahun ajaran -->
                        <div class="mb-3">

                            <label class="form-label">
                                Nama Tahun Ajaran
                            </label>

                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   placeholder="Contoh: 2026/2027"
                                   value="<?= htmlspecialchars($nama); ?>"
                                   required>

                            <small class="text-secondary">
                                Tahun ajaran yang sudah terdaftar
                                tidak dapat ditambahkan kembali.
                            </small>

                        </div>

                        <!-- Tanggal mulai -->
                        <div class="mb-3">

                            <label class="form-label">
                                Tanggal Mulai
                            </label>

                            <input type="date"
                                   name="tanggal_mulai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($tanggal_mulai); ?>"
                                   required>

                        </div>

                        <!-- Tanggal selesai -->
                        <div class="mb-3">

                            <label class="form-label">
                                Tanggal Selesai
                            </label>

                            <input type="date"
                                   name="tanggal_selesai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($tanggal_selesai); ?>"
                                   required>

                        </div>

                        <!-- Status tahun ajaran -->
                        <div class="mb-4">

                            <label class="form-label">
                                Status Tahun Ajaran
                            </label>

                            <select name="status_aktif"
                                    class="form-select"
                                    required>

                                <option value="1"
                                    <?= $status_aktif == 1 ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= $status_aktif == 0 ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>

                            </select>

                        </div>

                        <!-- Tombol simpan dan kembali -->
                        <div class="d-flex gap-2">

                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary">
                                Simpan Data
                            </button>

                            <a href="data_tahun_ajaran.php"
                               class="btn btn-secondary">
                                Kembali
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<!-- JavaScript Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>