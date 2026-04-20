<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

class Metabox {
    protected $config;

    public function __construct() {
        $this->config = new Config();
    }

    public function onLoaded() {
        add_action('add_meta_boxes', [$this, 'addMetaboxes']);
    }

    public function addMetaboxes() {
        $constants = $this->config->getConstants();
        foreach ($constants['metabox']['posttypes'] as $posttype) {
            add_meta_box(
                $constants['metabox']['id'],
                __('Suche nach UnivIS IDs'),
                [$this, 'fillMetabox'],
                $posttype,
                $constants['metabox']['context'],
                $constants['metabox']['priority']
            );
        }
    }

    public function fillMetabox() {
        $constants = $this->config->getConstants();
        ?>
            <div class="tagsdiv" id="univis">
                <div class="jaxtag">
                    <form method="post">
                    <div class="ajaxtag hide-if-no-js">
                        <select name="dataType" id="dataType" class="univisSelect" required="required">
                            <?php foreach ($constants['search_types'] as $value => $label) { ?>
                                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="ajaxtag hide-if-no-js">
                        <input type="text" name="keyword" id="keyword" value="">
                        <input type="button" class="button tagadd" id="searchUnivisID" value="Search">
                    </div>
                    <div class="ajaxtag hide-if-no-js">
                        <div id="univis-search-result"></div>
                        <div id="loading" class="loading"><i class="fa fa-refresh fa-spin fa-2x"></i></div>
                    </div>
                    </form>
                </div>
            </div>
        <?php
    }
}
