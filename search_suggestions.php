<?php
include "connection.php";

if(isset($_POST['query'])){
    $search = mysqli_real_escape_string($conn, $_POST['query']);
    $search_lower = strtolower($search);

    // Get category from AJAX request
    $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : '0';

    // Build query
    $query = "SELECT product_id, product_name, product_price, discounted_price, product_image 
              FROM product 
              WHERE LOWER(product_name) LIKE '%$search_lower%'";

    // Apply category filter if not "All Categories"
    if($category != '0'){
        $query .= " AND LOWER(category) = LOWER('$category')";
    }

    $query .= " LIMIT 5";

    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $name = $row['product_name'];

            // Highlight typed letters
            $pattern = "/($search)/i";
            $highlighted_name = preg_replace($pattern, '<span style="color:#ff6600;font-weight:bold;">$1</span>', $name);

            // Price display
            $price = $row['discounted_price'] > 0 ? $row['discounted_price'] : $row['product_price'];

            // Correct image path for 'uploads' folder
            $imagePath = 'uploads/'.$row['product_image'];

            echo '<li class="suggestion-item" data-id="'.$row['product_id'].'" style="
                display:flex; 
                align-items:center; 
                padding:10px; 
                border-bottom:1px solid #eee; 
                cursor:pointer; 
                transition: background 0.2s, box-shadow 0.2s;
            ">
                <img src="'.$imagePath.'" style="width:50px; height:50px; object-fit:cover; margin-right:10px; border-radius:5px;">
                <div>
                    <div>'.$highlighted_name.'</div>
                    <div style="font-size:13px; color:#777;">₹'.$price.'</div>
                </div>
            </li>';
        }
    } else {
        echo '<li style="padding:10px;">No results found</li>';
    }
}
?>
