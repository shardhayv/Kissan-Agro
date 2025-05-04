<?php
// Include authentication functions
require_once '../includes/auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    set_error_message('You must be logged in to access this page');
    redirect(site_url('admin/index.php'));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or update product
    if (isset($_POST['save_product'])) {
        $name = sanitize($_POST['name']);
        $category_id = (int)$_POST['category_id'];
        $description = sanitize($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;

        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../uploads/';

            // Create uploads directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = time() . '_' . $_FILES['image']['name'];
            $upload_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $file_name;
            }
        }

        // Add new product
        if (!isset($_POST['product_id'])) {
            $query = "INSERT INTO products (category_id, name, description, price, stock, is_featured";

            if ($image) {
                $query .= ", image";
            }

            $query .= ") VALUES ($category_id, '$name', '$description', $price, $stock, $is_featured";

            if ($image) {
                $query .= ", '$image'";
            }

            $query .= ")";

            if (mysqli_query($conn, $query)) {
                set_success_message('Product added successfully');
                redirect('products.php');
            } else {
                set_error_message('Error: ' . mysqli_error($conn));
            }
        }
        // Update existing product
        else {
            $product_id = (int)$_POST['product_id'];

            $query = "UPDATE products SET
                      category_id = $category_id,
                      name = '$name',
                      description = '$description',
                      price = $price,
                      stock = $stock,
                      is_featured = $is_featured";

            if ($image) {
                $query .= ", image = '$image'";
            }

            $query .= " WHERE id = $product_id";

            if (mysqli_query($conn, $query)) {
                set_success_message('Product updated successfully');
                redirect('products.php');
            } else {
                set_error_message('Error: ' . mysqli_error($conn));
            }
        }
    }

    // Delete product
    if (isset($_POST['delete_product'])) {
        $product_id = (int)$_POST['product_id'];

        // Get product image to delete
        $query = "SELECT image FROM products WHERE id = $product_id";
        $result = mysqli_query($conn, $query);
        $product = mysqli_fetch_assoc($result);

        // Delete product from database
        $query = "DELETE FROM products WHERE id = $product_id";

        if (mysqli_query($conn, $query)) {
            // Delete product image if exists
            if (!empty($product['image'])) {
                $image_path = '../uploads/' . $product['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            set_success_message('Product deleted successfully');
        } else {
            set_error_message('Error: ' . mysqli_error($conn));
        }

        redirect('products.php');
    }
}

// Get categories for form
$categories = get_categories();

// Get product for edit
$product = null;
if (isset($_GET['edit'])) {
    $product_id = (int)$_GET['edit'];
    $product = get_product($product_id);

    if (!$product) {
        set_error_message('Product not found');
        redirect('products.php');
    }
}

// Get all products
$query = "SELECT p.*, c.name as category_name FROM products p
          JOIN categories c ON p.category_id = c.id
          ORDER BY p.id DESC";
$result = mysqli_query($conn, $query);
$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// Include admin header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <?php echo isset($_GET['action']) && $_GET['action'] === 'add' ? 'Add New Product' : (isset($_GET['edit']) ? 'Edit Product' : 'Manage Products'); ?>
        </h1>
        <?php if (!isset($_GET['action']) && !isset($_GET['edit'])): ?>
            <a href="products.php?action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['action']) && $_GET['action'] === 'add' || isset($_GET['edit'])): ?>
        <!-- Add/Edit Product Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <?php echo isset($_GET['edit']) ? 'Edit Product' : 'Add New Product'; ?>
                </h6>
            </div>
            <div class="card-body">
                <form action="products.php" method="post" enctype="multipart/form-data">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 stacked-form-group">
                                <label for="name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($product) ? $product['name'] : ''; ?>" required>
                            </div>

                            <div class="mb-3 stacked-form-group">
                                <label for="category_id" class="form-label">Category *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo isset($product) && $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo $category['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3 stacked-form-group">
                                        <label for="price" class="form-label">Price (₹) *</label>
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo isset($product) ? $product['price'] : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3 stacked-form-group">
                                        <label for="stock" class="form-label">Stock *</label>
                                        <input type="number" class="form-control" id="stock" name="stock" min="0" value="<?php echo isset($product) ? $product['stock'] : '0'; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" <?php echo isset($product) && $product['is_featured'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_featured">Featured Product</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3 stacked-form-group">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo isset($product) ? $product['description'] : ''; ?></textarea>
                            </div>

                            <div class="mb-3 stacked-form-group">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="form-text text-muted">Leave empty to keep current image (if editing).</small>
                            </div>

                            <?php if (isset($product) && !empty($product['image'])): ?>
                                <div class="mb-3 stacked-form-group">
                                    <label class="form-label">Current Image</label>
                                    <div>
                                        <img src="../uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" id="imagePreview" class="img-thumbnail" style="max-width: 100%; max-height: 200px;">
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <img id="imagePreview" class="img-thumbnail" style="max-width: 100%; max-height: 200px; display: none;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-3 form-actions">
                        <button type="submit" name="save_product" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Product
                        </button>
                        <a href="products.php" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- Products List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Products</h6>
            </div>
            <div class="card-body">
                <?php if (empty($products)): ?>
                    <p class="text-center">No products found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered mobile-optimized-table">
                            <thead>
                                <tr>
                                    <th class="priority-1">ID</th>
                                    <th class="priority-1">Image</th>
                                    <th class="priority-1">Name</th>
                                    <th class="priority-2">Category</th>
                                    <th class="priority-2">Price</th>
                                    <th class="priority-3">Stock</th>
                                    <th class="priority-3">Featured</th>
                                    <th class="priority-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td class="priority-1"><?php echo $product['id']; ?></td>
                                        <td class="priority-1">
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="../uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-thumbnail product-thumbnail">
                                            <?php else: ?>
                                                <img src="../assets/images/product-placeholder.jpg" alt="No Image" class="img-thumbnail product-thumbnail">
                                            <?php endif; ?>
                                        </td>
                                        <td class="priority-1"><?php echo $product['name']; ?></td>
                                        <td class="priority-2"><?php echo $product['category_name']; ?></td>
                                        <td class="priority-2"><?php echo format_price($product['price']); ?></td>
                                        <td class="priority-3"><?php echo $product['stock']; ?></td>
                                        <td class="priority-3">
                                            <?php if ($product['is_featured']): ?>
                                                <span class="badge bg-success">Yes</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="priority-1">
                                            <div class="table-actions">
                                                <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit d-md-none"></i><span class="d-none d-md-inline"><i class="fas fa-edit me-1"></i> Edit</span>
                                                </a>
                                                <form action="products.php" method="post" class="d-inline">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" name="delete_product" class="btn btn-sm btn-danger delete-btn">
                                                        <i class="fas fa-trash d-md-none"></i><span class="d-none d-md-inline"><i class="fas fa-trash me-1"></i> Delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Include admin footer
include 'includes/footer.php';
?>