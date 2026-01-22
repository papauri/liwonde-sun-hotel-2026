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
		:root {
			--futuristic-indigo: #5b7cff;
			--futuristic-cyan: #45f0ff;
			--futuristic-pink: #ff66c4;
			--futuristic-ink: #0a0f1f;
		}
		.rooms-gallery-page {
			background: radial-gradient(circle at 20% 20%, rgba(69, 240, 255, 0.15), transparent 45%),
						radial-gradient(circle at 80% 10%, rgba(255, 102, 196, 0.12), transparent 45%),
						radial-gradient(circle at 40% 90%, rgba(91, 124, 255, 0.2), transparent 50%),
						#060a17;
			color: #f7f9ff;
		}
		.gallery-hero {
			background: linear-gradient(135deg, rgba(10, 16, 36, 0.96) 0%, rgba(13, 28, 56, 0.95) 100%);
			color: #fff;
			padding: 110px 0 80px 0;
			position: relative;
			overflow: hidden;
		}
		.gallery-hero::before,
		.gallery-hero::after {
			content: '';
			position: absolute;
			border-radius: 999px;
			filter: blur(0px);
			opacity: 0.6;
			z-index: 1;
			animation: pulse-orb 8s ease-in-out infinite;
		}
		.gallery-hero::before {
			width: 320px;
			height: 320px;
			top: -120px;
			right: 8%;
			background: radial-gradient(circle, rgba(69, 240, 255, 0.4), transparent 70%);
		}
		.gallery-hero::after {
			width: 420px;
			height: 420px;
			bottom: -200px;
			left: -60px;
			background: radial-gradient(circle, rgba(255, 102, 196, 0.35), transparent 70%);
			animation-delay: 1.6s;
		}
		.gallery-hero .container {
			position: relative;
			z-index: 2;
		}
		.gallery-hero__title {
			font-family: var(--font-serif);
			font-size: 3.4rem;
			font-weight: 700;
			margin-bottom: 22px;
			letter-spacing: 1px;
			text-shadow: 0 12px 40px rgba(5, 10, 25, 0.55);
		}
		.gallery-hero__desc {
			font-size: 1.25rem;
			color: #e0e0e0;
			margin-bottom: 32px;
			max-width: 640px;
		}
		.gallery-hero__bg {
			position: absolute;
			top: 0; left: 0; right: 0; bottom: 0;
			background: url('images/hero/slide1.jpg') center/cover no-repeat;
			opacity: 0.2;
			z-index: 1;
			mix-blend-mode: screen;
		}
		.gallery-3d-grid {
			perspective: 1600px;
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
			gap: 28px;
			margin-top: 50px;
			transform-style: preserve-3d;
			align-items: stretch;
		}
		.gallery-3d-card {
			background: linear-gradient(135deg, rgba(12, 20, 40, 0.95), rgba(25, 38, 72, 0.9));
			border-radius: 22px;
			box-shadow: 0 20px 50px rgba(2, 6, 18, 0.6), 0 0 0 1px rgba(111, 160, 255, 0.15);
			transition: transform 0.7s cubic-bezier(.25,1.5,.5,1), box-shadow 0.3s, opacity 0.6s ease;
			position: relative;
			overflow: hidden;
			z-index: 1;
			will-change: transform;
			transform-style: preserve-3d;
			transform: translateY(28px) rotateX(var(--rotate-x, 0deg)) rotateY(var(--rotate-y, 0deg)) translateZ(0);
			backdrop-filter: blur(6px);
			opacity: 0;
			border: 1px solid rgba(91, 124, 255, 0.2);
			isolation: isolate;
			display: flex;
			flex-direction: column;
			min-height: clamp(360px, 52vh, 440px);
			height: 100%;
		}
		.gallery-3d-card:before {
			content: '';
			position: absolute;
			inset: 0;
			border-radius: 22px;
			box-shadow: 0 24px 60px 0 rgba(91, 124, 255, 0.25), 0 2px 0 rgba(69, 240, 255, 0.18);
			opacity: 0.65;
			z-index: -1;
		}
		.gallery-3d-card:after {
			content: '';
			position: absolute;
			left: 50%;
			bottom: -18px;
			width: 60%;
			height: 32px;
			background: radial-gradient(ellipse at center, rgba(91, 124, 255, 0.35) 0%, rgba(10,25,41,0.01) 100%);
			filter: blur(6px);
			border-radius: 50%;
			transform: translateX(-50%);
			z-index: 0;
		}
		.gallery-3d-card .gallery-card-sheen {
			position: absolute;
			inset: 0;
			border-radius: 22px;
			background: linear-gradient(120deg, rgba(69, 240, 255, 0.1), rgba(255, 102, 196, 0.08) 40%, transparent 70%);
			opacity: 0;
			transition: opacity 0.4s ease;
			z-index: 1;
			pointer-events: none;
		}
		.gallery-3d-card .gallery-card-image {
			position: relative;
			height: clamp(180px, 22vw, 220px);
			overflow: hidden;
			transform: translateZ(26px);
			aspect-ratio: 4 / 3;
		}
		.gallery-3d-card .gallery-card-image img {
			border-radius: 18px 18px 0 0;
			box-shadow: 0 12px 40px rgba(2, 6, 18, 0.65);
			transition: transform 0.6s cubic-bezier(.25,1.5,.5,1);
			transform: translateZ(28px);
			width: 100%;
			height: 100%;
			object-fit: cover;
		}
		.gallery-3d-card:hover .gallery-card-image img {
			transform: translateZ(36px) scale(1.06) translateY(-6px) rotateZ(-1deg);
		}
		.gallery-3d-card .gallery-card-body {
			padding-bottom: 22px;
			transform: translateZ(22px);
			display: grid;
			gap: 10px;
			min-height: clamp(150px, 20vh, 190px);
			flex: 1;
		}
		.gallery-3d-card .gallery-card-body h3 {
			min-height: clamp(40px, 6vh, 52px);
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
			overflow: hidden;
		}
		.gallery-3d-card .gallery-card-body p {
			min-height: clamp(60px, 10vh, 84px);
			display: -webkit-box;
			-webkit-line-clamp: 3;
			-webkit-box-orient: vertical;
			overflow: hidden;
		}
		.gallery-3d-card .gallery-card-badge {
			background: linear-gradient(120deg, var(--gold), var(--dark-gold));
			color: #fff;
			box-shadow: 0 8px 18px rgba(212, 175, 55, 0.35);
			position: absolute;
			top: 18px;
			left: 18px;
			padding: 5px 12px;
			border-radius: 999px;
			font-size: 12px;
			font-weight: 600;
			z-index: 3;
			text-transform: uppercase;
			letter-spacing: 0.08em;
		}
		.gallery-3d-card:focus {
			outline: none;
			box-shadow: 0 0 0 3px rgba(69, 240, 255, 0.6), 0 20px 50px rgba(2, 6, 18, 0.6);
		}
		.gallery-3d-card .gallery-card-3d-bg {
			position: absolute;
			inset: 0;
			border-radius: 22px;
			background: linear-gradient(120deg, rgba(91, 124, 255, 0.22) 0%, rgba(6, 12, 28, 0.15) 100%);
			z-index: -2;
		}
		.gallery-3d-card .gallery-card-portal {
			position: absolute;
			inset: 0;
			border-radius: 22px;
			background: conic-gradient(from 120deg, rgba(69, 240, 255, 0.3), rgba(255, 102, 196, 0.2), rgba(91, 124, 255, 0.3), rgba(69, 240, 255, 0.3));
			opacity: 0.18;
			filter: blur(20px);
			transform: translateZ(-40px);
			z-index: -3;
		}
		.gallery-3d-card .gallery-card-ring {
			position: absolute;
			inset: 12px;
			border-radius: 18px;
			border: 1px solid rgba(69, 240, 255, 0.2);
			box-shadow: 0 0 18px rgba(69, 240, 255, 0.15);
			transform: translateZ(8px);
			pointer-events: none;
			z-index: 2;
		}
		.gallery-3d-card .gallery-card-holo {
			position: absolute;
			inset: 18px;
			border-radius: 18px;
			background: linear-gradient(135deg, rgba(69, 240, 255, 0.12), rgba(255, 102, 196, 0.1), transparent 70%);
			mix-blend-mode: screen;
			opacity: 0.55;
			transform: translateZ(14px);
			pointer-events: none;
			z-index: 2;
		}
		.gallery-3d-card .gallery-card-floor {
			position: absolute;
			left: 50%;
			bottom: -30px;
			width: 70%;
			height: 44px;
			background: radial-gradient(ellipse at center, rgba(69, 240, 255, 0.28) 0%, rgba(10,25,41,0.02) 70%);
			filter: blur(12px);
			border-radius: 50%;
			transform: translateX(-50%) translateZ(-20px);
			z-index: -1;
		}
		.gallery-3d-card .gallery-card-frame {
			position: absolute;
			inset: 8px;
			border-radius: 18px;
			border: 1px solid rgba(255, 255, 255, 0.08);
			box-shadow: inset 0 0 18px rgba(69, 240, 255, 0.08);
			transform: translateZ(12px);
			pointer-events: none;
			z-index: 2;
		}
		.gallery-3d-card .gallery-card-body h3 {
			color: #f8fbff;
		}
		.gallery-3d-card .gallery-card-body p {
			color: rgba(234, 240, 255, 0.78);
		}
		.gallery-3d-card:hover .gallery-card-sheen {
			opacity: 1;
		}
		.gallery-3d-card.is-visible {
			animation: float-card 6s ease-in-out infinite, glow-rise 0.9s ease forwards;
			opacity: 1;
			transform: translateY(0) rotateX(var(--rotate-x, 0deg)) rotateY(var(--rotate-y, 0deg));
		}
		.gallery-3d-card.is-visible:nth-child(2n) {
			animation-delay: 0.12s;
		}
		.gallery-3d-card.is-visible:nth-child(3n) {
			animation-delay: 0.24s;
		}
		.gallery-3d-card.is-visible:nth-child(4n) {
			animation-delay: 0.36s;
		}
		.gallery-empty {
			border-radius: 24px;
			background: rgba(12, 20, 40, 0.6);
			padding: 48px;
			text-align: center;
			border: 1px solid rgba(91, 124, 255, 0.2);
			box-shadow: 0 20px 50px rgba(2, 6, 18, 0.45);
		}
		.gallery-empty h2 {
			margin-bottom: 12px;
			color: #f8fbff;
		}
		@keyframes float-card {
			0%, 100% { transform: translateY(0) rotateX(var(--rotate-x, 0deg)) rotateY(var(--rotate-y, 0deg)); }
			50% { transform: translateY(-12px) rotateX(calc(var(--rotate-x, 0deg) + 1deg)) rotateY(calc(var(--rotate-y, 0deg) - 1deg)); }
		}
		@keyframes glow-rise {
			0% {
				opacity: 0;
				transform: translateY(28px) scale(0.96);
			}
			100% {
				opacity: 1;
				transform: translateY(0) scale(1);
			}
		}
		@keyframes pulse-orb {
			0%, 100% { transform: scale(1); opacity: 0.5; }
			50% { transform: scale(1.08); opacity: 0.7; }
		}
		@media (max-width: 768px) {
			.gallery-hero__title {
				font-size: 2.6rem;
			}
			.gallery-3d-grid {
				gap: 22px;
				grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			}
			.gallery-3d-card {
				min-height: clamp(320px, 56vh, 420px);
			}
			.gallery-3d-card .gallery-card-image {
				height: clamp(170px, 32vw, 200px);
			}
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
				<?php if (!empty($rooms)): ?>
					<?php foreach ($rooms as $room): ?>
					<a class="gallery-3d-card" tabindex="0" href="pages/room.php?room=<?php echo urlencode($room['slug']); ?>">
						<div class="gallery-card-3d-bg"></div>
						<div class="gallery-card-portal"></div>
						<div class="gallery-card-ring"></div>
						<div class="gallery-card-holo"></div>
						<div class="gallery-card-sheen"></div>
						<div class="gallery-card-floor"></div>
						<div class="gallery-card-frame"></div>
						<div class="gallery-card-image">
							<img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" loading="lazy">
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
				<?php else: ?>
					<div class="gallery-empty">
						<h2>Rooms are preparing for launch</h2>
						<p>Our futuristic suites are being curated. Please check back soon or reach out to our reservations team for availability.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</main>
	<?php include 'includes/footer.php'; ?>
	<script src="js/main.js"></script>
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
		card.style.setProperty('--rotate-x', `${-rotateX}deg`);
		card.style.setProperty('--rotate-y', `${rotateY}deg`);
		card.style.transform = `perspective(1100px) rotateX(${-rotateX}deg) rotateY(${rotateY}deg) translateZ(0) scale(1.05)`;
	  });
	  card.addEventListener('mouseleave', function() {
		card.style.removeProperty('--rotate-x');
		card.style.removeProperty('--rotate-y');
		card.style.transform = '';
	  });
	  card.addEventListener('focus', function() {
		card.style.boxShadow = '0 0 0 3px rgba(69, 240, 255, 0.6), 0 20px 50px rgba(2, 6, 18, 0.6)';
	  });
	  card.addEventListener('blur', function() {
		card.style.boxShadow = '';
	  });
	});

	const observer = new IntersectionObserver((entries) => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				entry.target.classList.add('is-visible');
			}
		});
	}, { threshold: 0.3 });

	document.querySelectorAll('.gallery-3d-card').forEach(card => observer.observe(card));
	</script>
</body>
</html>
