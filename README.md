rrze-univis
============

WordPress Plugin
----------------

Neu in Version 1.2.6:    

- Ausblenden importierter Lehrveranstaltungen möglich: [univis task="lehrveranstaltungen-alle" id="49680223" lv_import="0"] blendet alle importierten Lehrveranstaltungen aus, um doppelte Ausgaben zu vermeiden.     

Neu in Version 1.2.1:    

- Filterung nach Lehrveranstaltungstyp möglich: [univis task="lehrveranstaltungen-alle" id="21101522" type="vorl"] gibt z.B. alle Vorlesungen der Org-Nr. 21101522 aus. Bei type müssen die Kürzel wie im Vorlesungsverzeichnis angegeben werden.    
- Übersetzungsdatei für Britisch-Englisch ergänzt

Neu in Version 1.2:

- Bei der Anzeige von Lehrveranstaltungen wird automatisch das Semester angezeigt, dass gerade bei UnivIS als aktuelles Semester eingestellt ist    
- [univis task="mitarbeiter-einzeln" firstname="Max" lastname="Mustermann"]: Bindet die Daten der einen Person ein. Die Person muss dabei der Organisationseinheit angehören, die in Einstellungen - UnivIS eingegeben wurde.    
- [univis task="lehrveranstaltungen-einzeln" id="21101522"]: Zeigt die Daten zur Lehrveranstaltung mit dieser ID. Die Lehrveranstaltung muss dabei der Organisationseinheit zugeordnet sein, die in Einstellungen - UnivIS eingegeben wurde, und außerdem aus dem aktuellen Semester stammen.    
- [univis task="lehrveranstaltungen-alle" dozentid="21555666"]: Zeigt alle Lehrveranstaltungen der Person mit dieser ID. Der Dozent muss dabei der Organisationseinheit angehören, die in Einstellungen - UnivIS eingegeben wurde.    
- [univis task="lehrveranstaltungen-alle" dozentname="Mustermann,Max"]: Zeigt alle Lehrveranstaltungen der Person mit dem Namen Max Mustermann. Der Dozent muss dabei der Organisationseinheit angehören, die in Einstellungen - UnivIS eingegeben wurde. Der Name des Dozenten muss in der Form Nachname,Vorname angegeben werden (ohne Leerzeichen!).    

Einbindung von UnivIS-Daten auf WordPress-Seiten mittels Shortcode:

- [univis]: Anzeige eines Links zur UnivIS-Startseite, der Linktext kann unter Einstellungen - UnivIS modifiziert werden    
- [univis number="1005681200"] oder [univis number="1005681200" task="mitarbeiter-alle"]: Bindet die Mitarbeiterübersicht der Organisationseinheit mit der UnivISOrgNr 1005681200 ein, besonders geeignet für wissenschaftliche Einrichtungen    
- [univis number="1005681200" task="mitarbeiter-orga"]: Bindet die Mitarbeiterübersicht der Organisationseinheit mit der UnivISOrgNr 1005681200 ein, besonders geeignet für nicht-wissenschaftliche Einrichtungen    
- [univis number="1005681200" task="lehrveranstaltungen-alle"]: Bindet sämtliche Lehrveranstaltungen der Organisationseinheit mit der UnivISOrgNr 1005681200 ein    
- [univis number="1005681200" task="publikationen"]: Bindet sämtliche UnivIS-Publikationen der Organisationseinheit mit der UnivISOrgNr 1005681200 ein    

Der Shortcode-Parameter number kann weggelassen werden, wenn in der Einstellungsseite des Plugins (Einstellungen - UnivIS) eine UnivISOrgNr vergeben wird. Dann muss aber zwingend der Parameter task vergeben werden (default-Wert für task ist mitarbeiter-alle).


Umsetzung der automatischen Formatierungen in mehrzeiligen Textfeldern wie in UnivIS (fett, kursiv, hochgestellt, tiefgestellt, automatische Links).
