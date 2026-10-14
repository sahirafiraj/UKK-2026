```php
<?php
session_start();
include "config/koneksi.php";

// Memeriksa login dan hak akses admin
if (!isset($_SESSION['login']) || ($_SESSION['role'] ?? '') != 'admin') {
    header("Location: login.php");
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Admin';
$pesan = "";

// Memproses form saat tombol Simpan ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $user_id = $_POST['user_id'] ?? '';
    $status_aktif = $_POST['status_aktif'] ?? '';

    // Validasi data
    if (
        $nip == '' ||
        $nama == '' ||
        $email == '' ||
        $user_id == '' ||
        $status_aktif == ''
    ) {
        $pesan = "Semua kolom wajib diisi.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesan = "Format email tidak valid.";

    } elseif (
        !ctype_digit((string) $user_id) ||
        !in_array($status_aktif, ['0', '1'], true)
    ) {
        $pesan = "Akun pengguna atau status tidak valid.";

    } else {

        // Memastikan akun pengguna tersedia
        $cek_user = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_users WHERE id = ?"
        );

        if (!$cek_user) {
            $pesan = "Query pemeriksaan akun gagal dibuat.";
        } else {
            mysqli_stmt_bind_param($cek_user, "i", $user_id);
            mysqli_stmt_execute($cek_user);
            mysqli_stmt_store_result($cek_user);

            if (mysqli_stmt_num_rows($cek_user) == 0) {
                $pesan = "Akun pengguna tidak ditemukan.";
            }

            mysqli_stmt_close($cek_user);
        }

        // Menyimpan data jika valid
        if ($pesan == '') {

            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO t_guru
                (nip, nama, email, status_aktif, user_id)
                VALUES (?, ?, ?, ?, ?)"
            );

            if ($stmt) {

                mysqli_stmt_bind_param(
                    $stmt,
                    "sssii",
                    $nip,
                    $nama,
                    $email,
                    $status_aktif,
                    $user_id
                );

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);

                    header("Location: data_guru.php");
                    exit;

                } else {
                    if (mysqli_stmt_errno($stmt) == 1062) {
                        $pesan = "NIP, email, atau akun pengguna sudah digunakan.";
                    } else {
                        $pesan = "Gagal menyimpan data guru. Periksa database.";
                    }
                }

                mysqli_stmt_close($stmt);

            } else {
                $pesan = "Query penyimpanan gagal dibuat.";
            }
        }
    }
}

// Mengambil daftar akun pengguna untuk dropdown
$query_user = mysqli_query(
    $koneksi,
    "SELECT * FROM t_users ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Data Guru</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR ADMIN -->
        <div
            class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
            style="background-color: #172554;"
        >

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link text-white">
                        Kelola Data Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php"
                       class="nav-link active bg-white text-primary">
                        Kelola Data Guru
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php" class="nav-link text-white">
                        Kelola Data Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php" class="nav-link text-white">
                        Kelola Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_penempatan_siswa.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php" class="nav-link text-white">
                        Kelola Data Wali Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php" class="nav-link text-white">
                        Kelola Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php" class="nav-link text-white">
                        Kelola Jenis Pelanggaran
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

            <!-- HEADER HALAMAN -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">

                <h2 class="fw-bold">Tambah Data Guru</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($nama_login); ?></b>
                </p>

            </div>

            <!-- FORM TAMBAH GURU -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white p-3">
                    <h5 class="fw-bold mb-0">Form Tambah Guru</h5>
                </div>

                <div class="card-body p-4">

                    <!-- MENAMPILKAN PESAN -->
                    <?php if ($pesan != '') { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>
                    <?php } ?>

                    <form method="POST" action="">

                        <!-- NIP -->
                        <div class="mb-3">
                            <label for="nip" class="form-label">
                                NIP
                            </label>

                            <input
                                type="text"
                                name="nip"
                                id="nip"
                                class="form-control"
                                maxlength="30"
                                value="<?= htmlspecialchars($_POST['nip'] ?? ''); ?>"
                                placeholder="Masukkan NIP guru"
                                required
                            >
                        </div>

                        <!-- NAMA GURU -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Guru
                            </label>

                            <input
                                type="text"
                                name="nama"
                                id="nama"
                                class="form-control"
                                maxlength="150"
                                value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>"
                                placeholder="Masukkan nama guru"
                                required
                            >
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                maxlength="150"
                                value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
                                placeholder="Masukkan email guru"
                                required
                            >
                        </div>

                        <!-- AKUN PENGGUNA -->
                        <div class="mb-3">
                            <label for="user_id" class="form-label">
                                Akun Pengguna
                            </label>

                            <select
                                name="user_id"
                                id="user_id"
                                class="form-select"
                                required
                            >
                                <option value="">-- Pilih Akun Pengguna --</option>

                                <?php if ($query_user) { ?>
                                    <?php while ($user = mysqli_fetch_assoc($query_user)) { ?>

                                        <option
                                            value="<?= (int) $user['id']; ?>"
                                            <?= (($_POST['user_id'] ?? '') == $user['id'])
                                                ? 'selected'
                                                : ''; ?>
                                        >
                                            <?= htmlspecialchars(
                                                'ID: ' . $user['id'] . ' - ' .
                                                ($user['username'] ?? $user['email'] ?? 'Akun pengguna')
                                            ); ?>
                                        </option>

                                    <?php } ?>
                                <?php } ?>

                            </select>

                            <small class="text-secondary">
                                Pilih akun yang akan dihubungkan dengan guru.
                            </small>
                        </div>

                        <!-- STATUS -->
                        <div class="mb-4">
                            <label for="status_aktif" class="form-label">
                                Status
                            </label>

                            <select
                                name="status_aktif"
                                id="status_aktif"
                                class="form-select"
                                required
                            >
                                <option value="">-- Pilih Status --</option>

                                <option
                                    value="1"
                                    <?= (($_POST['status_aktif'] ?? '') === '1')
                                        ? 'selected'
                                        : ''; ?>
                                >
                                    Aktif
                                </option>

                                <option
                                    value="0"
                                    <?= (($_POST['status_aktif'] ?? '') === '0')
                                        ? 'selected'
                                        : ''; ?>
                                >
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">
                            Simpan Guru
                        </button>

                        <a href="data_guru.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<!-- BOOTSTRAP JAVASCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
</body>
</html>
