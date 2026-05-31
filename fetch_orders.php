<?php
// fetch_orders.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connection.php"; 

// IMPORTANT: Define the image path. Adjust 'uploads/' if your folder is named differently or located elsewhere.
define('IMAGE_BASE_PATH', 'uploads/'); 

// Get filter parameters
$uid = $_GET['user_id'] ?? $_SESSION['userid'] ?? null;
$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
$status_filter = mysqli_real_escape_string($conn, $_GET['status'] ?? '');

// Security Check
if (!$uid) {
    echo '<div class="text-center py-12 text-red-500">Authentication error. Please log in again.</div>';
    exit;
}

// Start building the query
$main_query = "SELECT orderno, dt, status, total_amount AS amount FROM orders WHERE status <> 0 AND userid = '$uid'";

// Add Search Filter Logic (by order number OR product name)
if (!empty($search)) {
    $main_query .= " AND (orderno LIKE '%$search%' OR orderno IN (
        SELECT orderno FROM cart 
        INNER JOIN product ON cart.product_id = product.product_id
        WHERE product.product_name LIKE '%$search%'
    ))";
}

// Add Status Filter Logic
if (!empty($status_filter)) {
    $main_query .= " AND status = '$status_filter'";
}

// Finalize query
$main_query .= " ORDER BY dt DESC";

$result = mysqli_query($conn, $main_query);

if (!$result) {
    echo '<div class="text-center py-12 text-red-500">Database query error.</div>';
    exit;
}

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $orderno = htmlspecialchars($row['orderno']);
        $order_date = date('F j, Y', strtotime($row['dt']));
        $status_code = (int)$row['status']; // Cast to integer for switch comparison
        $amount = number_format($row['amount'], 2);

        // --- Status Logic Generation ---
        $status_text = ''; $status_icon = ''; $status_class = ''; $action_buttons = ''; $progress_width = 0; 
        
        switch ($status_code) {
            case 1: 
                $status_text = 'Order Placed'; 
                $status_icon = 'fa-clock'; 
                $status_class = 'status-1 bg-blue-600'; 
                $progress_width = 25; 
                $action_buttons = '<a href="cancel_order.php?orderno='.$orderno.'" data-orderno="'.$orderno.'" class="cancel-link px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition-colors"><i class="fas fa-times mr-2"></i>Cancel</a>'; 
                break;
            case 2: 
                $status_text = 'Processed'; 
                $status_icon = 'fa-cogs'; 
                $status_class = 'status-2 bg-yellow-600'; 
                $progress_width = 50; 
                $action_buttons = '<button class="track-order px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors" data-order="'.$orderno.'"><i class="fas fa-truck mr-2"></i>Track Order</button>'; 
                break;
            case 3: 
                $status_text = 'Shipped'; 
                $status_icon = 'fa-shipping-fast'; 
                $status_class = 'status-3 bg-indigo-600'; 
                $progress_width = 75; 
                $action_buttons = '<button class="track-order px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" data-order="'.$orderno.'"><i class="fas fa-truck mr-2"></i>Track Package</button>'; 
                break;
            case 4: 
                $status_text = 'Delivered'; 
                $status_icon = 'fa-check-circle'; 
                $status_class = 'status-4 bg-green-600'; 
                $progress_width = 100; 
                $action_buttons = '<button class="reorder px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition-all" data-order="'.$orderno.'"><i class="fas fa-redo mr-2"></i>Reorder</button>'; 
                break;
            case 5: // Payment/Other Failure
                $status_text = 'Payment Failed'; 
                $status_icon = 'fa-exclamation-triangle'; 
                $status_class = 'status-5 bg-red-600'; 
                $progress_width = 0; 
                $action_buttons = '<button class="reorder px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition-all" data-order="'.$orderno.'"><i class="fas fa-redo mr-2"></i>Try Again</button>'; 
                break;
            case 100: // Cancelled
                $status_text = 'Cancelled'; 
                $status_icon = 'fa-times-circle'; 
                $status_class = 'status-cancelled bg-gray-500'; 
                $progress_width = 0; 
                $action_buttons = '<button class="reorder px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition-all" data-order="'.$orderno.'"><i class="fas fa-redo mr-2"></i>Reorder</button>'; 
                break;
            default: 
                $status_text = 'Unknown Status'; 
                $status_icon = 'fa-question-circle'; 
                $status_class = 'status-default bg-gray-500'; 
                break;
        }
        
        // Query to fetch up to 3 product previews. 
        // IMPORTANT: Added product.product_id to the select list for review linkage.
        $prod_qry = "SELECT product.product_id, product.product_name, product.product_image, cart.quantity, cart.rate
                     FROM cart 
                     INNER JOIN product ON cart.product_id = product.product_id
                     WHERE cart.orderno = '$orderno' LIMIT 3";
        $prod_res = mysqli_query($conn, $prod_qry);
        $product_previews = [];
        while($prod = mysqli_fetch_assoc($prod_res)) {
            $product_previews[] = $prod;
        }
