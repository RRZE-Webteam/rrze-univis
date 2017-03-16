<?php if ($daten['veranstaltung']) :
    $veranstaltung = $daten['veranstaltung'];
    ?>
    <h2><?php echo $veranstaltung['name']; ?></h2>

    <?php if (array_key_exists('dozs', $veranstaltung) && array_key_exists('doz', $veranstaltung['dozs'][0])) : ?>
        <h3><?php __e('Dozent/in', RRZE_UnivIS::textdomain);?></h3> 
        <?php
        foreach ($veranstaltung['dozs'][0]['doz'] as $doz) :
            if (!empty($doz['title'])) :
                $name['title'] = '<span itemprop="honorificPrefix">' . $doz['title'] . '</span>';
            endif;
            if (!empty($doz['firstname'])) :
                $name['firstname'] = '<span itemprop="givenName">' . $doz['firstname'] . '</span>';
            endif;
            if (!empty($doz['lastname'])) :
                $name['lastname'] = '<span itemprop="familyName">' . $doz['lastname'] . '</span>';
            endif;
            $fullname = implode(' ', $name);
            ?>

            <h6 itemprop="name" itemscope itemtype="http://schema.org/Person"><a href="http://univis.uni-erlangen.de/prg?search=persons&id=<?php echo $doz['id']; ?>&show=info"><?php echo $fullname; ?></a></h6>
            <?php
        endforeach;
    endif;
    ?>

    <h3><?php __e('Angaben', RRZE_UnivIS::textdomain);?></h3>   
    
    <?php if (!empty($veranstaltung['angaben'])): ?>               
        <p><?php echo $veranstaltung['angaben']; ?></p>
    <?php endif; ?>

    <h4><?php __e('Zeit und Ort', RRZE_UnivIS::textdomain);?>:</h4>        
    <ul>
        <?php
        if (array_key_exists('terms', $veranstaltung) && array_key_exists('term', $veranstaltung['terms'][0])) {
            //if (!empty(univisController::get_key($veranstaltung, 'terms', 0)) && !empty(univisController::get_key($veranstaltung['terms'], 'term', 0))) {
            foreach ($veranstaltung['terms'][0]['term'] as $term) :
                if(!empty($term['date'])) :
                    $t['date'] = $term['date'];
                endif;        
                if(!empty($term['starttime'])) :
                    $time['starttime'] = $term['starttime'];
                endif;  
                if(!empty($term['endtime'])) :
                    $time['endtime'] = $term['endtime'];
                endif; 
                if(!empty($time)) :
                    $t['time'] = $time['starttime'] . '-' . $time['endtime'] . ' ' . __('Uhr', RRZE_UnivIS::textdomain);
                else:
                    $t['time'] = __('Zeit n.V.', RRZE_UnivIS::textdomain);
                endif;              
                if(!empty($term['room_short'])) :
                    if(!empty($t['time'])) :
                        $t['time'] .= ',';
                    elseif(!empty($t['date'])) :
                        $t['date'] .= ',';
                    endif;
                    $t['room_short'] = $term['room_short'];
                endif;          
                if(!empty($term['exclude'])) :
                    $t['exclude'] = '(' . __('außer', RRZE_UnivIS::textdomain) . ' ' . $term['exclude'] . ')';
                endif;
                $term_formatted = implode(' ', $t);?>
                <li><?php echo $term_formatted; ?></li>
            <?php endforeach;
        } else {
            ?>
            <li><?php _e('Zeit/Ort n.V.', RRZE_UnivIS::textdomain); ?></li>
    <?php } ?>
    </ul>

        <?php if (array_key_exists('studs', $veranstaltung) && array_key_exists('stud', $veranstaltung['studs'][0])) : ?>
        <h4><?php __e('Studienfächer / Studienrichtungen', RRZE_UnivIS::textdomain);?></h4>                 
        <ul>  
            <?php
            foreach ($veranstaltung['studs'][0]['stud'] as $stud) :
                if (!empty($stud['pflicht'])) :
                    $s['pflicht'] = $stud['pflicht'];
                endif;
                if (!empty($stud['richt'])) :
                    $s['richt'] = $stud['richt'];
                endif;
                if (!empty($stud['sem'])) :
                    $s['sem'] = $stud['sem'];
                endif;
                if (!empty($stud['credits'])) :
                    $s['credits'] = '(ECTS-Credits: ' . $stud['credits'] . ')';
                endif;
                $studinfo = implode(' ', $s);
                ?>
                <li><?php echo $studinfo; ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?> 


    <?php if (!empty($veranstaltung['organizational'])) : ?>
        <h4><?php __e('Voraussetzungen / Organisatorisches', RRZE_UnivIS::textdomain);?></h4>                
        <p><?php echo $veranstaltung['organizational']; ?></p>
        <?php endif;
    ?>


    <?php if (!empty($veranstaltung['summary'])) : ?>
        <h4><?php __e('Inhalt', RRZE_UnivIS::textdomain);?></h4>              
        <p><?php echo $veranstaltung['summary']; ?></p>
        <?php endif;
    ?>




    <?php if (!empty($veranstaltung['ects_infos'])) : ?>
        <h4><?php __e('ECTS-Informationen', RRZE_UnivIS::textdomain);?></h4>              
        <?php if (!empty($veranstaltung['ects_name'])) : ?>
            <h5><?php __e('Title', RRZE_UnivIS::textdomain);?>:</h5>
            <p><?php echo $veranstaltung['ects_name']; ?></p>          
        <?php endif; ?>                
        <?php if (!empty($veranstaltung['ects_content'])) : ?>
            <h5><?php __e('Content', RRZE_UnivIS::textdomain);?>:</h5>
            <p><?php echo $veranstaltung['ects_summary']; ?></p>  
        <?php endif; ?>
        <?php if (!empty($veranstaltung['ects_literature'])) : ?>
            <h5><?php __e('Literature', RRZE_UnivIS::textdomain);?>:</h5>
            <p><?php echo $veranstaltung['ects_literature']; ?></p>  
        <?php endif; ?>
        <?php endif; ?>



        <?php if (!empty($veranstaltung['zusatzinfos'])) : ?>
        <h4><?php __e('Zusätzliche Informationen', RRZE_UnivIS::textdomain);?></h4>  
        <p>
            <?php if (!empty($veranstaltung['keywords'])) : ?>
                <?php __e('Schlagwörter', RRZE_UnivIS::textdomain);?>: <?php echo $veranstaltung['keywords']; ?><br>          
            <?php endif; ?>                
        <?php if (!empty($veranstaltung['turnout'])) : ?>
                <?php __e('Erwartete Teilnehmerzahl', RRZE_UnivIS::textdomain);?>: <?php echo $veranstaltung['turnout']; ?><br>  
        <?php endif; ?>
        <?php if (!empty($veranstaltung['url_description'])) : ?>
                www: <a href="<?php echo $veranstaltung['url_description']; ?>"><?php echo $veranstaltung['url_description']; ?></a> <br>
        <?php endif; ?>
        </p> 
    <?php endif;
endif;
?>

<?php if ($daten['assets']) :
    if (!empty($daten['assets']['download_link'])) :
        ?>
        <a href="<?php echo $daten['assets']['download_link']; ?>"> Download </a>                
    <?php endif;
endif;
?>
