<div class="rrze-univis">
<?php foreach ($data as $aEvent) : 
foreach ($aEvent as $event) :
    $type = $event['lecture_type'];
    $lang = get_locale();
    $options = get_option('rrze-univis');
    $ssstart = (!empty($options['basic_ssStart']) ? $options['basic_ssStart'] : 0);
    $ssend = (!empty($options['basic_ssEnd']) ? $options['basic_ssEnd'] : 0);
    $wsstart = (!empty($options['basic_wsStart']) ? $options['basic_wsStart'] : 0);
    $wsend = (!empty($options['basic_wsEnd']) ? $options['basic_wsEnd'] : 0);

    echo '<div itemscope itemtype="https://schema.org/Course">';
    echo '<h' . $this->atts['hstart'] . '>';
    if ($lang != 'de_DE' && $lang != 'de_DE_formal' && !empty($event['ects_name'])){
        $event['title'] = $event['ects_name']; 
    }else{
        $event['title'] = $event['lecture_title'];
    }
    echo '<span itemprop="name">' . $event['title'] . '</span>';
    echo '</h' . $this->atts['hstart'] . '>';
    if (!empty($event['lecturers'])) : 
        echo '<h' . ($this->atts['hstart'] + 1) . '>' . __('Lecturers', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 1) . '>';
    ?>
        <ul>
        <?php
        foreach ($event['lecturers'] as $lecturer) :
            $name = array();
            if (!empty($lecturer['title'])) :
                $name['title'] = '<span itemprop="honorificPrefix">' . $lecturer['title'] . '</span>';
            endif;
            if (!empty($lecturer['firstname'])) :
                $name['firstname'] = '<span itemprop="givenName">' . $lecturer['firstname'] . '</span>';
            endif;
            if (!empty($lecturer['lastname'])) :
                $name['lastname'] = '<span itemprop="familyName">' . $lecturer['lastname'] . '</span>';
            endif;
            $fullname = implode(' ', $name);
            if (!empty($lecturer['univisID'])):
                $url = '<a href="' . get_permalink() . 'univisid/' . $lecturer['univisID'] . '">' . $fullname . '</a>';
            else:
                $url = $fullname;
            endif;?>
            <li itemprop="provider" itemscope itemtype="http://schema.org/Person"><?php echo $url; ?></li>
            <?php
        endforeach; ?>
        </ul>
    <?php endif; 

    echo '<h' . ($this->atts['hstart'] + 1) . '>' . __('Details', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 1) . '>';
    if (!empty($event['comment'])){
        echo '<p>' . make_clickable($event['comment']) . '</p>';
    }

    echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Time and place', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';

    ?>
    <ul>
    <?php
    if (isset($event['courses'])){
        foreach ($event['courses'] as $aCourse) {
            if (!empty($aCourse['coursename'])) {
                $t['coursename'] = '(' . __('Course', 'rrze-univis') . ' ' . $aCourse['coursename'] . ')';
            }
            $lecturers = '';
            if (!empty($aCourse['lecturers'])){
                foreach($aCourse['lecturers'] as $lecturer){
                    $lecturers .= '<a class="url" href="' . get_permalink() . 'univisid/' . $lecturer['univisID'] . '" itemprop="name">' . ' ' . (!empty($lecturer['firstname']) ? substr($lecturer['firstname'], 0, 1) . '. ' : '' ) . $lecturer['lastname'] . '</a>, ';
                }
                $lecturers = substr($lecturers, 0, strlen($lecturers) - 2);
            }
            if (isset($aCourse['terms'])){
                $t = [];
                foreach ($aCourse['terms'] as $term) {
                    if (!empty($term['time_description'])) {
                        $t['time_description'] = make_clickable($term['time_description']);
                    }
                    if (!empty($term['repeat'])) {
                        $t['repeat'] = $term['repeat'];
                    }
                    if (!empty($term['startdate']) && (int)$term['startdate']){
                        if (!empty($term['enddate']) && (int)$term['enddate'] && ($term['startdate'] != $term['enddate'])){
                            $t['date'] = date("d.m.Y", strtotime($term['startdate'])) . ' - ' . date("d.m.Y", strtotime($term['enddate']));
                        }else{
                            $t['date'] = date("d.m.Y", strtotime($term['startdate']));
                        }
                    }
                    if (!empty($term['starttime']) && !empty($term['endtime']) && (int)$term['starttime'] && (int)$term['endtime']){
                        $t['time'] = $term['starttime'] . ' - ' . $term['endtime'];
                    }else{
                        $t['time'] = __('Time on appointment', 'rrze-univis');
                    }
                    if (!empty($term['exclude'])){
                        $t['time'] .= ' (' . __('exclude', 'rrze-univis') . ' ' . $term['exclude'] . ')';
                    }
                    $map = (!empty($term['north']) && !empty($term['east']) ? 'https://karte.fau.de/api/v1/iframe/marker/' . $term['north'] . ',' . $term['east'] . '/zoom/16' : '');
                    if (!empty($term['room'])){
                        $t['room'] = __('Room', 'rrze-univis') . ' ' . (!empty($map)?"<a class='url' href='$map'>":'') . $term['room'] . (!empty($map)?'</a>':'');
                    }
                    if (!empty($lecturers)) {
                        $t['lecturers'] = $lecturers;
                    }
                    // ICS
                    $ics = '';
                    if (in_array('ics', $this->show) && !in_array('ics', $this->hide)) {
                        $props = [
                            'summary' => $event['title'],
                            'startdate' => (!empty($term['startdate'])  && (int)$term['startdate'] ? $term['startdate'] : null),
                            'enddate' => (!empty($term['enddate']) && (int)$term['enddate'] ? $term['enddate'] : null),
                            'starttime' => (!empty($term['starttime']) && (int)$term['starttime'] ? $term['starttime'] : null),
                            'endtime' => (!empty($term['endtime']) && (int)$term['endtime'] ? $term['endtime'] : null),
                            'repeat' => (!empty($term['repeat']) ? $term['repeat'] : null),
                            'location' => (!empty($t['room']) ? $t['room'] : null),
                            'description' => (!empty($event['comment']) ? $event['comment'] : null),
                            'url' => get_permalink(),
                            'map' => $map,
                            'filename' => sanitize_file_name($type),
                            'ssstart' => $ssstart,
                            'ssend' => $ssend,
                            'wsstart' => $wsstart,
                            'wsend' => $wsend,
                        ];
                        $screenReaderTxt = ': ' . __('Termin', 'rrze-univis') . ' ' . (!empty($t['repeat']) ? $t['repeat'] : '') . ' ' . (!empty($t['date']) ? $t['date'] . ' ' : '') . $t['time'] . ' ' . __('in den Kalender importieren', 'rrze-univis');
                        $ics = ' <span class="lecture-info-ics" itemprop="ics"><a href="' . plugin_dir_url(__FILE__) .'../ics.php?' . http_build_query($props) . '">.ics<span class="screen-reader-text">' . $screenReaderTxt . '</span></a></span>';
                    }
                    echo '<li>' . implode(', ', $t) . $ics . '</li>';
                }
            }
        }
    }else{
        echo '<li>' . __('Time and place on appointment', 'rrze-univis') . '</li>';
    } 
