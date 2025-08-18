<?php

// Menyertakan file untuk koneksi database.
include_once "dbconnect.php";

 // Memulai sesi untuk melacak data pengguna selama mereka berada di situs web.
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDTH-Payment</title>
    <link rel="stylesheet" href="Pembayaran.css">
    <link href="https://fonts.googleapis.com/css?family=Cabin|Indie+Flower|Inknut+Antiqua|Lora|Ravi+Prakash" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <script src="https://kit.fontawesome.com/4592f70558.js" crossorigin="anonymous"></script>
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo">
            <a href="Home.php"><img src="Foto/I.png" alt=""></a>
        </div>
        <ul>
            <li><a href="Home.php">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Service</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="kelolapesanan.php">My Ticket</a></li>
        </ul>
        <div class="profile">
            <i class="fas fa-user" id="profileIcon"></i>
            <div class="dropdown-menu" id="dropdownMenu">
                <?php if (isset($_SESSION['namaLengkap'])): ?>
                    <p><?php echo htmlspecialchars($_SESSION['namaLengkap']); ?></p>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="Login.php">Login/SignUp</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['name_1'])) {
    require 'dbconnect.php';

    // Mengambil data jumlah tiket dari form yang dikirimkan.
    $quantities = $_POST['quantity'];

     // Array untuk menyimpan jumlah tiket berdasarkan ID tiket.
    $ticket_quantities = array();

    // Variabel untuk menyimpan total harga tiket.
    $total_price = 0;

     // Variabel untuk menyimpan total jumlah tiket.
    $total_quantity = 0;

    // Array untuk menyimpan kategori tiket.
    $cat = array();

    // Menampilkan Judul Invoice
    echo '<h2 style="text-align: center;">Invoice</h2>';

    // Mulai tabel untuk menampilkan detail tiket
    echo '<div class="tabel-container">';
    echo '<table class="tabelDetail">';
    echo '<tr>';
    echo '<th>Ticket Type</th>';
    echo '<th>Price</th>';
    echo '<th>Quantity</th>';
    echo '<th>Total Price</th>';
    echo '</tr>';

    // Loop melalui setiap jenis tiket yang dipilih
    foreach ($quantities as $ticket_id => $quantity) {
        if ($quantity > 0) {

            // Mengambil data tiket dari database berdasarkan ID.
            $result = $mysqli->query("SELECT * FROM tickets WHERE id = " . intval($ticket_id));

            // Mengambil data tiket sebagai array asosiatif.
            $ticket = $result->fetch_assoc();

            if ($ticket) {

                // Menghitung subtotal untuk setiap jenis tiket.
                $subtotal = floatval($ticket['price']) * intval($quantity);

                // Menambahkan subtotal ke total harga.
                $total_price += $subtotal;

                // Menambahkan jumlah tiket ke total jumlah.
                $total_quantity += intval($quantity);

                 // Menambahkan jenis tiket ke array kategori.
                $cat[] = $ticket['type'];

                echo '<tr>';

                 // Menampilkan jenis tiket.
                echo '<td>' . htmlspecialchars($ticket['type']) . '</td>';

                // Menampilkan harga tiket.
                echo '<td>' . 'Rp ' . number_format($ticket['price'], 0, ',', '.') . '</td>';

                // Menampilkan jumlah tiket.
                echo '<td>' . htmlspecialchars($quantity) . '</td>';

                // Menampilkan subtotal harga.
                echo '<td>' . 'Rp ' . number_format($subtotal, 0, ',', '.') . '</td>';
                echo '</tr>';                

                // Menyimpan jumlah tiket berdasarkan ID tiket.
                $ticket_quantities[$ticket_id] = $quantity;
            }
        }
    }

    // Tutup tabel
    echo '</table>';
    echo '</div>';

    // Tampilkan total harga
    echo "<br>";
    echo '<p class="total-price">Total Price: Rp ' . number_format($total_price, 0, ',', '.') . '</p>';

    // Simpan total harga, jumlah tiket, kategori tiket , jumlah tiket berdasarkan id ke  dalam sesion.
    $_SESSION['total_price'] = $total_price;
    $_SESSION['total_quantity'] = $total_quantity;
    $_SESSION['cat'] = $cat;
    $_SESSION['ticket_quantities'] = $ticket_quantities;
}

