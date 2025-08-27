<style>
/*Untuk Logo*/
.logo-neon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
        box-shadow: 0 0 5px rgba(38, 38, 40, 1), 0 0 10px rgba(82, 85, 85, 1), 0 0 20px rgba(217, 222, 222, 1), 0 0 40px rgba(59, 59, 67, 1);
        animation: neon-glow 2s infinite alternate;
    }

    @keyframes neon-glow {
        from {
            box-shadow: 0 0 5px rgba(85, 85, 87, 1), 0 0 10px rgba(42, 43, 43, 1), 0 0 20px rgba(15, 17, 17, 1), 0 0 40px rgba(163, 163, 168, 1);
        }
        to {
            box-shadow: 0 0 10px rgba(32, 32, 32, 1), 0 0 20px rgba(91, 91, 95, 1), 0 0 40px rgba(113, 115, 115, 1), 0 0 80px rgba(63, 63, 67, 1);
        }
    }


.running-text {
	width: 100%;
    background: #212529; /* warna hitam gelap biar nyatu sama navbar */
    color: #fff;
    font-family: 'Brush Script MT', cursive; /* huruf sambung */
    font-size: 20px;
    padding: 5px 0;
    text-align: center;
    letter-spacing: 1px;
}
</style> 
<?php
?>

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
<a class="navbar-brand ps-3" href="index.php">
    <div class="d-flex align-items-center">
        <!-- Foto bulat dengan efek neon glow -->
        <img src="assets/img/Admin.JPG" alt="Logo" 
             class="logo-neon">

        <!-- Tulisan -->
        <div class="d-flex flex-column lh-1" style="margin-top:6px;">
            <span style="font-family:'Great Vibes', cursive; font-size:28px; line-height:1; color:#fff;">Graceful</span>
            <span style="font-size:10px; letter-spacing:3px; color:#ddd; text-transform:none;">- decoration -</span>
        </div>
    </div>
</a>
	<button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
	<ul class="navbar-nav ml-auto ml-md-0">
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" id="userDropdown" href="#" data-bs-toggle="dropdown"><i class="fas fa-user fa-fw"></i></a>
			<div class="dropdown-menu dropdown-menu-end">
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" href="Logout.php">Logout</a>
			</div>
		</li>
		</ul>
		<div class="running-text">
    <marquee behavior="scroll" direction="left" scrollamount="5">
        Kp Tanjung Jaya, Desa Dawagung, Kecamatan Rajapolah, Kabupaten Tasikmalaya, Provinsi Jawa Barat
    </marquee>
</div>
	</nav>

