<?php
include "../connection.php"; 

// --- 1. INITIAL SETUP AND PRODUCT ID CHECK ---
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Error: Product ID is missing.'); window.location.href='view_products.php';</script>";
    exit;
}

$product_id = intval($_GET['id']);
$message = '';
$message_type = 'success'; // default message type

// --- 2. HANDLE STOCK STATUS UPDATE (Pure Status Toggle Logic) ---
if (isset($_POST['set_stock'])) {
    $action = $_POST['set_stock'];
    $new_quantity = 0;
    $new_status = '';

    if ($action === 'in_stock') {
        // Set to 1 and 'In Stock'
        $new_quantity = 1; 
        $new_status = 'In Stock';
        $message = "Product status updated to In Stock (Quantity set to 1).";
        $message_type = 'info';
    } elseif ($action === 'out_of_stock') {
        // Set to 0 and 'Out of Stock'
        $new_quantity = 0;
        $new_status = 'Out of Stock';
        $message = "Product status updated to Out of Stock (Quantity set to 0).";
        $message_type = 'warning';
    }

    if (!empty($new_status)) {
        $stock_update_sql = "UPDATE product SET 
                             product_quantity = {$new_quantity}, 
                             stock_status = '{$new_status}'
                             WHERE product_id = {$product_id}";
        
        if (mysqli_query($conn, $stock_update_sql)) {
            // Redirect with success message to prevent form resubmission
            echo "<script>window.location.href='edit_products.php?id={$product_id}&status_updated={$new_status}';</script>";
            exit;
        } else {
            $message = "Error updating stock status: " . mysqli_error($conn);
            $message_type = 'danger';
        }
    }
}

