<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));
$id_user = (int) ($_SESSION['id'] ?? 0);

if ($role != 'guru') {
    echo "Anda tidak memiliki akses";
    exit;
}

// CEK GURU LOGIN
$query_guru = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_guru
    WHERE user_id = '$id_user'
    AND status_aktif = 1
    LIMIT 1
");

if (!$query_guru || mysqli_num_rows($query_guru) == 0) {
    echo "Data guru tidak ditemukan";
    exit;
}

$data_guru = mysqli_fetch_assoc($query_guru);
$guru_id = $data_guru['id'];
$nama_guru = $data_guru['nama'];

// CEK TAHUN AJARAN AKTIF
$query_tahun = mysqli_query($koneksi, "
    SELECT id, nama_tahun_ajaran
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    LIMIT 1
");

if (!$query_tahun || mysqli_num_rows($query_tahun) == 0) {
    echo "Tahun ajaran aktif tidak ditemukan";
    exit;
}

$data_tahun = mysqli_fetch_assoc($query_tahun);
$tahun_ajaran_id = $data_tahun['id'];

// CEK WALI KELAS
$query_wali = mysqli_query($koneksi, "
    SELECT kelas_id
    FROM t_wali_kelas
    WHERE guru_id = '$guru_id'
    AND tahun_ajaran_id = '$tahun_ajaran_id'
    AND status_aktif = 1
    LIMIT 1
");

if (!$query_wali || mysqli_num_rows($query_wali) == 0) {
    echo "Halaman ini hanya dapat diakses oleh Wali Kelas";
    exit;
}

$data_wali = mysqli_fetch_assoc($query_wali);
$kelas_wali_id = $data_wali['kelas_id'];

// ID PELANGGARAN
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    echo "Data pelanggaran tidak valid";
    exit;
}

// AMBIL DATA PELANGGARAN DAN SISWA WALI KELAS
$query_surat = mysqli_query($koneksi, "
    SELECT
        ps.*,
        s.nis,
        k.nama AS nama_kelas,
        ta.nama_tahun_ajaran
    FROM t_pelanggaran_siswa ps
    JOIN t_siswa s
        ON ps.siswa_id = s.id
    JOIN t_kelas_siswa ks
        ON ks.siswa_id = s.id
    JOIN t_kelas k
        ON k.id = ks.kelas_id
    JOIN t_tahun_ajaran ta
        ON ta.id = ks.tahun_ajaran_id
    WHERE ps.id = '$id'
    AND ks.kelas_id = '$kelas_wali_id'
    AND ks.tahun_ajaran_id = '$tahun_ajaran_id'
    AND ks.status_aktif = 1
    AND s.status_aktif = 1
    AND k.status_aktif = 1
    AND ta.status_aktif = 1
    LIMIT 1
");

if (!$query_surat || mysqli_num_rows($query_surat) == 0) {
    echo "Data pelanggaran tidak ditemukan atau bukan siswa wali kelas Anda";
    exit;
}

$data = mysqli_fetch_assoc($query_surat);

// TOTAL POIN SISWA
$siswa_id = $data['siswa_id'];

$query_poin = mysqli_query($koneksi, "
    SELECT SUM(poin) AS total_poin
    FROM t_pelanggaran_siswa
    WHERE siswa_id = '$siswa_id'
    AND tahun_ajaran_id = '$tahun_ajaran_id'
");

$data_poin = mysqli_fetch_assoc($query_poin);
$total_poin = $data_poin['total_poin'] ?? 0;

$tanggal_surat = date('d F Y');
$nomor_surat = "421.5/SMKM/PK/".date('m')."/".date('Y');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Surat Pemanggilan Orang Tua</title>

    <style>
        @page {
            size: A4;
            margin: 2cm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            color: #000;
            margin: 0;
        }

        .surat {
            width: 100%;
        }

        .kop {
            display: flex;
            align-items: center;
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
        }

        .kop-text {
            flex: 1;
            font-size: 13pt;
            font-weight: bold;
            line-height: 1.3;
        }

        .kop-text small {
            font-size: 10pt;
            font-weight: normal;
        }

        .nomor {
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .isi {
            line-height: 1.5;
            text-align: justify;
        }

        .ttd {
            width: 45%;
            margin-left: auto;
            text-align: center;
            margin-top: 35px;
        }

        .nama_ttd {
            margin-top: 75px;
            font-weight: bold;
            text-decoration: underline;
        }

        .tombol {
            margin-bottom: 20px;
        }

        @media print {
            .tombol {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="tombol">
    <button onclick="window.print()">Cetak Surat</button>
    <button onclick="window.close()">Tutup</button>
</div>

<div class="surat">

    <!-- KOP SURAT -->
    <div class="kop">

        <img src="img/logo_smk_muhammadiyah.png" class="logo">

        <div class="kop-text">
            MAJELIS PENDIDIKAN DASAR MENENGAH<br>
            DAN PENDIDIKAN NONFORMAL<br>
            PIMPINAN DAERAH MUHAMMADIYAH KOTA TASIKMALAYA<br>
            SMK MUHAMMADIYAH TASIKMALAYA<br>

            <small>
                Jl. Rumah Sakit Umum No. 29 Kota Tasikmalaya
            </small>
        </div>

    </div>

    <!-- NOMOR SURAT -->
    <div class="nomor">
        Nomor : <?= htmlspecialchars($nomor_surat); ?><br>
        Lamp. : -<br>
        Hal : Pemanggilan Orang Tua/Wali Siswa
    </div>

    <!-- TUJUAN -->
    <div style="margin-left: 80px; margin-bottom: 25px;">
        Kepada Yth.<br>
        Orang Tua/Wali dari:<br>

        <b><?= htmlspecialchars($data['nama_siswa']); ?></b><br>

        Kelas <?= htmlspecialchars($data['nama_kelas']); ?><br>

        SMK Muhammadiyah Tasikmalaya<br>
        di<br>
        Tempat
    </div>

    <!-- ISI SURAT -->
    <div class="isi">

        Assalamu'alaikum Wr. Wb.

        <p>
            Dengan hormat, semoga Bapak/Ibu senantiasa berada dalam
            keadaan sehat wal'afiat serta diberikan kelancaran dalam
            menjalankan aktivitas sehari-hari.
        </p>

        <p>
            Sehubungan dengan adanya pelanggaran tata tertib sekolah
            yang dilakukan oleh putra/putri Bapak/Ibu, kami bermaksud
            mengundang Bapak/Ibu untuk hadir di sekolah guna membahas
            permasalahan tersebut serta melakukan pembinaan bersama.
        </p>

        <p>
            Adapun data pelanggaran siswa adalah sebagai berikut:
        </p>

        <table width="100%" border="1" cellpadding="6"
               cellspacing="0" style="border-collapse: collapse;">

            <tr>
                <td width="35%">Nama Siswa</td>
                <td><?= htmlspecialchars($data['nama_siswa']); ?></td>
            </tr>

            <tr>
                <td>NIS</td>
                <td><?= htmlspecialchars($data['nis'] ?? '-'); ?></td>
            </tr>

            <tr>
                <td>Kelas</td>
                <td><?= htmlspecialchars($data['nama_kelas']); ?></td>
            </tr>

            <tr>
                <td>Pelanggaran</td>
                <td><?= htmlspecialchars($data['nama_pelanggaran']); ?></td>
            </tr>

            <tr>
                <td>Tanggal Pelanggaran</td>
                <td><?= htmlspecialchars($data['tanggal']); ?></td>
            </tr>

            <tr>
                <td>Poin Pelanggaran</td>
                <td><?= htmlspecialchars($data['poin']); ?></td>
            </tr>

            <tr>
                <td>Total Poin Tahun Ajaran</td>
                <td><?= htmlspecialchars($total_poin); ?></td>
            </tr>

        </table>

        <p>
            Demikian surat pemanggilan ini kami sampaikan.
            Atas perhatian dan kerja sama Bapak/Ibu, kami mengucapkan
            terima kasih.
        </p>

        Wassalamu'alaikum Wr. Wb.

    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd">

        Tasikmalaya, <?= htmlspecialchars($tanggal_surat); ?><br>
        Wali Kelas,

        <div class="nama_ttd">
            <?= htmlspecialchars($nama_guru); ?>
        </div>

    </div>

</div>

</body>
</html>