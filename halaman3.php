<?php
session_start();
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $id_tiket = isset($_GET['id_tiket']) ? intval($_GET['id_tiket']) : 0;

    if ($id_tiket > 0 && !empty($type)) {
        $sql = "SELECT t.*, h.* FROM tiket t 
                JOIN harga h ON t.id_harga = h.id_harga 
                WHERE t.id_tiket = $id_tiket";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $eventData = $result->fetch_assoc();
        } else {
            die("Event not found.");
        }
    } else {
        die("Invalid event ID or ticket type.");
    }
    
    // Define the ticket type names
    $ticketTypes = [
        'Tribun' => 'Regular: Tribun',
        'Festival' => 'Regular: Festival',
        'VIP' => 'VIP'
    ];

    // Fetch the ticket price
    $price = 0;
    if ($type == 'Tribun') {
        $price = $eventData['Tribun'];
    } elseif ($type == 'Festival') {
        $price = $eventData['Festival'];
    } elseif ($type == 'VIP') {
        $price = $eventData['VIP'];
    } else {
        die("Invalid ticket type.");
    }
}

$search = ""; // Inisialisasi variabel $search
if(isset($_GET['search'])) {
    $search = $_GET['search'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Okco Tiket - Checkout</title>
    <link rel="stylesheet" href="halaman-3.css">
    <!-- icon pesan dan tiket -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ambil jumlah tiket dari Local Storage
            const quantity = localStorage.getItem('ticketQuantity') || 1;

            const ticketQuantityLabel = document.getElementById("ticket-quantity");
            ticketQuantityLabel.value = quantity;

            // Update harga dan total
            const price = <?php echo $price; ?>;
            const subtotalLabel = document.querySelector(".subtotal .harga-tiket");
            const totalLabel = document.querySelector(".total .harga-tiket");

            subtotalLabel.textContent = "Rp " + (price * quantity).toLocaleString('id-ID');
            totalLabel.textContent = "Rp " + ((price * quantity) + 10000).toLocaleString('id-ID');

            // Generate dynamic forms
            const formContainer = document.querySelector(".Form-Pemesan");
            for (let i = 0; i < quantity; i++) {
                const formTemplate = `
                    <div class="Data-Pemesan">
                        <span class="material-symbols-outlined icon">account_box</span>
                        <span class="account-teks">Data Diri Pemesan ${i + 1}</span>
                    </div>
                    <hr>
                    <div class="nama">
                        <label for="nama_lengkap_${i}">Nama Lengkap <span class="kali">*</span></label><br>
                        <input type="text" name="nama_lengkap_${i}" id="nama_lengkap_${i}" maxlength="50" required oninvalid="this.setCustomValidity('Data Masih Kosong atau Salah')" oninput="this.setCustomValidity('')">
                    </div>
                    <div class="nik">
                        <label for="nik_${i}">NIK <span class="kali">*</span></label><br>
                        <input type="number" name="nik_${i}" id="nik_${i}" class="input-number" max="9999999999999999" required oninvalid="this.setCustomValidity('Data Masih Kosong atau Salah')" oninput="this.setCustomValidity('')">
                    </div>
                    <div class="email">
                        <label for="email_${i}">Email <span class="kali">*</span></label><br>
                        <input type="text" name="email_${i}" id="email_${i}" maxlength="50" required oninvalid="this.setCustomValidity('Data Masih Kosong atau Salah')" oninput="this.setCustomValidity('')">
                    </div>
                    <div class="nomor">
                        <label for="nomor_${i}">No.Whatsapp <span class="kali">*</span></label><br>
                        <input type="number" name="nomor_${i}" id="nomor_${i}" class="input-number" max="9999999999999" required oninvalid="this.setCustomValidity('Data Masih Kosong atau Salah')" oninput="this.setCustomValidity('')">
                    </div>
                `;
                formContainer.insertAdjacentHTML('beforeend', formTemplate);
            }
        });

        function checkLoginStatus(event) {
            const isLoggedIn = <?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true ? 'true' : 'false'; ?>;
            if (!isLoggedIn) {
                event.preventDefault();
                alert("Anda harus Login untuk melanjutkan pembayaran");
                
            }
        }

        
    </script>
</head>
<body>
    <header>
    <nav>
            <a href="index.php"><img id="okco" src="assets/okco-png.png" alt="okco"></a>
            <form method="GET" action="index.php">
            <div class="search">
                    <input class="search-input" type="search" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="search-icon">
                        <img src="assets/search.png" alt="Search">
                    </button>
                </div>
            </form>
            <div class="nav-links">
                <a href="pesan.php" class="pesan">Pemesanan</a>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="navi">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="navi">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    
    <form class="utama" method="post" action="process_order.php" onsubmit="checkLoginStatus(event)">
        <div class="Form">
            <div class="Form-Pemesan">
                <!-- Dynamic forms will be inserted here -->
            </div>
            <input type="hidden" name="id_tiket" value="<?php echo $id_tiket; ?>">
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <input type="hidden" name="quantity" id="ticket-quantity" value="1"> <!-- Input for quantity -->
        </div>
        <div class="decs">
            <div class="rincian">
                <span class="material-symbols-outlined icon">news</span>
                <span class="rincian-pemesanan">Rincian Pesanan</span>
            </div>
            <div class="banner-event">
                <img src="<?php echo htmlspecialchars($eventData['gambar_tiket']); ?>" alt="foto event">
            </div>
            <div class="nama-event">
                <label><?php echo htmlspecialchars($eventData['judul_tiket']); ?></label>
            </div>
            <hr>
            <div class="tiket-info">
                <label>Tiket</label>
                <label class="kanan">Jumlah</label>
            </div>
            <div class="tiket-detail">
                <label id="ticket-type"><?php echo htmlspecialchars($ticketTypes[$type]); ?></label><br>
                <span class="harga-tiket">Rp <?php echo number_format($price, 0, ',', '.'); ?></span>
            </div>
            <hr>
            <div class="uang">
                <div class="subtotal">
                    <label class="tiket-info">Subtotal</label>
                    <span class="harga-tiket kanan">Rp <?php echo number_format($price * $quantity, 0, ',', '.'); ?></span>
                </div>
                <div class="price">
                    <label class="tiket-info">Biaya Layanan</label>
                    <span class="biaya-harga-tiket kanan">Rp 10.000</span>
                </div>
                <hr>
                <div class="total">
                    <label class="tiket-info">Total</label>
                    <span class="harga-tiket kanan">Rp <?php echo number_format(($price * $quantity) + 10000, 0, ',', '.'); ?></span>
                </div>
            </div>
            <div class="beli">
                <button type="submit">BELI</button>
            </div>
        </div>
        <div class="kosong"></div>
    </form>
</body>
</html>
