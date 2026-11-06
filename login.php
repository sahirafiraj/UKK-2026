<?php
session_start();

if (isset($_SESSION['login'])) {

    if ($_SESSION['role'] == 'admin') {
        header("Location: dashboard.php");
        exit;
    }

    if ($_SESSION['role'] == 'guru') {
        header("Location: dashboard_guru.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="style/style.css">

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background-color: #f1f5f9;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(23, 37, 84, 0.08);
        }

        .logo-sekolah {
            display: block;
            width: 85px;
            height: 85px;
            object-fit: contain;
            margin: 0 auto 15px;
        }

        .judul-login {
            color: #172554;
            font-size: 23px;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 8px;
            text-align: center;
        }

        .nama-sekolah {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 26px;
            text-align: center;
        }

        .form-label {
            color: #172554;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            min-height: 45px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .btn-login {
            width: 100%;
            min-height: 44px;
            padding: 10px;
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 7px;
            font-size: 15px;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }

        .btn-login:hover {
            background-color: #1d4ed8;
            color: #ffffff;
        }

        .footer-login {
            color: #94a3b8;
            font-size: 12px;
            margin-top: 22px;
            margin-bottom: 0;
            text-align: center;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 15px;
            }

            .login-card .card-body {
                padding: 24px !important;
            }

            .judul-login {
                font-size: 21px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="card login-card">

            <div class="card-body p-4">

                <img
                    src="img/logo_sekolah.png"
                    alt="Logo SMK Muhammadiyah Tasikmalaya"
                    class="logo-sekolah"
                >

                <h2 class="judul-login">
                    Sistem Informasi<br>
                    Pelanggaran Siswa
                </h2>

                <p class="nama-sekolah">
                    SMK Muhammadiyah Tasikmalaya
                </p>

                <?php
                if (isset($_SESSION['pesan_error'])) {
                    echo "<div class='alert alert-danger py-2 small text-start' role='alert'>"
                        . htmlspecialchars($_SESSION['pesan_error'])
                        . "</div>";

                    unset($_SESSION['pesan_error']);
                }
                ?>

                <form action="proses_login.php" method="POST">

                    <div class="mb-3 text-start">

                        <label for="email" class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="Masukkan email"
                            autocomplete="username"
                            required
                        >

                    </div>

                    <div class="mb-4 text-start">

                        <label for="password" class="form-label">
                            Password
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >

                    </div>

                    <button type="submit" class="btn btn-login">
                        Login
                    </button>

                </form>

                <p class="footer-login">
                    © 2026 Sistem Informasi Pelanggaran Siswa
                </p>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>