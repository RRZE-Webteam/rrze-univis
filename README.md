# RRZE-UnivIS

Darstellung von Lehrveranstaltungen und organisatorischen Daten aus UnivIS.

## Download 

GITHub-Repo: https://github.com/RRZE-Webteam/rrze-univis


## Autor 
RRZE-Webteam , http://www.rrze.fau.de

## Copryright

GNU General Public License (GPL) Version 3 


## Zweck 

- Shortcode [univis] zum Einbindung von UnivIS-Daten in WordPress-Seiten.
- Gutenberg-Blöcke "RRZE-UnivIS Mitarbeiter", "RRZE-UnivIS Lehrveranstaltungen" und "RRZE-UnivIS Publikationen"
- Widget "RRZE UnivIS"

## Dokumentation

Eine vollständige Dokumentation mit vielen Anwendungsbeispielen findet sich auf der Seite: 
https://www.wordpress.rrze.fau.de/plugins/fau-und-rrze-plugins/rrze-univis/


### Kurzeinführung


#### WP-Einstellungsmenü

Einstellungen › RRZE-UnivIS

#### Verwendung des Shortcodes [univis]

- Anzeige eines Links zur UnivIS-Startseite. Der Linktext kann unter Einstellungen / RRZE-UnivIS modifiziert werden
```
[univis]
```
- Bindet die Mitarbeiterübersicht ein - besonders geeignet für wissenschaftliche Einrichtungen (Telefonnummern, Mobilnummern, E-Mail-Adressen und Postadressen können ein- und ausgeblendet werden. Mit "call" können Telefonnummern auf mobilen Geräten direkt gewählt werden.)
```
[univis number="123456789"]
[univis task="mitarbeiter-alle"]
[univis task="mitarbeiter-alle" show="telefon, mail, address, mobile, call"]
[univis task="mitarbeiter-alle"]
```    
- Bindet die Mitarbeiterübersicht ein - besonders geeignet für nicht-wissenschaftliche Einrichtungen (Telefonnummern und E-Mail-Adressen können ein- und ausgeblendet werden.)
```
[univis task="mitarbeiter-orga"]
[univis task="mitarbeiter-orga" hide="telefon"]
[univis task="mitarbeiter-orga" show="mail"]
```
- Bindet die Mitarbeiterübersicht der Organisationseinheit mit der UnivISOrgNr 123456789 im Telefonbuchformat ein (alphabetische Sortierung, optional mit Telefonnummern, E-Mail-Adressen, Sprungmarken)
```
[univis number="123456789" task="mitarbeiter-telefonbuch"]
[univis number="123456789" task="mitarbeiter-telefonbuch" show="telefon, mail, sprungmarken"]
```
- Bindet sämtliche Lehrveranstaltungen der Organisationseinheit mit der UnivISOrgNr 123456789 ein
```
[univis number="123456789" task="lehrveranstaltungen-alle"]
```
- Bindet sämtliche UnivIS-Publikationen der eingestellten Organisationseinheit ein
```
[univis task="publikationen"]
```


##### Ausblenden importierter Lehrveranstaltungen möglich

- Blendet alle importierten Lehrveranstaltungen aus, um doppelte Ausgaben zu vermeiden
```
[univis task="lehrveranstaltungen-alle" lv_import="0"]
```

##### Filterung nach Lehrveranstaltungstyp möglich

- Gibt alle Vorlesungen der Org-Nr. 123456789 aus. Zu den Terminen werden .ics Dateien angeboten, um diese in den eigenen Kalender einzutragen. Bei type müssen die Kürzel wie im Vorlesungsverzeichnis angegeben werden (vorl, ueb, tut, ...)
```
[univis task="lehrveranstaltungen-alle" id="123456789" type="vorl" show="ics"]
```
- Bindet die Daten der einen Person ein. Die Person muss dabei der Organisationseinheit angehören, die in Einstellungen - UnivIS eingegeben wurde
```
[univis task="mitarbeiter-einzeln" firstname="Max" lastname="Mustermann"]
[univis task="mitarbeiter-einzeln" name="Mustermann,Max"]
```
- Zeigt die Daten zur Lehrveranstaltung mit dieser ID. Die Lehrveranstaltung muss dabei der Organisationseinheit zugeordnet sein, die in Einstellungen - RRZE-UnivIS eingegeben wurde und aus dem aktuellen Semester stammen
```
[univis task="lehrveranstaltungen-einzeln" id="123456789"]
```
- Publikationen, eingeschränkt nach Erscheinungsjahr:
```
[univis task="publikationen" since="2017"]
```
- Zeigt alle Lehrveranstaltungen der Person mit dieser ID. Der Dozent muss dabei der Organisationseinheit angehören, die in Einstellungen - UnivIS eingegeben wurde.
```
[univis task="lehrveranstaltungen-alle" dozentid="123456789"]
```
- Zeigt alle Lehrveranstaltungen zu einem bestimmten Typ in gewünschter Reihenfolge. Beispiel: Vorlesungen, dann Übungen, aber keine Tutorials oder andere Lehrveranstaltungen:
```
[univis task="lehrveranstaltungen-alle" order="vorl,ueb"]
```
- Zeigt alle Lehrveranstaltungen der Person mit dem Namen Max Mustermann. Der Dozent muss dabei der Organisationseinheit angehören, die in Einstellungen - UnivIS eingegeben wurde. Der Name des Dozenten muss in der Form Nachname,Vorname ohne Leerzeichen angegeben werden.
```
[univis task="lehrveranstaltungen-alle" dozentname="Mustermann,Max"]
```
- Zeigt alle Lehrveranstaltungen an:
Im aktuellen Semester
```
[univis task="lehrveranstaltungen-alle"]
```
Im nächsten Semester
```
[univis task="lehrveranstaltungen-alle" sem="1"]
```
Im vergangenen Semester
```
[univis task="lehrveranstaltungen-alle" sem="-1"]
```
Im Sommersemester 2021
```
[univis task="lehrveranstaltungen-alle" sem="2021s"]
```

##### Alle Attribute und deren Werte

- task:
mitarbeiter-alle, 
mitarbeiter-einzeln, 
mitarbeiter-orga, 
mitarbeiter-telefonbuch, 
lehrveranstaltungen-alle, 
lehrveranstaltungen-einzeln, 
publikationen, 

- univisid

- number

- id

- name

- dozentename

- lv_id

- show:
sprungmarken, 
ics, 
telefon, 
mobile, 
fax,
url,
address, 
call

- hide:
sprungmarken, 
ics, 
telefon, 
mobile, 
fax,
url,
address, 
call

- sem:
-1, 
1, 
2021s

- order

- since

- lv_import

- zeige_jobs

- ignoriere_jobs

- hstart

#### Hinweise

- Der Shortcode-Parameter number kann weggelassen werden, wenn in der Einstellungsseite des Plugins (Einstellungen - UnivIS) eine UnivISOrgNr vergeben wird. 
- Bei der Anzeige von Lehrveranstaltungen wird automatisch das Semester angezeigt, dass bei UnivIS als aktuelles Semester eingestellt ist. 
- Formatierungen von UnivIS werden in HTML übersetzt (fett, kursiv, hochgestellt, tiefgestellt, automatische Links, mehrzeilig)
- Die UnivIS-ID einer Lehrveranstaltung, Organisation oder Person finden Sie über die Suche unter "Settings" oder in der Metabox beim Erstellen eines Posts oder einer Page.