<?php
include "header.php";
include "../connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Orders - Aroma Hub Admin</title>
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
        .badge-new {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }
        .table-header {
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
                            <span class="ml-1 text-sm font-medium text-primary md:ml-2">New Orders</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 hero-gradient rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">New Orders</h1>
                        <p class="text-gray-600 mt-1">Manage recently placed orders awaiting processing</p>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="flex items-center space-x-2">
                        <span class="badge-new text-white px-4 py-2 rounded-full text-sm font-medium">
                            <?php
                            // Query 1: Count of new orders (no change needed here)
                            $count_sql = "SELECT COUNT(*) as count FROM orders WHERE status=1";
                            $count_result = mysqli_query($conn, $count_sql);
                            $count_row = mysqli_fetch_assoc($count_result);
                            echo $count_row['count'];
                            ?> New Orders
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
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total New Orders</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php echo $count_row['count']; ?>
                        </p>
                    </div>
                </div>
            </div>

            <?php
            $today_sql = "SELECT COUNT(*) as today_count FROM orders WHERE status=1 AND DATE(dt) = CURDATE()";
            $today_result = mysqli_query($conn, $today_sql);
            $today_row = mysqli_fetch_assoc($today_result);
            ?>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today's Orders</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $today_row['today_count']; ?></p>
                    </div>
                </div>
            </div>

            <?php
            // FIX 1: Use orders.total_amount directly for total value
            $total_value_sql = "SELECT SUM(total_amount) as total_value FROM orders WHERE status=1";
            $total_value_result = mysqli_query($conn, $total_value_sql);
            $total_value_row = mysqli_fetch_assoc($total_value_result);
            ?>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Value</p>
                        <p class="text-2xl font-bold text-gray-900">₹<?php echo number_format($total_value_row['total_value'] ?: 0, 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Avg Order Value</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php echo $count_row['count'] > 0 ? '₹' . number_format(($total_value_row['total_value'] ?: 0) / $count_row['count'], 2) : '₹0.00'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="table-header px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">New Orders List</h2>
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
                                Order Date
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
                        // FIX 2: Corrected SQL query to include all new orders
                        // Uses orders.total_amount and removes the INNER JOIN cart and GROUP BY clause
                        $sql = "SELECT 
                                    orders.dt, 
                                    orders.orderno, 
                                    orders.total_amount AS total, 
                                    user.username 
                                FROM orders 
                                INNER JOIN user ON orders.userid = user.userid 
                                WHERE orders.status = 1 
                                ORDER BY orders.dt DESC";
                        $result = mysqli_query($conn, $sql);
                        $i = 0;

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $i++;
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo $i; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                    <?php echo date('M d, Y', strtotime($row['dt'])); ?>
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        #<?php echo $row['orderno']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <i class="fas fa-rupee-sign mr-1 text-green-600"></i>
                                    <span class="font-semibold text-green-600"><?php echo number_format($row['total'], 2); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="viewdetails.php?orderno=<?php echo $row['orderno']; ?>" 
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Details
                                    </a>
                                    <button class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200">
                                        <i class="fas fa-cog mr-2"></i>
                                        Process
                                    </button>
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
                                        <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">No new orders</h3>
                                    <p class="text-sm text-gray-500">There are currently no new orders to display.</p>
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
                    <i class="fas fa-check-circle mr-2 text-green-600"></i>
                    Process All Orders
                </button>
                <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-envelope mr-2 text-blue-600"></i>
                    Send Notifications
                </button>
                <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-print mr-2 text-purple-600"></i>
                    Print Labels
                </button>
            </div>
        </div>
    </div>

    <script>
        // Add any JavaScript functionality here
        document.addEventListener('DOMContentLoaded', function() {
            // You can add interactive features like:
            // - Row selection
            // - Bulk actions
            // - Real-time updates
            // - Filters and sorting
        });
    </script>
</body>
</html>

<?php
include "footer.php";
?>