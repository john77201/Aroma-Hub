<?php
include "header.php";
include "connection.php";

// Query to fetch bulk products, maintaining the original logic
$query = "SELECT * FROM product WHERE category='Bulk' LIMIT 12";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bulk Orders - Aroma Hub | Premium Spices & Seasonings</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <style>
    /* Tailwind configuration for primary color (inherited from products.php) */
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#f97316',
                }
            }
        }
    }

    /* Custom styles for product card consistency */
    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }
    .product-card { 
      transition: all 0.3s ease; 
      /* Ensures the shadow and border are consistent with myorders.html design logic */
      border: 1px solid #f3f4f6; 
    }
    .product-card:hover { 
      transform: translateY(-8px); 
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    /* Add a small utility class for the quality field styling */
    .quality-badge { 
      font-size: 0.875rem; /* text-sm */
      color: #1d4ed8; /* blue-700 */
      font-weight: 600; /* font-semibold */
    }
  </style>
</head>
<body class="min-h-screen bg-white">

<?php // if your header.php includes HTML, it will render here ?>

<main>
  <section class="bg-gray-50 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
          <li><a href="index.php" class="text-gray-500 hover:text-orange-600 transition-colors">Home</a></li>
          <li class="flex items-center">
            <i data-lucide="chevron-right" class="h-4 w-4 text-gray-400 mx-2"></i>
            <span class="text-orange-600 font-medium">Bulk Orders</span>
          </li>
        </ol>
      </nav>
    </div>
  </section>
  
  <hr>

  <section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Bulk & Wholesale Spices</h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Order high-quality spices in large quantities at wholesale prices.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
          <div class="product-card bg-white rounded-xl overflow-hidden shadow-md">
            <div class="relative overflow-hidden">
              <a href="bulkproduct.php?id=<?= $row['product_id']; ?>">
                <img src="uploads/<?= $row['product_image']; ?>" 
                    alt="<?= htmlspecialchars($row['product_name']); ?>" 
                    class="w-full h-48 object-cover group-hover-scale-110 transition-transform duration-300"/>
              </a>
              
              <?php if($row['stock_status']=='Out Of Stock'){ ?>
                <span class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded text-xs font-medium">Out of Stock</span>
              <?php } else { ?>
                <span class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded text-xs font-medium">In Stock</span>
              <?php } ?>
              
              <button class="absolute top-3 right-3 bg-white/80 hover:bg-white p-2 rounded transition-colors" 
                      onclick="window.location='wishlist.php?product_id=<?= $row['product_id']; ?>'">
                <i data-lucide="heart" class="h-4 w-4 text-gray-600"></i>
              </button>
            </div>

            <div class="p-4">
              <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                <?= htmlspecialchars($row['product_name']); ?>
              </h3>

              <?php if (!empty($row['quality'])) { ?>
                <p class="text-sm text-gray-700 mb-2">
                    <span class="font-medium">Quality:</span> 
                    <span class="quality-badge"><?= htmlspecialchars($row['quality']); ?></span>
                </p>
              <?php } ?>
              
              <p class="text-sm text-gray-600 mb-2">
                <?= htmlspecialchars(mb_strimwidth($row['product_description'], 0, 60, "...")); ?>
              </p>

              <div class="flex items-center space-x-2 mb-4">
                <?php if(!empty($row['discounted_price']) && $row['discounted_price']<$row['product_price']){ ?>
                  <span class="text-xl font-bold text-orange-600">₹<?= number_format($row['discounted_price'],2); ?></span>
                  <span class="text-sm text-gray-500 line-through">₹<?= number_format($row['product_price'],2); ?></span>
                <?php } else { ?>
                  <span class="text-xl font-bold text-orange-600">₹<?= number_format($row['product_price'],2); ?></span>
                <?php } ?>
              </div>

              <div class="flex space-x-2">
                <?php if($row['stock_status']=='Out Of Stock'){ ?>
                  <button class="flex-1 bg-gray-300 text-gray-600 py-2 px-4 rounded-lg font-medium" disabled>
                    Unavailable
                  </button>
                <?php } else { 
                    // Determine price for cart link
                    $rate = !empty($row['discounted_price']) ? $row['discounted_price'] : $row['product_price'];
                  ?>
                  <a href="addtocart.php?id=<?= $row['product_id']; ?>&rate=<?= $rate; ?>" 
                    class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded-lg font-medium text-center transition-colors">
                    <i data-lucide="shopping-cart" class="h-4 w-4 inline mr-1"></i>Add to Cart
                  </a>
                <?php } ?>
                
                <a href="bulkproduct.php?id=<?= $row['product_id']; ?>" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors">
                  <i data-lucide="eye" class="h-4 w-4"></i>
                </a>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
      
      <?php if(mysqli_num_rows($result) == 0): ?>
          <div class="text-center py-12 bg-gray-100 rounded-xl mt-8">
              <i data-lucide="package-search" class="h-10 w-10 text-gray-500 mx-auto mb-4"></i>
              <h3 class="text-xl font-semibold text-gray-700">No Bulk Products Available</h3>
              <p class="text-gray-500 mt-2">Please check back later or contact us for custom orders.</p>
          </div>
      <?php endif; ?>
      
    </div>
  </section>
</main>

<?php include "footer.php"; ?>
<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
</body>
</html>