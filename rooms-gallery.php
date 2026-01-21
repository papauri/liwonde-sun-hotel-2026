<?php
// Liwonde Sun Hotel - Rooms Gallery (Modern, Futuristic, 3D Grid)
require_once 'config/database.php';

$site_name = getSetting('site_name', 'Liwonde Sun Hotel');
$site_logo = getSetting('site_logo', 'images/logo/logo.png');
$site_tagline = getSetting('site_tagline', 'Where Luxury Meets Nature');
$currency_symbol = getSetting('currency_symbol', 'K');
$email_reservations = getSetting('email_reservations', 'book@liwondesunhotel.com');
$phone_main = getSetting('phone_main', '+265 123 456 789');

// Fetch all active rooms
$rooms = [];
try {
	$roomStmt = $pdo->query("SELECT * FROM rooms WHERE is_active = 1 ORDER BY is_featured DESC, display_order ASC, id ASC");
	$rooms = $roomStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	$rooms = [];
}

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
	<title><?php echo htmlspecialchars($site_name); ?> | Rooms Gallery</title>
	<meta name="description" content="A modern gallery of all available rooms at <?php echo htmlspecialchars($site_name); ?>. Explore featured rooms in a 3D interactive grid.">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<link rel="stylesheet" href="css/style.css">
	<style>
		.gallery-hero {
			background: linear-gradient(135deg, #0a1929 0%, #1a2844 100%);
			color: #fff;
			padding: 90px 0 60px 0;
			position: relative;
			overflow: hidden;
		}
		.gallery-hero .container {
			position: relative;
			z-index: 2;
		}
		.gallery-hero__title {
			font-family: var(--font-serif);
			font-size: 3rem;
			font-weight: 700;
			margin-bottom: 18px;
			letter-spacing: 1px;
			text-shadow: 0 8px 32px rgba(10,25,41,0.18);
		}
		.gallery-hero__desc {
			font-size: 1.25rem;
			color: #e0e0e0;
			margin-bottom: 32px;
			max-width: 600px;
		}
		.gallery-hero__bg {
			position: absolute;
			top: 0; left: 0; right: 0; bottom: 0;
			background: url('images/hero/slide1.jpg') center/cover no-repeat;
			opacity: 0.13;
			z-index: 1;
		}
		.gallery-3d-grid {
			perspective: 1200px;
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
			gap: 38px;
			margin-top: 40px;
		}
		.gallery-3d-card {
			background: linear-gradient(135deg, #fff 60%, #f7f7f7 100%);
			border-radius: 22px;
			box-shadow: 0 10px 40px rgba(10,25,41,0.13), 0 1.5px 0 rgba(212,175,55,0.08);
			transition: transform 0.5s cubic-bezier(.25,1.5,.5,1), box-shadow 0.3s;
			position: relative;
			overflow: visible;
			z-index: 1;
			will-change: transform;
		}
		.gallery-3d-card:before {
			content: '';
			position: absolute;
			inset: 0;
			border-radius: 22px;
			box-shadow: 0 24px 60px 0 rgba(212,175,55,0.10), 0 2px 0 rgba(212,175,55,0.08);
			opacity: 0.7;
			z-index: -1;
		}
		.gallery-3d-card:after {
			content: '';
			position: absolute;
			left: 50%;
			bottom: -18px;
			width: 60%;
			height: 32px;
			background: radial-gradient(ellipse at center, rgba(212,175,55,0.13) 0%, rgba(10,25,41,0.01) 100%);
			filter: blur(6px);
			border-radius: 50%;
			transform: translateX(-50%);
			z-index: 0;
		}
		.gallery-3d-card .gallery-card-image img {
			border-radius: 18px 18px 0 0;
			box-shadow: 0 8px 32px rgba(10,25,41,0.10);
			transition: transform 0.5s cubic-bezier(.25,1.5,.5,1);
		}
		.gallery-3d-card:hover .gallery-card-image img {
			transform: scale(1.06) translateY(-4px) rotateZ(-1deg);
		}
		.gallery-3d-card .gallery-card-body {
			padding-bottom: 18px;
		}
		.gallery-3d-card .gallery-card-badge {
			background: linear-gradient(90deg, var(--gold), var(--dark-gold));
			color: #fff;
			box-shadow: 0 2px 8px rgba(212,175,55,0.18);
			position: absolute;
			top: 18px;
			left: 18px;
			padding: 6px 16px;
			border-radius: 12px;
			font-size: 13px;
			font-weight: 600;
		}
		.gallery-3d-card:focus {
			outline: none;
			box-shadow: 0 0 0 4px var(--gold);
		}
		.gallery-3d-card .gallery-card-3d-bg {
			position: absolute;
			inset: 0;
			border-radius: 22px;
			background: linear-gradient(120deg, rgba(212,175,55,0.07) 0%, rgba(10,25,41,0.04) 100%);
			z-index: -2;
		}
	</style>
</head>
<body class="rooms-gallery-page">
	<?php include 'includes/loader.php'; ?>
	<?php include 'includes/header.php'; ?>
	<div class="gallery-hero">
		<div class="gallery-hero__bg"></div>
		<div class="container">
			<h1 class="gallery-hero__title">Our Rooms & Suites</h1>
			<p class="gallery-hero__desc">Step into a world of luxury. Explore our signature rooms and suites in a futuristic 3D gallery. Click any room to view details and book your stay.</p>
		</div>
	</div>
	<main>
		<div class="container">
			<div class="gallery-3d-grid">
				<?php foreach ($rooms as $room): ?>
				<a class="gallery-3d-card" tabindex="0" href="pages/room.php?room=<?php echo urlencode($room['slug']); ?>">
					<div class="gallery-card-3d-bg"></div>
					<div class="gallery-card-image">
						<img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">
						<?php if (!empty($room['badge'])): ?>
						<span class="gallery-card-badge"><?php echo htmlspecialchars($room['badge']); ?></span>
						<?php endif; ?>
					</div>
					<div class="gallery-card-body">
						<h3><?php echo htmlspecialchars($room['name']); ?></h3>
						<p><?php echo htmlspecialchars($room['short_description']); ?></p>
					</div>
				</a>
				<?php endforeach; ?>
			</div>
		</div>
	</main>
	<?php include 'includes/footer.php'; ?>
	<script>
	// 3D card tilt effect for gallery cards
	document.querySelectorAll('.gallery-3d-card').forEach(card => {
	  card.addEventListener('mousemove', function(e) {
		const rect = card.getBoundingClientRect();
		const x = e.clientX - rect.left;
		const y = e.clientY - rect.top;
		const centerX = rect.width / 2;
		const centerY = rect.height / 2;
		const rotateX = ((y - centerY) / centerY) * 10;
		const rotateY = ((x - centerX) / centerX) * 10;
		card.style.transform = `perspective(900px) rotateX(${-rotateX}deg) rotateY(${rotateY}deg) scale(1.04)`;
	  });
	  card.addEventListener('mouseleave', function() {
		card.style.transform = '';
	  });
	  card.addEventListener('focus', function() {
		card.style.boxShadow = '0 0 0 4px var(--gold)';
	  });
	  card.addEventListener('blur', function() {
		card.style.boxShadow = '';
	  });
	});
	</script>
</body>
</html>
