<?php
// CRITICAL: session_start() must be the very first thing in the file.
session_start(); 
include "connection.php";

// Define the fixed packaging cost for server-side and client-side use
const PACKAGING_COST = 99.00;

// --- Handle Add to Cart Submission with Server-Side Validation and Recalculation ---
if (isset($_POST['add_to_cart']) && isset($_POST['cart_data'])) {
    $cartData = json_decode($_POST['cart_data'], true);
    
    // Check if the input is valid
    if (!isset($cartData['spices']) || !is_array($cartData['spices']) || empty($cartData['spices'])) {
        // Redirect back with an error or exit gracefully
        header('Location: customize_giftbox.php?status=error&message=no_spices_selected');
        exit();
    }
    
    $recalculated_subtotal = 0.00;
    $validated_spices = [];
    $spice_ids = [];

    // 1. Prepare list of spice IDs and quantities from client data
    foreach (array_keys($cartData['spices']) as $key) {
        list($spice_id, $quantity_label) = explode('-', $key);
        $spice_ids[] = (int)$spice_id;
    }
    
    // 2. Fetch all selected spice details from the database
    $safe_ids = implode(',', array_unique($spice_ids));
    // Check for an empty string to prevent invalid SQL
    if (empty($safe_ids)) {
        header('Location: customize_giftbox.php?status=error&message=invalid_spice_data');
        exit();
    }
    
    // Use prepared statements or mysqli_real_escape_string for true security, but for demonstration:
    $validation_query = "SELECT product_id, product_name, discounted_price, product_price, product_image FROM product WHERE product_id IN ($safe_ids) AND category = 'Custom'";
    $validation_result = mysqli_query($conn, $validation_query);

    $db_spices = [];
    while($row = mysqli_fetch_assoc($validation_result)) {
        $db_spices[$row['product_id']] = $row;
    }
    
    // 3. Iterate over the client-sent spices, validate prices, and recalculate total
    foreach ($cartData['spices'] as $key => $client_spice) {
        list($spice_id, $quantity_label) = explode('-', $key);
        $spice_id = (int)$spice_id;

        if (isset($db_spices[$spice_id])) {
            $db_spice = $db_spices[$spice_id];
            
            // Determine the price per gram (discounted or original)
            $price_per_gram = !empty($db_spice['discounted_price']) ? $db_spice['discounted_price'] : $db_spice['product_price'];
            
            $weight_grams = 0;
            if ($quantity_label === '100g') {
                $weight_grams = 100;
            } elseif ($quantity_label === '250g') {
                $weight_grams = 250;
            } else {
                // Invalid quantity, skip this item
                continue; 
            }
            
            // Server-side price calculation (guaranteed correct)
            $actual_price = $price_per_gram * $weight_grams;
            
            // Add the validated item to the cart entry
            $validated_spices[$key] = [
                'id' => $spice_id,
                'name' => $db_spice['product_name'],
                'quantity' => $quantity_label,
                'price' => (float)number_format($actual_price, 2, '.', ''), // Format to 2 decimal places as float
                'image' => 'uploads/' . $db_spice['product_image'] 
            ];

            $recalculated_subtotal += $actual_price;
        }
    }

    // Final calculations based ONLY on server-validated data
    $recalculated_subtotal = (float)number_format($recalculated_subtotal, 2, '.', '');
    $recalculated_total = $recalculated_subtotal + PACKAGING_COST;

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Add the validated custom gift box to cart
    $_SESSION['cart'][] = [
        'type' => 'custom_gift_box',
        'spices' => $validated_spices,
        'subtotal' => $recalculated_subtotal,
        'packaging_cost' => PACKAGING_COST,
        'total' => $recalculated_total,
        'gift_message' => htmlspecialchars(trim($cartData['giftMessage'] ?? '')),
        'added_at' => time()
    ];
    
    // Redirect to shopping cart
    header('Location: shoppingcart.php');
    exit();
}

// --- 1. Fetch Custom Spices from the Database (for rendering the selection page) ---
$query = "SELECT * FROM product WHERE category = 'Custom'";
$result = mysqli_query($conn, $query);

