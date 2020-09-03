<?php require_once('../../../private/initialize.php'); ?>
<?php 

require_login();

$id = $_GET['id'] ?? '1';
$subject = find_subject_by_id($id);
$pages_set = find_pages_by_subject_id($id);

?>
<?php  $page_title = "Show Subjects"; ?>
<?php  include(SHARED_PATH . '/staff_header.php'); ?>


<div id="content">
    <a href="<?php echo url_for('/staff/subjects') ?>">&laquo; Back to List</a>
    <div class="page show">
        <h1>Subject: <?php echo h($subject["menu_name"]) ?></h1>

        <dl>
            <dt>Menu Name</dt>
            <dd><?php echo h($subject["menu_name"]) ?></dd>
        </dl>
        <dl>
            <dt>Position</dt>
            <dd><?php echo h($subject["position"]) ?></dd>
        </dl>
        <dl>
            <dt>Visible</dt>
            <dd><?php echo $subject["visible"] == '1' ? 'true' : 'false' ?></dd>
        </dl>
    </div>

    <hr/>

    <div class="pages listing">
    <h1>Pages</h1>

    <div class="actions">
        <a class="action" href="<?php echo url_for('/staff/pages/new.php?subject_id=' . h(u($id))); ?>">Create Page</a>
    </div>

  	<table class="list">
  	    <tr>
            <th>ID</th>
            <!-- <th>Subject Name</th> -->
            <th>Position</th>
            <th>Visible</th>
            <th>Name</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
  	    </tr>

        <!-- data -->
        <?php while($page = mysqli_fetch_assoc($pages_set)) { ?>
            
            <tr>
                <td><?php echo h($page['id']) ?></td>        
                <!-- <td> -->
                  <?php 
                    // $subject = find_subject_by_id($page['subject_id']);
                    // echo h($subject['menu_name']);
                  ?>
                <!-- </td>         -->
                <td><?php echo h($page['position']) ?></td>        
                <td><?php echo $page['visible'] == 1 ? 'true' : 'false' ?></td>        
                <td><?php echo h($page['menu_name']); ?></td>        
                <td><a href="<?php echo url_for('/staff/pages/show.php?id=') . h(u($page['id'])) ?>">View</a></td>        
                <td><a href="<?php echo url_for('/staff/pages/edit.php?id=') . h(u($page['id'])) ?>">Edit</a></td>        
                <td><a href="<?php echo url_for('/staff/pages/delete.php?id=') . h(u($page['id'])) ?>">Delete</a></td>        
            </tr>
        <?php } ?>
  	</table>
    <?php mysqli_free_result($pages_set); ?>
  </div>
</div>
</div>


<?php  include(SHARED_PATH . '/staff_footer.php'); ?>