?>
    </ul>

    <?php if (!empty($event['stud'])) : 
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Fields of study', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
    ?>
    <ul>
        <?php
        foreach ($event['stud'] as $stud) :
            $s = array();
            if (!empty($stud['pflicht'])) :
                $s['pflicht'] = $stud['pflicht'];
            endif;
            if (!empty($stud['richt'])) :
                $s['richt'] = $stud['richt'];
            endif;
            if (!empty($stud['sem'])) :
                $s['sem'] = __('from SEM', 'rrze-univis') . $stud['sem'];
            endif;
            $studinfo = implode(' ', $s);
            ?>
            <li><?php echo $studinfo; ?></li>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>


    <?php if (!empty($event['organizational'])) : ?>
        <h4><?php __('Prerequisites / Organizational information', 'rrze-univis');?></h4>
        <p><?php echo make_clickable($event['organizational']); ?></p>
        <?php endif;
    ?>


    <?php 
    if (!empty($event['summary'])){
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Content', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        echo '<p itemprop="description">' . make_clickable($event['summary']) . '</p>';
    }

    if (!empty($event['literature'])){
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Recommended Literature', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        echo '<p>' . make_clickable($event['literature']) . '</p>';
    }
    if (!empty($event['ects_name'])){
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('ECTS information', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        if (!empty($event['ects_name'])){
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Title', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $event['ects_name'] . '</p>';
        }
        if (!empty($event['ects_cred'])){
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Credits', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $event['ects_cred'] . '</p>';
        }
        if (!empty($event['ects_summary'])){
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Content', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $event['ects_summary'] . '</p>';
        }
        if (!empty($event['ects_summary'])){
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Organizational', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $event['ects_summary'] . '</p>';
        }
        if (!empty($event['ects_literature'])){
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Literature', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $event['ects_literature'] . '</p>';
        }
    }

    if (!empty($event['keywords']) || !empty($event['maxturnout']) || !empty($event['url_description'])){
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Additional information', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        if (!empty($event['keywords'])){
            echo '<p>' . __('Keywords', 'rrze-univis') .': ' . $event['keywords'] . '</p>';
        }
        if (!empty($event['maxturnout'])){
            echo '<p>' . __('Expected participants', 'rrze-univis') .': ' . $event['maxturnout'] . '</p>';
        }
        if (!empty($event['url_description'])){
            echo '<p>www: <a href="' . $event['url_description'] . '">' . $event['url_description'] . '</a></p>';
        }
    }

echo '</div>'; // schema
endforeach; 
endforeach; ?>
</div>