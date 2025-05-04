<?php
// Include necessary files
require_once '../includes/functions.php';
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!is_logged_in() || !is_admin()) {
    redirect('/mill/admin/login.php');
}

// Page title
$page_title = "SEO Analysis";

// Include header
include '../admin/includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../admin/includes/sidebar.php'; ?>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">SEO Analysis</h1>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">SEO Overview</h5>
                        </div>
                        <div class="card-body">
                            <p>This page provides an overview of the SEO status of your website. Use this information to improve your website's search engine visibility.</p>
                            
                            <div class="alert alert-info">
                                <strong>Note:</strong> Regular SEO audits are recommended to maintain and improve your search engine rankings.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">Meta Tags Status</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Title Tags
                                    <span class="badge bg-success rounded-pill">Implemented</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Meta Descriptions
                                    <span class="badge bg-success rounded-pill">Implemented</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Canonical URLs
                                    <span class="badge bg-success rounded-pill">Implemented</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Open Graph Tags
                                    <span class="badge bg-success rounded-pill">Implemented</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Twitter Card Tags
                                    <span class="badge bg-success rounded-pill">Implemented</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">Technical SEO Status</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    XML Sitemap
                                    <span class="badge bg-success rounded-pill">Implemented</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Robots.txt
                                    <span class="badge bg-success rounded-pill">Implemented</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Structured Data
                                    <span class="badge bg-success rounded-pill">Implemented</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Mobile Responsiveness
                                    <span class="badge bg-success rounded-pill">Implemented</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Page Speed Optimization
                                    <span class="badge bg-success rounded-pill">Implemented</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">SEO Tools</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Generate Sitemap</h5>
                                            <p class="card-text">Generate a new XML sitemap for your website.</p>
                                            <a href="../generate-sitemap.php" target="_blank" class="btn btn-primary">Generate Sitemap</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">View Robots.txt</h5>
                                            <p class="card-text">View your website's robots.txt file.</p>
                                            <a href="../robots.txt" target="_blank" class="btn btn-primary">View Robots.txt</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">SEO Recommendations</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Recommendation</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Register with Google Search Console</td>
                                            <td>High</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>Set up Google Analytics</td>
                                            <td>High</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>Create backlinks from reputable websites</td>
                                            <td>Medium</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>Optimize images with descriptive alt tags</td>
                                            <td>Medium</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>Implement SSL certificate</td>
                                            <td>High</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
// Include footer
include '../admin/includes/footer.php';
?>
