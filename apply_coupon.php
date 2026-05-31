<?php
// apply_coupon.php

session_start();
include 'connection.php'; 

// Safety check
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code']) && isset($_POST['total'])) {
    
    $promo_code = trim(strtoupper($_POST['code']));
    // Ensure the received total is treated as a float for calculation
    $current_total = floatval($_POST['total']); 
    $discount_amount = 0.00;
    
    if ($promo_code === "AROMASPICES50") {
        $discount_amount = 50.00; 
        $new_total_raw = $current_total - $discount_amount;
        if ($new_total_raw < 0) $new_total_raw = 0.00;

        $_SESSION['coupon_discount'] = $discount_amount; 
        $_SESSION['coupon_code'] = $promo_code;
        
        echo json_encode([
            'success' => true,
            'message' => "Promo applied! ₹{$discount_amount} off your order.",
            // Return both the raw number and the formatted string for flexibility
            'new_total_raw' => $new_total_raw, 
            'new_total_formatted' => number_format($new_total_raw, 2),
            'discount' => number_format($discount_amount, 2),
        ]);
        
    } else {
        unset($_SESSION['coupon_discount']);
        unset($_SESSION['coupon_code']);
        
        echo json_encode([
            'success' => false,
            'message' => 'Invalid promo code.',
            'new_total_formatted' => number_format($current_total, 2), 
            'discount' => number_format(0, 2),
        ]);
    }
    
    exit;
}
?>