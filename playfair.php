<?php
// Fungsi untuk mengenkripsi teks menggunakan Playfair Cipher
function playfairEncrypt($plaintext, $key)
{
    $matrix = generatePlayfairMatrix($key);
    $plaintext = preg_replace('/[^a-zA-Z]/', '', strtoupper($plaintext));

    // Gantilah huruf 'j' dengan 'i'
    $plaintext = str_replace('J', 'I', $plaintext);

    // Jika panjang teks ganjil, tambahkan karakter 'X' di akhir
    if (strlen($plaintext) % 2 != 0) {
        $plaintext .= 'X';
    }

    $ciphertext = '';

    for ($i = 0; $i < strlen($plaintext); $i += 2) {
        $char1 = $plaintext[$i];
        $char2 = $plaintext[$i + 1];

        if ($char1 == $char2) {
            $char2 = 'X';
            $i--; // Move back one position to process the same character again
        }

        $ciphertext .= playfairEncryptInternal($char1, $char2, $matrix);
    }

    return $ciphertext;
}

function playfairDecrypt($ciphertext, $key)
{
    $matrix = generatePlayfairMatrix($key);
    $ciphertext = preg_replace('/[^a-zA-Z]/', '', strtoupper($ciphertext));
    $plaintext = '';

    // Proses dekripsi per dua karakter
    for ($i = 0; $i < strlen($ciphertext); $i += 2) {
        $char1 = $ciphertext[$i];
        $char2 = $ciphertext[$i + 1];

        list($row1, $col1) = findCharPosition($matrix, $char1);
        list($row2, $col2) = findCharPosition($matrix, $char2);

        if ($row1 == $row2) {
            $plaintext .= $matrix[$row1][($col1 - 1 + 5) % 5];
            $plaintext .= $matrix[$row2][($col2 - 1 + 5) % 5];
        } elseif ($col1 == $col2) {
            $plaintext .= $matrix[($row1 - 1 + 5) % 5][$col1];
            $plaintext .= $matrix[($row2 - 1 + 5) % 5][$col2];
        } else {
            $plaintext .= $matrix[$row1][$col2];
            $plaintext .= $matrix[$row2][$col1];
        }
    }

    // Hapus karakter 'X' dari akhir plaintext jika ditambahkan saat enkripsi
    if (strlen($ciphertext) % 2 != 0) {
        $plaintext = rtrim($plaintext, 'X');
    }

    return $plaintext;
}

// Fungsi internal untuk mendekripsi dua karakter
function playfairDecryptInternal($ciphertext, $matrix)
{
    $ciphertext = preg_replace('/[^a-zA-Z]/', '', strtoupper($ciphertext));
    $plaintext = '';

    // Proses dekripsi per dua karakter
    for ($i = 0; $i < strlen($ciphertext); $i += 2) {
        $char1 = $ciphertext[$i];
        $char2 = $ciphertext[$i + 1];

        list($row1, $col1) = findCharPosition($matrix, $char1);
        list($row2, $col2) = findCharPosition($matrix, $char2);

        if ($row1 == $row2) {
            $plaintext .= $matrix[$row1][($col1 - 1 + 5) % 5];
            $plaintext .= $matrix[$row2][($col2 - 1 + 5) % 5];
        } elseif ($col1 == $col2) {
            $plaintext .= $matrix[($row1 - 1 + 5) % 5][$col1];
            $plaintext .= $matrix[($row2 - 1 + 5) % 5][$col2];
        } else {
            $plaintext .= $matrix[$row1][$col2];
            $plaintext .= $matrix[$row2][$col1];
        }
    }

    // Hapus karakter 'X' dari akhir plaintext jika ditambahkan saat enkripsi
    if (strlen($ciphertext) % 2 != 0) {
        $plaintext = rtrim($plaintext, 'X');
    }

    // Sisipkan 'X' di antara huruf yang sama
    $plaintext = preg_replace('/(.)\1/', '$1X$1', $plaintext);

    return $plaintext;
}

/**
 * Fungsi untuk menghasilkan matriks Playfair berdasarkan kunci.
 *
 * @param string $key Kunci untuk membuat matriks Playfair.
 * @return array Matriks Playfair yang dihasilkan.
 */
function generatePlayfairMatrix($key)
{
    $key = preg_replace('/[^a-zA-Z]/', '', strtoupper($key));
    $key = str_replace('J', 'I', $key);
    $key = array_unique(str_split($key));
    $alphabet = range('A', 'Z');
    $matrix = [];

    foreach ($key as $k) {
        if ($k == 'I') {
            $k = 'J';
        }
        $matrix[] = $k;
        $alphabet = array_diff($alphabet, [$k]);
    }

    foreach ($alphabet as $a) {
        if ($a == 'J') {
            continue; // Skip J, as it is treated the same as I
        }
        $matrix[] = $a;
    }

    return array_chunk($matrix, 5);
}

/**
 * Fungsi untuk menemukan posisi karakter pada matriks Playfair.
 *
 * @param array $matrix Matriks Playfair.
 * @param string $char Karakter yang akan dicari.
 * @return array Posisi karakter dalam matriks [row, col].
 */
function findCharPosition($matrix, $char)
{
    foreach ($matrix as $row => $cols) {
        if (in_array($char, $cols)) {
            $col = array_search($char, $cols);
            return [$row, $col];
        }
    }
}

/**
 * Fungsi internal untuk mengenkripsi dua karakter.
 *
 * @param string $char1 Karakter pertama.
 * @param string $char2 Karakter kedua.
 * @param array $matrix Matriks Playfair.
 * @return string Teks terenkripsi dari dua karakter.
 */
function playfairEncryptInternal($char1, $char2, $matrix)
{
    list($row1, $col1) = findCharPosition($matrix, $char1);
    list($row2, $col2) = findCharPosition($matrix, $char2);

    if ($row1 == $row2) {
        $ciphertext = $matrix[$row1][($col1 + 1) % 5] . $matrix[$row2][($col2 + 1) % 5];
    } elseif ($col1 == $col2) {
        $ciphertext = $matrix[($row1 + 1) % 5][$col1] . $matrix[($row2 + 1) % 5][$col2];
    } else {
        $ciphertext = $matrix[$row1][$col2] . $matrix[$row2][$col1];
    }

    return $ciphertext;
}

/**
 * Fungsi untuk memeriksa apakah kata sandi memenuhi kriteria keamanan.
 *
 * @param string $password Kata sandi yang akan diperiksa.
 * @return bool True jika kata sandi memenuhi kriteria, false sebaliknya.
 */
function isPasswordValid($password)
{
    if (strlen($password) < 8) {
        return false;
    }

    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }

    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }

    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }

    if (!preg_match('/[!@#\$%\^&\*\(\)_\+\-=\[\]\{\};:\'",<>\.\/?\\|`~]/', $password)) {
        return false;
    }

    return true;
}

/**
 * Fungsi untuk membuat koneksi ke database.
 *
 * @return mysqli Koneksi database.
 */
function createConnection()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "playfair_cipher";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
