<?php if ($data) :
    $lang = get_locale();
    foreach ($data as $typ => $veranstaltungen) : 
        ?>
	<h2>
            <?php echo $typ; ?>
        </h2>
	<ul>
        <?php 
            foreach ($veranstaltungen as $veranstaltung) : 
                $url = get_permalink() . 'lv_id/' . $veranstaltung['lecture_id'];
                ?>
                <li>
                    <h3><a href="<?php echo $url; ?>"><?php 
                    if ($lang != 'de_DE' && !empty($veranstaltung['ects_name'])){
                        $veranstaltung['title'] = $veranstaltung['ects_name']; 
                    }else{
                        $veranstaltung['title'] = $veranstaltung['name'];
                    }
                    echo $veranstaltung['title'];
                    ?></a></h3>
                    <?php 
                    if (!empty($veranstaltung['comment'])) : ?>
                        <p><?php echo $veranstaltung['comment']; ?></p>
                    <?php
                    endif; 
                    ?>
                    <ul>
                        <?php
                        if (isset($veranstaltung['courses'])) :
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
                                            'filename' => sanitize_file_name($typ),
                                        ];
                    
                                        $t['ics'] = '<a href="' . plugin_dir_url(__FILE__ ) .'../ics.php?' . http_build_query($props) . '">' . __('ICS') . '</a>';
                                    }

                                    $term_formatted = implode(' ', $t);
                                    ?>    
                                    <li><?php echo $term_formatted; ?></li>
                                <?php
                                endforeach;
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
