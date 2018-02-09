<?php

namespace RRZE\UnivIS;

use RRZE\UnivIS\Core\UnivIS;
use RRZE\UnivIS\Core\Render;

defined('ABSPATH') || exit;

class Controller {

    public $defaults = [
        'UnivISOrgNr' => '0',
        'task' => 'mitarbeiter-alle',
        'Personenanzeige_Bildsuche' => '1',
        'Personenanzeige_ZusatzdatenInDatei' => '1',
        'Personenanzeige_Publikationen' => '0',
        'Personenanzeige_Lehrveranstaltung' => '1',
        'START_SOMMERSEMESTER' => '1.4',
        'START_WINTERSEMESTER' => '1.10',
        'Zeige_Sprungmarken' => '1',
        'OrgUnit' => '',
        'Sortiere_Alphabet' => '0',
        'Sortiere_Jobs' => '1'
    ];
    
    public $language = [
        'suffix' => '', 
        'orgunit' => 'orgunit', 
        'orgunits' => 'orgunits', 
        'orgname' => 'orgname', 
        'description' => 'description', 
        'text' => 'text',
        'title' => 'title'
    ];
     
    protected $messages = [ ];

    /**
     * Optionen
     *
     * @var array
     * @access private
     */
    private $optionen = NULL;

    /**
     * Constructor.
     *
     *
     * @param Uebergebene argumente
     * @param Pfad zu Conf Datei
     * @access 	public
     */
    public function __construct($task, $type, $atts = NULL) {
        $this->_ladeConf($type, $atts);

        if ($task && $this->optionen) {
            $this->optionen['task'] = $task;
        }
    }

    private function _ladeConf($type, $atts = NULL) {
        $options = array();
        if (is_array($atts)) {
            $this->optionen = $atts;
            return;
        }

        // Merge defaults with options
        $this->optionen = array_merge($this->defaults, $options);
        if ($type) {
            $this->optionen = array_merge($this->optionen, $args);
        }
    }
    
    public function ladeHTML($args = NULL) {        
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
            $html = $this->_renderTemplate($daten);

            if ($html != -1) { //Rendern erfolgreich?
                // Gerenderte Daten in Cache speichern
                //$cache->setzeDaten($html);
                return $html;
            } else {
                // Fehleranzeige vorerst rausgenommen
                if (isset($this->optionen['errormsg'])) {
                    return "Template Fehler: Konnte Template Datei nicht finden.";
                    //return;
                }
            }
        } else {
            return;
        }
    }

    private function _renderTemplate($daten) {

        $daten = self::_sanitize_key($daten);

        // Sprachunterstützung
        if (isset($daten['optionen']['lang'])) {
            extract($daten['optionen']['lang']);
        } else {
            extract($this->language);
        }

        $filename = plugin_dir_path(__FILE__) . "Templates/" . $this->optionen['task'] . ".php";

        if (is_file($filename)) {
            ob_start();
            include $filename;
            return str_replace("\n", " ", ob_get_clean());
        }

        return -1;
    }

    private static function get_key($array, $key, $option) {
        if (!is_array($array)) {
            return false;
        }
        foreach ($array as $k => $v) {
            if ($k == $key && is_array($v) && isset($v[$option])) {
                return $v;
            }
            $data = self::get_key($v, $key, $option);
            if ($data != false) {
                return $data;
            }
        }

        return false;
    }

    private static function _sanitize_key($array) {
        $data = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = self::_sanitize_key($value);
            }

            $key = preg_replace('/[^a-z0-9_]/', '_', strtolower($key));
            $data[$key] = $value;
        }
        return $data;
    }

    private static function correct_phone_number($phone_number) {
        if (( strpos($phone_number, '+49 9131 85-') !== 0 ) && ( strpos($phone_number, '+49 911 5302-') !== 0 )) {
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
                        
                    default:
                        if (strpos($phone_data, '9115302') !== FALSE) {
                            $durchwahl = explode('9115302', $phone_data);
                            if (strlen($durchwahl[1]) === 3 || strlen($durchwahl[1]) === 5) {
                                $phone_number = $vorwahl_nbg . $durchwahl[1];
                            }
                            break;
                        }
                        
                        if (strpos($phone_data, '913185') !== FALSE) {
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
