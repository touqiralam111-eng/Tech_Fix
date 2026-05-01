<?php
// about.php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - TechFix | Professional IT Solutions Surat</title>
    <link rel="stylesheet" href="cssstyle.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">Tech<span>Fix</span></a>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="service_request.php">Services</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="about.php" class="active">About</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="container">
            <div class="card">
                <h1>About TechFix Surat</h1>
                <p>Your trusted partner for all IT solutions in Surat</p>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 2rem;">
                    <div>
                        <h2>Our Story</h2>
                        <p>TechFix was established with a vision to provide reliable and affordable IT solutions to the residents and businesses of Surat. With years of experience in computer repair, network setup, and IT support, we've built a reputation for excellence and customer satisfaction.</p>
                        
                        <h2>Our Mission</h2>
                        <p>To deliver top-quality IT services with transparency, reliability, and exceptional customer support. We believe in building long-term relationships with our clients through trust and outstanding service delivery.</p>
                    </div>
                    <div>
                        <h2>Why Choose Us?</h2>
                        <ul style="line-height: 2;">
                            <li>✅ Certified and experienced technicians</li>
                            <li>✅ 24/7 emergency support</li>
                            <li>✅ Transparent pricing with no hidden costs</li>
                            <li>✅ Quick response time</li>
                            <li>✅ 30-day service guarantee</li>
                            <li>✅ Home service available across Surat</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>