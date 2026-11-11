<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role != 'guru') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Guru';
$id_user = (int) ($_SESSION['id'] ?? 0);
$halaman = basename($_SERVER['PHP_SELF']);

$pesan = "";
$jenis_pesan = "";

// GURU SESUAI AKUN LOGIN

$query_guru_login = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_guru
    WHERE user_id = '$id_user'
    AND status_aktif = 1
    LIMIT 1
");

$data_guru_login = mysqli_fetch_assoc($query_guru_login);

if (!$data_guru_login) {
    echo "Data guru tidak ditemukan atau tidak aktif.";
    exit;
}

$guru_id = (int) $data_guru_login['id'];
$nama_guru = $data_guru_login['nama'];

// TAHUN AJARAN AKTIF

$query_tahun = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    LIMIT 1
");

$data_tahun = mysqli_fetch_assoc($query_tahun);

if (!$data_tahun) {
    echo "Tahun ajaran aktif belum tersedia.";
    exit;
}

$tahun_ajaran_id = (int) $data_tahun['id'];
$nama_tahun = $data_tahun['nama'];

// PILIHAN KELAS

$kelas_id = (int) ($_GET['kelas_id'] ?? $_POST['kelas_id'] ?? 0);

$query_kelas = mysqli_query($koneksi, "
    SELECT DISTINCT k.id, k.nama
    FROM t_kelas_siswa ks
    JOIN t_kelas k ON ks.kelas_id = k.id
    WHERE ks.tahun_ajaran_id = '$tahun_ajaran_id'
    ORDER BY k.nama ASC
");

// DATA KATEGORI DAN JENIS PELANGGARAN

$query_kategori = mysqli_query($koneksi, "
    SELECT
        pk.id,
        pk.nama,
        (
            SELECT p.poin
            FROM t_pelanggaran p
            WHERE p.pelanggaran_kategori_id = pk.id
            AND p.status_aktif = 1
            ORDER BY p.id ASC
            LIMIT 1
        ) AS poin_referensi
    FROM t_pelanggaran_kategori pk
    WHERE pk.status_aktif = 1
    ORDER BY pk.nama ASC
");

$query_pelanggaran = mysqli_query($koneksi, "
    SELECT
        p.id,
        p.pelanggaran_kategori_id,
        p.nama,
        p.poin,
        pk.nama AS nama_kategori
    FROM t_pelanggaran p
    JOIN t_pelanggaran_kategori pk
        ON pk.id = p.pelanggaran_kategori_id
    WHERE p.status_aktif = 1
    AND pk.status_aktif = 1
    ORDER BY p.nama ASC
");

$daftar_pelanggaran = [];

if ($query_pelanggaran) {
    while ($item = mysqli_fetch_assoc($query_pelanggaran)) {
        $daftar_pelanggaran[] = $item;
    }
}

// PENCARIAN SISWA AJAX

if (isset($_GET['ajax']) && $_GET['ajax'] == 'cari_siswa') {

    header('Content-Type: application/json; charset=utf-8');

    $kata_kunci = trim($_GET['q'] ?? '');
    $kelas_cari = (int) ($_GET['kelas_id'] ?? 0);
    $hasil = [];

    if ($kata_kunci != '' && $kelas_cari > 0) {

        $cari_aman = mysqli_real_escape_string($koneksi, $kata_kunci);

        $query_ajax = mysqli_query($koneksi, "
            SELECT
                ks.id AS penempatan_id,
                s.id AS siswa_id,
                s.nis,
                s.nama AS nama_siswa
            FROM t_kelas_siswa ks
            JOIN t_siswa s ON s.id = ks.siswa_id
            WHERE ks.kelas_id = '$kelas_cari'
            AND ks.tahun_ajaran_id = '$tahun_ajaran_id'
            AND (
                s.nama LIKE '%$cari_aman%'
                OR s.nis LIKE '%$cari_aman%'
            )
            ORDER BY s.nama ASC
        ");

        if ($query_ajax) {
            while ($siswa = mysqli_fetch_assoc($query_ajax)) {
                $hasil[] = $siswa;
            }
        }
    }

    echo json_encode($hasil);
    exit;
}

// PROSES SIMPAN PELANGGARAN

if (isset($_POST['simpan'])) {

    $penempatan_id = (int) ($_POST['penempatan_id'] ?? 0);
    $kelas_id = (int) ($_POST['kelas_id'] ?? 0);

    $jenis_input = $_POST['jenis_input'] ?? 'tersedia';

    $tanggal = mysqli_real_escape_string(
        $koneksi,
        $_POST['tanggal'] ?? ''
    );

    $keterangan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['keterangan'] ?? '')
    );

    $status = $_POST['status'] ?? 'Pending';

    $status_valid = ['Pending', 'Diproses', 'Selesai'];

    if (!in_array($status, $status_valid)) {
        $status = 'Pending';
    }

    $query_siswa_pilih = mysqli_query($koneksi, "
        SELECT
            ks.siswa_id,
            ks.tahun_ajaran_id,
            ks.kelas_id,
            s.nama AS nama_siswa,
            k.nama AS nama_kelas
        FROM t_kelas_siswa ks
        JOIN t_siswa s ON s.id = ks.siswa_id
        JOIN t_kelas k ON k.id = ks.kelas_id
        WHERE ks.id = '$penempatan_id'
        AND ks.kelas_id = '$kelas_id'
        AND ks.tahun_ajaran_id = '$tahun_ajaran_id'
        LIMIT 1
    ");

    if ($kelas_id <= 0) {

        $pesan = "Silakan pilih kelas terlebih dahulu.";
        $jenis_pesan = "danger";

    } elseif (
        $penempatan_id <= 0 ||
        !$query_siswa_pilih ||
        mysqli_num_rows($query_siswa_pilih) == 0
    ) {

        $pesan = "Silakan pilih siswa dari daftar pencarian.";
        $jenis_pesan = "danger";

    } elseif ($tanggal == "") {

        $pesan = "Tanggal pelanggaran wajib diisi.";
        $jenis_pesan = "danger";

    } else {

        $data_siswa = mysqli_fetch_assoc($query_siswa_pilih);

        $siswa_id = (int) $data_siswa['siswa_id'];

        $nama_siswa = mysqli_real_escape_string(
            $koneksi,
            $data_siswa['nama_siswa']
        );

        $nama_kelas = mysqli_real_escape_string(
            $koneksi,
            $data_siswa['nama_kelas']
        );

        $pelanggaran_id = 0;
        $kategori_id = 0;
        $nama_pelanggaran = '';
        $poin = 0;

        // JENIS PELANGGARAN DARI DAFTAR

        if ($jenis_input == 'tersedia') {

            $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);

            $query_jenis_pilih = mysqli_query($koneksi, "
                SELECT
                    p.id,
                    p.pelanggaran_kategori_id,
                    p.nama,
                    p.poin
                FROM t_pelanggaran p
                JOIN t_pelanggaran_kategori pk
                    ON pk.id = p.pelanggaran_kategori_id
                WHERE p.id = '$pelanggaran_id'
                AND p.status_aktif = 1
                AND pk.status_aktif = 1
                LIMIT 1
            ");

            if (
                !$query_jenis_pilih ||
                mysqli_num_rows($query_jenis_pilih) == 0
            ) {

                $pesan = "Silakan pilih jenis pelanggaran yang tersedia.";
                $jenis_pesan = "danger";

            } else {

                $data_jenis = mysqli_fetch_assoc($query_jenis_pilih);

                $kategori_id = (int) $data_jenis['pelanggaran_kategori_id'];

                $nama_pelanggaran = mysqli_real_escape_string(
                    $koneksi,
                    $data_jenis['nama']
                );

                $poin = (int) $data_jenis['poin'];
            }

        // JENIS PELANGGARAN CUSTOM

        } elseif ($jenis_input == 'custom') {

            $kategori_id = (int) ($_POST['kategori_custom'] ?? 0);

            $nama_pelanggaran = mysqli_real_escape_string(
                $koneksi,
                trim($_POST['nama_custom'] ?? '')
            );

            $query_kategori_pilih = mysqli_query($koneksi, "
                SELECT id
                FROM t_pelanggaran_kategori
                WHERE id = '$kategori_id'
                AND status_aktif = 1
                LIMIT 1
            ");

            $query_referensi = mysqli_query($koneksi, "
                SELECT id, poin
                FROM t_pelanggaran
                WHERE pelanggaran_kategori_id = '$kategori_id'
                AND status_aktif = 1
                ORDER BY id ASC
                LIMIT 1
            ");

            if ($nama_pelanggaran == '') {

                $pesan = "Nama pelanggaran custom wajib diisi.";
                $jenis_pesan = "danger";

            } elseif (
                !$query_kategori_pilih ||
                mysqli_num_rows($query_kategori_pilih) == 0
            ) {

                $pesan = "Silakan pilih kategori pelanggaran.";
                $jenis_pesan = "danger";

            } elseif (
                !$query_referensi ||
                mysqli_num_rows($query_referensi) == 0
            ) {

                $pesan = "Kategori tersebut belum memiliki jenis pelanggaran aktif untuk referensi.";
                $jenis_pesan = "danger";

            } else {

                $data_referensi = mysqli_fetch_assoc($query_referensi);

                $pelanggaran_id = (int) $data_referensi['id'];
                $poin = (int) $data_referensi['poin'];
            }

        } else {

            $pesan = "Jenis pelanggaran tidak valid.";
            $jenis_pesan = "danger";
        }

        // SIMPAN JIKA TIDAK ADA PESAN ERROR

        if ($pesan == '') {

            $simpan = mysqli_query($koneksi, "
                INSERT INTO t_pelanggaran_siswa
                (
                    tahun_ajaran_id,
                    siswa_id,
                    nama_siswa,
                    kelas_id,
                    nama_kelas,
                    pelanggaran_id,
                    nama_pelanggaran,
                    pelanggaran_kategori_id,
                    guru_id,
                    nama_guru,
                    tanggal,
                    keterangan,
                    poin,
                    tindakan,
                    status
                )
                VALUES
                (
                    '$tahun_ajaran_id',
                    '$siswa_id',
                    '$nama_siswa',
                    '$kelas_id',
                    '$nama_kelas',
                    '$pelanggaran_id',
                    '$nama_pelanggaran',
                    '$kategori_id',
                    '$guru_id',
                    '" . mysqli_real_escape_string($koneksi, $nama_guru) . "',
                    '$tanggal',
                    '$keterangan',
                    '$poin',
                    '-',
                    '$status'
                )
            ");

            if ($simpan) {

                header("Location: catat_pelanggaran.php?kelas_id=$kelas_id&berhasil=1");
                exit;

            } else {

                $pesan = "Gagal menyimpan data: " . mysqli_error($koneksi);
                $jenis_pesan = "danger";
            }
        }
    }
}

// PESAN BERHASIL

if (isset($_GET['berhasil'])) {
    $pesan = "Catatan pelanggaran berhasil disimpan.";
    $jenis_pesan = "success";
}

// DATA PELANGGARAN TERBARU

$query_terbaru = mysqli_query($koneksi, "
    SELECT
        id,
        tanggal,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        poin,
        nama_guru,
        status
    FROM t_pelanggaran_siswa
    ORDER BY id DESC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catat Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.datatables.net/3.1.4/css/dataTables.dataTables.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="style/style.css">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR GURU -->

        <div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

            <h4 class="text-white mb-4">
                Sistem Pelanggaran
            </h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php"
                       class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : 'text-white'; ?>">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="catat_pelanggaran.php"
                       class="nav-link <?= $halaman == 'catat_pelanggaran.php' ? 'active' : 'text-white'; ?>">
                        Catat Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="tindakan.php"
                       class="nav-link <?= $halaman == 'tindakan.php' ? 'active' : 'text-white'; ?>">
                        Tindakan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="riwayat.php"
                       class="nav-link <?= $halaman == 'riwayat.php' ? 'active' : 'text-white'; ?>">
                        Riwayat
                    </a>
                </li>

                <li class="nav-item">
                    <a href="rekap_poin.php"
                       class="nav-link <?= $halaman == 'rekap_poin.php' ? 'active' : 'text-white'; ?>">
                        Rekap Poin
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

        <main class="content-main">
            <div class="page-content">

                <!-- HEADER -->

                <div class="bg-white rounded shadow-sm p-4 mb-4">

                    <h2 class="fw-bold">
                        Catat Pelanggaran Siswa
                    </h2>

                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($nama_login); ?></b>
                    </p>

                </div>

                <!-- PESAN -->

                <?php if ($pesan != "") { ?>
                    <div class="alert alert-<?= $jenis_pesan; ?>">
                        <?= htmlspecialchars($pesan); ?>
                    </div>
                <?php } ?>

                <!-- FORM CATAT PELANGGARAN -->

                <div class="card shadow-sm form-pelanggaran mb-4">

                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-1">Form Catat Pelanggaran</h5>
                        <small class="text-secondary">
                            Lengkapi data pelanggaran siswa dengan benar.
                        </small>
                    </div>

                    <div class="card-body">

                        <!-- PILIH KELAS -->

                        <form method="GET" action="catat_pelanggaran.php" class="mb-4">

                            <div class="kelas-box">

                                <label class="form-label" for="kelas_id">
                                    Kelas Siswa
                                </label>

                                <select name="kelas_id"
                                        id="kelas_id"
                                        class="form-select"
                                        onchange="this.form.submit()"
                                        required>

                                    <option value="">-- Pilih Kelas --</option>

                                    <?php
                                    if ($query_kelas) {
                                        while ($kelas = mysqli_fetch_assoc($query_kelas)) {
                                    ?>
                                        <option value="<?= $kelas['id']; ?>"
                                            <?= ($kelas_id == $kelas['id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($kelas['nama']); ?>
                                        </option>
                                    <?php
                                        }
                                    }
                                    ?>

                                </select>

                                <small class="text-secondary d-block mt-2">
                                    Pilih kelas terlebih dahulu untuk mencari siswa.
                                </small>

                            </div>

                        </form>

                        <!-- FORM SIMPAN -->

                        <form method="POST" action="catat_pelanggaran.php">

                            <input type="hidden" name="kelas_id" value="<?= $kelas_id; ?>">

                            <div class="row g-3">

                                <!-- NAMA SISWA -->

                                <div class="col-md-6">

                                    <label class="form-label" for="cari_siswa">
                                        Nama Siswa
                                    </label>

                                    <div style="position: relative;">

                                        <input type="text"
                                               id="cari_siswa"
                                               class="form-control"
                                               placeholder="Ketik nama atau NIS siswa..."
                                               autocomplete="off"
                                               <?= $kelas_id <= 0 ? 'disabled' : ''; ?>>

                                        <input type="hidden"
                                               name="penempatan_id"
                                               id="penempatan_id">

                                        <div id="hasil_siswa"
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
                                        Ketik nama atau NIS, lalu pilih siswa dari daftar yang muncul.
                                    </small>

                                    <?php if ($kelas_id <= 0) { ?>
                                        <small class="text-danger">
                                            Pilih kelas terlebih dahulu.
                                        </small>
                                    <?php } ?>

                                </div>

                                <!-- GURU PENCATAT -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Guru Pencatat
                                    </label>

                                    <input type="text"
                                           class="form-control readonly-field"
                                           value="<?= htmlspecialchars($nama_guru); ?>"
                                           readonly>

                                </div>

                                <!-- JENIS PELANGGARAN -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Jenis Pelanggaran
                                    </label>

                                    <select name="jenis_input"
                                            id="jenis_input"
                                            class="form-select"
                                            required>

                                        <option value="tersedia">
                                            Pilih dari daftar
                                        </option>

                                        <option value="custom">
                                            + Jenis pelanggaran custom
                                        </option>

                                    </select>

                                </div>

                                <!-- PILIH JENIS YANG TERSEDIA -->

                                <div class="col-md-6" id="box_pelanggaran">

                                    <label class="form-label" for="pelanggaran_id">
                                        Daftar Jenis Pelanggaran
                                    </label>

                                    <select name="pelanggaran_id"
                                            id="pelanggaran_id"
                                            class="form-select">

                                        <option value="">
                                            -- Pilih Jenis Pelanggaran --
                                        </option>

                                        <?php foreach ($daftar_pelanggaran as $item) { ?>

                                            <option
                                                value="<?= $item['id']; ?>"
                                                data-poin="<?= $item['poin']; ?>"
                                                data-kategori="<?= $item['pelanggaran_kategori_id']; ?>">

                                                <?= htmlspecialchars(
                                                    $item['nama'] .
                                                    ' - ' .
                                                    $item['nama_kategori'] .
                                                    ' (' .
                                                    $item['poin'] .
                                                    ' poin)'
                                                ); ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <!-- JENIS CUSTOM -->

                                <div class="col-md-6" id="box_custom" style="display:none;">

                                    <label class="form-label" for="nama_custom">
                                        Nama Pelanggaran Custom
                                    </label>

                                    <input type="text"
                                           name="nama_custom"
                                           id="nama_custom"
                                           class="form-control"
                                           placeholder="Ketik jenis pelanggaran sendiri...">

                                </div>

                                <!-- KATEGORI CUSTOM -->

                                <div class="col-md-6" id="box_kategori" style="display:none;">

                                    <label class="form-label" for="kategori_custom">
                                        Kategori Pelanggaran
                                    </label>

                                    <select name="kategori_custom"
                                            id="kategori_custom"
                                            class="form-select">

                                        <option value="">
                                            -- Pilih Kategori --
                                        </option>

                                        <?php
                                        if ($query_kategori) {
                                            while ($kategori = mysqli_fetch_assoc($query_kategori)) {
                                        ?>

                                            <option value="<?= $kategori['id']; ?>"
                                                    data-poin="<?= (int) ($kategori['poin_referensi'] ?? 0); ?>">

                                                <?= htmlspecialchars($kategori['nama']); ?>

                                            </option>

                                        <?php
                                            }
                                        }
                                        ?>

                                    </select>

                                </div>

                                <!-- POIN -->

                                <div class="col-md-6">

                                    <label class="form-label" for="poin">
                                        Poin Pelanggaran
                                    </label>

                                    <input type="number"
                                           id="poin"
                                           class="form-control readonly-field"
                                           value=""
                                           readonly>

                                    <small class="text-secondary">
                                        Poin otomatis mengikuti jenis pelanggaran yang dipilih.
                                    </small>

                                </div>

                                <!-- TANGGAL -->

                                <div class="col-md-6">

                                    <label class="form-label" for="tanggal">
                                        Tanggal Pelanggaran
                                    </label>

                                    <input type="date"
                                           name="tanggal"
                                           id="tanggal"
                                           class="form-control"
                                           value="<?= date('Y-m-d'); ?>"
                                           required>

                                </div>

                                <!-- STATUS -->

                                <div class="col-md-6">

                                    <label class="form-label" for="status">
                                        Status
                                    </label>

                                    <select name="status"
                                            id="status"
                                            class="form-select"
                                            required>

                                        <option value="Pending">Pending</option>
                                        <option value="Diproses">Diproses</option>
                                        <option value="Selesai">Selesai</option>

                                    </select>

                                </div>

                                <!-- TAHUN AJARAN -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Tahun Ajaran
                                    </label>

                                    <input type="text"
                                           class="form-control readonly-field"
                                           value="<?= htmlspecialchars($nama_tahun); ?>"
                                           readonly>

                                </div>

                                <!-- KETERANGAN -->

                                <div class="col-12">

                                    <label class="form-label" for="keterangan">
                                        Keterangan
                                    </label>

                                    <textarea name="keterangan"
                                              id="keterangan"
                                              class="form-control"
                                              rows="4"
                                              placeholder="Tuliskan keterangan pelanggaran (opsional)..."></textarea>

                                </div>

                            </div>

                            <div class="form-divider"></div>

                            <div class="form-actions d-flex justify-content-end gap-2">

                                <button type="reset"
                                        class="btn btn-light border btn-form">
                                    Reset Form
                                </button>

                                <button type="submit"
                                        name="simpan"
                                        class="btn btn-primary btn-form"
                                        <?= $kelas_id <= 0 ? 'disabled' : ''; ?>>
                                    Simpan Pelanggaran
                                </button>

                            </div>

                        </form>

                    </div>
                </div>

                <!-- PELANGGARAN TERBARU -->

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-white py-3">

                        <h5 class="fw-bold mb-1">
                            Pelanggaran Terbaru
                        </h5>

                        <small class="text-secondary">
                            Daftar catatan pelanggaran siswa.
                        </small>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped table-hover align-middle"
                                   id="tabelPelanggaran">

                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Poin</th>
                                        <th>Guru Pencatat</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    $no = 1;

                                    if ($query_terbaru && mysqli_num_rows($query_terbaru) > 0) {

                                        while ($data = mysqli_fetch_assoc($query_terbaru)) {
                                    ?>

                                        <tr>

                                            <td><?= $no++; ?></td>

                                            <td>
                                                <?= htmlspecialchars($data['tanggal'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <span class="badge bg-danger">
                                                    <?= htmlspecialchars($data['poin'] ?? '0'); ?>
                                                    poin
                                                </span>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?php
                                                if (($data['status'] ?? '') == 'Selesai') {
                                                    echo '<span class="badge bg-success">Selesai</span>';
                                                } elseif (($data['status'] ?? '') == 'Diproses') {
                                                    echo '<span class="badge bg-warning text-dark">Diproses</span>';
                                                } else {
                                                    echo '<span class="badge bg-secondary">' .
                                                        htmlspecialchars($data['status'] ?? 'Pending') .
                                                        '</span>';
                                                }
                                                ?>
                                            </td>

                                        </tr>

                                    <?php
                                        }
                                    }
                                    ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->

                <div class="text-center text-secondary py-3">
                    <small>© 2026 Sistem Pelanggaran Siswa</small>
                </div>

            </div>
        </main>

    </div>
</div>

<!-- DATATABLES DAN BOOTSTRAP -->

<script src="https://cdn.datatables.net/3.1.4/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- JAVASCRIPT -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    // PENCARIAN NAMA SISWA

    const inputSiswa = document.getElementById('cari_siswa');
    const hasilSiswa = document.getElementById('hasil_siswa');
    const penempatanId = document.getElementById('penempatan_id');

    let waktuPencarian;

    if (inputSiswa) {

        inputSiswa.addEventListener('input', function () {

            const kataKunci = this.value.trim();

            penempatanId.value = '';

            clearTimeout(waktuPencarian);

            hasilSiswa.innerHTML = '';
            hasilSiswa.style.display = 'none';

            if (kataKunci.length === 0) {
                return;
            }

            waktuPencarian = setTimeout(function () {

                fetch(
                    'catat_pelanggaran.php?ajax=cari_siswa&kelas_id=<?= $kelas_id; ?>&q=' +
                    encodeURIComponent(kataKunci)
                )
                .then(response => response.json())
                .then(data => {

                    hasilSiswa.innerHTML = '';

                    if (data.length === 0) {

                        hasilSiswa.innerHTML =
                            '<div class="list-group-item text-secondary">Siswa tidak ditemukan</div>';

                        hasilSiswa.style.display = 'block';
                        return;
                    }

                    data.forEach(function (siswa) {

                        const pilihan = document.createElement('button');

                        pilihan.type = 'button';
                        pilihan.className = 'list-group-item list-group-item-action';

                        pilihan.textContent =
                            siswa.nama_siswa + ' - ' + siswa.nis;

                        pilihan.addEventListener('click', function () {

                            inputSiswa.value =
                                siswa.nama_siswa + ' - ' + siswa.nis;

                            penempatanId.value = siswa.penempatan_id;

                            hasilSiswa.style.display = 'none';
                        });

                        hasilSiswa.appendChild(pilihan);
                    });

                    hasilSiswa.style.display = 'block';

                })
                .catch(error => {
                    console.error('Pencarian siswa gagal:', error);
                });

            }, 200);
        });

        document.addEventListener('click', function (event) {

            if (
                !inputSiswa.contains(event.target) &&
                !hasilSiswa.contains(event.target)
            ) {
                hasilSiswa.style.display = 'none';
            }
        });
    }

    // JENIS PELANGGARAN TERSEDIA / CUSTOM

    const jenisInput = document.getElementById('jenis_input');
    const boxPelanggaran = document.getElementById('box_pelanggaran');
    const boxCustom = document.getElementById('box_custom');
    const boxKategori = document.getElementById('box_kategori');

    const selectPelanggaran = document.getElementById('pelanggaran_id');
    const namaCustom = document.getElementById('nama_custom');
    const kategoriCustom = document.getElementById('kategori_custom');
    const inputPoin = document.getElementById('poin');

    function aturJenisPelanggaran() {

        if (jenisInput.value === 'custom') {

            boxPelanggaran.style.display = 'none';
            boxCustom.style.display = 'block';
            boxKategori.style.display = 'block';

            selectPelanggaran.required = false;
            namaCustom.required = true;
            kategoriCustom.required = true;

        } else {

            boxPelanggaran.style.display = 'block';
            boxCustom.style.display = 'none';
            boxKategori.style.display = 'none';

            selectPelanggaran.required = true;
            namaCustom.required = false;
            kategoriCustom.required = false;

        }
    }

    jenisInput.addEventListener('change', function () {

        aturJenisPelanggaran();
        inputPoin.value = '';

    });

    selectPelanggaran.addEventListener('change', function () {

        const pilihan = this.options[this.selectedIndex];

        if (pilihan && pilihan.value !== '' && pilihan.dataset.poin !== undefined) {
            inputPoin.value = pilihan.dataset.poin;
        } else {
            inputPoin.value = '';
        }

    });

    kategoriCustom.addEventListener('change', function () {

        const pilihan = this.options[this.selectedIndex];

        if (pilihan && pilihan.value !== '' && pilihan.dataset.poin !== undefined) {
            inputPoin.value = pilihan.dataset.poin;
        } else {
            inputPoin.value = '';
        }

    });

    aturJenisPelanggaran();

    // RESET FORM

    const formSimpan = document.querySelector('form[method="POST"]');

    if (formSimpan) {

        formSimpan.addEventListener('reset', function () {

            setTimeout(function () {

                inputSiswa.value = '';
                penempatanId.value = '';

                hasilSiswa.innerHTML = '';
                hasilSiswa.style.display = 'none';

                jenisInput.value = 'tersedia';

                selectPelanggaran.value = '';
                inputPoin.value = '';

                aturJenisPelanggaran();

            }, 10);
        });
    }

    // DATATABLE PELANGGARAN TERBARU

    const tabel = document.querySelector('#tabelPelanggaran');

    if (tabel && typeof DataTable !== 'undefined') {

        new DataTable(tabel, {

            pageLength: 10,

            lengthMenu: [5, 10, 25, 50, 100],

            order: [],

            language: {

                search: 'Cari:',
                searchPlaceholder: 'Cari data...',

                lengthMenu: 'Tampilkan _MENU_ data',

                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

                infoEmpty: 'Tidak ada data',

                infoFiltered: '(difilter dari _MAX_ total data)',

                zeroRecords: 'Data tidak ditemukan',

                emptyTable: 'Belum ada catatan pelanggaran',

                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya'
                }

            }

        });

    } else if (typeof DataTable === 'undefined') {

        console.error('DataTables belum berhasil dimuat.');

    }

});
</script>

</body>
</html>