<?php
include "header.php";
include "../connection.php";

// --- STATUS UPDATE LOGIC: MARK AS DELIVERED (Status 4) ---
if (isset($_GET['action']) && $_GET['action'] == 'delivered' && isset($_GET['orderno'])) {
    $orderno = mysqli_real_escape_string($conn, $_GET['orderno']);
    $new_status = 4; // Assuming 4 is the status for 'Delivered'

    // Update the status in the orders table
    $update_sql = "UPDATE orders SET status = $new_status WHERE orderno = '$orderno'";
    
    if (mysqli_query($conn, $update_sql)) {
        // Success message and redirect to clean the URL
        echo "<script>alert('Order #$orderno successfully marked as DELIVERED!'); window.location.href='shipped.php';</script>";
        exit;
    } else {
        // Error handling
        echo "<script>alert('Error marking order #$orderno as delivered: " . mysqli_error($conn) . "'); window.location.href='shipped.php';</script>";
        exit;
    }
}
// --- END STATUS UPDATE LOGIC ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipped Orders - Aroma Hub Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
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
            /* Consistent orange/red gradient */
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
        .badge-shipped {
            /* Green gradient for Shipped status */
            background: linear-gradient(135deg, #10b981, #059669); 
        }
        .table-header {
            /* Consistent light orange/red gradient for table header */
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
        }
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
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Orders</span>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="ml-1 text-sm font-medium text-primary md:ml-2">Shipped Orders</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 hero-gradient rounded-xl flex items-center justify-center">
                        <i class="fas fa-truck-moving text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Shipped Orders</h1>
                        <p class="text-gray-600 mt-1">Review orders that are currently shipped and awaiting final delivery confirmation.</p>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="flex items-center space-x-2">
                        <span class="badge-shipped text-white px-4 py-2 rounded-full text-sm font-medium">
                            <?php
                            // Query for count of SHIPPED orders (status=3)
                            $count_sql = "SELECT COUNT(*) as count FROM orders WHERE status=3";
                            $count_result = mysqli_query($conn, $count_sql);
                            $count_row = mysqli_fetch_assoc($count_result);
                            echo $count_row['count'];
                            ?> Shipped Orders
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-truck-moving text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Shipped Orders</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php echo $count_row['count']; ?>
                        </p>
                    </div>
                </div>
            </div>

            <?php
            // Query for today's SHIPPED orders (status=3)
            $today_sql = "SELECT COUNT(*) as today_count FROM orders WHERE status=3 AND DATE(dt) = CURDATE()";
            $today_result = mysqli_query($conn, $today_sql);
            $today_row = mysqli_fetch_assoc($today_result);
            ?>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today's Shipped</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $today_row['today_count']; ?></p>
                    </div>
                </div>
            </div>

            <?php
            // Query for total value and count of SHIPPED orders (status=3) - FIXED to use the primary orders.total_amount
            // This query now returns a single row with the aggregate sum and count.
            $total_value_sql = "SELECT SUM(total_amount) as total_value, COUNT(*) as order_count FROM orders WHERE status=3";
            $total_value_result = mysqli_query($conn, $total_value_sql);
            
            // Fetch the single result row
            $value_row = mysqli_fetch_assoc($total_value_result);

            // Assign variables directly from the single-row result
            $total_value_sum = $value_row['total_value'] ?: 0;
            $order_count = $value_row['order_count'] ?: 0;
            ?>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Value (Shipped)</p>
                        <p class="text-2xl font-bold text-gray-900">₹<?php echo number_format($total_value_sum, 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Avg Order Value</p>
                        <p class="text-2xl font-bold text-gray-900">
                            ₹<?php echo $order_count > 0 ? number_format($total_value_sum / $order_count, 2) : '0.00'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="table-header px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Current Shipments List</h2>
                    <div class="flex items-center space-x-3">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                        <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                S.No
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Shipped Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order Number
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Amount
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        // Main query for SHIPPED orders (status=3) - FIXED to include orders without 'cart' entries
                        // We now use orders.total_amount for the total, and removed the unnecessary join/grouping on the 'cart' table.
                        $sql = "SELECT orders.dt, orders.orderno, orders.total_amount as total, user.username 
                                FROM orders 
                                INNER JOIN user ON orders.userid=user.userid 
                                WHERE orders.status=3 
                                ORDER BY orders.dt DESC";
                        $result = mysqli_query($conn, $sql);
                        $i = 0;

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $i++;
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo $i; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-check mr-2 text-green-500"></i>
                                    <?php echo date('M d, Y H:i', strtotime($row['dt'])); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mr-3">
                                        <span class="text-white font-medium text-sm">
                                            <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['username']); ?></div>
                                        <div class="text-sm text-gray-500">Customer</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        #<?php echo htmlspecialchars($row['orderno']); ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <i class="fas fa-rupee-sign mr-1 text-primary"></i>
                                    <span class="font-semibold text-primary">₹<?php echo number_format($row['total'], 2); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="viewdetails.php?orderno=<?php echo htmlspecialchars($row['orderno']); ?>" 
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Details
                                    </a>
                                    
                                    <a href="shipped.php?action=delivered&orderno=<?php echo htmlspecialchars($row['orderno']); ?>" 
                                        onclick="return confirm('Are you sure you want to mark Order #<?php echo htmlspecialchars($row['orderno']); ?> as DELIVERED? This action cannot be undone and will move the order to the Delivered list.');"
                                        class="inline-flex items-center px-3 py-2 border border-green-500 text-sm leading-4 font-medium rounded-lg text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Mark as Delivered
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-check-double text-green-500 text-2xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">No active shipments to display</h3>
                                    <p class="text-sm text-gray-500">All recent orders are either still processing or have been delivered.</p>
                                </div>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-search-location mr-2 text-primary"></i>
                    Bulk Tracking Update
                </button>
                <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-history mr-2 text-blue-600"></i>
                    View Past Archive
                </button>
                <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-file-csv mr-2 text-secondary"></i>
                    Generate Sales Report
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add any necessary JavaScript initialization here
        });
    </script>
</body>
</html>

<?php
include "footer.php";
?>