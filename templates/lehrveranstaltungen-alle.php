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
                    $veranstaltung['title'] = $veranstaltung['name'];
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
                                        if (!empty($term['room']['short'])) :
                                            $t['room'] = __('Room', 'rrze-univis') . ' ' . $term['room']['short'];
                                        endif;
                                        if (!empty($term['exclude'])) :
                                            $t['exclude'] = '(' . __('exclude', 'rrze-univis') . ' ' . $term['exclude'] . ')';
                                        endif;
                                        if (!empty($course['coursename'])) :
                                            $t['coursename'] = '(' . __('Course', 'rrze-univis') . ' ' . $course['coursename'] . ')';
                                        endif;
                                        // ICS
                                        if (in_array('ics', $this->show) && !in_array('ics', $this->hide)){
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
                                                'map' => (!empty($term['room']['north']) && !empty($term['room']['east']) ? 'https://karte.fau.de/api/v1/iframe/marker/' . $term['room']['north'] . ',' . $term['room']['east'] . '/zoom/16' : ''),
                                                'filename' => sanitize_file_name($typ),
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
                                    <?php
                                    endforeach;
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