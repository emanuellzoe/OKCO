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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $sql = "SELECT t.*, h.* FROM tiket t 
            JOIN harga h ON t.id_harga = h.id_harga 
            WHERE t.id_tiket = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $eventData = $result->fetch_assoc();
    } else {
        die("Event not found.");
    }
} else {
    die("Invalid event ID.");
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT t.id_tiket, t.judul_tiket, t.gambar_tiket, t.tanggal_tiket, t.lokasi_tiket, t.artis_tiket, h.Festival 
        FROM tiket t 
        JOIN harga h ON t.id_harga = h.id_harga 
        WHERE t.judul_tiket LIKE '%$search%' 
        OR t.lokasi_tiket LIKE '%$search%' 
        OR t.artis_tiket LIKE '%$search%' 
        OR t.tanggal_tiket LIKE '%$search%'";

$result = $conn->query($sql);


$hargaQuery = "SELECT * FROM harga WHERE id_harga = ?";
$stmt2 = mysqli_prepare($conn, $hargaQuery);
mysqli_stmt_bind_param($stmt2, "s", $eventData['id_harga']);
mysqli_stmt_execute($stmt2);
$hargaResult = mysqli_stmt_get_result($stmt2);
$hargaData = mysqli_fetch_assoc($hargaResult);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Detail</title>
    <link rel="stylesheet" href="cssHalaman2.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const beliTiketButtons = document.querySelectorAll(".beli-tiket2 a");

            beliTiketButtons.forEach(button => {
                button.addEventListener("click", function(event) {
                    const type = button.closest(".beli-tiket2").getAttribute("data-type");
                    let quantity = 1;
                    
                    if (type === "tribun") {
                        const tribunInput = document.getElementById("quantity_tribun");
                        quantity = tribunInput ? tribunInput.value : 1;
                    } else if (type === "festival") {
                        const festivalInput = document.getElementById("quantity_festival");
                        quantity = festivalInput ? festivalInput.value : 1;
                    } else if (type === "VIP") {
                        const vipInput = document.getElementById("quantity_VIP");
                        quantity = vipInput ? vipInput.value : 1;
                    }

                    // Simpan jumlah tiket di Local Storage
                    localStorage.setItem('ticketQuantity', quantity);
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const beliTiketButtons = document.querySelectorAll(".beli-tiket2 a");

            beliTiketButtons.forEach(button => {
                button.addEventListener("click", function(event) {
                    const type = button.closest(".beli-tiket2").getAttribute("data-type");
                    let quantity = 1;

                    if (type === "tribun") {
                        const tribunInput = document.querySelector("input[name='quantity_tribun']");
                        quantity = tribunInput ? tribunInput.value : 1;
                    } else if (type === "festival") {
                        const festivalInput = document.querySelector("input[name='quantity_festival']");
                        quantity = festivalInput ? festivalInput.value : 1;
                    } else if (type === "VIP") {
                        const vipInput = document.querySelector("input[name='quantity_VIP']");
                        quantity = vipInput ? vipInput.value : 1;
                    }

                    // Simpan jumlah tiket di Local Storage
                    localStorage.setItem('ticketQuantity', quantity);
                });
            });
        });
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
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <a href="logout.php" class="navi">Logout</a>
            <?php else: ?>
                <a href="login.php" class="navi">Login</a>
            <?php endif; ?>
            <a href="pesan.php" class="pesan">Pemesanan</a>
        </nav>
    </header>

    <div class="box">
        <a href=""><img src="icon/back-arrow.png" alt=""></a>
        </div>
        <br>        
        <div class="container">
        <div class="konten_pertama">
            <div class="headling"><h1><?php echo htmlspecialchars($eventData['judul_tiket']); ?></h1></div>
            <div class="detail_info_pertama">
                <div class="gambar">
                    <img src="<?php echo htmlspecialchars($eventData['gambar_tiket']); ?>" alt="">
                </div>
            </div>
            <div class="detail_info_kedua">
                <div class="jadwal">
                    <h2>Detail Event</h2>
                    <div class="tanggal">
                        <div class="tanggal-icon"><img src="icon/calendar.png" alt="Not Found"></div>
                        <div class="tanggal-text">
                            <label for="">Tanggal</label>
                            <span><?php echo date('d F Y', strtotime($eventData['tanggal_tiket'])); ?></span>
                        </div>
                    </div>
                    <div class="waktu">
                        <div class="waktu-icon"><img src="icon/waktu.png" alt="Not Found"></div>
                        <div class="waktu-text">
                            <label for="">Waktu</label>
                            <span><?php echo htmlspecialchars($eventData['waktu_tiket']); ?></span>
                        </div>
                    </div>
                    <div class="tempat">
                        <div class="tempat-icon"><img src="icon/tempat.png" alt="Not Found"></div>
                        <div class="tempat-text">
                            <label for="">Lokasi</label>
                            <span><?php echo htmlspecialchars($eventData['lokasi_tiket']); ?></span>
                        </div>
                    </div>
                </div>
                
            </div>  
        </div>

        <div class="konten_kedua">
            <div class="pilihan">
                <div class="pilihan-des" id="pilihan-des">
                    <span class="material-symbols-outlined">description</span>
                    <div class="des-text">
                        <label for="deskripsi">Deskripsi</label>
                        <input type="radio" name="option" id="deskripsi" checked>
                    </div>
                </div>
                <div class="pilihan-tiket" id="pilihan-tiket">
                    <span class="material-symbols-outlined">confirmation_number</span>
                    <div class="tiket-text">
                        <label for="tiket">Tiket</label>
                        <input type="radio" name="option" id="tiket">
                    </div>
                </div>
            </div>

            <div class="info_tiket">
                <div class="deskripsi_lanjut">
                    <h3>Deskripsi Event</h3>
                    <p><?php echo htmlspecialchars($eventData['deskripsi']); ?></p>
            </div>

            <div class="tiket-lanjut" id="pilihan">
                <div class="border-tiktik">
                    <div class="tiktik">
                            <div class="jenis-tiket">Regular: Tribun</div>
                            <div class="tiket-detail">
                                <label for="">Harga</label>
                                <span>Rp <?php echo number_format($hargaData['Tribun'], 0, ',', '.'); ?></span>
                                <p>Stok Tiket: <?php echo htmlspecialchars($eventData['stok_Tribun']); ?></p>
                                <input class="number hidden" id="quantity_tribun" type="number" name="quantity_tribun" min="1" max="10">
                            </div>
                    </div>
                        <?php if ($eventData['stok_Tribun'] > 0): ?>
                            <button class="batal-tiket">Batal Pilih</button>
                            <div class="beli-tiket" data-type="tribun">Pilih</div>
                            <div class="beli-tiket2" data-type="tribun"><a href="halaman3.php?id_tiket=<?php echo htmlspecialchars($id); ?>&type=Tribun">Beli</a></div>
                        <?php else: ?>
                            <span class="beli-tiket-sold">Sold Out</span>
                        <?php endif; ?>
                </div>
                    
                <div class="border-tiktik">
                    <div class="tiktik">
                            <div class="jenis-tiket">Regular: Festival</div>
                            <div class="tiket-detail">
                                <label for="">Harga</label>
                                <span>Rp <?php echo number_format($hargaData['Festival'], 0, ',', '.'); ?></span>
                                <p>Stok Tiket: <?php echo htmlspecialchars($eventData['stok_Festival']); ?></p>
                                
                                <input class="number hidden" id="quantity_festifal" type="number" name="quantity_festival" min="1" max="10">
                            </div>
                    </div>
                        <?php if ($eventData['stok_Festival'] > 0): ?>
                            <button class="batal-tiket">Batal Pilih</button>
                            <div class="beli-tiket" data-type="festival">Pilih</div>
                            <div class="beli-tiket2" data-type="festival"><a href="halaman3.php?id_tiket=<?php echo htmlspecialchars($id); ?>&type=Festival">Beli</a></div>
                        <?php else: ?>
                            <span class="beli-tiket-sold">Sold Out</span>
                        <?php endif; ?>
                </div>
                    
                <div class="border-tiktik">
                    <div class="tiktik">
                            <div class="jenis-tiket">Regular: VIP</div>
                            <div class="tiket-detail">
                                <label for="">Harga</label>
                                <span>Rp <?php echo number_format($hargaData['VIP'], 0, ',', '.'); ?></span>
                                <p>Stok Tiket: <?php echo htmlspecialchars($eventData['stok_VIP']); ?></p>
                                
                                <input class="number hidden" id="quantity_VIP" type="number" name="quantity_VIP" min="1" max="10">
                            </div>
                    </div>
                        <?php if ($eventData['stok_VIP'] > 0): ?>
                            <button class="batal-tiket">Batal Pilih</button>
                            <div class="beli-tiket" data-type="VIP">Pilih</div>
                            <div class="beli-tiket2" data-type="VIP"><a href="halaman3.php?id_tiket=<?php echo htmlspecialchars($id); ?>&type=VIP">Beli</a></div>
                        <?php else: ?>
                            <span class="beli-tiket-sold">Sold Out</span>
                        <?php endif; ?>
                </div>
                </div>
            </div>



            
        </div>
    </div>
        
    <footer>
        <p><a href="#"><img id="okco1" src="icon/okco-png.png" alt="okco"></a></p>
        <p>&#169; 2024 Artatix.co.id, All rights reserved</p>
    </footer>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            var tiket = document.querySelector('.tiket-lanjut');
            tiket.style.display = 'none';

            var deskripsi = document.querySelector('.deskripsi_lanjut');
            deskripsi.style.display = 'block';

            document.getElementById('pilihan-des').addEventListener('click', function() {
                deskripsi.style.display = 'block';
                tiket.style.display = 'none';
            });

            document.getElementById('pilihan-tiket').addEventListener('click', function() {
                deskripsi.style.display = 'none';
                tiket.style.display = 'block';
            });
        });


        document.addEventListener("DOMContentLoaded", function() {
    const beliTiketButtons = document.querySelectorAll('.beli-tiket');
    const batalTiketButtons = document.querySelectorAll('.batal-tiket');

    beliTiketButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const parent = this.parentElement;
            const inputField = parent.querySelector('.number');
            const batalButton = parent.querySelector('.batal-tiket');
            inputField.classList.remove('hidden');
            batalButton.classList.remove('hidden');
            const link = this.querySelector('a');
            link.textContent = 'Beli'; // Change text to "Beli"
        });
    });

    batalTiketButtons.forEach(button => {
        button.addEventListener('click', function() {
            const parent = this.parentElement;
            const inputField = parent.querySelector('.number');
            inputField.value = ''; // Clear the input field
            inputField.classList.add('hidden');
            this.classList.add('hidden');
            const pilihButton = parent.querySelector('.beli-tiket');
            const link = pilihButton.querySelector('a');
            link.textContent = 'Pilih'; // Change text to "Pilih"
        });
    });

    // Hide all "Batal Pilih" buttons by default
    batalTiketButtons.forEach(button => {
        button.classList.add('hidden');
    });
});

