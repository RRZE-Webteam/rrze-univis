<?php if ($daten['veranstaltung']) :
    $veranstaltung = $daten['veranstaltung'];
    ?>
    <h2><?php echo $veranstaltung['name']; ?></h2>

    <?php if (array_key_exists('dozs', $veranstaltung) && array_key_exists('doz', $veranstaltung['dozs'][0])) : ?>
        <h3><?php _e('Dozent/in', RRZE_UnivIS::textdomain);?></h3> 
        <ul>
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
            <?php $url = get_permalink() . 'univisid/' . $doz['id']; ?>
            <li itemprop="name" itemscope itemtype="http://schema.org/Person"><a href="<?php echo $url; ?>"><?php echo $fullname; ?></a></li>
            <?php
        endforeach; ?>
        </ul>    
    <?php endif; ?>

    <h3><?php _e('Angaben', RRZE_UnivIS::textdomain);?></h3>   
    
    <?php if (!empty($veranstaltung['angaben'])): ?>               
        <p><?php echo $veranstaltung['angaben']; ?></p>
    <?php endif; ?>

    <h4><?php _e('Zeit und Ort', RRZE_UnivIS::textdomain);?>:</h4>        
            <?php if(array_key_exists('comment', $veranstaltung)) : ?>
            <p><?php echo $veranstaltung['comment']; ?></p>
            <?php endif; ?>
    <ul>
        <?php if(array_key_exists('course_terms', $veranstaltung)) :
            foreach ($veranstaltung['course_terms'] as $course_terms):
                if (!empty($course_terms['date'])) :
                    $t['date'] = $course_terms['date'];
                endif;
                if (!empty($course_terms['starttime'])) :
                    $time['starttime'] = $course_terms['starttime'];
                endif;
                if (!empty($course_terms['endtime'])) :
                    $time['endtime'] = $course_terms['endtime'];
                endif;
                if (!empty($time)) :
                    $t['time'] = $time['starttime'] . '-' . $time['endtime'];
                else:
                    $t['time'] = __('Zeit n.V.', RRZE_UnivIS::textdomain);
                endif;
                if (!empty($course_terms['room_short'])) :
                    if (!empty($t['time'])) :
                        $t['time'] .= ',';
                    elseif (!empty($t['date'])) :
                        $t['date'] .= ',';
                    endif;
                    $t['room_short'] = __('Raum', RRZE_UnivIS::textdomain) . ' ' . $course_terms['room_short'];
                endif;
                if (!empty($course_terms['exclude'])) :
                    $t['exclude'] = '(' . __('außer', RRZE_UnivIS::textdomain) . ' ' . $course_terms['exclude'] . ')';
                endif;
                // Kursname
                if (!empty($course_terms['coursename'])) :
                    $t['coursename'] = '(' . __('Kurs', RRZE_UnivIS::textdomain) . ' ' . $course_terms['coursename'] . ')';
                endif;
                $term_formatted = implode(' ', $t);
                ?>    
                <li><?php echo $term_formatted; ?></li>
            <?php endforeach;
        elseif (array_key_exists('terms', $veranstaltung) && array_key_exists('term', $veranstaltung['terms'][0])) :                  
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
                    $t['time'] = $time['starttime'] . '-' . $time['endtime'];
                else:
                    $t['time'] = __('Zeit n.V.', RRZE_UnivIS::textdomain);
                endif;              
                if(!empty($term['room_short'])) :
                    if(!empty($t['time'])) :
                        $t['time'] .= ',';
                    elseif(!empty($t['date'])) :
                        $t['date'] .= ',';
                    endif;
                    $t['room_short'] = __('Raum', RRZE_UnivIS::textdomain) . ' ' . $term['room_short'];
                endif;          
                if(!empty($term['exclude'])) :
                    $t['exclude'] = '(' . __('außer', RRZE_UnivIS::textdomain) . ' ' . $term['exclude'] . ')';
                endif;
                $term_formatted = implode(' ', $t);?>
                <li><?php echo $term_formatted; ?></li>
            <?php endforeach;
        else : ?>
            <li><?php _e('Zeit/Ort n.V.', RRZE_UnivIS::textdomain); ?></li>
        <?php endif; ?>
    </ul>

    
    <?php if (array_key_exists('studs', $veranstaltung) && array_key_exists('stud', $veranstaltung['studs'][0])) : ?>
    <h4><?php _e('Studienfächer / Studienrichtungen', RRZE_UnivIS::textdomain);?></h4>                 
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
        <h4><?php _e('Voraussetzungen / Organisatorisches', RRZE_UnivIS::textdomain);?></h4>                
        <p><?php echo $veranstaltung['organizational']; ?></p>
        <?php endif;
    ?>


    <?php if (!empty($veranstaltung['summary'])) : ?>
        <h4><?php _e('Inhalt', RRZE_UnivIS::textdomain);?></h4>              
        <p><?php echo $veranstaltung['summary']; ?></p>
        <?php endif;
    ?>




    <?php if (!empty($veranstaltung['ects_infos'])) : ?>
        <h4><?php _e('ECTS-Informationen', RRZE_UnivIS::textdomain);?></h4>              
        <?php if (!empty($veranstaltung['ects_name'])) : ?>
            <h5><?php _e('Title', RRZE_UnivIS::textdomain);?>:</h5>
            <p><?php echo $veranstaltung['ects_name']; ?></p>          
        <?php endif; ?>                
        <?php if (!empty($veranstaltung['ects_content'])) : ?>
            <h5><?php _e('Content', RRZE_UnivIS::textdomain);?>:</h5>
            <p><?php echo $veranstaltung['ects_summary']; ?></p>  
        <?php endif; ?>
        <?php if (!empty($veranstaltung['ects_literature'])) : ?>
            <h5><?php _e('Literature', RRZE_UnivIS::textdomain);?>:</h5>
            <p><?php echo $veranstaltung['ects_literature']; ?></p>  
        <?php endif; ?>
        <?php endif; ?>



        <?php if (!empty($veranstaltung['zusatzinfos'])) : ?>
        <h4><?php _e('Zusätzliche Informationen', RRZE_UnivIS::textdomain);?></h4>  
        <p>
            <?php if (!empty($veranstaltung['keywords'])) : ?>
                <?php _e('Schlagwörter', RRZE_UnivIS::textdomain);?>: <?php echo $veranstaltung['keywords']; ?><br>          
            <?php endif; ?>                
        <?php if (!empty($veranstaltung['turnout'])) : ?>
                <?php _e('Erwartete Teilnehmerzahl', RRZE_UnivIS::textdomain);?>: <?php echo $veranstaltung['turnout']; ?><br>  
        <?php endif; ?>
        <?php if (!empty($veranstaltung['url_description'])) : ?>
                www: <a href="<?php echo $veranstaltung['url_description']; ?>"><?php echo $veranstaltung['url_description']; ?></a> <br>
        <?php endif; ?>
        </p> 
    <?php endif;
endif;
?>

