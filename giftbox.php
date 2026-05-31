<?php
include "header.php";
include "connection.php";

// Fetch only Giftbox category
$query = "SELECT * FROM product WHERE category='Giftbox'";
$result = mysqli_query($conn, $query);

// Count total products
$total_products = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Boxes - Aroma Hub | Premium Spices & Seasonings</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Lucide Icons -->
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
    
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .gift-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(249, 250, 251, 0.9));
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(249, 115, 22, 0.1);
        }
        
        .gift-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border-color: rgba(249, 115, 22, 0.2);
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
        
        .floating-gift {
            animation: float 3s ease-in-out infinite;
        }
        
        .product-card { 
            transition: all 0.3s ease; 
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(249, 250, 251, 0.9));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(249, 115, 22, 0.1);
        }
        
        .product-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border-color: rgba(249, 115, 22, 0.2);
        }
        
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Breadcrumb -->
<section class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="index.php" class="text-gray-500 hover:text-primary transition-colors">Home</a></li>
                <li class="flex items-center">
                    <i data-lucide="chevron-right" class="h-4 w-4 text-gray-400 mx-2"></i>
                    <span class="text-primary font-medium">Gift Boxes</span>
                </li>
            </ol>
        </nav>
    </div>
</section>

<!-- Hero Section -->
<section class="hero-section text-white py-20 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center">
            <div class="floating-gift inline-block mb-6">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto">
                    <i class="fas fa-gift text-4xl text-white"></i>
                </div>
            </div>
            <h1 class="text-5xl md:text-6xl font-bold mb-6">
                Premium Gift Boxes
                <span class="block text-2xl md:text-3xl font-light mt-2 text-orange-100">Curated Spice Collections</span>
            </h1>
            <p class="text-xl text-orange-100 mb-8 max-w-3xl mx-auto">
                Create the perfect gift with our handpicked spices. Choose from <?= $total_products; ?> premium collections or customize your own unique blend.
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="customize_giftbox.php" class="btn-primary text-white px-8 py-4 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl inline-flex items-center justify-center">
                    <i class="fas fa-palette mr-3"></i>
                    Customize Gift Box
                </a>
                <button class="bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-semibold text-lg border-2 border-white/30 hover:bg-white/30 transition-all duration-300">
                    <i class="fas fa-play mr-3"></i>
                    Watch How It Works
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Gift Box Products Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Gift Collections</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Discover our expertly curated spice gift boxes – perfect for every taste and occasion
            </p>
            <div class="flex items-center justify-center mt-6">
                <div class="bg-orange-50 text-orange-700 px-4 py-2 rounded-full text-sm font-medium">
                    <i class="fas fa-box mr-2"></i>
                    <?= $total_products; ?> Gift Boxes Available
                </div>
            </div>
        </div>

        <?php if ($total_products > 0) { ?>
            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="product-card rounded-2xl overflow-hidden group">
                        <div class="relative overflow-hidden">
                            <a href="singleproduct.php?id=<?= $row['product_id']; ?>">
                                <img src="uploads/<?= $row['product_image']; ?>" 
                                     alt="<?= htmlspecialchars($row['product_name']); ?>" 
                                     class="w-full h-56 object-cover transition-transform duration-300 group-hover:scale-105"/>
                            </a>
                            
                            <!-- Status Badge -->
                            <?php if($row['stock_status']=='Out Of Stock'){ ?>
                                <span class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium shadow-lg">
                                    Out of Stock
                                </span>
                            <?php } else { ?>
                                <span class="absolute top-4 left-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium shadow-lg">
                                    In Stock
                                </span>
                            <?php } ?>
                            
                            <!-- Wishlist Button -->
                            <button class="absolute top-4 right-4 bg-white/90 hover:bg-white p-3 rounded-full transition-all duration-300 shadow-lg group-hover:scale-110" 
                                    onclick="window.location='wishlist.php?product_id=<?= $row['product_id']; ?>'">
                                <i data-lucide="heart" class="h-5 w-5 text-gray-600 hover:text-red-500"></i>
                            </button>

                            <!-- Discount Badge -->
                            <?php if(!empty($row['discounted_price']) && $row['discounted_price']<$row['product_price']){ 
                                $discount_percent = round((($row['product_price'] - $row['discounted_price']) / $row['product_price']) * 100);
                            ?>
                                <span class="absolute bottom-4 right-4 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                                    <?= $discount_percent; ?>% OFF
                                </span>
                            <?php } ?>
                        </div>

                        <div class="p-6">
                            <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                                <?= htmlspecialchars($row['product_name']); ?>
                            </h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2"><?= htmlspecialchars($row['product_description']); ?></p>

                            <!-- Price -->
                            <div class="flex items-center space-x-2 mb-6">
                                <?php if(!empty($row['discounted_price']) && $row['discounted_price']<$row['product_price']){ ?>
                                    <span class="text-2xl font-bold text-primary">₹<?= number_format($row['discounted_price'],2); ?></span>
                                    <span class="text-lg text-gray-500 line-through">₹<?= number_format($row['product_price'],2); ?></span>
                                <?php } else { ?>
                                    <span class="text-2xl font-bold text-primary">₹<?= number_format($row['product_price'],2); ?></span>
                                <?php } ?>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-3">
                                <?php if($row['stock_status']=='Out Of Stock'){ ?>
                                    <button class="flex-1 bg-gray-300 text-gray-600 py-3 px-4 rounded-xl font-medium cursor-not-allowed" disabled>
                                        <i class="fas fa-times mr-2"></i>Unavailable
                                    </button>
                                <?php } else { ?>
                                    <a href="addtocart.php?id=<?= $row['product_id']; ?>&rate=<?= !empty($row['discounted_price'])?$row['discounted_price']:$row['product_price']; ?>" 
                                       class="flex-1 btn-primary text-white py-3 px-4 rounded-xl font-medium text-center hover:shadow-lg transition-all duration-300">
                                        <i data-lucide="shopping-cart" class="h-4 w-4 inline mr-2"></i>Add to Cart
                                    </a>
                                <?php } ?>
                                <a href="singleproduct.php?id=<?= $row['product_id']; ?>" 
                                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-4 rounded-xl font-medium transition-all duration-300 hover:shadow-md">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-gift text-orange-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">No Gift Boxes Available</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    We're currently updating our gift box collection. Please check back soon or create a custom gift box.
                </p>
                <a href="customize_giftbox.php" class="btn-primary text-white px-8 py-3 rounded-xl font-medium inline-flex items-center">
                    <i class="fas fa-palette mr-2"></i>
                    Create Custom Gift Box
                </a>
            </div>
        <?php } ?>
    </div>
