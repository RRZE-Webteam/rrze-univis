<!-- 2DO: Öffnungszeiten -->

<?php if ($person) : ?>
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
                <?php if (!empty($person['organization'])) : ?>
                    <li class="person-info-institution"><span class="screen-reader-text"><?php _e('Organization', 'rrze-univis');?>: </span><span itemprop="worksFor"><?php echo $person['organization'];?></span></li>
                <?php endif;?>
                <?php if (!empty($person['department'])) : ?>
                    <li class="person-info-abteilung"><span class="screen-reader-text"><?php _e('Working group', 'rrze-univis');?>: </span><?php echo $person['department']; ?></li>
                <?php endif;?>

                <?php if (!empty($person['phone'])) :
                        // $phone_number = self::correct_phone_number($location['tel']); ?>
                        <li class="person-info-phone"><span class="screen-reader-text"><?php _e('Phone number', 'rrze-univis');?>: </span><span itemprop="telephone"><?php echo $person['phone'];?></span></li>
                    <?php endif;?>
                    <?php if (!empty($person['fax'])) : ?>
                        <li class="person-info-fax"><span class="screen-reader-text"><?php _e('Fax number', 'rrze-univis');?>: </span><span itemprop="faxNumber"><?php echo $person['fax'];?></span></li>
                    <?php endif;
                    if (!empty($person['email'])) : ?>
                        <li class="person-info-email"><span class="screen-reader-text"><?php _e('Email', 'rrze-univis');?>: </span><a itemprop="email" href="mailto:<?php echo $person['email'];?>"><?php echo $person['email'];?></a></li>
                    <?php endif;
                    if (!empty($person['url'])) : ?>
                        <li class="person-info-www"><span class="screen-reader-text"><?php _e('Website', 'rrze-univis');?>: </span><a itemprop="url" href="<?php echo $person['url'];?>"><?php echo $person['url'];?></a></li>
                    <?php endif;
                    if (!empty($person['street']) || !empty($person['city']) || !empty($person['office'])) : ?>
                    <li class="person-info-address"><span class="screen-reader-text"><?php _e('Address', 'rrze-univis');?>: <br></span>
                        <?php if (!empty($person['street']) || !empty($person['city'])) : ?>
                        <?php if (!empty($person['street'])) : ?>
                        <div itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress"><span class="person-info-street" itemprop="streetAddress"><?php echo $person['street'];?></span><br>
                        <?php endif;
                        if (!empty($person['city'])) : ?>
                            <span itemprop="addressLocality"><?php echo $person['city'];?></span></div>
                        <?php endif;
                        endif;
                        if (!empty($person['office'])) : ?>
                            <div class="person-info-room" itemprop="workLocation" itemscope="" itemtype="http://schema.org/Person"><?php echo __('Room', 'rrze-univis') . ' ' .  $person['office'];?></div>
                        <?php endif;
                        endif;?>
                    </li>

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
							echo " ";
							echo _e('to', 'rrze-univis') . " " . $officehour['endtime'];
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
    <?php if (!empty($person['lectures'])) : ?>
        <div>
            <h3 class="active">Lehrveranstaltungen</h3>
            <?php foreach ($person['lectures'] as $lec_type => $lectures) :
                ?>
                <div>
                    <ul>
                        <li>
                            <h3><?php echo $lec_type; ?></h3>
                            <ul>
                            <?php foreach ($lectures as $lecture) : ?>
                                <li><a href="lv_id/<?php echo $lecture['lecture_id']; ?>"><?php echo $lecture['name']; ?></a></li>
                            <?php endforeach; ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            <?php endforeach; ?>
    	</div>
    <?php endif; ?>
<?php endif;?>
