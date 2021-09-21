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
                $url = get_permalink() . 'lv_id/' . $veranstaltung['lecture_id'];
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
                if (!empty($veranstaltung['fruehstud'])){
                    echo '<p>' . $veranstaltung['fruehstud'] . '</p>';
                }
                if (!empty($veranstaltung['gast'])){
                    echo '<p>' . $veranstaltung['gast'] . '</p>';
                }
                if (!empty($veranstaltung['schein'])){
                    echo '<p>' . $veranstaltung['schein'] . '</p>';
                }
                if (!empty($veranstaltung['ects'])){
                    echo '<p>' . $veranstaltung['ects'] . '</p>';
                }
                if (!empty($veranstaltung['ects_cred'])){
                    echo '<p>' . $veranstaltung['ects_cred'] . '</p>';
                }
                if (!empty($veranstaltung['leclanguage_long']) && ($veranstaltung['leclanguage_long'] != __('Unterrichtssprache Deutsch', 'rrze-univis'))){
                    echo '<p>' . $veranstaltung['leclanguage_long'] . '</p>';
                }
                ?>
                <ul>
                        <?php
                        if (isset($veranstaltung['courses'])) :
                            foreach ($veranstaltung['courses'] as $course):
                                if ((empty($veranstaltung['lecturer_key']) || empty($course['doz'])) || (!empty($veranstaltung['lecturer_key']) && !empty($course['doz']) && (in_array($veranstaltung['lecturer_key'], $course['doz'])))){
                                    // foreach ($course['term'] as $course):
                                        $t = array();
                                        $time = array();
                                        if (!empty($course['repeat'])) :
                                            $t['repeat'] = $course['repeat'];
                                        endif;
                                        if (!empty($course['startdate'])) :
                                            if (!empty($course['enddate']) && $course['startdate'] != $course['enddate']):
                                                $t['date'] = date("d.m.Y", strtotime($course['startdate'])) . '-' . date("d.m.Y", strtotime($course['enddate']));
                                            else:
                                                $t['date'] = date("d.m.Y", strtotime($course['startdate']));
                                            endif;
                                        endif;
                                        if (!empty($course['starttime'])) :
                                            $time['starttime'] = $course['starttime'];
                                        endif;
                                        if (!empty($course['endtime'])) :
                                            $time['endtime'] = $course['endtime'];
                                        endif;
                                        if (!empty($time)) :
                                            $t['time'] = $time['starttime'] . '-' . $time['endtime'];
                                        else:
                                            $t['time'] = __('Time on appointment', 'rrze-univis');
                                        endif;
                                        if (!empty($course['room'])) :
                                            $t['room'] = __('Room', 'rrze-univis') . ' ' . $course['room'];
                                        endif;
                                        if (!empty($course['exclude'])) :
                                            $t['exclude'] = '(' . __('exclude', 'rrze-univis') . ' ' . $course['exclude'] . ')';
                                        endif;
                                        if (!empty($course['coursename'])) :
                                            $t['coursename'] = '(' . __('Course', 'rrze-univis') . ' ' . $course['coursename'] . ')';
                                        endif;
                                        // ICS
                                        if (in_array('ics', $this->show) && !in_array('ics', $this->hide)){
                                            $props = [
                                                'summary' => $veranstaltung['title'],
                                                'startdate' => (!empty($course['startdate']) ? $course['startdate'] : NULL),
                                                'enddate' => (!empty($course['enddate']) ? $course['enddate'] : NULL),
                                                'starttime' => (!empty($course['starttime']) ? $course['starttime'] : NULL),
                                                'endtime' => (!empty($course['endtime']) ? $course['endtime'] : NULL),
                                                'repeat' => (!empty($course['repeat']) ? $course['repeat'] : NULL),
                                                'location' => (!empty($t['room']) ? $t['room'] : NULL),
                                                'description' => (!empty($veranstaltung['comment']) ? $veranstaltung['comment'] : NULL),
                                                'url' => get_permalink(),
                                                'map' => (!empty($course['room']['north']) && !empty($course['room']['east']) ? 'https://karte.fau.de/api/v1/iframe/marker/' . $course['room']['north'] . ',' . $course['room']['east'] . '/zoom/16' : ''),
                                                'filename' => sanitize_file_name($typ),
                                                'ssstart' => $ssstart,
                                                'ssend' => $ssend,
                                                'wsstart' => $wsstart,
                                                'wsend' => $wsend,
                                            ];

                                            $screenReaderTxt = ': ' . __('Termin', 'rrze-univis') . ' ' . (!empty($t['repeat']) ? $t['repeat'] : '') . ' ' . (!empty($t['date']) ? $t['date'] . ' ' : '') . $t['time'] . ' ' . __('in den Kalender importieren', 'rrze-univis');
                                            $t['ics'] = '<span class="lecture-info-ics" itemprop="ics"><a href="' . plugin_dir_url(__FILE__ ) .'../ics.php?' . http_build_query($props) . '">.ics<span class="screen-reader-text">' . $screenReaderTxt . '</span></a></span>';
                                        }
                                        $t['time'] .= ',';
                                        $course_formatted = implode(' ', $t);
                                        ?>    
                                        <li><?php echo $course_formatted; ?></li>
                                    <?php
                                    // endforeach;
                                }
                            endforeach;
                        else : ?>
                                <li><?php _e('Time and place on appointment', 'rrze-univis');?></li>
                        <?php endif; ?>
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