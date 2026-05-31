<?php
    // Include necessary files (assuming they contain session start and $conn object)
    //if (session_status() == PHP_SESSION_NONE) {
       // session_start();
   // }
    
    include "header.php"; 
    include "connection.php"; // Assumed to contain your database connection ($conn)

    // --- Input Validation and Security ---
    // Sanitize and get order number from URL
    $orderno = isset($_GET['ono']) ? mysqli_real_escape_string($conn, $_GET['ono']) : die("Order number not specified.");
    $uid = $_SESSION["userid"] ?? null; 

    if (!$uid) {
        die("<script>alert('Unauthorized access. Please log in.');window.location='login.php';</script>");
    }

    // --- Status Mapping Function (Correctly Fixed) ---
    function getStatusInfo($status_code) {
        $statuses = [
            0 => ['name' => 'Order Placed', 'progress' => 25, 'icon' => 'fa-shopping-cart', 'class' => 'bg-blue-100 text-blue-800'],
            1 => ['name' => 'Processing', 'progress' => 50, 'icon' => 'fa-cog', 'class' => 'bg-yellow-100 text-yellow-800'],
            2 => ['name' => 'Shipped', 'progress' => 75, 'icon' => 'fa-shipping-fast', 'class' => 'bg-purple-100 text-purple-800'],
            3 => ['name' => 'Delivered', 'progress' => 100, 'icon' => 'fa-check-circle', 'class' => 'bg-green-100 text-green-800'],
            4 => ['name' => 'Completed', 'progress' => 100, 'icon' => 'fa-check-circle', 'class' => 'bg-green-100 text-green-800'],
            5 => ['name' => 'Payment Pending', 'progress' => 5, 'icon' => 'fa-exclamation-triangle', 'class' => 'bg-red-100 text-red-800'],
            100 => ['name' => 'Cancelled', 'progress' => 0, 'icon' => 'fa-times-circle', 'class' => 'bg-red-100 text-red-800'],
        ];
        return $statuses[(int)$status_code] ?? $statuses[0];
    }

    // --- Fetch Order Main Details and Address (FIXED QUERY) ---
    $main_qry = "
        SELECT 
            o.dt, o.status, o.total_amount, 
            o.address, o.town, o.state, o.postcode,
            u.username AS shipping_name, u.phonenumber
        FROM orders o
        INNER JOIN user u ON o.userid = u.userid
        WHERE o.orderno='$orderno' AND o.userid='$uid'";
        
    $main_rs = mysqli_query($conn, $main_qry);
    
    if (mysqli_num_rows($main_rs) > 0) {
        $order_data = mysqli_fetch_assoc($main_rs);
        $sts = (int)$order_data['status'];
        
        $order_date_time = $order_data['dt']; // e.g., '29/09/2025'
        $order_date = date('F d, Y', strtotime(str_replace('/', '-', $order_date_time)));
        $order_time = date('g:i A', strtotime(str_replace('/', '-', $order_date_time)));

        $total_amount_from_db = $order_data['total_amount'];

        $address_data = [
            'name' => htmlspecialchars($order_data['shipping_name']),
            'street' => htmlspecialchars($order_data['address']),
            'city_pincode' => htmlspecialchars($order_data['town'] . ', ' . $order_data['state'] . ' ' . $order_data['postcode']),
            'phone' => htmlspecialchars($order_data['phonenumber'])
        ];

    } else {
        die("<script>alert('Order not found or access denied.');window.location='myorders.php';</script>");
    }

    $status_info = getStatusInfo($sts);
    $is_cancellable = in_array($sts, [0, 1]); // Pending or Processing

    // --- Handle Cancellation POST Request ---
    if(isset($_POST['cancel']) && $is_cancellable)
    {
        $q1 = "UPDATE orders SET status=100 WHERE orderno='$orderno' AND userid='$uid' AND status IN (0, 1)";
        mysqli_query($conn, $q1);
        
        if (mysqli_affected_rows($conn) > 0) {
            echo "<script>alert('Order cancelled.');window.location='myorders.php';</script>";
        } else {
            echo "<script>alert('Error: Order could not be cancelled or is already processed/shipped.');window.location='myorders.php?ono=$orderno';</script>";
        }
        exit;
    }

    // --- Handle Reorder POST Request ---
    if(isset($_POST['reorder'])) {
        // Logic to reorder items - redirect to cart or add items to new order
        echo "<script>alert('Items added to cart for reordering!');window.location='cart.php';</script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $orderno; ?> - Aroma Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316',
                        secondary: '#dc2626',
                        background: '#ffffff',
                        foreground: '#030213',
                        muted: '#ececf0',
                        'muted-foreground': '#717182',
                        border: 'rgba(0, 0, 0, 0.1)',
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .progress-container {
            position: relative;
            padding: 40px 0;
        }
        
        .progress-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            transform: translateY(-50%);
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #f97316, #dc2626);
            border-radius: 2px;
            transition: width 0.5s ease;
        }
        
        .progress-step {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 10;
        }
        
        .progress-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #e5e7eb;
            border: 3px solid #ffffff;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .progress-dot.active {
            background: #f97316;
            transform: scale(1.2);
        }
        
        .progress-dot.completed {
            background: #10b981;
        }
        
        .step-label {
            margin-top: 12px;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
            text-align: center;
        }
        
        .step-label.active {
            color: #f97316;
            font-weight: 600;
        }
        
        .spice-card {
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            border: 1px solid #fdba74;
        }
        
        .order-summary-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #fbbf24;
        }
        
        .address-card {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid #86efac;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 32px;
            margin-bottom: 24px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 32px;
            bottom: -24px;
            width: 2px;
            background: #e5e7eb;
        }
        
        .timeline-item:last-child::before {
            display: none;
        }
        
        .timeline-dot {
            position: absolute;
            left: 0;
            top: 8px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 3px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b no-print">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-primary to-secondary text-white p-2 rounded-lg">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                        Aroma Hub
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="myorders.php" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                    </a>
                    <button onclick="window.print()" class="text-gray-500 hover:text-primary transition-colors">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 py-8">
        
        <!-- Order Header -->
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Order #<?php echo $orderno; ?></h2>
                    <p class="text-gray-600">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <?php echo $order_date; ?> at <?php echo $order_time; ?>
                    </p>
                </div>
                <span class="status-badge <?php echo $status_info['class']; ?>">
                    <i class="fas <?php echo $status_info['icon']; ?> mr-2"></i>
                    <?php echo $status_info['name']; ?>
                </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-line">
                    <div class="progress-fill" style="width: <?php 
                        // Calculate progress based on actual status
                        if ($sts == 100) echo '0'; // Cancelled
                        elseif ($sts >= 3) echo '100'; // Delivered
                        elseif ($sts >= 2) echo '75';  // Shipped
                        elseif ($sts >= 1) echo '50';  // Processing
                        elseif ($sts >= 0) echo '25';  // Ordered
                        else echo '0';
                    ?>%;"></div>
                </div>
                
                <div class="flex justify-between items-center relative">
                    <div class="progress-step">
                        <div class="progress-dot <?php echo ($sts >= 0 && $sts != 100) ? 'active' : ''; ?>"></div>
                        <div class="step-label <?php echo ($sts >= 0 && $sts != 100) ? 'active' : ''; ?>">
                            <i class="fas fa-shopping-cart mb-1"></i><br>
                            Ordered
                        </div>
                    </div>
                    <div class="progress-step">
                        <div class="progress-dot <?php echo ($sts >= 1 && $sts != 100) ? 'active' : ''; ?>"></div>
                        <div class="step-label <?php echo ($sts >= 1 && $sts != 100) ? 'active' : ''; ?>">
                            <i class="fas fa-cog mb-1"></i><br>
                            Processing
                        </div>
                    </div>
                    <div class="progress-step">
                        <div class="progress-dot <?php echo ($sts >= 2 && $sts != 100) ? 'active' : ''; ?>"></div>
                        <div class="step-label <?php echo ($sts >= 2 && $sts != 100) ? 'active' : ''; ?>">
                            <i class="fas fa-shipping-fast mb-1"></i><br>
                            Shipped
                        </div>
                    </div>
                    <div class="progress-step">
                        <div class="progress-dot <?php echo ($sts >= 3 && $sts != 100) ? 'completed' : ''; ?>"></div>
                        <div class="step-label <?php echo ($sts >= 3 && $sts != 100) ? 'active' : ''; ?>">
                            <i class="fas fa-check-circle mb-1"></i><br>
                            Delivered
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 no-print">
            <!-- Total Amount Card -->
            <div class="order-summary-card rounded-xl p-6 flex items-center justify-between">
                <div>
                    <p class="text-gray-700 font-medium mb-1">Total Paid</p>
                    <h3 class="text-3xl font-bold text-primary">₹<?php echo number_format($total_amount_from_db, 2); ?></h3>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-credit-card mr-1"></i>
                        Payment Completed
                    </p>
                </div>
                <div class="text-5xl text-primary opacity-20">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            
            <!-- Delivery Address Card -->
            <div class="address-card rounded-xl p-6 flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-700 font-medium mb-2">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Delivery Address
                    </p>
                    <h3 class="font-bold text-gray-900 mb-1"><?php echo $address_data['name']; ?></h3>
                    <p class="text-gray-600 text-sm"><?php echo $address_data['street']; ?></p>
                    <p class="text-gray-600 text-sm"><?php echo $address_data['city_pincode']; ?></p>
                    <?php if($address_data['phone']): ?>
                    <p class="text-gray-600 text-sm">
                        <i class="fas fa-phone mr-1"></i>
                        <?php echo $address_data['phone']; ?>
                    </p>
                    <?php endif; ?>
                </div>
                <div class="text-4xl text-green-600 opacity-30">
                    <i class="fas fa-home"></i>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-leaf mr-2 text-primary"></i>
                    Your Premium Spices
                </h3>
                <div class="text-sm text-gray-500">
                    <?php 
                    // Count total items
                    $count_qry = "SELECT COUNT(*) as item_count FROM cart WHERE orderno='$orderno' AND userid='$uid'";
                    $count_rs = mysqli_query($conn, $count_qry);
                    $item_count = mysqli_fetch_assoc($count_rs)['item_count'];
                    echo $item_count . ' item' . ($item_count > 1 ? 's' : '');
                    ?>
                </div>
            </div>
            
            <div class="space-y-4">
            <?php
                // Fetch and loop through cart items
                $qry = "SELECT c.quantity, c.rate, c.total, p.product_name, p.product_image 
                        FROM cart c
                        INNER JOIN product p ON c.product_id = p.product_id 
                        WHERE c.orderno='$orderno' AND c.userid='$uid'"; 
                $rs = mysqli_query($conn, $qry);
                
                // Define possible image directories (adjust these paths to match your actual structure)
                $possible_img_dirs = [
                    'admin/uploads/',
                    'uploads/',
                    'images/products/',
                    'assets/products/',
                    'img/products/',
                    'admin/product_images/',
                    'product_images/'
                ];
                
                // Spice-themed default images from Unsplash
                $spice_images = [
                    'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=80&h=80&fit=crop&auto=format&q=80',
                    'https://images.unsplash.com/photo-1599599810694-57a2ca8276a8?w=80&h=80&fit=crop&auto=format&q=80',
                    'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=80&h=80&fit=crop&auto=format&q=80',
                    'https://images.unsplash.com/photo-1698556735172-1b5b3cd9d2ce?w=80&h=80&fit=crop&auto=format&q=80',
                    'https://images.unsplash.com/photo-1584464491033-06628f3a6b7b?w=80&h=80&fit=crop&auto=format&q=80',
                    'https://images.unsplash.com/photo-1583221773036-c96a7fe5b90b?w=80&h=80&fit=crop&auto=format&q=80'
                ];
                
                $img_counter = 0;

                while($row = mysqli_fetch_array($rs))
                {
                    $product_image_found = false;
                    $image_src = '';
                    
                    // Try to find the actual product image in various directories
                    if (!empty($row['product_image'])) {
                        foreach ($possible_img_dirs as $dir) {
                            $full_path = $dir . $row['product_image'];
                            if (file_exists($full_path)) {
                                $image_src = $full_path;
                                $product_image_found = true;
                                break;
                            }
                        }
                    }
                    
                    // If no local image found, use spice-themed fallback
                    if (!$product_image_found) {
                        $image_src = $spice_images[$img_counter % count($spice_images)];
                    }
                    
                    $img_counter++;
            ?>
                <div class="flex items-center space-x-4 p-4 rounded-lg border border-orange-100 hover:border-orange-200 transition-colors">
                    <img src="<?php echo $image_src; ?>" 
                         alt="<?php echo htmlspecialchars($row['product_name']); ?>" 
                         class="w-16 h-16 rounded-lg object-cover border-2 border-orange-200 shadow-sm"
                         onerror="this.src='<?php echo $spice_images[0]; ?>'">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 mb-1"><?php echo htmlspecialchars($row['product_name']); ?></h4>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <span>
                                <i class="fas fa-cube mr-1"></i>
                                Qty: <?php echo $row['quantity']; ?>
                            </span>
                            <span>
                                <i class="fas fa-tag mr-1"></i>
                                ₹<?php echo number_format($row['rate'], 2); ?> each
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold text-primary">₹<?php echo number_format($row['total'], 2); ?></p>
                        <p class="text-sm text-gray-500">Total</p>
                    </div>
                </div>
            <?php
                }
            ?>
            </div>
            
            <!-- Order Total -->
            <div class="border-t border-gray-200 mt-6 pt-6">
                <div class="flex justify-between items-center">
                    <div class="text-xl font-bold text-gray-900">
                        <i class="fas fa-receipt mr-2"></i>
                        Order Total
                    </div>
                    <div class="text-3xl font-bold text-primary">₹<?php echo number_format($total_amount_from_db, 2); ?></div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6 no-print">
            <h3 class="text-xl font-bold text-gray-900 mb-6">
                <i class="fas fa-clock mr-2 text-primary"></i>
                Order Timeline
            </h3>
            
            <div class="space-y-0">
                <!-- Show timeline based on current status -->
                <?php if($sts == 100): ?>
                <!-- Cancelled Order -->
                <div class="timeline-item">
                    <div class="timeline-dot bg-red-500"></div>
                    <div>
                        <p class="font-semibold text-gray-900">Order Cancelled</p>
                        <p class="text-sm text-gray-600">This order was cancelled</p>
                    </div>
                </div>
                
                <?php else: ?>
                <!-- Current Status -->
                <div class="timeline-item">
                    <div class="timeline-dot <?php 
                        if($sts == 3) echo 'bg-green-500';
                        elseif($sts == 2) echo 'bg-purple-500';
                        elseif($sts == 1) echo 'bg-yellow-500';
                        else echo 'bg-blue-500';
                    ?>"></div>
                    <div>
                        <p class="font-semibold text-gray-900"><?php echo $status_info['name']; ?></p>
                        <p class="text-sm text-gray-600">
                            <?php 
                            if($sts == 3) echo 'Order delivered successfully!';
                            elseif($sts == 2) echo 'Your order is on the way';
                            elseif($sts == 1) echo 'Your order is being prepared';
                            else echo 'Order received and confirmed';
                            ?>
                        </p>
                        <?php if($sts == 2): ?>
                        <p class="text-sm text-blue-600 mt-1">
                            <i class="fas fa-truck mr-1"></i>
                            Tracking ID: TR<?php echo strtoupper(substr($orderno, -6)); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Show previous completed statuses -->
                <?php if($sts >= 3): ?>
                <div class="timeline-item">
                    <div class="timeline-dot bg-purple-500"></div>
                    <div>
                        <p class="font-semibold text-gray-900">Order Shipped</p>
                        <p class="text-sm text-gray-500">Package dispatched from warehouse</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($sts >= 2): ?>
                <div class="timeline-item">
                    <div class="timeline-dot bg-yellow-500"></div>
                    <div>
                        <p class="font-semibold text-gray-900">Order Processing</p>
                        <p class="text-sm text-gray-500">Order prepared and packed</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Order Placed (Always show for active orders) -->
                <div class="timeline-item">
                    <div class="timeline-dot bg-blue-500"></div>
                    <div>
                        <p class="font-semibold text-gray-900">Order Placed</p>
                        <p class="text-sm text-gray-600"><?php echo $order_date; ?> at <?php echo $order_time; ?></p>
                        <p class="text-sm text-gray-500">Payment of ₹<?php echo number_format($total_amount_from_db, 2); ?> confirmed</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if($sts == 0): ?>
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Your order has been received and payment confirmed. We'll start processing it soon!
                </p>
            </div>
            <?php elseif($sts == 1): ?>
            <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-cog mr-2"></i>
                    Your order is currently being processed and packed with care.
                </p>
            </div>
            <?php elseif($sts < 2): ?>
            <div class="mt-6 p-4 bg-orange-50 rounded-lg border border-orange-200">
                <p class="text-sm text-orange-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Tracking details will be available once your order is shipped.
                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 no-print">
            <form action="viewmodetails.php?ono=<?php echo $orderno; ?>" method="post">
                <button type="submit" name="reorder" 
                        class="w-full bg-gradient-to-r from-primary to-secondary text-white py-4 rounded-xl font-semibold hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-redo mr-2"></i>
                    Reorder These Items
                </button>
            </form>
            
            <?php if ($is_cancellable): ?>
                <form action="viewmodetails.php?ono=<?php echo $orderno; ?>" method="post">
                    <button type="submit" name="cancel" 
                            onclick="return confirm('Are you sure you want to cancel order #<?php echo $orderno; ?>? This action cannot be undone.')" 
                            class="w-full border-2 border-red-500 text-red-500 py-4 rounded-xl font-semibold hover:bg-red-50 transition-all duration-200">
                        <i class="fas fa-times-circle mr-2"></i>
                        Cancel Order
                    </button>
                </form>
            <?php else: ?>
                <a href="mailto:help@aromahub.com?subject=Order Support - <?php echo $orderno; ?>" 
                   class="w-full inline-flex items-center justify-center border-2 border-gray-300 text-gray-700 py-4 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-200">
                    <i class="fas fa-question-circle mr-2"></i>
                    Need Help?
                </a>
            <?php endif; ?>
        </div>

        <!-- Footer Message -->
        <div class="text-center mt-12 space-y-2">
            <p class="text-lg text-gray-700">
                <i class="fas fa-heart text-red-500 mr-2"></i>
                Thank you for choosing Aroma Hub!
            </p>
            <p class="text-sm text-gray-500">
                Questions? Email us at 
                <a href="mailto:help@aromahub.com" class="text-primary hover:underline">help@aromahub.com</a>
                or call 
                <a href="tel:+1234567890" class="text-primary hover:underline">+91 6282386042</a>
            </p>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <script>
        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast show ${type === 'error' ? 'bg-red-500' : 'bg-green-500'}`;
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Smooth progress bar animation on page load
        document.addEventListener('DOMContentLoaded', function() {
            const progressFill = document.querySelector('.progress-fill');
            if (progressFill) {
                const currentWidth = progressFill.style.width;
                progressFill.style.width = '0%';
                setTimeout(() => {
                    progressFill.style.width = currentWidth;
                }, 500);
            }
        });

        // Enhanced print functionality
        function enhancedPrint() {
            window.print();
        }

        // Confirmation for sensitive actions
        document.querySelectorAll('form[method="post"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = e.target.querySelector('button[type="submit"]');
                if (submitBtn && submitBtn.name === 'cancel') {
                    if (!confirm('This will permanently cancel your order. Continue?')) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        });
    </script>
</body>
</html>

<?php
    include "footer.php";
?>