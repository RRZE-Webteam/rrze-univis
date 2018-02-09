<?php
get_header();
get_template_part('template-parts/hero', 'small'); ?>
<div id="content">
    <div class="container">
        <?php echo fau_get_ad('werbebanner_seitlich', false); ?>

        <div class="row">

                <main>
                    <?php echo $data; ?>
                    <nav class="navigation">
                        <div class="nav-previous">
                            <a href="<?php echo get_permalink();?>"><span class="meta-nav">&laquo;</span> <?php _e('Back to overview', 'rrze-univis'); ?></a>
                        </div>
                    </nav>
                </main>

        </div>
    </div>
</div>
<?php get_footer();
