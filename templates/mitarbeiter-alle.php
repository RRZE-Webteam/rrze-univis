<div id="univis-personenindex">
    <?php foreach ($data as $position => $persons) : 
        // if ( (empty( $daten['optionen']['zeige_jobs']) || in_array($gruppe['name'], $daten['optionen']['zeige_jobs']) ) && !empty( $gruppe['personen'] ) ) : 
        ?>
    <h4><?php echo $position; ?></h4>
    <ul class="person liste-person" itemscope itemtype="http://schema.org/Person">
        <?php foreach ($persons as $person) : 
            $name = array(); 
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
                    if (!empty($person['phone'])) : 
                        // $phone_number = self::correct_phone_number($location['tel']); 
                        $pers['phone_number'] = '<span class="person-info-phone" itemprop="telephone">Tel. ' . $person['phone'] . '</span>';
                    endif; 
                    if (!empty($person['email'])) : 
                        $pers['email'] = '<span class="person-info-email">E-Mail: <a itemprop="email" href="mailto:' . $person['email'] . '">' . $person['email'] . '</a></span>';                        
                    endif;
                $out = implode(', ', $pers);
                    ?>
                    <span class="person-info" itemprop="name"><?php echo $out;?></span>
            </li>            
        <?php endforeach; ?>
    </ul>
    <?php 
    // endif;
    endforeach; ?>
</div>
