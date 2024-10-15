<?php 
session_start();

// Ensure the user is logged in
if(empty($_SESSION)){
    header("location: ../views/");
    exit;
}

// Include the Product class
include "../classes/Product.php";

$product = new Product;

// Fetch all products
$product_list = $product->displayProducts();

// Calculate total revenue and orders
$total_revenue = 0;
$total_orders = 0;
$low_stock_count = 0;

foreach ($product_list as $item) {
    $total_revenue += $item['price'] * $item['quantity'];
    if ($item['quantity'] == 0) {
        $low_stock_count++;
    }
    $total_orders++; // Increment for each product
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Monitor</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        /* Styling for video background */
        #background-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%; 
            min-height: 100%;
            z-index: -1; /* Ensures video stays in the background */
            object-fit: cover; /* Ensures the video covers the entire background without distortion */
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #4f2c10; /* Rich brown for main text */
            background-color: #fff5e1; /* Fallback color */
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
            transition: all 0.3s;
        }
        .sidebar h2 {
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            color: #f9c56f; /* Golden roast text */
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
            background-color: #e27225; /* Grilled orange */
            color: white;
        }
        .content {
            margin-left: 270px;
            padding: 50px;
            position: relative;
            z-index: 1; /* Ensures content stays on top of the video */
        }

        /* Dashboard Card Styling */
        .dashboard-card {
            background: rgba(245, 224, 181, 0.8); /* Semi-transparent roasted color */
            border: none;
            color: #7b4a22; /* Dark roasted brown */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        /* Button Styling */
        .btn-custom {
            background: #e27225; /* Grilled orange */
            border: none;
            color: white;
            transition: background 0.3s;
        }
        .btn-custom:hover {
            background: #f9c56f; /* Golden roast */
            color: white;
        }

        /* Modal Styling */
        .modal-header {
            background-color: #7b4a22;
            color: white;
        }
        .modal-body {
            background-color: #fff5e1; /* Light roasted background */
        }
        .form-control {
            border: 1px solid #7b4a22; /* Dark roasted brown */
        }
        .form-control:focus {
            border-color: #e27225; /* Grilled orange */
            box-shadow: 0 0 5px rgba(226, 114, 37, 0.5);
        }
    </style>
</head>
<body>
    <!-- Background Video -->
    <video id="background-video" autoplay loop muted>
        <source src="vid/nokma2.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h2>Stock Monitor</h2>
        <a href="../views/dashboard.php"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
        <a href="../views/product_list.php"><i class="fa-solid fa-drumstick-bite me-2"></i> Products</a>
        <a href="../views/print_sales.php"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Sales</a>
        <a href="../views/reports.php"><i class="fa-solid fa-chart-line me-2"></i> Reports</a>
        <a href="../actions/logout.php" class="btn btn-outline">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1 class="mb-4">Mhare Taste Lechon Manok Dashboard</h1>

        <!-- Dashboard Metrics -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="dashboard-card p-4 text-center">
                    <h5 class="card-title"><?= date('l') . ', ' . date('d M Y') ?></h5>
                    <h1 class="display-4"><?= date('d') ?></h1>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="dashboard-card p-4 text-center" style="background-color:#e27225;">
                    <h5 class="card-title text-white">Revenue Per Stock</h5>
                    <h1 class="display-5 text-white" style="font-size: 2.5rem;"><?= number_format($total_revenue, 2) ?></h1>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="dashboard-card p-4 text-center">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text display-4"><?= $total_orders ?></p>
                </div>
            </div>
        </div>

        <!-- Low Stock and New Metric Cards -->
        <div class="row mb-5">
           

            <div class="col-md-4 mb-3">
                <div class="dashboard-card p-4 text-center" style="background-color:#e27225;">
                    <h5 class="card-title text-white">Expected Sales</h5>
                    <p class="display-5 text-white">2,600.00</p> <!-- Replace with actual value if needed -->
                </div>
            </div>
        </div>

        <!-- ADD PRODUCT MODAL -->
        <div class="modal fade" id="add-product" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header border-0">
                        <h5 class="modal-title mx-auto text-info fw-bold">
                            <i class="fa-solid fa-box-open me-2"></i> Add New Product
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-5">
                        <form action="../actions/product-actions.php" method="post" class="w-75 mx-auto">
                            <!-- Product Name Field -->
                            <div class="mb-4">
                                <label for="product-name" class="form-label text-secondary fw-semibold">Product Name</label>
                                <input type="text" name="product_name" id="product-name" class="form-control" required>
                            </div>

                            <!-- Price and Quantity Fields -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="price" class="form-label text-secondary fw-semibold">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="price" id="price" class="form-control" required min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label text-secondary fw-semibold">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" required min="0">
                                </div>
                            </div>

                            <!-- Product Description Field -->
                            <div class="mb-4">
                                <label for="description" class="form-label text-secondary fw-semibold">Description</label>
                                <textarea name="description" id="description" rows="3" class="form-control"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-custom w-100">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </div>
</body>
</html>
