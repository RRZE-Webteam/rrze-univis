<?php

require_once("class_univis.php");
require_once("class_render.php");
require_once("class_cache.php");
require_once("class_assets.php");
require 'Mustache/Autoloader.php';

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
		$cache = new univisCache($this->optionen);
		$datenAusCache = $cache->holeDaten();

		if($datenAusCache != -1) {
			// Daten wurden aus Cache geladen
			return $datenAusCache;
		}

		// Lade Daten von Univis
		$univis = new UNIVIS($this->optionen);
		$daten = $univis->ladeDaten();

		// Pruefe ob Daten erfolgreich geladen wurden.
		if($daten != -1) {
			// Passe Datenstruktur fuer Templating an.
			$render = new univisRender($this->optionen);
			$daten = $render->bearbeiteDaten($daten);

			// Lade Zusatzinformationen
			$assets = new univisAssets($this->optionen);
			$daten["assets"] = $assets->holeDaten();

			// Daten rendern
			$html = $this->_renderTemplate($daten);

			if($html != -1) {	//Rendern erfolgreich?

				// Gerenderte Daten in Cache speichern
				$cache->setzeDaten($html);
				return $html;
			}else{
				return "Template Fehler: Konnte Template Datei nicht finden.";
			}

		}else{
			// Lade Daten aus Cache (auch veraltete).
			$datenAusCache = $cache->holeDaten(true);

			if($datenAusCache != -1) {
				return $datenAusCache;
			}else{
				// Konnte keine Daten laden. Alternativausgabe laden
				if($this->optionen["task"] == "mitarbeiter-einzeln") {
					// Lade Mitarbeiter Alle
					echo "<div class=\"hinweis_wichtig\"><h4>Fehler: Konnte Person " . $this->optionen["firstname"] . " " . $this->optionen["lastname"] . " nicht finden.</h4><p>Bitte wählen Sie eine Person aus der Liste.</p></div><br class=\"clear\" />";
					$this->optionen["task"] = "mitarbeiter-alle";
					return $this->ladeHTML();
				}
				if ($this->optionen["task"] == "lehrveranstaltungen-einzeln") {
					// Lade Lehrveranstaltungen Alle
					echo "<div class=\"hinweis_wichtig\"><h4>Fehler: Konnte Lehrveranstaltung id=" . $this->optionen["id"] . " nicht finden.</h4>";
                                        
                                        if( !empty($this->optionen["UnivISOrgNr"])) {
                                            echo "<p>Bitte wählen Sie eine Lehrveranstaltung aus der Liste.</p></div><br class=\"clear\" />";                                        
                                            $this->optionen["task"] = "lehrveranstaltungen-alle";
                                            return $this->ladeHTML();
                                        }
				}
			}
		}
	}

	private function _renderTemplate($daten) {
		Mustache_Autoloader::register();

		$m = new Mustache_Engine;
		$template = $this->_get_template();

		if($template == -1) return -1;

		return  $m->render($template, $daten);
	}


	private function _ladeConf($fpath, $args=NULL){
		$options= array();
                if(is_array($fpath)) {
                    $this->optionen = $fpath;
                    return;
                }

		// defaults
		//lapmk 02.03.2017: shortcodes immer Kleinbuchstaben
		$defaults = array(
			'UnivISOrgNr' => '0',
			'task' => 'mitarbeiter-alle',
			'personenanzeige_bildsuche' =>	'1',
			'personenanzeige_zusatzdatenindatei' =>	'1',
			'personenanzeige_publikationen'	=> '0',
			'personenanzeige_lehrveranstaltung' => '1',
			'START_SOMMERSEMESTER' => '1.4',
			'START_WINTERSEMESTER' => '1.10',
			'zeige_sprungmarken' => '1',
			'orgunit' => '',
			'sortiere_alphabet' => '0',
			'sortiere_jobs' => '1'
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

	function _get_template() {
		//lapmk 03.03.2017: neue Version 1 für mitarbeiter-einzeln-Template eingefügt; hier erfolgt Dateiauswahl
		$task=$this->optionen['task'];
		if ($task=='mitarbeiter-einzeln' && $this->optionen['mitarbeiter-einzeln-version']==1) $task.='1';
		$filename = $task.".shtml";
                //geändert!
                //$filename = "templates/".$filename;
                $filename = plugins_url( "templates/".$filename, __FILE__);
		$handle = fopen($filename, "r");
                //geändert!
                //$contents = fread($handle, filesize($filename));
                $contents = stream_get_contents($handle);
		fclose($handle);
		return $contents;
	}
}

