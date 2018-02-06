<?php get_header(); ?>
<div id="content">
    <div class="container">
        <div class="row">
            <div class="span8">
                <?php echo $data; ?>
                <nav class="navigation">
                    <div class="nav-previous">
                        <a href="<?php echo get_permalink();?>"><span class="meta-nav">&laquo;</span> <?php _e('Back to overview', 'rrze-univis'); ?></a>
                    </div>
                </nav>          
            </div>
        </div>
    </div>
</div>
<?php get_footer();
