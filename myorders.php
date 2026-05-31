<?php
// Ensure session is started to get user ID
// If header.php starts the session, keep this commented. 
/*
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
*/
include "header.php";
include "connection.php";

// Check if user is logged in
if (!isset($_SESSION["userid"])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION["userid"];

// Calculate total orders and total spent for the Hero section stats
$total_orders_result = mysqli_query($conn, "SELECT COUNT(orderno) AS total_orders FROM orders WHERE status <> 0 AND userid = '$uid'");
$total_orders_row = mysqli_fetch_assoc($total_orders_result);
$total_orders = $total_orders_row['total_orders'];

$total_spent_result = mysqli_query($conn, "SELECT SUM(total_amount) AS total_spent FROM orders WHERE userid = '$uid' AND status >= 2");
$total_spent_row = mysqli_fetch_assoc($total_spent_result);
$total_spent = $total_spent_row['total_spent'] ? $total_spent_row['total_spent'] : 0.00;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Aroma Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
        .order-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        /* Status colors remain fixed for standard identification */
        .status-1 { /* Order Placed */
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        .status-2 { /* Processed */
             background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        .status-3 { /* Shipped */
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        .status-4 { /* Delivered */
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .status-5 { /* Cancelled */
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        /* 🩶 FULL GREY BANNER 🩶 */
        .progress-bar {
            /* Subtle light grey/silver for progress accent */
            background: linear-gradient(90deg, #9CA3AF 0%, #E5E7EB 100%); 
        }
        
        .hero-gradient {
            /* A subtle gradient from a medium grey to a darker charcoal */
            background: linear-gradient(160deg, #6B7280 0%, #374151 100%); 
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <section class="hero-gradient text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">My Orders</h1>
                    <p class="text-white/90 mb-4 md:mb-0">Track and manage your spice orders</p>
                </div>
                <div class="flex items-center space-x-6 text-white/80">
                    <div class="text-center">
                        <div class="text-2xl font-bold" id="total-orders"><?php echo $total_orders; ?></div>
                        <div class="text-sm">Total Orders</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">₹<?php echo number_format($total_spent, 2); ?></div>
                        <div class="text-sm">Total Spent</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

   <div class="bg-white border-b sticky top-0 z-40"> 
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="relative w-full sm:w-64">
                    <input type="text" id="order-search" placeholder="Search orders..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <select id="status-filter" class="border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary/20">
                    <option value="">All Status</option>
                    <option value="4">Delivered</option>
                    <option value="2">Processed</option>
                    <option value="3">Shipped</option>
                    <option value="5">Cancelled</option>
                    <option value="1">Placed</option>
                </select>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6" id="orders-list">
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-spinner fa-spin text-3xl"></i>
                <p class="mt-2">Loading orders...</p>
            </div>
        </div>

        <div class="text-center mt-12 hidden" id="load-more-container">
            <button class="px-8 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Load More Orders
            </button>
        </div>
    </main>

    <?php include "footer.php"; ?>

    <script>
        // --- AJAX & SEARCH/FILTER LOGIC ---
        function loadOrders() {
            const searchTerm = $('#order-search').val();
            const statusFilter = $('#status-filter').val();

            $('#orders-list').html('<div class="text-center py-12 text-gray-500"><i class="fas fa-spinner fa-spin text-3xl"></i><p class="mt-2">Loading orders...</p></div>');

            $.ajax({
                url: 'fetch_orders.php',
                type: 'GET',
                data: { 
                    user_id: '<?php echo $uid; ?>',
                    search: searchTerm,
                    status: statusFilter
                },
                success: function(response) {
                    $('#orders-list').html(response);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                    $('#orders-list').html('<div class="text-center py-12 text-red-500"><i class="fas fa-exclamation-triangle text-3xl"></i><p class="mt-2">Failed to load orders. Check the console for fetch_orders.php error.</p></div>');
                }
            });
        }

        $(document).ready(function() {
            loadOrders();

            let searchTimeout;
            $('#order-search').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(loadOrders, 500); 
            });

            $('#status-filter').on('change', function() {
                loadOrders();
            });

            $(document).on('click', '.cancel-link', function() {
                const orderno = $(this).data('orderno');
                return confirm('Are you sure you want to cancel order #' + orderno + '? This action cannot be undone.');
            });
        });

    </script>
</body>
</html>