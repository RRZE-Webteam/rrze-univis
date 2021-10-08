<?php 
    $lang = get_locale();

    foreach ($data as $person) : 
    ?>
	<div class="person rrze-univis" class="person liste-person">
        <div class="page">
            <?php
            if (!empty($person['title'])) :
                $name['title'] = '<span class="honorific-prefix"><acronym title="' . $person['title_long'] . '">' . $person['title'] . '</acronym></span>';
            endif;
            if (!empty($person['firstname'])) :
                $name['firstname'] = '<span class="given-name">' . $person['firstname'] . '</span>';
            endif;
            if (!empty($person['lastname'])) :
                $name['lastname'] = '<span class="family-name">' . $person['lastname'] . '</span>';
                if (!empty($person['atitle'])) :
                    $name['lastname'] .= ',';
                endif;
            endif;
            if (!empty($person['atitle'])) :
                $name['atitle'] = '<span class="honorific-suffix"><acronym title="' . (!empty($person['atitle_long']) ? $person['atitle_long'] : $person['atitle']) . '">' . $person['atitle'] . '</span>';
            endif;
            $fullname = implode(' ', $name); 
            echo '<h' . $this->atts['hstart'] . '><span>' . $fullname . '</span></h' . $this->atts['hstart'] . '>';
            ?>
            <ul class="person-info">
                <?php if (!empty($person['work'])) : ?>
                    <li class="person-info-position"><span class="screen-reader-text"><?php _e('Job title', 'rrze-univis');?>: </span><strong><span><?php echo $person['work']; ?></span></strong></li>
                <?php endif;?>
                <?php if (!empty($person['organization'])) : ?>
                    <li class="person-info-institution"><span class="screen-reader-text"><?php _e('Organization', 'rrze-univis');?>: </span><span><?php echo $person['organization'];?></span></li>
                <?php endif;?>
                <?php if (!empty($person['department'])) : ?>
                    <li class="person-info-abteilung"><span class="screen-reader-text"><?php _e('Working group', 'rrze-univis');?>: </span><?php echo $person['department']; ?></li>
                <?php endif;?>

                <?php   
                    if (!empty($person['locations'])){
                        // tel
                        foreach($person['locations'] as $location){
                            if (!empty($location['tel']) && in_array('telefon', $this->show) && !in_array('telefon', $this->hide)){
                                if (in_array('call', $this->show) && !in_array('call', $this->hide)) {
                                    echo '<li><span class="screen-reader-text">' . __('Phone number', 'rrze-univis') . ': </span><span><a href="tel:' . $location['tel_call'] . '"> ' . $location['tel'] . '</a></span></li>';
                                } else {
                                    echo '<li><span class="screen-reader-text">' . __('Phone number', 'rrze-univis') . ': </span><span>' . $location['tel'] . '</span></li>';
                                }
                            }
                        }
                        // mobile
                        foreach($person['locations'] as $location){
                            if (!empty($location['mobile']) && in_array('mobile', $this->show) && !in_array('mobile', $this->hide)) {
                                if (in_array('call', $this->show) && !in_array('call', $this->hide)) {
                                    echo '<li><span class="screen-reader-text">' . __('Mobile number', 'rrze-univis') . ': </span><span class="mobile"><a href="tel:' . $location['mobile_call'] . '"> ' . $location['mobile'] . '</a></span></li>';
                                } else {
                                    echo '<li><span class="screen-reader-text">' . __('Mobile number', 'rrze-univis') . ': </span><span class="mobile">' . $location['mobile'] . '</span></li>';
                                }
                            }
                        }
                        // fax
                        foreach($person['locations'] as $location){
                            if (!empty($location['fax']) && in_array('fax', $this->show) && !in_array('fax', $this->hide)){
                                echo '<li><span class="screen-reader-text">' . __('Fax number', 'rrze-univis') . ': </span><span>' . $location['fax'] . '</span></li>';
                            }
                        }
                        // email
                        foreach($person['locations'] as $location){
                            if (!empty($location['email']) && in_array('mail', $this->show) && !in_array('mail', $this->hide)){
                                echo '<li><span class="screen-reader-text">' . __('Email', 'rrze-univis') . ': </span><span><a href="mailto:' . $location['email'] . '">' . $location['email'] . '</a></span></li>';
                            }
                        }
                        // address
                        foreach($person['locations'] as $location){
                            if (!empty($location['url']) && ((in_array('url', $this->show) && !in_array('url', $this->hide)) || ((in_array('address', $this->show) && !in_array('address', $this->hide) && !in_array('url', $this->hide))))){
                                echo '<li><span class="screen-reader-text">' . __('Website', 'rrze-univis') . ': </span><a href="' . $location['url'] . '">' . $location['url'] . '</a></li>';
                            }
                            if (in_array('address', $this->show) && !in_array('address', $this->hide) && (!empty($location['street']) || !empty($location['city']) || !empty($location['office']))){
                                if (!empty($location['street']) || !empty($location['city'])){
                                    echo '<li><span class="screen-reader-text">' . __('Address', 'rrze-univis') . ': <br></span>';
                                    if (!empty($location['street'])){
                                        echo '<div><span class="person-info-street">' . $location['street'] . '</span><br>';
                                    }
                                    if (!empty($location['postalcode'])){
                                        echo '<span>' . $location['postalcode'] . '</span> ';
                                        
                                    }
                                    if (!empty($location['city'])){
                                        echo '<span>' . $location['city'] . '</span>';
                                        
                                    }
                                    echo '</div>';
                                }
                                if (!empty($location['office'])){
                                    echo '<div>' . __('Room', 'rrze-univis') . ' ' .  $location['office'] . '</div>';
                                }
                                if (!empty($location['street']) || !empty($location['city'])){
                                    echo '</li>';
                                }
                            }
                        }

                   		if (!empty($person['officehours'])){
        					echo '<li><span><span class="screen-reader-text">' ;
        					echo _e('Office hours', 'rrze-univis') . ': </span> <span><b>'; 
        					echo _e('Office hours', 'rrze-univis') .':</b><br>' ;
        					foreach ($person['officehours'] as $officehour){
                                $t = [];
        						if (!empty($officehour['repeat'])) { 
                                    $t[] = $officehour['repeat'];
        						}
                                if (!empty($officehour['starttime']) && (int)$officehour['starttime']) {
                                    $tmp = $officehour['starttime'];
                                    if (!empty($officehour['endtime']) && (int)$officehour['endtime']) {
                                        $tmp .= ' ' . __('to', 'rrze-univis') . ' ' . $officehour['endtime'];
                                    }
                                    $t[] = $tmp;
                                }
        						if (!empty($officehour['office'])) { 
        							  $t[] = __('Room', 'rrze-univis') . " " . $officehour['office'];
        							}
        						if (!empty($officehour['comment'])) { 
        							$t[] = $officehour['comment'];
        							}
        						echo implode(', ', $t) . '<br>';
        					}
        					echo '</span></li>'; 
                        }
                    } ?> 
            </ul>
        </div>
    </div>
    <?php if (!empty($person['lectures'])) : ?>
        <div>
        <?php
            echo '<h' . ($this->atts['hstart'] + 1) . ' class="active">' . __('Lehrveranstaltungen', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 1) . '>';
            foreach ($person['lectures'] as $lec_type => $aEvents) :
                ?>
                <div>
                    <ul>
                        <li>
                            <?php echo '<h' . ($this->atts['hstart'] + 2) . '>' . $lec_type . '</h' . ($this->atts['hstart'] + 2) . '>'; ?>
                            <ul>
                            <?php foreach ($aEvents as $event){
                                if ($lang != 'de_DE' && $lang!='de_DE_formal' && !empty($event['ects_name'])){
                                    $event['title'] = $event['ects_name']; 
                                }else{
                                    $event['title'] = $event['lecture_title'];
                                }
                                echo '<li><a href="' . get_permalink() . 'lv_id/' . $event['lecture_univisID'] . '">' . $event['title'] . '</a></li>'; 
                            }
                            ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            <?php endforeach; ?>
    	</div>
    <?php endif; ?>
    <?php 

    if (!empty($person['schema'])){
        echo $person['schema'];
    }

endforeach; 

?>
