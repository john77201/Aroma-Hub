<?php
include "../connection.php"; 
// Assuming 'connection.php' establishes $conn

// --- 1. INITIAL SETUP AND PRODUCT ID CHECK ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect if ID is missing or invalid
    echo "<script>alert('Error: Invalid Gift Box ID.'); window.location.href='view_giftbox.php';</script>";
    exit();
}

$product_id = intval($_GET['id']);
$message = '';
$message_type = 'success'; // default message type

$CATEGORY_NAME = 'Giftbox'; 
$upload_dir = '../uploads/';

// Create uploads directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
    chmod($upload_dir, 0777);
}


// --- 2. HANDLE FORM SUBMISSION (POST Request) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_update'])) {
    
    // Sanitize and collect form data
    $productname = mysqli_real_escape_string($conn, $_POST['productname']);
    $price = floatval($_POST['productprice']);
    $discount_price = isset($_POST['discountedprice']) ? floatval($_POST['discountedprice']) : 0;
    $quantity = intval($_POST['productquantity']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Determine stock status based on quantity
    $stock_status = ($quantity > 0) ? 'In Stock' : 'Out of Stock';

    // Fetch current image name for deletion/fallback
    $current_data_qry = mysqli_query($conn, "SELECT product_image FROM product WHERE product_id = {$product_id}");
    $current_data = mysqli_fetch_assoc($current_data_qry);
    $old_main_image = $current_data['product_image'];
    $new_image_filename = $old_main_image;
    $update_main_image = false;

    $has_error = false;
    $image_upload_messages = [];

    // Handle MAIN IMAGE upload - ALL FILE TYPES SUPPORTED
    if (isset($_FILES['productimage']) && $_FILES['productimage']['error'] == UPLOAD_ERR_OK) {
        $file_ext = strtolower(pathinfo($_FILES['productimage']['name'], PATHINFO_EXTENSION));
        $new_image_filename = time() . '_' . uniqid() . '.' . $file_ext;
        $target_file = $upload_dir . $new_image_filename;

        // Check file size (10MB limit for safety)
        if ($_FILES['productimage']['size'] <= 10485760) {
            if (move_uploaded_file($_FILES["productimage"]["tmp_name"], $target_file)) {
                @chmod($target_file, 0644);
                $update_main_image = true;
                // Delete old image file 
                if (!empty($old_main_image) && file_exists($upload_dir . $old_main_image)) {
                    @unlink($upload_dir . $old_main_image);
                }
            } else {
                $image_upload_messages[] = "Error uploading new main file.";
                $has_error = true;
            }
        } else {
            $image_upload_messages[] = "Main file size exceeds 10MB limit.";
            $has_error = true;
        }
    }
    
    // Prepare SQL update statement
    if (!$has_error) {
        $sql = "UPDATE product SET
                    product_name = '{$productname}',
                    product_price = {$price},
                    discounted_price = {$discount_price},
                    product_quantity = {$quantity},
                    product_description = '{$description}',
                    category = '{$CATEGORY_NAME}', 
                    stock_status = '{$stock_status}'"
                    . ($update_main_image ? ", product_image = '{$new_image_filename}'" : "") .
               " WHERE product_id = {$product_id}";

        if (mysqli_query($conn, $sql)) {
            $message = "Gift Box details updated successfully!";
            $message_type = 'success';

            // --- IMPROVED GALLERY FILE UPLOAD - ALL FILE TYPES SUPPORTED ---
            if (isset($_FILES['extra_images'])) {
                $extra_upload_success_count = 0;
                $extra_upload_error_count = 0;
                $extra_error_details = [];
                
                $total_files = count($_FILES['extra_images']['name']);
                
                // Loop through each uploaded file
                for ($i = 0; $i < $total_files; $i++) {
                    // Get file details
                    $file_name = $_FILES['extra_images']['name'][$i];
                    $file_tmp = $_FILES['extra_images']['tmp_name'][$i];
                    $file_error = $_FILES['extra_images']['error'][$i];
                    $file_size = $_FILES['extra_images']['size'][$i];
                    
                    // Skip if no file was uploaded in this slot
                    if (empty($file_name) || $file_error === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }
                    
                    // Check for upload errors
                    if ($file_error !== UPLOAD_ERR_OK) {
                        $extra_upload_error_count++;
                        $error_msg = "File upload error code: " . $file_error;
                        switch($file_error) {
                            case UPLOAD_ERR_INI_SIZE:
                            case UPLOAD_ERR_FORM_SIZE:
                                $error_msg = "File too large";
                                break;
                            case UPLOAD_ERR_PARTIAL:
                                $error_msg = "File partially uploaded";
                                break;
                            default:
                                $error_msg = "Upload error occurred";
                        }
                        $extra_error_details[] = htmlspecialchars($file_name) . ": " . $error_msg;
                        continue;
                    }
                    
                    // Check file size (10MB limit for safety)
                    if ($file_size > 10485760) {
                        $extra_upload_error_count++;
                        $extra_error_details[] = htmlspecialchars($file_name) . ": File size exceeds 10MB limit";
                        continue;
                    }
                    
                    // Check if file_tmp exists and is readable
                    if (!file_exists($file_tmp) || !is_readable($file_tmp)) {
                        $extra_upload_error_count++;
                        $extra_error_details[] = htmlspecialchars($file_name) . ": Temporary file not accessible";
                        continue;
                    }
                    
                    // Get file extension (allow all extensions)
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // Sanitize filename to prevent security issues
                    $safe_original_name = preg_replace('/[^a-zA-Z0-9_.-]/', '_', basename($file_name));
                    
                    // Generate unique filename
                    $new_extra_filename = time() . '_' . $i . '_' . uniqid() . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_extra_filename;
                    
                    // Check if upload directory is writable
                    if (!is_writable($upload_dir)) {
                        $extra_upload_error_count++;
                        $extra_error_details[] = htmlspecialchars($file_name) . ": Upload directory not writable";
                        // Try to fix permissions
                        @chmod($upload_dir, 0777);
                        continue;
                    }

                    // Attempt to move file
                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        // Set proper permissions on uploaded file
                        @chmod($upload_path, 0644);
                        
                        // Escape filename for SQL
                        $safe_filename = mysqli_real_escape_string($conn, $new_extra_filename);
                        $insert_sql = "INSERT INTO product_images (product_id, image_name) VALUES ({$product_id}, '{$safe_filename}')";
                        
                        // Attempt to insert into database
                        if (mysqli_query($conn, $insert_sql)) {
                            $extra_upload_success_count++;
                        } else {
                            // DB insert failed, delete the uploaded file
                            @unlink($upload_path);
                            $extra_upload_error_count++;
                            $extra_error_details[] = htmlspecialchars($file_name) . ": Database insert failed - " . mysqli_error($conn);
                        }
                    } else {
                        // Move failed (likely permission issue)
                        $extra_upload_error_count++;
                        $extra_error_details[] = htmlspecialchars($file_name) . ": Failed to move uploaded file (check permissions)";
                    }
                }
                
                // Build detailed message
                if ($extra_upload_success_count > 0) {
                    $message .= " Successfully added {$extra_upload_success_count} gallery image(s).";
                }
                if ($extra_upload_error_count > 0) {
                    $message .= " Failed to upload {$extra_upload_error_count} gallery image(s).";
                    if (!empty($extra_error_details)) {
                        $message .= "<br><small>Details: " . implode('; ', $extra_error_details) . "</small>";
                    }
                    $message_type = 'warning';
                }
            }
            // --- END IMPROVED GALLERY IMAGE UPLOAD ---

            echo "<script>alert('" . str_replace("'", "\\'", strip_tags($message)) . "'); window.location.href='edit_giftbox.php?id={$product_id}';</script>";
            exit();

        } else {
            // Failed update
            $message = "Error updating Gift Box: " . mysqli_error($conn);
            $message_type = 'danger';
        }
    } else {
        // If there was an image error before DB update
        $message = "Form submission failed due to main image error(s): " . implode(' ', $image_upload_messages);
        $message_type = 'danger';
    }
}


