<?php
    include 'header.php';
    ?>

    <main>
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-orange-50 to-red-50 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Content -->
                    <div class="space-y-8 animate-slide-up">
                        <div class="space-y-4">
                            <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 leading-tight">
                                Premium
                                <span class="text-orange-600 block">Spices</span>
                                for Every Kitchen
                            </h1>
                            <p class="text-xl text-gray-600 leading-relaxed">
                                Discover authentic flavors from around the world. Fresh, organic, and sustainably sourced spices delivered to your doorstep.
                            </p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
    <a href="products.php" class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-3 rounded-lg text-lg font-medium transition-colors text-center block">
        Shop Now
    </a>
    <a href="products.php" class="border-2 border-orange-600 text-orange-600 hover:bg-orange-50 px-8 py-3 rounded-lg text-lg font-medium transition-colors text-center block">
        View Collection
    </a>
</div>

                        <!-- Features -->
                        <div class="grid grid-cols-3 gap-4 pt-8">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-orange-600">500+</div>
                                <div class="text-sm text-gray-600">Premium Spices</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-orange-600">100%</div>
                                <div class="text-sm text-gray-600">Organic</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-orange-600">24h</div>
                                <div class="text-sm text-gray-600">Fast Delivery</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Image -->
                    <div class="relative animate-fade-in">
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-400/20 to-red-400/20 rounded-3xl transform rotate-3"></div>
                        <div class="relative bg-white rounded-3xl shadow-2xl p-2 transform -rotate-2 hover:rotate-0 transition-transform duration-500">
                            <img
                                src="uploads\front_end\front.jpg"
                                alt="Colorful spices in market"
                                class="w-full h-96 object-cover rounded-2xl"
                            />
                        </div>
                        <!-- Floating badges -->
                        <div class="absolute top-4 -left-4 bg-white rounded-full px-4 py-2 shadow-lg animate-pulse-slow">
                            <span class="text-sm font-medium text-orange-600">Fresh Daily</span>
                        </div>
                        <div class="absolute bottom-4 -right-4 bg-white rounded-full px-4 py-2 shadow-lg animate-pulse-slow" style="animation-delay: 2s;">
                            <span class="text-sm font-medium text-red-600">Free Shipping</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Background decorative elements -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-orange-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse-slow"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-red-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse-slow" style="animation-delay: 2s;"></div>
        </section>

        <!-- Featured Products -->
       <?php
// Include necessary files
include "connection.php";

