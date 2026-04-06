<?php
require 'authentication.php'; // admin authentication check

// auth check
$user_id = $_SESSION['admin_id'];
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
}

// check admin
$user_role = $_SESSION['user_role'];
if ($user_role != 1) {
  header('Location: task-info.php');
}

// Get current admin's profile and department
$current_admin_sql = "SELECT a.*, d.dept_name FROM tbl_admin a LEFT JOIN departments d ON a.dept_id = d.dept_id WHERE a.user_id = :user_id";
$current_admin_stmt = $obj_admin->db->prepare($current_admin_sql);
$current_admin_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$current_admin_stmt->execute();
$current_admin_info = $current_admin_stmt->fetch(PDO::FETCH_ASSOC);
$current_admin_dept_id = $current_admin_info['dept_id'];

$page_name="Admin";
include("include/sidebar.php");

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
/* Header Style (Modern Gradient) */
.task-header {
    background: linear-gradient(135deg, #0f0096ff 0%, #00d9ffff 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-top: 20px;
    margin-bottom: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.task-header h2 {
    margin-top: 10px;
    font-weight: 700;
}

/* Navigation Tabs
.nav-tabs-custom {
    border-bottom: 2px solid #e0e0e0;
    margin-bottom: 20px;
}
.nav-tabs-custom > li > a {
    color: #667eea;
    font-weight: 600;
    padding: 10px 15px;
    border: none;
    border-radius: 0;
    transition: all 0.3s ease;
}
.nav-tabs-custom > li > a:hover {
    background-color: #f0f0f0;
    color: #667eea;
} *//* Navigation Tabs */
.nav-tabs-custom {
    border-bottom: 2px solid #e0e0e0;
    margin-bottom: 20px;
    background: white;
    border-radius: 10px 10px 0 0;
}

.nav-tabs-custom > li > a {
    color: #001f3f;
    font-weight: 600;
    padding: 12px 20px;
    border: none;
    border-radius: 0;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-tabs-custom > li > a:hover {
    background-color: #f0f8ff;
    color: #d4af37;
}

.nav-tabs-custom > .active > a,
.nav-tabs-custom > .active > a:hover,
.nav-tabs-custom > .active > a:focus {
    color: white;
    /* background: linear-gradient(135deg, #ffffffff 0%, #003366 100%); */
    border-color: #001f3f;
    border-radius: 15px;
    border-bottom: 3px solid #d4af37;
}
.nav-tabs-custom > .active > a,
.nav-tabs-custom > .active > a:hover,
.nav-tabs-custom > .active > a:focus {
    /* color: #fff;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    border-radius: 5px 5px 0 0; */
    color: white;
    /* background: linear-gradient(135deg, #ffffffff 0%, #003366 100%); */
    border-color: #001f3f;
    border-radius: 15px;
    border-bottom: 3px solid #d4af37;
}

/* Current User Profile Card */
.current-user-card {
    /* background: linear-gradient(135deg, #091F3B 0%, #E2B840 120%); */
    background:#091F3B;
    color: #E2B840;
    border-radius: 35px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 8px 25px rgba(0, 31, 63, 0.2);
    position: relative;
    overflow: hidden;
    border-top: 4px solid #d4af37;
}

.current-user-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(212, 175, 55, 0.08);
    border-radius: 50%;
}

.current-user-card::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -5%;
    width: 250px;
    height: 250px;
    background: rgba(212, 175, 55, 0.08);
    border-radius: 50%;
}

.current-user-card-content {
    position: relative;
    z-index: 2;
}

.current-user-avatar {
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    margin-bottom: 20px;
    border: 3px solid rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(10px);
}

.current-user-card h3 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 700;
}

.current-user-card p {
    margin: 8px 0;
    font-size: 14px;
    opacity: 0.95;
}

.current-user-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.info-item {
    background: rgba(255, 255, 255, 0.1);
    padding: 15px;
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.info-item-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.85;
    margin-bottom: 5px;
    display: block;
}

