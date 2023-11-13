<?php
session_start(); 

// Include file functions.php yang berisi fungsi-fungsi yang diperlukan
include('playfair.php');

// Membuat koneksi ke database
$conn = createConnection();

// Pesan untuk menampilkan informasi hasil login
$loginMessage = '';

// Memproses form login saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signin'])) {
    // Mengamankan input dari form
    $username = mysqli_real_escape_string($conn, $_POST['your_name']);
    $password = mysqli_real_escape_string($conn, $_POST['your_pass']);

    // Mengeksekusi query untuk mendapatkan informasi pengguna berdasarkan username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika data pengguna dengan username tersebut ditemukan
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Mengenkripsi password yang dimasukkan untuk dibandingkan dengan password terenkripsi di database
        $encrypted_password = playfairEncrypt($password, 'robi');
        // Membandingkan password yang dimasukkan dengan password terenkripsi di database
        if ($row['password'] === $encrypted_password) {
            // Jika cocok, mengeset session dan redirect ke halaman utama
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        } else {
            // Jika password tidak cocok, menampilkan pesan kesalahan
            $loginMessage = '<div class="alert alert-danger">Password salah.</div>';
        }
    } else {
        // Jika username tidak ditemukan, menampilkan pesan kesalahan
        $loginMessage = '<div class="alert alert-danger">Username tidak valid.</div>';
    }

    // Menutup statement dan koneksi database
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- CSS -->
    <link rel="stylesheet" href="css/lojin.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="main">
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                 
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">Sign In</h2>
                        <!-- Menampilkan pesan kesalahan -->
                        <?php echo $loginMessage; ?>
                        <form method="POST" class="register-form" id="login-form">
                            <div class="form-group">
                                <label for="your_name"></label>
                                <input type="text" name="your_name" id="your_name" placeholder="Username" />
                            </div>
                            <div class="form-group position-relative">
                                <input type="password" name="your_pass" id="your_pass" placeholder="Password" class="form-control">
                                <i id="password-toggle-icon" class="fas fa-eye-slash" onclick="togglePasswordVisibility('your_pass')"></i>
                            </div>
                            <div class="form-group form-button">
                                <input type="submit" name="signin" id="signin" class="form-submit" value="Masuk" />
                            </div>
                            <p style="margin-top: 10px; text-align: center;">Belum punya akun? <a href="register.php">Register</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Fungsi untuk menoggle visibility password
        function togglePasswordVisibility(inputId) {
            var passwordInput = document.getElementById(inputId);
            var passwordIcon = document.getElementById('password-toggle-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>

</html>