$spices = [];
while($row = mysqli_fetch_assoc($result)) {
    $product_id = $row['product_id'];

    // --- 2. Fetch Extra Images (if product_images table exists) ---
    $extra_images_sql = "SELECT image_name FROM product_images WHERE product_id = {$product_id}";
    $extra_images_result = mysqli_query($conn, $extra_images_sql);
    
    $extra_images = [];
    if ($extra_images_result) {
        while ($img_row = mysqli_fetch_assoc($extra_images_result)) {
            $extra_images[] = 'uploads/' . $img_row['image_name'];
        }
    }

    // --- 3. Price Calculation (Assuming product_price is stored PER GRAM) ---
    $price_per_gram = !empty($row['discounted_price']) ? $row['discounted_price'] : $row['product_price'];
    $original_price_per_gram = $row['product_price'];

    // Calculate 100g and 250g display prices
    // Prices are formatted here for DISPLAY, but raw price_per_gram is used for final server-side calc
    $price_100g = $price_per_gram * 100;
    $price_250g = $price_per_gram * 250;
    $original_price_100g = $original_price_per_gram * 100; 

    $spices[] = [
        'id' => $row['product_id'],
        'name' => $row['product_name'],
        'description' => $row['product_description'],
        'image' => 'uploads/' . $row['product_image'],
        'extra_images' => $extra_images,
        // Crucially, pass the raw prices to JS to use in the `selectedSpices` object
        'price_100g' => (float)$price_100g, 
        'price_250g' => (float)$price_250g, 
        'original_price_100g' => (float)$original_price_100g,
        'category' => $row['category'],
        'stock_status' => $row['stock_status']
    ];
}

$total_spices = count($spices);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customize Gift Box - Aroma Hub | Premium Spices & Seasonings</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316',
                        secondary: '#dc2626',
                        accent: '#fed7aa',
                    }
                }
            }
        }
    </script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .spice-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(249, 250, 251, 0.9));
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .spice-card.selected {
            border-color: #f97316;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(234, 88, 12, 0.05));
        }
        
        .spice-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15);
        }
        
        .quantity-btn {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            transition: all 0.3s ease;
        }
        
        .quantity-btn.selected {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f97316, #ea580c);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #ea580c, #dc2626);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(249, 115, 22, 0.4);
        }
        
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .gift-box-preview {
            background: linear-gradient(135deg, #fff7ed, #ffedd5);
            border: 2px dashed #f97316;
        }
        
        .floating-spice {
            animation: float 2s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.4s ease;
            z-index: 1000;
        }
        
        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }
    </style>
