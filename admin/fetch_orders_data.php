<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$days = isset($_GET['days']) ? (int)$_GET['days'] : 7;
if (!in_array($days, [7, 30, 180, 365, 1825])) {
    $days = 7;
}

$interval = $days - 1;
$orders_per_day = $conn->query("
    SELECT DATE(created_at) as order_date, COUNT(*) as order_count
    FROM orders
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL $interval DAY)
    GROUP BY DATE(created_at)
    ORDER BY order_date ASC
")->fetch_all(MYSQLI_ASSOC);

$dates = [];
$start_date = new DateTime();
$start_date->modify("-$interval days");
for ($i = 0; $i < $days; $i++) {
    $date_str = $start_date->format('Y-m-d');
    $dates[$date_str] = 0;
    $start_date->modify('+1 day');
}
foreach ($orders_per_day as $row) {
    $dates[$row['order_date']] = (int)$row['order_count'];
}

if ($days > 30) {
    $monthly_data = [];
    foreach ($dates as $date => $count) {
        $month = date('Y-m', strtotime($date));
        if (!isset($monthly_data[$month])) {
            $monthly_data[$month] = 0;
        }
        $monthly_data[$month] += $count;
    }
    $response = [
        'labels' => array_keys($monthly_data),
        'data' => array_values($monthly_data)
    ];
} else {
    $response = [
        'labels' => array_keys($dates),
        'data' => array_values($dates)
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
