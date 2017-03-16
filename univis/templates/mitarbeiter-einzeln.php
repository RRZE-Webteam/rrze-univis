<div id="univis-personenseite">
<?php if ( $daten['person'] ) :
    $person = $daten['person'];?>
<?php //_rrze_debug($daten); ?>
	<div class="person" class="person liste-person" itemscope itemtype="http://schema.org/Person">

            <?php  
            if (!empty($person['title'])) :
                $name['title'] = '<span class="honorific-prefix" itemprop="honorificPrefix"><acronym title="' . $person['title_long'] . '">' . $person['title'] . '</acronym></span>';
            endif;
            if (!empty($person['firstname'])) :
                $name['firstname'] = '<span class="given-name" itemprop="givenName">' . $person['firstname'] . '</span>';
            endif;
            if (!empty($person['lastname'])) :
                $name['lastname'] = '<span class="family-name" itemprop="familyName">' . $person['lastname'] . '</span>';
                if(!empty($person['atitle'])) :
                    $name['lastname'] .= ',';
                endif;
            endif;
            if (!empty($person['atitle'])) :
                $name['atitle'] = '<span class="honorific-suffix" itemprop="honorificSuffix"><acronym title="' . $person['atitle_long'] . '">' . $person['atitle'] . '</span>';
            endif;
            $fullname = implode(' ', $name);                
                ?>
            <h2><span itemprop="name"><?php echo $fullname;?></span></h2>
            <ul class="person-info">    
                <?php if (!empty($person['work'])) : ?>
                        <li class="person-info-position"><span class="screen-reader-text"><?php _e('Tätigkeit', RRZE_UnivIS::textdomain);?>: </span><strong><span itemprop="jobTitle"><?php echo $person['work']; ?></span></strong></li>
                    <?php endif;?>

                <?php if ( array_key_exists('orgunits', $person) && array_key_exists('orgunit', $person['orgunits'][0])) :
                    $person_orgunits = $person['orgunits'][0]['orgunit'];
                    $i = count($person_orgunits);
                    if(count($person_orgunits)>1) {
                        $i = count($person_orgunits)-2;
                    }
                    $orgunit = $person_orgunits[$i];
                        
                        ?>
                     <li class="person-info-institution"><span class="screen-reader-text"><?php _e('Organisation', RRZE_UnivIS::textdomain);?>: </span><span itemprop="worksFor"><?php echo $orgunit;?></span></li>                
                    <?php endif;?>
                        
                        
                <?php if (!empty($person['orgname'])) : ?>
                    <li class="person-info-abteilung"><span class="screen-reader-text"><?php _e('Abteilung', RRZE_UnivIS::textdomain);?>: </span><?php echo $person['orgname']; ?></li>
                    <?php endif;?>  
                        
                    
                 <?php if ( array_key_exists('locations', $person) && array_key_exists('location', $person['locations'][0])) : 
                    $location = $person['locations'][0]['location'][0]; ?>
                    <li class="person-info-phone"><span class="screen-reader-text"><?php _e('Telefonnummer', RRZE_UnivIS::textdomain);?>: </span><span itemprop="telephone"><?php echo $location['tel'];?></span></li>                        
                    <li class="person-info-email"><span class="screen-reader-text"><?php _e('E-Mail', RRZE_UnivIS::textdomain);?>: </span><a itemprop="email" href="mailto:<?php echo $location['email'];?>"><?php echo $location['email'];?></a></li>                        
                    <li class="person-info-www"><span class="screen-reader-text"><?php _e('Webseite', RRZE_UnivIS::textdomain);?>: </span><a itemprop="url" href="<?php echo $location['url'];?>"><?php echo $location['url'];?></a></li>     
                    <li class="person-info-address"><span class="screen-reader-text"><?php _e('Adresse', RRZE_UnivIS::textdomain);?>: <br></span>
                        <div itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress"><span class="person-info-street" itemprop="streetAddress"><?php echo $location['street'];?></span><br>
                        <span itemprop="addressLocality"><?php echo $location['ort'];?></span></div><div class="person-info-room" itemprop="workLocation" itemscope="" itemtype="http://schema.org/Person"><?php echo $location['office'];?></div></li>
    <?php // FAX FEHLT
    endif; ?>
                    
    <li class="person-info-office"><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint"><span class="screen-reader-text">Sprechzeiten: </span>Jede Woche Di, 15:30 - 16:30, Raum 2.054,</span></li>
</ul>
</div>
</div>                

				




