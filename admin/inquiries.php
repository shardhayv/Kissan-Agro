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
    // Update inquiry status
    if (isset($_POST['update_status'])) {
        $inquiry_id = (int)$_POST['inquiry_id'];
        $status = sanitize($_POST['status']);

        $query = "UPDATE inquiries SET status = '$status' WHERE id = $inquiry_id";

        if (mysqli_query($conn, $query)) {
            set_success_message('Inquiry status updated successfully');
        } else {
            set_error_message('Error: ' . mysqli_error($conn));
        }

        redirect('inquiries.php');
    }

    // Delete inquiry
    if (isset($_POST['delete_inquiry'])) {
        $inquiry_id = (int)$_POST['inquiry_id'];

        $query = "DELETE FROM inquiries WHERE id = $inquiry_id";

        if (mysqli_query($conn, $query)) {
            set_success_message('Inquiry deleted successfully');
        } else {
            set_error_message('Error: ' . mysqli_error($conn));
        }

        redirect('inquiries.php');
    }
}

// Get inquiry for view
$inquiry = null;
if (isset($_GET['view'])) {
    $inquiry_id = (int)$_GET['view'];

    $query = "SELECT * FROM inquiries WHERE id = $inquiry_id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $inquiry = mysqli_fetch_assoc($result);
    } else {
        set_error_message('Inquiry not found');
        redirect('inquiries.php');
    }
}

// Get all inquiries
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$query = "SELECT * FROM inquiries";

if (!empty($status_filter)) {
    $query .= " WHERE status = '$status_filter'";
}

$query .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$inquiries = [];

while ($row = mysqli_fetch_assoc($result)) {
    $inquiries[] = $row;
}

// Include admin header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <?php echo isset($_GET['view']) ? 'View Inquiry' : 'Manage Inquiries'; ?>
        </h1>
    </div>

    <?php if (isset($_GET['view'])): ?>
        <!-- View Inquiry -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Inquiry Details</h6>
                <a href="inquiries.php" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?php echo $inquiry['name']; ?></p>
                        <p><strong>Email:</strong> <?php echo $inquiry['email']; ?></p>
                        <p><strong>Phone:</strong> <?php echo !empty($inquiry['phone']) ? $inquiry['phone'] : 'N/A'; ?></p>
                        <p><strong>Subject:</strong> <?php echo $inquiry['subject']; ?></p>
                        <p><strong>Date:</strong> <?php echo date('d M Y H:i', strtotime($inquiry['created_at'])); ?></p>
                        <p>
                            <strong>Status:</strong>
                            <?php if ($inquiry['status'] === 'new'): ?>
                                <span class="badge bg-danger">New</span>
                            <?php elseif ($inquiry['status'] === 'in_progress'): ?>
                                <span class="badge bg-warning">In Progress</span>
                            <?php else: ?>
                                <span class="badge bg-success">Resolved</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <form action="inquiries.php" method="post">
                            <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                            <div class="mb-3">
                                <label for="status" class="form-label">Update Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="new" <?php echo $inquiry['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="in_progress" <?php echo $inquiry['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="resolved" <?php echo $inquiry['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                            </div>
                            <button type="submit" name="update_status" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Message:</h5>
                    <div class="p-3 bg-light rounded">
                        <?php echo nl2br($inquiry['message']); ?>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="mailto:<?php echo $inquiry['email']; ?>" class="btn btn-info">
                        <i class="fas fa-reply"></i> Reply via Email
                    </a>
                    <form action="inquiries.php" method="post" class="d-inline">
                        <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                        <button type="submit" name="delete_inquiry" class="btn btn-danger delete-btn">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Inquiries List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Inquiries</h6>
            </div>
            <div class="card-body">
                <!-- Filter -->
                <div class="mb-4">
                    <form action="inquiries.php" method="get" class="row g-3">
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="in_progress" <?php echo $status_filter === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="resolved" <?php echo $status_filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <?php if (!empty($status_filter)): ?>
                                <a href="inquiries.php" class="btn btn-secondary">Reset</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <?php if (empty($inquiries)): ?>
                    <p class="text-center">No inquiries found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inquiries as $inq): ?>
                                    <tr>
                                        <td><?php echo $inq['id']; ?></td>
                                        <td><?php echo $inq['name']; ?></td>
                                        <td><?php echo $inq['email']; ?></td>
                                        <td><?php echo $inq['subject']; ?></td>
                                        <td><?php echo date('d M Y', strtotime($inq['created_at'])); ?></td>
                                        <td>
                                            <?php if ($inq['status'] === 'new'): ?>
                                                <span class="badge bg-danger">New</span>
                                            <?php elseif ($inq['status'] === 'in_progress'): ?>
                                                <span class="badge bg-warning">In Progress</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Resolved</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="inquiries.php?view=<?php echo $inq['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <form action="inquiries.php" method="post" class="d-inline">
                                                <input type="hidden" name="inquiry_id" value="<?php echo $inq['id']; ?>">
                                                <button type="submit" name="delete_inquiry" class="btn btn-sm btn-danger delete-btn">
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
    <?php endif; ?>
</div>

<?php
// Include admin footer
include 'includes/footer.php';
?>