// --- 3. HANDLE EXTRA IMAGE DELETION ---
if (isset($_GET['delete_extra_id'])) {
    $extra_image_id = intval($_GET['delete_extra_id']);
    $img_to_delete_qry = mysqli_query($conn, "SELECT image_name FROM product_images WHERE image_id = {$extra_image_id} AND product_id = {$product_id}");
    $img_row = mysqli_fetch_assoc($img_to_delete_qry);
    
    if ($img_row) {
        if (mysqli_query($conn, "DELETE FROM product_images WHERE image_id = {$extra_image_id}")) {
            if (file_exists('../uploads/' . $img_row['image_name'])) {
                @unlink('../uploads/' . $img_row['image_name']);
            }
        }
    }
    echo "<script>window.location.href='edit_giftbox.php?id={$product_id}';</script>";
    exit;
}


// --- 4. FETCH PRODUCT DATA FOR FORM DISPLAY ---
$fetch_query = "SELECT * FROM product WHERE product_id = {$product_id} AND (category = '{$CATEGORY_NAME}' OR category = 'Giftbox')"; 
$result = mysqli_query($conn, $fetch_query);

if (mysqli_num_rows($result) === 0) {
    echo "<script>alert('Gift Box not found or invalid.'); window.location.href='view_giftbox.php';</script>";
    exit();
}

