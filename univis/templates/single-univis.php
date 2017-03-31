<?php
global $univis_data;

get_header();
?>
<?php while (have_posts()) : the_post(); ?>
    <div id="content">
        <div class="container">
            <div class="row">
                <div class="span8">
                    <?php
                    echo $univis_data;
                    ?>
                    <p></p>
                    <nav class="navigation">
                        <div class="nav-previous"><a href="<?php echo get_permalink();?>"><?php _e('<span class="meta-nav">&laquo;</span> Zurück zur Übersicht', RRZE_UnivIS::textdomain); ?></a></div>
                    </nav>          
                </div>
            </div>
        </div>
    </div>
<?php
endwhile;
get_footer();
