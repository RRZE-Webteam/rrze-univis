<div id="univis-personenindex">
    <p class="groupindex">
        <?php if ($daten['optionen']['zeige_sprungmarken']) : ?>
        <?php foreach ($daten['gruppen'] as $v) : ?>
        <a href="#<?php echo $v['name'];?>"><?php echo $v['name'];?></a>&nbsp;
        <?php endforeach; ?>
        <?php endif; ?>
    </p>

    <?php foreach ($daten['gruppen'] as $gruppe) : ?>
    <h2><a name="<?php echo $gruppe['name'];?>"><?php echo $gruppe['name'];?></a></h2>
    <ul>
    <?php foreach ($gruppe['personen'] as $person) : 
            $p = array();
            $pers = array();
            $fullname = '';
            $out = '';
            ?>
        <li itemprop="name" class="person liste-person" itemscope itemtype="http://schema.org/Person">
                   <?php if (!empty($person['lastname'])) :                    
                        $p['lastname'] = '<span class="family-name" itemprop="familyName">' . $person['lastname'] . '</span>';
                            if (!empty($person['firstname'])) : 
                                $p['lastname'] .= ',';
                            endif; 
                        endif;
                        if (!empty($person['firstname'])) : 
                            $p['firstname'] = '<span class="given-name" itemprop="givenName">' . $person['firstname'] . '</span>';
                        endif;
                        if(!empty($p)) :
                        $n = implode(' ', $p);
                        if (!empty($person['id'])) :
                            $url = get_permalink() . 'univisid/' . $person['id'];
                            $fullname .= '<a class="url" href="' . $url . '" itemprop="name">' . $n . '</a>';
                        else: 
                            $fullname .= $n;
                        endif;  
                    endif;
                        $pers['fullname'] = $fullname; 
                        if (!empty( $person['locations'][0]['location'][0] ) ) :
                            $location = $person['locations'][0]['location'][0]; 
                            if (!empty($location['tel']) && (!empty( $daten['optionen']['telefon']))) : 
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
                    <span itemprop="name" class="person liste-person" itemscope itemtype="http://schema.org/Person"><?php echo $out;?></span>

        </li>
        <?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
</div>
