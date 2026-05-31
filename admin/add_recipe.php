<?php
// --- 🛠️ STEP 1: ENABLE MAX ERROR REPORTING ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- FILE INCLUDES ---
include "../connection.php";
include "header.php";

// --- DEBUG: CHECK DATABASE CONNECTION ---
if (!isset($conn) || mysqli_connect_errno()) {
    die("FATAL ERROR: Could not connect to the database. Please check your 'connection.php' file. MySQL Error: " . mysqli_connect_error());
}

// --- PHP FORM PROCESSING LOGIC ---
if(isset($_POST['save']))
{
    $productname = $_POST['productname'];
    $productdescription = $_POST['productdescription'];
    $productquantity = $_POST['productquantity'];
    $productprice = $_POST['productprice'];
    $discountedprice = !empty($_POST['discountedprice']) ? $_POST['discountedprice'] : NULL;
    $category = $_POST['category'];

    // 1. Upload main image
    $productimage = $_FILES['image']['name']; 
    $tmp = $_FILES['image']['tmp_name'];
    $folder = "../uploads/".$productimage;

    // Check if the upload directory exists and is writable
    if (!is_dir('../uploads')) {
        if (!mkdir('../uploads', 0777, true)) {
             die("FATAL ERROR: Failed to create '../uploads' directory. Check folder path and permissions.");
        }
    }
    
    // Attempt file upload
    if (move_uploaded_file($tmp, $folder)) {
        // 2. Insert product into main table
        $sql = "INSERT INTO product 
        (product_name, product_description, category, product_quantity, product_price, discounted_price, product_image) 
        VALUES 
        ('$productname', '$productdescription', '$category', '$productquantity', '$productprice', " . 
        ($discountedprice !== NULL ? "'$discountedprice'" : "NULL") . ", '$productimage')";

        $result = mysqli_query($conn, $sql);

        if($result)
        {   
            //$pid = mysqli_insert_id($conn); 

            // 3. Upload extra images
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

            echo "<script>alert('Product added Successfully.'); window.location.href='add_recipe.php';</script>";
            exit;
        }
        else
        {
            echo "<script>alert('SQL Insertion Failed: " . mysqli_error($conn) . "');</script>";   
        }
    } else {
        $error_code = $_FILES['image']['error'];
        echo "<script>alert('File Upload Failed. Error code: $error_code. Check your php.ini settings (upload_max_filesize).');</script>";
    }
}
?>

<style>
    /* Custom utility classes based on your design style (same as previous) */
    .form-input {
        width: 100%; padding: 0.5rem 1rem; border-width: 1px; border-color: #d1d5db; border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.15s ease-in-out;
    }
    .form-input:focus {
        outline: none; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.5); border-color: transparent;
    }
    .form-input.h-32 {
        resize: vertical; height: 8rem;
    }
    .form-file {
        width: 100%; font-size: 0.875rem; color: #6b7280; padding: 0.5rem 0; transition: all 0.15s ease-in-out;
    }
    .form-file::-webkit-file-upload-button {
        padding: 0.5rem 1rem; margin-right: 1rem; border-width: 0; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; background-color: #fff7ed; color: #f97316; cursor: pointer; transition: background-color 0.15s ease-in-out;
    }
    .form-file:hover::-webkit-file-upload-button {
        background-color: #ffedd5;
    }
    .bg-primary { background-color: #f97316; }
    .hover\:bg-secondary:hover { background-color: #dc2626; }
    .text-primary { color: #f97316; }
    .shadow-xl { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); }
    .hover\:shadow-2xl:hover { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
    .transform:hover { transform: translateY(-2px); }
    .hover\:scale-\[1\.02\]:hover { transform: scale(1.02); }
</style>

<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Products / Recipes</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add New</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid p-4 lg:p-6">
        <div class="flex justify-center">
            <div class="w-full lg:w-4/5 xl:w-3/4"> 
                <div class="bg-white shadow-xl rounded-xl overflow-hidden transform transition duration-500 hover:shadow-2xl">
                    
                    <div class="p-6 md:p-8 bg-orange-50 border-b border-orange-200">
                        <h4 class="text-2xl font-bold text-gray-800 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.523 5.754 18 7.5 18s3.332.477 4.5 1.247m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.523 18.246 18 16.5 18s-3.332.477-4.5 1.247"></path></svg>
                            Add New Product / Recipe
                        </h4>
                        <p class="text-sm text-gray-500 mt-1">Configure the recipe details, pricing, and upload media.</p>
                    </div>

                    <form method="post" enctype="multipart/form-data" class="p-6 md:p-8 space-y-8">
                        
                        <div>
                            <h5 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Core Information</h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <div class="space-y-4">
                                    
                                    <div class="form-group">
                                        <label for="productname" class="block text-sm font-medium text-gray-700 mb-1">Recipe/Product Name <span class="text-red-500">*</span></label>
                                        <input type="text" class="form-input" id="productname" name="productname" placeholder="E.g., Chicken Tikka Masala (Spice Blend)" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                                        <select class="form-input" id="category" name="category" required>
                                            <option value="">-- Select Category --</option>
                                            <option value="Spices">Whole Spices</option>
                                            <option value="Powders">Spice Powders</option>
                                            <option value="Blended Spices">Blended Spices / Masalas</option>
                                            <option value="Dry Fruits">Dry Fruits & Nuts</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="productquantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity in Stock</label>
                                        <input type="number" class="form-input" id="productquantity" name="productquantity" placeholder="Enter stock count (e.g., 150)">
                                    </div>

                                </div>

                                <div class="space-y-4">
                                    <div class="form-group">
                                        <label for="productdescription" class="block text-sm font-medium text-gray-700 mb-1">Full Description/Recipe Notes</label>
                                        <textarea class="form-input h-32" id="productdescription" name="productdescription" placeholder="Describe the recipe, ingredients, or product in detail."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="productprice" class="block text-sm font-medium text-gray-700 mb-1">Original Price (₹) <span class="text-red-500">*</span></label>
                                        <input type="text" class="form-input" id="productprice" name="productprice" placeholder="E.g., 250.00" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="discountedprice" class="block text-sm font-medium text-gray-700 mb-1">Discounted Price (₹) (Optional)</label>
                                        <input type="text" class="form-input" id="discountedprice" name="discountedprice" placeholder="E.g., 199.00">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h5 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Recipe Images</h5>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Main Recipe Image <span class="text-red-500">*</span></label>
                                    <input type="file" class="form-file" id="image" name="image" required>
                                    <p class="text-xs text-gray-500 mt-1">This will be the main image shown on the recipe card.</p>
                                </div>

                                <div class="form-group">
                                    <label for="extra_images" class="block text-sm font-medium text-gray-700 mb-1">Gallery/Step Images (Optional)</label>
                                    <input type="file" class="form-file" id="extra_images" name="extra_images[]" multiple>
                                    <p class="text-xs text-gray-500 mt-1">Select up to 6 additional images (e.g., different preparation steps).</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t pt-6 -mx-8">
                            <div class="px-8 flex justify-end">
                                <button type="submit" name="save" class="w-full md:w-auto px-8 py-3 bg-primary text-white font-medium rounded-lg shadow-lg hover:bg-secondary transition duration-300 transform hover:scale-[1.02] focus:ring-4 focus:ring-primary/50">
                                    <i class="fas fa-save mr-2"></i>
                                    Submit Recipe
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>