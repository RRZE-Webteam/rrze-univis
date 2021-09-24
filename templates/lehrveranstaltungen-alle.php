<div class="rrze-univis">
<?php if ($data) :
    $lang = get_locale();
    $options = get_option('rrze-univis');
    $ssstart = (!empty($options['basic_ssStart']) ? $options['basic_ssStart'] : 0);
    $ssend = (!empty($options['basic_ssEnd']) ? $options['basic_ssEnd'] : 0);
    $wsstart = (!empty($options['basic_wsStart']) ? $options['basic_wsStart'] : 0);
    $wsend = (!empty($options['basic_wsEnd']) ? $options['basic_wsEnd'] : 0);

    foreach ($data as $typ => $aEvent) : 
        echo '<h' . $this->atts['hstart'] . '>' . $typ . '</h' . $this->atts['hstart'] . '>';
?>
	<ul>
        <?php 
            foreach ($aEvent as $event) : 
                $url = get_permalink() . 'lv_id/' . $event['lecture_univisID'];
                ?>
                <li>
                <?php 
                echo '<h' . ($this->atts['hstart'] + 1) . '><a href="' . $url . '">'; 
                if ($lang != 'de_DE' && $lang!='de_DE_formal' && !empty($event['ects_name'])){
                    $event['title'] = $event['ects_name']; 
                }else{
                    $event['title'] = $event['lecture_title'];
                }
                echo $event['title'];
                echo '</a></h' . ($this->atts['hstart'] + 1) . '>'; 

                if (!empty($event['lecturers'])){
                    $lecturers = '';
                    foreach($event['lecturers'] as $lecturer){
                        $lecturers .= '<a class="url" href="' . get_permalink() . 'univisid/' . $lecturer['univisID'] . '" itemprop="name">' . $lecturer['title'] . ' ' . $lecturer['firstname'] . ' ' . $lecturer['lastname'] . '</a>, ';
                    }
                    $lecturers = substr($lecturers, 0, strlen($lecturers) - 2);
                    echo __('Dozentinnen/Dozenten', 'rrze_univis') . ': ' . $lecturers;
                }
                if (!empty($event['comment'])){
                    echo '<p>' . make_clickable($event['comment']) . '</p>';
                }
                if (!empty($event['organizational'])){
                    echo '<p>' . make_clickable($event['organizational']) . '</p>';
                }
                if (!empty($event['sws'])){
                    echo '<p>' . $event['sws'] . '</p>';
                }
                if (!empty($event['maxturnout'])){
                    echo '<p>' . __('Expected participants', 'rrze-univis') .': ' . $event['maxturnout'] . '</p>';
                }
                if (!empty($event['beginners'])){
                    echo '<p>' . $event['beginners'] . '</p>';
                }
                if (!empty($event['earlystudy'])){
                    echo '<p>' . $event['earlystudy'] . '</p>';
                }
                if (!empty($event['guest'])){
                    echo '<p>' . $event['guest'] . '</p>';
                }
                if (!empty($event['certification'])){
                    echo '<p>' . $event['certification'] . '</p>';
                }
                if (!empty($event['ects'])){
                    echo '<p>' . $event['ects'] . '</p>';
                }
                if (!empty($event['ects_cred'])){
                    echo '<p>' . $event['ects_cred'] . '</p>';
                }
                if (!empty($event['leclanguage_long']) && ($event['leclanguage_long'] != 'German')){
                    echo '<p>' . __('Unterrichtssprache', 'rrze-univis') . ' ' . $event['leclanguage_long'] . '</p>';
                }
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
                                        'startdate' => (!empty($term['startdate']) ? $term['startdate'] : null),
                                        'enddate' => (!empty($term['enddate']) ? $term['enddate'] : null),
                                        'starttime' => (!empty($term['starttime']) ? $term['starttime'] : null),
                                        'endtime' => (!empty($term['endtime']) ? $term['endtime'] : null),
                                        'repeat' => (!empty($term['repeat']) ? $term['repeat'] : null),
                                        'location' => (!empty($t['room']) ? $t['room'] : null),
                                        'description' => (!empty($event['comment']) ? $event['comment'] : null),
                                        'url' => get_permalink(),
                                        'map' => $map,
                                        'filename' => sanitize_file_name($typ),
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