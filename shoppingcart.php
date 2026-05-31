<?php
// CRITICAL FIX: session_start() must be the very first line of PHP code.
//session_start();

include 'connection.php';
include "header.php"; 

// Ensure user logged in
if (!isset($_SESSION['userid'])) {
    echo "<script>alert('Please log in to view your cart.'); window.location.href='login.php';</script>";
    exit;
}

$userid = $_SESSION['userid'];

// --- 1. Fetch Regular Cart Items (DB-based) ---
$sql = "SELECT cart.cart_id, cart.quantity, 
                product.product_id, product.product_name, product.product_price, 
                product.discounted_price, product.product_image 
        FROM cart 
        INNER JOIN product ON cart.product_id = product.product_id 
        WHERE cart.userid = '$userid' AND cart.status=0";
$result = mysqli_query($conn, $sql);

$cart_items = []; // For regular products
$subtotal_before_coupon = 0; 
$item_count = 0; // Total quantity of items

while ($row = mysqli_fetch_assoc($result)) {
    $price_to_use = (!empty($row['discounted_price']) && $row['discounted_price'] > 0) 
                     ? $row['discounted_price'] 
                     : $row['product_price'];
    
    // NOTE: The 'rate' in checkout.php corresponds to $price_to_use (final selling price)
    $row_total = $price_to_use * $row['quantity'];
    
    $subtotal_before_coupon += $row_total;
    $item_count += $row['quantity']; // Add quantity of regular product

    $cart_items[] = [
        "type"      => "regular",
        "cart_id"   => $row['cart_id'],
        "product_id"=> $row['product_id'],
        "name"      => $row['product_name'],
        "image"     => "uploads/" . $row['product_image'],
        "price"     => $row['product_price'],
        "discount"  => $row['discounted_price'],
        "final_price" => $price_to_use,
        "quantity"  => $row['quantity'],
        "total"     => $row_total
    ];
}

// --- 2. Fetch Custom Gift Box Items (SESSION-based) ---
$custom_items = [];
// Use a temporary array to correctly re-key the session keys for the delete link
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $session_key => $custom_box) {
        if (isset($custom_box['type']) && $custom_box['type'] === 'custom_gift_box') {
            
            // Add the total calculated and validated by the server (customize_giftbox.php)
            $subtotal_before_coupon += $custom_box['total'];
            
            // Count the number of spices in the box to add to item_count
            if (isset($custom_box['spices']) && is_array($custom_box['spices'])) {
                 $item_count += count($custom_box['spices']); 
            }
            
            // Store the item with its unique session key for potential removal later
            $custom_items[] = array_merge($custom_box, ['session_key' => $session_key]);
        }
    }
}


// --- Final Cart Status Check and Coupon Handling ---
$total_items_in_cart = count($cart_items) + count($custom_items);

// 🎯 FIX: If cart is truly empty (no DB items AND no session items), clear coupon
if ($total_items_in_cart === 0) {
    // If cart is empty, clear any active coupon session data
    unset($_SESSION['coupon_discount']);
    unset($_SESSION['coupon_code']);
}


// 🎯 Handle Coupon Session Data on Load
$coupon_discount = 0;
$coupon_code = '';
if (isset($_SESSION['coupon_discount']) && isset($_SESSION['coupon_code'])) {
    $coupon_discount = floatval($_SESSION['coupon_discount']);
    $coupon_code = $_SESSION['coupon_code'];
}

$grand_total = $subtotal_before_coupon - $coupon_discount;
if ($grand_total < 0) $grand_total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - Aroma Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="bg-gray-50 font-sans">

