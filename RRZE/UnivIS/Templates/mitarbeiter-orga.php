<div id="univis-personenindex">
    <ul class="groupindex">
        <?php if ($daten['optionen']['zeige_sprungmarken']) : // wird nur angezeigt, wenn es mehr als eine OrgUnit gibt ?>
        <?php foreach ($daten['gruppen'] as $v) : ?>
        <li><a href="#<?php echo $v['name'];?>"><?php echo $v['name'];?></a></li>
        <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <?php foreach ($daten['gruppen'] as $gruppe) : ?>
    <h2><a name="<?php echo $gruppe['name'];?>"><?php echo $gruppe['name'];?></a></h2>
    <ul class="person liste-person" itemscope itemtype="http://schema.org/Person">
        <?php foreach ($gruppe['personen'] as $person) :
            $name= array();
            $p = array();
            $pers = array();
            $lastname = '';
            $firstname = '';
            $fullname = '';
            $out = '';
            ?>
        <li>
                <?php 
                    if (!empty($person['title'])) : 
                        $name['title'] = '<span itemprop="honorificPrefix"><acronym title="' . $person['title_long'] . '">' . $person['title'] . '</acronym></span>';
                    endif; 
                    if (!empty($person['firstname'])) :
                        $p['firstname'] = '<span itemprop="givenName">' . $person['firstname'] . '</span>';
                        if (!empty($person['lastname'])) : 
                            $p['firstname'] .= ' ';
                        endif;
                    endif;
                    if (!empty($person['lastname'])) :
                        $p['lastname'] = '<span itemprop="familyName">' . $person['lastname'] . '</span>';
                    endif;
                    if(!empty($p)) :
                        $n = implode(' ', $p);
                        if (!empty($person['id'])) :
                            $url = get_permalink() . 'univisid/' . $person['id'];
                            $fullname .= '<a class="url" href="' . $url . '" itemprop="name">';
                        endif; 
                        $fullname .= $n;
                        if (!empty($person['id'])) :
                            $fullname .= '</a>';
                        endif;  
                        $name['fullname'] = $fullname;
                    endif;
                    $pers['fullname'] = implode(' ', $name);
                    if (!empty($person['atitle'])) :
                        $pers['atitle'] = '<span itemprop="honorificSuffix"><acronym title="' . $person['atitle_long'] . '">' . $person['atitle'] . '</acronym></span>';                      
                    endif;
                    if (!empty( $person['locations'][0]['location'][0] ) ) :
                    $location = $person['locations'][0]['location'][0]; 
                        if (!empty($location['tel'])  && (!empty( $daten['optionen']['telefon']))) : 
                            $phone_number = self::correct_phone_number($location['tel']); 
                            $pers['phone_number'] = '<span class="person-info-phone" itemprop="telephone">Tel. ' . $phone_number . '</span>';
                        endif; 
                        if (!empty($location['email']) && (!empty( $daten['optionen']['mail']))) : 
                            $email = $location['email'];
                            $pers['email'] = '<span class="person-info-email">E-Mail: <a itemprop="email" href="mailto:' . strtolower($email) . '">' . strtolower($email) . '</a></span>';                        
                        endif;
                    endif;
                    
                    $out = implode(', ', $pers);
                    ?>
            <span class="person-info" itemprop="name"><?php echo $out;?></span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
</div>
