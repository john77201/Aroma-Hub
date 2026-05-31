<?php
// Note: Ensure this path is correct for your admin structure
include "../connection.php"; 

// Initialize a message variable for success or error feedback
$msg = "";

// --- 1. HANDLE FORM SUBMISSION ---
if (isset($_POST['submit'])) {
    
    // 1. Get and Sanitize Form Data
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category = 'Custom'; // Fixed category for this page
    $product_price = floatval($_POST['product_price']); // Price per gram
    $discounted_price = floatval($_POST['discounted_price']); // Discounted price per gram
    $product_quantity = intval($_POST['product_quantity']); // Stock quantity in grams
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    
    // Validation: Check if prices are valid
    if ($product_price <= 0) {
        $msg = "Product price must be greater than zero.";
    } 
    // Validation: Check if discounted price is less than original price
    else if ($discounted_price > $product_price) {
        $msg = "Discounted price cannot be higher than the original price.";
    } else {
        // --- 2. Handle File Upload (Main Image) ---
        $target_dir = "../uploads/";
        $original_file_name = basename($_FILES["product_image"]["name"]);
        
        // Create a unique filename to prevent overwriting
        $file_extension = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));
        $new_file_name = uniqid('custom_', true) . '.' . $file_extension;
        $target_file = $target_dir . $new_file_name;
        $uploadOk = 1;

        // Check if file is an actual image
        $check = @getimagesize($_FILES["product_image"]["tmp_name"]);
        if ($check === false) {
            $msg .= "File is not a valid image.<br>";
            $uploadOk = 0;
        }

        // Check file size (limit to 5MB)
        if ($_FILES["product_image"]["size"] > 5000000) {
            $msg .= "Sorry, your file is too large (max 5MB).<br>";
            $uploadOk = 0;
        }

        // Allow only JPG, JPEG, PNG
        if ($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg") {
            $msg .= "Sorry, only JPG, JPEG, & PNG files are allowed.<br>";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            $msg = "Failed to upload image. " . $msg;
        } else {
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                
                // --- 3. Insert Product Data into Database ---
                $insert_sql = "INSERT INTO product (product_name, category, product_price, discounted_price, product_quantity, product_image, product_description) 
                               VALUES ('{$product_name}', '{$category}', '{$product_price}', '{$discounted_price}', '{$product_quantity}', '{$new_file_name}', '{$product_description}')";

                if (mysqli_query($conn, $insert_sql)) {
                    
                    // --- SUCCESS REDIRECT (This is near line 66 and is CORRECTLY TERMINATED) ---
                    // Line 66 (or nearby) is fixed here by ensuring the statement is complete.
                    echo "<script>alert('Custom Spice added successfully!'); window.location.href='view_custom.php';</script>";
                    exit;
                } else {
                    $msg = "Database Error: " . mysqli_error($conn);
                }
            } else {
                $msg = "Sorry, there was an error uploading the main image.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Custom Spice - Aroma Hub Admin</title>
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
                    <a href="add_product.php" class="text-gray-600 hover:text-primary transition-colors">Add Product</a>
                    <a href="view_products.php" class="text-gray-600 hover:text-primary transition-colors">Products</a>
                    <a href="view_giftbox.php" class="text-gray-600 hover:text-primary transition-colors">Gift Boxes</a> 
                    <a href="view_custom.php" class="text-gray-600 hover:text-primary transition-colors">Custom Spices</a> 
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
            <h1 class="text-3xl font-bold mb-2">Add New Custom Spice</h1>
            <p class="text-white/90">Define a spice that customers can purchase by weight (per gram).</p>
        </div>
    </section>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-xl rounded-2xl p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">Spice Details</h2>

            <?php if (!empty($msg)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?php echo $msg; ?></span>
                </div>
            <?php endif; ?>

            <form action="add_custom.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                
                <div>
                    <label for="product_name" class="block text-sm font-medium text-gray-700">Spice Name</label>
                    <input type="text" name="product_name" id="product_name" required 
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <input type="text" value="Custom (Sold Per Gram)" readonly 
                           class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="product_price" class="block text-sm font-medium text-gray-700">Price (₹ / gram)</label>
                        <input type="number" name="product_price" id="product_price" step="0.01" min="0.01" required 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary" 
                               placeholder="e.g., 5.00">
                    </div>

                    <div>
                        <label for="discounted_price" class="block text-sm font-medium text-gray-700">Discounted Price (₹ / gram) <span class="text-gray-500 text-xs">(Optional)</span></label>
                        <input type="number" name="discounted_price" id="discounted_price" step="0.01" min="0" 
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary" 
                               placeholder="e.g., 4.50">
                    </div>
                </div>

                <div>
                    <label for="product_quantity" class="block text-sm font-medium text-gray-700">Stock Quantity (in grams)</label>
                    <input type="number" name="product_quantity" id="product_quantity" min="1" required 
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary" 
                           placeholder="e.g., 1000 (for 1 kg)">
                </div>

                <div>
                    <label for="product_description" class="block text-sm font-medium text-gray-700">Spice Description</label>
                    <textarea name="product_description" id="product_description" rows="4" required 
                              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                </div>

                <div>
                    <label for="product_image" class="block text-sm font-medium text-gray-700">Main Product Image</label>
                    <input type="file" name="product_image" id="product_image" required 
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    <p class="mt-2 text-xs text-gray-500">JPG, JPEG, or PNG only. Max 5MB.</p>
                </div>

                <div>
                    <button type="submit" name="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-primary hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        <i class="fas fa-save mr-2 mt-1"></i> Save Custom Spice
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
<?php
// Assuming 'footer.php' exists
// include 'footer.php'; 
?>