<div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
        <h2 class="text-xl font-bold p-6 border-b border-gray-200">Your Items (<?= $total_items_in_cart ?>)</h2>

        <?php if ($total_items_in_cart > 0): ?>
            
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item border-b border-gray-200 p-6 flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-20 h-20 object-cover rounded-lg">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1"><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="text-sm text-gray-500">Qty: <?= $item['quantity'] ?></p>
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-bold text-orange-600">₹<?= number_format($item['final_price'], 2) ?>/g</span>
                            <?php if ($item['discount'] && $item['discount'] > 0): ?>
                                <span class="text-sm text-gray-500 line-through">₹<?= number_format($item['price'], 2) ?>/g</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-lg font-bold text-gray-900 min-w-20 text-right">₹<?= number_format($item['total'], 2) ?></span>
                        <a href="editcart.php?id=<?= $item['product_id'] ?>&cartid=<?= $item['cart_id'] ?>" class="text-blue-500 hover:text-blue-600 p-2 rounded-lg hover:bg-blue-50">
                            <i data-lucide="edit" class="h-4 w-4"></i>
                        </a>
                        <a href="deletecart.php?id=<?= $item['cart_id'] ?>" class="text-red-500 hover:text-red-600 p-2 rounded-lg hover:bg-red-50">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php foreach ($custom_items as $box_key => $box): ?>
                <div class="cart-item border-b border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-orange-700">Custom Gift Box #<?= $box_key + 1 ?></h3>
                        <div class="flex items-center space-x-3">
                            <span class="text-lg font-bold text-gray-900">₹<?= number_format($box['total'], 2) ?></span>
                            <a href="delete_custom_cart.php?key=<?= htmlspecialchars($box['session_key']) ?>" class="text-red-500 hover:text-red-600 p-2 rounded-lg hover:bg-red-50" title="Remove entire gift box">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-3 pl-4 border-l-2 border-orange-200">
                        <?php if(isset($box['spices']) && is_array($box['spices'])): ?>
                            <?php foreach ($box['spices'] as $spice): ?>
                                <div class="flex justify-between items-center text-sm text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <img src="<?= htmlspecialchars($spice['image']) ?>" alt="<?= htmlspecialchars($spice['name']) ?>" class="w-8 h-8 object-cover rounded-full">
                                        <span><?= htmlspecialchars($spice['name']) ?> (<?= $spice['quantity'] ?>)</span>
                                    </div>
                                    <span class="font-medium">₹<?= number_format($spice['price'], 2) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="flex justify-between text-sm text-gray-600 border-t border-orange-100 pt-2">
                            <span>Gift Packaging:</span>
                            <span class="font-medium">₹<?= number_format($box['packaging_cost'], 2) ?></span>
                        </div>
                        <?php if (!empty($box['gift_message'])): ?>
                            <div class="text-xs text-gray-500 pt-2 border-t border-gray-100">
                                <strong>Gift Message:</strong> <?= nl2br(htmlspecialchars($box['gift_message'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
        <?php else: ?>
            <div class="p-8 text-center text-gray-600">Your cart is empty.</div>
        <?php endif; ?>
    </div>

    <div class="lg:col-span-1 bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-24">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Summary</h3>
        
        <div class="mb-6">
            <label for="promo-code" class="block text-sm font-medium text-gray-700 mb-2">Promo Code</label>
            <div class="flex space-x-2">
                <input type="text" id="promo-code" placeholder="Enter code" value="<?= htmlspecialchars($coupon_code) ?>"
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                <button type="button" onclick="applyPromoCode()" 
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Apply
                </button>
            </div>
            <?php if ($coupon_code && $coupon_discount > 0): ?>
                <p class="text-xs text-green-600 mt-2">Coupon **<?= htmlspecialchars($coupon_code) ?>** successfully applied!</p>
            <?php elseif ($coupon_code && $coupon_discount == 0): ?>
                <p class="text-xs text-red-500 mt-2">Coupon code is invalid or has expired.</p>
            <?php endif; ?>
        </div>

        <div class="space-y-3 border-t border-gray-200 pt-6">
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Subtotal (<?= $item_count ?> items)</span>
            <span class="font-medium" id="subtotal-display">₹<?= number_format($subtotal_before_coupon, 2) ?></span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Shipping</span>
            <span class="font-medium text-green-600">Free</span>
          </div>
          
          <?php if ($coupon_discount > 0): ?>
          <div id="coupon-discount-row" class="flex justify-between text-sm text-green-600 font-semibold">
              <span>Coupon Discount</span>
              <span id="coupon-discount-value">-₹<?= number_format($coupon_discount, 2) ?></span>
          </div>
          <?php endif; ?>

          <div class="border-t border-gray-200 pt-3">
            <div class="flex justify-between">
              <span class="text-lg font-semibold text-gray-900">Total</span>
              <span id="final-total-display" class="text-lg font-bold text-orange-600">₹<?= number_format($grand_total, 2) ?></span>
            </div>
          </div>
        </div>
        
        <?php if ($total_items_in_cart > 0): ?>
            <a href="checkout.php" class="w-full block text-center bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg transition-colors font-medium mt-6">
              <i data-lucide="credit-card" class="h-4 w-4 mr-2 inline"></i> Proceed to Checkout
            </a>
        <?php else: ?>
            <a href="index.php" class="w-full block text-center bg-gray-400 text-white py-3 rounded-lg transition-colors font-medium mt-6 cursor-not-allowed">
              <i data-lucide="credit-card" class="h-4 w-4 mr-2 inline"></i> Cart is Empty
            </a>
        <?php endif; ?>
        
        <div class="mt-6 text-center">
        <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
             <img src="images/ssl/ssl.jpg" alt="SSL Secure" class="h-10 w-15">
             <span>Secure SSL encrypted checkout</span>
        </div>
    </div>
    <div class="mt-4 p-4 bg-orange-50 rounded-lg">
        <div class="flex items-center space-x-2 text-sm">
            <i data-lucide="truck" class="h-4 w-4 text-orange-600"></i>
            <span class="text-gray-700">
                <strong>Free shipping</strong> on all orders
            </span>
        </div>
        <p class="text-sm text-gray-600 mt-1">Estimated delivery: 3-5 business days</p>
    </div>
        
    </div>
</div>

<script>
    // Get the initial subtotal before the coupon (RAW number)
    const INITIAL_SUBTOTAL = parseFloat(<?= $subtotal_before_coupon ?>); 
    const summarySpace = document.querySelector('.space-y-3.border-t.border-gray-200.pt-6');
    
    // Helper function to update the display
    function updateSummary(newTotalFormatted, discount) {
        
        // 1. Update Total
        document.getElementById("final-total-display").innerText = "₹" + newTotalFormatted;
        
        // 2. Update Discount Row 
        let discountRow = document.getElementById('coupon-discount-row');

        // Clean discount string (remove commas) before checking if > 0
        const numericDiscount = parseFloat(discount.replace(/,/g, ''));
        
        if (numericDiscount > 0) {
            if (!discountRow) {
                // Create the discount row element if it doesn't exist
                discountRow = document.createElement('div');
                discountRow.id = 'coupon-discount-row';
                discountRow.className = 'flex justify-between text-sm text-green-600 font-semibold';
                discountRow.innerHTML = '<span>Coupon Discount</span><span id="coupon-discount-value"></span>';
                
                // Insert it before the total line (the last div in summarySpace)
                const totalDiv = summarySpace.querySelector('.border-t.border-gray-200.pt-3');
                summarySpace.insertBefore(discountRow, totalDiv); 
            }
            document.getElementById('coupon-discount-value').innerText = "-₹" + discount;
        } else if (discountRow) {
            // Remove the discount row if the discount is zero/cleared
            discountRow.remove();
        }
    }


    function applyPromoCode() {
        const promoInput = document.getElementById('promo-code');
        const code = promoInput.value.trim();
        
        const baseTotal = INITIAL_SUBTOTAL; 

        // 1. Make AJAX request to the server
        fetch('apply_coupon.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            // Send the raw numeric total
            body: `code=${encodeURIComponent(code)}&total=${baseTotal}` 
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Pass the formatted string from the server to the update function
                updateSummary(data.new_total_formatted, data.discount_formatted); // Use discount_formatted from server
                alert(data.message);
                // Reload the page to correctly show the PHP-rendered success/failure message
                window.location.reload(); 
            } else {
                // Pass the original formatted total back for display (no discount)
                updateSummary(data.new_total_formatted, '0.00'); 
                alert(data.message);
                // Reload the page to correctly clear the coupon session and input value
                window.location.reload(); 
            }
        })
        .catch(error => {
            console.error('Error applying coupon:', error);
            alert('An error occurred while applying the coupon.');
        });
    }
</script>

<script>lucide.createIcons();</script>
<?php include "footer.php"; ?>
</body>
</html>