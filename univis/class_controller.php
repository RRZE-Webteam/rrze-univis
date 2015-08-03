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
//	function __construct($task, $args, $confFile=NULL) {
	function __construct($config) {
//print_r($config);
			$this->optionen = $config;
/*		
	$this->_ladeConf($confFile, $args);
		if($task && $this->optionen)
		$this->optionen["task"] = $task;
*/
	}

	function ladeHTML() {
		$cache = new univisCache($this->optionen);
		$datenAusCache = $cache->holeDaten();

		if($datenAusCache != -1) {
			// Daten wurden aus Cache geladen
      $search = '/^\<\!\-\- UnivIS-ID\:([0-9]+).*/';
      preg_match($search, $datenAusCache, $match);
      echo "<!-- UnivIS-ID:".$univisid."-->\n";
	    $GLOBALS['LocalUnivisID']=$match[1];
	    return $datenAusCache;
		}

		// Lade Daten von Univis
		$univis = new UNIVIS($this->optionen);

		$daten = $univis->ladeDaten();

		// Pruefe ob Daten erfolgreich geladen wurden.
		if($daten != -1) {
			// Passe Datenstruktur fuer Templating an.
			$render = new univisRender($this->optionen);

			$daten= $render->bearbeiteDaten($daten);

			$daten['Optionen']=$this->optionen;
			// Lade Zusatzinformationen
			//$assets = new univisAssets($this->optionen);
			//$daten["assets"] = $assets->holeDaten();

			// Daten rendern
	
			/*print("<pre>");
				print_r($daten);
				print("</pre>");*/

			$html = $this->_renderTemplate($daten);
      if($daten['person']['lehr']==="ja"){
	    $html= "<!-- UnivIS-ID:".$daten['person']["id"]."-->\n".$html;
	    $GLOBALS['LocalUnivisID']=$daten['person']["id"];
      }      

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
			      $search = '/^\<\!\-\- UnivIS-ID\:([0-9]+).*/';
            preg_match($search, $datenAusCache, $match);
	          $GLOBALS['LocalUnivisID']=$match[1];
	          return $datenAusCache;
			}else{
				// Konnte keine Daten laden. Alternativausgabe laden
					echo "<div class=\"hinweis_wichtig\"><h4>Fehler im Univis-Plugin!.</h4><p>Es konnten keine Daten geladen werden</p></div><br class=\"clear\" />";

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

/*
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
*/

	function _get_template() {
		$filename = $this->optionen['task'].".txt";
		$filename = dirname(__FILE__)."/../templates/".$filename;
		$contents = file_get_contents($filename);
		return $contents;
	}
}

