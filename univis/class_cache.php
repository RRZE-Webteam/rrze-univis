<?PHP

class univisCache {
        //geändert!
	//public static $path = "cache_dir";
        public static $path;
	//public static $path = plugins_url( "cache_dir", __FILE__); 
        
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
	function __construct($optionen) {
            //self::$path = plugins_url( "cache_dir", __FILE__);
           // self::$path = dirname( plugin_basename( __FILE__ ) )."/cache_dir";
					self::$path = plugin_dir_path( __FILE__ )."cache_dir";
				$this->optionen = $optionen;
		
	}

	public function holeDaten($force = false) {
	
		$sucheDateiErgebnis = $this->sucheDatei();
		
		if($sucheDateiErgebnis > 0) {
			// Datei ist vorhanden und gueltig
			echo "<!-- Univis Datensatz vom ".date("d.m.y H:i:s",filemtime($this->filepath()))." -->\n";
			return file_get_contents($this->filepath());
		}

		if($sucheDateiErgebnis == 0 && $force) {
			// Datei nicht gueltig, aber:
			// Ausgabe durch Parameter forciert.
			echo "<!-- Univis Datensatz vom ".date("d.m.y H:i:s",filemtime($this->filepath()))." (force=true) -->\n";
			return file_get_contents($this->filepath());
		}
			echo "<!-- Univis Datensatz aktuell -->";
		return -1;
	}

	public function setzeDaten($data) {
				$filepath = $this->filepath();
        if(is_dir(self::$path)) {
							file_put_contents($filepath, $data);
       }
	}

	private function sucheDatei() {
		
		// Generiere Pfad.
		$filepath = $this->filepath();

		if(!file_exists($filepath)) {
			//Datei existiert nicht.
			return -1;
		}

		$expire = $this->optionen["SeitenCache"];
		// Ueberpruefe Date auf Gueltigkeit
		if(time() < (filemtime($filepath) + $expire)) {
			// Datei gueltig
			return 1;
		}else{
			// Datei gefunden aber nicht gueltig
			return 0;
		}
	}

	private function filepath() {
		// Key md5 codieren
		$key = md5($this->key());
		return self::$path."/".$key;
	}

	private function key() {
		$optionen = $this->optionen;
		if(!$optionen) {
			return -1;
		}

		if(!array_key_exists("task", $optionen)) {
			// Fehler in Konifguration
			return -1; 
		}
		
		switch ($optionen["task"]) {
			case 'mitarbeiter-alle':				return $optionen["task"]."/".$optionen["UnivISOrgNr"];
			case 'mitarbeiter-einzeln':				return $optionen["task"]."/".$optionen["lastname"]."-".$optionen["firstname"];
			case 'lehrveranstaltungen-alle':		return $optionen["task"]."/".$optionen["UnivISOrgNr"];
			case 'lehrveranstaltungen-einzeln':		return $optionen["task"]."/".$optionen["id"];
			case 'publikationen':					return $optionen["task"]."/".$optionen["UnivISOrgNr"];
				
			default:								return -1;
		}

	}

}

?>
