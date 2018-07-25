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
    public function admin_settings_page()
    {
        $this->admin_settings_page = add_options_page(__('UnivIS', 'rrze-univis'), __('UnivIS', 'rrze-univis'), 'manage_options', 'rrze-univis', array($this, 'settings_page'));
        add_action('load-' . $this->admin_settings_page, array($this, 'admin_help_menu'));
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
