<?php
// Note: Ensure this path is correct for your admin structure
include "../connection.php"; 

// --- 1. HANDLE DELETE ACTION ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    // Sanitize input
    $product_id_to_delete = intval($_GET['id']);
    
    // 1. (Optional) Fetch the main image name to delete the file from disk (recommended for robustness)
    // $image_result = mysqli_query($conn, "SELECT product_image FROM product WHERE product_id = {$product_id_to_delete}");
    // if ($image_row = mysqli_fetch_assoc($image_result)) {
    //     $file_path = "../uploads/" . $image_row['product_image'];
    //     if (file_exists($file_path)) {
    //         unlink($file_path); // Deletes the physical file
    //     }
    // }

    // 2. Delete the product itself (no associated 'product_images' to delete for 'Custom' products as per add_custom.php)
    $delete_sql = "DELETE FROM product WHERE product_id = {$product_id_to_delete}";
    if (mysqli_query($conn, $delete_sql)) {
        // Redirect back to the Custom Spice view
        echo "<script>alert('Custom Spice deleted successfully!'); window.location.href='view_custom.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting Custom Spice: " . mysqli_error($conn) . "');</script>";
    }
}

// --- 2. FETCH ALL CUSTOM SPICES (Products with category 'Custom') ---
// Filtering the product table to show only items with category = 'Custom'
$products_qry = "SELECT product_id, product_name, category, product_price, discounted_price, product_quantity, product_image 
                 FROM product 
                 WHERE category = 'Custom' 
                 ORDER BY product_id DESC";
$products_result = mysqli_query($conn, $products_qry);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Custom Spices - Aroma Hub Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Tailwind Configuration for custom colors
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316', // Orange
                        secondary: '#dc2626', // Red (for discounts/danger)
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #f97316 0%, #dc2626 50%, #b91c1c 100%);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 hero-gradient rounded-xl flex items-center justify-center">
                        <i class="fas fa-seedling text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Aroma Hub</h1>
                        <p class="text-xs text-gray-500">Admin Dashboard</p>
                    </div>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="admin-dashboard.html" class="text-gray-600 hover:text-primary transition-colors">Dashboard</a>
                    <a href="add_custom.php" class="text-gray-600 hover:text-primary transition-colors">Add Custom Spice</a>
                    <a href="view_custom.php" class="text-primary font-medium">Custom Spices</a> 
                    <a href="view_giftbox.php" class="text-gray-600 hover:text-primary transition-colors">Gift Boxes</a>
                    <a href="#" class="text-gray-600 hover:text-primary transition-colors">Orders</a>
                    <a href="#" class="text-gray-600 hover:text-primary transition-colors">Analytics</a>
                </div>
                <div class="flex items-center space-x-4">
                </div>
            </div>
        </div>
    </header>
    
    <section class="hero-gradient text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-seedling text-white text-xl"></i> </div>
                <div>
                    <h1 class="text-3xl font-bold mb-2">Custom Spice Inventory</h1>
                    <p class="text-white/90">View, edit, and manage all spices available for customization</p>
                </div>
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-lg rounded-2xl p-6">
            <div class="flex justify-between items-center mb-4">
                 <h2 class="text-xl font-semibold text-gray-800">Total Custom Spices: <?php echo mysqli_num_rows($products_result); ?></h2>
                 <a href="add_custom.php" class="flex items-center bg-primary text-white py-2 px-4 rounded-xl hover:bg-red-600 transition-colors text-sm font-medium">
                     <i class="fas fa-plus-circle mr-2"></i> Add New Custom Spice
                 </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spice Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price (₹/gm)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock (Grams)</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (mysqli_num_rows($products_result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($products_result)): 
                                $original_price = number_format($row['product_price'], 2);
                                $discount_price = (float)$row['discounted_price'];
                                $stock = (int)$row['product_quantity'];

                                // --- Price Display Logic (Modified for Per/Gm) ---
                                if ($discount_price > 0 && $discount_price < $row['product_price']) {
                                    $display_price_html = "
                                        <div class='flex flex-col'>
                                            <span class='line-through text-gray-500 text-xs'>₹{$original_price}/gm</span> 
                                            <span class='text-secondary font-bold text-base'>₹" . number_format($discount_price, 2) . "/gm</span>
                                        </div>
                                    ";
                                    $price_for_sort = $discount_price;
                                } else {
                                    $display_price_html = "<span class='text-green-600 font-bold text-base'>₹{$original_price}/gm</span>";
                                    $price_for_sort = $row['product_price'];
                                }

                                // --- Stock Status Logic (unchanged, still shows quantity) ---
                                $stock_html = '';
                                if ($stock > 0) {
                                    $stock_html = "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800'><i class='fas fa-check-circle mr-1'></i> In Stock ({$stock} gm)</span>";
                                } else {
                                    $stock_html = "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800'><i class='fas fa-times-circle mr-1'></i> Out of Stock</span>";
                                }
                            ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img class="h-10 w-10 rounded-lg object-cover" 
                                              src="../uploads/<?php echo htmlspecialchars($row['product_image']); ?>" 
                                              alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($row['product_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                            <?php echo htmlspecialchars($row['category']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" data-sort-value="<?php echo $price_for_sort; ?>">
                                        <?php echo $display_price_html; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php echo $stock_html; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="edit_custom.php?id=<?php echo $row['product_id']; ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 transition-colors p-2 rounded-lg hover:bg-indigo-50">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <a href="view_custom.php?action=delete&id=<?php echo $row['product_id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this Custom Spice?')"
                                           class="text-red-600 hover:text-red-900 transition-colors p-2 rounded-lg hover:bg-red-50">
                                            <i class="fas fa-trash-alt mr-1"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No Custom Spices found. Please ensure you have added products with the category 'Custom'.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    </body>
</html>
<?php
// Assuming 'footer.php' exists
// include 'footer.php'; 
?>