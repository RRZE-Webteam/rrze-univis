<?php if ($person) : ?>
	<div class="person rrze-univis" class="person liste-person" itemscope itemtype="http://schema.org/Person">
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
                $name['atitle'] = '<span class="honorific-suffix" itemprop="honorificSuffix"><acronym title="' . (!empty($person['atitle_long']) ? $person['atitle_long'] : $person['atitle']) . '">' . $person['atitle'] . '</span>';
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

                <?php 
                    if (!empty($person['locations'])){
                        // phone
                        foreach($person['locations'] as $location){
                            if (!empty($location['tel']) && in_array('telefon', $this->show) && !in_array('telefon', $this->hide)){
                                echo '<li class="person-info-phone"><span class="screen-reader-text">' . __('Phone number', 'rrze-univis') . ': </span><span itemprop="telephone">' . $location['tel'] . '</span></li>';
                            }
                        }
                        // fax
                        foreach($person['locations'] as $location){
                            if (!empty($location['fax']) && in_array('telefon', $this->show) && !in_array('telefon', $this->hide)){
                                echo '<li class="person-info-fax"><span class="screen-reader-text">' . __('Fax number', 'rrze-univis') . ': </span><span itemprop="faxNumber">' . $location['fax'] . '</span></li>';
                            }
                        }
                        // email
                        foreach($person['locations'] as $location){
                            if (!empty($location['email']) && in_array('mail', $this->show) && !in_array('mail', $this->hide)){
                                echo '<li class="person-info-email"><span class="screen-reader-text">' . __('Email', 'rrze-univis') . ': </span><span itemprop="email">' . $location['email'] . '</span></li>';
                            }
                        }
                        // address
                        foreach($person['locations'] as $location){
                            if (!empty($location['url']) && ((in_array('url', $this->show) && !in_array('url', $this->hide)) || ((in_array('address', $this->show) && !in_array('address', $this->hide) && !in_array('url', $this->hide))))){
                                echo '<li class="person-info-www"><span class="screen-reader-text">' . __('Website', 'rrze-univis') . ': </span><a itemprop="url" href="' . $location['url'] . '">' . $location['url'] . '</a></li>';
                            }
                            if (in_array('address', $this->show) && !in_array('address', $this->hide) && (!empty($location['street']) || !empty($location['city']) || !empty($location['office']))){
                                if (!empty($location['street']) || !empty($location['city'])){
                                    echo '<li class="person-info-address"><span class="screen-reader-text">' . __('Address', 'rrze-univis') . ': <br></span>';
                                    if (!empty($location['street'])){
                                        echo '<div itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress"><span class="person-info-street" itemprop="streetAddress">' . $location['street'] . '</span><br>';
                                    }
                                    if (!empty($location['city'])){
                                        echo '<span itemprop="addressLocality">' . $location['city'] . '</span>';
                                        
                                    }
                                    echo '</div>';
                                }
                                if (!empty($location['office'])){
                                    echo '<div class="person-info-room" itemprop="workLocation" itemscope="" itemtype="http://schema.org/Person">' . __('Room', 'rrze-univis') . ' ' .  $location['office'] . '</div>';
                                }
                                if (!empty($location['street']) || !empty($location['city'])){
                                    echo '</li>';
                                }
                            }
                        }

                   		if (!empty($person['officehours'])){
        					echo '<li class="person-info-office"><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint"><span class="screen-reader-text">' ;
        					echo _e('Office hours', 'rrze-univis') . ': </span> <span><b>'; 
        					echo _e('Office hours', 'rrze-univis') .':</b><br>' ;
        					foreach ($person['officehours'] as $officehour){
        					 
        						if (!empty($officehour['repeat'])) { 
        							 echo $officehour['repeat'] . " ";
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
        							if (!empty($officehour['repeat']) || !empty($officehour['starttime']) ) {
        								echo ", " ;
        							}
        							  echo $officehour['comment'];
        							}
        							echo "<br>";
        					}
        					echo " </span></li>"; 
                        }
                    } ?> 
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
