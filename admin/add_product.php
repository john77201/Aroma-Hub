<?php
include "../connection.php";
include 'header.php';

if(isset($_POST['save']))
{
    $productname = $_POST['productname'];
    $productdescription = $_POST['productdescription'];
    $productquantity = $_POST['productquantity'];
    $productprice = $_POST['productprice'];
    $discountedprice = !empty($_POST['discountedprice']) ? $_POST['discountedprice'] : NULL;
    $category = $_POST['category'];

    // ✅ Upload main image
    $productimage = $_FILES['image']['name']; 
    $tmp = $_FILES['image']['tmp_name'];
    $folder = "../uploads/".$productimage;
    move_uploaded_file($tmp, $folder);

    // ✅ Insert product into main table
    $sql = "INSERT INTO product 
    (product_name, product_description, category, product_quantity, product_price, discounted_price, product_image) 
    VALUES 
    ('$productname', '$productdescription', '$category', '$productquantity', '$productprice', " . 
    ($discountedprice !== NULL ? "'$discountedprice'" : "NULL") . ", '$productimage')";

    $result = mysqli_query($conn, $sql);

    if($result)
    {   
        $pid = mysqli_insert_id($conn); // ✅ get new product_id

        // ✅ Upload extra images (3–6)
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

        echo "<script>alert('Product added Successfully.');</script>";
    }
    else
    {
        echo "<script>alert('Failed: ".mysqli_error($conn)."');</script>";   
    }
}
?>


    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="admin-dashboard.html" class="hover:text-primary transition-colors">Dashboard</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="#" class="hover:text-primary transition-colors">Products</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Add Product</span>
            </nav>
        </div>
    </div>

    <!-- Page Header -->
    <section class="hero-gradient text-white py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-plus text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-2">Add New Product</h1>
                    <p class="text-white/90">Add premium spices and seasonings to your inventory</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="POST" enctype="multipart/form-data" class="space-y-8">
            <!-- Basic Information -->
            <div class="form-card bg-white rounded-2xl p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-primary mr-3"></i>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Name -->
                    <div>
                        <label for="productname" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="productname" name="productname" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                               placeholder="Enter Product Name">
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select id="category" name="category" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                            <option value="">-- Select Category --</option>
                                        <option value="Spices">Whole Spices</option>
                                        <option value="Powders">Spice Powders</option>
                                        <option value="Blended Spices">Blended Spices / Masalas</option>
                                        <option value="Dry Fruits">Dry Fruits & Nuts</option>
                        </select>
                    </div>

                    <!-- Product Description -->
                    <div class="md:col-span-2">
                        <label for="productdescription" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="productdescription" name="productdescription" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                  placeholder="Detailed description of the product, its origin, uses, and benefits..."></textarea>
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
                    <!-- Product Price -->
                    <div>
                        <label for="productprice" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                            <input type="number" id="productprice" name="productprice" step="0.01" min="0" required
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <!-- Discounted Price -->
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

                    <!-- Product Quantity -->
                    <div>
                        <label for="productquantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Quantity in Stock <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="productquantity" name="productquantity" min="0" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                               placeholder="100">
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            <div class="form-card bg-white rounded-2xl p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-images text-primary mr-3"></i>
                    Product Images
                </h3>
                
                <!-- Main Image -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Main Product Image <span class="text-red-500">*</span>
                    </label>
                    <div class="file-upload-area rounded-xl p-8 text-center">
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

                <!-- Extra Images -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Additional Images (Optional - Up to 6 images)
                    </label>
                    <div class="file-upload-area rounded-xl p-8 text-center">
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

            <!-- Form Actions -->
            <div class="form-card bg-white rounded-2xl p-8">
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" name="save" 
                            class="flex-1 bg-gradient-to-r from-primary to-secondary text-white py-4 px-8 rounded-xl hover:shadow-lg transition-all font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Save Product
                    </button>
                    <button type="button" onclick="resetForm()" 
                            class="flex-1 border-2 border-gray-300 text-gray-700 py-4 px-8 rounded-xl hover:border-primary hover:text-primary transition-all font-medium">
                        <i class="fas fa-undo mr-2"></i>
                        Reset Form
                    </button>
                    <a href="index.php" 
                       class="flex-1 bg-gray-600 text-white py-4 px-8 rounded-xl hover:bg-gray-700 transition-all font-medium text-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 hero-gradient rounded-xl flex items-center justify-center">
                            <i class="fas fa-seedling text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Aroma Hub</h3>
                            <p class="text-sm text-gray-400">Admin Dashboard</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 max-w-md">
                        Manage your premium spice business with our comprehensive admin dashboard. 
                        Monitor sales, manage inventory, and grow your customer base.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Products</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Orders</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Customers</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Analytics</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">API Reference</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Support</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">System Status</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Aroma Hub Admin Dashboard. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Toast notification system
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    container.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Main image upload handling
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('main-preview-img').src = e.target.result;
                    document.getElementById('main-preview').classList.remove('hidden');
                    document.querySelector('.main-upload-content').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Extra images upload handling
        document.getElementById('extra_images').addEventListener('change', function(e) {
            const files = e.target.files;
            const container = document.getElementById('extra-preview-container');
            container.innerHTML = '';
            
            if (files.length > 0) {
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
                document.getElementById('extra-preview').classList.remove('hidden');
                document.querySelector('.extra-upload-content').classList.add('hidden');
            }
        });

        // Click to upload functionality
        document.querySelector('.file-upload-area').addEventListener('click', function(e) {
            if (e.target.closest('#main-preview')) return;
            document.getElementById('image').click();
        });

        document.querySelectorAll('.file-upload-area')[1].addEventListener('click', function(e) {
            if (e.target.closest('#extra-preview')) return;
            document.getElementById('extra_images').click();
        });

        // Drag and drop functionality
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
                if (this === document.querySelectorAll('.file-upload-area')[0]) {
                    document.getElementById('image').files = files;
                    document.getElementById('image').dispatchEvent(new Event('change'));
                } else {
                    document.getElementById('extra_images').files = files;
                    document.getElementById('extra_images').dispatchEvent(new Event('change'));
                }
            });
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const productname = document.getElementById('productname').value.trim();
            const category = document.getElementById('category').value;
            const productprice = document.getElementById('productprice').value;
            const productquantity = document.getElementById('productquantity').value;
            const image = document.getElementById('image').files[0];

            if (!productname || !category || !productprice || !productquantity || !image) {
                e.preventDefault();
                alert('Please fill in all required fields and upload a main product image.');
                return false;
            }

            if (parseFloat(productprice) <= 0) {
                e.preventDefault();
                alert('Product price must be greater than 0.');
                return false;
            }

            if (parseInt(productquantity) < 0) {
                e.preventDefault();
                alert('Product quantity cannot be negative.');
                return false;
            }
        });

        // Reset form function
        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
                document.querySelector('form').reset();
                document.getElementById('main-preview').classList.add('hidden');
                document.getElementById('extra-preview').classList.add('hidden');
                document.querySelector('.main-upload-content').classList.remove('hidden');
                document.querySelector('.extra-upload-content').classList.remove('hidden');
            }
        }

        // Auto-calculate discount percentage
        document.getElementById('discountedprice').addEventListener('input', function() {
            const originalPrice = parseFloat(document.getElementById('productprice').value);
            const discountedPrice = parseFloat(this.value);
            
            if (originalPrice && discountedPrice && discountedPrice < originalPrice) {
                const discountPercent = Math.round(((originalPrice - discountedPrice) / originalPrice) * 100);
                this.setAttribute('title', `${discountPercent}% discount`);
            }
        });
    </script>
</body>
</html>