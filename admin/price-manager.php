<?php
session_start();

// Simple authentication system - CHANGE THE PASSWORD IN PRODUCTION!
$valid_password = 'hotel_admin_2026';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $valid_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Invalid password!";
    }
}

// Check if logout was requested
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Check if admin is logged in
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

// If logged in and form submitted, update prices
if ($is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_prices'])) {
    $config_file = '../includes/pricing-config.php';

    if (file_exists($config_file)) {
        $config_content = file_get_contents($config_file);

        // Update prices in the config file
        $config_content = preg_replace(
            "/'price' => \d+(?=,\s*'currency_symbol')/",
            "'price' => " . (int)$_POST['standard_price'],
            $config_content
        );

        $config_content = preg_replace(
            "/('deluxe'.*?)'price' => \d+/s",
            "$1'price' => " . (int)$_POST['deluxe_price'],
            $config_content
        );

        $config_content = preg_replace(
            "/('executive_suite'.*?)'price' => \d+/s",
            "$1'price' => " . (int)$_POST['executive_suite_price'],
            $config_content
        );

        $config_content = preg_replace(
            "/('family_suite'.*?)'price' => \d+/s",
            "$1'price' => " . (int)$_POST['family_suite_price'],
            $config_content
        );

        if (file_put_contents($config_file, $config_content)) {
            $success_message = "Prices updated successfully!";
        } else {
            $error_message = "Failed to update prices!";
        }
    } else {
        $error_message = "Configuration file not found!";
    }
}

// Load current prices if logged in
$roomPrices = [
    'standard' => ['name' => 'Standard Room', 'price' => 120],
    'deluxe' => ['name' => 'Deluxe Room', 'price' => 180],
    'executive_suite' => ['name' => 'Executive Suite', 'price' => 280],
    'family_suite' => ['name' => 'Family Suite', 'price' => 220]
];

if ($is_logged_in) {
    if (file_exists('../includes/pricing-config.php')) {
        include '../includes/pricing-config.php';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Liwonde Sun Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #0a1929;
            --secondary-color: #d4af37;
            --accent-color: #8b0000;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --light-gray: #e9ecef;
            --transition: all 0.3s ease;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 10px 25px rgba(0, 0, 0, 0.15);
            --border-radius: 8px;
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Poppins', sans-serif;
            --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, #1a3a5f 100%);
            --gradient-secondary: linear-gradient(135deg, var(--secondary-color) 0%, #f0c419 100%);
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
        }

        body {
            font-family: var(--font-body);
            line-height: 1.6;
            color: var(--dark-color);
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
        }

        header h1 {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .admin-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 2.5rem;
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-hover);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logout a {
            color: var(--secondary-color);
            text-decoration: none;
            padding: 8px 15px;
            border: 1px solid var(--secondary-color);
            border-radius: 5px;
            transition: var(--transition);
        }

        .logout a:hover {
            background: var(--secondary-color);
            color: var(--primary-color);
        }

        .notification {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            display: none;
        }

        .success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid rgba(40, 167, 69, 0.5);
            color: #28a745;
        }

        .error {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #dc3545;
        }

        .price-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .price-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1.8rem;
            border: 1px solid var(--glass-border);
            transition: var(--transition);
        }

        .price-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            background: rgba(255, 255, 255, 0.08);
        }

        .price-card h3 {
            font-family: var(--font-heading);
            color: var(--secondary-color);
            margin-bottom: 1.2rem;
            font-size: 1.4rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #e2e8f0;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(0, 0, 0, 0.3);
            color: white;
            font-size: 1rem;
            transition: var(--transition);
        }

        input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.3);
        }

        .save-btn {
            display: block;
            margin: 2rem auto 0;
            padding: 15px 40px;
            background: var(--gradient-secondary);
            color: var(--dark-color);
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .save-btn:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-hover);
        }

        .login-container {
            max-width: 500px;
            margin: 10rem auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            text-align: center;
        }

        .login-container h2 {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            font-family: var(--font-heading);
        }

        .form-group.login {
            margin-bottom: 1.5rem;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--gradient-secondary);
            color: var(--dark-color);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-login:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($is_logged_in): ?>
            <header>
                <h1>Admin Dashboard</h1>
                <p>Manage Liwonde Sun Hotel Content & Pricing</p>
            </header>

            <div class="admin-panel">
                <div class="panel-header">
                    <h2>Price Management</h2>
                    <div class="logout">
                        <a href="?logout=1">Logout</a>
                    </div>
                </div>

                <?php if (isset($success_message)): ?>
                    <div class="notification success" style="display: block;">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="notification error" style="display: block;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="price-grid">
                        <div class="price-card">
                            <h3>Standard Room</h3>
                            <div class="form-group">
                                <label for="standard_price">Price per Night (USD)</label>
                                <input type="number" id="standard_price" name="standard_price" value="<?php echo $roomPrices['standard']['price']; ?>" min="0" required>
                            </div>
                        </div>

                        <div class="price-card">
                            <h3>Deluxe Room</h3>
                            <div class="form-group">
                                <label for="deluxe_price">Price per Night (USD)</label>
                                <input type="number" id="deluxe_price" name="deluxe_price" value="<?php echo $roomPrices['deluxe']['price']; ?>" min="0" required>
                            </div>
                        </div>

                        <div class="price-card">
                            <h3>Executive Suite</h3>
                            <div class="form-group">
                                <label for="executive_suite_price">Price per Night (USD)</label>
                                <input type="number" id="executive_suite_price" name="executive_suite_price" value="<?php echo $roomPrices['executive_suite']['price']; ?>" min="0" required>
                            </div>
                        </div>

                        <div class="price-card">
                            <h3>Family Suite</h3>
                            <div class="form-group">
                                <label for="family_suite_price">Price per Night (USD)</label>
                                <input type="number" id="family_suite_price" name="family_suite_price" value="<?php echo $roomPrices['family_suite']['price']; ?>" min="0" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="update_prices" class="save-btn">Update Prices</button>
                </form>
            </div>
        <?php else: ?>
            <div class="login-container">
                <h2>Admin Login</h2>
                <?php if (isset($error)): ?>
                    <div class="notification error" style="display: block; margin-bottom: 1.5rem;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <form method="post">
                    <div class="form-group login">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-login">Login</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-hide notifications after 3 seconds
        setTimeout(function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
                notification.style.display = 'none';
            });
        }, 3000);
    </script>
</body>
</html>