document.addEventListener("DOMContentLoaded", function() {
    // Select all "Pilih" buttons
    const pilihButtons = document.querySelectorAll('.beli-tiket');

    // Add event listener to all "Pilih" buttons
    pilihButtons.forEach(button => {
        button.addEventListener('click', function() {
            const parent = button.closest('.border-tiktik');
            const beliButton = parent.querySelector('.beli-tiket2');
            const batalButton = parent.querySelector('.batal-tiket');

            // Show "Beli" and "Batal" buttons, hide "Pilih" button
            button.style.display = 'none';
            beliButton.style.display = 'block';
            batalButton.style.display = 'block';
        });
    });

    // Select all "Batal" buttons
    const batalButtons = document.querySelectorAll('.batal-tiket');

    // Add event listener to all "Batal" buttons
    batalButtons.forEach(button => {
        button.addEventListener('click', function() {
            const parent = button.closest('.border-tiktik');
            const beliButton = parent.querySelector('.beli-tiket2');
            const pilihButton = parent.querySelector('.beli-tiket');

            // Show "Pilih" button, hide "Beli" and "Batal" buttons
            button.style.display = 'none';
            beliButton.style.display = 'none';
            pilihButton.style.display = 'block';
        });
    });

    // Initialize all to show "Pilih" button only
    document.querySelectorAll('.beli-tiket2, .batal-tiket').forEach(el => {
        el.style.display = 'none';
    });
});


