<?php
include "header.php";
include "connection.php";

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query dynamically
$select_columns = "product_id, product_name, category, product_price, discounted_price, product_quantity, product_image, product_description, stock_status";
$conditions = ["1=1"]; // Base condition to make AND logic easier

// Category filter
if(!empty($category)){
    $category_escaped = mysqli_real_escape_string($conn, $category);
    $conditions[] = "category='$category_escaped'";
}

// Search filter
if(!empty($search)){
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $conditions[] = "(product_name LIKE '%$search_escaped%' OR category LIKE '%$search_escaped%' OR product_description LIKE '%$search_escaped%')";
}

// Price filters
if(!empty($min_price)){
    $conditions[] = "product_price >= $min_price";
}

if(!empty($max_price)){
    $conditions[] = "product_price <= $max_price";
}

// Build the complete query
$where = implode(" AND ", $conditions);
$query = "SELECT {$select_columns} FROM product WHERE $where";

// Add sorting
if(!empty($sort)){
    if($sort == 'price_asc') {
        $query .= " ORDER BY product_price ASC";
    } elseif($sort == 'price_desc') {
        $query .= " ORDER BY product_price DESC";
    } elseif($sort == 'name_asc') {
        $query .= " ORDER BY product_name ASC";
    } elseif($sort == 'name_desc') {
        $query .= " ORDER BY product_name DESC";
    }
} else {
    $query .= " ORDER BY product_name ASC"; // Default sorting
}

