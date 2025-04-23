<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch statistics
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_price) as total FROM orders")->fetch_assoc()['total'];

// Fetch messages from contact_inquiries table
$messages = $conn->query("SELECT * FROM contact_inquiries ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Fetch orders count per day for last 7 days
$orders_per_day = $conn->query("
    SELECT DATE(created_at) as order_date, COUNT(*) as order_count
    FROM orders
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(created_at)
    ORDER BY order_date ASC
")->fetch_all(MYSQLI_ASSOC);

// Prepare data for chart
$dates = [];
$order_counts = [];
$start_date = new DateTime();
$start_date->modify('-6 days');
for ($i = 0; $i < 7; $i++) {
    $date_str = $start_date->format('Y-m-d');
    $dates[$date_str] = 0;
    $start_date->modify('+1 day');
}
foreach ($orders_per_day as $row) {
    $dates[$row['order_date']] = (int)$row['order_count'];
}
$labels = json_encode(array_keys($dates));
$data = json_encode(array_values($dates));
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="shortcut icon" href="https://i.ibb.co/7NV0mNm9/IMG-20230803-013043224-transformed-x4-x16.jpg" type="image/png" />
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <script src="../assets/js/header.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p><?php echo $total_products; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p><?php echo $total_orders; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p>$<?php echo number_format($total_revenue, 2); ?></p>
            </div>
        </div>

    <div style="max-width: 700px; margin: 2rem auto; text-align: center;">
        <label for="timeRangeSelect" style="color: #00ffe7; font-weight: bold; font-size: 16px; margin-right: 10px;">Select Time Range:</label>
        <select id="timeRangeSelect" style="padding: 6px 12px; font-size: 16px; border-radius: 5px; border: none; background-color: #121212; color: #00ffe7;">
            <option value="7">Last 7 Days</option>
            <option value="30">Last 1 Month</option>
            <option value="180">Last 6 Months</option>
            <option value="365">Last 1 Year</option>
            <option value="1825">Last 5 Years</option>
        </select>
    </div>
    <div class="chart-container" style="width: 100%; max-width: 700px; margin: 2rem auto;">
        <canvas id="ordersChart"></canvas>
    </div>
        
        <div class="admin-links">
            <a href="view_messages.php" class="admin-link">View Messages</a>
            <a href="manage_products.php" class="admin-link">Manage Products</a>
            <a href="manage_orders.php" class="admin-link">Manage Orders</a>
            <a href="manage_users.php" class="admin-link">Manage Users</a>
            <a href="create_admin.php" class="admin-link">Create Admin</a>
            <a href="activity_log.php" class="admin-link">View Activity Log</a>
            <a href="logout.php" class="admin-link">Logout</a>
        </div>
    </div>

        <!-- Messages Section -->
        <!-- <div class="messages-section">
            <h2>User Messages</h2>
            <?php if (!empty($messages)): ?>
                <table class="messages-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                <td><?php echo htmlspecialchars($message['email']); ?></td>
                                <td><?php echo htmlspecialchars($message['message']); ?></td>
                                <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No messages found.</p>
            <?php endif; ?>
        </div> -->

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <style>
        /* Dark theme and neon style for chart container */
        .chart-container {
            background: #121212;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 30px #00ffe7, 0 0 60px #00ffe7 inset;
            width: 90vw;
            max-width: 700px;
            margin: 2rem auto;
        }
        @media (max-width: 768px) {
            .chart-container {
                width: 95vw;
                padding: 10px;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script>
        const ctx = document.getElementById('ordersChart').getContext('2d');

        // Create gradient for the line fill
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(0, 255, 231, 0.7)');
        gradient.addColorStop(1, 'rgba(0, 255, 231, 0.1)');

        let ordersChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $labels; ?>,
                datasets: [{
                    label: 'Orders in Last 7 Days',
                    data: <?php echo $data; ?>,
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: '#00ffe7',
                    borderWidth: 3,
                    pointBackgroundColor: '#00ffe7',
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    tension: 0.4,
                    hoverBorderWidth: 4,
                    hoverBorderColor: '#00fff0',
                    shadowOffsetX: 0,
                    shadowOffsetY: 0,
                    shadowBlur: 20,
                    shadowColor: '#00ffe7',
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#00ffe7',
                            font: {
                                size: 18,
                                weight: 'bold',
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#00ffe7',
                        titleColor: '#000',
                        bodyColor: '#000',
                        cornerRadius: 10,
                        padding: 12,
                        displayColors: false,
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    zoom: {
                        pan: {
                            enabled: true,
                            mode: 'x',
                            modifierKey: 'ctrl',
                        },
                        zoom: {
                            wheel: {
                                enabled: true,
                            },
                            pinch: {
                                enabled: true
                            },
                            mode: 'x',
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#00ffe7',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 255, 231, 0.2)'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        precision: 0,
                        ticks: {
                            color: '#00ffe7',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 255, 231, 0.2)'
                        }
                    }
                }
            }
        });

        // Function to fetch updated data and update the chart
        async function updateChart(days) {
            try {
                const response = await fetch('fetch_orders_data.php?days=' + days);
                const jsonData = await response.json();
                ordersChart.data.labels = jsonData.labels;
                ordersChart.data.datasets[0].data = jsonData.data;
                ordersChart.data.datasets[0].label = getLabelForDays(days);
                ordersChart.update();
            } catch (error) {
                console.error('Error fetching chart data:', error);
            }
        }

        // Update chart every 60 seconds with current selected days
        let currentDays = document.getElementById('timeRangeSelect').value;
        setInterval(() => updateChart(currentDays), 60000);

        // Update chart when time range selection changes
        document.getElementById('timeRangeSelect').addEventListener('change', (event) => {
            currentDays = event.target.value;
            updateChart(currentDays);
        });

        // Helper function to get label text for days
        function getLabelForDays(days) {
            switch (parseInt(days)) {
                case 7:
                    return 'Orders in Last 7 Days';
                case 30:
                    return 'Orders in Last 1 Month';
                case 180:
                    return 'Orders in Last 6 Months';
                case 365:
                    return 'Orders in Last 1 Year';
                case 1825:
                    return 'Orders in Last 5 Years';
                default:
                    return 'Orders';
            }
        }

        // Initial chart load
        updateChart(currentDays);
    </script>

</body>
</html>