document.addEventListener("DOMContentLoaded", function() {
    const tribunInput = document.getElementById("quantity_tribun");
    const festivalInput = document.getElementById("quantity_festival");
    const vipInput = document.getElementById("quantity_VIP");
    const ticketQuantityLabel = document.getElementById("ticket-quantity");

    function updateQuantity(input) {
        input.addEventListener("input", function() {
            ticketQuantityLabel.textContent = input.value;
        });
    }

    if (tribunInput) updateQuantity(tribunInput);
    if (festivalInput) updateQuantity(festivalInput);
    if (vipInput) updateQuantity(vipInput);

    const beliTiketButtons = document.querySelectorAll(".beli-tiket2 a");

    beliTiketButtons.forEach(button => {
        button.addEventListener("click", function(event) {
            const type = button.closest(".beli-tiket2").getAttribute("data-type");
            let quantity = 1;

            if (type === "tribun" && tribunInput) quantity = tribunInput.value;
            if (type === "festival" && festivalInput) quantity = festivalInput.value;
            if (type === "VIP" && vipInput) quantity = vipInput.value;

            ticketQuantityLabel.textContent = quantity;

            const url = new URL(button.href);
            url.searchParams.set('quantity', quantity);
            button.href = url.toString();
        });
    });
});

   

    </script>
</body>
</html>

<?php
// Close the prepared statement and the connection
mysqli_stmt_close($stmt2);
$conn->close();
?>