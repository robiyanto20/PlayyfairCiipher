<?php
session_start(); // Pindahkan session_start ke paling awal

include('playfair.php');

$conn = createConnection();

$registrationMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($conn, $_POST['name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['re_pass']);

    // Hapus kondisi validasi password
    // if (!isPasswordValid($password)) {
    //     $registrationMessage = '<div class="alert alert-danger">Password harus minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter khusus.</div>';
    // } 
    if ($password !== $confirmPassword) {
        $registrationMessage = '<div class="alert alert-danger">Konfirmasi password tidak sesuai.</div>';
    } else {
        // Enkripsi password dengan Playfair Cipher
        $key = 'robi'; // Ganti dengan kunci Playfair yang diinginkan
        $encryptedPassword = playfairEncrypt($password, $key);

        // Dekripsi password untuk disimpan di kolom 'dekripsi'
        $decryptedPassword = playfairDecrypt($encryptedPassword, $key);

        $stmt = $conn->prepare("INSERT INTO users (username, password, dekripsi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $encryptedPassword, $decryptedPassword);

        if ($stmt->execute()) {
            $registrationMessage = '<div class="alert alert-success">Registrasi berhasil!</div>';
        } else {
            $registrationMessage = '<div class="alert alert-danger">Error: Registrasi gagal. Coba lagi nanti atau hubungi administrator.</div>';
        }

        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <!-- CSS -->
    <link rel="stylesheet" href="css/registrasi.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="main">
        <section class="signup">
            <div class="container">
                <div class="signup-content">
                    <div class="signup-form">
                        <h2 class="form-title">Sign up</h2>
                        <form method="POST" class="register-form" id="register-form">
                            <?php echo $registrationMessage; ?>
                            <div class="form-group">
                                <label for="name"></label>
                                <input type="text" name="name" id="name" placeholder="Username" />
                            </div>
                            <div class="form-group position-relative">
                                <input type="password" name="password" id="InputForPassword" placeholder="Password" class="form-control">
                                <!-- Ikon mata yang dapat diklik -->
                                <i id="password-toggle-icon"
                                    class="fas fa-eye-slash position-absolute end-0 top-50 translate-middle-y"
                                    onclick="togglePasswordVisibility('InputForPassword')" style="cursor: pointer;"></i>
                            </div>
                            <div class="form-group position-relative">
                                <input type="password" name="re_pass" id="re_pass" placeholder="Ulang Password" class="form-control">
                                <!-- Ikon mata yang dapat diklik -->
                                <i id="confirm-password-toggle-icon"
                                    class="fas fa-eye-slash position-absolute end-0 top-50 translate-middle-y"
                                    onclick="togglePasswordVisibility('re_pass')" style="cursor: pointer;"></i>
                            </div>
                            <div class="form-group form-button">
                                <input type="submit" name="signup" id="signup" class="form-submit" value="Register" />
                            </div>
                            <p style="margin-top: 10px; text-align: center;">Sudah Punya Akun? <a
                                    href="login.php">Login</a></p>
                        </form>
                    </div>
                    
                </div>
            </div>
        </section>
    </div>
    <script>
        function togglePasswordVisibility(inputId) {
            var passwordInput = document.getElementById(inputId);
            var passwordIcon = document.getElementById(inputId === 'InputForPassword' ? 'password-toggle-icon' : 'confirm-password-toggle-icon');

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
