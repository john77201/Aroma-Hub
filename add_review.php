<?php
// Start session, include connection, and header/footer files
if (session_status() === PHP_SESSION_NONE) {
   // session_start();
}
include "header.php"; // Assuming this includes connection.php or we'll include it explicitly
include "connection.php";

// ------------------------------------
// 1. AUTHENTICATION AND ID VALIDATION
// ------------------------------------
if (!isset($_SESSION["userid"])) {
    // Redirect unauthenticated users
    header("Location: login.php");
    exit;
}
$uid = $_SESSION["userid"];

// Get the product and order IDs from the URL
$product_id = isset($_GET['product']) ? intval($_GET['product']) : 0;
$orderno = isset($_GET['order']) ? mysqli_real_escape_string($conn, $_GET['order']) : '';

if ($product_id <= 0 || empty($orderno)) {
    echo "<script>alert('Error: Missing product or order information.'); window.location.href='myorders.php';</script>";
    exit;
}

$message = "";
$message_type = "";

// ------------------------------------
// 2. FORM SUBMISSION HANDLING (POST)
// ------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = mysqli_real_escape_string($conn, $_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $message = "Please select a rating between 1 and 5 stars.";
        $message_type = "error";
    } else {
        // Prepare SQL statement to insert the review
        // Note: The order_no and product_id are taken from the hidden fields/GET parameters
        $insert_sql = "INSERT INTO reviews (user_id, product_id, order_no, rating, comment) 
                       VALUES ('$uid', '$product_id', '$orderno', '$rating', '$comment')";

        if (mysqli_query($conn, $insert_sql)) {
            $message = "Thank you! Your review has been submitted successfully.";
            $message_type = "success";
            
            // OPTIONAL: Redirect after successful submission to prevent re-submitting on refresh
            // header("Location: myorders.php?review_status=submitted");
            // exit;
            
        } else {
            // Check for duplicate key error (if the user submits twice for the same order item)
            if (mysqli_errno($conn) == 1062) {
                $message = "You have already submitted a review for this item on this order.";
                $message_type = "warning";
            } else {
                $message = "An error occurred while submitting your review: " . mysqli_error($conn);
                $message_type = "error";
            }
        }
    }
}

// ------------------------------------
// 3. FETCH PRODUCT DETAILS FOR DISPLAY
// ------------------------------------
$product_sql = "SELECT product_name, product_image FROM product WHERE product_id = $product_id";
$product_result = mysqli_query($conn, $product_sql);
if (mysqli_num_rows($product_result) == 0) {
    echo "<script>alert('Error: Product not found.'); window.location.href='myorders.php';</script>";
    exit;
}
$product_data = mysqli_fetch_assoc($product_result);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Review - Aroma Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
    <style>
        /* Hide default radio buttons */
        .rating-input {
            display: none;
        }
        /* Style the star labels */
        .star-rating label {
            color: #d1d5db; /* Default grey color */
            cursor: pointer;
            font-size: 2rem;
            transition: color 0.2s ease-in-out;
        }
        /* Color the star when hovered/checked */
        .star-rating input:checked ~ label, 
        .star-rating input:checked + label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #f97316; /* Primary orange color */
        }
        /* Reverse order for rating selection logic */
        .star-rating {
            direction: rtl;
            unicode-bidi: bidi-override;
        }
        .star-rating > label {
            display: inline-block;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white p-8 rounded-xl shadow-2xl border border-gray-100">
            <h1 class="text-3xl font-bold text-gray-900 mb-6 border-b pb-4">
                <i class="fas fa-star text-primary mr-3"></i> Submit Your Review
            </h1>

            <div class="flex items-center space-x-6 mb-8 p-4 bg-gray-50 rounded-lg border">
                <img src="uploads/<?php echo htmlspecialchars($product_data['product_image']); ?>" alt="Product Image" class="w-20 h-20 object-cover rounded-lg border-2 border-primary/50">
                <div>
                    <p class="text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($product_data['product_name']); ?></p>
                    <p class="text-sm text-gray-500">Order #<?php echo $orderno; ?></p>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="p-4 mb-6 rounded-lg 
                    <?php 
                        if ($message_type == 'success') echo 'bg-green-100 text-green-800 border-green-300';
                        elseif ($message_type == 'error') echo 'bg-red-100 text-red-800 border-red-300';
                        elseif ($message_type == 'warning') echo 'bg-yellow-100 text-yellow-800 border-yellow-300';
                    ?>
                border" role="alert">
                    <i class="fas <?php echo ($message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'); ?> mr-2"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="add_review.php?product=<?php echo $product_id; ?>&order=<?php echo $orderno; ?>">
                
                <div class="mb-8">
                    <label class="block text-lg font-medium text-gray-700 mb-3">Your Rating</label>
                    <div class="star-rating space-x-1">
                        <input type="radio" id="5-star" name="rating" value="5" class="rating-input" required>
                        <label for="5-star" class="fas fa-star"></label>
                        <input type="radio" id="4-star" name="rating" value="4" class="rating-input">
                        <label for="4-star" class="fas fa-star"></label>
                        <input type="radio" id="3-star" name="rating" value="3" class="rating-input">
                        <label for="3-star" class="fas fa-star"></label>
                        <input type="radio" id="2-star" name="rating" value="2" class="rating-input">
                        <label for="2-star" class="fas fa-star"></label>
                        <input type="radio" id="1-star" name="rating" value="1" class="rating-input">
                        <label for="1-star" class="fas fa-star"></label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="comment" class="block text-lg font-medium text-gray-700 mb-2">Your Comments (Optional)</label>
                    <textarea id="comment" name="comment" rows="6" 
                              class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md p-3" 
                              placeholder="Tell us what you liked or disliked about the product..."></textarea>
                </div>

                <div class="flex justify-end space-x-4 pt-4 border-t">
                    <a href="myorders.php" class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancel / Go Back
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-secondary transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Review
                    </button>
                </div>
            </form>
        </div>
    </main>

    <?php include "footer.php"; ?>
</body>
</html>