.info-item-value {
    font-size: 16px;
    font-weight: 600;
}

.current-user-actions {
    display: flex;
    gap: 12px;
    margin-top: 25px;
    position: relative;
    z-index: 10;
}

.btn-update-profile {
    background: rgba(255, 255, 255, 0.2);
    color: #E2B840;
    border: 2px solid rgba(255, 255, 255, 0.4);
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    font-size: 16px;
}

.btn-update-profile:hover {
    background: #091F3B;
    border-color: rgba(255, 255, 255, 0.6);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: #E2B840;
}

.btn-change-password {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.4);
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    font-size: 14px;
}

.btn-change-password:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.6);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Task Card */
.task-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border-left: 5px solid #667eea;
    transition: all 0.3s ease;
}

.task-card:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

/* Table Enhancements */
.table-custom {
    margin-bottom: 0;
}

.table-custom thead tr {
    background: #eaeaea;
}

.table-custom thead tr th {
    color: #001f3f;
    font-weight: 700;
    border-bottom: 2px solid #d4af37;
    padding: 16px;
    text-transform: uppercase;
    font-size: 16px;
    letter-spacing: 0.5px;
}

.table-custom tbody tr {
    transition: all 0.2s ease;
}

.table-custom tbody tr:hover {
  color: #ffffffff;
    background-color: #02238fff;
    box-shadow: inset 0 0 0 1px #e8e8f0;
}

.table-custom tbody tr td {
    vertical-align: middle;
    padding: 15px;
    color: #ffffffff;
    font-size: 14px;
}

/* Admin Name */
/* .admin-name {
    font-weight: 600;
    color: #667eea;
} */

