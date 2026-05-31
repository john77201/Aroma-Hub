<?php
session_start();
include "connection.php";

// === Wishlist Count ===
$wishlist_count = 0;
if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];
    $wc = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM wishlist WHERE user_id='$userid'");
    if ($wc) {
        $wc_row = mysqli_fetch_assoc($wc);
        $wishlist_count = $wc_row['cnt'];
    }
}

// === Cart Count + Total ===
$cart_count = 0;
$cart_total = 0;
if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];
    $qry = "SELECT COUNT(c.cart_id) AS no, 
                   SUM(
                       (CASE 
                            WHEN p.discounted_price IS NOT NULL AND p.discounted_price > 0 
                            THEN p.discounted_price 
                            ELSE p.product_price 
                        END) * c.quantity
                   ) AS total
            FROM cart c
            INNER JOIN product p ON c.product_id = p.product_id
            WHERE c.userid='$userid' AND c.status=0";
    $res = mysqli_query($conn, $qry);
    if ($res) {
        $data = mysqli_fetch_assoc($res);
        $cart_count = $data['no'] ?? 0;
        $cart_total = $data['total'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aroma Hub - Premium Spices & Seasonings</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <style>
    .dropdown-content { display: none; }
    .dropdown:hover .dropdown-content { display: block; }
  </style>
</head>
<body class="min-h-screen bg-white">
<header class="bg-white shadow-sm border-b border-gray-200">

  <!-- 🔹 Top Bar with Login/Register or User Menu -->
  <div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-10 text-sm text-gray-600">
      <!-- Phone -->
      <div>
        📞 <span class="font-medium">Telephone Enquiry:</span>
        <a href="tel:+916282386042" class="hover:text-orange-600">+91 6282386042</a>
      </div>
      
      <!-- User Login/Register -->
      <div class="relative group">
        <?php if (isset($_SESSION['userid'])) { ?>
          <button class="flex items-center space-x-1 hover:text-orange-600">
            <i data-lucide="user" class="h-4 w-4"></i>
            <span><?= htmlspecialchars($_SESSION['uname']); ?></span>
            <i data-lucide="chevron-down" class="h-3 w-3"></i>
          </button>
            <div class="absolute right-0 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible 
                group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
        <ul class="py-2 text-sm text-gray-700">
            <li><a href="myorders.php" class="block px-4 py-2 hover:bg-gray-100">My Orders</a></li>
            <li><a href="shoppingcart.php" class="block px-4 py-2 hover:bg-gray-100">Cart</a></li>
            <li><a href="checkout.php" class="block px-4 py-2 hover:bg-gray-100">Checkout</a></li>
            <li><a href="logout.php" class="block px-4 py-2 hover:bg-gray-100">Log out</a></li>
        </ul>
    </div>
        <?php } else { ?>
          <button class="flex items-center space-x-1 hover:text-orange-600">
            <i data-lucide="user" class="h-4 w-4"></i>
            <span>Guest</span>
            <i data-lucide="chevron-down" class="h-3 w-3"></i>
          </button>
          <div class="absolute right-0 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-lg 
                  opacity-0 invisible group-hover:opacity-100 group-hover:visible 
                  transition-all duration-200 z-50">
        <ul class="py-2 text-sm text-gray-700">
          <li><a href="register.php" class="block px-4 py-2 hover:bg-gray-100">Register</a></li>
          <li><a href="login.php" class="block px-4 py-2 hover:bg-gray-100">Login</a></li>
          <li><a href="admin/adminlogin.php" class="block px-4 py-2 hover:bg-gray-100">Admin Login</a></li>
        </ul>
      </div>
        <?php } ?>
      </div>
    </div>
  </div>

  <!-- 🔹 Main Header with Logo, Search, Wishlist, Cart -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <!-- Logo -->
      <div class="flex-shrink-0">
        <a href="index.php" class="text-2xl font-bold">
          <span class="text-orange-600">Aroma</span> Hub
        </a>
      </div>

      <!-- Search -->
      <div class="flex-1 max-w-2xl mx-8">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
          </div>
          <input type="text" id="searchInput" name="search"
                 placeholder="Search for spices, herbs, and more..."
                 autocomplete="off"
                 class="pl-10 pr-4 w-full bg-gray-50 border border-gray-200 rounded-full py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
          <ul id="searchSuggestions"
              class="absolute top-full left-0 w-full bg-white border border-gray-200 rounded-md mt-1 shadow-lg hidden max-h-64 overflow-y-auto z-50"></ul>
        </div>
      </div>

      <!-- Wishlist & Cart -->
      <div class="flex items-center space-x-4">
        <a href="wishlist.php" class="relative p-2 text-gray-700 hover:text-red-600 transition-colors">
          <i data-lucide="heart" class="h-5 w-5"></i>
          <?php if ($wishlist_count > 0): ?>
            <span class="absolute -top-2 -right-2 h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">
              <?= $wishlist_count ?>
            </span>
          <?php endif; ?>
        </a>
        <a href="shoppingcart.php" class="relative p-2 text-gray-700 hover:text-orange-600 transition-colors">
          <i data-lucide="shopping-cart" class="h-5 w-5"></i>
          <?php if ($cart_count > 0): ?>
            <span class="absolute -top-2 -right-2 h-5 w-5 rounded-full bg-orange-500 text-white text-xs flex items-center justify-center">
              <?= $cart_count ?>
            </span>
          <?php endif; ?>
        </a>
      </div>
    </div>
  </div>

  <!-- 🔹 Navigation -->
  <nav class="bg-gradient-to-r from-orange-600 to-red-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-center h-14">
        <div class="flex space-x-8">
          <a href="index.php" class="flex items-center space-x-1 px-3 py-2 text-white hover:bg-white/20 rounded transition-colors">
            <i data-lucide="home" class="h-4 w-4"></i>
            <span>Home</span>
          </a>
          <div class="dropdown relative">
            <button class="flex items-center space-x-1 px-3 py-2 text-white hover:bg-white/20 rounded transition-colors">
              <i data-lucide="store" class="h-4 w-4"></i>
              <span>Shop</span>
              <i data-lucide="chevron-down" class="h-4 w-4"></i>
            </button>
            <div class="dropdown-content absolute top-full left-0 mt-1 w-56 bg-white rounded-md shadow-lg py-1 z-50">
              <a href="products.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">All Categories</a>
              <a href="products.php?category=Spices" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Whole Spices</a>
              <a href="products.php?category=Powders" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Spice Powders</a>
              <a href="products.php?category=Blended Spices" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Blended Spices</a>
              <a href="products.php?category=Dry Fruits" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Dry Fruits &amp; Nuts</a>
            </div>
          </div>
          <a href="giftbox.php" class="flex items-center space-x-1 px-3 py-2 text-white hover:bg-white/20 rounded transition-colors">
            <i data-lucide="gift" class="h-4 w-4"></i>
            <span>Gift Box</span>
          </a>
          <a href="bulkorders.php" class="flex items-center space-x-1 px-3 py-2 text-white hover:bg-white/20 rounded transition-colors">
            <i data-lucide="package" class="h-4 w-4"></i>
            <span>Bulk Orders</span>
          </a>
          <a href="blogs.php" class="flex items-center space-x-1 px-3 py-2 text-white hover:bg-white/20 rounded transition-colors">
    <i data-lucide="file-text" class="h-4 w-4"></i>
    <span>Blogs</span>
</a>
          <a href="contact.php" class="flex items-center space-x-1 px-3 py-2 text-white hover:bg-white/20 rounded transition-colors">
            <i data-lucide="phone" class="h-4 w-4"></i>
            <span>Contact Us</span>
          </a>
        </div>
      </div>
    </div>
  </nav>
</header>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
  let debounceTimer;
  $('#searchInput').on('keyup', function(){
    clearTimeout(debounceTimer);
    let query = $(this).val().trim();
    if(query.length === 0){ $('#searchSuggestions').fadeOut(); return; }
    debounceTimer = setTimeout(function(){
      $.ajax({
        url: 'search_suggestions.php',
        type: 'POST',
        data: { query: query },
        success: function(data){
          $('#searchSuggestions').html(data).fadeIn();
        }
      });
    }, 300);
  });
  $(document).on('click', '.suggestion-item', function(){
    let id = $(this).data('id');
    window.location.href = 'singleproduct.php?id=' + id;
  });
  $(document).click(function(e){
    if(!$(e.target).closest('#searchInput').length){
      $('#searchSuggestions').fadeOut();
    }
  });
});
lucide.createIcons();
</script>
</body>
</html>
