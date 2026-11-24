<?php
/**
 * Global Configuration and Reusable Helper Functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

// Dynamically determine BASE_URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Ensure BASE_URL points to the project root
$parts = explode('/', trim($scriptName, '/'));
$projectFolder = (!empty($parts[0])) ? '/' . $parts[0] . '/' : '/';
// Specifically for MS-RESTURANT if running under subfolder
if (stripos($_SERVER['REQUEST_URI'] ?? '', 'MS-RESTURANT') !== false) {
    $baseUrl = $protocol . $host . '/MS-RESTURANT/';
} else {
    $baseUrl = $protocol . $host . $projectFolder;
    if (substr($baseUrl, -1) !== '/') {
        $baseUrl .= '/';
    }
}
define('BASE_URL', $baseUrl);

/**
 * Fetch restaurant settings from database
 */
function getSettings($pdo) {
    static $settings = null;
    if ($settings === null) {
        try {
            $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
            $settings = $stmt->fetch();
            if (!$settings) {
                $settings = [
                    'restaurant_name' => 'Saffron & Spice Gourmet Restaurant',
                    'logo'            => 'logo.png',
                    'phone'           => '923213098649',
                    'email'           => 'mirzasaad1625397@gmail.com',
                    'whatsapp'        => '923213098649',
                    'address'         => 'Plot 42, Food Street, Block 7, Clifton, Karachi, Pakistan',
                    'opening_hours'   => 'Mon - Sun: 12:00 PM - 01:00 AM',
                    'delivery_fee'    => 150.00,
                    'currency_symbol' => 'Rs. ',
                    'facebook'        => 'https://facebook.com',
                    'instagram'       => 'https://instagram.com',
                    'website'         => 'http://localhost/MS-RESTURANT/'
                ];
            }
        } catch (Exception $e) {
            $settings = [
                'restaurant_name' => 'Saffron & Spice Gourmet Restaurant',
                'logo'            => 'logo.png',
                'phone'           => '923213098649',
                'email'           => 'mirzasaad1625397@gmail.com',
                'whatsapp'        => '923213098649',
                'address'         => 'Plot 42, Food Street, Block 7, Clifton, Karachi, Pakistan',
                'opening_hours'   => 'Mon - Sun: 12:00 PM - 01:00 AM',
                'delivery_fee'    => 150.00,
                'currency_symbol' => 'Rs. ',
                'facebook'        => 'https://facebook.com',
                'instagram'       => 'https://instagram.com',
                'website'         => 'http://localhost/MS-RESTURANT/'
            ];
        }
    }
    return $settings;
}

/**
 * Clean & sanitize user input
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency price
 */
function formatPrice($amount) {
    return 'Rs. ' . number_format((float)$amount, 2);
}

/**
 * Format date/time
 */
function formatDate($datetime) {
    if (!$datetime) return 'N/A';
    return date('M d, Y h:i A', strtotime($datetime));
}

/**
 * Set flash session message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Display flash session message
 */
function displayFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . htmlspecialchars($flash['type']) . ' alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-info me-2"></i>' . htmlspecialchars($flash['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

/**
 * Authentication Helpers
 */
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

function isAdmin() {
    return isset($_SESSION['user']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Shopping Cart Management (Stored in $_SESSION['cart'])
 * Format: [ food_id => quantity ]
 */
function initCart() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}
initCart();

function addToCart($food_id, $qty = 1) {
    initCart();
    $food_id = (int)$food_id;
    $qty = max(1, (int)$qty);
    if (isset($_SESSION['cart'][$food_id])) {
        $_SESSION['cart'][$food_id] += $qty;
    } else {
        $_SESSION['cart'][$food_id] = $qty;
    }
}

function updateCartQuantity($food_id, $qty) {
    initCart();
    $food_id = (int)$food_id;
    $qty = (int)$qty;
    if ($qty <= 0) {
        unset($_SESSION['cart'][$food_id]);
    } else {
        $_SESSION['cart'][$food_id] = $qty;
    }
}

function removeFromCart($food_id) {
    initCart();
    $food_id = (int)$food_id;
    if (isset($_SESSION['cart'][$food_id])) {
        unset($_SESSION['cart'][$food_id]);
    }
}

function clearCart() {
    $_SESSION['cart'] = [];
}

function getCartCount() {
    initCart();
    $count = 0;
    foreach ($_SESSION['cart'] as $qty) {
        $count += (int)$qty;
    }
    return $count;
}

/**
 * Fetch detailed cart items with food records from database
 */
function getCartDetails($pdo) {
    initCart();
    $items = [];
    $subtotal = 0.00;

    if (!empty($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $stmt = $pdo->prepare("SELECT f.*, c.name AS category_name FROM foods f LEFT JOIN categories c ON f.category_id = c.id WHERE f.id IN ($placeholders) AND f.status = 'active'");
        $stmt->execute($ids);
        $foods = $stmt->fetchAll();

        foreach ($foods as $food) {
            $qty = $_SESSION['cart'][$food['id']] ?? 1;
            $itemSubtotal = $food['price'] * $qty;
            $subtotal += $itemSubtotal;
            $items[] = [
                'id'       => $food['id'],
                'name'     => $food['name'],
                'price'    => $food['price'],
                'image'    => $food['image'],
                'category' => $food['category_name'],
                'quantity' => $qty,
                'subtotal' => $itemSubtotal
            ];
        }
    }

    $settings = getSettings($pdo);
    $deliveryFee = (float)($settings['delivery_fee'] ?? 150.00);
    $grandTotal = $subtotal > 0 ? ($subtotal + $deliveryFee) : 0.00;

    return [
        'items'        => $items,
        'subtotal'     => $subtotal,
        'delivery_fee' => $deliveryFee,
        'grand_total'  => $grandTotal
    ];
}

/**
 * Generate formatted WhatsApp click-to-chat URL
 */
function buildWhatsAppUrl($phone, $message) {
    // Clean phone number (strip non-digits, replace leading 0 with 92 if in Pakistan)
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    if (strpos($cleanPhone, '0') === 0 && strlen($cleanPhone) === 11) {
        $cleanPhone = '92' . substr($cleanPhone, 1);
    }
    $encodedMsg = rawurlencode($message);
    return "https://api.whatsapp.com/send?phone={$cleanPhone}&text={$encodedMsg}";
}

/**
 * Generate formatted WhatsApp message for order
 */
function formatWhatsAppOrderMessage($customerName, $phone, $deliveryAddress, $items, $deliveryFee, $grandTotal, $orderNumber = null) {
    $msg = "Hello, I want to place an order.\n\n";
    if ($orderNumber) {
        $msg .= "Order ID: " . $orderNumber . "\n";
    }
    $msg .= "Customer: " . $customerName . "\n";
    $msg .= "Phone: " . $phone . "\n\n";
    $msg .= "Order:\n";

    $i = 1;
    foreach ($items as $item) {
        $name = $item['name'] ?? $item['food_name'];
        $qty = $item['quantity'];
        $sub = $item['subtotal'] ?? ($item['price'] * $qty);
        $msg .= "{$i}. {$name} x{$qty} = Rs. " . number_format($sub, 0) . "\n";
        $i++;
    }

    $msg .= "\nDelivery Address:\n" . $deliveryAddress . "\n\n";
    if ($deliveryFee > 0) {
        $msg .= "Delivery Fee: Rs. " . number_format($deliveryFee, 0) . "\n";
    }
    $msg .= "Total: Rs. " . number_format($grandTotal, 0);

    return $msg;
}

/**
 * Generate single food quick inquiry/order WhatsApp message
 */
function formatWhatsAppFoodItemMessage($foodName, $price, $customerName = 'Guest') {
    $msg = "Hello, I want to order the following item:\n\n";
    $msg .= "Item: {$foodName}\n";
    $msg .= "Price: Rs. " . number_format($price, 0) . "\n\n";
    $msg .= "Customer: {$customerName}\n";
    $msg .= "Please confirm availability and delivery time.";
    return $msg;
}