/* Badge */
.badge-current {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-star {
    font-size: 14px;
}

/* Message Box */
.message-box {
    background: #f0f4ff;
    border-left: 4px solid #667eea;
    color: #334e99;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.message-box i {
    font-size: 18px;
}

/* Lock Icon for Protected Records */
.lock-icon {
    color: #999;
    font-size: 14px;
    opacity: 0.7;
}

/* No Action Column */
.no-action-text {
    color: #999;
    font-style: italic;
    font-size: 13px;
}

/* Responsive */
@media (max-width: 768px) {
    .current-user-card {
        padding: 20px;
    }

    .current-user-info-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .current-user-actions {
        flex-direction: column;
    }

    .btn-update-profile, .btn-change-password {
        width: 100%;
        justify-content: center;
    }

    .table-custom thead {
        display: none;
    }

    .table-custom tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
    }

    .table-custom td {
        display: block;
        text-align: right;
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .table-custom td::before {
        content: attr(data-label);
        float: left;
        font-weight: 600;
        color: #667eea;
    }

    .table-custom td:last-child {
        border-bottom: none;
    }
}
</style>

<div class="row">
    <div class="col-md-12">
        <div class="task-header">
            <h2><i class="fa fa-users"></i> Manage Admin Accounts</h2>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">Manage superuser credentials and profiles</p>
        </div>
    </div>
</div>

<!-- Current User Profile Card -->
<div class="row">
    <div class="col-md-12">
        <div class="current-user-card">
            <div class="current-user-card-content">
                <div class="current-user-avatar">
                    <?php
                    $initials = '';
                    $names = explode(' ', $current_admin_info['fullname']);
                    if (isset($names[0])) $initials .= strtoupper(substr($names[0], 0, 1));
                    if (isset($names[1])) $initials .= strtoupper(substr($names[1], 0, 1));
                    if (empty($initials)) $initials = strtoupper(substr($current_admin_info['fullname'], 0, 2));
                    echo htmlspecialchars($initials);
                    ?>
                </div>

                <h3><?php echo htmlspecialchars($current_admin_info['fullname']); ?></h3>
                <p><i class="fa fa-user-circle"></i> Super Admin Account</p>

                <div class="current-user-info-grid">
                    <div class="info-item">
                        <span class="info-item-label"><i class="fa fa-user"></i> Username</span>
                        <span class="info-item-value"><?php echo htmlspecialchars($current_admin_info['username']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-item-label"><i class="fa fa-phone"></i> Contact</span>
                        <span class="info-item-value"><?php echo htmlspecialchars($current_admin_info['contact']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-item-label"><i class="fa fa-building"></i> Department</span>
                        <span class="info-item-value"><?php echo htmlspecialchars($current_admin_info['dept_name'] ?? 'Not Assigned'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-item-label"><i class="fa fa-shield"></i> Role</span>
                        <span class="info-item-value">Super Admin</span>
                    </div>
                </div>

                <div class="current-user-actions">
                    <a href="update-admin.php?admin_id=<?php echo htmlspecialchars($current_admin_info['user_id']); ?>" class="btn-update-profile">
                        <i class="fa fa-edit"></i> Edit Profile
                    </a>
                    <!-- <a href="changePasswordForEmployee.php" class="btn-change-password">
                        <i class="fa fa-key"></i> Change Password
                    </a> -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Box -->
<div class="row">
    <div class="col-md-12">
        <div class="message-box">
            <i class="fa fa-info-circle"></i>
            <span><strong>Note:</strong> Each admin can only update their own profile. Other admin accounts are for reference only.</span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="task-card">
            <ul class="nav nav-tabs nav-justified nav-tabs-custom">
                <li class="active"><a href="manage-admin.php"><i class="fa fa-users"></i> Manage Admin</a></li>
                <li><a href="admin-manage-user.php"><i class="fa fa-user-tie"></i> Manage Employee</a></li>
            </ul>
            <div class="gap"></div>

            <div class="table-responsive">
                <table class="table table-codensed table-custom">
                    <thead>
                        <tr>
                            <th>Serial No.</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Username</th>
                            <th>Department</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                        $sql = "SELECT a.*, d.dept_name FROM tbl_admin a LEFT JOIN departments d ON a.dept_id = d.dept_id WHERE a.user_role = 1 ORDER BY a.fullname ASC";
                        $info = $obj_admin->manage_all_info($sql);

                        $serial  = 1;
                        while( $row = $info->fetch(PDO::FETCH_ASSOC) ){
                            $is_current_user = ($row['user_id'] == $user_id);
                    ?>
                        <tr>
                            <td data-label="Serial"><?php echo $serial; $serial++; ?></td>
                            <td data-label="Name">
                                <span class="admin-name"><?php echo htmlspecialchars($row['fullname']); ?></span>
                                <?php if ($is_current_user) { ?>
                                    <span class="badge-current">
                                        <i class="fa fa-star badge-star"></i> You
                                    </span>
                                <?php } ?>
                            </td>
                            <td data-label="Contact"><?php echo htmlspecialchars($row['contact']); ?></td>
                            <td data-label="Username"><?php echo htmlspecialchars($row['username']); ?></td>
                            <td data-label="Department"><?php echo htmlspecialchars($row['dept_name'] ?? 'Not Assigned'); ?></td>
                            <td data-label="Status">
                                <?php if ($is_current_user) { ?>
                                    <span class="badge-current">
                                        <i class="fa fa-lock"></i> Editable
                                    </span>
                                <?php } else { ?>
                                    <span class="no-action-text">
                                        <i class="fa fa-lock lock-icon"></i> Protected
                                    </span>
                                <?php } ?>
                            </td>
                        </tr>

                    <?php  } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php
if(isset($_SESSION['update_user_pass'])){
    echo '<script>alert("✓ Password updated successfully");</script>';
    unset($_SESSION['update_user_pass']);
}
if(isset($_SESSION['update_user'])){
    echo '<script>alert("✓ Admin profile updated successfully");</script>';
    unset($_SESSION['update_user']);
}
include("include/footer.php");

?>
