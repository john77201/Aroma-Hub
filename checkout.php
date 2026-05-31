<?php
// CRITICAL FIX: session_start() must be the very first thing in the file.
//session_start(); 

include "header.php"; 
include "connection.php";

// Redirect to login if user not logged in
if(!isset($_SESSION['userid'])){
    echo "<script>window.location='login.php';</script>";
    exit;
}

$userid = $_SESSION['userid'];

// Fetch user details for address fields (Read-Only)
$sql = "SELECT * FROM user WHERE userid='$userid'";
$rs = mysqli_query($conn, $sql);
$user_data = mysqli_fetch_array($rs);

// --- Initialization ---
$stotal = 0; // Subtotal used for calculation (after product discounts, before coupon)
$original_subtotal = 0; // Subtotal before any discounts
$shipping_cost = 0; 
$cart_items_to_show = []; // Items array containing DB and Session items
$items_to_insert = []; // Array of items for DB insertion (used during 'save')

// --- 1. Fetch Regular Cart Items (DB-based) ---
$qry_db = "SELECT cart.cart_id, cart.quantity, product.product_id, product.product_name, product.product_image, product.product_price, product.discounted_price 
           FROM cart
           INNER JOIN product ON cart.product_id = product.product_id
           WHERE cart.userid='$userid' AND cart.status=0";
$result_db = mysqli_query($conn, $qry_db); 

while ($data = mysqli_fetch_assoc($result_db)) {
    $price_to_use = (!empty($data['discounted_price']) && $data['discounted_price'] > 0) 
                     ? $data['discounted_price'] 
                     : $data['product_price'];
    $row_total = $price_to_use * $data['quantity'];
    
    // Accumulate the final discounted subtotal
    $stotal += $row_total; 
    
    // Accumulate the original subtotal (using full price if available, for display only)
    $original_subtotal += $data['product_price'] * $data['quantity']; 
    
    // Data for display
    $cart_items_to_show[] = [
        'type' => 'regular',
        'name' => $data['product_name'],
        'quantity' => $data['quantity'],
        'price' => $price_to_use, // Final price per unit
        'total' => $row_total,     // Final price total
        'image' => "uploads/" . $data['product_image'] ?? 'placeholder.jpg'
    ];
}

// --- 2. Fetch Custom Gift Box Items (SESSION-based) ---
$custom_items_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $session_key => $custom_box) {
        if (isset($custom_box['type']) && $custom_box['type'] === 'custom_gift_box') {
            
            // Add custom box total to the final subtotal for payment calculation
            $stotal += $custom_box['total'];
            $original_subtotal += $custom_box['total']; // Assuming no discount on custom boxes yet
            $custom_items_count++;
            
            // Data for display
            $cart_items_to_show[] = [
                'type' => 'custom',
                'session_key' => $session_key,
                'name' => 'Custom Gift Box #' . $custom_items_count,
                'total' => $custom_box['total'],
                'spices' => $custom_box['spices'],
                'packaging_cost' => $custom_box['packaging_cost'],
                'gift_message' => $custom_box['gift_message'] ?? ''
            ];
        }
    }
}


// --- 3. Apply Discounts and Calculate Grand Total ---

// Calculate product-level discount (difference between original price and discounted total)
$product_discount_amount = $original_subtotal - $stotal;
if ($product_discount_amount < 0) $product_discount_amount = 0; // Sanity check

// Retrieve coupon discount amount from session (set in shoppingcart.php)
$coupon_session_discount = isset($_SESSION['coupon_discount']) ? floatval($_SESSION['coupon_discount']) : 0;

// Subtotal after ALL product discounts: $stotal
$subtotal_after_product_discount = $stotal;

// Apply the session coupon discount
$final_payable_subtotal = $subtotal_after_product_discount - $coupon_session_discount;

// Calculate final total
$grand_total = $final_payable_subtotal + $shipping_cost; 
$grand_total = max(0, $grand_total); // Final total must be zero or positive

$total_displayed_discount = $product_discount_amount + $coupon_session_discount;