// Tampilkan formulir pembayaran
if (isset($_SESSION['total_quantity']) && $_SESSION['total_quantity'] > 0) {

    // Mengambil total jumlah tiket dari sesi.
    $total_quantity = $_SESSION['total_quantity'];

     // Mengambil kategori tiket dari sesi.
    $cat = $_SESSION['cat'];
?>
<div class="container">
    <form action="Pembayaran.php" method="post" onsubmit="return validateForm()">
        <?php
        // menampilkan form sebanyak jumlah tiket yang ada
        for ($i = 1; $i <= $total_quantity; $i++) {
            echo '<div class="row">';
            echo '<div class="col">';
            echo '<h4>Ticket ' . $i . '</h4>';
            echo '<div class="inputbox">';
            echo '<span>Full name :</span>';
            echo '<input type="text" name="name_' . $i . '" placeholder="Nama Lengkap Anda.." required>';
            echo '</div>';
            echo '<div class="inputbox">';
            echo '<span>Email :</span>';
            echo '<input type="email" name="email_' . $i . '" placeholder="example@gmail.com" required>';
            echo '</div>';
            echo '<div class="inputbox">';
            echo '<span>Address :</span>';
            echo '<input type="text" name="address_' . $i . '" placeholder="room - street">';
            echo '</div>';
            echo '<div class="inputbox">';
            echo '<span>City :</span>';
            echo '<input type="text" name="city_' . $i . '" placeholder="Your City">';
            echo '</div>';
            echo '<div class="flex">';
            echo '<div class="inputbox">';
            echo '<span>State :</span>';
            echo '<input type="text" name="state_' . $i . '" placeholder="Indonesia">';
            echo '</div>';
            echo '<div class="inputbox">';
            echo '<span>No Hp :</span>';
            echo '<input type="text" name="no_hp_' . $i . '" placeholder="+62"> required>';
            echo '</div>';
            echo '</div>';
            echo '<div class="inputbox">';
            echo '<span>CAT :</span>';
            echo '<input type="text" name="cat_' . $i . '" value="' . htmlspecialchars($cat[($i-1) % count($cat)]) . '" placeholder="Your Category" readonly>';
            echo '<br>';
            echo '<br>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>
        <a href="About.php" class="button-85" role="button">BACK</a>
        <input type="submit" value="Proceed to Checkout" class="submit-btn">
    </form>
</div>
<?php
}


// Periksa apakah user_id diset
if(isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    echo "User ID: " . $userId; // Tampilkan user ID
} else {
    // Jika tidak ada ID pengguna di sesi, mungkin pengguna belum login atau sesi telah berakhir
    // Tindakan yang sesuai, misalnya redirect ke halaman login
    header("Location: login.php");
    exit();
}

// Tampilkan nama konser 
if(isset($_SESSION['namaKonser'])) {
    $namaKonser = $_SESSION['namaKonser'];
    echo "Nama Konser: " . htmlspecialchars($namaKonser);
} else {
    echo "Nama Konser tidak tersedia.";
}

// Memeriksa apakah metode permintaan adalah POST dan apakah name_1 ada dalam data POST.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name_1'])) {
    require 'dbconnect.php';

    // Looping untuk setiap tiket yang akan dibeli berdasarkan total quantity yang disimpan di sesi.
    for ($i = 1; $i <= $_SESSION['total_quantity']; $i++) {
        
        // Mengambil data dari formulir POST berdasarkan indeks.
        $full_name = $POST['name' . $i];
        $email = $POST['email' . $i];
        $address = $POST['address' . $i];
        $city = $POST['city' . $i];
        $state = $POST['state' . $i];
        $no_hp = $POST['no_hp' . $i];
        $cat = $POST['cat' . $i];

        
        $namaKonser = $_SESSION['namaKonser']; // Mengambil NamaKonser dari session
        $userId = $_SESSION['user_id'];
        $ticket_number = $i; // Menetapkan nomor tiket sebagai nilai loop.

        // Simpan data tiket ke tabel invoice
        $sql = "INSERT INTO invoice (ticket_number, full_name, email, address, city, state, no_hp, cat, NamaKonser, userKey) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($mysqli->error));
        }

        // Mengikat parameter ke statement SQL. Parameter pertama adalah string format yang menentukan tipe data dari parameter selanjutnya.
        $stmt->bind_param("issssssssi", $ticket_number, $full_name, $email, $address, $city, $state, $no_hp, $cat, $namaKonser, $userId);

        if ($stmt->execute()) {
            echo "Ticket " . $i . " purchased successfully.<br>";
        } else {
            echo "Error: " . htmlspecialchars($stmt->error) . "<br>";
        }
        $stmt->close();
    }

    // Mendapatkan jumlah tiket yang dibeli dari sesi
    $ticket_quantities = $_SESSION['ticket_quantities'];

    //  Looping untuk setiap tiket berdasarkan ID tiket dan jumlah yang dibeli.
    foreach ($ticket_quantities as $ticket_id => $quantity) {
        
        // Mengambil data tiket dari database berdasarkan ID tiket.
        $result = $mysqli->query("SELECT * FROM tickets WHERE id = " . intval($ticket_id));
        if ($result) {

            // Mengambil data tiket sebagai array 
            $ticket = $result->fetch_assoc();

            //Mengambil tipe tiket.
            $type = $ticket['type'];

            // Hitung total stok yang akan dikurangi berdasarkan jumlah tiket yang dibeli
            $total_stock_to_reduce = $quantity;

            // Kurangi stok tiket di database berdasarkan jumlah tiket yang dibeli
            $update_stock_query = "UPDATE tickets SET stock = stock - ? WHERE type = ?";
            $update_stock_stmt = $mysqli->prepare($update_stock_query);
            if ($update_stock_stmt) {
                $update_stock_stmt->bind_param("is", $total_stock_to_reduce, $type);
                if (!$update_stock_stmt->execute()) {
                    echo '<script>alert("Gagal memperbarui stok tiket.");</script>';
                }
                $update_stock_stmt->close();
            } else {
                echo '<script>alert("Prepare statement untuk update stok tiket gagal.");</script>';
            }
        } else {
            echo '<script>alert("Gagal mendapatkan data tiket.");</script>';
        }
    }

    

    // Menampilkan pesan alert setelah pembelian berhasil
    echo "<script>alert('Pesanan berhasil dipesan!'); window.location.href = 'Home.php';</script>";
    $mysqli->close();
}


