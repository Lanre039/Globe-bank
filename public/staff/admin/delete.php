<?php

require_once('../../../private/initialize.php');
require_login();

if(!isset($_GET['id'])) {
  redirect_to(url_for('/staff/admin/index.php'));
}

$id = $_GET['id'];

if(is_post_request()) {

    $result = delete_admin_user($id);
    if ($result) {
      $_SESSION['message'] = "The user was deleted successfully";
    }
    redirect_to(url_for('/staff/admin/index.php'));

} else {
    $admin = find_admin_by_id($id);  
}

?>

<?php $page_title = 'Delete Admin'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/admin/index.php'); ?>">&laquo; Back to List</a>

  <div class="subject delete">
    <h1>Delete User</h1>
    <p>Are you sure you want to delete this user?</p>
    <p class="item"><?php echo h($admin['username']); ?></p>

    <form action="<?php echo url_for('/staff/admin/delete.php?id=' . h(u($admin['id']))); ?>" method="post">
      <div id="operations">
        <input type="submit" name="commit" value="Delete User" />
      </div>
    </form>
  </div>

</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
