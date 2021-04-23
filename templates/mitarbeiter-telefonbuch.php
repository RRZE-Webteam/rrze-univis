<?php if ($data) : ?>
<div id="univis-personenindex" class="rrze-univis">
<p class="groupindex">
        <?php if (in_array('sprungmarken', $this->show) && !in_array('sprungmarken', $this->hide)) : ?>
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
            if (!empty($person['locations'])){
                foreach($person['locations'] as $location){
                    if (!empty($location['tel']) && in_array('telefon', $this->show) && !in_array('telefon', $this->hide)){
                        $pers[] = '<span class="person-info-phone" itemprop="telephone">Tel. ' . $location['tel'] . '</span>';
                    }
                    if (!empty($location['email']) && in_array('mail', $this->show) && !in_array('mail', $this->hide)){
                        $pers[] = '<span class="person-info-email">E-Mail: <a itemprop="email" href="mailto:' . $location['email'] . '">' . $location['email'] . '</a></span>';                        
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
