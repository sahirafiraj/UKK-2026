<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card text-center mb-3" style="width: 25rem;">
        <div class="card-body">
            
            <h2 class="card-title">Login Sistem Informasi Pelanggaran Siswa</h2>

                <?php
                if (isset($_SESSION['pesan_error'])) {
                    echo "<p class='text-danger'>" .
                         htmlspecialchars($_SESSION['pesan_error']) .
                         "</p>";

                    unset($_SESSION['pesan_error']);
                }
                ?>

                <form action="proses_login.php" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Email</label>

                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>

                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <button type="submit"
                            class="btn btn-primary">
                        Login
                    </button>

                </form>

            </div>

        </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>

</body>
</html>