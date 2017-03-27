<?php
global $univis_data; 
global $options;

get_header(); ?>
    <?php while ( have_posts() ) : the_post(); ?>
<?php get_template_part('hero', 'small'); ?>
        <div id="content">
            <div class="container">
                <?php 
		echo fau_get_ad('werbebanner_seitlich',false);
		?>
                
                <div class="row">
                    <div class="span12">
                        				    <main>
                    <?php 

		    echo $univis_data;
                    ?>
              </main>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile;
get_footer();