$result = mysqli_query($conn, $query);
$total_products = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - Aroma Hub | Premium Spices & Seasonings</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <style>
    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }
    .product-card { 
        transition: all 0.3s ease; 
    }
    .product-card:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .filter-form {
        background: linear-gradient(135deg, #f97316 0%, #dc2626 100%);
    }
  </style>
</head>
<body class="min-h-screen bg-white">

<main>
  <!-- Breadcrumb Section -->
  <section class="bg-gray-50 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
          <li><a href="index.php" class="text-gray-500 hover:text-orange-600 transition-colors">Home</a></li>
          <li class="flex items-center">
            <i data-lucide="chevron-right" class="h-4 w-4 text-gray-400 mx-2"></i>
            <span class="text-orange-600 font-medium">Products</span>
            <?php if($category != ''){ ?>
                <i data-lucide="chevron-right" class="h-4 w-4 text-gray-400 mx-2"></i>
                <span class="text-gray-700"><?= htmlspecialchars($category); ?></span>
            <?php } ?>
          </li>
        </ol>
      </nav>
    </div>
  </section>

  <!-- Header Section -->
  <section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Our Premium Spices</h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Discover our extensive collection of high-quality spices, herbs, and seasonings
        </p>
        <p class="text-sm text-gray-500 mt-2">
          Showing <?= $total_products; ?> product<?= $total_products != 1 ? 's' : ''; ?>
          <?php if($category != '') echo " in " . htmlspecialchars($category); ?>
        </p>
      </div>

      <!-- Filter and Sort Section -->
      <div class="filter-form rounded-lg p-6 mb-8 shadow-lg">
        <form method="GET" class="space-y-4">
          <!-- Preserve category if set -->
          <?php if($category != ''){ ?>
            <input type="hidden" name="category" value="<?= htmlspecialchars($category); ?>">
          <?php } ?>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-1">
              <label class="block text-white text-sm font-medium mb-2">Search Products</label>
              <div class="relative">
                <input type="text" 
                       name="search" 
                       placeholder="Search spices..." 
                       value="<?= htmlspecialchars($search); ?>"
                       class="w-full px-4 py-2 pl-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400"></i>
              </div>
            </div>

            <!-- Min Price -->
            <div>
              <label class="block text-white text-sm font-medium mb-2">Min Price (₹)</label>
              <div class="relative">
                <input type="number" 
                       name="min_price" 
                       placeholder="0" 
                       min="0" 
                       step="0.01"
                       value="<?= $min_price; ?>"
                       class="w-full px-4 py-2 pl-8 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <span class="absolute left-3 top-2.5 text-gray-400">₹</span>
              </div>
            </div>

            <!-- Max Price -->
            <div>
              <label class="block text-white text-sm font-medium mb-2">Max Price (₹)</label>
              <div class="relative">
                <input type="number" 
                       name="max_price" 
                       placeholder="1000" 
                       min="0" 
                       step="0.01"
                       value="<?= $max_price; ?>"
                       class="w-full px-4 py-2 pl-8 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <span class="absolute left-3 top-2.5 text-gray-400">₹</span>
              </div>
            </div>

            <!-- Sort By -->
            <div>
              <label class="block text-white text-sm font-medium mb-2">Sort By</label>
              <select name="sort" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <option value="">Default</option>
                <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
              </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-col gap-2">
              <label class="block text-white text-sm font-medium mb-2">Actions</label>
              <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-white text-orange-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                  <i data-lucide="filter" class="h-4 w-4 inline mr-1"></i>Filter
                </button>
                <a href="products.php<?= $category != '' ? '?category=' . urlencode($category) : ''; ?>" 
                   class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition-colors text-center">
                  <i data-lucide="x" class="h-4 w-4 inline mr-1"></i>Clear
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Products Grid -->
      <?php if($total_products == 0){ ?>
        <div class="text-center py-16">
          <i data-lucide="package-x" class="h-16 w-16 text-gray-300 mx-auto mb-4"></i>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
          <p class="text-gray-600 mb-4">Try adjusting your filters or search terms</p>
          <a href="products-enhanced.php" class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition-colors">
            View All Products
          </a>
        </div>
      <?php } else { ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <div class="product-card bg-white border-0 shadow-lg rounded-lg overflow-hidden">
              <div class="relative overflow-hidden">
                <a href="singleproduct.php?id=<?= $row['product_id']; ?>">
                  <img src="uploads/<?= $row['product_image']; ?>" 
                      alt="<?= htmlspecialchars($row['product_name']); ?>" 
                      class="w-full h-48 object-cover group-hover-scale-110 transition-transform duration-300"/>
                </a>
                
                <?php 
                // Stock status badge
                if(strtolower($row['stock_status']) == 'out of stock'){ 
                ?>
                  <span class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded text-xs font-medium">
                    <i data-lucide="x-circle" class="h-3 w-3 inline mr-1"></i>Out of Stock
                  </span>
                <?php } else { ?>
                  <span class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded text-xs font-medium">
                    <i data-lucide="check-circle" class="h-3 w-3 inline mr-1"></i>In Stock
                  </span>
                <?php } ?>
                
                <!-- Wishlist button -->
                <button class="absolute top-3 right-3 bg-white/80 hover:bg-white p-2 rounded transition-colors" 
                        onclick="window.location='wishlist.php?product_id=<?= $row['product_id']; ?>'">
                  <i data-lucide="heart" class="h-4 w-4 text-gray-600"></i>
                </button>

                <!-- Discount badge -->
                <?php if(!empty($row['discounted_price']) && $row['discounted_price'] < $row['product_price']){ 
                  $discount_percent = round((($row['product_price'] - $row['discounted_price']) / $row['product_price']) * 100);
                ?>
                  <span class="absolute top-3 right-14 bg-orange-500 text-white px-2 py-1 rounded text-xs font-medium">
                    -<?= $discount_percent; ?>%
                  </span>
                <?php } ?>
              </div>

              <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                  <?= htmlspecialchars($row['product_name']); ?>
                </h3>
                <p class="text-sm text-gray-600 mb-2 line-clamp-2"><?= htmlspecialchars($row['product_description']); ?></p>

                <!-- Price display -->
                <div class="flex items-center space-x-2 mb-4">
                  <?php if(!empty($row['discounted_price']) && $row['discounted_price'] < $row['product_price']){ ?>
                    <span class="text-xl font-bold text-orange-600">₹<?= number_format($row['discounted_price'], 2); ?></span>
                    <span class="text-sm text-gray-500 line-through">₹<?= number_format($row['product_price'], 2); ?></span>
                  <?php } else { ?>
                    <span class="text-xl font-bold text-orange-600">₹<?= number_format($row['product_price'], 2); ?></span>
                  <?php } ?>
                </div>

                <!-- Action buttons -->
                <div class="flex space-x-2">
                  <?php if(strtolower($row['stock_status']) == 'out of stock'){ ?>
                    <button class="flex-1 bg-gray-300 text-gray-600 py-2 px-4 rounded-lg font-medium cursor-not-allowed" disabled>
                      <i data-lucide="x-circle" class="h-4 w-4 inline mr-1"></i>Unavailable
                    </button>
                  <?php } else { ?>
                    <a href="addtocart.php?id=<?= $row['product_id']; ?>&rate=<?= !empty($row['discounted_price']) ? $row['discounted_price'] : $row['product_price']; ?>" 
                      class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded-lg font-medium text-center transition-colors">
                      <i data-lucide="shopping-cart" class="h-4 w-4 inline mr-1"></i>Add to Cart
                    </a>
                  <?php } ?>
                  <a href="singleproduct.php?id=<?= $row['product_id']; ?>" 
                     class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors">
                    <i data-lucide="eye" class="h-4 w-4"></i>
                  </a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </section>
</main>

<?php include "footer.php"; ?>

<script>
// Initialize Lucide icons
lucide.createIcons();

// Add to cart functionality with AJAX
document.addEventListener('DOMContentLoaded', function () {
    // Handle add to cart clicks
    document.querySelectorAll('a[href*="addtocart.php"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            // Extract product ID from href
            const urlParams = new URLSearchParams(href.split('?')[1]);
            const productId = urlParams.get('id');
            const rate = urlParams.get('rate');
            
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 inline mr-1 animate-spin"></i>Adding...';
            this.style.pointerEvents = 'none';
            
            // Make AJAX request
            fetch('addtocart.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/x-www-form-urlencoded' 
                },
                body: `id=${productId}&rate=${rate}&ajax=1`
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'LOGIN') {
                    alert('Please login to continue');
                    window.location = 'login.php';
                } else if (data.trim() === 'ADDED') {
                    // Show success message
                    this.innerHTML = '<i data-lucide="check" class="h-4 w-4 inline mr-1"></i>Added!';
                    this.classList.remove('bg-orange-600', 'hover:bg-orange-700');
                    this.classList.add('bg-green-600');
                    
                    // Reset after 2 seconds
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('bg-green-600');
                        this.classList.add('bg-orange-600', 'hover:bg-orange-700');
                        this.style.pointerEvents = 'auto';
                        lucide.createIcons();
                    }, 2000);
                } else {
                    alert('Something went wrong. Please try again.');
                    this.innerHTML = originalText;
                    this.style.pointerEvents = 'auto';
                    lucide.createIcons();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
                this.innerHTML = originalText;
                this.style.pointerEvents = 'auto';
                lucide.createIcons();
            });
        });
    });
});
</script>
</body>
</html>