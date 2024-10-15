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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        /* Background and Body */
        body {
            background-color: #fff5e1; /* Warm light background */
            color: #4f2c10; /* Rich roasted brown for text */
            font-family: 'Poppins', sans-serif;
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

        /* Content and Table Styling */
        .content {
            margin-left: 270px;
            padding: 50px;
        }

        .card {
            background-color: rgba(245, 224, 181, 0.8); /* Semi-transparent roasted brown */
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
        }

        .card-header {
            background-color: transparent;
            border-bottom: none;
        }

        .card-header h1 {
            color: #7b4a22; /* Dark roasted brown */
        }

        .table thead {
            background-color: #7b4a22; /* Dark roasted brown header */
            color: white;
        }

        .table tbody tr {
            transition: background 0.3s;
        }

        .table tbody tr:hover {
            background-color: #f7e1c0; /* Light roasted hover */
        }

        /* Button Styling */
        .btn-custom {
            background-color: #e27225; /* Grilled orange */
            border: none;
            color: white;
            transition: background 0.3s;
        }

        .btn-custom:hover {
            background-color: #f9c56f; /* Golden roasted hover */
            color: white;
        }

        /* Modal Styling */
        .modal-header {
            background-color: #7b4a22; /* Dark roasted modal header */
            color: white;
            border-bottom: none;
        }

        .modal-body {
            background-color: #fff5e1; /* Light roasted modal body */
        }

        .form-control {
            border: 1px solid #7b4a22; /* Roasted brown input border */
        }

        .form-control:focus {
            border-color: #e27225; /* Grilled orange focus */
            box-shadow: 0 0 5px rgba(226, 114, 37, 0.5);
        }

        .btn-close {
            background-color: white;
        }

        .no-records {
            background: #f8d7da; /* Light Red Background for no records */
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h2>Stock Monitor</h2>
        <a href="../views/dashboard.php"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
        <a href="#"><i class="fa-solid fa-box me-2"></i> Products</a>
        <a href="../views/print_sales.php"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Sales</a>
        <a href="../views/reports.php"><i class="fa-solid fa-chart-line me-2"></i> Reports</a>
        <a href="../actions/logout.php" class="btn btn-outline">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1 class="mb-4">Product List</h1>

        <!-- Products Table -->
        <div class="container mt-5">
            <div class="card w-100 mx-auto shadow-lg border-0" style="max-width: 1200px;">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col text-start">
                            <h1 class="display-6 fw-bold">Product List</h1>
                        </div>
                        <div class="col text-end">
                            <i class="fa-solid fa-plus fa-3x text-info" data-bs-toggle="modal" data-bs-target="#add-product" style="cursor: pointer;"></i>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <?php
                        if(empty($product_list)){
                    ?>
                        <div class="container-fluid p-5 text-center no-records">
                            <h1 class="display-6 fw-bold pt-5 pb-3">No Records Found</h1>
                            <i class="fa-regular fa-circle-xmark fa-8x pb-5"></i>
                        </div>
                    <?php
                        } else {
                    ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                foreach($product_list as $product){
                            ?>
                                    <tr>
                                        <td><?= $product['id']?></td>
                                        <td><?= $product['product_name']?></td>
                                        <td>₱<?= number_format($product['price'], 2)?></td>
                                        <td><?= $product['quantity']?></td>
                                        <td>
                                            <a href="edit-product.php?product_id=<?= $product['id'] ?>" class="btn btn-sm btn-custom" title="Edit Product"><i class="fa-solid fa-pen"></i></a>
                                            <a href="../actions/delete-product.php?product_id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" title="Delete Product"><i class="fa-solid fa-trash"></i></a>
                                            <a href="buy-product.php?product_id=<?= $product['id'] ?>" class="btn btn-sm btn-success" title="List as Sold"><i class="fa-solid fa-check"></i> </a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>

        <!-- ADD PRODUCT MODAL -->
        <div class="modal fade" id="add-product" tabindex="-1" aria-labelledby="registration" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title mx-auto text-white fw-bold">
                            <i class="fa-solid fa-box me-2"></i> Add New Product
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

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
                                        <span class="input-group-text bg-light border-primary">₱</span>
                                        <input type="number" name="price" id="price" class="form-control" aria-label="Price" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label text-secondary fw-semibold">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" required>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-custom px-5 rounded-pill" name="add_product">
                                    <i class="fa-solid fa-plus me-2"></i> Add Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
