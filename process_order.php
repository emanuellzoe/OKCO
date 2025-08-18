<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Page</title>
    <link rel="stylesheet" href="proses.css">
</head>
<body>
    <?php
    session_start();

    if (!isset($_SESSION['id_user'])) {
        die("You must be logged in to place an order.");
    }
    
    include 'koneksi.php';
    // $servername = "localhost";
    // $username = "root";
    // $password = "";
    // $dbname = "progcoba2";

    // // Create connection
    // $conn = new mysqli($servername, $username, $password, $dbname);

    // // Check connection
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_tiket = isset($_POST['id_tiket']) ? intval($_POST['id_tiket']) : 0;
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;


        // Fetch ticket details
        $sql = "SELECT * FROM tiket WHERE id_tiket = $id_tiket";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $ticketData = $result->fetch_assoc();
        } else {
            die("Event not found.");
        }

        // Update stock based on ticket type
        $newStock = 0;
        if ($type == 'Tribun') {
            $newStock = $ticketData['stok_Tribun'] - $quantity;
            $updateSql = "UPDATE tiket SET stok_Tribun = $newStock WHERE id_tiket = $id_tiket";
        } elseif ($type == 'Festival') {
            $newStock = $ticketData['stok_Festival'] - $quantity;
            $updateSql = "UPDATE tiket SET stok_Festival = $newStock WHERE id_tiket = $id_tiket";
        } elseif ($type == 'VIP') {
            $newStock = $ticketData['stok_VIP'] - $quantity;
            $updateSql = "UPDATE tiket SET stok_VIP = $newStock WHERE id_tiket = $id_tiket";
        } else {
            die("Invalid ticket type.");
        }

        if ($newStock < 0) {
            die("Insufficient stock for the selected ticket type.");
        }

        if (!$conn->query($updateSql)) {
            die("Error updating ticket stock: " . $conn->error);
        }

        // Process each ticket for the order
        for ($i = 0; $i < $quantity; $i++) {
            $nama_lengkap = isset($_POST['nama_lengkap_' . $i]) ? $_POST['nama_lengkap_' . $i] : '';
            $nik = isset($_POST['nik_' . $i]) ? intval($_POST['nik_' . $i]) : 0;
            $email = isset($_POST['email_' . $i]) ? $_POST['email_' . $i] : '';
            // $nomor = isset($_POST['nomor_' . $i]) ? intval($_POST['nomor_' . $i]) : 0;
            $nomor = isset($_POST['nomor_' . $i]) ? $_POST['nomor_' . $i] : '';
            if (!empty($nomor) && substr($nomor, 0, 1) !== '0') {
                // Menambahkan digit 0 di awal jika tidak dimulai dengan 0
                $nomor = '0' . $nomor;
            }

            if (!empty($nama_lengkap) && $nik > 0 && !empty($email) && $nomor > 0) {
                // Insert data into invoice table
                $sql = "INSERT INTO invoice (nik, nama, no_whatsapp, email, id_user, id_tiket) VALUES ('$nik', '$nama_lengkap', '$nomor', '$email', '{$_SESSION['id_user']}', '$id_tiket')";

                if (!$conn->query($sql)) {
                    die("Error inserting invoice: " . $conn->error);
                }
            } else {
                die("Invalid input data.");
            }
        }

        echo '<div class="pesan-sukses">
                <h2>Tiket Berhasil Dipesan!</h2>
                <p>Terima kasih telah memesan tiket.</p>
                <a href="index.php" class="btn-kembali">Kembali ke Home</a>
              </div>';
    } else {
        die("Invalid request method.");
    }

    $conn->close();
    ?>
</body>
</html>
