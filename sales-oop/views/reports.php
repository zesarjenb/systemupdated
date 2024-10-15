<?php 
$host = 'localhost';
$db = 'sales_oop'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert sales data for a specific date
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date']) && isset($_POST['amount'])) {
        $date = $_POST['date'];
        $amount = $_POST['amount'];

        // Insert into the sales table
        $stmt = $pdo->prepare("INSERT INTO sales (date, amount) VALUES (:date, :amount)");
        $stmt->execute(['date' => $date, 'amount' => $amount]);
        echo "<p class='message'>Sale recorded: ₱" . number_format($amount, 2) . " on $date.</p>";
    }

    // Fetch daily sales data for the current week
    $stmt = $pdo->query("
        SELECT DAYOFWEEK(date) AS weekday, SUM(amount) AS total 
        FROM sales 
        WHERE date >= CURDATE() - INTERVAL WEEKDAY(CURDATE()) DAY
        GROUP BY weekday
        ORDER BY weekday
    ");
    $dailyReports = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Calculate total sales for the week
    $totalWeeklySales = array_sum($dailyReports);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly and Daily Sales Report - Mhare Taste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #fff5e1; /* Warm roasted background */
            font-family: 'Poppins', sans-serif;
            color: #4f2c10; /* Dark roasted brown text */
            margin: 0;
            padding: 0;
        }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #7b4a22; /* Dark roasted brown */
            color: white;
            padding: 30px 0;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
        }

        .sidebar h2 {
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            color: #f9c56f; /* Golden roasted text */
        }

        .sidebar a {
            color: white;
            padding: 15px 30px;
            display: block;
            text-decoration: none;
            font-size: 1.1em;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #e27225; /* Grilled orange hover */
            color: white;
        }

        /* Content Area */
        .content {
            margin-left: 270px;
            padding: 50px;
            background-color: #fffdf8; /* Light roasted content background */
            overflow: auto;
        }

        h1, h2 {
            color: #7b4a22; /* Dark roasted brown */
        }

        /* Form Styling */
        input[type="number"], input[type="date"], input[type="submit"] {
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        input[type="submit"] {
            background-color: #e27225; /* Grilled orange */
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #f9c56f; /* Golden roasted hover */
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: rgba(245, 224, 181, 0.8); /* Semi-transparent roasted */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            color: #4f2c10;
        }

        th {
            background-color: #7b4a22; /* Dark roasted brown header */
            color: white;
        }

        tr:hover td {
            background-color: #f7e1c0; /* Light roasted hover */
        }

        .message {
            color: green;
            margin: 10px 0;
            animation: slideIn 0.5s ease-in-out;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Stock Monitor</h2>
        <a href="../views/dashboard.php"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
        <a href="../views/product_list.php"><i class="fa-solid fa-box me-2"></i> Products</a>
        <a href="../views/print_sales.php"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Sales</a>
        <a href="../views/reports.php"><i class="fa-solid fa-chart-line me-2"></i> Reports</a>
        <a href="../actions/logout.php" class="btn btn-outline">Logout</a>
    </div>

    <div class="content">
        <h1>Weekly and Daily Sales Report - Mhare Taste</h1>

        <h2>Record Sale</h2>
        <form method="post">
            <label>Amount: <input type="number" name="amount" required></label>
            <label>Date: <input type="date" name="date" required></label>
            <input type="submit" value="Add Sale">
        </form>

        <h2>Current Week Calendar</h2>
        <div class="calendar">
            <table>
                <tr>
                    <?php
                    // Day names array
                    $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

                    // Display each day of the current week with total sales
                    for ($i = 0; $i < 7; $i++) {
                        echo "<td class='day'>{$dayNames[$i]}<br><strong>₱" . number_format($dailyReports[$i + 1] ?? 0, 2) . "</strong></td>";
                    }
                    ?>
                </tr>
            </table>
        </div>

        <h2>Total Sales for the Week</h2>
        <p class="total">Total: ₱<?php echo number_format($totalWeeklySales, 2); ?></p>

        <h2>Daily Sales Reports</h2>
        <table>
            <tr>
                <th>Day</th>
                <th>Total Sales</th>
            </tr>
            <?php
            for ($day = 1; $day <= 7; $day++) {
                echo "<tr>";
                echo "<td>" . $dayNames[$day - 1] . "</td>";
                echo "<td>₱" . number_format($dailyReports[$day] ?? 0, 2) . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

</body>
</html>
