<?php require_once('../../../private/initialize.php'); ?>
<?php 
require_login();

$id = $_GET['id'] ?? '1';

$page = find_page_by_id($id);

?>
<?php  $page_title = "Show Page"; ?>
<?php  include(SHARED_PATH . '/staff_header.php'); ?>


<div id="content">
    <a href="<?php echo url_for('/staff/subjects/show.php?id=' . h(u($page['subject_id']))) ?>">&laquo; Back to List</a>
    <div class="page show">
        <h1>Page: <?php echo h($page["menu_name"]) ?></h1>

        <div class="actions">
            <a class="action" href="<?php echo url_for('/index.php?id=' . h(u($page['id'])) . '&preview=true'); ?>" target="_blank">Preview</a>
        </div>

        <div class="attributes">
            <dl>
                <dt>Subject</dt>
                <dd>
                    <?php 
                        $subject = find_subject_by_id($page['subject_id']);
                        echo h($subject['menu_name']);
                    ?>
                </dd>
            </dl>
            <dl>
                <dt>Menu Name</dt>
                <dd><?php echo h($page["menu_name"]) ?></dd>
            </dl>
            <dl>
                <dt>Position</dt>
                <dd><?php echo h($page["position"]) ?></dd>
            </dl>
            <dl>
                <dt>Visible</dt>
                <dd><?php echo $page["visible"] == '1' ? 'true' : 'false' ?></dd>
            </dl>

            <dl>
                <dt>Content</dt>
                <dd>
                <textarea name="content" cols="60" rows="10"><?php echo h($page['content']); ?></textarea>
                </dd>
            </dl>
        </div>
        
    </div>
</div>

<?php  include(SHARED_PATH . '/staff_footer.php'); ?>