?>
<footer class="footer">
    <div class="container3">
        <div class="row">
            <div class="footer-col">
                <h4>company</h4>
                <ul>
                    <li><a href="#">about us</a></li>
                    <li><a href="#">our services</a></li>
                    <li><a href="#">privacy policy</a></li>
                    <li><a href="#">affiliate program</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>get help</h4>
                <ul>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">shipping</a></li>
                    <li><a href="#">returns</a></li>
                    <li><a href="#">order status</a></li>
                    <li><a href="#">payment options</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>follow us</h4>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>
    <p>&copy;Indonesia Ticket Hub 2024</p>
</footer>
<div id="popup-box" class="modal">
    <div class="content">
        <h1>Transaction Successful!</h1>
        <p>Your ticket will be sent via email</p>
        <a href="Home.php" class="T">&times;</a>
    </div>
</div>

<script>
function validateForm() {

    //  Mendeklarasikan array  untuk menyimpan nama,email,notlp.
    var names = [];
    var emails = [];
    var notelp = [];

    <?php
    // Generate JavaScript array of names, emails, and phone numbers from PHP session
    for ($i = 1; $i <= $total_quantity; $i++) {
        echo "var name_$i = document.getElementsByName('name_$i')[0].value;";
        echo "var email_$i = document.getElementsByName('email_$i')[0].value;";
        echo "var no_hp_$i = document.getElementsByName('no_hp_$i')[0].value;";

        // menambahkan value ke dalam array
        echo "names.push(name_$i);";
        echo "emails.push(email_$i);";
        echo "notelp.push(no_hp_$i);";
    }
    ?>

    // Mendefinisikan fungsi hasDuplicates untuk memeriksa apakah ada duplikat dalam array.
    function hasDuplicates(array) {
        // Menggunakan Set untuk menghapus duplikat dan membandingkan ukuran Set dengan panjang array asli untuk menentukan apakah ada duplikat.
        return (new Set(array)).size !== array.length;
    }

    // memeriksa apakah ada duplikat di nama , email , atau notlp 
    if (hasDuplicates(names)) {
        alert("Please ensure that each full name is unique.");
        return false;
    }

    if (hasDuplicates(emails)) {
        alert("Please ensure that each email is unique.");
        // Menghentikan proses form jika ada duplikat.
        return false;
    }

    if (hasDuplicates(notelp)) {
        alert("Please ensure that each phone number is unique.");
        return false;
    }
    return true;
}

document.getElementById('profileIcon').addEventListener('mouseover', function() {
      var dropdown = document.getElementById('dropdownMenu');
      dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
      if (!event.target.matches('#profileIcon')) {
        var dropdown = document.getElementById('dropdownMenu');
        if (dropdown.style.display === 'block') {
          dropdown.style.display = 'none';
        }
      }
    }
</script>
</body>
</html>