<?php
include "../connection.php";
include 'header.php';

// --- PHP Logic for Bulk Product Insertion ---
if(isset($_POST['save']))
{
    // Sanitize/escape input data (Crucial for security)
    $productname = mysqli_real_escape_string($conn, $_POST['productname']);
    $productdescription = mysqli_real_escape_string($conn, $_POST['productdescription']);
    
    // NOTE: The Bulk product form does NOT collect productquantity, but the DB likely requires it. 
    // We will use 0 or a placeholder for bulk, or you can add the field to the form if needed.
    // Based on your original bulk logic, we will assume bulk orders don't track quantity here,
    // but we MUST check your DB table for required fields. Assuming 0 for now.
    $productquantity = 0; // Defaulting to 0 or a large number for bulk visibility
    
    $productprice = mysqli_real_escape_string($conn, $_POST['productprice']);
    $discountedprice_input = !empty($_POST['discountedprice']) ? mysqli_real_escape_string($conn, $_POST['discountedprice']) : NULL;
    $quality = mysqli_real_escape_string($conn, $_POST['quality']); // Bulk-specific field
    $category = "Bulk"; // Fixed category for bulk orders

    // Handle discounted price
    $discountedprice = ($discountedprice_input !== NULL) ? "'$discountedprice_input'" : "NULL";

    // Upload main image
    $productimage = $_FILES['image']['name']; 
    $tmp = $_FILES['image']['tmp_name'];
    $new_image_name = time() . '_' . $productimage; // Unique name for file
    $folder = "../uploads/".$new_image_name;
    move_uploaded_file($tmp, $folder);

    // Insert product into main table (Note: 'quality' is included here)
    // IMPORTANT: Ensure your 'product' table has a 'quality' column.
    $sql = "INSERT INTO product 
    (product_name, product_description, category, product_quantity, product_price, discounted_price, product_image, quality) 
    VALUES 
    ('$productname', '$productdescription', '$category', '$productquantity', '$productprice', $discountedprice, '$new_image_name', '$quality')";

    $result = mysqli_query($conn, $sql);

    if($result)
    { 
        $pid = mysqli_insert_id($conn); // get new product_id

        // Upload extra images (3–6)
        if(!empty($_FILES['extra_images']['name'][0])){
            foreach($_FILES['extra_images']['name'] as $key=>$val){
                if(!empty($val)){
                    $imgName = time().'_'.$val;
                    $tmpName = $_FILES['extra_images']['tmp_name'][$key];
                    move_uploaded_file($tmpName, "../uploads/".$imgName);

                    mysqli_query($conn, "INSERT INTO product_images (product_id, image_name) VALUES ('$pid','$imgName')");
                }
            }
        }

        echo "<script>alert('Bulk Order product added Successfully.'); window.location.href='view_products.php';</script>";
    }
    else
    {
        echo "<script>alert('Failed to add product: ".mysqli_error($conn)."');</script>"; 
    }
}
// --- END PHP Logic ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bulk Product - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316', // Orange-600
                        secondary: '#dc2626', // Red-600
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-gradient {
            background-image: linear-gradient(to right, #f97316, #dc2626);
        }
        .form-card {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .file-upload-area {
            border: 2px dashed #d1d5db; /* gray-300 */
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .file-upload-area:hover, .file-upload-area.dragover {
            border-color: #f97316; /* primary color */
        }
        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        #toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .toast {
            padding: 1rem;
            border-radius: 0.5rem;
            color: white;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }
        .toast.success { background-color: #10b981; } /* emerald-500 */
        .toast.error { background-color: #ef4444; } /* red-500 */
        .toast.info { background-color: #3b82f6; } /* blue-500 */
    </style>
</head>
<body>
    <div id="toast-container"></div>
    
    <div class="bg-white border-b">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="index.php" class="hover:text-primary transition-colors">Dashboard</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="view_products.php" class="hover:text-primary transition-colors">Products</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Add Bulk Product</span>
            </nav>
        </div>
    </div>

    <section class="hero-gradient text-white py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-boxes-stacked text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-2">Add New Bulk Product</h1>
                    <p class="text-white/90">Add products specifically for bulk/wholesale orders</p>
                </div>
            </div>
        </div>
    </section>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="POST" enctype="multipart/form-data" class="space-y-8">
            <div class="form-card bg-white rounded-2xl p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-primary mr-3"></i>
                    Bulk Product Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="productname" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="productname" name="productname" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                               placeholder="Enter Product Name (e.g., Cardamom Bulk)">
                    </div>

                    <div>
                        <label for="quality" class="block text-sm font-medium text-gray-700 mb-2">
                            Quality <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="quality" name="quality" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                               placeholder="Enter Quality (e.g., Premium, Grade A)">
                    </div>
                </div>

                <div class="mt-6">
                    <label for="productdescription" class="block text-sm font-medium text-gray-700 mb-2">
                        Product Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="productdescription" name="productdescription" rows="4" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                            placeholder="Detailed description of the bulk product, minimum order quantity (if applicable), and specifications..."></textarea>
                </div>
            </div>

            <div class="form-card bg-white rounded-2xl p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-indian-rupee-sign text-primary mr-3"></i>
                    Pricing
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="productprice" class="block text-sm font-medium text-gray-700 mb-2">
                            Base Price (₹) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                            <input type="number" id="productprice" name="productprice" step="0.01" min="0" required
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label for="discountedprice" class="block text-sm font-medium text-gray-700 mb-2">
                            Discounted Price (Optional)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                            <input type="number" id="discountedprice" name="discountedprice" step="0.01" min="0"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                   placeholder="0.00">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Leave empty if no discount</p>
                    </div>

                    <input type="hidden" name="productquantity" value="0"> 
                </div>
            </div>

            <div class="form-card bg-white rounded-2xl p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-images text-primary mr-3"></i>
                    Product Images
                </h3>
                
                <div class="mb-8">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-3">
                        Main Product Image <span class="text-red-500">*</span>
                    </label>
                    <div class="file-upload-area rounded-xl p-8 text-center" onclick="document.getElementById('image').click()">
                        <input type="file" id="image" name="image" accept="image/*" required class="hidden">
                        <div class="main-upload-content">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                            <p class="text-lg font-medium text-gray-700 mb-2">Click to upload main image</p>
                            <p class="text-sm text-gray-500">PNG, JPG, GIF up to 10MB</p>
                        </div>
                        <div id="main-preview" class="hidden mt-4">
                            <img id="main-preview-img" class="preview-image mx-auto" alt="Main product preview">
                            <p class="text-sm text-gray-600 mt-2">Main Product Image</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="extra_images" class="block text-sm font-medium text-gray-700 mb-3">
                        Additional Images (Optional - Up to 6 images)
                    </label>
                    <div class="file-upload-area rounded-xl p-8 text-center" onclick="document.getElementById('extra_images').click()">
                        <input type="file" id="extra_images" name="extra_images[]" accept="image/*" multiple class="hidden">
                        <div class="extra-upload-content">
                            <i class="fas fa-images text-4xl text-gray-400 mb-4"></i>
                            <p class="text-lg font-medium text-gray-700 mb-2">Click to upload additional images</p>
                            <p class="text-sm text-gray-500">Select multiple images (3-6 recommended)</p>
                        </div>
                        <div id="extra-preview" class="hidden mt-4">
                            <div id="extra-preview-container" class="flex flex-wrap gap-4 justify-center"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card bg-white rounded-2xl p-8">
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" name="save" 
                            class="flex-1 bg-gradient-to-r from-primary to-secondary text-white py-4 px-8 rounded-xl hover:shadow-lg transition-all font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Save Bulk Product
                    </button>
                    <button type="button" onclick="resetForm()" 
                            class="flex-1 border-2 border-gray-300 text-gray-700 py-4 px-8 rounded-xl hover:border-primary hover:text-primary transition-all font-medium">
                        <i class="fas fa-undo mr-2"></i>
                        Reset Form
                    </button>
                    <a href="index.php" 
                       class="flex-1 bg-gray-600 text-white py-4 px-8 rounded-xl hover:bg-gray-700 transition-all font-medium text-center flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </main>

    <?php include "footer.php"; // Assuming your footer is included here based on the design ?>
    
    <script>
        // --- Image Upload and Preview Logic ---
        
        // Helper function for click/drop to open file selector
        function setupFileUpload(inputId, previewId, contentSelector, multiple = false) {
            const input = document.getElementById(inputId);
            const previewContainer = document.getElementById(previewId);
            const content = document.querySelector(contentSelector);
            const fileArea = input.closest('.file-upload-area');

            // Event listener for file selection
            input.addEventListener('change', function(e) {
                const files = e.target.files;
                if (!multiple && files.length > 0) {
                    // Main image logic
                    const file = files[0];
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.getElementById('main-preview-img');
                        img.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                        content.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else if (multiple && files.length > 0) {
                    // Extra images logic
                    const container = document.getElementById('extra-preview-container');
                    container.innerHTML = '';
                    Array.from(files).slice(0, 6).forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'text-center';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="preview-image" alt="Extra image ${index + 1}">
                                <p class="text-xs text-gray-600 mt-1">Image ${index + 1}</p>
                            `;
                            container.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    });
                    previewContainer.classList.remove('hidden');
                    content.classList.add('hidden');
                } else {
                    // No files selected, reset preview
                    previewContainer.classList.add('hidden');
                    content.classList.remove('hidden');
                    if (!multiple) {
                        document.getElementById('main-preview-img').src = '';
                    } else {
                        document.getElementById('extra-preview-container').innerHTML = '';
                    }
                }
            });

            // Click to open file selector
            if(fileArea) {
                fileArea.addEventListener('click', function(e) {
                    if (e.target.closest(`#${previewId}`)) return;
                    input.click();
                });
            }
        }

        // Setup main image
        setupFileUpload('image', 'main-preview', '.main-upload-content', false);
        // Setup extra images
        setupFileUpload('extra_images', 'extra-preview', '.extra-upload-content', true);

        // Reset form function
        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
                document.querySelector('form').reset();
                document.getElementById('main-preview').classList.add('hidden');
                document.getElementById('extra-preview').classList.add('hidden');
                document.querySelector('.main-upload-content').classList.remove('hidden');
                document.querySelector('.extra-upload-content').classList.remove('hidden');
                document.getElementById('extra-preview-container').innerHTML = '';
                document.getElementById('main-preview-img').src = '';
                // Also reset placeholder attribute/title if needed
                const discountedPriceInput = document.getElementById('discountedprice');
                discountedPriceInput.removeAttribute('title');
            }
        }

        // Auto-calculate discount percentage (optional tooltip)
        document.getElementById('discountedprice').addEventListener('input', updateDiscountTooltip);
        document.getElementById('productprice').addEventListener('input', updateDiscountTooltip);
        
        function updateDiscountTooltip() {
            const originalPrice = parseFloat(document.getElementById('productprice').value);
            const discountedPrice = parseFloat(document.getElementById('discountedprice').value);
            const discountedPriceInput = document.getElementById('discountedprice');
            
            if (originalPrice && discountedPrice) {
                if (discountedPrice >= originalPrice) {
                     discountedPriceInput.setAttribute('title', `Discounted price must be less than Base Price.`);
                } else {
                    const discountPercent = Math.round(((originalPrice - discountedPrice) / originalPrice) * 100);
                    discountedPriceInput.setAttribute('title', `${discountPercent}% discount`);
                }
            } else {
                discountedPriceInput.removeAttribute('title');
            }
        }
        
        // --- Drag and Drop functionality (Optional, but included for completeness) ---
        document.querySelectorAll('.file-upload-area').forEach(area => {
            area.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            area.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            area.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (this.querySelector('#image')) {
                    document.getElementById('image').files = files;
                    document.getElementById('image').dispatchEvent(new Event('change'));
                } else if (this.querySelector('#extra_images')) {
                    document.getElementById('extra_images').files = files;
                    document.getElementById('extra_images').dispatchEvent(new Event('change'));
                }
            });
        });
    </script>
</body>
</html>