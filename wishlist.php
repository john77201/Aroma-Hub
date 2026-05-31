<?php
include 'header.php';
include 'connection.php';

// Redirect to login if user not logged in
if(!isset($_SESSION['userid'])){
    echo "<script>window.location='login.php';</script>";
    exit;
}

// Add product to wishlist if product_id is passed
if (isset($_GET['product_id'])) {
    $pid = $_GET['product_id'];
    $userid = $_SESSION['userid'];

    // Prevent duplicate wishlist entries
    $check = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id='$userid' AND product_id='$pid'");
    if(mysqli_num_rows($check) == 0){
        mysqli_query($conn, "INSERT INTO wishlist (user_id, product_id) VALUES ('$userid','$pid')");
    }

    echo "<script>window.location='wishlist.php';</script>";
    exit;
}

// Fetch all wishlist items for this user
$userid = $_SESSION['userid'];
$qry = "SELECT w.id AS wishlist_id, p.* 
        FROM wishlist w
        JOIN product p ON w.product_id = p.product_id
        WHERE w.user_id='$userid'";
$result = mysqli_query($conn, $qry);

// Calculate statistics
$total_value = 0;
$total_savings = 0;
$items_count = mysqli_num_rows($result);
$wishlist_data = [];

while($row = mysqli_fetch_assoc($result)){
    $price_to_use = (!empty($row['discounted_price']) && $row['discounted_price'] > 0) 
                    ? $row['discounted_price'] 
                    : $row['product_price'];
    $original_price = $row['product_price'];
    
    $total_value += $price_to_use;
    if(!empty($row['discounted_price']) && $row['discounted_price'] > 0){
        $total_savings += ($original_price - $row['discounted_price']);
    }
    
    $wishlist_data[] = $row;
}