// --- 3. HANDLE FULL FORM SUBMISSION (POST) ---
if (isset($_POST['submit_update'])) {
    // Sanitize and escape POST data
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $quantity = intval($_POST['product_quantity']);
    $price = floatval($_POST['product_price']);
    $discounted_price = floatval($_POST['discounted_price']);
    // Stock status determined by the new quantity
    $stock_status = ($quantity > 0) ? 'In Stock' : 'Out of Stock';

    // Get old main image filename
    $old_image_result = mysqli_query($conn, "SELECT product_image FROM product WHERE product_id = {$product_id}");
    $old_main_image = mysqli_fetch_assoc($old_image_result)['product_image'];

    $upload_dir = '../uploads/';
    $main_image_file = $old_main_image;
    $has_error = false;
    
    // Main Image Update Logic
    if (!empty($_FILES['product_image']['name'])) {
        $file_ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = time() . '-' . uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                $main_image_file = $new_filename;
                if (!empty($old_main_image) && file_exists($upload_dir . $old_main_image)) {
                    @unlink($upload_dir . $old_main_image);
                }
            } else {
                $message = "Error uploading main image.";
                $message_type = 'danger';
                $has_error = true;
            }
        } else {
            $message = "Invalid main image file type. Only JPG, PNG, GIF allowed.";
            $message_type = 'danger';
            $has_error = true;
        }
    }

    // UPDATE MAIN PRODUCT TABLE
    if (!$has_error) {
        $update_sql = "UPDATE product SET 
                       product_name = '{$product_name}', 
                       category = '{$category}', 
                       product_description = '{$description}', 
                       product_quantity = {$quantity}, 
                       product_price = {$price}, 
                       discounted_price = {$discounted_price}, 
                       product_image = '{$main_image_file}',
                       stock_status = '{$stock_status}'
                       WHERE product_id = {$product_id}";

        if (mysqli_query($conn, $update_sql)) {
            $message = "Product details updated successfully!";
            $message_type = 'success';
            
            // --- FIXED GALLERY IMAGE UPLOAD ---
            if (isset($_FILES['extra_images']) && is_array($_FILES['extra_images']['name'])) {
                $extra_upload_success_count = 0;
                $extra_upload_error_count = 0;
                $allowed_ext = ['jpg', 'png', 'jpeg', 'gif'];
                
                // Loop through each uploaded file
                for ($i = 0; $i < count($_FILES['extra_images']['name']); $i++) {
                    $file_name = $_FILES['extra_images']['name'][$i];
                    $file_tmp = $_FILES['extra_images']['tmp_name'][$i];
                    $file_error = $_FILES['extra_images']['error'][$i];
                    $file_size = $_FILES['extra_images']['size'][$i];
                    
                    // Skip empty files
                    if (empty($file_name) || $file_error === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }
                    
                    // Check for upload errors
                    if ($file_error !== UPLOAD_ERR_OK) {
                        $extra_upload_error_count++;
                        continue;
                    }
                    
                    // Check file size (optional - 5MB limit)
                    if ($file_size > 5242880) {
                        $extra_upload_error_count++;
                        continue;
                    }
                    
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // Check file type and ensure it's a valid image
                    if (in_array($file_ext, $allowed_ext) && getimagesize($file_tmp)) {
                        
                        $new_extra_filename = time() . '_' . $i . '_' . uniqid() . '.' . $file_ext;
                        $upload_path = $upload_dir . $new_extra_filename;

                        // Attempt to move file
                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            $insert_sql = "INSERT INTO product_images (product_id, image_name) VALUES ({$product_id}, '{$new_extra_filename}')";
                            
                            // Attempt to insert into database
                            if (mysqli_query($conn, $insert_sql)) {
                                $extra_upload_success_count++;
                            } else {
                                @unlink($upload_path); // DB failed, delete file
                                $extra_upload_error_count++;
                            }
                        } else {
                            // Move failed (likely permission issue)
                            $extra_upload_error_count++;
                        }
                    } else {
                        // Invalid file type/format
                        $extra_upload_error_count++;
                    }
                }
                
                if ($extra_upload_success_count > 0) {
                    $message .= " Successfully added {$extra_upload_success_count} gallery image(s).";
                }
                if ($extra_upload_error_count > 0) {
                    $message .= " (Warning: Failed to upload {$extra_upload_error_count} gallery image(s).)";
                    $message_type = 'warning';
                }
            }
            // --- END FIXED GALLERY IMAGE UPLOAD ---
            
             echo "<script>alert('{$message}'); window.location.href='edit_products.php?id={$product_id}';</script>";
             exit;

        } else {
            $message = "Error updating product: " . mysqli_error($conn);
            $message_type = 'danger';
        }
    }
}

// --- 4. HANDLE EXTRA IMAGE DELETION ---
if (isset($_GET['delete_extra_id'])) {
    $extra_image_id = intval($_GET['delete_extra_id']);
    $img_to_delete_qry = mysqli_query($conn, "SELECT image_name FROM product_images WHERE image_id = {$extra_image_id} AND product_id = {$product_id}");
    $img_row = mysqli_fetch_assoc($img_to_delete_qry);
    if ($img_row) {
        mysqli_query($conn, "DELETE FROM product_images WHERE image_id = {$extra_image_id}");
        if (file_exists('../uploads/' . $img_row['image_name'])) {
            @unlink('../uploads/' . $img_row['image_name']);
        }
    }
    echo "<script>window.location.href='edit_products.php?id={$product_id}';</script>";
    exit;
}


// --- 5. FETCH PRODUCT DATA FOR FORM DISPLAY ---
$product_qry = "SELECT * FROM product WHERE product_id = {$product_id}";
$product_result = mysqli_query($conn, $product_qry);

if (mysqli_num_rows($product_result) == 0) {
    echo "<script>alert('Error: Product not found.'); window.location.href='view_products.php';</script>";
    exit;
}

$product_data = mysqli_fetch_assoc($product_result);

// Check for successful status update redirect
if (isset($_GET['status_updated'])) {
    $status = htmlspecialchars($_GET['status_updated']);
    $message_type = ($status == 'In Stock') ? 'success' : 'warning';
    $message = "Stock status successfully updated to {$status}.";
}

