rrze-univis
============

WordPress Plugin
----------------

Einbindung von UnivIS-Daten auf WordPress-Seiten mittels Shortcode:

- [univis]: Anzeige eines Links zur UnivIS-Startseite, der Linktext kann unter Einstellungen - UnivIS modifiziert werden    
- [univis number="1005681200"] oder [univis number="1005681200" task="mitarbeiter-alle"]: Bindet die Mitarbeiterübersicht der Organisationseinheit mit der UnivISOrgNr 1005681200 ein, besonders geeignet für wissenschaftliche Einrichtungen    
- [univis number="1005681200" task="mitarbeiter-orga"]: Bindet die Mitarbeiterübersicht der Organisationseinheit mit der UnivISOrgNr 1005681200 ein, besonders geeignet für nicht-wissenschaftliche Einrichtungen    
- [univis number="1005681200" task="lehrveranstaltungen-alle"]: Bindet sämtliche Lehrveranstaltungen der Organisationseinheit mit der UnivISOrgNr 1005681200 ein    
- [univis number="1005681200" task="publikationen"]: Bindet sämtliche UnivIS-Publikationen der Organisationseinheit mit der UnivISOrgNr 1005681200 ein    

Der Shortcode-Parameter number kann weggelassen werden, wenn in der Einstellungsseite des Plugins (Einstellungen - UnivIS) eine UnivISOrgNr vergeben wird. Dann muss aber zwingend der Parameter task vergeben werden (default-Wert für task ist mitarbeiter-alle).


Umsetzung der automatischen Formatierungen in mehrzeiligen Textfeldern wie in UnivIS (fett, kursiv, hochgestellt, tiefgestellt, automatische Links).
