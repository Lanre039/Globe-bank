<?php
    $subject_id = $subject_id ?? '';
    $page_id = $page_id ?? '';
    $visible = $visible ?? false;
?>


<navigation>
     
    <?php $nav_subjects = find_all_subjects(['visible' => $visible]); ?>
    <ul class="subjects">
        <?php while($nav_subject = mysqli_fetch_assoc($nav_subjects)) { ?>
            <li class="<?php echo $subject_id == $nav_subject['id'] ? "selected" : '' ?>">
                <a href="<?php echo url_for('index.php?subject_id=' . h(u($nav_subject['id']))); ?>">
                    <?php echo h($nav_subject['menu_name']); ?>  
                </a>
                
                <?php if ($subject_id == $nav_subject['id']) { ?>
                    <?php $nav_pages = find_pages_by_subject_id($nav_subject['id'], ['visible' => $visible]); ?>
                    <ul class="subjects">
                        <?php while($nav_page = mysqli_fetch_assoc($nav_pages)) { ?>
                        <li class="<?php if($page_id == $nav_page['id']) { echo "selected"; } ?>">
                        <a href="<?php echo url_for("index.php?id=" . h(u($nav_page['id']))); ?>">
                            <?php echo h($nav_page['menu_name']); ?>    
                        </a>
                        </li>
                        <?php } ?>
                        <?php mysqli_free_result($nav_pages); ?>
                    </ul>
                <?php } ?>
            </li>
        <?php } ?>
        <?php mysqli_free_result($nav_subjects); ?>
    </ul>
</navigation>
