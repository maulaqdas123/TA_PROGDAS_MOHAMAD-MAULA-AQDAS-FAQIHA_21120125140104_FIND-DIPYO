<?php

// ====================================================================
// A. KONFIGURASI DAN DATA MASTER
// ====================================================================

// TIPE DATA: Array Halte
$HALTE_UNDIP = [
    'Gedung Rektorat','Gerbang Undip','Fakultas Ekonomika dan Bisnis (FEB)','Fakultas Hukum (FH)',
    'Fakultas Ilmu Budaya (FIB)','Fakultas Ilmu Sosial dan Ilmu Politik (FISIP)','Fakultas Kedokteran (FK)',
    'Fakultas Kesehatan Masyarakat (FKM)','Fakultas Perikanan dan Ilmu Kelautan (FPIK)','Fakultas Peternakan dan Pertanian (FPP)',
    'Fakultas Sains dan Matematika (FSM)','Fakultas Teknik (FT)','Fakultas Psikologi (FPsi)',
];

// TIPE DATA: Pemetaan URL Gambar Halte
$HALTE_IMAGE_MAP = [
    'Gedung Rektorat' => 'https://undip.ac.id/wp-content/uploads/2023/09/Gedung-WP.jpg',
    'Gerbang Undip' => 'https://skep.fk.undip.ac.id/wp-content/uploads/2021/09/UNDIP.jpg',
    'Fakultas Kedokteran (FK)' => 'https://masuk-ptn.com/images/department/e44a07b41285547fd284d02e3fe480a11f5a8841.jpg',
    'Fakultas Ekonomika dan Bisnis (FEB)' => 'https://undip.ac.id/wp-content/uploads/2023/04/WhatsApp-Image-2023-04-15-at-09.32.53.jpeg',
    'Fakultas Hukum (FH)' => 'https://undip.ac.id/wp-content/uploads/2024/07/IMG_0468-HDR-scaled-1.jpg',
    'Fakultas Ilmu Budaya (FIB)' => 'https://undip.ac.id/wp-content/uploads/2021/11/FIB-UNDIP-Perkuat-Visi-Menjadi-Fakultas-Riset-Unggul-di-Asia-Tenggara-Tahun-2025-1.jpg',
    'Fakultas Ilmu Sosial dan Ilmu Politik (FISIP)' => 'https://dap.fisip.undip.ac.id/wp-content/uploads/2024/08/Teras-FISIP-UNDIP-1.jpg',
    'Fakultas Kesehatan Masyarakat (FKM)' => 'https://undip.ac.id/wp-content/uploads/2021/08/fkm.jpg',
    'Fakultas Perikanan dan Ilmu Kelautan (FPIK)' => 'https://pbs.twimg.com/media/GQFu7ymaIAAC6ZM.jpg',
    'Fakultas Peternakan dan Pertanian (FPP)' => 'https://undip.ac.id/wp-content/uploads/2025/03/kompres-fpp.png',
    'Fakultas Sains dan Matematika (FSM)' => 'https://fsm.undip.ac.id/wp-content/uploads/2020/10/DSC7133-1.jpg',
    'Fakultas Teknik (FT)' => 'https://ft.undip.ac.id/wp-content/uploads/dekanat.png',
    'Fakultas Psikologi (FPsi)' => 'https://undip.ac.id/wp-content/uploads/2023/05/Fak-Psi-1067x675-1.jpg',
    'DEFAULT_UNDIP' => 'https://undip.ac.id/wp-content/uploads/2024/04/DJI_0219.jpg', 
];

date_default_timezone_set('Asia/Jakarta');

// ====================================================================
// B. CLASS DAN OBJEK (OOP)
// ====================================================================

class Bus
{
    // TIPE DATA: Properti
    public $id;
    public $nama;
    public $kapasitas;
    public $posisiHalte;
    public $waktuTibaEstimasi;
    public $penumpangAktif;
    public $imageUrl;

    public function __construct($id, $nama, $kapasitas, $imageUrl)
    {
        $this->id = $id;
        $this->nama = $nama;
        $this->kapasitas = $kapasitas;
        $this->posisiHalte = rand(0, 12);
        $this->waktuTibaEstimasi = date('H:i:s', time() + rand(5, 30) * 60);
        $sessionKey = 'bus_state_' . $id;
        if (isset($_SESSION[$sessionKey])) { $this->penumpangAktif = $_SESSION[$sessionKey]; } else { $this->penumpangAktif = []; }
        $this->imageUrl = $imageUrl;
    }

