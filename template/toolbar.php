<div class="wolfnet_toolbar <?php echo $toolbarClass; ?>" data-numrows="<?php echo $numrows ?>" data-startrow="<?php echo $startrow ?>">
    <?php if ($paginated) { ?>
        <a href="<?php echo $prevLink; ?>" title="Previous Page" class="wolfnet_page_nav wolfnet_page_nav_prev <?php echo $prevClass; ?>" rel="follow">
            <span>Previous</span>
        </a>
    <?php } ?>
    <span class="wolfnet_page_info">
        <?php if ($paginated) { ?>
            <span class="wolfnet_page_items">
                <span class="wolfnet_page_start"><?php echo $startrow; ?></span>-<span class="wolfnet_page_end"><?php echo $lastitem; ?></span>
                 of
                <span class="wolfnet_page_total"><?php echo $maxresults; ?></span>
            </span>
        <?php } ?>
    </span>
    <?php if ($paginated) { ?>
        <a href="<?php echo $nextLink; ?>" title="Next Page" class="wolfnet_page_nav wolfnet_page_nav_next <?php echo $nextClass; ?>" rel="follow">
            <span>Next</span>
        </a>
    <?php } ?>
</div>
