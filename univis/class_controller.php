<?php

require_once("class_univis.php");
require_once("class_render.php");
//require_once("class_cache.php");
//require_once("class_assets.php");

class univisController {

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
	function __construct($task, $args, $confFile=NULL) {

		$this->_ladeConf($confFile, $args);

		if($task && $this->optionen)
			$this->optionen["task"] = $task;
	}

	function ladeHTML() {
//		$cache = new univisCache($this->optionen);
//		$datenAusCache = $cache->holeDaten();
//
//		if($datenAusCache != -1) {
//			// Daten wurden aus Cache geladen
//			return $datenAusCache;
//		}

		// Lade Daten von Univis
		$univis = new UNIVIS($this->optionen);
		$daten = $univis->ladeDaten();

		// Pruefe ob Daten erfolgreich geladen wurden.
		if($daten != -1) {
			// Passe Datenstruktur fuer Templating an.
			$render = new univisRender($this->optionen);
			$daten = $render->bearbeiteDaten($daten);

			// Lade Zusatzinformationen
//			$assets = new univisAssets($this->optionen);
//			$daten["assets"] = $assets->holeDaten();

			// Daten rendern
			$html = $this->_renderTemplate($daten);

			if($html != -1) {	//Rendern erfolgreich?

				// Gerenderte Daten in Cache speichern
				//$cache->setzeDaten($html);
				return $html;
			}else{
                            // Fehleranzeige vorerst rausgenommen
                            if(isset($this->optionen['errormsg'])) {
				return "Template Fehler: Konnte Template Datei nicht finden.";  
                                //return;
                            }
			}

		}else{
			// Lade Daten aus Cache (auch veraltete).
//			$datenAusCache = $cache->holeDaten(true);
//
//			if($datenAusCache != -1) {
//				return $datenAusCache;
//			}else{
//			        // Fehleranzeige vorerst rausgenommen
				// Konnte keine Daten laden. Alternativausgabe laden
//				if($this->optionen["task"] == "mitarbeiter-einzeln") {
//					// Lade Mitarbeiter Alle
//					echo "<div class=\"hinweis_wichtig\"><h4>Fehler: Konnte Person " . $this->optionen["firstname"] . " " . $this->optionen["lastname"] . " nicht finden.</h4><p>Bitte wählen Sie eine Person aus der Liste.</p></div><br class=\"clear\" />";
//					$this->optionen["task"] = "mitarbeiter-alle";
//					return $this->ladeHTML();
//				}
//				if ($this->optionen["task"] == "lehrveranstaltungen-einzeln") {
//					// Lade Lehrveranstaltungen Alle
//					echo "<div class=\"hinweis_wichtig\"><h4>Fehler: Konnte Lehrveranstaltung id=" . $this->optionen["id"] . " nicht finden.</h4>";
//                                        
//                                        if( !empty($this->optionen["UnivISOrgNr"])) {
//                                            echo "<p>Bitte wählen Sie eine Lehrveranstaltung aus der Liste.</p></div><br class=\"clear\" />";                                        
//                                            $this->optionen["task"] = "lehrveranstaltungen-alle";
//                                            return $this->ladeHTML();
//                                        }
//				}
//			}
                    return;
		}
	}

	private function _renderTemplate($daten) {
            
                $daten = self::_sanitize_key($daten);
                
                // Sprachunterstützung
                if(isset($daten['optionen']['lang'])) {
                    extract($daten['optionen']['lang']);
                } else {
                    extract(RRZE_UnivIS::$language);
                }                   

                $filename = plugin_dir_path(__FILE__) . "templates/" . $this->optionen['task'].".php";
                
                if (is_file($filename)) {
                    ob_start();
                    include $filename;
                    return str_replace("\n", " ", ob_get_clean());
                }
                
                return -1;           
	}
        
        private static function get_key($array, $key, $option) {
            if( !is_array( $array)) {
                return false;
            }
            foreach ($array as $k => $v) {
                if($k == $key && is_array($v) && isset($v[$option])){
                    return $v;
                }
                $data = self::get_key($v, $key, $option);
                if($data != false){
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

	private function _ladeConf($fpath, $args=NULL){
		$options= array();
                if(is_array($fpath)) {
                    $this->optionen = $fpath;
                    return;
                }

		// defaults
		$defaults = array(
			'UnivISOrgNr' => '0',
			'task' => 'mitarbeiter-alle',
			'Personenanzeige_Bildsuche' =>	'1',
			'Personenanzeige_ZusatzdatenInDatei' =>	'1',
			'Personenanzeige_Publikationen'	=> '0',
			'Personenanzeige_Lehrveranstaltung' => '1',
			'START_SOMMERSEMESTER' => '1.4',
			'START_WINTERSEMESTER' => '1.10',
			'Zeige_Sprungmarken' => '1',
			'OrgUnit' => '',
			'Sortiere_Alphabet' => '0',
			'Sortiere_Jobs' => '1'
		);

		// load options
		if ($fpath == NULL) {
			$fpath = '../../univis.conf';
		}
		$fpath_alternative = $_SERVER["DOCUMENT_ROOT"].'/vkdaten/univis.conf';                
		
                if(file_exists($fpath_alternative)){ $fpath = $fpath_alternative; }
		$options = array();
		$fh = fopen($fpath, 'r') or die('Cannot open file!');
		while(!feof($fh)) {
			$line = fgets($fh);
			$line = trim($line);
			if((strlen($line) == 0) || (substr($line, 0, 1) == '#')) {
				continue; // ignore comments and empty rows
			}
			$arr_opts = preg_split('/\t/', $line); // tab separated
			$options[$arr_opts[0]] = $arr_opts[1];
		}
		fclose($fh);

		// merge defaults with options
		$this->optionen = array_merge($defaults, $options);
		if($args)
			$this->optionen = array_merge($this->optionen, $args);

	}

}