    // METHOD: Menghitung jumlah kursi tersedia
    public function getKursiTersedia() { return $this->kapasitas - count($this->penumpangAktif); }

    // METHOD: Update status bus di session (Simulasi DB)
    public function saveState() { $_SESSION['bus_state_' . $this->id] = $this->penumpangAktif; }

    // METHOD: Memperbarui URL gambar berdasarkan posisi halte
    public function updateImageUrl($halteList, $imageMap)
    {
        $currentHalteName = $halteList[$this->posisiHalte];
        // PENGKONDISIAN: Cek gambar spesifik
        if (isset($imageMap[$currentHalteName])) { $this->imageUrl = $imageMap[$currentHalteName]; } else { $this->imageUrl = $imageMap['DEFAULT_UNDIP']; }
    }

    // METHOD: Mengubah posisi bus (SIMULASI PERULANGAN)
    public function updatePosisi($halteCount)
    {
        $this->posisiHalte = ($this->posisiHalte + 1) % $halteCount;
        $this->waktuTibaEstimasi = date('H:i:s', time() + rand(5, 15) * 60);
    }

    // METHOD: Proses Tap In
    public function tapIn($nim, $dariHalte, $keHalte, $busList)
    {
        // LOGIKA BARU: Cek apakah pengguna sudah Tap In di bus lain
        if (getUserActiveBus($nim, $busList) !== null) {
            return "Tap In gagal! Anda sudah Tap In di bus lain. Silakan Tap Out terlebih dahulu.";
        }
        
        // PENGKONDISIAN: Cek ketersediaan kursi & apakah sudah tap in
        if ($this->getKursiTersedia() > 0) {
            if (!isset($this->penumpangAktif[$nim])) {
                $this->penumpangAktif[$nim] = ['dari' => $dariHalte, 'ke' => $keHalte];
                $this->saveState();
                return "Tap In berhasil! Dari: $dariHalte, Tujuan: $keHalte. Selamat jalan!";
            } else { return "Anda sudah Tap In di bus ini. Silakan Tap Out terlebih dahulu."; }
        }
        return "Bus Penuh! Tap In gagal.";
    }

    // METHOD: Proses Tap Out
    public function tapOut($nim)
    {
        if (isset($this->penumpangAktif[$nim])) {
            unset($this->penumpangAktif[$nim]);
            $this->saveState();
            return "Tap Out berhasil. Kursi sekarang kosong.";
        }
        return "Anda belum Tap In pada bus ini.";
    }
}

// FUNCTION GLOBAL: Mencari bus yang saat ini dinaiki oleh pengguna
function getUserActiveBus($userId, $busList) {
    // PERULANGAN: Mencari melalui semua bus
    foreach ($busList as $bus) {
        if (isset($bus->penumpangAktif[$userId])) {
            return $bus->nama; // Mengembalikan nama bus jika ditemukan
        }
    }
    return null; // Mengembalikan null jika pengguna tidak sedang naik bus manapun
}

// ====================================================================
// C. LOGIKA PEMROSESAN FORM DAN INITIALISASI
// ====================================================================

if (session_status() == PHP_SESSION_NONE) { session_start(); }

$FALLBACK_IMAGE_URL = "https://placehold.co/300x450/4B0082/ffffff?text=TRACKING+MODE";
$NEW_DASHBOARD_BG_URL = 'https://i.ytimg.com/vi/k-eV4fmR0as/maxresdefault.jpg';
$dashboardBgImage = $NEW_DASHBOARD_BG_URL . '?v=' . time(); 
$cardImageUrlDefault = $HALTE_IMAGE_MAP['DEFAULT_UNDIP'];

// PERULANGAN: Inisialisasi Objek Bus
$busList = [];
for ($i = 1; $i <= 6; $i++) { $busList[] = new Bus($i, "Dipyo-$i", 30, $cardImageUrlDefault); }
for ($i = 1; $i <= 3; $i++) { $busList[] = new Bus(6 + $i, "Bus Trans-$i", 50, $cardImageUrlDefault); }

