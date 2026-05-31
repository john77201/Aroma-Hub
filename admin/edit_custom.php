<?php
include "../connection.php";
include "header.php";

$product_id = $_GET['product_id'];

// --- 1. Fetch only the product IF its category is 'Custom' ---
$qry = "SELECT * FROM product WHERE product_id='$product_id' AND category='Custom'";
$answer = mysqli_query($conn, $qry);

// Redirect if product is not found or is not a Custom spice
if (mysqli_num_rows($answer) == 0) {
    echo "<script>alert('Product not found or not a Custom spice.');window.location.href='view_products.php';</script>";
    exit;
}

$row = mysqli_fetch_assoc($answer);

// Fetch extra images
$extraImagesQry = "SELECT * FROM product_images WHERE product_id='$product_id'";
$extraImagesResult = mysqli_query($conn, $extraImagesQry);

if (isset($_POST['save'])) {
    // Escape strings for security
    $productname = mysqli_real_escape_string($conn, $_POST['productname']);
    $productdescription = mysqli_real_escape_string($conn, $_POST['productdescription']);
    $productquantity = mysqli_real_escape_string($conn, $_POST['productquantity']);
    $productprice = mysqli_real_escape_string($conn, $_POST['productprice']);
    $discountprice = mysqli_real_escape_string($conn, $_POST['discountprice']); 
    $oldimage = mysqli_real_escape_string($conn, $_POST['imagee']);
    $productimage = $_FILES['image']['name'];
    $delete_old_image = false;

    // --- Handle Main Image Upload ---
    if ($productimage != "") {
        $tmp = $_FILES['image']['tmp_name'];
        $folder = "../uploads/" . $productimage;
        if (move_uploaded_file($tmp, $folder)) {
            $delete_old_image = true;
        } else {
            // Revert to old image if upload fails
            $productimage = $oldimage;
        }
    } else {
        $productimage = $oldimage;
    }

    // --- 2. Update Product Table (Only if category is 'Custom') ---
    $sql = "UPDATE product SET 
                product_name='$productname',
                product_description='$productdescription',
                product_quantity='$productquantity',
                product_price='$productprice',
                discounted_price='$discountprice',
                product_image='$productimage'
            WHERE product_id='$product_id' AND category='Custom'";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result) {   
        // Delete old main image AFTER successful database update
        if ($delete_old_image && !empty($oldimage) && file_exists("../uploads/".$oldimage)) {
            @unlink("../uploads/".$oldimage);
        }
        
        // Add new extra images
        if (!empty($_FILES['extra_images']['name'][0])) {
            foreach ($_FILES['extra_images']['name'] as $key => $val) {
                if (!empty($val)) {
                    $imgName = time() . '_' . mysqli_real_escape_string($conn, $val);
                    $tmpName = $_FILES['extra_images']['tmp_name'][$key];
                    if (move_uploaded_file($tmpName, "../uploads/" . $imgName)) {
                        mysqli_query($conn, "INSERT INTO product_images (product_id, image_name) VALUES ('$product_id','$imgName')");
                    }
                }
            }
        }

        // Redirect to a dedicated view page for custom products (assuming view_custom.php exists)
        echo "<script>alert('Custom Spice Updated Successfully.');window.location.href='view_custom.php';</script>";
    } else {
        echo "<script>alert('Failed to update Custom Spice: ".mysqli_error($conn)."');</script>";  
    }
}
?>

<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Edit Custom Spice</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="view_custom.php">Custom Spices</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <form class="form-horizontal" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <h4 class="card-title">Edit Custom Spice Details</h4>

                            <div class="form-group row">
                                <label for="productname" class="col-sm-3 text-end control-label col-form-label">Spice Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="productname" name="productname" value="<?= htmlspecialchars($row['product_name']); ?>" required />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="productdescription" class="col-sm-3 text-end control-label col-form-label">Description</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="productdescription" name="productdescription" required><?= htmlspecialchars($row['product_description']); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="productquantity" class="col-sm-3 text-end control-label col-form-label">Default Quantity</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="productquantity" name="productquantity" value="<?= $row['product_quantity']; ?>" required />
                                    <small class="text-muted">This is typically the base amount for the 'Custom' selection.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="productprice" class="col-sm-3 text-end control-label col-form-label">Base Price</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="productprice" name="productprice" value="<?= $row['product_price']; ?>" required />
                                    <small class="text-muted">This price is used as the base for the 100g/250g calculation on the frontend.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="discountprice" class="col-sm-3 text-end control-label col-form-label">Discount Price</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="discountprice" name="discountprice" value="<?= $row['discounted_price']; ?>" placeholder="Optional" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="image" class="col-sm-3 text-end control-label col-form-label">Main Image</label>
                                <div class="col-sm-9">
                                    <?php if (!empty($row['product_image'])): ?>
                                        <p>Current Image:</p>
                                        <img src="../uploads/<?= htmlspecialchars($row['product_image']); ?>" style="max-width:150px; height:auto;">
                                        <input type="hidden" name="imagee" value="<?= $row['product_image']; ?>">
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="image" name="image" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 text-end control-label col-form-label">Extra Images</label>
                                <div class="col-sm-9">
                                    <div class="d-flex flex-wrap">
                                        <?php 
                                        // Reset the pointer to fetch all extra images again
                                        mysqli_data_seek($extraImagesResult, 0); 
                                        while($img = mysqli_fetch_assoc($extraImagesResult)) { ?>
                                            <div class="extra-image-wrapper text-center me-2 mb-2">
                                                <img src="../uploads/<?= htmlspecialchars($img['image_name']); ?>" style="width:100px; height:100px; object-fit:cover;">
                                                <br>
                                                <button type="button" class="btn btn-danger btn-sm delete-image" 
                                                    data-id="<?= $img['image_id']; ?>" data-pid="<?= $row['product_id']; ?>">
                                                    Delete
                                                </button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <small class="text-muted">You can add new extra images above.</small>
                                    <input type="file" class="form-control mt-2" name="extra_images[]" multiple>
                                </div>
                            </div>

                        </div>
                        <div class="border-top">
                            <div class="card-body">
                                <button type="submit" name="save" class="btn btn-primary">Submit</button>

                                <?php if ($row['stock_status'] == 'Out Of Stock') { ?>
                                    <a href="instock.php?id=<?= $row['product_id']; ?>" class="btn btn-success">Mark In Stock</a>
                                <?php } else { ?>
                                    <a href="outstock.php?id=<?= $row['product_id']; ?>" class="btn btn-danger">Mark Out of Stock</a>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $(".delete-image").click(function(){
        if(confirm("Are you sure you want to delete this image?")) {
            var image_id = $(this).data("id");
            var pid = $(this).data("pid");
            var button = $(this);

            $.ajax({
                // Ensure this path is correct: 'delete_extra_image.php' should be in the same folder as 'edit_custom.php'
                url: 'delete_extra_image.php', 
                type: 'GET',
                data: {id: image_id, pid: pid},
                success: function(response){
                    alert("Image deleted successfully"); 
                    button.closest(".extra-image-wrapper").remove();
                },
                error: function(xhr, status, error){
                    // Show a more descriptive error if the AJAX call fails
                    alert("Error deleting image: " + xhr.responseText);
                }
            });
        }
    });
});
</script>