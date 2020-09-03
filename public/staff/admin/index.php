<?php 

require_once('../../../private/initialize.php'); 
require_login();

$user_set = find_all_admin_user();

?>

<?php $page_title = "Admin" ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
<div class="subjects listing">
    <h1>Admin</h1>

    <div class="actions">
        <a class="action" href="<?php echo url_for('/staff/admin/new.php'); ?>">Create Admin</a>
    </div>

  	<table class="list">
  	    <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Username</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
          
        <?php while($user = mysqli_fetch_assoc($user_set)) { ?>
            <tr>
                <?php 
                    echo "<td>{$user['id']}</td>";
                    echo "<td>{$user['first_name']}</td>";
                    echo "<td>{$user['last_name']}</td>";
                    echo "<td>{$user['email']}</td>";
                    echo "<td>{$user['username']}</td>";
                ?>
                <td><a href="<?php echo url_for('/staff/admin/show.php?id=') . h(u($user['id'])) ?>">View</a></td>        
                <td><a href="<?php echo url_for('/staff/admin/edit.php?id=') . h(u($user['id'])) ?>">Edit</a></td>        
                <td><a href="<?php echo url_for('/staff/admin/delete.php?id=') . h(u($user['id'])) ?>">Delete</a></td> 
            </tr>
        <?php } ?>
        <?php mysqli_free_result($user_set); ?>
  	</table>
  </div>
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>