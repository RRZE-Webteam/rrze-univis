<div class="rrze-univis">
<?php if ($veranstaltung) : 
    $lang = get_locale();
    $options = get_option('rrze-univis');
    $ssstart = (!empty($options['basic_ssStart']) ? $options['basic_ssStart'] : 0);
    $ssend = (!empty($options['basic_ssEnd']) ? $options['basic_ssEnd'] : 0);
    $wsstart = (!empty($options['basic_wsStart']) ? $options['basic_wsStart'] : 0);
    $wsend = (!empty($options['basic_wsEnd']) ? $options['basic_wsEnd'] : 0);

    echo '<h' . $this->atts['hstart'] . '>';
    if ($lang != 'de_DE' && !empty($veranstaltung['ects_name'])){
        $veranstaltung['title'] = $veranstaltung['ects_name']; 
    }else{
        $veranstaltung['title'] = $veranstaltung['name'];
    }
    echo $veranstaltung['title'];
    echo '</h' . $this->atts['hstart'] . '>';
    if (!empty($veranstaltung['lecturers'])) : 
        echo '<h' . ($this->atts['hstart'] + 1) . '>' . __('Lecturers', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 1) . '>';
    ?>
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
    <?php endif; 

    echo '<h' . ($this->atts['hstart'] + 1) . '>' . __('Details', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 1) . '>';

    if (!empty($veranstaltung['angaben'])): ?>
        <p><?php echo $veranstaltung['angaben']; ?></p>
    <?php endif; 

    echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Time and place', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
    if (array_key_exists('comment', $veranstaltung)) : ?>
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
                    // ICS
                    if (in_array('ics', $this->show)){
                        $props = [
                            'summary' => $veranstaltung['title'],
                            'startdate' => (!empty($term['startdate']) ? $term['startdate'] : NULL),
                            'enddate' => (!empty($term['enddate']) ? $term['enddate'] : NULL),
                            'starttime' => (!empty($term['starttime']) ? $term['starttime'] : NULL),
                            'endtime' => (!empty($term['endtime']) ? $term['endtime'] : NULL),
                            'repeat' => (!empty($term['repeat']) ? $term['repeat'] : NULL),
                            'location' => (!empty($t['room']) ? $t['room'] : NULL),
                            'description' => (!empty($veranstaltung['comment']) ? $veranstaltung['comment'] : NULL),
                            'url' => get_permalink(),
                            'filename' => sanitize_file_name($veranstaltung['lecture_type_long']),
                            'ssstart' => $ssstart,
                            'ssend' => $ssend,
                            'wsstart' => $wsstart,
                            'wsend' => $wsend,
                        ];
    
                        $t['ics'] = '<span class="lecture-info-ics" itemprop="ics"><a href="' . plugin_dir_url(__FILE__ ) .'../ics.php?' . http_build_query($props) . '">.ics</a></span>';
                    }
                    $term_formatted = implode(' ', $t);
                    ?>
                    <li><?php echo $term_formatted; ?></li>
            <?php endforeach;
            endforeach;
        else : ?>
            <li><?php __('Time and place on appointment', 'rrze-univis'); ?></li>
        <?php endif; ?>
    </ul>

    <?php if (array_key_exists('studs', $veranstaltung) && array_key_exists('stud', $veranstaltung['studs'][0])) : 
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Fields of study', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
    ?>
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
        <h4><?php __('Prerequisites / Organizational information', 'rrze-univis');?></h4>
        <p><?php echo $veranstaltung['organizational']; ?></p>
        <?php endif;
    ?>


    <?php 
    if (!empty($veranstaltung['summary'])){
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Content', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        echo '<p>' . $veranstaltung['summary'] . '</p>';
    }

    if (!empty($veranstaltung['literature'])){
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Recommended Literature', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        echo '<p>' . $veranstaltung['literature'] . '</p>';
    }
    if (!empty($veranstaltung['ects_infos'])){
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('ECTS information', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        if (!empty($veranstaltung['ects_name'])){
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Title', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $veranstaltung['ects_name'] . '</p>';
        }
        if (!empty($veranstaltung['ects_cred'])){
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Credits', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $veranstaltung['ects_cred'] . '</p>';
        }
        if (!empty($veranstaltung['ects_summary'])){
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Content', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $veranstaltung['ects_summary'] . '</p>';
        }
        if (!empty($veranstaltung['ects_literature'])){
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Literature', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $veranstaltung['ects_literature'] . '</p>';
        }
    }

    if (!empty($veranstaltung['keywords']) || !empty($veranstaltung['maxturnout']) || !empty($veranstaltung['url_description'])){
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Additional information', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        if (!empty($veranstaltung['keywords'])){
            echo '<p>' . __('Keywords', 'rrze-univis') .': ' . $veranstaltung['keywords'] . '</p>';
        }
        if (!empty($veranstaltung['maxturnout'])){
            echo '<p>' . __('Expected participants', 'rrze-univis') .': ' . $veranstaltung['maxturnout'] . '</p>';
        }
        if (!empty($veranstaltung['url_description'])){
            echo '<p>www: <a href="' . $veranstaltung['url_description'] . '>' . $veranstaltung['url_description'] . '</a></p>';
        }
    }
endif; ?>
</div>