// Fetch the 4 newest products from the database
// The 'DESC' keyword orders by product_id in descending order (newest first).
// 'LIMIT 4' restricts the result to the four most recent products.
$query = "SELECT * FROM product ORDER BY product_id DESC LIMIT 4";
$result = mysqli_query($conn, $query);
?>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                New Arrivals
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Discover our newest and most exciting spice additions
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="group cursor-pointer bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 rounded-lg overflow-hidden block">
                    <a href="singleproduct.php?id=<?= $row['product_id']; ?>">
                        <div class="relative overflow-hidden">
                            <img
                                src="uploads/<?= htmlspecialchars($row['product_image']); ?>"
                                alt="<?= htmlspecialchars($row['product_name']); ?>"
                                class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300"
                            />

                            <div class="absolute top-3 left-3 flex flex-col space-y-2">
                                <?php if ($row['stock_status'] == 'Out Of Stock') { ?>
                                    <span class="bg-red-500 text-white px-2 py-1 rounded text-xs font-medium">Out of Stock</span>
                                <?php } else if ($row['quality'] == 'Organic') { ?>
                                    <span class="bg-emerald-500 text-white px-2 py-1 rounded text-xs font-medium">Organic</span>
                                <?php } ?>
                            </div>

                            <button 
                                class="absolute top-3 right-3 bg-white/80 hover:bg-white p-2 rounded z-10"
                                onclick="event.preventDefault(); window.location='wishlist.php?product_id=<?= $row['product_id']; ?>';"
                            >
                                <i data-lucide="heart" class="h-4 w-4"></i>
                            </button>

                            <div class="absolute bottom-3 left-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10">
                                <a 
                                    href="addtocart.php?id=<?= $row['product_id']; ?>&rate=<?= !empty($row['discounted_price']) ? $row['discounted_price'] : $row['product_price']; ?>"
                                    class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded flex items-center justify-center space-x-2"
                                    onclick="event.preventDefault(); window.location.href=this.href;"
                                >
                                    <i data-lucide="shopping-cart" class="h-4 w-4"></i>
                                    <span>Add to Cart</span>
                                </a>
                            </div>
                        </div>
                    </a>

                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                            <?= htmlspecialchars($row['product_name']); ?>
                        </h3>
                        
                        <div class="flex items-center mb-2">
                            <div class="flex items-center">
                                <?php 
                                // You would need to fetch the average rating from your database.
                                // This is a static example for now.
                                $rating = 5; // Example rating
                                for($i = 0; $i < 5; $i++) {
                                    if ($i < $rating) {
                                        echo '<i data-lucide="star" class="h-4 w-4 text-yellow-400 fill-current"></i>';
                                    } else {
                                        echo '<i data-lucide="star" class="h-4 w-4 text-gray-300"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <span class="text-sm text-gray-600 ml-2">4.8 (142)</span>
                        </div>

                        <div class="flex items-center space-x-2">
                            <?php if (!empty($row['discounted_price']) && $row['discounted_price'] < $row['product_price']) { ?>
                                <span class="text-xl font-bold text-orange-600">₹<?= number_format($row['discounted_price'], 2); ?></span>
                                <span class="text-sm text-gray-500 line-through">₹<?= number_format($row['product_price'], 2); ?></span>
                            <?php } else { ?>
                                <span class="text-xl font-bold text-orange-600">₹<?= number_format($row['product_price'], 2); ?></span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="text-center mt-12">
            <a href="products.php" class="border-2 border-orange-600 text-orange-600 hover:bg-orange-50 px-8 py-3 rounded-lg text-lg font-medium transition-colors">
                View All Products
            </a>
        </div>
    </div>
</section>
        <!-- Categories -->
        <section class="py-16 bg-gradient-to-br from-orange-50 to-red-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        Shop by Categories
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Explore our carefully curated collections of premium spices and seasonings
                    </p>
                </div>

                <!-- Categories Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Category 1 -->
                  <a href="products.php?category=Spices" class="group cursor-pointer bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 rounded-lg overflow-hidden block">
    <div class="relative overflow-hidden">
        <img
            src="uploads\front_end\whole spices.jpg"
            alt="Whole Spices"
            class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300"
        />
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>

    <div class="p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
            Whole Spices
        </h3>
        <p class="text-gray-600 mb-4">
            Fresh whole spices for maximum flavor
        </p>
        
        <div class="flex items-center text-orange-600 font-medium group-hover:translate-x-2 transition-transform duration-300">
            <span>Explore Collection</span>
            <i data-lucide="chevron-right" class="w-4 h-4 ml-2"></i>
        </div>
    </div>
</a>
              <!-- Category 2 -->
<a href="products.php?category=Powders" class="group cursor-pointer bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 rounded-lg overflow-hidden block">
    <div class="relative overflow-hidden">
        <img
            src="uploads/front_end/spice powders.jpg"
            alt="Spice Powders"
            class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300"
        />
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>

    <div class="p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
            Spice Powders
        </h3>
        <p class="text-gray-600 mb-4">
            Ready-to-use fresh and aromatic spice powders
        </p>
        
        <div class="flex items-center text-orange-600 font-medium group-hover:translate-x-2 transition-transform duration-300">
            <span>Explore Collection</span>
            <i data-lucide="chevron-right" class="w-4 h-4 ml-2"></i>
        </div>
    </div>
</a>


             <!-- Category 3 -->
<a href="products.php?category=Blended Spices" class="group cursor-pointer bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 rounded-lg overflow-hidden block">
    <div class="relative overflow-hidden">
        <img
            src="uploads/front_end/blended.avif"
            alt="Blended Spices"
            class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300"
        />
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>

    <div class="p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
            Blended Spices
        </h3>
        <p class="text-gray-600 mb-4">
            Expertly crafted masalas like Garam Masala, Curry Powder, and Biryani Mix
        </p>
        
        <div class="flex items-center text-orange-600 font-medium group-hover:translate-x-2 transition-transform duration-300">
            <span>Explore Collection</span>
            <i data-lucide="chevron-right" class="w-4 h-4 ml-2"></i>
        </div>
    </div>
</a>


                    <!-- Category 4 -->
<a href="products.php?category=Dry Fruits" class="group cursor-pointer bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 rounded-lg overflow-hidden block">
    <div class="relative overflow-hidden">
        <img
            src="uploads/front_end/nuts.jpg"
            alt="Dry Fruits & Nuts"
            class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300"
        />
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>

    <div class="p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
            Dry Fruits &amp; Nuts
        </h3>
        <p class="text-gray-600 mb-4">
            Premium quality almonds, cashews, raisins, and more
        </p>
        
        <div class="flex items-center text-orange-600 font-medium group-hover:translate-x-2 transition-transform duration-300">
            <span>Explore Collection</span>
            <i data-lucide="chevron-right" class="w-4 h-4 ml-2"></i>
        </div>
    </div>
</a>


                  <!-- Category 5 -->
<a href="giftbox.php" class="group cursor-pointer bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 rounded-lg overflow-hidden block">
    <div class="relative overflow-hidden">
        <img
            src="uploads/front_end/gift.avif"
            alt="Mixed Spices Gift Box"
            class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300"
        />
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>

    <div class="p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
            Mixed Spices Gift Box
        </h3>
        <p class="text-gray-600 mb-4">
            Beautifully packed collections of premium blended and whole spices
        </p>
        
        <div class="flex items-center text-orange-600 font-medium group-hover:translate-x-2 transition-transform duration-300">
            <span>Explore Collection</span>
            <i data-lucide="chevron-right" class="w-4 h-4 ml-2"></i>
        </div>
    </div>
</a>


                 <!-- Category 6 -->
<a href="bulkorders.php" class="group cursor-pointer bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 rounded-lg overflow-hidden block">
    <div class="relative overflow-hidden">
        <img
            src="uploads/front_end/bulk.png"
            alt="Bulk Spices"
            class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300"
        />
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>

    <div class="p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
            Bulk Spices
        </h3>
        <p class="text-gray-600 mb-4">
            Wholesale supply of high-quality whole and blended spices in bulk quantities
        </p>
        
        <div class="flex items-center text-orange-600 font-medium group-hover:translate-x-2 transition-transform duration-300">
            <span>Explore Collection</span>
            <i data-lucide="chevron-right" class="w-4 h-4 ml-2"></i>
        </div>
    </div>
</a>

                    </div>
                </div>
            </div>
        </section>

        <!-- Image Slider Section -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        Spice Gallery
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Explore the vibrant world of spices from different cuisines and cultures
                    </p>
                </div>

                <!-- Image Slider -->
                <div class="relative">
                    <div class="slider-container overflow-hidden rounded-2xl shadow-2xl">
                        <div class="slider-wrapper flex transition-transform duration-500 ease-in-out" id="imageSlider">
                            <!-- Slide 1 -->
                            <div class="slide min-w-full relative">
                                <img
                                    src="https://images.unsplash.com/photo-1543376798-286a59d5da12?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxpbmRpYW4lMjBzcGljZXMlMjBjb29raW5nfGVufDF8fHx8MTc1ODcxNzkxOXww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
                                    alt="Indian Spices Cooking"
                                    class="w-full h-96 object-cover"
                                />
                                <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <h3 class="text-3xl font-bold mb-2">Indian Cuisine Spices</h3>
                                        <p class="text-lg opacity-90">Authentic flavors from the subcontinent</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 2 -->
                            <div class="slide min-w-full relative">
                                <img
                                    src="https://images.unsplash.com/photo-1741010812421-2b5bff9f95fc?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtZWRpdGVycmFuZWFuJTIwY29va2luZyUyMHNwaWNlc3xlbnwxfHx8fDE3NTg3MjI5MzR8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
                                    alt="Mediterranean Cooking Spices"
                                    class="w-full h-96 object-cover"
                                />
                                <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <h3 class="text-3xl font-bold mb-2">Mediterranean Herbs</h3>
                                        <p class="text-lg opacity-90">Fresh herbs from the Mediterranean coast</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 3 -->
                            <div class="slide min-w-full relative">
                                <img
                                    src="https://images.unsplash.com/photo-1455619452474-d2be8b1e70cd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxhc2lhbiUyMHNwaWNlcyUyMGNvb2tpbmd8ZW58MXx8fHwxNzU4NzIyOTQxfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
                                    alt="Asian Spices Cooking"
                                    class="w-full h-96 object-cover"
                                />
                                <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <h3 class="text-3xl font-bold mb-2">Asian Aromatics</h3>
                                        <p class="text-lg opacity-90">Bold flavors from across Asia</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 4 -->
                            <div class="slide min-w-full relative">
                                <img
                                    src="https://images.unsplash.com/photo-1756363886854-b51467278a52?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjb2xvcmZ1bCUyMHNwaWNlcyUyMG1hcmtldCUyMGJhemFhcnxlbnwxfHx8fDE3NTg3MjIwMTJ8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
                                    alt="Colorful Spices Market"
                                    class="w-full h-96 object-cover"
                                />
                                <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <h3 class="text-3xl font-bold mb-2">Global Spice Market</h3>
                                        <p class="text-lg opacity-90">World's finest spices in one place</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Arrows -->
                    <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full p-3 shadow-lg transition-all duration-300" onclick="previousSlide()">
                        <i data-lucide="chevron-left" class="h-6 w-6 text-gray-700"></i>
                    </button>
                    <button class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full p-3 shadow-lg transition-all duration-300" onclick="nextSlide()">
                        <i data-lucide="chevron-right" class="h-6 w-6 text-gray-700"></i>
                    </button>

                    <!-- Dots Indicator -->
                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                        <button class="w-3 h-3 rounded-full bg-white bg-opacity-60 hover:bg-opacity-100 transition-all duration-300 slider-dot active" onclick="goToSlide(0)"></button>
                        <button class="w-3 h-3 rounded-full bg-white bg-opacity-60 hover:bg-opacity-100 transition-all duration-300 slider-dot" onclick="goToSlide(1)"></button>
                        <button class="w-3 h-3 rounded-full bg-white bg-opacity-60 hover:bg-opacity-100 transition-all duration-300 slider-dot" onclick="goToSlide(2)"></button>
                        <button class="w-3 h-3 rounded-full bg-white bg-opacity-60 hover:bg-opacity-100 transition-all duration-300 slider-dot" onclick="goToSlide(3)"></button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
       
        <!-- Recipe Section -->
       <!-- Blogs Preview Section -->
<section id="blogs-section" class="py-16 bg-gradient-to-br from-orange-50 to-red-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                Latest From Our Blog
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Explore spice stories, health benefits, and cooking guides curated by Aroma Hub
            </p>
        </div>

        <!-- Blog Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <?php
            include "connection.php";

            // Fetch the latest 3 blogs
            $blog_query = "SELECT * FROM blog ORDER BY created_at DESC LIMIT 3";
            $blog_result = mysqli_query($conn, $blog_query);

            while ($row = mysqli_fetch_assoc($blog_result)) {
                $title = htmlspecialchars($row['title']);
                $snippet = htmlspecialchars(substr($row['content'], 0, 120)) . "...";
                $image = "uploads/blogs/" . htmlspecialchars($row['image']);
                $id = $row['blog_id'];
                $date = date("F j, Y", strtotime($row['created_at']));
            ?>

            <!-- Single Blog Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                <img src="<?php echo $image; ?>" 
                     alt="<?php echo $title; ?>" 
                     class="w-full h-48 object-cover" />

                <div class="p-6">
                    <span class="text-sm text-gray-500 block mb-2"><?php echo $date; ?></span>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-orange-600 transition-colors">
                        <a href="blogdetails.php?id=<?php echo $id; ?>"><?php echo $title; ?></a>
                    </h3>
                    <p class="text-gray-600 mb-4">
                        <?php echo $snippet; ?>
                    </p>

                    <a href="blogdetails.php?id=<?php echo $id; ?>" 
                       class="text-orange-600 font-medium hover:underline">
                        Read More →
                    </a>
                </div>
            </div>

            <?php } ?>

        </div>

        <!-- View All Blogs Button -->
        <div class="text-center mt-12">
            <a href="blogs.php" 
               class="border-2 border-orange-600 text-orange-600 hover:bg-orange-50 px-8 py-3 rounded-lg text-lg font-medium transition-colors">
                Browse All Blogs
            </a>
        </div>

    </div>
</section>


        <!-- Customer Testimonials -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        What Our Customers Say
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Don't just take our word for it - here's what our satisfied customers have to say about Aroma Hub
                    </p>
                </div>

                <!-- Testimonials Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            </div>
                        </div>
                        <blockquote class="text-gray-700 mb-6 italic">
                            "The quality of spices from Aroma Hub is absolutely outstanding! My curries have never tasted better. The turmeric is so fresh and aromatic - you can really taste the difference."
                        </blockquote>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-orange-700 font-bold text-lg">S</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Sarah Johnson</h4>
                                <p class="text-gray-600 text-sm">Home Chef, California</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            </div>
                        </div>
                        <blockquote class="text-gray-700 mb-6 italic">
                            "As a professional chef, I'm very particular about my spices. Aroma Hub consistently delivers premium quality products. Their cardamom pods are the best I've ever used!"
                        </blockquote>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-orange-700 font-bold text-lg">M</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Chef Michael Rodriguez</h4>
                                <p class="text-gray-600 text-sm">Executive Chef, New York</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            </div>
                        </div>
                        <blockquote class="text-gray-700 mb-6 italic">
                            "Love the organic collection! The packaging is beautiful and the spices stay fresh for so long. The customer service is also exceptional - they really care about their customers."
                        </blockquote>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-orange-700 font-bold text-lg">E</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Emily Chen</h4>
                                <p class="text-gray-600 text-sm">Food Blogger, Seattle</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 4 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            </div>
                        </div>
                        <blockquote class="text-gray-700 mb-6 italic">
                            "The spice blends are incredible! I've been experimenting with their Mediterranean herb mix and it's transformed my cooking. Fast shipping and excellent quality every time."
                        </blockquote>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-orange-700 font-bold text-lg">D</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">David Thompson</h4>
                                <p class="text-gray-600 text-sm">Home Cook, Texas</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 5 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            </div>
                        </div>
                        <blockquote class="text-gray-700 mb-6 italic">
                            "Perfect for my restaurant! The bulk orders are convenient and the quality is consistent. My customers always compliment the authentic flavors in our dishes."
                        </blockquote>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-orange-700 font-bold text-lg">A</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Aisha Patel</h4>
                                <p class="text-gray-600 text-sm">Restaurant Owner, Chicago</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 6 -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                            </div>
                        </div>
                        <blockquote class="text-gray-700 mb-6 italic">
                            "The international spices collection is amazing! I love trying recipes from different cultures and Aroma Hub has everything I need. Great prices and fast delivery!"
                        </blockquote>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center mr-4">
                                <span class="text-orange-700 font-bold text-lg">R</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Robert Williams</h4>
                                <p class="text-gray-600 text-sm">Culinary Student, Florida</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="p-6">
                        <div class="text-3xl font-bold text-orange-600 mb-2">50,000+</div>
                        <div class="text-gray-600">Happy Customers</div>
                    </div>
                    <div class="p-6">
                        <div class="text-3xl font-bold text-orange-600 mb-2">4.9/5</div>
                        <div class="text-gray-600">Average Rating</div>
                    </div>
                    <div class="p-6">
                        <div class="text-3xl font-bold text-orange-600 mb-2">10,000+</div>
                        <div class="text-gray-600">Reviews</div>
                    </div>
                    <div class="p-6">
                        <div class="text-3xl font-bold text-orange-600 mb-2">99%</div>
                        <div class="text-gray-600">Satisfaction Rate</div>
                    </div>
                </div>
            </div>
 <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Feature 1 -->
                    <div class="text-center bg-white border-0 shadow-lg hover:shadow-xl transition-shadow duration-300 rounded-lg p-8">
                        <div class="flex justify-center mb-4">
                            <div class="p-4 bg-orange-50 rounded-full">
                                <i data-lucide="truck" class="h-12 w-12 text-orange-600"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">
                            Free Shipping
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Free delivery on orders over $50. Fast and reliable shipping worldwide.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="text-center bg-white border-0 shadow-lg hover:shadow-xl transition-shadow duration-300 rounded-lg p-8">
                        <div class="flex justify-center mb-4">
                            <div class="p-4 bg-orange-50 rounded-full">
                                <i data-lucide="shield" class="h-12 w-12 text-orange-600"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">
                            Quality Guarantee
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            100% satisfaction guarantee. Return if not completely satisfied.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="text-center bg-white border-0 shadow-lg hover:shadow-xl transition-shadow duration-300 rounded-lg p-8">
                        <div class="flex justify-center mb-4">
                            <div class="p-4 bg-orange-50 rounded-full">
                                <i data-lucide="leaf" class="h-12 w-12 text-orange-600"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">
                            Organic & Natural
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Sustainably sourced, organic spices without artificial additives.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="text-center bg-white border-0 shadow-lg hover:shadow-xl transition-shadow duration-300 rounded-lg p-8">
                        <div class="flex justify-center mb-4">
                            <div class="p-4 bg-orange-50 rounded-full">
                                <i data-lucide="award" class="h-12 w-12 text-orange-600"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">
                            Premium Quality
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Handpicked spices from the finest sources around the world.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        </section>
        
    </main>

    <?php
    include 'footer.php';
    ?>
    <!-- JavaScript -->
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add intersection observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observe all sections for animations
        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });

        // Add cart functionality (basic example)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.add-to-cart') || e.target.closest('[class*="shopping-cart"]')) {
                // Simple cart notification
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
                notification.textContent = 'Item added to cart!';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        });

        // Mobile menu toggle (if needed)
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('hidden');
            }
        }

        // Search functionality placeholder
        const searchInput = document.querySelector('input[type="text"]');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    // Implement search functionality here
                    console.log('Searching for:', this.value);
                }
            });
        }

        // Newsletter subscription
        const newsletterForm = document.querySelector('footer input[type="email"]');
        const subscribeBtn = document.querySelector('footer button');
        
        if (subscribeBtn) {
            subscribeBtn.addEventListener('click', function() {
                const email = newsletterForm.value;
                if (email && email.includes('@')) {
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
                    notification.textContent = 'Successfully subscribed!';
                    document.body.appendChild(notification);
                    
                    newsletterForm.value = '';
                    
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                } else {
                    alert('Please enter a valid email address');
                }
            });
        }

        // Image Slider Functionality
        let currentSlide = 0;
        const totalSlides = 4;
        const slider = document.getElementById('imageSlider');
        const dots = document.querySelectorAll('.slider-dot');

        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            const translateX = -slideIndex * 100;
            slider.style.transform = `translateX(${translateX}%)`;
            
            // Update dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === slideIndex);
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            goToSlide(currentSlide);
        }

        function previousSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            goToSlide(currentSlide);
        }

        // Auto-advance slider every 5 seconds
        setInterval(nextSlide, 5000);

        // Recipe card interactions
        document.addEventListener('click', function(e) {
            if (e.target.closest('.recipe-view-btn') || e.target.textContent === 'View Recipe') {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
                notification.textContent = 'Recipe opened!';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        });

        // User Authentication Management
        let isLoggedIn = false;
        let currentUser = null;

        function showLoginState() {
            const guestMenu = document.getElementById('guest-menu');
            const userMenu = document.getElementById('user-menu');
            const accountText = document.getElementById('account-text');
            
            if (isLoggedIn) {
                guestMenu.style.display = 'none';
                userMenu.style.display = 'block';
                accountText.textContent = currentUser ? `Hi, ${currentUser}` : 'Account';
            } else {
                guestMenu.style.display = 'block';
                userMenu.style.display = 'none';
                accountText.textContent = 'Account';
            }
        }

        function showLoginForm() {
            // Simulate login form
            const username = prompt('Enter username (demo: any name)');
            if (username && username.trim()) {
                isLoggedIn = true;
                currentUser = username.trim();
                showLoginState();
                
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
                notification.textContent = `Welcome back, ${currentUser}!`;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        }

        function showRegisterForm() {
            // Simulate registration form
            const username = prompt('Enter username for registration (demo: any name)');
            if (username && username.trim()) {
                isLoggedIn = true;
                currentUser = username.trim();
                showLoginState();
                
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
                notification.textContent = `Account created! Welcome, ${currentUser}!`;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        }

        function logoutUser() {
            if (confirm('Are you sure you want to logout?')) {
                isLoggedIn = false;
                currentUser = null;
                showLoginState();
                
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-orange-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
                notification.textContent = 'Successfully logged out!';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        }

        // Scroll to recipes section
        function scrollToRecipes() {
            const recipesSection = document.getElementById('recipes-section');
            if (recipesSection) {
                recipesSection.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }

        // Initialize the page with guest state
        document.addEventListener('DOMContentLoaded', function() {
            showLoginState();
        });
    </script>
</body>
</html>