<?php
include "header.php";
include "../connection.php";

$ono = $_GET['orderno'];
$sql = "SELECT orders.address, orders.town, orders.postcode, orders.state, orders.total_amount, orders.dt, orders.status, user.username, user.email, user.phonenumber FROM orders INNER JOIN user ON orders.userid = user.userid WHERE orders.orderno = '$ono'";

$rs = mysqli_query($conn, $sql);

if (isset($_POST['save'])) {
    $q = "UPDATE orders SET status = 2 WHERE orderno = '$ono'";
    mysqli_query($conn, $q);
    echo "<script>alert('Order Processed Successfully');window.location='neworders.php';</script>";
}

// Get order details
$order_details = mysqli_fetch_assoc($rs);
mysqli_data_seek($rs, 0); // Reset pointer for the while loop later

// --- FIX 1: Use a single query to get total items (from cart) and total amount (from orders) ---
$summary_sql = "SELECT 
                    (SELECT COUNT(*) FROM cart WHERE orderno='$ono') AS item_count, 
                    orders.total_amount AS order_total_from_orders 
                FROM orders 
                WHERE orderno='$ono'";
$summary_result = mysqli_query($conn, $summary_sql);
$summary = mysqli_fetch_assoc($summary_result);
// --- END FIX 1 ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?php echo $ono; ?> - Aroma Hub Admin</title>
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
        .order-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-new { background-color: #dbeafe; color: #1e40af; }
        .status-processing { background-color: #fef3c7; color: #92400e; }
        .status-shipped { background-color: #e0e7ff; color: #5b21b6; }
        .status-completed { background-color: #d1fae5; color: #065f46; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="admin-dashboard.html" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary">
                            <i class="fas fa-home mr-2"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="new-orders.php" class="text-sm font-medium text-gray-700 hover:text-primary">New Orders</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="ml-1 text-sm font-medium text-primary md:ml-2">Order #<?php echo $ono; ?></span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 hero-gradient rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-invoice text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="text-lg font-medium text-gray-600">Order #<?php echo $ono; ?></span>
                            <?php
                            $status_classes = [
                                1 => 'status-new',
                                2 => 'status-processing', 
                                3 => 'status-shipped',
                                4 => 'status-completed'
                            ];
                            $status_names = [
                                1 => 'New Order',
                                2 => 'Processing',
                                3 => 'Shipped', 
                                4 => 'Completed'
                            ];
                            $status = $order_details['status'];
                            ?>
                            <span class="status-badge <?php echo $status_classes[$status]; ?>">
                                <i class="fas fa-circle mr-2" style="font-size: 6px;"></i>
                                <?php echo $status_names[$status]; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 lg:mt-0">
                    <div class="flex items-center space-x-3">
                        <a href="new-orders.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Orders
                        </a>
                        <?php if ($status == 1): ?>
                        <form method="post" class="inline">
                            <button type="submit" name="save" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg hover:from-primary/90 hover:to-secondary/90">
                                <i class="fas fa-check mr-2"></i>
                                Mark as Processed
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Order Date</p>
                        <p class="text-lg font-bold text-gray-900"><?php echo date('M d, Y', strtotime($order_details['dt'])); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Items</p>
                        <p class="text-lg font-bold text-gray-900"><?php echo $summary['item_count']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-rupee-sign text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Order Total</p>
                        <p class="text-lg font-bold text-green-600">₹<?php echo number_format($order_details['total_amount'], 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-truck text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Status</p>
                        <p class="text-lg font-bold text-gray-900"><?php echo $status_names[$status]; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary/10 to-secondary/10 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-user mr-3 text-primary"></i>
                            Customer Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <?php 
                        // Note: mysqli_data_seek( $rs, 0 ) was called above to reset the pointer.
                        $rs->data_seek(0); // Ensure pointer is at the start (alternative to mysqli_data_seek)
                        if ($row = mysqli_fetch_assoc($rs)) { // Fetch the first row again
                        ?>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">
                                        <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                                    </span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($row['username']); ?></h3>
                                    <p class="text-sm text-gray-500">Customer</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-envelope text-gray-400 w-5"></i>
                                    <span class="text-sm text-gray-600"><?php echo htmlspecialchars($row['email']); ?></span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-phone text-gray-400 w-5"></i>
                                    <span class="text-sm text-gray-600"><?php echo htmlspecialchars($row['phonenumber']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-primary"></i>
                                Shipping Address
                            </h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                <p><?php echo htmlspecialchars($row['address']); ?></p>
                                <p><?php echo htmlspecialchars($row['town']); ?>, <?php echo htmlspecialchars($row['state']); ?></p>
                                <p>PIN: <?php echo htmlspecialchars($row['postcode']); ?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary/10 to-secondary/10 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-shopping-cart mr-3 text-primary"></i>
                            Order Items
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Product
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rate
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $j = 0;
                                // This query only finds individual products, so it will be empty for custom orders.
                                // We keep it this way because the custom order details are not in 'cart'.
                                $qry = "SELECT cart.product_id, cart.rate, cart.total, cart.quantity, product.product_name 
                                       FROM cart 
                                       INNER JOIN product ON cart.product_id=product.product_id 
                                       WHERE cart.orderno='$ono'";
                                $rr = mysqli_query($conn, $qry);
                                
                                // Reset grand_total to 0 for the loop, as the true total is in $order_details['total_amount']
                                $grand_total_cart = 0; 
                                
                                if (mysqli_num_rows($rr) > 0) {
                                    while ($data = mysqli_fetch_assoc($rr)) {
                                        $j++;
                                        $grand_total_cart += $data['total'];
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-primary to-secondary rounded-lg flex items-center justify-center mr-4">
                                                <span class="text-white font-bold text-sm"><?php echo $j; ?></span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($data['product_name']); ?></div>
                                                <div class="text-sm text-gray-500">ID: <?php echo $data['product_id']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₹<?php echo number_format($data['rate'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo $data['quantity']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                        ₹<?php echo number_format($data['total'], 2); ?>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    // Case for Custom/Gift Box Order (no items in cart table)
                                ?>
                                <tr class="bg-yellow-50">
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-yellow-800">
                                        <i class="fas fa-gift mr-2"></i>
                                        This order appears to be a **Customized Gift Box** or special order. Individual product details are not stored in the standard cart table. The total amount is **₹<?php echo number_format($order_details['total_amount'], 2); ?>**.
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                        Grand Total:
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-green-600">
                                        ₹<?php echo number_format($order_details['total_amount'], 2); ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($order_details['status'] == 1): ?>
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Actions</h3>
            <div class="flex flex-col sm:flex-row gap-4">
                <form method="post" class="flex-1">
                    <button type="submit" name="save" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg hover:from-primary/90 hover:to-secondary/90 transition-all duration-200">
                        <i class="fas fa-check-circle mr-2"></i>
                        Mark as Processed
                    </button>
                </form>
                <button class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all duration-200">
                    <i class="fas fa-print mr-2"></i>
                    Print Order
                </button>
                <button class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all duration-200">
                    <i class="fas fa-envelope mr-2"></i>
                    Email Customer
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth transitions and interactions
            const actionButtons = document.querySelectorAll('button, a');
            actionButtons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-1px)';
                });
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>

<?php
include "footer.php";
?>