<?php
/**
 * Navigation Bar.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <say@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
?>
<nav class="navbar navbar-inverse" style="margin-bottom: 0;border-radius: 0;">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="welcome.php"><img src="favicon.ico"></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'welcome.php' ? 'active' : ''; ?>"><a href="welcome.php">Anasayfa</a></li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'app/views/approve_bulk.php' || basename($_SERVER['PHP_SELF']) == 'submit_bulk.php' ? 'active' : ''; ?>"><a href="app/views/approve_bulk.php">Toplu Onay</a></li>
        
        <?php if ($_SESSION["usertype"] == "superuser" || $_SESSION["usertype"] == "admin") : ?>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'pending.php' ? 'active' : ''; ?>"><a href="pending.php">Bekleyen Faturalar</a></li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'pending_finance.php' ? 'active' : ''; ?>"><a href="pending_finance.php">Finans Bekleyen</a></li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'process_bulk.php' ? 'active' : ''; ?>"><a href="process_bulk.php">Toplu İşleme</a></li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'upload.php' ? 'active' : ''; ?>"><a href="upload.php">Fatura Yükle</a></li>
        <?php endif; ?>
        
        <?php if ($_SESSION["usertype"] == "superuser") : ?>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manageusers.php' ? 'active' : ''; ?>"><a href="manageusers.php">Manage Users</a></li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'app/views/allinvoices.php' ? 'active' : ''; ?>"><a href="app/views/allinvoices.php" onclick="confirmNavigation(event, 'app/views/allinvoices.php')">Tüm Faturalar</a></li>
        <?php endif; ?>      
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>"><a href="profile.php">Profile (<?php echo $_SESSION["username"]; ?>)</a></li>
      </ul>
    </div>
  </div>
</nav>

<script>
    function confirmNavigation(event, url) {
      event.preventDefault(); // Prevent the default anchor behavior
      if (confirm('Tüm Faturaları Aç?')) {
        window.location.href = url; // Redirect if confirmed
      }
    }
</script>