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
    // Add or update category
    if (isset($_POST['save_category'])) {
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);

        // Add new category
        if (!isset($_POST['category_id'])) {
            $query = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";

            if (mysqli_query($conn, $query)) {
                set_success_message('Category added successfully');
            } else {
                set_error_message('Error: ' . mysqli_error($conn));
            }
        }
        // Update existing category
        else {
            $category_id = (int)$_POST['category_id'];

            $query = "UPDATE categories SET name = '$name', description = '$description' WHERE id = $category_id";

            if (mysqli_query($conn, $query)) {
                set_success_message('Category updated successfully');
            } else {
                set_error_message('Error: ' . mysqli_error($conn));
            }
        }

        redirect('categories.php');
    }

    // Delete category
    if (isset($_POST['delete_category'])) {
        $category_id = (int)$_POST['category_id'];

        // Check if category has products
        $query = "SELECT COUNT(*) as product_count FROM products WHERE category_id = $category_id";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);

        if ($row['product_count'] > 0) {
            set_error_message('Cannot delete category. It has products associated with it.');
        } else {
            $query = "DELETE FROM categories WHERE id = $category_id";

            if (mysqli_query($conn, $query)) {
                set_success_message('Category deleted successfully');
            } else {
                set_error_message('Error: ' . mysqli_error($conn));
            }
        }

        redirect('categories.php');
    }
}

// Get category for edit
$category = null;
if (isset($_GET['edit'])) {
    $category_id = (int)$_GET['edit'];

    $query = "SELECT * FROM categories WHERE id = $category_id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $category = mysqli_fetch_assoc($result);
    } else {
        set_error_message('Category not found');
        redirect('categories.php');
    }
}

// Get all categories
$query = "SELECT c.*, COUNT(p.id) as product_count 
          FROM categories c 
          LEFT JOIN products p ON c.id = p.category_id 
          GROUP BY c.id 
          ORDER BY c.name";
$result = mysqli_query($conn, $query);
$categories = [];

while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

// Include admin header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <?php echo isset($_GET['action']) && $_GET['action'] === 'add' ? 'Add New Category' : (isset($_GET['edit']) ? 'Edit Category' : 'Manage Categories'); ?>
        </h1>
        <?php if (!isset($_GET['action']) && !isset($_GET['edit'])): ?>
            <a href="categories.php?action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Category
            </a>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-md-<?php echo isset($_GET['action']) && $_GET['action'] === 'add' || isset($_GET['edit']) ? '8' : '12'; ?>">
            <!-- Categories List -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Categories</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($categories)): ?>
                        <p class="text-center">No categories found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Products</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td><?php echo $cat['id']; ?></td>
                                            <td><?php echo $cat['name']; ?></td>
                                            <td><?php echo substr($cat['description'], 0, 100) . (strlen($cat['description']) > 100 ? '...' : ''); ?></td>
                                            <td><?php echo $cat['product_count']; ?></td>
                                            <td>
                                                <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="categories.php" method="post" class="d-inline">
                                                    <input type="hidden" name="category_id" value="<?php echo $cat['id']; ?>">
                                                    <button type="submit" name="delete_category" class="btn btn-sm btn-danger delete-btn">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['action']) && $_GET['action'] === 'add' || isset($_GET['edit'])): ?>
            <div class="col-md-4">
                <!-- Add/Edit Category Form -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <?php echo isset($_GET['edit']) ? 'Edit Category' : 'Add New Category'; ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="categories.php" method="post">
                            <?php if (isset($_GET['edit'])): ?>
                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($category) ? $category['name'] : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo isset($category) ? $category['description'] : ''; ?></textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" name="save_category" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Category
                                </button>
                                <a href="categories.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Include admin footer
include 'includes/footer.php';
?>