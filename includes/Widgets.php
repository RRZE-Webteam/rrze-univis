<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

require_once ABSPATH . 'wp-includes/class-wp-widget.php';

class Widgets extends \WP_Widget
{

    protected $pluginFile;
    protected $settings;
    protected $config;

    public function __construct($pluginFile, $settings)
    {
        $this->pluginFile = $pluginFile;
        $this->settings = $settings;
        $this->config = new Config();

        parent::__construct(
            $this->config->get('constants.widget.id_base'),
            __('UnivIS Widget', 'rrze-univis'),
            array('description' => $this->config->get('constants.widget.description'))
        );
    }

    // Creating widget front-end
    public function widget($args, $instance)
    {
        $atts = '';
        $task = (!empty($instance['task']) ? $instance['task'] : '');
        $atts .= (!empty($instance['show']) ? ' show=' . $instance['show'] : '');
        $atts .= (!empty($instance['hide']) ? ' hide=' . $instance['hide'] : '');
        $field = $this->config->get('constants.widget.task_field_map.' . $task, $this->config->get('constants.widget.task_field_map.default'));

        $shortcode = new Shortcode($this->pluginFile, $this->settings);
        $shortcode->onLoaded();

        echo $args['before_widget'];
        echo do_shortcode('[univis task="' . $task . '" ' . $field . '=' . $instance['univisid'] . $atts . ']');
        echo $args['after_widget'];
    }

    public function getSelectHTML($name, $selectedID = 0)
    {
        $aOptions = $this->config->get('constants.widget.tasks', []);
        $output = "<select id='{$this->get_field_id($name)}' name='{$this->get_field_name($name)}' class='widefat'>";
        foreach ($aOptions as $ID => $txt) {
            $sSelected = selected($selectedID, $ID, false);
            $output .= "<option value='$ID' $sSelected>$txt</option>";
        }
        $output .= "</select></p>";
        return $output;
    }

    public function getInputHTML($name, $label, $val = '')
    {
        return "<input type='text' id='{$this->get_field_id($name)}' name='{$this->get_field_name($name)}' placeholder=' . $label . ' class='widefat' value='" . (!empty($val) ? $val : '') . "'>";
    }

    // Widget Backend
    public function form($instance)
    {
        echo '<br \>';
        echo $this->getSelectHTML('task', !empty($instance['task']) ? $instance['task'] : null);
        echo $this->getInputHTML('univisid', 'UnivIS ID', !empty($instance['univisid']) ? $instance['univisid'] : null);
        echo '<br \>&nbsp;';
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['task'] = (!empty($new_instance['task']) ? $new_instance['task'] : '');
        $instance['univisid'] = (!empty($new_instance['univisid']) ? $new_instance['univisid'] : '');
        $instance['show'] = (!empty($new_instance['show']) ? $new_instance['show'] : '');
        $instance['hide'] = (!empty($new_instance['hide']) ? $new_instance['hide'] : '');
        return $instance;
    }
}
