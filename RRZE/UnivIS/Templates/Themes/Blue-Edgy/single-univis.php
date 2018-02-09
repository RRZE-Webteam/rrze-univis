<?php get_header(); ?>

<div id="main-column" class="ym-column linearize-level-1">

    <?php if (_rrze_theme_options('column.layout') == '1-2-3') : ?>
        <aside class="ym-col1 ym-noprint">
            <?php get_sidebar('left'); ?>
        </aside>

        <aside class="ym-col2 ym-noprint">
            <?php get_sidebar('right'); ?>
        </aside>

        <div class="ym-col3">
            <div class="ym-cbox ym-clearfix">
                <?php include 'content-univis.php'; ?>
            </div>
        </div>
        <!-- .ym-ie-clearing only needed for IE6 & 7 support -->
        <div class="ym-ie-clearing">&nbsp;</div>        

    <?php elseif (_rrze_theme_options('column.layout') == '1-3') : ?>
        <aside class="ym-col1 ym-noprint">
            <?php get_sidebar('left'); ?>
        </aside>

        <div class="ym-col3">
            <div class="ym-cbox-right">
                <?php include 'content-univis.php'; ?>
            </div>
        </div>

    <?php elseif (_rrze_theme_options('column.layout') == '2-3') : ?>
        <aside class="ym-col2 ym-noprint">
            <?php get_sidebar('right'); ?>
        </aside>

        <div class="ym-col3">
            <div class="ym-cbox-left">
                <?php include 'content-univis.php'; ?>
            </div>
        </div>

    <?php elseif (_rrze_theme_options('column.layout') == '3') : ?>
        <div class="ym-col3">
            <?php include 'content-univis.php'; ?>
        </div>

    <?php endif; ?>
</div>

<?php
get_footer();
