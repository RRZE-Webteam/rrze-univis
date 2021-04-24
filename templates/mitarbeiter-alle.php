<?php if ($data) : ?>
<div id="univis-personenindex" class="rrze-univis">
    <?php foreach ($data as $position => $persons) : ?>
    <h4><?php echo $position; ?></h4>
    <ul class="person liste-person" itemscope itemtype="http://schema.org/Person">
        <?php foreach ($persons as $person) : 
            $name = $p = $pers = [];
            $lastname = $firstname = $fullname = $out = '';
        ?>
        <li>                
        <?php 
            if (!empty($person['title'])) : 
                $name['title'] = '<span itemprop="honorificPrefix"><acronym title="' . $person['title_long'] . '">' . $person['title'] . '</acronym></span>';
            endif; 
            if (!empty($person['lastname'])) :
                $p['lastname'] = '<span itemprop="familyName">' . $person['lastname'] . '</span>';
                if (!empty($person['firstname'])) : 
                    $p['lastname'] .= ',';
                endif;
            endif;
            if (!empty($person['firstname'])) :
                $p['firstname'] = '<span itemprop="givenName">' . $person['firstname'] . '</span>';
            endif;
            if(!empty($p)) :
                $n = implode(' ', $p);
                if (!empty($person['person_id'])) :
                    $url = get_permalink() . 'univisid/' . $person['person_id'];
                    $fullname .= '<a class="url" href="' . $url . '" itemprop="name">';
                endif; 
                $fullname .= $n;
                if (!empty($person['person_id'])) :
                    $fullname .= '</a>';
                endif;  
                $name['fullname'] = $fullname;
            endif;
            $pers['fullname'] = implode(' ', $name);
            if (!empty($person['atitle'])) :
                $pers['atitle'] = '<span itemprop="honorificSuffix"><acronym title="' . $person['atitle'] . '">' . $person['atitle'] . '</acronym></span>';                      
            endif;

            if (!empty($person['locations'])){
                // tel
                foreach($person['locations'] as $location){
                    if (!empty($location['tel']) && in_array('telefon', $this->show) && !in_array('telefon', $this->hide)){
                        if (in_array('call', $this->show) && !in_array('call', $this->hide)) {
                            $pers[] = '<span class="screen-reader-text">' . __('Phone number', 'rrze-univis') . ': </span><span itemprop="telephone"><a href="tel:' . $location['tel_call'] . '"> ' . $location['tel'] . '</a></span>';
                        } else {
                            $pers[] = '<span class="screen-reader-text">' . __('Phone number', 'rrze-univis') . ': </span><span itemprop="telephone">' . $location['tel'] . '</span>';
                        }
                    }
                }
                // mobile
                foreach($person['locations'] as $location){
                    if (!empty($location['mobile']) && in_array('mobile', $this->show) && !in_array('mobile', $this->hide)) {
                        if (in_array('call', $this->show) && !in_array('call', $this->hide)) {
                            $pers[] = '<span class="screen-reader-text">' . __('Mobile number', 'rrze-univis') . ': </span><span class="mobile" itemprop="telephone"><a href="tel:' . $location['mobile_call'] . '"> ' . $location['mobile'] . '</a></span>';
                        } else {
                            $pers[] = '<span class="screen-reader-text">' . __('Mobile number', 'rrze-univis') . ': </span><span class="mobile" itemprop="telephone">' . $location['mobile'] . '</span>';
                        }
                    }
                }
                // fax
                foreach($person['locations'] as $location){
                    if (!empty($location['fax']) && in_array('fax', $this->show) && !in_array('fax', $this->hide)){
                        $pers[] = '<span class="screen-reader-text">' . __('Fax number', 'rrze-univis') . ': </span><span itemprop="faxNumber">' . $location['fax'] . '</span>';
                    }
                }
                // email
                foreach($person['locations'] as $location){
                    if (!empty($location['email']) && in_array('mail', $this->show) && !in_array('mail', $this->hide)){
                        $pers[] = '<span class="screen-reader-text">' . __('Email', 'rrze-univis') . ': </span><span itemprop="email">' . $location['email'] . '</span>';
                    }
                }
                // address
                foreach($person['locations'] as $location){
                    if (!empty($location['url']) && ((in_array('url', $this->show) && !in_array('url', $this->hide)) || ((in_array('address', $this->show) && !in_array('address', $this->hide) && !in_array('url', $this->hide))))){
                        $pers[] = '<span class="screen-reader-text">' . __('Website', 'rrze-univis') . ': </span><a itemprop="url" href="' . $location['url'] . '">' . $location['url'] . '</a>';
                    }
                    if (in_array('address', $this->show) && !in_array('address', $this->hide) && (!empty($location['street']) || !empty($location['ort']) || !empty($location['office']))){
                        if (!empty($location['street']) || !empty($location['ort'])){
                            $pers[] = '<span class="screen-reader-text">' . __('Address', 'rrze-univis') . ': <br></span>';
                            if (!empty($location['street'])){
                                $pers[] = '<div itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress"><span class="person-info-street" itemprop="streetAddress">' . $location['street'] . '</span><br>';
                            }
                            if (!empty($location['ort'])){
                                $pers[] = '<span itemprop="addressLocality">' . $location['ort'] . '</span>';
                                
                            }
                            $pers[] = '</div>';
                        }
                        if (!empty($location['office'])){
                            $pers[] = '<div class="person-info-room" itemprop="workLocation" itemscope="" itemtype="http://schema.org/Person">' . __('Room', 'rrze-univis') . ' ' .  $location['office'] . '</div>';
                        }
                    }
                }
            }
            $out = implode(', ', $pers);
        ?>
        <span class="person-info" itemprop="name"><?php echo $out;?></span>
        </li>            
        <?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
</div>
<?php endif; ?>
