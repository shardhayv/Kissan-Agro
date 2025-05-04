<?php
// Include authentication functions
require_once '../includes/auth.php';

// Check if user is logged in and has admin access
check_access(['admin']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle image upload for new images
    if (isset($_POST['upload_image'])) {
        $image_key = sanitize($_POST['image_key']);
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);

        // Handle image upload
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../uploads/site/';

            // Create uploads directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = time() . '_' . $_FILES['image']['name'];
            $upload_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = $file_name;
            } else {
                set_error_message('Failed to upload image');
                redirect('images.php');
            }
        } else {
            set_error_message('Please select an image to upload');
            redirect('images.php');
        }

        // Check if image key already exists
        $query = "SELECT id FROM site_images WHERE image_key = '$image_key'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Update existing image
            $query = "UPDATE site_images SET
                      image_path = '$image_path',
                      title = '$title',
                      description = '$description'
                      WHERE image_key = '$image_key'";
        } else {
            // Insert new image
            $query = "INSERT INTO site_images (image_key, image_path, title, description)
                      VALUES ('$image_key', '$image_path', '$title', '$description')";
        }

        if (mysqli_query($conn, $query)) {
            set_success_message('Image saved successfully');
        } else {
            set_error_message('Error: ' . mysqli_error($conn));
        }

        redirect('images.php');
    }

    // Handle image update (edit)
    if (isset($_POST['update_image'])) {
        $image_id = (int)$_POST['image_id'];
        $image_key = sanitize($_POST['image_key']);
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);

        // Check if a new image was uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../uploads/site/';

            // Create uploads directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Get the old image path to delete it later
            $query = "SELECT image_path FROM site_images WHERE id = $image_id";
            $result = mysqli_query($conn, $query);
            $old_image_path = '';

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $old_image_path = '../uploads/site/' . $row['image_path'];
            }

            // Upload new image
            $file_name = time() . '_' . $_FILES['image']['name'];
            $upload_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Update database with new image path
                $query = "UPDATE site_images SET
                          image_path = '$file_name',
                          title = '$title',
                          description = '$description'
                          WHERE id = $image_id";

                if (mysqli_query($conn, $query)) {
                    // Delete old image file if it exists
                    if (!empty($old_image_path) && file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                    set_success_message('Image updated successfully');
                } else {
                    set_error_message('Error updating image: ' . mysqli_error($conn));
                }
            } else {
                set_error_message('Failed to upload new image');
            }
        } else {
            // Update only the title and description
            $query = "UPDATE site_images SET
                      title = '$title',
                      description = '$description'
                      WHERE id = $image_id";

            if (mysqli_query($conn, $query)) {
                set_success_message('Image details updated successfully');
            } else {
                set_error_message('Error updating image details: ' . mysqli_error($conn));
            }
        }

        redirect('images.php');
    }

    // Handle image deletion
    if (isset($_POST['delete_image'])) {
        $image_id = (int)$_POST['image_id'];

        // Get image path before deleting
        $query = "SELECT image_path FROM site_images WHERE id = $image_id";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $image_path = '../uploads/site/' . $row['image_path'];

            // Delete image file if it exists
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            // Delete from database
            $query = "DELETE FROM site_images WHERE id = $image_id";

            if (mysqli_query($conn, $query)) {
                set_success_message('Image deleted successfully');
            } else {
                set_error_message('Error: ' . mysqli_error($conn));
            }
        } else {
            set_error_message('Image not found');
        }

        redirect('images.php');
    }
}

// Get all site images
$images = get_all_site_images();

