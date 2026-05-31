<?php
// CRITICAL: Start the session to access $_SESSION['cart']
session_start();

// Check if the user is logged in (optional, but good practice)
if (!isset($_SESSION['userid'])) {
    // Redirect or exit if not logged in
    header('Location: login.php');
    exit;
}

// 1. Check if the 'key' parameter is set in the URL
if (!isset($_GET['key'])) {
    // No key provided, redirect back to the cart with an error status
    $_SESSION['status'] = ['type' => 'error', 'message' => 'Invalid item removal request.'];
    header('Location: shoppingcart.php');
    exit;
}

// 2. Get the session key (the array index) and validate it
// We assume the key is a numeric index provided by the loop in shoppingcart.php
$session_key = $_GET['key'];

// 3. Check if the 'cart' session array exists and the key is valid
if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && array_key_exists($session_key, $_SESSION['cart'])) {
    
    // Optional: Get the name for a confirmation message
    $item_name = 'Custom Gift Box';
    if (isset($_SESSION['cart'][$session_key]['type']) && $_SESSION['cart'][$session_key]['type'] === 'custom_gift_box') {
        // Unset the specific index from the $_SESSION['cart'] array
        unset($_SESSION['cart'][$session_key]);

        // Re-index the array after deletion to prevent gaps that could break future loops
        // This is important because array_key_exists uses the original index.
        $_SESSION['cart'] = array_values($_SESSION['cart']);

        // Set success message
        $_SESSION['status'] = [
            'type' => 'success', 
            'message' => $item_name . ' successfully removed from your cart.'
        ];
    } else {
         // The key existed, but the type wasn't 'custom_gift_box' (safety check)
        $_SESSION['status'] = ['type' => 'error', 'message' => 'Item found, but not recognized as a custom box.'];
    }

} else {
    // Key was invalid or not found
    $_SESSION['status'] = ['type' => 'error', 'message' => 'Error: Could not find that item in your cart.'];
}

// 4. Redirect back to the shopping cart page
header('Location: shoppingcart.php');
exit;
?>