<?php
//session_start();
include "connection.php";
include "header.php";

// Get product id and cart id
$id = $_GET['id'] ?? null;
$cartid = $_GET['cartid'] ?? null;

if (!$id || !$cartid) {
    echo "<script>alert('Invalid request'); window.location='shoppingcart.php';</script>";
    exit;
}

// Fetch product data
$stmt = $conn->prepare("SELECT * FROM product WHERE product_id=?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    echo "<script>alert('Product not found'); window.location='shoppingcart.php';</script>";
    exit;
}

// Fetch current cart quantity
$cart_qty = 1;
$cart_check = $conn->prepare("SELECT quantity FROM cart WHERE cart_id=?");
$cart_check->bind_param("i", $cartid);
$cart_check->execute();
$cart_res = $cart_check->get_result();
if ($cart_res->num_rows > 0) {
    $cart_data = $cart_res->fetch_assoc();
    $cart_qty = $cart_data['quantity'];
}
$cart_check->close();

// Determine price
$price = (!empty($product['discounted_price']) && $product['discounted_price'] > 0) 
         ? $product['discounted_price'] 
         : $product['product_price'];

// Handle update cart
if(isset($_POST['save'])) {
    $qty = $_POST['qty'];
    $rate = $_POST['rate'];
    $total = $qty * $rate;

    $stmt = $conn->prepare("UPDATE cart SET quantity=?, rate=?, total=? WHERE cart_id=?");
    $stmt->bind_param("iddi", $qty, $rate, $total, $cartid);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Cart updated successfully'); window.location='shoppingcart.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Cart - <?= htmlspecialchars($product['product_name']); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<style>
    .btn-primary { background-color: #f97316; transition: 0.3s; }
    .btn-primary:hover { background-color: #ea580c; }
    .btn-secondary { background-color: #1f2937; transition: 0.3s; }
    .btn-secondary:hover { background-color: #111827; }
</style>
</head>
<body class="bg-gray-50">

<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="flex items-center space-x-4 mb-6">
            <button onclick="window.history.back();" class="flex items-center text-gray-600 hover:text-orange-600">
                <i data-lucide="arrow-left" class="h-4 w-4 mr-1"></i> Back
            </button>
            <h2 class="text-2xl font-bold text-gray-900">Edit Cart</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Image -->
            <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                <img src="uploads/<?= htmlspecialchars($product['product_image']); ?>" 
                     alt="<?= htmlspecialchars($product['product_name']); ?>" 
                     class="w-full h-full object-cover">
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($product['product_name']); ?></h1>
                <p class="text-gray-600"><?= nl2br(htmlspecialchars($product['product_description'])); ?></p>

                <!-- Price -->
                <div class="flex items-center space-x-3">
                    <span class="text-3xl font-bold text-orange-600">₹<?= number_format($price, 2); ?></span>
                    <?php if (!empty($product['discounted_price']) && $product['discounted_price'] > 0): ?>
                        <span class="line-through text-gray-500">₹<?= number_format($product['product_price'], 2); ?></span>
                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-medium">
                            <?= round((($product['product_price'] - $product['discounted_price']) / $product['product_price']) * 100); ?>% OFF
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Edit Cart Form -->
                <form method="post">
                    <div class="flex items-center space-x-4 mb-4">
                        <span class="font-medium text-gray-900">Quantity:</span>
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button type="button" onclick="changeQuantity(-1)" class="px-3 py-2 hover:bg-gray-100">
                                <i data-lucide="minus" class="h-4 w-4"></i>
                            </button>
                            <input type="number" name="qty" id="qty" value="<?= $cart_qty ?>" min="1" class="w-16 text-center px-2 py-2 border-x border-gray-300 focus:outline-none" readonly>
                            <input type="hidden" name="rate" value="<?= $price ?>">
                            <button type="button" onclick="changeQuantity(1)" class="px-3 py-2 hover:bg-gray-100">
                                <i data-lucide="plus" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="save" class="btn-primary text-white py-3 px-6 rounded-lg font-medium">
                        Update Cart
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    let quantity = <?= $cart_qty ?>;

    function changeQuantity(delta) {
        quantity += delta;
        if(quantity < 1) quantity = 1;
        document.getElementById('qty').value = quantity;
    }
</script>

</body>
</html>

<?php include "footer.php"; ?>
