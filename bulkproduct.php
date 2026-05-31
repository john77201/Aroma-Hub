<?php
// session_start(); // Assuming this is handled in header.php or connection.php, or will be uncommented later
include "connection.php";
include "header.php";

// Define bulk product constraints
const BULK_MIN_QTY = 5;
const BULK_MAX_QTY = 25;

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>
        alert('Product ID is missing');
        window.location='index.php';
      </script>";
    exit;
}

// 1. Fetch Product: Use prepared statements for security
$stmt = $conn->prepare("SELECT * FROM product WHERE product_id=?");
// Assuming product_id is an integer (i) or string (s). Using 's' for maximum compatibility with URL parameters like UUIDs if used, though 'i' is likely correct for a standard ID. Sticking to 's' as in your singleproduct.php example for product_id.
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    echo "<script>
        alert('Product not found');
        window.location='index.php';
      </script>";
    exit;
}

// 2. Fetch extra images: Use prepared statements
$extraImages = [];
$imgStmt = $conn->prepare("SELECT image_name FROM product_images WHERE product_id=?");
$imgStmt->bind_param("s", $id);
$imgStmt->execute();
$imgResult = $imgStmt->get_result();
while ($imgRow = $imgResult->fetch_assoc()) {
    $extraImages[] = $imgRow['image_name'];
}
$imgStmt->close();