</section>

<!-- Customize Section -->
<section class="bg-gradient-to-r from-orange-50 to-red-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-primary to-secondary rounded-3xl flex items-center justify-center mx-auto mb-8">
                <i class="fas fa-magic text-white text-3xl"></i>
            </div>
            <h2 class="text-4xl font-bold text-gray-900 mb-6">Create Your Custom Gift Box</h2>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Choose your favorite spices, select the perfect quantities, and create a personalized gift that's truly unique.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="text-center">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-list-check text-primary text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">1. Choose Spices</h3>
                    <p class="text-gray-600">Select from our premium collection of authentic spices</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-weight-scale text-primary text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">2. Select Quantities</h3>
                    <p class="text-gray-600">Choose between 100g and 250g for each spice</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-gift text-primary text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">3. Beautiful Packaging</h3>
                    <p class="text-gray-600">We'll package it beautifully with a personalized note</p>
                </div>
            </div>
            
            <a href="customize_giftbox.php" class="btn-primary text-white px-12 py-5 rounded-2xl font-bold text-xl shadow-2xl hover:shadow-3xl inline-flex items-center">
                <i class="fas fa-palette mr-4"></i>
                Customize Gift Box Now
            </a>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shipping-fast text-green-600 text-2xl"></i>
                </div>
                <h3 class="font-bold mb-2">Free Shipping</h3>
                <p class="text-gray-600 text-sm">On orders over ₹999</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-gift text-blue-600 text-2xl"></i>
                </div>
                <h3 class="font-bold mb-2">Gift Wrapping</h3>
                <p class="text-gray-600 text-sm">Beautiful packaging included</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-certificate text-purple-600 text-2xl"></i>
                </div>
                <h3 class="font-bold mb-2">Premium Quality</h3>
                <p class="text-gray-600 text-sm">Hand-selected spices</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-heart text-orange-600 text-2xl"></i>
                </div>
                <h3 class="font-bold mb-2">Personal Touch</h3>
                <p class="text-gray-600 text-sm">Custom message cards</p>
            </div>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Add any interactive functionality here
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Gift Box page loaded with <?= $total_products; ?> products');
        
        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add loading animation for product images
        const productImages = document.querySelectorAll('.product-card img');
        productImages.forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
        });
    });
</script>
</body>
</html>