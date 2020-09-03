<?php require_once('../../../private/initialize.php'); ?>
<?php 


require_login();

$id = $_GET['id'] ?? '1';
$user = find_admin_by_id($id);

?>
<?php  $page_title = "Show Admin"; ?>
<?php  include(SHARED_PATH . '/staff_header.php'); ?>


<div id="content">
    <a href="<?php echo url_for('/staff/admin/index.php') ?>">&laquo; Back to List</a>
    <div class="admin show">
        
        <h1>Admin: <?php echo h($user['username']); ?> </h1>
        <div class="actions">
            <a class="action" href="<?php echo url_for('/staff/admin/edit.php?id='. h(u($user['id']))); ?>">Edit</a>
            <a class="action" href="<?php echo url_for('/staff/admin/delete.php?id='. h(u($user['id']))); ?>">Delete</a>
        </div>

        <div class="attributes">
            <dl>
                <dt>First name</dt>
                <dd><?php echo h($user['first_name']); ?></dd>
            </dl>
            <dl>
                <dt>Last name</dt>
                <dd><?php echo h($user['last_name']); ?></dd>
            </dl>
            <dl>
                <dt>Email</dt>
                <dd><?php echo h($user["email"]); ?></dd>
            </dl>
            <dl>
                <dt>Username</dt>
                <dd><?php echo h($user["username"]); ?></dd>
            </dl>
        </div> 
    </div>
</div>

<?php  include(SHARED_PATH . '/staff_footer.php'); ?>