// PERULANGAN: Simulasi Pergerakan dan Status Awal
foreach ($busList as $bus) {
    $bus->updatePosisi(count($HALTE_UNDIP));
    $bus->updateImageUrl($HALTE_UNDIP, $HALTE_IMAGE_MAP);

    $sessionKey = 'bus_state_' . $bus->id;
    // PENGKONDISIAN: Simulasi Bus Penuh hanya jika session baru
    if (!isset($_SESSION[$sessionKey])) {
        // LOGIKA BARU: Dipyo-3 dan Dipyo-4 dibuat penuh
        if (strpos($bus->nama, 'Dipyo-3') !== false || strpos($bus->nama, 'Dipyo-4') !== false) {
             // Bus Dipyo-3 & Dipyo-4 dibuat penuh (30/30)
             for ($j = 0; $j < $bus->kapasitas; $j++) { $bus->penumpangAktif["SIM_FULL_$j"] = ['dari' => 'Simulasi', 'ke' => 'Penuh']; }
             $bus->saveState(); 
        } elseif (strpos($bus->nama, 'Bus Trans-2') !== false) {
             // Bus Trans-2 dibuat hampir penuh
             for ($j = 0; $j < $bus->kapasitas - 1; $j++) { $bus->penumpangAktif["SIM_NEARLY_$j"] = ['dari' => 'Simulasi', 'ke' => 'Hampir Penuh']; }
             $bus->saveState(); 
        } elseif ($bus->id % 3 === 0) {
            // Sisanya dibuat terisi acak (sebagian kursi)
            for ($k = 0; $k < 5; $k++) {
                $bus->penumpangAktif["SIM_PARTIAL_$k{$bus->id}"] = ['dari' => $HALTE_UNDIP[rand(0, 4)], 'ke' => $HALTE_UNDIP[rand(5, 12)]];
            }
             $bus->saveState();
        }
    }
}

$tappedBus = null; $actionMessage = "";
$currentUserId = isset($_GET['user_id']) ? htmlspecialchars($_GET['user_id']) : '120221999';
$userActiveBus = getUserActiveBus($currentUserId, $busList); // PENGKONDISIAN: Cek bus aktif pengguna

// LOGIKA POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bus_id_action'])) {
    $busId = (int)$_POST['bus_id_action']; $actionType = $_POST['action_type']; $userId = $_POST['user_id'];
    foreach ($busList as $bus) {
        if ($bus->id === $busId) {
            // PENGKONDISIAN: Memanggil Tap In/Out dengan logika baru
            if ($actionType === 'tap_in') { $actionMessage = $bus->tapIn($userId, $_POST['halte_dari'], $_POST['halte_ke'], $busList); }
            elseif ($actionType === 'tap_out') { $actionMessage = $bus->tapOut($userId); }
            break;
        }
    }
    header("Location: BusTracker.php?user_id=" . urlencode($userId) . "&msg=" . urlencode($actionMessage)); exit();
}

// LOGIKA GET REQUEST
if (isset($_GET['msg'])) { $actionMessage = htmlspecialchars($_GET['msg']); }
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tap_bus_id'])) {
    $busId = (int)$_GET['tap_bus_id'];
    foreach ($busList as $bus) { if ($bus->id === $busId) { $tappedBus = $bus; break; } }
}

// ====================================================================
// D. GUI (Tampilan Halaman)
// ====================================================================

