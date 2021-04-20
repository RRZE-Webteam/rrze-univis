<?php

namespace RRZE\UnivIS;

defined( 'ABSPATH' ) || exit;

require_once ABSPATH.'wp-includes/class-wp-widget.php';


class UnivISWidget extends \WP_Widget {
  
    public function __construct() {
        parent::__construct(
            'univis_widget', 
            __('UnivIS Widget', 'rrze-univis'), 
            array( 'description' => __( 'Displays a lecture, person or publication', 'rrze-univis' ), ) 
        );
    }
      

    // Creating widget front-end
    public function widget( $args, $instance ) {
        // echo 'these are the args:';
        // var_dump($args);
        // exit;
        $atts = '';
        $atts .= (!empty($instance['show'])?' show=' . $instance['show'] : '');
        $atts .= (!empty($instance['hide'])?' hide=' . $instance['hide'] : '');
        echo $args['before_widget'];
        echo do_shortcode('[univis task="'. $instance['task'] . '" ' . 'univisid=' . $instance['univisid'] . $atts . ']');
        echo $args['after_widget'];
    }

    public function getSelectHTML($name, $selectedID = 0){
        $aOptions = [
            'lehrveranstaltungen-einzeln' => __('Lehrveranstaltung', 'rrze-univis'),
            'mitarbeiter-einzeln' => __('Person', 'rrze-univis'),
            'publikationen' => __('Publikation', 'rrze-univis')
        ];
        $output = "<select id='{$this->get_field_id($name)}' name='{$this->get_field_name($name)}' class='widefat'>";
        foreach($aOptions as $ID => $txt){
            $sSelected = selected($selectedID, $ID, FALSE );
            $output .= "<option value='$ID' $sSelected>$txt</option>";
        }
        $output .= "</select></p>";
        return $output;
    }

    public function getInputHTML($name, $label, $val = ''){
        return "<input type='text' id='{$this->get_field_id($name)}' name='{$this->get_field_name($name)}' placeholder=' . $label . ' class='widefat' value='" . (!empty($val) ? $val : '') . "'>";
    }

              
    // Widget Backend 
    public function form( $instance ) {
        echo '<br \>';
        echo $this->getSelectHTML('task', !empty($instance['task']) ? $instance['task'] : NULL );
        echo $this->getInputHTML('univisid', !empty($instance['univisid']) ? $instance['univisid'] : NULL, 'UnivIS ID');
        echo '<br \>&nbsp;';
    }
          
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['task'] = (!empty($new_instance['task']) ? $new_instance['task'] : '');
        $instance['univisid'] = (!empty($new_instance['univisid']) ? $new_instance['univisid'] : '');
        $instance['show'] = (!empty($new_instance['show']) ? $new_instance['show'] : '');
        $instance['hide'] = (!empty($new_instance['hide']) ? $new_instance['hide'] : '');
        return $instance;
    }
} 


     
