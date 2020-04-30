<?php
get_header();
get_template_part('template-parts/hero', 'small'); ?>
<div id="content">
    <div class="container">

        <div class="row">

                <main class="col-xs-12" id="droppoint">
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
<?php
get_template_part('template-parts/footer', 'social'); 
get_footer(); 

