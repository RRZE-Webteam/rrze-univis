<article id="person-<?php the_ID(); ?>">
    <header class="entry-header">
        <h1><?php the_title(); ?></h1>           
    </header>
    <div class="entry-content">
    <?php echo $data; ?>
    <nav id="nav-pages">
        <div class="navmenu-previous">
            <a href="<?php echo get_permalink();?>"><span class="meta-nav">&laquo;</span> <?php _e('Back to overview', 'rrze-univis'); ?></a>
        </div>
    </nav>
    </div>
    <footer class="entry-meta">
        <?php edit_post_link( __( '(Edit)', 'rrze-univis'), '<div class="ym-wbox"><span class="edit-link">', '</span></div>' ); ?>
    </footer>
</article>
