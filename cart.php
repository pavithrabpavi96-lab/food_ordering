<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["add"])) {
        $id = filter_input(INPUT_POST, "food_id", FILTER_VALIDATE_INT);

        if ($id && $id > 0) {
            $stmt = $conn->prepare("SELECT id FROM foods WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            if ($stmt->get_result()->num_rows > 0) {
                $_SESSION["cart"][$id] =
                    min(99, ($_SESSION["cart"][$id] ?? 0) + 1);
            }
            $stmt->close();
        }
    }

    if (isset($_POST["remove"])) {
        $id = filter_input(INPUT_POST, "food_id", FILTER_VALIDATE_INT);
        if ($id) unset($_SESSION["cart"][$id]);
    }

    if (isset($_POST["update"])) {
        foreach ($_POST["qty"] ?? [] as $id => $qty) {
            $id = filter_var($id, FILTER_VALIDATE_INT);
            $qty = filter_var($qty, FILTER_VALIDATE_INT);

            if ($id && $qty !== false) {
                if ($qty <= 0) unset($_SESSION["cart"][$id]);
                else $_SESSION["cart"][$id] = min(99, $qty);
            }
        }
    }

    header("Location: cart.php");
    exit;
}

$items = [];
$total = 0;

foreach ($_SESSION["cart"] as $id => $qty) {
    $stmt = $conn->prepare("SELECT id,name,price FROM foods WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $food = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$food) {
        unset($_SESSION["cart"][$id]);
        continue;
    }

    $food["quantity"] = $qty;
    $food["subtotal"] = $food["price"] * $qty;
    $total += $food["subtotal"];
    $items[] = $food;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>My Cart</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>My Cart</h1>
    <nav><a href="index.php">Continue Shopping</a></nav>
</header>
<main class="menu">
<?php if (!$items): ?>
    <h2>Your cart is empty!</h2>
<?php else: ?>
    <form method="post">
    <?php foreach ($items as $item): ?>
        <section class="food-card" style="margin-bottom:15px">
            <h3><?= htmlspecialchars($item["name"]) ?></h3>
            <p>Price: ₹<?= number_format($item["price"], 2) ?></p>
            <label>Quantity</label>
            <input type="number" min="0" max="99"
                name="qty[<?= (int)$item["id"] ?>]"
                value="<?= (int)$item["quantity"] ?>">
            <p>Subtotal: ₹<?= number_format($item["subtotal"], 2) ?></p>
            <button type="submit" name="remove"
                value="<?= (int)$item["id"] ?>"
                formaction="cart.php"
                formmethod="post"
                onclick="this.form.insertAdjacentHTML('beforeend',
                '<input type=&quot;hidden&quot; name=&quot;food_id&quot; value=&quot;<?= (int)$item["id"] ?>&quot;>')">
                Remove Item
            </button>
        </section>
    <?php endforeach; ?>
    <button type="submit" name="update">Update Quantities</button>
    </form>
    <h2>Total: ₹<?= number_format($total, 2) ?></h2>
    <a href="checkout.php">Proceed to Checkout</a>
<?php endif; ?>
</main>
</body>
</html>