?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Dipyo Undip Tracker - Dashboard</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
:root{--primary-color:#003366;--secondary-color:#ffd700;--bg-dark:#121212;--card-dark:#1f1f1f;--text-light:#f3f4f6;--success:#10b981;--danger:#ef4444;--info:#3b82f6;}
*{box-sizing:border-box;font-family:'Inter',sans-serif;}
body{background-color:var(--bg-dark);color:var(--text-light);margin:0;padding:0;display:flex;min-height:100vh;}
.sidebar{width:80px;background-color:var(--card-dark);padding:1.5rem 0;display:flex;flex-direction:column;align-items:center;box-shadow:4px 0 10px rgba(0,0,0,.5);border-right:1px solid #333;}
.sidebar-item{padding:.8rem 0;margin-bottom:1rem;width:80%;text-align:center;border-radius:12px;transition:background-color .3s;}
.sidebar-item:hover,.sidebar-item.active{background-color:var(--primary-color);}
.sidebar-icon{font-size:1.8rem;color:var(--text-light);}
.main-content{flex-grow:1;padding:3rem;overflow-y:auto;position:relative;background-color:var(--bg-dark);}
.main-content::before{content:'';position:absolute;top:0;left:0;right:0;bottom:0;background-color:rgba(31,31,31,.7);z-index:0;}
.header,h2,.alert-message,.bus-carousel-container{position:relative;z-index:1;}
.header{margin-bottom:2.5rem;padding-bottom:1.5rem;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #333;}
.bus-carousel-container{display:flex;overflow-x:auto;gap:20px;padding-bottom:1.5rem;scrollbar-width:thin;scrollbar-color:var(--primary-color) var(--card-dark);transition:transform .3s ease-in-out;perspective:1000px;}
.bus-card{flex:0 0 auto;width:250px;height:380px;background-color:var(--card-dark);border-radius:12px;box-shadow:0 6px 15px rgba(0,0,0,.6);overflow:hidden;cursor:pointer;text-decoration:none;position:relative;transition:all .3s ease-in-out;transform:scale(1);opacity:1;}
.bus-carousel-container:hover .bus-card{transform:scale(.95);opacity:.7;}
.bus-carousel-container:hover .bus-card:hover{transform:scale(1);z-index:10;opacity:1;box-shadow:0 15px 30px rgba(0,0,0,.9);width:320px;}
.bus-carousel-container:not(:hover) .bus-card{width:250px;transform:scale(1);opacity:1;}
.bus-card h3{position:relative;top:auto;left:auto;transform:none;white-space:normal;width:auto;text-align:left;font-size:1.8rem;color:var(--secondary-color);transition:none;}
.bus-card .bus-info{display:flex;}
.bus-card-image{width:100%;height:100%;object-fit:cover;position:absolute;opacity:.5;transition:opacity .3s;}
.bus-carousel-container:hover .bus-card .bus-card-image{opacity:.3;}
.bus-carousel-container:hover .bus-card:hover .bus-card-image{opacity:.5;}
.bus-info{position:absolute;bottom:0;left:0;padding:1.5rem;width:100%;min-height:50%;display:flex;flex-direction:column;justify-content:flex-end;background:linear-gradient(to top,rgba(0,0,0,.95),rgba(0,0,0,0));}
.bus-info h3{margin-top:0;font-size:1.8rem;}
.bus-info p{margin:.2rem 0;font-size:.95rem;font-weight:300;}
.bus-info strong{font-weight:600;color:var(--text-light);}
.status-badge{font-size:.8rem;font-weight:700;padding:.3rem .8rem;border-radius:9999px;display:inline-block;margin-top:.8rem;text-transform:uppercase;}
.available{background-color:var(--success);color:var(--bg-dark);}
.full{background-color:var(--danger);color:var(--text-light);}
.details-button{display:block;text-align:center;background-color:var(--primary-color);color:var(--text-light);padding:.7rem;border-radius:8px;font-weight:600;margin-top:1rem;transition:background-color .3s;}
.details-button:hover{background-color:#004d99;}
.modal-overlay{<?php echo $tappedBus?'display:flex;':'display:none;';?>position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,.9);align-items:center;justify-content:center;z-index:1000;}
.modal-content{background-color:var(--card-dark);padding:3rem;border-radius:20px;width:95%;max-width:600px;box-shadow:0 10px 40px rgba(0,0,0,.9);position:relative;border-top:4px solid var(--secondary-color);}
.close-button{position:absolute;top:15px;right:25px;background:none;border:none;color:var(--text-light);font-size:2.5rem;cursor:pointer;text-decoration:none;}
.modal-info{padding:1rem;background-color:#2c2c2c;border-radius:10px;margin-top:1rem;margin-bottom:2rem;border-left:5px solid var(--info);}
.modal-info p{margin:.5rem 0;}
.form-group label{display:block;margin-top:1.5rem;margin-bottom:.6rem;font-weight:600;color:var(--secondary-color);font-size:1.1rem;}
.form-group select,.form-group input{width:100%;padding:1rem;border-radius:10px;border:1px solid #444;background-color:#2c2c2c;color:var(--text-light);font-size:1rem;}
.btn-submit{margin-top:2rem;width:100%;padding:1.2rem;border:none;border-radius:10px;font-weight:700;cursor:pointer;transition:background-color .3s,transform .2s;font-size:1.1rem;}
.btn-tap-in{background-color:var(--success);color:var(--bg-dark);}
.btn-tap-in:hover{background-color:#059669;transform:translateY(-1px);}
.btn-tap-out{background-color:var(--danger);color:var(--text-light);}
.btn-tap-out:hover{background-color:#b91c1c;transform:translateY(-1px);}
.alert-message{padding:1.5rem;border-radius:10px;margin-bottom:2rem;font-weight:600;font-size:1.1rem;}
.alert-success{background-color:var(--success);color:var(--bg-dark);}
.alert-error{background-color:var(--danger);color:var(--text-light);}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="sidebar">
    <div class="sidebar-item active" title="Dashboard"><i class="sidebar-icon fas fa-road"></i></div>
    <div class="sidebar-item" title="Logout" onclick="window.location.href='index.php'"><i class="sidebar-icon fas fa-sign-out-alt"></i></div>
</div>
<div class="main-content">
    <div class="header">
        <h1 style="color: var(--secondary-color); font-size: 2.8rem; margin: 0;">Dipyo Tracker</h1>
        <div style="text-align: right; font-size: 1rem;">
            <p style="color: #aaa; margin: 0;">Waktu Server: <?php echo date('d M Y, H:i:s'); ?></p>
        </div>
    </div>
    <?php
    if (!empty($actionMessage)) {
        $isSuccess = (strpos($actionMessage, 'berhasil') !== false);
        $alertClass = $isSuccess ? 'alert-success' : 'alert-error';
        echo "<div class='alert-message {$alertClass}'><i class='fas fa-info-circle'></i> {$actionMessage}</div>";
    }
    ?>
    <h2 style="color: var(--text-light); margin-bottom: 1.5rem;">Bus & Dipyo Dalam Perjalanan</h2>
    <div class="bus-carousel-container">
        <?php
        // PERULANGAN: Menampilkan semua Bus/Dipyo
        foreach ($busList as $bus) {
            // TIPE DATA & PENGKONDISIAN: Variabel untuk status bus
            $kursiTersedia = $bus->getKursiTersedia();
            $badgeClass = $kursiTersedia > 0 ? 'available' : 'full';
            $statusText = $kursiTersedia > 0 ? 'Tersedia' : 'Penuh';
            $icon = strpos($bus->nama, 'Dipyo') !== false ? 'fa-shuttle-van' : 'fa-bus-alt';
            // GUI: Card Bus (Gaya Media Streaming)
            echo "<a class='bus-card' href='?tap_bus_id={$bus->id}&user_id={$currentUserId}'>";
            echo "<img src='{$bus->imageUrl}' alt='{$bus->nama}' class='bus-card-image' onerror=\"this.onerror=null; this.src='{$FALLBACK_IMAGE_URL}';\">";
            echo "<div class='bus-info'>";
            echo "<h3><i class='fas {$icon}'></i> {$bus->nama}</h3>";
            echo "<p>Lokasi: <strong>{$HALTE_UNDIP[$bus->posisiHalte]}</strong></p>";
            echo "<p>Est. Tiba: <strong>{$bus->waktuTibaEstimasi} WIB</strong></p>";
            echo "<p>Penumpang Aktif: <strong>" . count($bus->penumpangAktif) . " orang</strong></p>";
            echo "<p>Kursi Tersedia: <strong>{$kursiTersedia}</strong> / {$bus->kapasitas}</p>";
            echo "<span class='status-badge {$badgeClass}'>{$statusText}</span>";
            echo "<span class='details-button'>Detail / Tap In</span>";
            echo "</div>";
            echo "</a>";
        }
        ?>
    </div>
</div>
<div class="modal-overlay" id="tapModal">
    <div class="modal-content">
        <?php if ($tappedBus): ?>
            <a href="BusTracker.php?user_id=<?php echo htmlspecialchars($currentUserId); ?>" class="close-button" title="Tutup Modal"><i class="fas fa-times"></i></a>
            <h2 style="color: var(--secondary-color);"><?php echo htmlspecialchars($tappedBus->nama); ?></h2>
            <div class="modal-info">
                <p><i class="fas fa-map-marker-alt"></i> Posisi Sekarang: <strong><?php echo $HALTE_UNDIP[$tappedBus->posisiHalte]; ?></strong></p>
                <p><i class="fas fa-chair"></i> Kursi Tersedia: <strong style="color: <?php echo $tappedBus->getKursiTersedia() > 0 ? 'var(--success)' : 'var(--danger)'; ?>;"><?php echo $tappedBus->getKursiTersedia(); ?></strong> / <?php echo $tappedBus->kapasitas; ?></p>
            </div>
            <?php
            $isUserTappedIn = isset($tappedBus->penumpangAktif[$currentUserId]);
            $userActiveBusName = getUserActiveBus($currentUserId, $busList); // PENGKONDISIAN: Cek bus aktif pengguna

            // LOGIKA UTAMA: PENGKONDISIAN UNTUK TAP IN/OUT
            if ($userActiveBusName !== null && $userActiveBusName !== $tappedBus->nama) {
                 // KASUS 1: Pengguna sudah Tap In di bus lain
                echo '<h3 style="color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> Gagal Tap In!</h3>';
                echo "<p style='font-size: 1rem;'>Anda sudah Tap In di **{$userActiveBusName}**. Silakan Tap Out dari bus tersebut sebelum Tap In di sini.</p>";
            } elseif ($tappedBus->getKursiTersedia() <= 0 && !$isUserTappedIn) {
                // KASUS 2: Bus Penuh dan Pengguna Belum Tap In
                echo '<h3 style="color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> Peringatan: Bus Penuh!</h3>';
                echo '<p style="font-size: 1rem;">Bus ini sudah mencapai kapasitas maksimum (0 kursi tersedia). Tap In gagal.</p>';
            } elseif ($isUserTappedIn) {
                // KASUS 3: Form Tap Out (Jika sudah Tap In di bus ini)
                $userTrip = $tappedBus->penumpangAktif[$currentUserId];
                echo '<h3 style="color: var(--text-light); border-bottom: 1px solid #444; padding-bottom: 0.5rem;"><i class="fas fa-user-check"></i> Perjalanan Aktif Anda</h3>';
                echo '<div class="modal-info" style="border-left-color: var(--success);">';
                echo "<p style='margin: 0.5rem 0;'>**Berangkat:** <strong>" . htmlspecialchars($userTrip['dari']) . "</strong></p>";
                echo "<p style='margin: 0.5rem 0;'>**Tujuan:** <strong>" . htmlspecialchars($userTrip['ke']) . "</strong></p>";
                echo '</div>';
                echo '<h4 style="color: var(--text-light); margin-top: 3rem;"><i class="fas fa-arrow-alt-circle-left"></i> Fitur Tap Out</h4>';
                echo '<form method="POST" action="BusTracker.php">';
                echo "<input type='hidden' name='bus_id_action' value='{$tappedBus->id}'>";
                echo "<input type='hidden' name='action_type' value='tap_out'>";
                echo "<input type='hidden' name='user_id' value='" . htmlspecialchars($currentUserId) . "'>";
                echo '<button type="submit" class="btn-submit btn-tap-out"><i class="fas fa-sign-out-alt"></i> TAP OUT (Saya Sudah Turun)</button>';
                echo '</form>';
            } elseif (!$isUserTappedIn && $tappedBus->getKursiTersedia() > 0) {
                // KASUS 4: Form Tap In (Jika belum Tap In dan ada kursi)
                echo '<h3 style="color: var(--text-light); border-bottom: 1px solid #444; padding-bottom: 0.5rem;"><i class="fas fa-arrow-alt-circle-right"></i> Fitur Tap In</h3>';
                echo '<form method="POST" action="BusTracker.php">';
                echo "<input type='hidden' name='bus_id_action' value='{$tappedBus->id}'>";
                echo "<input type='hidden' name='action_type' value='tap_in'>";
                echo "<input type='hidden' name='user_id' value='" . htmlspecialchars($currentUserId) . "'>";
                // GUI: Dropdown Halte Berangkat
                echo '<div class="form-group"><label for="halte_dari">Berangkat Dari:</label><select name="halte_dari" required><option value="">--- Pilih Halte Keberangkatan ---</option>';
                // PERULANGAN: Halte Berangkat
                foreach ($HALTE_UNDIP as $halte) { echo "<option value='{$halte}'>{$halte}</option>"; }
                echo '</select></div>';
                // GUI: Dropdown Halte Tujuan
                echo '<div class="form-group"><label for="halte_ke">Akan Turun Di (Tujuan):</label><select name="halte_ke" required><option value="">--- Pilih Halte Tujuan ---</option>';
                // PERULANGAN: Halte Tujuan
                foreach ($HALTE_UNDIP as $halte) { echo "<option value='{$halte}'>{$halte}</option>"; }
                echo '</select></div>';
                echo '<button type="submit" class="btn-submit btn-tap-in"><i class="fas fa-check-circle"></i> TAP IN SEKARANG</button>';
                echo '</form>';
            }
            ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>