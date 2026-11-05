<?php
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!$_SESSION["cart"]) {
    header("Location: cart.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customer = trim($_POST["customer_name"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($customer === "" || $address === "" ||
        !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
        $message = "Please enter valid delivery details.";
    } else {
        $conn->begin_transaction();

        try {
            $items = [];
            $total = 0;

            // Re-read current prices from the database.
            foreach ($_SESSION["cart"] as $id => $quantity) {
                $stmt = $conn->prepare(
                    "SELECT id, price FROM foods WHERE id = ?"
                );
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $food = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$food || $quantity < 1 || $quantity > 99) {
                    throw new Exception("A cart item is invalid.");
                }

                $food["quantity"] = $quantity;
                $items[] = $food;
                $total += (float)$food["price"] * $quantity;
            }

            $stmt = $conn->prepare(
                "INSERT INTO orders
                (user_id, customer_name, address, phone, total)
                VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "isssd",
                $_SESSION["user_id"],
                $customer,
                $address,
                $phone,
                $total
            );
            $stmt->execute();
            $orderId = $conn->insert_id;
            $stmt->close();

            $stmt = $conn->prepare(
                "INSERT INTO order_items
                (order_id, food_id, quantity, price)
                VALUES (?, ?, ?, ?)"
            );

            foreach ($items as $item) {
                $foodId = $item["id"];
                $quantity = $item["quantity"];
                $price = $item["price"];

                $stmt->bind_param(
                    "iiid",
                    $orderId,
                    $foodId,
                    $quantity,
                    $price
                );
                $stmt->execute();
            }

            $stmt->close();
            $conn->commit();

            $_SESSION["cart"] = [];
            echo "<h2>Order placed successfully!</h2>";
            echo "<p>Order number: " . (int)$orderId . "</p>";
            echo '<a href="index.php">Continue Shopping</a>';
            exit;
        } catch (Throwable $e) {
            $conn->rollback();
            $message = "Could not place your order. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="menu" style="max-width:600px;margin:auto">
    <h2>Delivery Details</h2>
    <p><?= htmlspecialchars($message) ?></p>
    <form method="post">
        <label>Customer Name</label>
        <input name="customer_name"
               value="<?= htmlspecialchars($_SESSION["user_name"] ?? "") ?>"
               required>
        <label>Delivery Address</label>
        <textarea name="address" required></textarea>
        <label>Phone Number</label>
        <input name="phone" required>
        <button type="submit">Place Order (Cash on Delivery)</button>
    </form>
    <p><a href="cart.php">Back to Cart</a></p>
</main>
</body>
</html>