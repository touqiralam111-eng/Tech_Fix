<?php
// terms.php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - TechFix</title>
    <link rel="stylesheet" href="cssstyle.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">Tech<span>Fix</span></a>
                <a href="index.php" class="btn btn-outline">Back to Home</a>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="container">
            <div class="card">
                <h1>Terms of Service</h1>
                <p>Last updated: <?php echo date('F d, Y'); ?></p>
                
                <h2>Service Agreement</h2>
                <p>By using our services, you agree to these terms. We provide IT repair and support services with guaranteed quality.</p>
                
                <h2>Pricing and Payments</h2>
                <p>All prices are in Indian Rupees (₹). Payment is due upon service completion unless otherwise arranged.</p>
                
                <h2>Service Guarantee</h2>
                <p>We offer a 30-day guarantee on all repairs and services. Contact us for warranty claims.</p>
            </div>
        </div>
    </div>
</body>
</html>