{{#email}}
	                <li>E-Mail: <a class="email" href="mailto:{{email}}">{{email}}</a></li>
	                {{/email}}
	                {{#tel}}
	                <li>Telefon: <span class="tel">{{tel}}</span></li>
	                {{/tel}}
	                {{#fax}}
	                <li>Fax: <span class="fax">{{fax}}</span></li>
	                {{/fax}}
	                {{#office}}
	                <li>Raum: <span class="office">{{office}}</span></li>
	                {{/office}}
			    </ul>
			    {{/location}}
			{{/locations}}
	    </address>
	</div>

	<div class="accordion" style="padding-top: 30px;">
		<!-- Zusatzinformationen aus Dateiverzeichnis -->
		{{#assets}}
			{{#beschreibung}}
				<h3 class="active">Information zur Person</h3>

			<!-- Unformatierten Text einfügen -->
				<pre>{{beschreibung}}</pre>

			<!-- Formatiertes HTML einfügen -->
			<!-- <div>{{{beschreibung}}}</div> -->

			{{/beschreibung}}
		{{/assets}}

		<!-- Lehrveranstaltungen -->
		{{#lehrveranstaltungen}}
			<h3 class="active">Lehrveranstaltungen</h3>
			<div>
				<ul>
					{{#lehrveranstaltungen}}
						{{#veranstaltungen}}
						<li>
							<h3>{{title}}</h3>
							<ul>
								{{#data}}
									<li><a href="http://univis.uni-erlangen.de/prg?search=lectures&id={{id}}&show=long">{{name}}</a></li>
								{{/data}}
							</ul>
						</li>
						{{/veranstaltungen}}
					{{/lehrveranstaltungen}}
				</ul>
			</div>
		{{/lehrveranstaltungen}}

		{{#publikationen}}
			<h3 class="active">Publikationen</h3>
			<div>
			{{#publikationen}}
				{{#years}}

	<h4>{{title}}</h4>
	<ul>
		{{#data}}
			<li style="margin-bottom: 10px;">
			<span>
				{{#authors}}
					{{#author}}
						{{#pkey}}
							{{#full-profile}}
								<a href="http://univis.uni-erlangen.de/prg?search=persons&id={{ id }}&show=info">{{lastname}}, {{firstname}}</a>;
							{{/full-profile}}

							{{^full-profile}}
								{{name}};
							{{/full-profile}}
						{{/pkey}}
					{{/author}}
				{{/authors}}
			</span>
			<br>
			<b><i>{{pubtitle}}</i></b>
			<br />
			<span>

				<!-- Artikel in Zeitschriften (artzeit) -->
				{{#journal}}
					In: <b><i>{{journal}}</i></b> {{#volume}}{{volume}}{{/volume}} ({{year}}), S. {{pages}}
					{{#doi}}<br />[DOI: <a href="http://dx.doi.org/{{doi}}" target="_blank">{{doi}}</a>]{{/doi}}
					{{#puburl}}<br />[URL: <a href="{{puburl}}" target="_blank">{{puburl}}</a>]{{/puburl}}
				{{/journal}}

				<!-- Artikel in Sammelband (artmono) -->
				{{#booktitle}}{{^conference}}
					In: {{#editors}}{{#editor}}{{#pkey}}<b>{{lastname}}</b>{{/pkey}}{{/editor}} (Hrsg.): {{/editors}}<b><i>{{booktitle}}.</i></b>
					<br />{{#plocation}}{{plocation}} : {{/plocation}}{{publisher}}, {{year}}, S. {{pages}}.
					{{#doi}}<br />[DOI: <a href="http://dx.doi.org/{{doi}}" target="_blank">{{doi}}</a>]{{/doi}}
					{{#puburl}}<br />[URL: <a href="{{puburl}}" target="_blank">{{puburl}}</a>]{{/puburl}}
				{{/conference}}{{/booktitle}}

				<!-- Vortrag (talk) -->
				{{#conference}}{{^booktitle}}{{^publisher}}
					Vortrag: {{conference}}{{#school}}, {{school}}{{/school}}
					<br />{{#address}}{{address}}, {{/address}}{{#hsyear}}{{hsyear}}.{{year}}{{/hsyear}}{{^hsyear}}{{year}}{{/hsyear}}
					{{#doi}}<br />[DOI: <a href="http://dx.doi.org/{{doi}}" target="_blank">{{doi}}</a>]{{/doi}}
					{{#puburl}}<br />[URL: <a href="{{puburl}}" target="_blank">{{puburl}}</a>]{{/puburl}}
				{{/publisher}}{{/booktitle}}{{/conference}}

				<!-- Artikel in Tagungsband (arttagu) -->
				{{#conference}}{{#booktitle}}
					{{#organizer}}
						In: <b>{{organizer}}</b> (Veranst.):
					{{/organizer}}
					{{^organizer}}
						{{#editors}}In:
							{{#editor}}
								{{#pkey}}<b>{{lastname}}</b>; {{/pkey}}
							{{/editor}}
						(Hrsg:){{/editors}}
					{{/organizer}}
					<b><i>{{booktitle}}</i></b>
					<br /><i>({{conference}}, {{address}}{{#hsyear}}, {{hsyear}}{{/hsyear}})</i>
					<br />{{#plocation}}{{plocation}} : {{/plocation}}{{#publisher}}{{publisher}}, {{/publisher}}{{year}}{{#pages}}, S. {{pages}}.{{/pages}}
					{{#series}}<br />({{series}}{{/series}}{{#servolume}} Bd. {{servolume}}{{/servolume}}{{#series}}){{/series}}{{#isbn}} - ISBN {{isbn}}{{/isbn}}
					{{#doi}}<br />[DOI: <a href="http://dx.doi.org/{{doi}}" target="_blank">{{doi}}</a>]{{/doi}}
					{{#puburl}}<br />[URL: <a href="{{puburl}}" target="_blank">{{puburl}}</a>]{{/puburl}}
				{{/booktitle}}{{/conference}}

				<!-- Monographie (monogr) -->
				{{^journal}}{{^booktitle}}{{^conference}}{{^school}}{{^number}}
					{{#volume}}BD. {{volume}}. {{/volume}}{{#plocation}}{{plocation}} : {{/plocation}}{{#publisher}}{{publisher}}, {{/publisher}}{{year}}
					{{#series}}<br />({{series}}{{/series}}{{#servolume}} Bd. {{servolume}}){{/servolume}}
					<br />{{#pages}}{{pages}} Seiten.{{/pages}}{{#isbn}} ISBN {{isbn}}{{/isbn}}
					{{#doi}}<br />[DOI: <a href="http://dx.doi.org/{{doi}}" target="_blank">{{doi}}</a>]{{/doi}}
					{{#puburl}}<br />[URL: <a href="{{puburl}}" target="_blank">{{puburl}}</a>]{{/puburl}}
				{{/number}}{{/school}}{{/conference}}{{/booktitle}}{{/journal}}

				<!-- Hochschulschrift (Dissertation, Habilitationsschrift, Diplomarbeit etc.) (hschri) -->
				<!-- Hochschulschrift (auch im Verlag erschienen) (dissvg) -->
				<!-- Interner Bericht (Technischer Bericht, Forschungsbericht) (techrep) -->
				{{#school}}{{^conference}}
					{{#publisher}}{{#plocation}}{{plocation}} : {{/plocation}}{{publisher}}, {{year}}<br />{{/publisher}} <!-- nur für dissvg -->
					{{address}}, {{school}}{{#hstype}}, {{hstype}}{{/hstype}}{{#number}} ({{number}}){{/number}}, {{#hsyear}}{{hsyear}}{{/hsyear}}{{^hsyear}}{{year}}{{/hsyear}}.
					{{#series}}<br />({{series}}{{/series}}{{#servolume}} Bd. {{servolume}}){{/servolume}}
					<br />{{#pages}}{{pages}} Seiten. {{/pages}}{{#isbn}}ISBN {{isbn}}. {{/isbn}}{{#issn}}ISSN {{issn}}{{/issn}}
					{{#doi}}<br />[DOI: <a href="http://dx.doi.org/{{doi}}" target="_blank">{{doi}}</a>]{{/doi}}
					{{#puburl}}<br />[URL: <a href="{{puburl}}" target="_blank">{{puburl}}</a>{{/puburl}}
				{{/conference}}{{/school}}

				<!-- Tagungsband (nicht im Verlag erschienen) (tagung) -->
				<!-- Tagungsband (als Verlagspublikation) (tagband) -->
				{{#conference}}{{#publisher}}{{^booktitle}}
					<i>({{conference}} {{address}} {{hsyear}})</i>
					<br />{{#plocation}}{{/plocation}} : {{plocation}}{{#publisher}}{{publisher}}, {{/publisher}}{{year}}
					{{#series}}<br />({{series}}{{/series}}{{#number}}, Nr. {{number}}{{/number}}{{#series}}.){{series}}{{/series}}
					<br />{{#pages}}{{pages}} Seiten.{{/pages}}{{#isbn}} ISBN {{isbn}}.{{/isbn}}{{#issn}} ISSN {{issn}}{{/issn}}
					{{#doi}}<br />[DOI: <a href="http://dx.doi.org/{{doi}}" target="_blank">{{doi}}</a>]{{/doi}}
					{{#puburl}}<br />[URL: <a href="{{puburl}}" target="_blank">{{puburl}}</a>{{/puburl}}
				{{/booktitle}}{{/publisher}}{{/conference}}

				<!-- Schutzrecht (schutzr) -->
				{{#number}}{{^school}}{{^journal}}
					Schutzrecht {{number}} {{#hstype}}{{hstype}}{{/hstype}} ({{hsyear}})
				{{/journal}}{{/school}}{{/number}}

			</span>
			<br>
		</li>
		{{/data}}
	</ul>
{{/years}}
			{{/publikationen}}
		</div>
		{{/publikationen}}
	</div> <!-- end: div.accordion -->

	<script type="text/javascript">
		$('.accordion').find('h3').click(function(){
			$(this).next().slideToggle();
			$(this).toggleClass("active off");
		});
</script>
</div>