</head>
<body class="bg-gray-50">
    <section class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="index.php" class="text-gray-500 hover:text-primary transition-colors">Home</a></li>
                    <li class="flex items-center">
                        <i data-lucide="chevron-right" class="h-4 w-4 text-gray-400 mx-2"></i>
                        <a href="giftbox.php" class="text-gray-500 hover:text-primary transition-colors">Gift Boxes</a>
                    </li>
                    <li class="flex items-center">
                        <i data-lucide="chevron-right" class="h-4 w-4 text-gray-400 mx-2"></i>
                        <span class="text-primary font-medium">Customize</span>
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center font-medium">
                            1
                        </div>
                        <span class="font-medium text-primary">Select Spices</span>
                    </div>
                    <div class="w-16 h-0.5 bg-gray-300"></div>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-medium">
                            2
                        </div>
                        <span class="text-gray-600">Review & Checkout</span>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    <span id="selected-count">0</span> spices selected
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Choose Your Spices</h2>
                    <p class="text-gray-600">Select from <?= $total_spices; ?> premium spices and choose quantities (100g or 250g) for your custom gift box.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="spices-container">
                    <?php foreach($spices as $spice): ?>
                        <div class="spice-card rounded-2xl p-4" data-spice-id="<?= $spice['id']; ?>">
                            <div class="relative mb-4">
                                <img src="<?= $spice['image']; ?>" alt="<?= htmlspecialchars($spice['name']); ?>" 
                                    class="w-full h-40 object-cover rounded-xl"
                                    onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1">
                                    <span class="text-xs font-medium text-gray-700"><?= ucfirst($spice['category']); ?></span>
                                </div>
                            </div>
                            
                            <h3 class="font-bold text-lg mb-2"><?= htmlspecialchars($spice['name']); ?></h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2"><?= htmlspecialchars($spice['description']); ?></p>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">100g</span>
                                    <div class="flex items-center space-x-2">
                                        <?php if($spice['price_100g'] < $spice['original_price_100g']): ?>
                                            <span class="text-xs text-gray-500 line-through">₹<?= number_format($spice['original_price_100g'], 2); ?></span>
                                        <?php endif; ?>
                                        <span class="font-bold text-primary">₹<?= number_format($spice['price_100g'], 2); ?></span>
                                    </div>
                                    <button onclick="toggleSpice(<?= $spice['id']; ?>, '100g', <?= $spice['price_100g']; ?>, '<?= addslashes($spice['name']); ?>', '<?= $spice['image']; ?>')" 
                                            class="quantity-btn px-4 py-2 rounded-lg font-medium text-sm" 
                                            id="btn-<?= $spice['id']; ?>-100g">
                                        Add
                                    </button>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">250g</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-primary">₹<?= number_format($spice['price_250g'], 2); ?></span>
                                    </div>
                                    <button onclick="toggleSpice(<?= $spice['id']; ?>, '250g', <?= $spice['price_250g']; ?>, '<?= addslashes($spice['name']); ?>', '<?= $spice['image']; ?>')" 
                                            class="quantity-btn px-4 py-2 rounded-lg font-medium text-sm" 
                                            id="btn-<?= $spice['id']; ?>-250g">
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if($total_spices == 0): ?>
                    <div class="text-center py-16">
                        <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-seedling text-orange-600 text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">No Spices Available</h3>
                        <p class="text-gray-600 mb-8 max-w-md mx-auto">
                            We're currently updating our spice inventory. Please check back soon.
                        </p>
                        <a href="giftbox.php" class="btn-primary text-white px-8 py-3 rounded-xl font-medium inline-flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Gift Boxes
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    <div class="gift-box-preview rounded-2xl p-6">
                        <div class="text-center mb-6">
                            <div class="floating-spice w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-gift text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Your Custom Gift Box</h3>
                            <p class="text-gray-600 text-sm">Preview of selected spices</p>
                        </div>

                        <div class="space-y-3 mb-6" id="selected-spices">
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-plus-circle text-3xl mb-3"></i>
                                <p>Start selecting spices to see them here</p>
                            </div>
                        </div>

                        <div class="border-t border-orange-200 pt-4">
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span>Subtotal (Spices):</span>
                                    <span id="subtotal">₹0.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>Gift Packaging:</span>
                                    <span>₹<?= number_format(PACKAGING_COST, 2); ?></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>Shipping:</span>
                                    <span class="text-green-600">Free</span>
                                </div>
                            </div>
                            <div class="border-t border-orange-200 pt-2">
                                <div class="flex justify-between font-bold text-lg">
                                    <span>Total:</span>
                                    <span class="text-primary" id="total">₹<?= number_format(PACKAGING_COST, 2); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 mt-6">
                            <button 
                                onclick="addToCart()" 
                                id="add-to-cart-btn"
                                disabled
                                class="w-full flex items-center justify-center btn-primary text-white py-3 rounded-xl font-medium">
                                <i class="fas fa-cart-plus mr-2"></i>
                                Add to Cart
                            </button>

                            <button 
                                onclick="checkout()" 
                                id="checkout-btn"
                                disabled
                                class="w-full flex items-center justify-center bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-medium transition-colors">
                                <i class="fas fa-credit-card mr-2"></i>
                                Direct Checkout
                            </button>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Gift Message (Optional)
                            </label>
                            <textarea 
                                id="gift-message"
                                rows="3" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                                placeholder="Write a personalized message for your gift..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="cart-form" method="POST" action="" style="display: none;">
        <input type="hidden" name="add_to_cart" value="1">
        <input type="hidden" name="cart_data" id="cart-data-input">
    </form>

    <div id="toast" class="toast"></div>

    <script>
        // Use PHP constant in JavaScript
        const PACKAGING_COST = <?= PACKAGING_COST; ?>;

        let selectedSpices = {};
        let totalPrice = PACKAGING_COST; // Initialize with packaging cost

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            updateSummary();
        });

        // Toggle spice selection
        function toggleSpice(spiceId, quantity, price, name, image) {
            // Key format: "ID-QUANTITY" (e.g., "3-100g")
            const key = `${spiceId}-${quantity}`;
            const btn = document.getElementById(`btn-${spiceId}-${quantity}`);
            const otherQuantity = quantity === '100g' ? '250g' : '100g';
            const otherBtn = document.getElementById(`btn-${spiceId}-${otherQuantity}`);
            const spiceCard = document.querySelector(`[data-spice-id="${spiceId}"]`);

            if (selectedSpices[key]) {
                // Remove spice
                delete selectedSpices[key];
                btn.textContent = 'Add';
                btn.classList.remove('selected');
                showToast(`${name} (${quantity}) removed from gift box`);
            } else {
                // Check if the other quantity is selected for the same spice
                const otherKey = `${spiceId}-${otherQuantity}`;
                if (selectedSpices[otherKey]) {
                    // Remove the other quantity first
                    delete selectedSpices[otherKey];
                    otherBtn.textContent = 'Add';
                    otherBtn.classList.remove('selected');
                }
                
                // Add new spice. NOTE: The price here is for display only.
                // The server will re-calculate the final price for security.
                selectedSpices[key] = {
                    id: spiceId,
                    name: name,
                    quantity: quantity,
                    price: parseFloat(price), // Use the passed price for JS calculation
                    image: image
                };
                btn.textContent = 'Added';
                btn.classList.add('selected');
                showToast(`${name} (${quantity}) added to gift box`);
            }
            
            // Update the card selected state
            const isSelected = Object.keys(selectedSpices).some(k => k.startsWith(spiceId + '-'));
            if (isSelected) {
                spiceCard.classList.add('selected');
            } else {
                spiceCard.classList.remove('selected');
            }

            updateSummary();
            updateSelectedSpicesDisplay();
        }

        // Update price summary (Client-side estimate only)
        function updateSummary() {
            const subtotal = Object.values(selectedSpices).reduce((sum, spice) => sum + spice.price, 0);
            const total = subtotal + PACKAGING_COST; 

            document.getElementById('subtotal').textContent = `₹${subtotal.toFixed(2)}`;
            document.getElementById('total').textContent = `₹${total.toFixed(2)}`;
            document.getElementById('selected-count').textContent = Object.keys(selectedSpices).length;

            // Enable/disable buttons
            const hasSpices = Object.keys(selectedSpices).length > 0;
            const cartBtn = document.getElementById('add-to-cart-btn');
            const checkoutBtn = document.getElementById('checkout-btn');
            
            if (cartBtn && checkoutBtn) {
                cartBtn.disabled = !hasSpices;
                checkoutBtn.disabled = !hasSpices;
            }

            totalPrice = total;
        }

        // Update selected spices display
        function updateSelectedSpicesDisplay() {
            const container = document.getElementById('selected-spices');
            
            if (Object.keys(selectedSpices).length === 0) {
                container.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-plus-circle text-3xl mb-3"></i>
                        <p>Start selecting spices to see them here</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = Object.values(selectedSpices).map(spice => `
                <div class="flex items-center space-x-3 bg-white rounded-lg p-3 shadow-sm">
                    <img src="${spice.image}" alt="${spice.name}" class="w-12 h-12 object-cover rounded-lg">
                    <div class="flex-1">
                        <h4 class="font-medium text-sm">${spice.name}</h4>
                        <p class="text-xs text-gray-500">${spice.quantity} - ₹${spice.price.toFixed(2)}</p>
                    </div>
                    <button onclick="toggleSpice(${spice.id}, '${spice.quantity}', ${spice.price}, '${spice.name.replace(/'/g, "\\'")}', '${spice.image}')" 
                            class="text-red-500 hover:text-red-700 text-lg">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');
        }

        // Add to cart function: Prepares JSON data for server-side processing
        function addToCart() {
            if (Object.keys(selectedSpices).length === 0) {
                showToast('Please select at least one spice', 'error');
                return;
            }

            const giftMessage = document.getElementById('gift-message').value;
            
            // Prepare the data structure expected by the PHP handler
            const cartData = {
                spices: selectedSpices, // Send the selected spices dictionary (keys are important)
                giftMessage: giftMessage
                // NOTE: We DO NOT send subtotal/total. PHP calculates it.
            };

            // Set the cart data in the hidden form field
            document.getElementById('cart-data-input').value = JSON.stringify(cartData);
            
            showToast('Adding custom gift box to cart...', 'info');
            
            // Submit the form
            setTimeout(() => {
                document.getElementById('cart-form').submit();
            }, 500);
        }

        // Direct checkout function (Client-side redirect placeholder)
        function checkout() {
            // For a real application, you would implement an AJAX checkout flow
            // or redirect to a page that first POSTs the data to the server
            // to ensure price validation BEFORE moving to payment.

            showToast('Direct checkout functionality needs server integration...', 'error');
            
            /*
            // Example of redirecting with data stored locally (less secure)
            if (Object.keys(selectedSpices).length === 0) {
                showToast('Please select at least one spice', 'error');
                return;
            }

            const giftMessage = document.getElementById('gift-message').value;
            const subtotal = Object.values(selectedSpices).reduce((sum, spice) => sum + spice.price, 0);
            
            const checkoutData = {
                spices: selectedSpices,
                subtotal: subtotal,
                packaging_cost: PACKAGING_COST,
                total: totalPrice,
                giftMessage: giftMessage,
                type: 'custom_gift_box'
            };

            localStorage.setItem('checkout_data', JSON.stringify(checkoutData));
            
            showToast('Redirecting to checkout...');
            
            setTimeout(() => {
                window.location.href = 'checkout.php';
            }, 1000);
            */
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'info' ? 'fa-info-circle' : 'fa-exclamation-triangle'} text-lg"></i>
                    <span class="font-medium">${message}</span>
                </div>
            `;
            toast.style.background = type === 'success' 
                ? 'linear-gradient(135deg, #10b981, #059669)' 
                : type === 'info'
                ? 'linear-gradient(135deg, #3b82f6, #2563eb)'
                : 'linear-gradient(135deg, #ef4444, #dc2626)';
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3500);
        }
    </script>
</body>
</html>