// --- 4. Order Submission Logic ---
// --- 4. Order Submission Logic ---
if (isset($_POST['save'])) {
    
    $orderno = "AROMAHUB" . rand(100000, 999999); 
    
    // Sanitize and prepare address fields
    $address1 = mysqli_real_escape_string($conn, $_POST['address1']);
    $address2 = mysqli_real_escape_string($conn, $_POST['address2']);
    $address = trim($address1 . " " . $address2);
    $town = mysqli_real_escape_string($conn, $_POST['town']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $postcode = mysqli_real_escape_string($conn, $_POST['pin']);
    $date = date('d/m/Y');

    // 4.1. Insert Order into 'orders' table
    // CRITICAL FIX: Removing 'coupon_discount' and 'product_discount' from the query 
    // as they do not exist in the database table (confirmed by your screenshot).
    $q1 = "INSERT INTO orders (userid, orderno, address, town, state, postcode, status, dt, total_amount) 
            VALUES ('$userid','$orderno','$address','$town','$state','$postcode',0,'$date', '$grand_total')";
    
    // Line 123 in your file:
    mysqli_query($conn, $q1); 

    // 4.2. Update Regular Cart Items to status=1
    $q_update_cart = "UPDATE cart SET orderno='$orderno', status=1 WHERE userid='$userid' AND status=0";
    mysqli_query($conn, $q_update_cart);

    // 4.3. Handle Custom Gift Box Items (Clearing session data is fine for now)
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $session_key => $custom_box) {
            if (isset($custom_box['type']) && $custom_box['type'] === 'custom_gift_box') {
                // Remove the custom box from the session cart
                unset($_SESSION['cart'][$session_key]); 
            }
        }
    }
    
    // 4.4. Clear coupon session data once the order is placed
    unset($_SESSION['coupon_discount']);
    unset($_SESSION['coupon_code']);

    // 4.5. Redirect to payment
    $_SESSION['orderno'] = $orderno;
    echo "<script>window.location='payment.php';</script>";
    exit;
}
// --- END PHP Logic ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Aroma Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316',
                        secondary: '#dc2626',
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .input-group { position: relative; }
        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            top: -8px; left: 12px; font-size: 0.75rem; color: #f97316; background: white; padding: 0 4px;
        }
        .input-group label {
            position: absolute; top: 12px; left: 16px; transition: all 0.2s ease; pointer-events: none; color: #6b7280; background: white;
        }
        .input-group input:required:invalid:not(:focus):not(:placeholder-shown) { border-color: #ef4444; }
        .card-shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .card-shadow:hover { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body>
    <div class="bg-white border-b">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Checkout</h1>
                <p class="text-gray-600">Complete your order details below</p>
            </div>
        </div>
    </div>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="bg-white rounded-2xl card-shadow p-8 transition-all duration-300">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-shipping-fast text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Shipping Information</h2>
                </div>

                <form action="" method="post" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="input-group">
                            <input type="text" id="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" placeholder=" " class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all" readonly>
                            <label for="username">User Name (Read-Only)</label>
                        </div>
                        <div class="input-group">
                             <input type="text" id="phone" value="<?php echo htmlspecialchars($user_data['phonenumber']); ?>" placeholder=" " class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all" readonly>
                            <label for="phone">Phone Number (Read-Only)</label>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" placeholder=" " class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all" readonly>
                        <label for="email">Email Address (Read-Only)</label>
                    </div>

                    <div class="input-group">
                        <input type="text" id="address1" name="address1" placeholder=" " class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all" required>
                        <label for="address1">Street Address *</label>
                    </div>
                    <div class="input-group">
                        <input type="text" id="address2" name="address2" placeholder=" " class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <label for="address2">Apartment, suite, unit etc. (optional)</label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                         <div class="input-group">
                            <input type="text" id="town" name="town" placeholder=" " class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all" required>
                            <label for="town">Town / City *</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="state" name="state" placeholder=" " class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all" required>
                            <label for="state">State / County *</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="pin" name="pin" placeholder=" " class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all" required>
                            <label for="pin">Postcode / Zip *</label>
                        </div>
                    </div>

                    <div class="pt-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Options</h3>
                        <div class="p-4 border border-primary bg-orange-50 rounded-xl">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900 flex items-center">
                                    <i class="fas fa-check-circle text-primary mr-2"></i> Standard Delivery
                                </span>
                                <span class="text-primary font-semibold">FREE</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">5-7 business days (Free)</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 pt-4">
                        <button type="submit" name="save" class="w-full bg-gradient-to-r from-primary to-secondary text-white py-4 px-6 rounded-xl hover:from-orange-600 hover:to-red-700 transition-all duration-300 font-semibold text-lg shadow-lg hover:shadow-xl">
                            Place Order & Continue to Payment
                        </button>
                    </div>

                </form>
            </div>

            <div class="bg-white rounded-2xl card-shadow p-8 transition-all duration-300 h-fit sticky top-8">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-receipt text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Order Summary</h2>
                </div>

                <div class="space-y-4 mb-8">
                    <?php
                    $custom_box_counter = 0;
                    // Display Cart Items (DB + Session)
                    foreach ($cart_items_to_show as $item) {
                        if ($item['type'] === 'regular'):
                            $image_path = htmlspecialchars($item['image']); 
                            $final_image_src = file_exists($image_path) ? $image_path : 'https://via.placeholder.com/80?text=Product';
                    ?>
                        <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-orange-50 to-red-50 rounded-xl">
                            <img src="<?php echo $final_image_src; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-16 h-16 object-cover rounded-xl">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-sm text-gray-600">Qty: <?php echo $item['quantity']; ?> @ ₹<?php echo number_format($item['price'], 2); ?></span>
                                    <span class="font-semibold text-primary">₹<?php echo number_format($item['total'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($item['type'] === 'custom'): 
                            $custom_box_counter++;
                    ?>
                         <div class="p-4 border border-orange-300 bg-yellow-50 rounded-xl">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-bold text-orange-700"><?= $item['name'] ?></h3>
                                <span class="font-semibold text-primary">₹<?= number_format($item['total'], 2); ?></span>
                            </div>
                            <ul class="text-sm text-gray-700 list-disc list-inside space-y-1 ml-2">
                                <?php foreach ($item['spices'] as $spice): ?>
                                    <li><?= htmlspecialchars($spice['name']) ?> (<?= $spice['quantity'] ?>)</li>
                                <?php endforeach; ?>
                                <li class="font-medium">Packaging: ₹<?= number_format($item['packaging_cost'], 2) ?></li>
                            </ul>
                            <?php if (!empty($item['gift_message'])): ?>
                                <p class="text-xs text-gray-500 mt-2 border-t pt-1">Msg: <?= htmlspecialchars(substr($item['gift_message'], 0, 50)) ?>...</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; } ?>
                </div>
                
                <div class="space-y-3 border-t border-gray-200 pt-6">
                    <div class="flex justify-between text-gray-600">
                        <span>Items Subtotal (Original)</span> 
                        <span id="subtotal">₹<?php echo number_format($original_subtotal, 2); ?></span>
                    </div>

                    <div id="discount-row" class="flex justify-between font-medium <?php echo $total_displayed_discount > 0 ? 'text-green-600' : 'hidden'; ?>">
                        <span>Total Discount</span>
                        <span id="discount">-₹<?php echo number_format($total_displayed_discount, 2); ?></span>
                    </div>

                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span id="shipping">FREE</span>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-gray-900">Total Payable</span>
                            <span class="text-2xl font-bold text-primary" id="total">₹<?php echo number_format($grand_total, 2); ?></span>
                        </div>
                        <?php if (isset($_SESSION['coupon_code'])): ?>
                            <p class="text-xs text-green-600 mt-1">Applied Coupon: **<?php echo htmlspecialchars($_SESSION['coupon_code']); ?>**</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="space-y-3 mt-8">
                    <a href="shoppingcart.php" class="w-full border border-gray-300 text-gray-700 py-3 px-6 rounded-xl hover:bg-gray-50 transition-colors font-medium flex items-center justify-center">
                       <i class="fas fa-arrow-left mr-2"></i> Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include "footer.php"; ?> 
</body>
</html>