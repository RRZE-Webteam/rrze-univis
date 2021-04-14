<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

use RRZE\UnivIS\Settings;
use RRZE\UnivIS\Shortcode;


/**
 * Hauptklasse (Main)
 */
class Main {
    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $pluginFile;

    /**
     * Variablen Werte zuweisen.
     * @param string $pluginFile Pfad- und Dateiname der Plugin-Datei
     */
    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
    }

    /**
     * Es wird ausgeführt, sobald die Klasse instanziiert wird.
     */
    public function onLoaded() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('add_meta_boxes', [$this, 'addMetaboxes']);


        // Settings-Klasse wird instanziiert.
        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();


        // Shortcode-Klasse wird instanziiert.
        $shortcode = new Shortcode($this->pluginFile, $settings);
        $shortcode->onLoaded();

        // Widget
        add_action( 'widgets_init', [$this, 'loadWidget'] );    
    }

    /**
     * Enqueue der globale Skripte.
     */
    public function enqueueScripts() {
        wp_register_style('rrze-univis', plugins_url('assets/css/plugin.css', plugin_basename($this->pluginFile)));
    }

    public function loadWidget() {
        $myWidget = new UnivISWidget();
        register_widget($myWidget);
    }

    public function addMetaboxes(){
        add_meta_box('get_univis_ids', __('Suche nach UnivIS IDs'), [$this, 'fillMetabox'], NULL, 'side', 'core');
    }

    public function fillMetabox() {
        ?>
        <div class="inside">
            <div class="tagsdiv" id="univis">
            	<div class="jaxtag">
            		<div class="ajaxtag hide-if-no-js">
                        <form method="post">
                            <input type="hidden" name="action" value="univis_search">
                            <input type="text" name="unvis_keyword" id="department_name" value="">
                            <input type="button" class="button tagadd" value="Suchen">
                        </form>
            	    </div>
                </div>
        <?php
        if (isset($_POST["action"]) && $_POST["action"] == 'univis_search' ){
            $name = filter_input(INPUT_POST, 'unvis_keyword', FILTER_SANITIZE_STRING);
            if ($name){
                $options = get_option( 'rrze-univis' );
                $data = 0;
                $UnivISURL = (!empty($options['basic_univis_url']) ? $options['basic_univis_url'] : '');
                $univisOrgID = (!empty($options['basic_UnivISOrgNr']) ? $options['basic_UnivISOrgNr'] : 0);

                if ($UnivISURL && $univisOrgID){
                    $univis = new UnivISAPI($UnivISURL, $univisOrgID, NULL);
                    $data = $univis->getData('departmentByName', $name);
                }
            
                echo '<div id="result">';
                if (!$UnivISURL){
                    echo __('Link zu UnivIS fehlt.', 'rrze-univis');
                }elseif (!$data){
                    echo __('Keine passenden Datensätze gefunden.', 'rrze-univis');
                }else{
                    echo '<table class="wp-list-table widefat striped"><thead><tr><td><b><i>Univ</i>IS</b> OrgNr.</td><td>Name</td></tr></thead>';
                    foreach($data as $entry){
                        if (isset($entry['orgnr'])){
                            echo '<tr><td>' . $entry['orgnr'] . '</td><td>' . $entry['name'] . '</td></tr>';
                        }
                    }
                    echo '</table>';
                }
                echo '</div>';
            }
        }
    ?>

            </div>
        </div>

        <!-- <div class="tagsdiv">
            <div class="jaxtag">
            <form method="post">
            <input type="hidden" name="action" value="univis_search">
                <table class="form-table" role="presentation" class="striped">
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo __('Organisationseinheit', 'rrze-univis'); ?></th>
                            <td><input type="text" name="department_name" id="department_name" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Suchen', 'rrze-univis'); ?>"></td>
                        </tr>
                    </tbody>
                </table>            
            </form>
            </div>
        </div> -->
        <?php


    }
}
