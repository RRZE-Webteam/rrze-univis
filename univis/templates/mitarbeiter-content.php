<?php if ( $daten['person'] ) :
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
                    if(count($person_orgunits)>1) :
                        $i = count($person_orgunits)-2;
                    endif;
                    $orgunit = $person_orgunits[$i];
                        
                        ?>
                     <li class="person-info-institution"><span class="screen-reader-text"><?php _e('Organisation', RRZE_UnivIS::textdomain);?>: </span><span itemprop="worksFor"><?php echo $orgunit;?></span></li>                
                    <?php endif;?>
                        
                        
                <?php if (!empty($person['orgname'])) : ?>
                    <li class="person-info-abteilung"><span class="screen-reader-text"><?php _e('Abteilung', RRZE_UnivIS::textdomain);?>: </span><?php echo $person['orgname']; ?></li>
                    <?php endif;?>  
                        
                    
                 <?php if ( array_key_exists('locations', $person) && array_key_exists('location', $person['locations'][0])) : 
                    $location = $person['locations'][0]['location'][0]; ?>
                    <?php if(!empty($location['tel'])) : ?>
                        <li class="person-info-phone"><span class="screen-reader-text"><?php _e('Telefonnummer', RRZE_UnivIS::textdomain);?>: </span><span itemprop="telephone"><?php echo $location['tel'];?></span></li>   
                    <?php endif;?>
                    <?php if(!empty($location['fax'])) : ?>
                        <li class="person-info-fax"><span class="screen-reader-text"><?php _e('Faxnummer', RRZE_UnivIS::textdomain);?>: </span><span itemprop="faxNumber"><?php echo $location['fax'];?></span></li>  
                    <?php endif;
                    if(!empty($location['email'])) : ?>
                        <li class="person-info-email"><span class="screen-reader-text"><?php _e('E-Mail', RRZE_UnivIS::textdomain);?>: </span><a itemprop="email" href="mailto:<?php echo $location['email'];?>"><?php echo $location['email'];?></a></li>                        
                    <?php endif;
                    if(!empty($location['url'])) : ?>
                        <li class="person-info-www"><span class="screen-reader-text"><?php _e('Webseite', RRZE_UnivIS::textdomain);?>: </span><a itemprop="url" href="<?php echo $location['url'];?>"><?php echo $location['url'];?></a></li>     
                    <?php endif;
                    if(!empty($location['street']) || !empty($location['ort']) || !empty($location['office'])) : ?>
                    <li class="person-info-address"><span class="screen-reader-text"><?php _e('Adresse', RRZE_UnivIS::textdomain);?>: <br></span>
                        <?php if(!empty($location['street']) || !empty($location['ort'])) : ?>
                        <?php if(!empty($location['street'])) : ?>
                        <div itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress"><span class="person-info-street" itemprop="streetAddress"><?php echo $location['street'];?></span><br>
                        <?php endif;
                        if(!empty($location['ort'])) : ?>
                            <span itemprop="addressLocality"><?php echo $location['ort'];?></span></div>
                        <?php endif;
                        endif;
                        if(!empty($location['office'])) : ?>
                            <div class="person-info-room" itemprop="workLocation" itemscope="" itemtype="http://schema.org/Person"><?php echo __('Raum', RRZE_UnivIS::textdomain) . ' ' .  $location['office'];?></div>
                        <?php endif;
                        endif;?>
                    </li>
                   
                <?php // 
                endif; ?>
                <?php //'<li class="person-info-office"><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint"><span class="screen-reader-text"> '. _e('Sprechzeiten', RRZE_UnivIS::textdomain) . ': </span>Hier müssen die Öffnungszeiten rein</span></li>'; 
                // Öffnungszeiten müssen noch mit rein, werden aus UnivIS noch nicht mit ausgelesen! ?>
            </ul>
            </div>
        </div>
    
    <?php // Zusatzinformationen aus Dateiverzeichnis kommen hier rein: assets - beschreibung ?>
    


    <!-- Lehrveranstaltungen -->
    <?php if( array_key_exists('lehrveranstaltungen', $person) && isset($person['lehrveranstaltungen']['veranstaltungen'])) : ?>
        <?php // Wenn die Publikationen noch mit reinkommen, dann das div accordion wieder außerhalb des if setzen ?>
        <div class="accordion">
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
                                        <li><a href="http://univis.uni-erlangen.de/prg?search=lectures&id=<?php echo $data['id']; ?>&show=long"><?php echo $data['name']; ?></a></li>
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

<nav class="navigation">
    <div class="nav-previous"><a href="../../"><?php _e('<span class="meta-nav">&laquo;</span> Zurück zur Übersicht', RRZE_UnivIS::textdomain); ?></a></div>
</nav>