// 3. Handle Add to Cart / Buy Now
if(isset($_POST['save']) || isset($_POST['buynow'])) {
    // Check if session has started and user is logged in
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if(isset($_SESSION['userid'])) {
        $pid = $id;
        $userid = $_SESSION['userid'];
        $qty = (int)$_POST['qty']; // Cast to integer for safety
        $rate = (float)$_POST['rate']; // Cast to float/double
        $total = $qty * $rate;
        $status = 0;

        // Validation for bulk quantity
        if ($qty < BULK_MIN_QTY || $qty > BULK_MAX_QTY) {
            echo "<script>
                alert('Invalid quantity for bulk product. Minimum is " . BULK_MIN_QTY . " and maximum is " . BULK_MAX_QTY . ".');
                window.location='bulkproduct.php?id=" . $id . "';
            </script>";
            exit;
        }

        // Use prepared statements for security
        $cartStmt = $conn->prepare("INSERT INTO cart(product_id, userid, quantity, rate, total, status) VALUES(?, ?, ?, ?, ?, ?)");
        // 's' for product_id, 'i' for userid, 'i' for quantity, 'd' for rate, 'd' for total, 'i' for status
        $cartStmt->bind_param("siiddi", $pid, $userid, $qty, $rate, $total, $status);
        $cartStmt->execute();
        $cartStmt->close();

        if (isset($_POST['save'])) {
            echo "<script>
                alert('Product added to cart successfully!');
                window.location='products.php';
              </script>";
        } elseif (isset($_POST['buynow'])) {
            echo "<script>
                alert('Product added to cart successfully!');
                window.location='checkout.php';
              </script>";
        }
    } else {
        echo "<script>
            alert('Please login to continue');
            window.location='login.php';
          </script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_name']); ?> - Bulk Order</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        /* Custom styles for the orange theme */
        .btn-primary {
            background-color: #f97316;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #ea580c;
        }
        .btn-secondary {
            background-color: #1f2937;
            transition: background-color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #111827;
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        .toast.show {
            transform: translateX(0);
        }
        .thumbnail-selected {
            border-color: #f97316 !important;
            border-width: 2px;
        }
        .thumbnail-hover:hover {
            border-color: #d1d5db;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <div id="toast" class="toast">
        <span id="toast-message"></span>
    </div>

    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <button onclick="goBack()" class="flex items-center space-x-2 text-gray-600 hover:text-orange-600 transition-colors">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                <span>Back to Products</span>
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
                <div class="space-y-4">
                    <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                        <img 
                            id="main-image"
                            src="uploads/<?= htmlspecialchars($product['product_image']); ?>"
                            alt="<?= htmlspecialchars($product['product_name']); ?>"
                            class="w-full h-full object-cover"
                        />
                    </div>

                    <div class="flex space-x-4">
                        <button 
                            onclick="selectImage(0)"
                            class="thumbnail w-20 h-20 rounded-lg overflow-hidden border-2 thumbnail-selected transition-colors"
                            data-index="0"
                        >
                            <img 
                                src="uploads/<?= htmlspecialchars($product['product_image']); ?>"
                                alt="<?= htmlspecialchars($product['product_name']); ?>"
                                class="w-full h-full object-cover"
                            />
                        </button>
                        <?php foreach ($extraImages as $index => $img) { ?>
                            <button 
                                onclick="selectImage(<?= $index + 1 ?>)"
                                class="thumbnail w-20 h-20 rounded-lg overflow-hidden border-2 border-gray-200 thumbnail-hover transition-colors"
                                data-index="<?= $index + 1 ?>"
                            >
                                <img 
                                    src="uploads/<?= htmlspecialchars($img); ?>"
                                    alt="<?= htmlspecialchars($product['product_name']); ?> <?= $index + 1 ?>"
                                    class="w-full h-full object-cover"
                                />
                            </button>
                        <?php } ?>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <?php if ($product['stock_status'] !== 'Out Of Stock') { ?>
                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">In Stock</span>
                            <?php } else { ?>
                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">Out of Stock</span>
                            <?php } ?>
                            <?php if ($product['quality']) { ?>
                                <span class="bg-emerald-500 text-white px-3 py-1 rounded-full text-sm font-medium"><?= htmlspecialchars($product['quality']); ?></span>
                            <?php } ?>
                            <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">Bulk Only (Min: <?= BULK_MIN_QTY ?> Kg)</span>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($product['product_name']); ?></h1>
                        <p class="text-gray-600"><?= nl2br(htmlspecialchars($product['product_description'])); ?></p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <span class="text-4xl font-bold text-orange-600">
                            ₹<?= number_format($product['discounted_price'] > 0 ? $product['discounted_price'] : $product['product_price'], 2); ?>/Kg
                        </span>
                        <?php if (!empty($product['discounted_price']) && $product['discounted_price'] > 0) { ?>
                            <span class="text-xl text-gray-500 line-through">
                                ₹<?= number_format($product['product_price'], 2); ?>/Kg
                            </span>
                            <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-medium">
                                <?= round((($product['product_price'] - $product['discounted_price']) / $product['product_price']) * 100); ?>% OFF
                            </span>
                        <?php } ?>
                    </div>
                    
                    <form method="post" action="">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <span class="text-lg font-medium text-gray-900">Quantity (in Kg):</span>
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button 
                                        type="button"
                                        onclick="changeQuantity(-1)"
                                        class="px-3 py-2 hover:bg-gray-100 transition-colors"
                                        id="decrease-btn"
                                    >
                                        <i data-lucide="minus" class="h-4 w-4"></i>
                                    </button>
                                    <input type="number" name="qty" id="qty" value="<?= BULK_MIN_QTY ?>" min="<?= BULK_MIN_QTY ?>" max="<?= BULK_MAX_QTY ?>" class="w-16 text-center px-2 py-2 text-lg font-medium border-x border-gray-300 focus:outline-none" readonly>
                                    <input type="hidden" name="rate" value="<?= $product['discounted_price'] > 0 ? $product['discounted_price'] : $product['product_price']; ?>">
                                    <button 
                                        type="button"
                                        onclick="changeQuantity(1)"
                                        class="px-3 py-2 hover:bg-gray-100 transition-colors"
                                    >
                                        <i data-lucide="plus" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <?php if ($product['stock_status'] == 'Out Of Stock') { ?>
                                    <button type="button" class="flex-1 btn-secondary text-white py-3 px-6 rounded-lg font-medium opacity-50 cursor-not-allowed" disabled>
                                        Out of Stock
                                    </button>
                                <?php } else { ?>
                                    <button 
                                        type="submit" 
                                        name="save" 
                                        class="flex-1 btn-primary text-white py-3 px-6 rounded-lg font-medium flex items-center justify-center"
                                    >
                                        <i data-lucide="shopping-cart" class="h-5 w-5 mr-2"></i>
                                        Add to Cart
                                    </button>
                                    <button 
                                        type="submit" 
                                        name="buynow" 
                                        class="flex-1 btn-secondary text-white py-3 px-6 rounded-lg font-medium"
                                    >
                                        Buy Now
                                    </button>
                                <?php } ?>
                                <button 
                                    type="button"
                                    onclick="toggleWishlist()"
                                    class="px-4 py-3 rounded-lg border-2 border-gray-300 hover:border-gray-400 transition-colors"
                                    id="wishlist-btn"
                                >
                                    <i data-lucide="heart" class="h-5 w-5 text-gray-600" id="heart-icon"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery & Returns</h3>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="truck" class="h-5 w-5 text-green-600"></i>
                                <div>
                                    <span class="font-medium text-gray-900">Bulk Shipping</span>
                                    <p class="text-sm text-gray-600">Special logistics for bulk orders, 3-5 business days</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i data-lucide="rotate-ccw" class="h-5 w-5 text-blue-600"></i>
                                <div>
                                    <span class="font-medium text-gray-900">Bulk Order Return Policy</span>
                                    <p class="text-sm text-gray-600">Contact support for bulk returns/exchanges</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i data-lucide="shield" class="h-5 w-5 text-purple-600"></i>
                                <div>
                                    <span class="font-medium text-gray-900">Quality Guarantee</span>
                                    <p class="text-sm text-gray-600">100% authentic spices with quality assurance for large volumes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // PHP Constants for JS
        const BULK_MIN_QTY = <?= BULK_MIN_QTY ?>;
        const BULK_MAX_QTY = <?= BULK_MAX_QTY ?>;

        // Product data
        const productImages = [
            "uploads/<?= htmlspecialchars($product['product_image']); ?>",
            <?php foreach ($extraImages as $img) { ?>
                "uploads/<?= htmlspecialchars($img); ?>",
            <?php } ?>
        ];

        // State variables
        let currentQuantity = BULK_MIN_QTY; // Start at min bulk quantity
        let selectedImageIndex = 0;
        let isWishlisted = false;

        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            updateQuantityDisplay();
        });

        function updateQuantityDisplay() {
            const qtyInput = document.getElementById('qty');
            if (qtyInput) {
                qtyInput.value = currentQuantity;
            }
            const decreaseBtn = document.getElementById('decrease-btn');
            if (decreaseBtn) {
                // Disable decrease button if at minimum quantity
                decreaseBtn.disabled = currentQuantity <= BULK_MIN_QTY;
                if (currentQuantity <= BULK_MIN_QTY) {
                    decreaseBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    decreaseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
            // Add similar logic for the increase button if max is reached
        }

        // Image selection function
        function selectImage(index) {
            selectedImageIndex = index;
            const mainImage = document.getElementById('main-image');
            if (mainImage) {
                mainImage.src = productImages[index];
            }
            
            const thumbnails = document.querySelectorAll('.thumbnail');
            thumbnails.forEach((thumb, i) => {
                if (i === index) {
                    thumb.classList.add('thumbnail-selected');
                    thumb.classList.remove('border-gray-200', 'thumbnail-hover');
                } else {
                    thumb.classList.remove('thumbnail-selected');
                    thumb.classList.add('border-gray-200', 'thumbnail-hover');
                }
            });
        }

        // Quantity management (Bulk: Min 5, Max 25)
        function changeQuantity(delta) {
            const newQuantity = currentQuantity + delta;
            if (newQuantity >= BULK_MIN_QTY && newQuantity <= BULK_MAX_QTY) {
                currentQuantity = newQuantity;
                updateQuantityDisplay();
            } else if (newQuantity < BULK_MIN_QTY) {
                showToast(`Minimum bulk quantity is ${BULK_MIN_QTY} Kg.`, 'info');
            } else if (newQuantity > BULK_MAX_QTY) {
                showToast(`Maximum bulk quantity is ${BULK_MAX_QTY} Kg.`, 'info');
            }
        }

        // Wishlist functionality (simplified for front-end demonstration)
        function toggleWishlist() {
            isWishlisted = !isWishlisted;
            const heartIcon = document.getElementById('heart-icon');
            
            if (isWishlisted) {
                heartIcon.classList.add('text-red-500');
                heartIcon.style.fill = 'currentColor';
                showToast('<?= htmlspecialchars($product['product_name']); ?> added to wishlist', 'success');
            } else {
                heartIcon.classList.remove('text-red-500');
                heartIcon.style.fill = 'none';
                showToast('<?= htmlspecialchars($product['product_name']); ?> removed from wishlist', 'info');
            }
            // NOTE: You'll need to implement the AJAX/PHP backend for actually saving the wishlist item.
        }

        // Toast notification system
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            
            if (!toast || !toastMessage) return;

            toastMessage.textContent = message;
            
            // Reset colors
            toast.style.backgroundColor = ''; 
            
            if (type === 'success') {
                toast.style.backgroundColor = '#10b981';
            } else if (type === 'info') {
                toast.style.backgroundColor = '#3b82f6';
            } else if (type === 'error') {
                toast.style.backgroundColor = '#ef4444';
            }
            
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Navigation
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
<?php
include 'footer.php';
?>