// Include admin header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Website Images</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addImageModal">
            <i class="fas fa-plus"></i> Add New Image
        </button>
    </div>

    <?php display_messages(); ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Website Images</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="imagesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Key</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($images as $image): ?>
                            <tr>
                                <td><?php echo $image['id']; ?></td>
                                <td>
                                    <img src="/mill/uploads/site/<?php echo $image['image_path']; ?>" alt="<?php echo $image['title']; ?>" class="img-thumbnail" style="max-width: 100px;">
                                </td>
                                <td><?php echo $image['image_key']; ?></td>
                                <td><?php echo $image['title']; ?></td>
                                <td><?php echo $image['description']; ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($image['updated_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary edit-image-btn"
                                        data-id="<?php echo $image['id']; ?>"
                                        data-key="<?php echo $image['image_key']; ?>"
                                        data-title="<?php echo $image['title']; ?>"
                                        data-description="<?php echo $image['description']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#editImageModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="images.php" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                        <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                        <button type="submit" name="delete_image" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($images)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No images found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">How to Use Website Images</h6>
        </div>
        <div class="card-body">
            <p>To use these images in your website templates, use the <code>get_site_image()</code> function with the image key:</p>
            <pre><code>&lt;img src="&lt;?php echo get_site_image('logo', 'default-logo.png'); ?&gt;" alt="Logo"&gt;</code></pre>
            <p>The second parameter is optional and specifies a default image from the assets/images directory to use if the requested image is not found.</p>

            <hr>

            <h5 class="mt-4 mb-3">Common Image Keys and Their Usage</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Image Key</th>
                            <th>Used On</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>logo</code></td>
                            <td>Header, Footer</td>
                            <td>The main website logo</td>
                        </tr>
                        <tr>
                            <td><code>hero_bg</code></td>
                            <td>Homepage</td>
                            <td>Background image for the hero section on the homepage</td>
                        </tr>
                        <tr>
                            <td><code>about_header</code></td>
                            <td>About Page</td>
                            <td>Header background image for the About page</td>
                        </tr>
                        <tr>
                            <td><code>products_header</code></td>
                            <td>Products Page</td>
                            <td>Header background image for the Products page</td>
                        </tr>
                        <tr>
                            <td><code>contact_header</code></td>
                            <td>Contact Page</td>
                            <td>Header background image for the Contact page</td>
                        </tr>
                        <tr>
                            <td><code>track_order_header</code></td>
                            <td>Track Order Page</td>
                            <td>Header background image for the Track Order page</td>
                        </tr>
                        <tr>
                            <td><code>cart_header</code></td>
                            <td>Cart Page</td>
                            <td>Header background image for the Shopping Cart page</td>
                        </tr>
                        <tr>
                            <td><code>terms_header</code></td>
                            <td>Terms & Conditions Page</td>
                            <td>Header background image for the Terms and Conditions page</td>
                        </tr>
                        <tr>
                            <td><code>privacy_header</code></td>
                            <td>Privacy Policy Page</td>
                            <td>Header background image for the Privacy Policy page</td>
                        </tr>
                        <tr>
                            <td><code>checkout_header</code></td>
                            <td>Order/Checkout Page</td>
                            <td>Header background image for the Order/Checkout page</td>
                        </tr>
                        <tr>
                            <td><code>about_image</code></td>
                            <td>About Page, Homepage</td>
                            <td>Main image displayed on the About page and homepage</td>
                        </tr>
                        <tr>
                            <td><code>wheat_mill</code></td>
                            <td>About Page</td>
                            <td>Image of the wheat mill facility</td>
                        </tr>
                        <tr>
                            <td><code>wheat_mill_image</code></td>
                            <td>Homepage</td>
                            <td>Image of the wheat mill facility on homepage</td>
                        </tr>
                        <tr>
                            <td><code>rice_mill</code></td>
                            <td>About Page</td>
                            <td>Image of the rice mill facility</td>
                        </tr>
                        <tr>
                            <td><code>rice_mill_image</code></td>
                            <td>Homepage</td>
                            <td>Image of the rice mill facility on homepage</td>
                        </tr>
                        <tr>
                            <td><code>team1</code>, <code>team2</code>, <code>team3</code></td>
                            <td>About Page</td>
                            <td>Team member photos</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info mt-3">
                <strong>Note:</strong> When you update an image, the change will be reflected immediately across the entire website wherever that image key is used.
            </div>
        </div>
    </div>
</div>

<!-- Add Image Modal -->
<div class="modal fade" id="addImageModal" tabindex="-1" aria-labelledby="addImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addImageModalLabel">Add New Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="images.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="image_key" class="form-label">Image Key *</label>
                        <input type="text" class="form-control" id="image_key" name="image_key" required>
                        <small class="form-text text-muted">A unique identifier for this image (e.g., 'logo', 'hero_bg')</small>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image *</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <img id="imagePreview" class="img-thumbnail" style="max-width: 100%; display: none;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="upload_image" class="btn btn-primary">Upload Image</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Image Modal -->
<div class="modal fade" id="editImageModal" tabindex="-1" aria-labelledby="editImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editImageModalLabel">Edit Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="images.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="edit_image_id" name="image_id">
                    <div class="mb-3">
                        <label for="edit_image_key" class="form-label">Image Key *</label>
                        <input type="text" class="form-control" id="edit_image_key" name="image_key" required readonly>
                        <small class="form-text text-muted">The image key cannot be changed</small>
                    </div>
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div class="text-center">
                            <img id="currentImagePreview" class="img-thumbnail" style="max-width: 100%; max-height: 200px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image" class="form-label">New Image (Optional)</label>
                        <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                        <small class="form-text text-muted">Upload a new image only if you want to replace the current one</small>
                    </div>
                    <div class="mb-3">
                        <img id="editImagePreview" class="img-thumbnail" style="max-width: 100%; display: none;">
                    </div>
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <strong>Note:</strong> This image will be used throughout the website wherever the key <code id="usageKey"></code> is referenced.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_image" class="btn btn-primary">Update Image</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Image preview for add form
    document.getElementById('image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    // Image preview for edit form
    document.getElementById('edit_image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('editImagePreview').src = e.target.result;
                document.getElementById('editImagePreview').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    // Populate edit modal
    document.querySelectorAll('.edit-image-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const key = this.getAttribute('data-key');
            const title = this.getAttribute('data-title');
            const description = this.getAttribute('data-description');
            const imagePath = this.closest('tr').querySelector('img').getAttribute('src');

            document.getElementById('edit_image_id').value = id;
            document.getElementById('edit_image_key').value = key;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('currentImagePreview').src = imagePath;
            document.getElementById('usageKey').textContent = key;

            // Reset the new image preview
            document.getElementById('editImagePreview').style.display = 'none';
        });
    });
</script>

<?php
// Include admin footer
include 'includes/footer.php';
?>