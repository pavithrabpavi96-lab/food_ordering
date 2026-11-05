<?php
require "db.php";
$result = $conn->query("SELECT * FROM foods ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Foodie - Online Food Ordering</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Foodie 🍔</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="cart.php">My Cart</a>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<section class="hero">
    <h2>Delicious Food, Delivered to You!</h2>
    <p>Choose your favourite food and order online.</p>
</section>

<main class="menu">
    <h2>Our Food Menu</h2>
    <div class="food-grid">
    <?php while ($food = $result->fetch_assoc()): ?>
        <article class="food-card">
            <img
                src="<?= htmlspecialchars($food['image']) ?>"
                alt="<?= htmlspecialchars($food['name']) ?>"
                onerror="this.style.display='none'">
            <h3><?= htmlspecialchars($food['name']) ?></h3>
            <p><?= htmlspecialchars($food['description'] ?? '') ?></p>
            <p class="price">
                ₹<?= number_format((float)$food['price'], 2) ?>
            </p>
            <form action="cart.php" method="post">
                <input type="hidden" name="food_id"
                       value="<?= (int)$food['id'] ?>">
                <button type="submit" name="add">Add to Cart</button>
            </form>
        </article>
    <?php endwhile; ?>
    </div>
</main>

<footer>Online Food Ordering System | Mini Project</footer>
<script src="script.js"></script>
</body>
</html>