// Reset pointer for display
mysqli_data_seek($result, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - Aroma Hub</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316',
                        secondary: '#dc2626',
                        accent: '#fed7aa',
                        'warm-gray': '#78716c',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'scale-in': 'scaleIn 0.3s ease-out',
                        'bounce-gentle': 'bounceGentle 0.6s ease-out',
                        'shimmer': 'shimmer 2s linear infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(30px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.9)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                        bounceGentle: {
                            '0%, 20%, 50%, 80%, 100%': { transform: 'translateY(0)' },
                            '40%': { transform: 'translateY(-10px)' },
                            '60%': { transform: 'translateY(-5px)' },
                        },
                        shimmer: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(100%)' },
                        },
                    },
                },
            },
        }
    </script>
    
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .wishlist-item {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(249, 250, 251, 0.9));
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(249, 115, 22, 0.1);
        }
        
        .wishlist-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            background: linear-gradient(135deg, rgba(255, 255, 255, 1), rgba(255, 247, 237, 0.9));
            border-color: rgba(249, 115, 22, 0.2);
        }
        
        .toast {
            position: fixed;
            top: 24px;
            right: 24px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f97316, #ea580c);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #ea580c, #dc2626);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(249, 115, 22, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(249, 250, 251, 0.8));
            backdrop-filter: blur(10px);
            border: 2px solid #f97316;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(249, 115, 22, 0.3);
        }
        
        .hero-section {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #dc2626 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            opacity: 0.3;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Header -->
   
    <!-- Hero Section -->
    <section class="hero-section text-white py-16 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <div class="inline-flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 mb-6">
                    <i class="fas fa-heart text-pink-300"></i>
                    <span class="text-sm font-medium">Your Favorite Spices</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold mb-4">
                    My Wishlist
                    <span class="block text-2xl md:text-3xl font-light mt-2 text-orange-100">Curated with Love</span>
                </h1>
                <p class="text-xl text-orange-100 mb-8 max-w-2xl mx-auto"><?php echo $items_count; ?> premium spices saved for later</p>
                
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-8 max-w-md mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold">₹<?php echo number_format($total_value, 2); ?></div>
                        <div class="text-sm text-orange-200">Total Value</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">₹<?php echo number_format($total_savings, 2); ?></div>
                        <div class="text-sm text-orange-200">You Save</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold"><?php echo $items_count; ?></div>
                        <div class="text-sm text-orange-200">Items</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions Bar -->
<div class="glass-card border-b border-orange-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">
                    <i class="fas fa-heart text-primary mr-2"></i>
                    <?php echo $items_count; ?> Items in Wishlist
                </span>
            </div>
            <div class="flex items-center space-x-3">
                <a href="products.php" 
                   class="flex items-center bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>



    <!-- Wishlist Items -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if($items_count > 0){ ?>
        <div class="space-y-6">
            <?php 
            $delay = 0.1;
            while($row = mysqli_fetch_assoc($result)){ 
                $price_to_use = (!empty($row['discounted_price']) && $row['discounted_price'] > 0) 
                                ? $row['discounted_price'] 
                                : $row['product_price'];
                $has_discount = !empty($row['discounted_price']) && $row['discounted_price'] > 0;
                $discount_percent = $has_discount ? round((($row['product_price'] - $row['discounted_price']) / $row['product_price']) * 100) : 0;
            ?>
            <!-- Wishlist Item -->
            <div class="wishlist-item rounded-2xl p-6 animate-slide-up" style="animation-delay: <?php echo $delay; ?>s">
                <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
                    <!-- Product Image -->
                    <div class="relative flex-shrink-0 group">
                        <div class="w-28 h-28 rounded-2xl overflow-hidden bg-gradient-to-br from-orange-100 to-orange-50 p-1">
                            <a href="singleproduct.php?id=<?php echo $row['product_id']; ?>">
                                <img src="uploads/<?php echo $row['product_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($row['product_name']); ?>" 
                                     class="w-full h-full object-cover rounded-xl transition-transform duration-300 group-hover:scale-105">
                            </a>
                        </div>
                        <!-- Heart Icon -->
                        <div class="absolute top-2 left-2 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center">
                            <i class="fas fa-heart text-red-500 text-sm"></i>
                        </div>
                        <?php if($row['stock_status'] != 'Out Of Stock'){ ?>
                        <div class="absolute -bottom-2 -right-2">
                            <span class="bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs px-3 py-1 rounded-full font-medium shadow-lg">In Stock</span>
                        </div>
                        <?php } else { ?>
                        <div class="absolute -bottom-2 -right-2">
                            <span class="bg-gradient-to-r from-red-500 to-rose-500 text-white text-xs px-3 py-1 rounded-full font-medium shadow-lg">Out of Stock</span>
                        </div>
                        <?php } ?>
                    </div>
                    
                    <!-- Product Details -->
                    <div class="flex-1 min-w-0">
                        <div class="mb-3">
                            <a href="singleproduct.php?id=<?php echo $row['product_id']; ?>">
                                <h3 class="text-xl font-bold text-gray-900 mb-1 hover:text-primary transition-colors"><?php echo htmlspecialchars($row['product_name']); ?></h3>
                            </a>
                            <?php if(!empty($row['product_description'])){ ?>
                            <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars(substr($row['product_description'], 0, 100)); ?>...</p>
                            <?php } ?>
                        </div>
                        
                        <div class="flex items-center space-x-4 mb-4">
                            <span class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">₹<?php echo number_format($price_to_use, 2); ?></span>
                            <?php if($has_discount){ ?>
                            <span class="text-lg text-gray-500 line-through">₹<?php echo number_format($row['product_price'], 2); ?></span>
                            <span class="bg-gradient-to-r from-red-500 to-pink-500 text-white text-sm px-3 py-1 rounded-full font-medium"><?php echo $discount_percent; ?>% off</span>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col space-y-3 w-full md:w-auto md:min-w-[200px]">
                        <?php if($row['stock_status'] == 'Out Of Stock'){ ?>
                            <button class="bg-gray-400 text-white px-6 py-3 rounded-xl font-medium cursor-not-allowed" disabled>
                                <i class="fas fa-ban mr-2"></i>
                                Out of Stock
                            </button>
                        <?php } else { ?>
                            <a href="checkout.php?buy_now=1&id=<?php echo $row['product_id']; ?>&rate=<?php echo $price_to_use; ?>" 
                               class="btn-primary text-white px-6 py-3 rounded-xl font-medium shadow-lg hover:shadow-xl text-center">
                                <i class="fas fa-lightning-bolt mr-2"></i>
                                Buy Now
                            </a>
                            <a href="addtocart.php?id=<?php echo $row['product_id']; ?>&rate=<?php echo $price_to_use; ?>" 
                               class="btn-secondary text-primary px-6 py-3 rounded-xl font-medium text-center">
                                <i class="fas fa-cart-plus mr-2"></i>
                                Add to Cart
                            </a>
                        <?php } ?>
                        <a href="remove_wishlist.php?id=<?php echo $row['wishlist_id']; ?>" 
                           onclick="return confirm('Are you sure you want to remove this item from wishlist?');"
                           class="text-red-500 hover:text-red-600 p-3 rounded-xl hover:bg-red-50 transition-all duration-300 text-center">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Remove
                        </a>
                    </div>
                </div>
            </div>
            <?php 
                $delay += 0.1;
            } ?>
        </div>

        <!-- Bulk Actions -->
        <div class="mt-12 animate-fade-in">
            <div class="glass-card rounded-3xl p-8 max-w-2xl mx-auto text-center">
                <div class="mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heart text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Love these spices?</h3>
                    <p class="text-gray-600">Your curated collection of premium spices</p>
                </div>
                
                <!-- Summary Cards -->
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-4">
                        <div class="text-2xl font-bold text-gray-900">₹<?php echo number_format($total_value, 2); ?></div>
                        <div class="text-sm text-gray-600">Total Value</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-4">
                        <div class="text-2xl font-bold text-green-600">₹<?php echo number_format($total_savings, 2); ?></div>
                        <div class="text-sm text-gray-600">You Save</div>
                    </div>
                </div>
                
                <?php if($total_value >= 99){ ?>
                <!-- Free Shipping Notice -->
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center justify-center text-green-700">
                        <i class="fas fa-truck mr-2"></i>
                        <span class="font-medium">Free shipping on orders over ₹99 • Your order qualifies!</span>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

        <?php } else { ?>
        <!-- Empty Wishlist State -->
        <div class="text-center max-w-lg mx-auto py-16">
            <div class="relative mb-8">
                <div class="w-32 h-32 bg-gradient-to-br from-orange-100 to-red-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce-gentle">
                    <i class="fas fa-heart text-4xl text-primary"></i>
                </div>
            </div>
            
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Your Wishlist is Empty</h2>
            <p class="text-lg text-gray-600 mb-8">Save your favorite spices and herbs for later. Start browsing our premium collection to add items to your wishlist.</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8 text-sm text-gray-500">
                <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-heart text-red-500"></i>
                    <span>Save favorites</span>
                </div>
                <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-bell text-yellow-500"></i>
                    <span>Get price alerts</span>
                </div>
                <div class="flex items-center justify-center space-x-2">
                    <i class="fas fa-share text-blue-500"></i>
                    <span>Share with friends</span>
                </div>
            </div>
            
            <a href="products.php" class="btn-primary text-white px-8 py-4 rounded-xl font-medium text-lg shadow-lg hover:shadow-xl inline-block">
                <i class="fas fa-shopping-bag mr-3"></i>
                Start Shopping
            </a>
        </div>
        <?php } ?>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script>
        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} text-lg"></i>
                    <span class="font-medium">${message}</span>
                </div>
            `;
            toast.className = `toast show`;
            toast.style.background = type === 'success' 
                ? 'linear-gradient(135deg, #10b981, #059669)' 
                : 'linear-gradient(135deg, #ef4444, #dc2626)';
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3500);
        }
        
        // Check for success message in URL
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get('added') === '1'){
            showToast('Product added to wishlist successfully!');
        }
        if(urlParams.get('removed') === '1'){
            showToast('Product removed from wishlist');
        }
    </script>
</body>
</html>