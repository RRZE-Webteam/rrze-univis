<?php

namespace RRZE\UnivIS;

use RRZE\UnivIS\Core\UnivIS;
use RRZE\UnivIS\Core\Render;

defined('ABSPATH') || exit;

class Controller
{
    /**
     * @var array
     * @access public
     */    
    public $language = [
        'suffix' => '',
        'orgunit' => 'orgunit',
        'orgunits' => 'orgunits',
        'orgname' => 'orgname',
        'description' => 'description',
        'text' => 'text',
        'title' => 'title'
    ];
     
    /**
     * @var array
     * @access protected
     */      
    protected $messages = [];

    /**
     * Optionen
     *
     * @var array
     * @access protected
     */
    protected $optionen = null;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
    }

    /**
     * init
     *
     * @param string $task
     * @param array $atts
     * @access public
     */
    public function init($task, $atts = null)
    {
        $this->ladeConf($atts);

        if ($task && $this->optionen) {
            $this->optionen['task'] = $task;
        }
    }

    /**
     * ladeConf
     *
     * @param array $atts
     * @return void
     * @access protected
     */
    protected function ladeConf($atts = null)
    {
        if (is_array($atts)) {
            $this->optionen = $atts;
            return;
        }
    }
    
    /**
     * ladeHTML
     *
     * @param array $args
     * @return mixed
     * @access public
     */    
    public function ladeHTML($args = null)
    {
        // Lade Daten von Univis
        $univis = new UnivIS($this->optionen);
        $daten = $univis->ladeDaten();

        // Pruefe ob Daten erfolgreich geladen wurden.
        if ($daten != -1) {
            // Passe Datenstruktur fuer Templating an.
            $render = new Render($this->optionen);
            $daten = $render->bearbeiteDaten($daten);

            // Lade Zusatzinformationen
            //			$assets = new univisAssets($this->optionen);
            //			$daten["assets"] = $assets->holeDaten();
            // Daten rendern
            $html = $this->renderTemplate($daten);

            if ($html != -1) { //Rendern erfolgreich?
                // Gerenderte Daten in Cache speichern
                //$cache->setzeDaten($html);
                return $html;
            } else {
                // Fehleranzeige vorerst rausgenommen
                if (isset($this->optionen['errormsg'])) {
                    return __('Template Error: Could not find template file.', 'rrze-univis');
                    //return "Template Fehler: Konnte Template Datei nicht finden.";
                    //return;
                }
            }
        } else {
            return;
        }
    }

    /**
     * renderTemplate
     *
     * @param array $daten
     * @return mixed
     * @access protected
     */    
    protected function renderTemplate($daten)
    {
        $daten = self::sanitize_key($daten);

        // Sprachunterstützung
        if (isset($daten['optionen']['lang'])) {
            extract($daten['optionen']['lang']);
        } else {
            extract($this->language);
        }

        $filename = trailingslashit(dirname(__FILE__)) . 'Templates/' . $this->optionen['task'] . '.php';
        do_action('rrze.log.debug', ['plugin' => 'rrze-univis', 'filename' => $filename]);

        if (is_file($filename)) {
            ob_start();
            include $filename;
            return str_replace("\n", " ", ob_get_clean());
        }

        return -1;
    }

    /**
     * sanitize_key
     *
     * @param array $array
     * @return array
     * @access public
     */    
    public static function sanitize_key($array)
    {
        $data = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = self::sanitize_key($value);
            }

            $key = preg_replace('/[^a-z0-9_]/', '_', strtolower($key));
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * correct_phone_number
     *
     * @param string $phone_number
     * @return string
     * @access public
     */    
    public static function correct_phone_number($phone_number)
    {
        if ((strpos($phone_number, '+49 9131 85-') !== 0) && (strpos($phone_number, '+49 911 5302-') !== 0)) {
            if (!preg_match('/\+49 [1-9][0-9]{1,4} [1-9][0-9]+/', $phone_number)) {
                $phone_data = preg_replace('/\D/', '', $phone_number);
                $vorwahl_erl = '+49 9131 85-';
                $vorwahl_nbg = '+49 911 5302-';
                
                switch (strlen($phone_data)) {
                    case '3':
                        $phone_number = $vorwahl_nbg . $phone_data;
                        break;
                    
                    case '5':
                        if (strpos($phone_data, '06') === 0) {
                            $phone_number = $vorwahl_nbg . substr($phone_data, -3);
                            break;
                        }
                        $phone_number = $vorwahl_erl . $phone_data;
                        break;

                    case '7':
                        if (strpos($phone_data, '85') === 0 || strpos($phone_data, '06') === 0) {
                            $phone_number = $vorwahl_erl . substr($phone_data, -5);
                            break;
                        }
                        
                        if (strpos($phone_data, '5302') === 0) {
                            $phone_number = $vorwahl_nbg . substr($phone_data, -3);
                            break;
                        }
                        
                        // no break
                    default:
                        if (strpos($phone_data, '9115302') !== false) {
                            $durchwahl = explode('9115302', $phone_data);
                            if (strlen($durchwahl[1]) === 3 || strlen($durchwahl[1]) === 5) {
                                $phone_number = $vorwahl_nbg . $durchwahl[1];
                            }
                            break;
                        }
                        
                        if (strpos($phone_data, '913185') !== false) {
                            $durchwahl = explode('913185', $phone_data);
                            if (strlen($durchwahl[1]) === 5) {
                                $phone_number = $vorwahl_erl . $durchwahl[1];
                            }
                            break;
                        }
                        
                        if (strpos($phone_data, '09131') === 0 || strpos($phone_data, '499131') === 0) {
                            $durchwahl = explode('9131', $phone_data);
                            $phone_number = "+49 9131 " . $durchwahl[1];
                            break;
                        }
                        
                        if (strpos($phone_data, '0911') === 0 || strpos($phone_data, '49911') === 0) {
                            $durchwahl = explode('911', $phone_data);
                            $phone_number = "+49 911 " . $durchwahl[1];
                            break;
                        }
                }
            }
        }
        
        return $phone_number;
    }
}