$gift_box_data = mysqli_fetch_assoc($result);

// Fetch additional gallery images
$extra_images_qry = "SELECT * FROM product_images WHERE product_id = {$product_id}";
$extra_images_result = mysqli_query($conn, $extra_images_qry);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gift Box - Aroma Hub Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316', 
                        secondary: '#dc2626',
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
        textarea.resize-none {
            resize: vertical; 
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
        .alert-warning { background-color: #fef3c7; color: #92400e; border-left: 4px solid #f59e0b; }
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
                <a href="view_giftbox.php" class="text-primary font-medium">Gift Boxes</a> 
            </div>
        </div>
    </div>
</header>
    
<section class="hero-gradient text-white py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <i class="fas fa-box text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-1">Edit Gift Box: <?php echo htmlspecialchars($gift_box_data['product_name']); ?></h1>
                <p class="text-white/90">Update the special details and contents of this Gift Box.</p>
            </div>
        </div>
    </div>
</section>

<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <?php if (!empty($message)): // Display messages from form submit ?>
        <div class="alert-<?php echo $message_type; ?> p-4 mb-6 rounded-lg font-medium text-sm" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-8">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

        <!-- Basic Information -->
        <div class="form-card bg-white rounded-2xl p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-info-circle text-primary mr-3"></i>
                Basic Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                <div>
                    <label for="productname" class="block text-sm font-medium text-gray-700 mb-2">
                        Gift Box Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="productname" name="productname" required
                            value="<?php echo htmlspecialchars($gift_box_data['product_name']); ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                            placeholder="Enter Gift Box Name">
                </div>

                <div>
                    <label for="productquantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity in Stock <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="productquantity" name="productquantity" required min="0"
                            value="<?php echo htmlspecialchars($gift_box_data['product_quantity']); ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                            placeholder="e.g., 50">
                    <p class="text-xs text-gray-500 mt-1">Status will be set to '<?php echo $gift_box_data['stock_status']; ?>'</p>
                </div>

            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" required rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none"
                              placeholder="A detailed description of the Gift Box..."><?php echo htmlspecialchars($gift_box_data['product_description']); ?></textarea>
            </div>
        </div>

        <!-- Pricing -->
        <div class="form-card bg-white rounded-2xl p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-indian-rupee-sign text-primary mr-3"></i>
                Pricing
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="productprice" class="block text-sm font-medium text-gray-700 mb-2">
                        Original Price (₹) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                        <input type="number" id="productprice" name="productprice" required step="0.01" min="0"
                                value="<?php echo htmlspecialchars($gift_box_data['product_price']); ?>"
                                class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                placeholder="e.g., 599.00">
                    </div>
                </div>

                <div>
                    <label for="discountedprice" class="block text-sm font-medium text-gray-700 mb-2">
                        Discounted Price (₹) <span class="text-gray-400">(Optional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                        <input type="number" id="discountedprice" name="discountedprice" step="0.01" min="0"
                                value="<?php echo htmlspecialchars($gift_box_data['discounted_price']); ?>"
                                class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                placeholder="e.g., 499.00">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Leave empty or 0 if no discount</p>
                </div>
            </div>
        </div>

        <!-- Images -->
        <div class="form-card bg-white rounded-2xl p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-images text-primary mr-3"></i>
                Gift Box Files & Media
            </h3>
            
            <!-- Main File -->
            <div class="mb-8 p-6 border rounded-xl border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Main Gift Box File <span class="text-red-500">*</span>
                </label>
                <div class="flex items-start space-x-6 mb-4">
                    <div id="current-main-preview" class="text-center">
                        <?php 
                        $current_file = $gift_box_data['product_image'];
                        $file_extension = strtolower(pathinfo($current_file, PATHINFO_EXTENSION));
                        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                        
                        if (in_array($file_extension, $image_extensions)) {
                            echo '<img src="../uploads/' . htmlspecialchars($current_file) . '" class="preview-image-md mx-auto" alt="Current main product file">';
                        } else {
                            echo '<div class="preview-image-md mx-auto bg-gray-100 flex items-center justify-center flex-col rounded-lg">';
                            echo '<i class="fas fa-file text-4xl text-gray-400 mb-2"></i>';
                            echo '<span class="text-xs text-gray-600">.' . htmlspecialchars($file_extension) . '</span>';
                            echo '</div>';
                        }
                        ?>
                        <p class="text-xs text-gray-500 mt-1">Current File</p>
                    </div>
                    <div id="new-main-upload-area" class="flex-1">
                        <label for="productimage" class="block text-sm font-medium text-gray-700 mb-2">Replace Main File (All types supported)</label>
                        <div class="file-upload-area rounded-xl p-4 text-center" onclick="document.getElementById('productimage').click();">
                            <input type="file" id="productimage" name="productimage" class="hidden">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-1"></i>
                            <p class="text-sm text-gray-700">Click to upload or drag & drop</p>
                            <p class="text-xs text-gray-500 mt-1">Max 10MB</p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Uploading a new file will replace the current one. Leave blank to keep it.</p>
            </div>

            <!-- Gallery Images -->
            <div>
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-grip-horizontal text-primary mr-2"></i>
                    Gallery Files (Images, PDFs, Documents, Videos, etc.)
                </h4>

                <!-- Existing Files -->
                <div id="existing-extra-images" class="flex flex-wrap gap-4 mb-6 p-4 border rounded-xl bg-gray-50">
                    <?php 
                    if (mysqli_num_rows($extra_images_result) > 0) {
                        while($img = mysqli_fetch_assoc($extra_images_result)) {
                            $gallery_file = $img['image_name'];
                            $gallery_ext = strtolower(pathinfo($gallery_file, PATHINFO_EXTENSION));
                            $is_image = in_array($gallery_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                            ?>
                            <div class="relative group">
                                <?php if ($is_image) { ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($gallery_file); ?>" 
                                        class="preview-image-sm" alt="Gallery file">
                                <?php } else { ?>
                                    <div class="preview-image-sm bg-gray-100 flex items-center justify-center flex-col rounded-lg border border-gray-300">
                                        <i class="fas fa-file text-2xl text-gray-400 mb-1"></i>
                                        <span class="text-xs text-gray-600">.<?php echo htmlspecialchars($gallery_ext); ?></span>
                                    </div>
                                <?php } ?>
                                <a href="edit_giftbox.php?id=<?php echo $product_id; ?>&delete_extra_id=<?php echo $img['image_id']; ?>" 
                                    onclick="return confirm('Are you sure you want to delete this gallery file?')"
                                    title="Delete File"
                                    class="absolute -top-2 -right-2 bg-secondary text-white w-6 h-6 rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p class='text-gray-500'>No extra files in the gallery yet.</p>";
                    }
                    ?>
                </div>

                <!-- Add New Gallery Files -->
                <label for="extra_images" class="block text-sm font-medium text-gray-700 mb-2">Add New Gallery Files (Images, PDFs, Documents, etc.)</label>
                <div id="gallery-upload-area" class="file-upload-area rounded-xl p-6 text-center" onclick="document.getElementById('extra_images').click();">
                    <input type="file" id="extra_images" name="extra_images[]" multiple class="hidden">
                    <i class="fas fa-plus text-2xl text-gray-400 mb-1"></i>
                    <p class="text-sm text-gray-700">Click to select or drag & drop multiple files</p>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple files - All file types supported</p>
                </div>
                <p class="text-xs text-gray-500 mt-2">These will be added to the existing files. All file types supported (Max 10MB each)</p>
                
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
                    Save Gift Box Changes
                </button>
                <a href="view_giftbox.php" 
                    class="flex-1 border-2 border-gray-300 text-gray-700 py-4 px-8 rounded-xl hover:border-primary hover:text-primary transition-all font-medium text-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Cancel / Back to Gift Boxes
                </a>
            </div>
        </div>
    </form>
</main>

<script>
    // Main file preview for replacement - ALL FILE TYPES
    document.getElementById('productimage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const currentImg = document.getElementById('current-main-preview');
            const isImage = file.type.startsWith('image/');
            
            if (isImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentImg.innerHTML = `
                        <img src="${e.target.result}" class="preview-image-md mx-auto border-4 border-green-500" alt="New file preview">
                        <p class="text-xs text-green-600 mt-1">New File Preview</p>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                // Show file icon for non-images
                const ext = file.name.split('.').pop().toLowerCase();
                let icon = 'fa-file';
                if (['pdf'].includes(ext)) icon = 'fa-file-pdf';
                else if (['doc', 'docx'].includes(ext)) icon = 'fa-file-word';
                else if (['xls', 'xlsx'].includes(ext)) icon = 'fa-file-excel';
                else if (['ppt', 'pptx'].includes(ext)) icon = 'fa-file-powerpoint';
                else if (['zip', 'rar', '7z'].includes(ext)) icon = 'fa-file-archive';
                else if (['mp4', 'avi', 'mov', 'mkv'].includes(ext)) icon = 'fa-file-video';
                else if (['mp3', 'wav', 'ogg'].includes(ext)) icon = 'fa-file-audio';
                
                currentImg.innerHTML = `
                    <div class="preview-image-md mx-auto bg-green-50 border-4 border-green-500 flex items-center justify-center flex-col rounded-lg">
                        <i class="fas ${icon} text-4xl text-green-600 mb-2"></i>
                        <span class="text-xs text-green-700">.${ext}</span>
                        <span class="text-xs text-gray-600 mt-1">${(file.size / 1024).toFixed(1)} KB</span>
                    </div>
                    <p class="text-xs text-green-600 mt-1">New File Preview</p>
                `;
            }
        }
    });
    
    // Gallery files preview with validation - ALL FILE TYPES
    document.getElementById('extra_images').addEventListener('change', function(e) {
        const files = e.target.files;
        const previewContainer = document.getElementById('preview-container');
        const previewSection = document.getElementById('selected-files-preview');
        
        // Clear previous previews
        previewContainer.innerHTML = '';
        
        if (files.length > 0) {
            previewSection.classList.remove('hidden');
            
            Array.from(files).forEach((file, index) => {
                // Validate file size (10MB limit)
                const isValidSize = file.size <= 10485760;
                
                // Check if it's an image
                const isImage = file.type.startsWith('image/');
                
                const div = document.createElement('div');
                div.className = 'relative';
                
                if (isValidSize) {
                    if (isImage) {
                        // Preview images
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            div.innerHTML = `
                                <img src="${e.target.result}" class="w-16 h-16 object-cover rounded border border-green-500" title="${file.name}">
                                <span class="absolute -top-2 -right-2 bg-green-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">${index + 1}</span>
                            `;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // Show file icon for non-images
                        const ext = file.name.split('.').pop().toLowerCase();
                        let icon = 'fa-file';
                        if (['pdf'].includes(ext)) icon = 'fa-file-pdf';
                        else if (['doc', 'docx'].includes(ext)) icon = 'fa-file-word';
                        else if (['xls', 'xlsx'].includes(ext)) icon = 'fa-file-excel';
                        else if (['ppt', 'pptx'].includes(ext)) icon = 'fa-file-powerpoint';
                        else if (['zip', 'rar', '7z'].includes(ext)) icon = 'fa-file-archive';
                        else if (['mp4', 'avi', 'mov', 'mkv'].includes(ext)) icon = 'fa-file-video';
                        else if (['mp3', 'wav', 'ogg'].includes(ext)) icon = 'fa-file-audio';
                        else if (['txt', 'csv'].includes(ext)) icon = 'fa-file-alt';
                        
                        div.innerHTML = `
                            <div class="w-16 h-16 bg-green-50 rounded border border-green-500 flex items-center justify-center flex-col" title="${file.name}">
                                <i class="fas ${icon} text-green-600 text-xl mb-1"></i>
                                <span class="text-xs text-green-700">.${ext}</span>
                            </div>
                            <span class="absolute -top-2 -right-2 bg-green-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">${index + 1}</span>
                        `;
                    }
                } else {
                    // File too large
                    div.innerHTML = `
                        <div class="w-16 h-16 bg-red-100 rounded border border-red-500 flex items-center justify-center text-red-500 text-xs" title="${file.name} - File too large (max 10MB)">
                            <i class="fas fa-times"></i>
                        </div>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">!</span>
                    `;
                }
                
                previewContainer.appendChild(div);
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
            document.getElementById('productimage').files = files;
            document.getElementById('productimage').dispatchEvent(new Event('change'));
        }
    });
</script>

</body>
</html>