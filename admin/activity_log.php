<?php
session_start();
include __DIR__ . '/../includes/db.php';
require 'AdminLogger.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get recent activity logs
$logs = AdminLogger::getLogs();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <h1>Activity Log</h1>
        
        <div class="activity-log">
            <?php if (!empty($logs)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Admin ID</th>
                            <th>Activity</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <?php
                            // Parse log entry
                            preg_match('/\[(.*?)\] Admin ID: (\d+) \| Activity: (.*?) \| Description: (.*)/', $log, $matches);
                            if (count($matches) === 5) {
                                list(, $timestamp, $admin_id, $activity, $description) = $matches;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($timestamp); ?></td>
                                    <td><?php echo htmlspecialchars($admin_id); ?></td>
                                    <td><?php echo htmlspecialchars($activity); ?></td>
                                    <td><?php echo htmlspecialchars($description); ?></td>
                                </tr>
                            <?php } ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No activity logs found.</p>
            <?php endif; ?>
        </div>
        
        <a href="dashboard.php" class="admin-link">Back to Dashboard</a>
    </div>
</body>
</html>
