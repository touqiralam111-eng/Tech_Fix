<?php
// index.php - Updated with Your Details
include 'config.php';

// Get analytics safely
$total_visits = get_visit_count();
$total_users = get_user_count();

// Safe database queries
$total_services = 0;
$completed_services = 0;
$active_admins = 0;

$services_result = $conn->query("SELECT COUNT(*) as count FROM service_requests");
if ($services_result) {
    $total_services = $services_result->fetch_assoc()['count'];
}

$completed_result = $conn->query("SELECT COUNT(*) as count FROM service_requests WHERE status='completed'");
if ($completed_result) {
    $completed_services = $completed_result->fetch_assoc()['count'];
}

$admins_result = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type='admin'");
if ($admins_result) {
    $active_admins = $admins_result->fetch_assoc()['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFix - Professional IT Solutions & Computer Repair Services</title>
    <link rel="stylesheet" href="cssstyle.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <i class="fas fa-laptop-code"></i>Tech<span>Fix</span>
                </a>
                <ul class="nav-links">
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="service_request.php"><i class="fas fa-tools"></i> Services</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
                    <?php if (is_logged_in()): ?>
                        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <?php if (is_admin()): ?>
                            <li><a href="admin_dashboard.php"><i class="fas fa-cog"></i> Admin</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <div class="user-menu">
                    <?php if (is_logged_in()): ?>
                        <div class="user-info">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                            </div>
                            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </div>
                        <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Professional IT Solutions in Surat</h1>
            <p>Fast, reliable, and affordable tech support for all your computer and network needs. 24/7 expert assistance.</p>
            <div class="hero-buttons">
                <?php if (!is_logged_in()): ?>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-rocket"></i> Get Started Free
                    </a>
                <?php else: ?>
                    <a href="service_request.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Request Service
                    </a>
                <?php endif; ?>
                <a href="tel:+918511726065" class="btn btn-outline">
                    <i class="fas fa-phone"></i> Call Now: +91 8511726065
                </a>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="main-content">
        <div class="container">
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_visits; ?></div>
                    <div class="stat-label">Website Visits</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-label">Happy Clients</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_services; ?></div>
                    <div class="stat-label">Services Provided</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $completed_services; ?></div>
                    <div class="stat-label">Projects Completed</div>
                </div>
            </div>

            <!-- Services Section with Detailed Pricing -->
            <div class="card" id="services">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-tools"></i> Our IT Services & Pricing</h2>
                    <p>Comprehensive technology solutions with transparent pricing</p>
                </div>
                <div class="services-grid">
                    <div class="service-item">
                        <i class="fas fa-laptop-medical"></i>
                        <h3>Computer Repair & Maintenance</h3>
                        <p><strong>Starting at ₹500</strong></p>
                        <ul style="text-align: left; margin-top: 1rem; color: #666;">
                            <li>Virus & Malware Removal - ₹500-1500</li>
                            <li>Hardware Repair - ₹800-3000</li>
                            <li>Software Installation - ₹300-1000</li>
                            <li>OS Installation - ₹800-1500</li>
                            <li>PC Tune-up - ₹500</li>
                        </ul>
                        <div style="margin-top: 1rem;">
                            <small style="color: var(--success);"><i class="fas fa-clock"></i> 1-4 Hours</small>
                        </div>
                    </div>

                    <div class="service-item">
                        <i class="fas fa-network-wired"></i>
                        <h3>Network Setup & Support</h3>
                        <p><strong>Starting at ₹1000</strong></p>
                        <ul style="text-align: left; margin-top: 1rem; color: #666;">
                            <li>WiFi Router Setup - ₹1000-2500</li>
                            <li>LAN Network Setup - ₹1500-5000</li>
                            <li>Network Security - ₹2000-8000</li>
                            <li>VPN Configuration - ₹1500-4000</li>
                            <li>Network Troubleshooting - ₹800/hour</li>
                        </ul>
                        <div style="margin-top: 1rem;">
                            <small style="color: var(--success);"><i class="fas fa-clock"></i> 2-6 Hours</small>
                        </div>
                    </div>

                    <div class="service-item">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Security Solutions</h3>
                        <p><strong>Starting at ₹1500</strong></p>
                        <ul style="text-align: left; margin-top: 1rem; color: #666;">
                            <li>Antivirus Installation - ₹500-2000</li>
                            <li>Firewall Setup - ₹2000-6000</li>
                            <li>Data Encryption - ₹1500-4000</li>
                            <li>Security Audit - ₹3000-8000</li>
                            <li>Cybersecurity Consultation - ₹1000/hour</li>
                        </ul>
                        <div style="margin-top: 1rem;">
                            <small style="color: var(--success);"><i class="fas fa-clock"></i> 2-8 Hours</small>
                        </div>
                    </div>

                    <div class="service-item">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <h3>Cloud Services</h3>
                        <p><strong>Starting at ₹2000</strong></p>
                        <ul style="text-align: left; margin-top: 1rem; color: #666;">
                            <li>Cloud Backup Setup - ₹2000-6000</li>
                            <li>Cloud Migration - ₹5000-15000</li>
                            <li>Office 365 Setup - ₹1500-4000</li>
                            <li>Google Workspace - ₹1500-4000</li>
                            <li>Cloud Storage Setup - ₹1000-3000</li>
                        </ul>
                        <div style="margin-top: 1rem;">
                            <small style="color: var(--success);"><i class="fas fa-clock"></i> 4-12 Hours</small>
                        </div>
                    </div>

                    <div class="service-item">
                        <i class="fas fa-database"></i>
                        <h3>Data Recovery</h3>
                        <p><strong>Starting at ₹1500</strong></p>
                        <ul style="text-align: left; margin-top: 1rem; color: #666;">
                            <li>Hard Drive Recovery - ₹1500-8000</li>
                            <li>SSD Data Recovery - ₹2000-10000</li>
                            <li>RAID Recovery - ₹5000-20000</li>
                            <li>USB/Pen Drive Recovery - ₹1000-4000</li>
                            <li>Backup Solution Setup - ₹2000-6000</li>
                        </ul>
                        <div style="margin-top: 1rem;">
                            <small style="color: var(--success);"><i class="fas fa-clock"></i> 2-24 Hours</small>
                        </div>
                    </div>

                    <div class="service-item">
                        <i class="fas fa-mobile-alt"></i>
                        <h3>Mobile & Device Support</h3>
                        <p><strong>Starting at ₹300</strong></p>
                        <ul style="text-align: left; margin-top: 1rem; color: #666;">
                            <li>Smartphone Repair - ₹300-3000</li>
                            <li>Tablet Repair - ₹500-4000</li>
                            <li>Software Update - ₹300-800</li>
                            <li>Data Transfer - ₹500-1500</li>
                            <li>Device Setup - ₹400-1000</li>
                        </ul>
                        <div style="margin-top: 1rem;">
                            <small style="color: var(--success);"><i class="fas fa-clock"></i> 1-3 Hours</small>
                        </div>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 2rem;">
                    <div style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 1.5rem; border-radius: var(--radius); display: inline-block;">
                        <h4><i class="fas fa-home"></i> Home Service Available</h4>
                        <p>We provide doorstep service across Surat with ₹200 additional charge</p>
                    </div>
                </div>
            </div>

            <!-- Why Choose Us -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-star"></i> Why Choose TechFix Surat?</h2>
                    <p>Experience the difference with our premium IT services</p>
                </div>
                <div class="features-grid">
                    <div class="feature-item">
                        <i class="fas fa-clock"></i>
                        <h4>24/7 Support</h4>
                        <p>Round-the-clock technical support with rapid response times for all your urgent IT needs in Surat.</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-award"></i>
                        <h4>Certified Experts</h4>
                        <p>Our technicians hold industry certifications and have years of hands-on experience in computer repair.</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-rupee-sign"></i>
                        <h4>Affordable Pricing</h4>
                        <p>Competitive, transparent pricing with no hidden costs. Quality service that fits your budget.</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <h4>Quality Guarantee</h4>
                        <p>We stand behind our work with comprehensive 30-day service guarantees and ongoing support.</p>
                    </div>
                </div>
            </div>

            <!-- Contact & Location -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-map-marker-alt"></i> Our Location & Contact</h2>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start;">
                    <div>
                        <h3 style="color: var(--primary); margin-bottom: 1rem;">TechFix Surat</h3>
                        <div style="margin-bottom: 1.5rem;">
                            <p><strong><i class="fas fa-map-pin"></i> Address:</strong></p>
                            <p>Rampura Petrol Pump Main Road<br>Surat, Gujarat 395003</p>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <p><strong><i class="fas fa-phone"></i> Phone:</strong></p>
                            <p><a href="tel:+918511726065" style="color: var(--primary); text-decoration: none;">+91 8511726065</a></p>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <p><strong><i class="fas fa-envelope"></i> Email:</strong></p>
                            <p><a href="mailto:touqiralam111@gmail.com" style="color: var(--primary); text-decoration: none;">touqiralam111@gmail.com</a></p>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <p><strong><i class="fas fa-clock"></i> Business Hours:</strong></p>
                            <p>Monday - Sunday: 8:00 AM - 10:00 PM<br>Emergency Support: 24/7 Available</p>
                        </div>
                    </div>
                    <div style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 2rem; border-radius: var(--radius); text-align: center;">
                        <i class="fas fa-headset fa-3x" style="margin-bottom: 1rem;"></i>
                        <h3>Ready to Get Started?</h3>
                        <p style="margin-bottom: 2rem;">Contact us for a free consultation and quote</p>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <a href="tel:+918511726065" class="btn" style="background: white; color: var(--primary);">
                                <i class="fas fa-phone"></i> Call Now
                            </a>
                            <a href="https://wa.me/918511726065" class="btn" style="background: #25D366; color: white;">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <a href="contact.php" class="btn btn-outline" style="border-color: white; color: white;">
                                <i class="fas fa-envelope"></i> Send Message
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 TechFix Surat. All rights reserved. | Professional IT Solutions & Computer Repair Services</p>
            <div class="footer-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="service_request.php"><i class="fas fa-tools"></i> Services</a>
                <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
                <a href="about.php"><i class="fas fa-info-circle"></i> About</a>
                <a href="privacy.php"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
                <a href="terms.php"><i class="fas fa-file-contract"></i> Terms of Service</a>
            </div>
            <div style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.8;">
                <p><i class="fas fa-map-marker-alt"></i> Rampura Petrol Pump Main Road, Surat - 395003</p>
                <p><i class="fas fa-phone"></i> <a href="tel:+918511726065" style="color: white;">+91 8511726065</a> | 
                   <i class="fas fa-envelope"></i> <a href="mailto:touqiralam111@gmail.com" style="color: white;">touqiralam111@gmail.com</a></p>
            </div>
        </div>
    </footer>

    <script src="jsscript.js"></script>
    <!-- WhatsApp Float Button -->
<a href="https://wa.me/918511726065" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i>
</a>

</body>
</html>