<?php
function checkIfExists($email_value, $phone_value) {    
    $servername = "localhost:3306";
    $username = "root";
    $dbpassword = "";
    $dbname = "php_auth";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;port=3306", $username, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email OR phone = :phone");
        $stmt->bindParam(':email', $email_value);
        $stmt->bindParam(':phone', $phone_value);
        $stmt->execute();

        // check if the email or phone number already exists in the database
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    } catch(PDOException $e) {
        echo $e->getMessage();
    }

    $conn = null;
}

// Encryption function
function encrypt_cookie_value($value, $key) {
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($value, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
    return $ciphertext;
}

// Decryption function
function decrypt_cookie_value($ciphertext, $key) {
    $c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    if (hash_equals($hmac, $calcmac)) {
        return $original_plaintext;
    }
    return false;
}
?>