?>

        <div class="order-card bg-white rounded-2xl p-6 mb-6 shadow-lg <?php if ($status_code == 100 || $status_code == 5) echo 'opacity-75'; ?>">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start space-y-4 lg:space-y-0">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">#<?php echo $orderno; ?></h3>
                            <p class="text-gray-600">Placed on <?php echo $order_date; ?></p>
                        </div>
                        <span class="<?php echo $status_class; ?> text-white px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas <?php echo $status_icon; ?> mr-1"></i>
                            <?php echo $status_text; ?>
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        <?php foreach ($product_previews as $prod): 
                            $product_id = $prod['product_id']; // Get product ID
                        ?>
                        <div class="flex items-center justify-between space-x-3 p-3 border border-gray-100 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <img src="<?php echo IMAGE_BASE_PATH . htmlspecialchars($prod['product_image']); ?>" alt="<?php echo htmlspecialchars($prod['product_name']); ?>" class="w-12 h-12 rounded object-cover border border-gray-100">
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($prod['product_name']); ?></p>
                                    <p class="text-sm text-gray-600">Qty: <?php echo htmlspecialchars($prod['quantity']); ?> × ₹<?php echo number_format($prod['rate'], 2); ?></p>
                                </div>
                            </div>
                            
                            <?php 
                            if ($status_code == 4) { // Status 4 is Delivered
                                // Check if a review already exists for this specific purchase
                                $check_review_sql = "SELECT review_id FROM reviews 
                                                     WHERE user_id = '{$uid}' 
                                                     AND product_id = '{$product_id}' 
                                                     AND order_no = '{$orderno}'"; 
                                $review_check_result = mysqli_query($conn, $check_review_sql);

                                if (mysqli_num_rows($review_check_result) == 0) {
                                    // Review NOT submitted: Show ADD REVIEW Button
                                    echo ' <a href="add_review.php?product=' . $product_id . '&order=' . $orderno . '" 
                                               class="text-sm font-medium text-primary hover:text-secondary whitespace-nowrap ml-auto flex items-center">
                                               <i class="fas fa-star text-base mr-1"></i> Add Review
                                            </a>';
                                } else {
                                    // Review submitted: Show Reviewed Status
                                    echo ' <span class="text-sm text-green-600 font-medium whitespace-nowrap ml-auto flex items-center">
                                               <i class="fas fa-check-circle text-base mr-1"></i> Reviewed
                                            </span>';
                                }
                            }
                            ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($status_code > 0 && $status_code < 4): ?>
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Order Progress</span>
                            <span class="text-sm text-gray-600"><?php echo $progress_width; ?>% Complete</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="progress-bar h-2 rounded-full bg-primary" style="width: <?php echo $progress_width; ?>%"></div>
                        </div>
                    </div>
                    <?php elseif ($status_code == 100 || $status_code == 5): ?>
                     <div class="bg-red-50 p-4 rounded-lg mb-4">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-info-circle text-red-600"></i>
                                <div>
                                    <p class="font-medium text-red-900"><?php echo $status_text; ?></p>
                                    <p class="text-sm text-red-700">This order is no longer active.</p>
                                </div>
                            </div>
                     </div>
                    <?php endif; ?>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="text-lg font-semibold text-gray-900">
                            Total: ₹<?php echo $amount; ?>
                        </div>
                        <div class="flex space-x-2">
                            <?php echo $action_buttons; ?>
                            <a href="viewmodetails.php?sts=<?php echo $row['status'] ?>&ono=<?php echo $orderno ?>" class="view-details px-4 py-2 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-all">
                                <i class="fas fa-eye mr-2"></i>
                                Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php
    } // End while loop
} else {
    // No orders found message
    echo '<div class="text-center py-12 bg-white rounded-2xl shadow-lg">
            <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700">No matching orders found</h3>
            <p class="text-gray-500 mt-2">Try adjusting your search or filter settings.</p>
          </div>';
}
?>