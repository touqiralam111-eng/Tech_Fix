<?php
echo "<h1>Website is Working!</h1>";
echo "<p>If you can see this, your server is running correctly.</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo '<a href="install.php">Run Installer</a>';
?>