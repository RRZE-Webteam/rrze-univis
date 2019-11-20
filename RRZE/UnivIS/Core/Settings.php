<?php

namespace RRZE\UnivIS\Core;

defined('ABSPATH') || exit;

class Settings
{
    protected $option_name;
    
    protected $options;
    
    /*
     * "Screen ID" der Einstellungsseite
     * string
     */
    protected $admin_settings_page;
    protected $admin_utility_page;
    
    public function __construct()
    {
        $this->options = new Options();
        $this->option_name = $this->options->get_option_name();
        $this->options = $this->options->get_options();
    }
    
    /*
     * Füge eine Optionsseite in das Menü "Einstellungen" hinzu.
     * @return void
     */
    public function admin_settings_page() {
        $this->admin_settings_page = add_options_page(__('UnivIS', 'rrze-univis'), __('UnivIS', 'rrze-univis'), 'manage_options', 'rrze-univis', array($this, 'settings_page'));
        add_action('load-' . $this->admin_settings_page, array($this, 'admin_help_menu'));
    }
    

        /*
     * Füge eine Optionsseite in das Menü "Werkzeuge" hinzu.
     * @return void
     */
    public function admin_utility_page() {
        $this->admin_utility_page = add_submenu_page( 'tools.php', __('UnivIS', 'rrze-univis'), __('Suche nach UnivIS OrgID', 'rrze-univis'), 'manage_options', 'rrze-univis', array($this, 'utility_page') );
        add_action('load-' . $this->admin_utility_page, array($this, 'admin_help_menu'));
    }


    /*
     * Die Ausgabe der Optionsseite.
     * @return void
     */
    public function settings_page()
    {
        ?>
        <div class="wrap">
            <h2><?php echo __('UnivIS Settings', 'rrze-univis'); ?></h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('rrze_univis_options');
                do_settings_sections('rrze_univis_options');
                submit_button(); ?>
            </form>
        </div>
        <?php
    }


    /*
     * Die Ausgabe der Werkzeuge Seite.
     * @return void
     * 
     */
    public function utility_page() {
        ?>
        <div class="wrap">
            <h2><?php echo __('Suche nach UnivIS OrgID', 'rrze-univis'); ?></h2>
            <form method="post">
            <input type="hidden" name="action" value="search_orgid">
                <table class="form-table" role="presentation" class="striped">
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo __('Department', 'rrze-univis'); ?></th>
                            <td><input type="text" name="department_name" id="department_name" value=""></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Suchen', 'rrze-univis'); ?>"></td>
                        </tr>
                    </tbody>
                </table>            
            </form>
        </div>
        <?php

        echo '<div id="result">';

        if (isset($_POST["action"]) && $_POST["action"] == 'search_orgid' ){
            $department_name = rawurlencode($_POST["department_name"]);
            $api = 'http://univis.uni-erlangen.de/prg?search=departments&show=orglist&name=';
            if ( isset($department_name) && $department_name != '' ){
                $api .= $department_name;
                $json = file_get_contents( $api );
                $result = utf8_encode( $json );
                 if ( strpos( $result, 'keine passenden Daten' ) > 0 ){
                    echo 'Keine passenden Datensätze gefunden.';
                 } else {
                    preg_match('/(<table>.*<\/table>)/', $result, $matches );
                    $table = str_replace( '<table>', '<table class="wp-list-table widefat striped">', $matches[0] );
                    $table = preg_replace('/<tr>/', '<thead><tr>', $table, 1);
                    $table = preg_replace('/<\/tr>/', '</tr></thead>', $table, 1);
                    echo $table;
                 }
            }
        }

        echo '</div>';
}



    /*
     * Legt die Einstellungen der Optionsseite fest.
     * @return void
     */
    public function admin_settings()
    {
        register_setting('rrze_univis_options', $this->option_name, array($this, 'options_validate'));
        add_settings_section('rrze_univis_section', false, '__return_false', 'rrze_univis_options');
        add_settings_field('rrze_univis_linktext', __('Text for link to UnivIS', 'rrze-univis'), array($this, 'rrze_univis_linktext'), 'rrze_univis_options', 'rrze_univis_section');
        add_settings_field('rrze_univis_orgnr', __('UnivIS OrgNr.', 'rrze-univis'), array($this, 'rrze_univis_orgnr'), 'rrze_univis_options', 'rrze_univis_section');
    }

    /*
     * Validiert die Eingabe der Optionsseite.
     * @param array $input
     * @return array
     */
    public function options_validate($input)
    {
        $input['univis_default_link'] = !empty($input['univis_default_link']) ? $input['univis_default_link'] : '';
        $input['UnivISOrgNr'] = !empty($input['UnivISOrgNr']) ? $input['UnivISOrgNr'] : '';
        return $input;
    }

    /*
     * Feld: Linktext zu UnivIS
     * @return void
     */
    public function rrze_univis_linktext()
    {
        ?>
        <input type='text' name="<?php printf('%s[univis_default_link]', $this->option_name); ?>" value="<?php echo $this->options->univis_default_link; ?>">
        <?php
    }

    /*
     * Feld: UnivIS-OrgNr
     * @return void
     */
    public function rrze_univis_orgnr()
    {
        ?>
        <input type='text' name="<?php printf('%s[UnivISOrgNr]', $this->option_name); ?>" value="<?php echo $this->options->UnivISOrgNr; ?>">
        <?php
    }
    
    /*
     * Erstellt die Kontexthilfe der Optionsseite.
     * @return void
     */
    public function admin_help_menu()
    {
        $content = array(
            '<p>' . __('UnivIS data can be included via a shortcode into the page content.', 'rrze_univis') . '</p>'
        );


        $help_tab = array(
            'id' => $this->admin_settings_page,
            'title' => __('Overview', 'rrze-univis'),
            'content' => implode(PHP_EOL, $content),
        );

        $help_sidebar = sprintf('<p><strong>%1$s:</strong></p><p><a href="http://blogs.fau.de/webworking">RRZE-Webworking</a></p><p><a href="https://github.com/RRZE-Webteam">%2$s</a></p>', __('For more information', 'rrze-univis'), __('RRZE Webteam on Github', 'rrze-univis'));

        $screen = get_current_screen();

        if ($screen->id != $this->admin_settings_page) {
            return;
        }

        $screen->add_help_tab($help_tab);

        $screen->set_help_sidebar($help_sidebar);
    }
}
