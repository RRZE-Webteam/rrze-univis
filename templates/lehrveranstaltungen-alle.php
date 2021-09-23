<div class="rrze-univis">
<?php if ($data) :
    $lang = get_locale();
    $options = get_option('rrze-univis');
    $ssstart = (!empty($options['basic_ssStart']) ? $options['basic_ssStart'] : 0);
    $ssend = (!empty($options['basic_ssEnd']) ? $options['basic_ssEnd'] : 0);
    $wsstart = (!empty($options['basic_wsStart']) ? $options['basic_wsStart'] : 0);
    $wsend = (!empty($options['basic_wsEnd']) ? $options['basic_wsEnd'] : 0);

    foreach ($data as $typ => $veranstaltungen) : 
        echo '<h' . $this->atts['hstart'] . '>' . $typ . '</h' . $this->atts['hstart'] . '>';
?>
	<ul>
        <?php 
            foreach ($veranstaltungen as $veranstaltung) : 
                $url = get_permalink() . 'lv_id/' . $veranstaltung['lecture_univisID'];
                ?>
                <li>
                <?php 
                echo '<h' . ($this->atts['hstart'] + 1) . '><a href="' . $url . '">'; 
                if ($lang != 'de_DE' && $lang!='de_DE_formal' && !empty($veranstaltung['ects_name'])){
                    $veranstaltung['title'] = $veranstaltung['ects_name']; 
                }else{
                    $veranstaltung['title'] = $veranstaltung['lecture_title'];
                }
                echo $veranstaltung['title'];
                echo '</a></h' . ($this->atts['hstart'] + 1) . '>'; 

                if (!empty($veranstaltung['lecturers'])){
                    $lecturers = '';
                    foreach($veranstaltung['lecturers'] as $lecturer){
                        $lecturers .= '<a class="url" href="' . get_permalink() . 'univisid/' . $lecturer['univisID'] . '" itemprop="name">' . $lecturer['title'] . ' ' . $lecturer['firstname'] . ' ' . $lecturer['lastname'] . '</a>, ';
                    }
                    $lecturers = substr($lecturers, 0, strlen($lecturers) - 2);
                    echo __('Dozentinnen/Dozenten', 'rrze_univis') . ': ' . $lecturers;
                }

                if (!empty($veranstaltung['comment'])){
                    echo '<p>' . make_clickable($veranstaltung['comment']) . '</p>';
                }
                if (!empty($veranstaltung['organizational'])){
                    echo '<p>' . make_clickable($veranstaltung['organizational']) . '</p>';
                }
                if (!empty($veranstaltung['sws'])){
                    echo '<p>' . $veranstaltung['sws'] . '</p>';
                }
                if (!empty($veranstaltung['maxturnout'])){
                    echo '<p>' . __('Expected participants', 'rrze-univis') .': ' . $veranstaltung['maxturnout'] . '</p>';
                }
                if (!empty($veranstaltung['beginners'])){
                    echo '<p>' . $veranstaltung['beginners'] . '</p>';
                }
                if (!empty($veranstaltung['earlystudy'])){
                    echo '<p>' . $veranstaltung['earlystudy'] . '</p>';
                }
                if (!empty($veranstaltung['guest'])){
                    echo '<p>' . $veranstaltung['guest'] . '</p>';
                }
                if (!empty($veranstaltung['certification'])){
                    echo '<p>' . $veranstaltung['certification'] . '</p>';
                }
                if (!empty($veranstaltung['ects'])){
                    echo '<p>' . $veranstaltung['ects'] . '</p>';
                }
                if (!empty($veranstaltung['ects_cred'])){
                    echo '<p>' . $veranstaltung['ects_cred'] . '</p>';
                }
                if (!empty($veranstaltung['leclanguage_long']) && ($veranstaltung['leclanguage_long'] != 'German')){
                    echo '<p>' . __('Unterrichtssprache', 'rrze-univis') . ' ' . $veranstaltung['leclanguage_long'] . '</p>';
                }
                ?>
                <ul>
                <?php
                if (isset($veranstaltung['courses'])){
                    foreach ($veranstaltung['courses'] as $aCourse) {
                        if (!empty($aCourse['coursename'])) {
                            $t['coursename'] = '(' . __('Course', 'rrze-univis') . ' ' . $course['coursename'] . ')';
                        }
                        if (!empty($aCourse['lecturers'])){
                            $lecturers = '';
                            foreach($aCourse['lecturers'] as $lecturer){
                                $lecturers .= '<a class="url" href="' . get_permalink() . 'univisid/' . $lecturer['univisID'] . '" itemprop="name">' . $lecturer['title'] . ' ' . $lecturer['firstname'] . ' ' . $lecturer['lastname'] . '</a>, ';
                            }
                            $t['lecturers'] = substr($lecturers, 0, strlen($lecturers) - 2);
                        }

                        if (isset($aCourse['terms'])){
                            foreach ($aCourse['terms'] as $term) {
                                $t = array();
                                $time = array();
                                if (!empty($term['repeat'])) {
                                    $t['repeat'] = $term['repeat'];
                                }
                                if (!empty($term['startdate'])){
                                    if (!empty($term['enddate']) && $term['startdate'] != $term['enddate']){
                                        $t['date'] = date("d.m.Y", strtotime($term['startdate'])) . '-' . date("d.m.Y", strtotime($term['enddate']));
                                    }else{
                                        $t['date'] = date("d.m.Y", strtotime($term['startdate']));
                                    }
                                }
                                if (!empty($term['starttime'])){
                                    $time['starttime'] = $term['starttime'];
                                }
                                if (!empty($term['endtime'])){
                                    $time['endtime'] = $term['endtime'];
                                }
                                if (!empty($time)){
                                    $t['time'] = $time['starttime'] . '-' . $time['endtime'];
                                }else{
                                    $t['time'] = __('Time on appointment', 'rrze-univis');
                                }
                                if (!empty($term['room'])){
                                    $t['room'] = __('Room', 'rrze-univis') . ' ' . $term['room'];
                                }
                                if (!empty($term['exclude'])){
                                    $t['exclude'] = '(' . __('exclude', 'rrze-univis') . ' ' . $term['exclude'] . ')';
                                }
                                // ICS
                                if (in_array('ics', $this->show) && !in_array('ics', $this->hide)) {
                                    $props = [
                                            'summary' => $veranstaltung['title'],
                                            'startdate' => (!empty($term['startdate']) ? $term['startdate'] : null),
                                            'enddate' => (!empty($term['enddate']) ? $term['enddate'] : null),
                                            'starttime' => (!empty($term['starttime']) ? $term['starttime'] : null),
                                            'endtime' => (!empty($term['endtime']) ? $term['endtime'] : null),
                                            'repeat' => (!empty($term['repeat']) ? $term['repeat'] : null),
                                            'location' => (!empty($t['room']) ? $t['room'] : null),
                                            'description' => (!empty($veranstaltung['comment']) ? $veranstaltung['comment'] : null),
                                            'url' => get_permalink(),
                                            'map' => (!empty($term['room']['north']) && !empty($course['room']['east']) ? 'https://karte.fau.de/api/v1/iframe/marker/' . $course['room']['north'] . ',' . $course['room']['east'] . '/zoom/16' : ''),
                                            'filename' => sanitize_file_name($typ),
                                            'ssstart' => $ssstart,
                                            'ssend' => $ssend,
                                            'wsstart' => $wsstart,
                                            'wsend' => $wsend,
                                        ];

                                    $screenReaderTxt = ': ' . __('Termin', 'rrze-univis') . ' ' . (!empty($t['repeat']) ? $t['repeat'] : '') . ' ' . (!empty($t['date']) ? $t['date'] . ' ' : '') . $t['time'] . ' ' . __('in den Kalender importieren', 'rrze-univis');
                                    $t['ics'] = '<span class="lecture-info-ics" itemprop="ics"><a href="' . plugin_dir_url(__FILE__) .'../ics.php?' . http_build_query($props) . '">.ics<span class="screen-reader-text">' . $screenReaderTxt . '</span></a></span>';
                                }
                                $t['time'] .= ',';
                                $course_formatted = implode(' ', $t);
                                echo '<li>' . $course_formatted . '</li>';
                            }
                        }
                    }
                }else{
                    echo '<li>' . __('Time and place on appointment', 'rrze-univis') . '</li>';
                } 
                ?>
                    </ul>
                </li>
                <?php 
            endforeach;
        ?>
	</ul>
    <?php 
    endforeach;
endif;
?>
</div>