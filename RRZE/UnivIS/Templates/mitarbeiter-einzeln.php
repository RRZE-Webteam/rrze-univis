<?php if ($daten['person']) :
    $person = $daten['person'];?>
	<div class="person" class="person liste-person" itemscope itemtype="http://schema.org/Person">
        <div class="page">
            <?php
            if (!empty($person['title'])) :
                $name['title'] = '<span class="honorific-prefix" itemprop="honorificPrefix"><acronym title="' . $person['title_long'] . '">' . $person['title'] . '</acronym></span>';
            endif;
            if (!empty($person['firstname'])) :
                $name['firstname'] = '<span class="given-name" itemprop="givenName">' . $person['firstname'] . '</span>';
            endif;
            if (!empty($person['lastname'])) :
                $name['lastname'] = '<span class="family-name" itemprop="familyName">' . $person['lastname'] . '</span>';
                if (!empty($person['atitle'])) :
                    $name['lastname'] .= ',';
                endif;
            endif;
            if (!empty($person['atitle'])) :
                $name['atitle'] = '<span class="honorific-suffix" itemprop="honorificSuffix"><acronym title="' . $person['atitle_long'] . '">' . $person['atitle'] . '</span>';
            endif;
            $fullname = implode(' ', $name); ?>
            <h2><span itemprop="name"><?php echo $fullname;?></span></h2>
            <ul class="person-info">
                <?php if (!empty($person['work'])) : ?>
                        <li class="person-info-position"><span class="screen-reader-text"><?php _e('Job title', 'rrze-univis');?>: </span><strong><span itemprop="jobTitle"><?php echo $person['work']; ?></span></strong></li>
                    <?php endif;?>

                <?php
                if ($suffix != '' && array_key_exists($orgunits, $person) && array_key_exists($orgunit, $person[$orgunits][0])) :
                    $person_orgunits = $person[$orgunits][0][$orgunit];
                elseif (array_key_exists('orgunits', $person) && array_key_exists('orgunit', $person['orgunits'][0])) :
                    $person_orgunits = $person['orgunits'][0]['orgunit'];
                endif;
                if (!empty($person_orgunits)) :
                    $i = count($person_orgunits);
                    if (count($person_orgunits)>1) :
                        $i = count($person_orgunits)-2;
                    endif;
                    $orgunit = $person_orgunits[$i];
                        ?>
                     <li class="person-info-institution"><span class="screen-reader-text"><?php _e('Organization', 'rrze-univis');?>: </span><span itemprop="worksFor"><?php echo $orgunit;?></span></li>
                    <?php endif;?>


                <?php if ($suffix != '' && !empty($person[$orgname])) :
                    $orgname_out = $person[$orgname];
                elseif (!empty($person['orgname'])) :
                    $orgname_out = $person['orgname'];
                endif;
                if (!empty($orgname_out)) : ?>
                    <li class="person-info-abteilung"><span class="screen-reader-text"><?php _e('Working group', 'rrze-univis');?>: </span><?php echo $orgname_out; ?></li>
                    <?php endif;?>


                 <?php if (array_key_exists('locations', $person) && array_key_exists('location', $person['locations'][0])) :
                    $location = $person['locations'][0]['location'][0]; ?>
                    <?php if (!empty($location['tel'])) :
                        $phone_number = self::correct_phone_number($location['tel']); ?>
                        <li class="person-info-phone"><span class="screen-reader-text"><?php _e('Phone number', 'rrze-univis');?>: </span><span itemprop="telephone"><?php echo $phone_number;?></span></li>
                    <?php endif;?>
                    <?php if (!empty($location['fax'])) : ?>
                        <li class="person-info-fax"><span class="screen-reader-text"><?php _e('Fax number', 'rrze-univis');?>: </span><span itemprop="faxNumber"><?php echo $location['fax'];?></span></li>
                    <?php endif;
                    if (!empty($location['email'])) : ?>
                        <li class="person-info-email"><span class="screen-reader-text"><?php _e('Email', 'rrze-univis');?>: </span><a itemprop="email" href="mailto:<?php echo $location['email'];?>"><?php echo $location['email'];?></a></li>
                    <?php endif;
                    if (!empty($location['url'])) : ?>
                        <li class="person-info-www"><span class="screen-reader-text"><?php _e('Website', 'rrze-univis');?>: </span><a itemprop="url" href="<?php echo $location['url'];?>"><?php echo $location['url'];?></a></li>
                    <?php endif;
                    if (!empty($location['street']) || !empty($location['ort']) || !empty($location['office'])) : ?>
                    <li class="person-info-address"><span class="screen-reader-text"><?php _e('Address', 'rrze-univis');?>: <br></span>
                        <?php if (!empty($location['street']) || !empty($location['ort'])) : ?>
                        <?php if (!empty($location['street'])) : ?>
                        <div itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress"><span class="person-info-street" itemprop="streetAddress"><?php echo $location['street'];?></span><br>
                        <?php endif;
                        if (!empty($location['ort'])) : ?>
                            <span itemprop="addressLocality"><?php echo $location['ort'];?></span></div>
                        <?php endif;
                        endif;
                        if (!empty($location['office'])) : ?>
                            <div class="person-info-room" itemprop="workLocation" itemscope="" itemtype="http://schema.org/Person"><?php echo __('Room', 'rrze-univis') . ' ' .  $location['office'];?></div>
                        <?php endif;
                        endif;?>
                    </li>

                <?php //
                endif; ?>
           		 <?php if (array_key_exists('officehours', $person) && array_key_exists('officehour', $person['officehours'][0])) :
					echo '<br><li class="person-info-office"><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint"><span class="screen-reader-text">' ;
					echo _e('Office hours', 'rrze-univis') . ': </span> <span><b>'; 
					echo _e('Office hours', 'rrze-univis') .':</b><br>' ;
					 foreach ($person['officehours'][0]['officehour'] as $officehour){
					 
						if (!empty($officehour['repeatstring'])) { 
							 echo $officehour['repeatstring'] . " ";
						}
						if (!empty($officehour['starttime'])) { 
							 echo $officehour['starttime'];
						}
					    if (!empty($officehour['endtime'])) { 
							echo " bis " . $officehour['endtime'];
						}
						if (!empty($officehour['office'])) { 
							  echo ", " ; 
							  echo  _e('Room', 'rrze-univis') . " " . $officehour['office'];
							}
						if (!empty($officehour['comment'])) { 
							if (!empty($officehour['repeatstring']) || !empty($officehour['starttime']) ) {
								echo ", " ;
							}
							  echo $officehour['comment'];
							}
							echo "<br>";
					 }
					 echo " </span></li>"; 
                endif; ?> 
            </ul>
        </div>
    </div>
    <?php // Zusatzinformationen aus Dateiverzeichnis kommen hier rein: assets - beschreibung?>
    <!-- Lehrveranstaltungen -->
    <?php if (array_key_exists('lehrveranstaltungen', $person) && isset($person['lehrveranstaltungen']['veranstaltungen'])) : ?>
        <?php // Wenn die Publikationen noch mit reinkommen, dann das nächste div wieder außerhalb des if setzen?>
        <div>
            <h3 class="active">Lehrveranstaltungen</h3>
            <?php foreach ($person['lehrveranstaltungen']['veranstaltungen'] as $veranstaltungen) :
                ?>
                <div>
                    <ul>
                        <li>
                            <h3><?php echo $veranstaltungen['title']; ?></h3>
                            <ul>
                                <?php if (array_key_exists('data', $veranstaltungen)) :
                                    foreach ($veranstaltungen['data'] as $data) :
                                        ?>
                                        <li><a href="lv_id/<?php echo $data['id']; ?>"><?php echo $data['name']; ?></a></li>
                                    <?php endforeach;
                                endif;
                                ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            <?php endforeach; ?>
    	</div>
    <?php endif; ?>
<?php endif;?>
