<?php if ($person): ?>
	<div class="person rrze-univis liste-person" itemscope itemtype="http://schema.org/Person">
        <div class="page">
            <?php
            if (!empty($person['title'])):
                $name['title'] = '<span class="honorific-prefix" itemprop="honorificPrefix"><abbr title="' . $person['title_long'] . '">' . $person['title'] . '</abbr></span>';
            endif;
            if (!empty($person['firstname'])):
                $name['firstname'] = '<span class="given-name" itemprop="givenName">' . $person['firstname'] . '</span>';
            endif;
            if (!empty($person['lastname'])):
                $name['lastname'] = '<span class="family-name" itemprop="familyName">' . $person['lastname'] . '</span>';
                if (!empty($person['atitle'])):
                    $name['lastname'] .= ',';
                endif;
            endif;
            if (!empty($person['atitle'])):
                $name['atitle'] = '<span class="honorific-suffix" itemprop="honorificSuffix"><abbr title="' . (!empty($person['atitle_long']) ? $person['atitle_long'] : $person['atitle']) . '">' . $person['atitle'] . '</abbr></span>';
            endif;
            $fullname = implode(' ', $name);
            echo '<h' . $this->atts['hstart'] . '><span itemprop="name">' . $fullname . '</span></h' . $this->atts['hstart'] . '>';
            ?>
            <ul class="person-info">
                <?php if (!empty($person['work'])): ?>
                    <li class="person-info-position"><span class="screen-reader-text"><?php _e('Job title', 'rrze-univis');?>: </span><strong><span itemprop="jobTitle"><?php echo $person['work']; ?></span></strong></li>
                <?php endif;?>
                <?php if (!empty($person['organization'])): ?>
                    <li class="person-info-institution"><span class="screen-reader-text"><?php _e('Organization', 'rrze-univis');?>: </span><span itemprop="worksFor"><?php echo $person['organization']; ?></span></li>
                <?php endif;?>
                <?php if (!empty($person['department'])): ?>
                    <li class="person-info-abteilung"><span class="screen-reader-text"><?php _e('Working group', 'rrze-univis');?>: </span><?php echo $person['department']; ?></li>
                <?php endif;?>

                <?php
            if (!empty($person['locations'])) {
                // tel
                foreach ($person['locations'] as $location) {
                    if (!empty($location['tel']) && in_array('telefon', $this->show) && !in_array('telefon', $this->hide)) {
                        if (in_array('call', $this->show) && !in_array('call', $this->hide)) {
                            echo '<li class="person-info-phone"><span class="screen-reader-text">' . __('Phone number', 'rrze-univis') . ': </span><span itemprop="telephone"><a href="tel:' . $location['tel_call'] . '"> ' . $location['tel'] . '</a></span></li>';
                        } else {
                            echo '<li class="person-info-phone"><span class="screen-reader-text">' . __('Phone number', 'rrze-univis') . ': </span><span itemprop="telephone">' . $location['tel'] . '</span></li>';
                        }
                    }
                }
                // mobile
                foreach ($person['locations'] as $location) {
                    if (!empty($location['mobile']) && in_array('mobile', $this->show) && !in_array('mobile', $this->hide)) {
                        if (in_array('call', $this->show) && !in_array('call', $this->hide)) {
                            echo '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobile number', 'rrze-univis') . ': </span><span class="mobile" itemprop="telephone"><a href="tel:' . $location['mobile_call'] . '"> ' . $location['mobile'] . '</a></span></li>';
                        } else {
                            echo '<li class="person-info-mobile"><span class="screen-reader-text">' . __('Mobile number', 'rrze-univis') . ': </span><span class="mobile" itemprop="telephone">' . $location['mobile'] . '</span></li>';
                        }
                    }
                }
                // fax
                foreach ($person['locations'] as $location) {
                    if (!empty($location['fax']) && in_array('fax', $this->show) && !in_array('fax', $this->hide)) {
                        echo '<li class="person-info-fax"><span class="screen-reader-text">' . __('Fax number', 'rrze-univis') . ': </span><span itemprop="faxNumber">' . $location['fax'] . '</span></li>';
                    }
                }
                // email
                foreach ($person['locations'] as $location) {
                    if (!empty($location['email']) && in_array('mail', $this->show) && !in_array('mail', $this->hide)) {
                        echo '<li class="person-info-email"><span class="screen-reader-text">' . __('Email', 'rrze-univis') . ': </span><span itemprop="email"><a href="mailto:' . $location['email'] . '">' . $location['email'] . '</a></span></li>';
                    }
                }
                // address
                foreach ($person['locations'] as $location) {
                    if (!empty($location['url']) && ((in_array('url', $this->show) && !in_array('url', $this->hide)) || ((in_array('address', $this->show) && !in_array('address', $this->hide) && !in_array('url', $this->hide))))) {
                        echo '<li class="person-info-url"><span class="screen-reader-text">' . __('Website', 'rrze-univis') . ': </span><a itemprop="url" href="' . $location['url'] . '">' . $location['url'] . '</a></li>';
                    }
                    if (in_array('address', $this->show) && !in_array('address', $this->hide) && (!empty($location['street']) || !empty($location['ort']) || !empty($location['office']))) {
                        if (!empty($location['street']) || !empty($location['ort'])) {
                            echo '<li class="person-info-address"><span class="screen-reader-text">' . __('Address', 'rrze-univis') . ': <br></span>';
                            if (!empty($location['street'])) {
                                echo '<div itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress"><span class="person-info-street" itemprop="streetAddress">' . $location['street'] . '</span><br>';
                            }
                            if (!empty($location['ort'])) {
                                echo '<span itemprop="addressLocality">' . $location['ort'] . '</span>';

                            }
                            echo '</div>';
                        }
                        if (!empty($location['office']) && !in_array('office', $this->hide)) {
                            echo '<div itemprop="workLocation" itemscope="" itemtype="http://schema.org/Person">' . __('Room', 'rrze-univis') . ' ' . $location['office'] . '</div>';
                        }
                        if (!empty($location['street']) || !empty($location['ort'])) {
                            echo '</li>';
                        }
                    }
                    if (in_array('office', $this->show) && !in_array('office', $this->hide) && !in_array('address', $this->show) && (!empty($location['office']))) {
                        echo '<div itemprop="workLocation" itemscope="" itemtype="http://schema.org/Person">' . __('Room', 'rrze-univis') . ' ' . $location['office'] . '</div>';
                    }
                }

                if (!empty($person['officehours'])) {
                    echo '<li class="person-info-officehours"><span itemprop="hoursAvailable" itemtype="http://schema.org/ContactPoint"><span class="screen-reader-text">';
                    echo _e('Office hours', 'rrze-univis') . ': </span> <span><b>';
                    echo _e('Office hours', 'rrze-univis') . ':</b><br>';
                    foreach ($person['officehours'] as $officehour) {

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
                            echo ", ";
                            echo _e('Room', 'rrze-univis') . " " . $officehour['office'];
                        }
                        if (!empty($officehour['comment'])) {
                            if (!empty($officehour['repeat']) || !empty($officehour['starttime'])) {
                                echo ", ";
                            }
                            echo $officehour['comment'];
                        }
                        echo "<br>";
                    }
                    echo " </span></li>";
                }
            }?>
            </ul>
        </div>
    </div>
    <?php if (!empty($person['lectures'])): ?>
        <div class="rrze-univis">
        <?php
echo '<h' . ($this->atts['hstart'] + 1) . ' class="active">' . __('Lectures', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 1) . '>';
foreach ($person['lectures'] as $lec_type => $lectures):
?>
                    <ul>
                        <li>
                            <?php echo '<h' . ($this->atts['hstart'] + 2) . '>' . $lec_type . '</h' . ($this->atts['hstart'] + 2) . '>'; ?>
                            <ul>
                            <?php foreach ($lectures as $lecture) {
    echo '<li><a href="' . get_permalink() . 'lv_id/' . $lecture['lecture_id'] . '">' . $lecture['name'] . '</a></li>';
}
?>
                            </ul>
                        </li>
                    </ul>

            <?php endforeach;?>
    	</div>
    <?php endif;?>
<?php endif;?>
