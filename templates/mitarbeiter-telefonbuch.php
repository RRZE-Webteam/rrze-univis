<div id="univis-personenindex">
<p class="groupindex">
        <?php if (in_array('sprungmarken', $show)) : ?>
        <?php foreach (array_keys($data) as $v) : ?>
        <a href="#<?php echo $v;?>"><?php echo $v;?></a>&nbsp;
        <?php endforeach; ?>
        <?php endif; ?>
    </p>
    <?php foreach ($data as $department => $persons) : ?>
    <h2><a name="<?php echo $department;?>"><?php echo $department;?></a></h2>
    <ul class="person liste-person" itemscope itemtype="http://schema.org/Person">
        <?php foreach ($persons as $person) :
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
                    if (!empty($person['lastname'])) :
                        $p['lastname'] = '<span itemprop="familyName">' . $person['lastname'] . '</span>, ';
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
                    $pers['fullname'] = implode(', ', $name);
                    if (!empty($person['phone']) && in_array('telefon', $show)) : 
                        $pers['phone_number'] = '<span class="person-info-phone" itemprop="telephone">Tel. ' . $person['phone'] . '</span>';
                    endif; 
                    if (!empty($person['email']) && in_array('mail', $show)) : 
                        $pers['email'] = '<span class="person-info-email">E-Mail: <a itemprop="email" href="mailto:' . $person['email'] . '">' . $person['email'] . '</a></span>';                        
                    endif;
                
                    $out = implode(', ', $pers);
                    ?>
            <span class="person-info" itemprop="name"><?php echo $out;?></span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
</div>
