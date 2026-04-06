<?php

require 'authentication.php'; // admin authentication check

// auth check
$user_id = $_SESSION['admin_id'];
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
}


// Safely check for 'admin_id'
$admin_id = isset($_GET['admin_id']) ? $_GET['admin_id'] : '';

if(isset($_POST['update_current_employee'])){

    $obj_admin->update_admin_data($_POST,$admin_id);
}

if(isset($_POST['btn_user_password'])){

    $obj_admin->update_user_password($_POST,$admin_id);
}

$sql = "SELECT * FROM tbl_admin WHERE user_id='$admin_id' ";
$info = $obj_admin->manage_all_info($sql);
$row = $info->fetch(PDO::FETCH_ASSOC);

$page_name="Admin";
include("include/sidebar.php");

// Fetch departments
$departments_sql = "SELECT dept_id, dept_name FROM departments ORDER BY dept_name ASC";
$departments_info = $obj_admin->manage_all_info($departments_sql);
$departments = $departments_info->fetchAll(PDO::FETCH_ASSOC);

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

/* Card Style for Main Content (Modern and Clean) */
.task-card {
    background: white;
    border-radius: 15px;
    padding: 25px; /* Slightly increased padding */
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border-left: 5px solid #667eea;
}

/* Form Controls and Alignment Fix */
.form-horizontal .form-group {
    margin-bottom: 20px;
}
/* Ensure labels are correctly aligned for large screens */
.form-horizontal label {
    font-weight: 600;
    color: #333;
    padding-top: 7px;
    text-align: right;
}
/* This ensures labels stack left-aligned on small screens (Bootstrap default for form-horizontal) */
@media (max-width: 767px) {
    .form-horizontal label {
        text-align: left;
    }
}

.form-control {
    border-radius: 8px;
    height: 45px;
    padding: 10px 15px;
}

/* Navigation Tabs */
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
}
.nav-tabs-custom > .active > a,
.nav-tabs-custom > .active > a:hover,
.nav-tabs-custom > .active > a:focus {
    color: #fff;
    background-color: #667eea;
    border-color: #667eea;
    border-radius: 5px 5px 0 0;
}

/* Button Styles */
.btn-success-custom { /* Update Details */
    background: #28a745;
    color: white;
    border: none;
    width: 100%;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
    margin-right: 10px;
    margin-bottom: 10px;
}

.btn-default-custom { /* Go Back Button Style */
    background: #f0f0f0;
    color: #667eea;
    border: 1px solid #dcdcdc;
    width: 100%;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
    position: relative;
}

.btn-primary { /* Change Password */
    background: #667eea;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
}

.btn-success { /* Password Submit */
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
}
</style>


    <div class="row">
      <div class="col-md-12">
        <div class="container-fluid">
            <div class="task-header">
                <h2><i class="fa fa-user-circle"></i> Edit Admin Account</h2>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Update administrator details and manage credentials</p>
            </div>

            <ul class="nav nav-tabs nav-justified nav-tabs-custom">
                <li class="active"><a href="manage-admin.php">Manage Admin</a></li>
                <li><a href="admin-manage-user.php">Manage Employee</a></li>
            </ul>
            <div class="gap"></div>

            <div class="task-card">
                <div class="row">
                    <div class="col-md-7">
                        <h3 style="margin-top: 0; margin-bottom: 15px; color: #667eea;"><i class="fa fa-info-circle"></i> Admin Details</h3>

                        <form class="form-horizontal" role="form" action="" method="post" autocomplete="off">

                            <div class="form-group">
                              <label class="control-label col-sm-4">Fullname</label>
                              <div class="col-sm-8">
                                <input type="text" value="<?php echo htmlspecialchars($row['fullname'] ?? ''); ?>" placeholder="Enter Admin Name" name="em_fullname" class="form-control" required>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="control-label col-sm-4">Username</label>
                              <div class="col-sm-8">
                                <input type="text" value="<?php echo htmlspecialchars($row['username'] ?? ''); ?>" placeholder="Enter Admin Username" name="em_username" class="form-control" required>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="control-label col-sm-4">Contact</label>
                              <div class="col-sm-8">
                                <input type="tel" value="<?php echo htmlspecialchars($row['contact'] ?? ''); ?>" placeholder="Enter Admin Contact" name="em_email" class="form-control" required>
                              </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Department</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="dept_id">
                                        <option value="">Select Department</option>
                                        <?php foreach($departments as $dept){ ?>
                                            <option value="<?php echo htmlspecialchars($dept['dept_id']); ?>" <?php if($dept['dept_id'] == $row['dept_id']) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($dept['dept_name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                              <div class="col-sm-offset-4 col-sm-8">
                                <button type="submit" name="update_current_employee" class="btn btn-success-custom"><i class="fa fa-save"></i> Update Details</button>

                                <button type="button" onclick="window.history.back()" class="btn btn-default-custom"><i class="fa fa-arrow-left"></i> Go Back</button>
                              </div>
                            </div>
                          </form>
                    </div>

                    <div class="col-md-5" style="border-left: 1px solid #eee; padding-left: 30px;">
                        <h3 style="margin-top: 0; margin-bottom: 15px; color: #667eea;"><i class="fa fa-key"></i> Manage Credentials</h3>
                        <button id="admin_pass_btn" class="btn btn-primary"><i class="fa fa-unlock-alt"></i> Change Password</button>

                        <form action="" method="POST" id="admin_pass_cng" style="display: none; margin-top: 15px;">
                            <div class="form-group">
                                <label for="admin_password">New Password:</label>
                                <input type="password" name="admin_password" class="form-control" id="admin_password" minlength="8" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="btn_user_password" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>


<?php

include("include/footer.php");

?>

<script type="text/javascript">
// Toggle password form visibility
$('#admin_pass_btn').click(function(){
    $('#admin_pass_cng').toggle('slow');
});

</script>
