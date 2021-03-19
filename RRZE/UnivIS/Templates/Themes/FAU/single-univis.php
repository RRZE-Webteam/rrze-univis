<?php
get_header();
get_template_part('template-parts/hero', 'small'); ?>
    <div id="content" class="rrze-univis">
        <div class="container">
            <div class="row">
               <div <?php post_class( 'entry-content' ); ?>>
                    <main id="droppoint">
			<h1 id="droppoint" class="mobiletitle"><?php the_title(); ?></h1>
			<?php echo $data; ?>
			<nav class="navigation">
                            <div class="nav-previous"><a href="<?php echo get_permalink();?>"><span class="meta-nav">&laquo;</span> <?php _e('Back to overview', 'rrze-univis'); ?></a></div>
                        </nav>
                    </main>
             </div>
            </div>
        </div>
    </div>
<?php
get_template_part('template-parts/footer', 'social'); 
get_footer(); 