// Fetch Extra Images
$extra_images_qry = "SELECT * FROM product_images WHERE product_id = {$product_id}";
$extra_images_result = mysqli_query($conn, $extra_images_qry);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Aroma Hub Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Tailwind Configuration
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
        .form-card {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        .preview-image-md {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .preview-image-sm {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .file-upload-area {
            border: 2px dashed #d1d5db; 
            background-color: #f9fafb; 
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .file-upload-area:hover {
            border-color: #f97316; 
            background-color: rgba(249, 115, 22, 0.05);
        }
        .file-upload-area.dragover {
            border-color: #f97316;
            background-color: rgba(249, 115, 22, 0.1);
        }
        /* Alert Styling */
        .alert-success { background-color: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
        .alert-danger { background-color: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }
        .alert-info { background-color: #e0f2fe; color: #0077c2; border-left: 4px solid #3b82f6; }
        .alert-warning { background-color: #fffbeb; color: #92400e; border-left: 4px solid #f59e0b; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Header -->
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
                    <a href="admin-add-product.php" class="text-gray-600 hover:text-primary transition-colors">Add Product</a>
                    <a href="view_products.php" class="text-primary font-medium">Products</a>
                    <a href="view_giftbox.php" class="text-gray-600 hover:text-primary transition-colors">Gift Boxes</a> 
                </div>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="admin-dashboard.html" class="hover:text-primary transition-colors">Dashboard</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="view_products.php" class="hover:text-primary transition-colors">Products</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Edit Product</span>
            </nav>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-gradient text-white py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-edit text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-1">Edit Product: <?php echo htmlspecialchars($product_data['product_name']); ?></h1>
                    <p class="text-white/90">Manage details and inventory for this product.</p>
                </div>
            </div>
        </div>
    </section>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <?php if (!empty($message)): // Display messages from form submit or status toggle ?>
            <div class="alert-<?php echo $message_type; ?> p-4 mb-6 rounded-lg font-medium text-sm" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Stock Status Toggle -->
        <div class="bg-white rounded-xl p-6 mb-6 shadow-sm flex flex-col sm:flex-row items-center justify-between border border-gray-100">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <i class="fas fa-cubes text-2xl text-primary"></i>
                <p class="text-lg font-medium text-gray-700">
                    Current Stock Status: 
                    <span class="font-bold text-lg <?php echo ($product_data['stock_status'] == 'In Stock') ? 'text-green-600' : 'text-red-600'; ?>">
                        <?php echo htmlspecialchars($product_data['stock_status']); ?>
                    </span>
                    <span class="text-sm text-gray-500 ml-2">(Qty: <?php echo htmlspecialchars($product_data['product_quantity']); ?>)</span>
                </p>
            </div>
            
            <div class="flex space-x-3">
                <form method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <button type="submit" name="set_stock" value="in_stock"
                            title="Set status to In Stock (Quantity will be set to 1)"
                            class="bg-green-500 text-white text-sm py-2 px-3 rounded-xl hover:bg-green-600 transition-colors font-medium disabled:opacity-50"
                            <?php echo ($product_data['stock_status'] == 'In Stock') ? 'disabled' : ''; ?>>
                        <i class="fas fa-check-circle mr-1"></i> Mark as In Stock
                    </button>
                </form>
                <form method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <button type="submit" name="set_stock" value="out_of_stock"
                            title="Set status to Out of Stock (Quantity will be set to 0)"
                            class="bg-red-500 text-white text-sm py-2 px-3 rounded-xl hover:bg-red-600 transition-colors font-medium disabled:opacity-50"
                            <?php echo ($product_data['stock_status'] == 'Out of Stock') ? 'disabled' : ''; ?>>
                        <i class="fas fa-times-circle mr-1"></i> Mark as Out of Stock
                    </button>
                </form>
            </div>
        </div>

        <!-- Edit Form -->
        <form method="POST" enctype="multipart/form-data" class="space-y-8">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

            <!-- Basic Information -->
            <div class="form-card bg-white rounded-2xl p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-primary mr-3"></i>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="product_name" class="block text-sm font-medium text-gray-700 mb-2">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" id="product_name" name="product_name" required
                                value="<?php echo htmlspecialchars($product_data['product_name']); ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                        <select id="category" name="category" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                            <option value="">-- Select Category --</option>
                            <option value="Spices" <?php echo ($product_data['category'] == 'Spices') ? 'selected' : ''; ?>>Whole Spices</option>
                            <option value="Powders" <?php echo ($product_data['category'] == 'Powders') ? 'selected' : ''; ?>>Spice Powders</option>
                            <option value="Blended Spices" <?php echo ($product_data['category'] == 'Blended Spices') ? 'selected' : ''; ?>>Blended Spices / Masalas</option>
                            <option value="Dry Fruits" <?php echo ($product_data['category'] == 'Dry Fruits') ? 'selected' : ''; ?>>Dry Fruits & Nuts</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="product_description" class="block text-sm font-medium text-gray-700 mb-2">Product Description <span class="text-red-500">*</span></label>
                        <textarea id="product_description" name="product_description" rows="4" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"><?php echo htmlspecialchars($product_data['product_description']); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="form-card bg-white rounded-2xl p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-indian-rupee-sign text-primary mr-3"></i>
                    Pricing & Inventory
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="product_price" class="block text-sm font-medium text-gray-700 mb-2">Product Price (₹) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                            <input type="number" id="product_price" name="product_price" step="0.01" min="0" required
                                    value="<?php echo htmlspecialchars($product_data['product_price']); ?>"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                        </div>
                    </div>

                    <div>
                        <label for="discounted_price" class="block text-sm font-medium text-gray-700 mb-2">Discounted Price (₹) (Optional)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                            <input type="number" id="discounted_price" name="discounted_price" step="0.01" min="0"
                                    value="<?php echo htmlspecialchars($product_data['discounted_price']); ?>"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Leave empty or 0 if no discount</p>
                    </div>

                    <div>
                        <label for="product_quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity in Stock <span class="text-red-500">*</span></label>
                        <input type="number" id="product_quantity" name="product_quantity" min="0" required
                                value="<?php echo htmlspecialchars($product_data['product_quantity']); ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                        <p class="text-xs text-gray-500 mt-1">Status below is derived from this quantity.</p>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="form-card bg-white rounded-2xl p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-images text-primary mr-3"></i>
                    Product Images
                </h3>
                
                <!-- Main Image -->
                <div class="mb-8 p-6 border rounded-xl border-gray-200">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Main Product Image <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-start space-x-6 mb-4">
                        <div id="current-main-preview" class="text-center">
                            <img src="../uploads/<?php echo htmlspecialchars($product_data['product_image']); ?>" 
                                 class="preview-image-md mx-auto" alt="Current main product image">
                            <p class="text-xs text-gray-500 mt-1">Current Image</p>
                        </div>
                        <div id="new-main-upload-area" class="flex-1">
                            <label for="product_image" class="block text-sm font-medium text-gray-700 mb-2">Replace Main Image</label>
                            <div class="file-upload-area rounded-xl p-4 text-center" onclick="document.getElementById('product_image').click();">
                                <input type="file" id="product_image" name="product_image" accept="image/*" class="hidden">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-1"></i>
                                <p class="text-sm text-gray-700">Click to upload or drag & drop</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Uploading a new file will replace the current main image. Leave blank to keep it.</p>
                </div>

                <!-- Gallery Images -->
                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-grip-horizontal text-primary mr-2"></i>
                        Gallery Images
                    </h4>

                    <!-- Existing Images -->
                    <div id="existing-extra-images" class="flex flex-wrap gap-4 mb-6 p-4 border rounded-xl bg-gray-50">
                        <?php 
                        if (mysqli_num_rows($extra_images_result) > 0) {
                            while($img = mysqli_fetch_assoc($extra_images_result)) {
                                ?>
                                <div class="relative group">
                                    <img src="../uploads/<?php echo htmlspecialchars($img['image_name']); ?>" 
                                         class="preview-image-sm" alt="Gallery image">
                                    <a href="edit_products.php?id=<?php echo $product_id; ?>&delete_extra_id=<?php echo $img['image_id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this gallery image?')"
                                       title="Delete Image"
                                       class="absolute -top-2 -right-2 bg-secondary text-white w-6 h-6 rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p class='text-gray-500'>No extra images in the gallery yet.</p>";
                        }
                        ?>
                    </div>

                    <!-- Add New Gallery Images -->
                    <label for="extra_images" class="block text-sm font-medium text-gray-700 mb-2">Add New Gallery Images</label>
                    <div id="gallery-upload-area" class="file-upload-area rounded-xl p-6 text-center" onclick="document.getElementById('extra_images').click();">
                        <input type="file" id="extra_images" name="extra_images[]" accept="image/*" multiple class="hidden">
                        <i class="fas fa-plus text-2xl text-gray-400 mb-1"></i>
                        <p class="text-sm text-gray-700">Click to select or drag & drop multiple files</p>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple images</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">These will be added to the existing images.</p>
                    
                    <!-- Preview Selected Files -->
                    <div id="selected-files-preview" class="mt-4 hidden">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Selected Files:</h5>
                        <div id="preview-container" class="flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="form-card bg-white rounded-2xl p-8">
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" name="submit_update" 
                            class="flex-1 bg-gradient-to-r from-primary to-secondary text-white py-4 px-8 rounded-xl hover:shadow-lg transition-all font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Update Product Details & Images
                    </button>
                    <a href="view_products.php" 
                       class="flex-1 border-2 border-gray-300 text-gray-700 py-4 px-8 rounded-xl hover:border-primary hover:text-primary transition-all font-medium text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancel / Back to Products
                    </a>
                </div>
            </div>
        </form>
    </main>
    
    <script>
        // Main image preview for replacement
        document.getElementById('product_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const currentImg = document.getElementById('current-main-preview');
                    currentImg.innerHTML = `
                        <img src="${e.target.result}" class="preview-image-md mx-auto border-4 border-green-500" alt="New product image preview">
                        <p class="text-xs text-green-600 mt-1">New Image Preview</p>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Gallery images preview
        document.getElementById('extra_images').addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.getElementById('preview-container');
            const previewSection = document.getElementById('selected-files-preview');
            
            // Clear previous previews
            previewContainer.innerHTML = '';
            
            if (files.length > 0) {
                previewSection.classList.remove('hidden');
                
                Array.from(files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="w-16 h-16 object-cover rounded border">
                                <span class="absolute -top-2 -right-2 bg-green-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">${index + 1}</span>
                            `;
                            previewContainer.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                previewSection.classList.add('hidden');
            }
        });

        // Drag and drop functionality
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            document.querySelectorAll('.file-upload-area').forEach(area => {
                area.addEventListener(eventName, preventDefaults, false);
            });
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            document.querySelectorAll('.file-upload-area').forEach(area => {
                area.addEventListener(eventName, highlight, false);
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            document.querySelectorAll('.file-upload-area').forEach(area => {
                area.addEventListener(eventName, unhighlight, false);
            });
        });

        function highlight(e) {
            e.currentTarget.classList.add('dragover');
        }

        function unhighlight(e) {
            e.currentTarget.classList.remove('dragover');
        }

        // Handle dropped files
        document.getElementById('gallery-upload-area').addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('extra_images').files = files;
            document.getElementById('extra_images').dispatchEvent(new Event('change'));
        });

        document.getElementById('new-main-upload-area').addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                document.getElementById('product_image').files = files;
                document.getElementById('product_image').dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>