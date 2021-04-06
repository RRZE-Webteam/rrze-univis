<!-- 2DO: check ects_infos, literature, organization -->
<?php if ($veranstaltung) : ?>
    <h2><?php echo $veranstaltung['name']; ?></h2>

    <?php if (!empty($veranstaltung['lecturers'])) : ?>
        <h3><?php _e('Lecturers', 'rrze-univis');?></h3>
        <ul>
        <?php
        foreach ($veranstaltung['lecturers'] as $doz) :
            $name = array();
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
            if (!empty($doz['person_id'])):
                $url = '<a href="' . get_permalink() . 'univisid/' . $doz['person_id'] . '">' . $fullname . '</a>';
            else:
                $url = $fullname;
            endif;?>
            <li itemprop="name" itemscope itemtype="http://schema.org/Person"><?php echo $url; ?></li>
            <?php
        endforeach; ?>
        </ul>
    <?php endif; ?>

    <h3><?php _e('Details', 'rrze-univis');?></h3>

    <?php if (!empty($veranstaltung['angaben'])): ?>
        <p><?php echo $veranstaltung['angaben']; ?></p>
    <?php endif; ?>

    <h4><?php _e('Time and place', 'rrze-univis');?>:</h4>
            <?php if (array_key_exists('comment', $veranstaltung)) : ?>
            <p><?php echo $veranstaltung['comment']; ?></p>
            <?php endif; ?>
    <ul>
        <?php if (isset($veranstaltung['courses'])) :
            foreach ($veranstaltung['courses'] as $course):
                foreach ($course['term'] as $term):
                    $t = array();
                    $time = array();
                    if (!empty($term['repeat'])) :
                        $t['repeat'] = $term['repeat'];
                    endif;
                    if (!empty($term['startdate'])) :
                        if (!empty($term['enddate']) && $term['startdate'] != $term['enddate']):
                            $t['date'] = date("d.m.Y", strtotime($term['startdate'])) . '-' . date("d.m.Y", strtotime($term['enddate']));
                        else:
                            $t['date'] = date("d.m.Y", strtotime($term['startdate']));
                        endif;
                    endif;
                    if (!empty($term['starttime'])) :
                        $time['starttime'] = $term['starttime'];
                    endif;
                    if (!empty($term['endtime'])) :
                        $time['endtime'] = $term['endtime'];
                    endif;
                    if (!empty($time)) :
                        $t['time'] = $time['starttime'] . '-' . $time['endtime'] . ',';
                    else:
                        $t['time'] = __('Time on appointment', 'rrze-univis') . ',';
                    endif;
                    if (!empty($term['room'])) :
                        $t['room'] = __('Room', 'rrze-univis') . ' ' . $term['room'];
                    endif;
                    if (!empty($term['exclude'])) :
                        $t['exclude'] = '(' . __('exclude', 'rrze-univis') . ' ' . $term['exclude'] . ')';
                    endif;
                    // Kursname
                    if (!empty($course['coursename'])) :
                        $t['coursename'] = '(' . __('Course', 'rrze-univis') . ' ' . $course['coursename'] . ')';
                    endif;
                    $term_formatted = implode(' ', $t);
                    ?>
                    <li><?php echo $term_formatted; ?></li>
            <?php endforeach;
            endforeach;
        else : ?>
            <li><?php _e('Time and place on appointment', 'rrze-univis'); ?></li>
        <?php endif; ?>
    </ul>


    <?php if (array_key_exists('studs', $veranstaltung) && array_key_exists('stud', $veranstaltung['studs'][0])) : ?>
    <h4><?php _e('Fields of study', 'rrze-univis');?></h4>
    <ul>
        <?php
        foreach ($veranstaltung['studs'][0]['stud'] as $stud) :
            $s = array();
            if (!empty($stud['pflicht'])) :
                $s['pflicht'] = $stud['pflicht'];
            endif;
            if (!empty($stud['richt'])) :
                $s['richt'] = $stud['richt'];
            endif;
            if (!empty($stud['sem'][0]) && absint($stud['sem'][0])) :
                $s['sem'] = sprintf('%s %d', __('from SEM', 'rrze-univis'), absint($stud['sem'][0]));
            endif;
            $studinfo = implode(' ', $s);
            ?>
            <li><?php echo $studinfo; ?></li>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>


    <?php if (!empty($veranstaltung['organizational'])) : ?>
        <h4><?php _e('Prerequisites / Organizational information', 'rrze-univis');?></h4>
        <p><?php echo $veranstaltung['organizational']; ?></p>
        <?php endif;
    ?>


    <?php if (!empty($veranstaltung['summary'])) : ?>
        <h4><?php _e('Content', 'rrze-univis');?></h4>
        <p><?php echo $veranstaltung['summary']; ?></p>
    <?php endif; ?>


    <?php if (!empty($veranstaltung['literature'])) : ?>
        <h4><?php _e('Recommended Literature', 'rrze-univis');?></h4>
        <p><?php echo $veranstaltung['literature']; ?></p>
    <?php endif; ?>

    <?php if (!empty($veranstaltung['ects_infos'])) : ?>
        <h4><?php _e('ECTS information', 'rrze-univis');?></h4>
        <?php if (!empty($veranstaltung['ects_name'])) : ?>
            <h5><?php _e('Title', 'rrze-univis');?></h5>
            <p><?php echo $veranstaltung['ects_name']; ?></p>
        <?php endif; ?>
        <?php if (!empty($veranstaltung['ects_cred'])) : ?>
            <h5><?php _e('Credits', 'rrze-univis');?></h5>
            <p><?php echo $veranstaltung['ects_cred']; ?></p>
        <?php endif; ?>
        <?php if (!empty($veranstaltung['ects_summary'])) : ?>
            <h5><?php _e('Content', 'rrze-univis');?>:</h5>
            <p><?php echo $veranstaltung['ects_summary']; ?></p>
        <?php endif; ?>
        <?php if (!empty($veranstaltung['ects_literature'])) : ?>
            <h5><?php _e('Literature', 'rrze-univis');?>:</h5>
            <p><?php echo $veranstaltung['ects_literature']; ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ((!empty($veranstaltung['keywords'])) || (!empty($veranstaltung['maxturnout'])) || (!empty($veranstaltung['url_description']))) : ?>
        <h4><?php _e('Additional information', 'rrze-univis');?></h4>
        <?php if (!empty($veranstaltung['keywords'])) : ?>
            <p><?php _e('Keywords', 'rrze-univis');?>: <?php echo $veranstaltung['keywords']; ?></p>
        <?php endif; ?>
        <?php if (!empty($veranstaltung['maxturnout'])) : ?>
            <p><?php _e('Expected participants', 'rrze-univis');?>: <?php echo $veranstaltung['maxturnout']; ?></p>
        <?php endif; ?>
        <?php if (!empty($veranstaltung['url_description'])) : ?>
            <p>www: <a href="<?php echo $veranstaltung['url_description']; ?>"><?php echo $veranstaltung['url_description']; ?></a></p>
        <?php endif; ?>
    <?php endif;
endif;
