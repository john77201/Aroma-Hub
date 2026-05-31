<?php
include "header.php"; // Include your header (which likely contains the top part of the HTML body/dashboard structure)
include "../connection.php";

// --- 1. HANDLE DELETE ACTION ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    // Sanitize input
    $product_id_to_delete = intval($_GET['id']);
    
    // It's critical to delete associated images first to avoid orphans
    // NOTE: In a robust application, you should also fetch and delete the image files from the disk here.
    mysqli_query($conn, "DELETE FROM product_images WHERE product_id = {$product_id_to_delete}");
    
    // 2. Delete the product itself
    // Crucially, we check for 'Bulk' category before deleting for safety, 
    // although the primary intent is product deletion regardless of category.
    $delete_sql = "DELETE FROM product WHERE product_id = {$product_id_to_delete} AND category='Bulk'";
    
    if (mysqli_query($conn, $delete_sql)) {
        echo "<script>alert('Bulk Product deleted successfully!'); window.location.href='view_bulk.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting product: " . mysqli_error($conn) . "');</script>";
    }
}

// --- 2. FETCH ONLY BULK PRODUCTS ---
$products_qry = "SELECT * FROM product WHERE category='Bulk' ORDER BY product_id DESC";
$products_result = mysqli_query($conn, $products_qry);
$total_products = mysqli_num_rows($products_result);

?>

<script src="https://cdn.tailwindcss.com"></script>
<script>
    // Re-include Tailwind Config if your header doesn't already do this
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#f97316', // Orange
                    secondary: '#dc2626', // Red
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
<section class="hero-gradient text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <i class="fas fa-cubes text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2">Bulk Products Inventory</h1>
                <p class="text-white/90">View, edit, and manage all your Bulk category products</p>
            </div>
        </div>
    </div>
</section>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Total Bulk Products: <?php echo $total_products; ?></h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productid</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quality</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Main Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extra Images</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price (₹)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($total_products > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($products_result)): 
                            $original_price = number_format($row['product_price'], 2);
                            $discount_price = (float)$row['discounted_price'];
                            $stock = (int)$row['product_quantity']; // Assuming product_quantity holds the stock level

                            // --- Price Display Logic ---
                            if ($discount_price > 0 && $discount_price < $row['product_price']) {
                                $display_price_html = "
                                    <div class='flex flex-col'>
                                        <span class='line-through text-gray-500 text-xs'>₹{$original_price}</span> 
                                        <span class='text-secondary font-bold text-sm'>₹" . number_format($discount_price, 2) . "</span>
                                    </div>
                                ";
                                $price_for_sort = $discount_price;
                            } else {
                                $display_price_html = "<span class='text-green-600 font-bold text-sm'>₹{$original_price}</span>";
                                $price_for_sort = $row['product_price'];
                            }

                            // --- Stock Status Logic ---
                            $stock_html = '';
                            if ($stock > 0) {
                                $stock_html = "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'><i class='fas fa-check-circle mr-1'></i> In Stock ({$stock})</span>";
                            } else {
                                $stock_html = "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800'><i class='fas fa-times-circle mr-1'></i> Out of Stock</span>";
                            }
                        ?>
                            <tr>
                                <td class="px-3 py-4 whitespace-nowrap text-xs text-gray-500"><?= $row['product_id']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($row['product_name']); ?>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs overflow-hidden text-ellipsis">
                                    <?php echo htmlspecialchars(substr($row['product_description'], 0, 50)) . (strlen($row['product_description']) > 50 ? '...' : ''); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <?php echo htmlspecialchars($row['quality']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img class="h-10 w-10 rounded-lg object-cover" 
                                            src="../uploads/<?php echo htmlspecialchars($row['product_image']); ?>" 
                                            alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap flex items-center space-x-1">
                                    <?php
                                    $pid = $row['product_id'];
                                    // Limiting to 3 small images for the table view
                                    $extra = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id='$pid' LIMIT 3"); 
                                    if(mysqli_num_rows($extra) > 0){
                                        while($img = mysqli_fetch_assoc($extra)){
                                            echo "<img src='../uploads/".htmlspecialchars($img['image_name'])."' class='w-8 h-8 object-cover rounded border border-gray-200' title='Extra Image'>";
                                        }
                                    } else {
                                        echo "<span class='text-xs text-gray-400'>No extra</span>";
                                    }
                                    ?>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm" data-sort-value="<?php echo $price_for_sort; ?>">
                                    <?php echo $display_price_html; ?>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php echo $stock_html; ?>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="edit_bulk.php?product_id=<?php echo $row['product_id']; ?>" 
                                       class="text-indigo-600 hover:text-indigo-900 transition-colors p-2 rounded-lg hover:bg-indigo-50"
                                       title="Edit Product">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <a href="view_bulk.php?action=delete&id=<?php echo $row['product_id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this Bulk product?')"
                                       class="text-red-600 hover:text-red-900 transition-colors p-2 rounded-lg hover:bg-red-50"
                                       title="Delete Product">
                                        <i class="fas fa-trash-alt mr-1"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">No Bulk products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php
include "footer.php";
?>