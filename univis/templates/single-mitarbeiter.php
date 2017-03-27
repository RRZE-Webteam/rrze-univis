<?php
global $univis_data;  

get_header(); ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <div id="content">
            <div class="container">
                <div class="row">
                    <div class="span8">
                    <?php 
		    echo $univis_data;
                    ?>
          
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile;
get_footer();
