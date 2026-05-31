<?php
//session_start(); // Assuming this is uncommented in your actual file
include "connection.php";
include "header.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>
            alert('Product ID is missing');
            window.location='index.php';
          </script>";
    exit;
}

// Prepare and execute statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM product WHERE product_id=?");
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

// --- NEW/UPDATED LOGIC ---
// Determine max quantity allowed (based on actual stock)
$max_stock = (int)$product['product_quantity'];
$is_out_of_stock = $max_stock <= 0;
$stock_display_text = $is_out_of_stock ? 'Out of Stock' : "In Stock ({$max_stock})";
$stock_class = $is_out_of_stock ? 'bg-red-500' : 'bg-green-500';
// --- END NEW/UPDATED LOGIC ---


// Fetch extra images
$extraImages = [];
$imgStmt = $conn->prepare("SELECT image_name FROM product_images WHERE product_id=?");
$imgStmt->bind_param("s", $id);
$imgStmt->execute();
$imgResult = $imgStmt->get_result();
while ($imgRow = $imgResult->fetch_assoc()) {
    $extraImages[] = $imgRow['image_name'];
}
$imgStmt->close();

if(isset($_POST['save']) || isset($_POST['buynow'])) {
    if(isset($_SESSION['userid'])) {
        $pid = $id;
        $userid = $_SESSION['userid'];
        $qty = (int)$_POST['qty']; // Ensure quantity is an integer
        $rate = $_POST['rate'];
        $total = $qty * $rate;
        $status = 0;

        // --- Server-side stock check for submission ---
        if ($qty > $max_stock) {
             echo "<script>
                        alert('Error: Requested quantity ($qty) exceeds available stock ({$max_stock}).');
                        window.location='singleproduct.php?id={$id}';
                       </script>";
            exit;
        }
        // --- End Server-side stock check ---

        // Use prepared statements for security
        // Assuming product_id is INT for 'i' in bind_param, if VARCHAR, use 's'
        $cartStmt = $conn->prepare("INSERT INTO cart(product_id, userid, quantity, rate, total, status) VALUES(?, ?, ?, ?, ?, ?)");
        $cartStmt->bind_param("iiisdi", $pid, $userid, $qty, $rate, $total, $status);
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

// --- NEW REVIEW LOGIC - FUNCTION TO DISPLAY STARS ---
/**
 * Generates HTML for star ratings (5 stars max).
 * @param int $rating The rating score (1 to 5).
 * @return string HTML for the stars.
 */
function display_rating_stars($rating) {
    $html = '';
    $max_stars = 5;
    for ($i = 1; $i <= $max_stars; $i++) {
        if ($i <= $rating) {
            // Filled Star
            $html .= '<i data-lucide="star" class="h-4 w-4 star-filled inline-block"></i>';
        } else {
            // Empty Star
            $html .= '<i data-lucide="star" class="h-4 w-4 star-empty inline-block"></i>';
        }
    }
    return $html;
}

// --- NEW REVIEW LOGIC - FETCH REVIEWS ---
$reviews_data = [];
$avg_rating = 0;
$total_reviews = 0;

$review_sql = "SELECT r.*, u.username 
               FROM reviews r 
               INNER JOIN user u ON r.user_id = u.userid 
               WHERE r.product_id = ? 
               ORDER BY r.review_date DESC";
$review_stmt = $conn->prepare($review_sql);
$review_stmt->bind_param("i", $id);
$review_stmt->execute();
$reviews_result = $review_stmt->get_result();

if (mysqli_num_rows($reviews_result) > 0) {
    $total_rating = 0;
    while($row = $reviews_result->fetch_assoc()) {
        $reviews_data[] = $row;
        $total_rating += $row['rating'];
    }
    $total_reviews = count($reviews_data);
    $avg_rating = round($total_rating / $total_reviews, 1);
}
$review_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_name']); ?> - Aroma Hub</title>
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
        .star-filled {
            color: #fbbf24;
            fill: currentColor;
        }
        .star-empty {
            color: #d1d5db;
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
                            <span class="<?= $stock_class; ?> text-white px-3 py-1 rounded-full text-sm font-medium">
                                <?= $stock_display_text; ?>
                            </span>
                            <?php if ($product['quality']) { ?>
                                <span class="bg-emerald-500 text-white px-3 py-1 rounded-full text-sm font-medium"><?= htmlspecialchars($product['quality']); ?></span>
                            <?php } ?>
                            <?php if ($total_reviews > 0): ?>
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
                                    <i data-lucide="star" class="h-4 w-4 mr-1 star-filled"></i>
                                    <?= $avg_rating; ?> (<?= $total_reviews; ?> reviews)
                                </span>
                            <?php endif; ?>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($product['product_name']); ?></h1>
                        <p class="text-gray-600"><?= nl2br(htmlspecialchars($product['product_description'])); ?></p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <span class="text-4xl font-bold text-orange-600">
                            <?php $final_price = $product['discounted_price'] > 0 ? $product['discounted_price'] : $product['product_price']; ?>
                            ₹<?= number_format($final_price, 2); ?>
                        </span>
                        <?php if (!empty($product['discounted_price']) && $product['discounted_price'] > 0) { ?>
                            <span class="text-xl text-gray-500 line-through">
                                ₹<?= number_format($product['product_price'], 2); ?>
                            </span>
                            <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-medium">
                                <?= round((($product['product_price'] - $product['discounted_price']) / $product['product_price']) * 100); ?>% OFF
                            </span>
                        <?php } ?>
                    </div>
                    
                    <form method="post" action="">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <span class="text-lg font-medium text-gray-900">Quantity:</span>
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button 
                                        type="button"
                                        onclick="changeQuantity(-1)"
                                        class="px-3 py-2 hover:bg-gray-100 transition-colors"
                                        id="decrease-btn"
                                    >
                                        <i data-lucide="minus" class="h-4 w-4"></i>
                                    </button>
                                    <input 
                                        type="number" 
                                        name="qty" 
                                        id="qty" 
                                        value="1" 
                                        min="1" 
                                        max="<?= $max_stock; ?>" 
                                        class="w-16 text-center px-2 py-2 text-lg font-medium border-x border-gray-300 focus:outline-none" 
                                        readonly
                                    >
                                    <input type="hidden" name="rate" value="<?= $final_price; ?>">
                                    <button 
                                        type="button"
                                        onclick="changeQuantity(1)"
                                        class="px-3 py-2 hover:bg-gray-100 transition-colors"
                                        id="increase-btn"
                                        <?= $is_out_of_stock ? 'disabled' : ''; ?>
                                    >
                                        <i data-lucide="plus" class="h-4 w-4"></i>
                                    </button>
                                </div>
                                <?php if (!$is_out_of_stock): ?>
                                    <p class="text-sm text-gray-500">Max: <?= $max_stock; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex space-x-4">
                                <?php if ($is_out_of_stock) { ?>
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
                                    onclick="window.location='wishlist.php?product_id=<?= $product['product_id']; ?>'"
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
                                    <span class="font-medium text-gray-900">Free Shipping</span>
                                    <p class="text-sm text-gray-600">Delivered in 2-3 business days</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i data-lucide="rotate-ccw" class="h-5 w-5 text-blue-600"></i>
                                <div>
                                    <span class="font-medium text-gray-900">Easy Returns</span>
                                    <p class="text-sm text-gray-600">30-day return policy</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i data-lucide="shield" class="h-5 w-5 text-purple-600"></i>
                                <div>
                                    <span class="font-medium text-gray-900">Quality Guarantee</span>
                                    <p class="text-sm text-gray-600">100% authentic spices with quality assurance</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-3">Customer Reviews (<?= $total_reviews; ?>)</h2>

            <?php if ($total_reviews > 0): ?>
                <div class="flex items-center space-x-4 mb-8 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="text-6xl font-bold text-yellow-700"><?= $avg_rating; ?></div>
                    <div>
                        <div class="flex items-center mb-1">
                            <?= display_rating_stars($avg_rating); ?>
                        </div>
                        <p class="text-gray-700 font-medium">Out of 5 Stars (<?= $total_reviews; ?> ratings)</p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <?php foreach ($reviews_data as $review): ?>
                        <div class="border-b pb-6 last:border-b-0">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-700 font-semibold">
                                        <?= strtoupper(substr(htmlspecialchars($review['username']), 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($review['username']); ?></p>
                                        <p class="text-sm text-gray-500">
                                            Reviewed on <?= date('F j, Y', strtotime($review['review_date'])); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <?= display_rating_stars((int)$review['rating']); ?>
                            </div>

                            <p class="text-gray-700 leading-relaxed">
                                <?= nl2br(htmlspecialchars($review['comment'] ?? 'No comment provided.')); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <div class="text-center py-10 text-gray-500 bg-gray-50 rounded-lg">
                    <i data-lucide="messages-square" class="h-10 w-10 mx-auto mb-3 text-gray-400"></i>
                    <p class="text-lg font-medium">Be the first to review this product!</p>
                    <p class="text-sm">You can add a review after ordering and receiving the item.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        // Product data
        const productImages = [
            "uploads/<?= htmlspecialchars($product['product_image']); ?>",
            <?php foreach ($extraImages as $img) { ?>
                "uploads/<?= htmlspecialchars($img); ?>",
            <?php } ?>
        ];
        
        // --- NEW/UPDATED JAVASCRIPT VARS ---
        const MAX_STOCK = <?= $max_stock; ?>;
        const IS_OUT_OF_STOCK = <?= $is_out_of_stock ? 'true' : 'false'; ?>;
        // --- END NEW/UPDATED JAVASCRIPT VARS ---

        // State variables
        let currentQuantity = 1;
        let selectedImageIndex = 0;
        let isWishlisted = false;

        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            // Set initial quantity to 1 or 0 if out of stock
            currentQuantity = IS_OUT_OF_STOCK ? 0 : 1;
            updateQuantityDisplay();

            // Initial selection for the main image
            selectImage(0); 
        });

        function updateQuantityDisplay() {
            const qtyInput = document.getElementById('qty');
            if (qtyInput) {
                qtyInput.value = currentQuantity;
            }
            const decreaseBtn = document.getElementById('decrease-btn');
            const increaseBtn = document.getElementById('increase-btn');
            
            // Disable decrease button if quantity is 1
            if (decreaseBtn) {
                decreaseBtn.disabled = currentQuantity <= 1;
                if (currentQuantity <= 1) {
                    decreaseBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    decreaseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            // Disable increase button if max stock is reached
            if (increaseBtn) {
                increaseBtn.disabled = currentQuantity >= MAX_STOCK || IS_OUT_OF_STOCK;
                if (currentQuantity >= MAX_STOCK || IS_OUT_OF_STOCK) {
                    increaseBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    increaseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        // Image selection function (no change)
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

        // Quantity management (UPDATED to check against MAX_STOCK)
        function changeQuantity(delta) {
            if (IS_OUT_OF_STOCK) return; // Do nothing if out of stock
            
            const newQuantity = currentQuantity + delta;
            
            if (newQuantity >= 1 && newQuantity <= MAX_STOCK) {
                currentQuantity = newQuantity;
                updateQuantityDisplay();
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
        }

        // Toast notification system
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            
            if (!toast || !toastMessage) return;

            toastMessage.textContent = message;
            
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