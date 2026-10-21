<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Memeriksa login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Memeriksa hak akses Guru
if ($_SESSION['role'] != 'guru') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_guru = $_SESSION['name'];
$pesan = "";
$status = "";

// Mengambil data siswa
$query_siswa = mysqli_query(
    $koneksi,
    "SELECT * FROM t_siswa ORDER BY id DESC"
);

// Mengambil data kategori pelanggaran
$query_kategori = mysqli_query(
    $koneksi,
    "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC"
);

// Memproses formulir
if (isset($_POST['simpan'])) {

    $id_siswa = mysqli_real_escape_string(
        $koneksi,
        $_POST['id_siswa']
    );

    $id_kategori = mysqli_real_escape_string(
        $koneksi,
        $_POST['id_kategori']
    );

    $tanggal = mysqli_real_escape_string(
        $koneksi,
        $_POST['tanggal']
    );

    $keterangan = mysqli_real_escape_string(
        $koneksi,
        $_POST['keterangan']
    );

    // Mencari data guru berdasarkan email login
    $email = mysqli_real_escape_string(
        $koneksi,
        $_SESSION['email']
    );

    $query_guru = mysqli_query(
        $koneksi,
        "SELECT id FROM t_guru
         WHERE email = '$email'
         LIMIT 1"
    );

    if (!$query_guru || mysqli_num_rows($query_guru) == 0) {

        $pesan = "Data guru tidak ditemukan.";
        $status = "danger";

    } else {

        $data_guru = mysqli_fetch_assoc($query_guru);
        $id_guru = $data_guru['id'];

        // Menyimpan data pelanggaran
        $simpan = mysqli_query(
            $koneksi,
            "INSERT INTO t_pelanggaran_siswa
            (id_siswa, id_kategori, id_guru, tanggal, keterangan)
            VALUES
            ('$id_siswa', '$id_kategori', '$id_guru',
             '$tanggal', '$keterangan')"
        );

        if ($simpan) {
            $pesan = "Data pelanggaran berhasil disimpan.";
            $status = "success";
        } else {
            $pesan = "Data gagal disimpan. Periksa nama kolom pada database.";
            $status = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catat Pelanggaran - Sistem Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR GURU -->
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
                    <a href="catat_pelanggaran.php" class="nav-link active">
                        Catat Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="tindakan.php" class="nav-link text-white">
                        Tindakan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="riwayat.php" class="nav-link text-white">
                        Riwayat Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="rekap_poin.php" class="nav-link text-white">
                        Rekap Poin
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
        <div class="col-md-9 col-lg-10 p-4"
             style="margin-left: 16.666667%; width: 83.333333%;">

            <h3 class="mb-1">Catat Pelanggaran Siswa</h3>

            <p class="text-secondary mb-4">
                Selamat datang, <?= htmlspecialchars($nama_guru); ?>.
                Silakan isi formulir pencatatan pelanggaran.
            </p>

            <?php if ($pesan != "") { ?>
                <div class="alert alert-<?= $status; ?>">
                    <?= htmlspecialchars($pesan); ?>
                </div>
            <?php } ?>

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Formulir Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <form method="POST" action="">

                        <!-- NAMA SISWA -->
                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>

                            <select name="id_siswa"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Siswa --</option>

                                <?php if ($query_siswa) { ?>
                                    <?php while ($siswa = mysqli_fetch_assoc($query_siswa)) { ?>

                                        <option value="<?= $siswa['id']; ?>">
                                            <?= htmlspecialchars($siswa['nama']); ?>
                                        </option>

                                    <?php } ?>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- KATEGORI PELANGGARAN -->
                        <div class="mb-3">
                            <label class="form-label">Kategori Pelanggaran</label>

                            <select name="id_kategori"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Kategori --</option>

                                <?php if ($query_kategori) { ?>
                                    <?php while ($kategori = mysqli_fetch_assoc($query_kategori)) { ?>

                                        <option value="<?= $kategori['id']; ?>">
                                            <?= htmlspecialchars($kategori['nama']); ?>
                                        </option>

                                    <?php } ?>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- TANGGAL -->
                        <div class="mb-3">
                            <label class="form-label">Tanggal Pelanggaran</label>

                            <input type="date"
                                   name="tanggal"
                                   class="form-control"
                                   value="<?= date('Y-m-d'); ?>"
                                   required>
                        </div>

                        <!-- KETERANGAN -->
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>

                            <textarea name="keterangan"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Masukkan keterangan pelanggaran"
                                      required></textarea>
                        </div>

                        <!-- TOMBOL -->
                        <button type="submit"
                                name="simpan"
                                class="btn btn-primary">
                            Simpan Pelanggaran
                        </button>

                        <a href="dashboard.php"
                           class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </div>

    </div>
</div>

</body>
</html>