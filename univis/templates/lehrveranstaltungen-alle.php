<?php if ($daten['veranstaltungen']) :
    foreach ($daten['veranstaltungen'] as $veranstaltung) : 

?>
	<h2><?php 
        echo $veranstaltung['title'];
        //_rrze_debug_log($veranstaltung); ?></h2>
	<ul>
        <?php if (!empty($veranstaltung['data'])) : 
            foreach ($veranstaltung['data'] as $data) : 
            if ( isset ($data['course_id']) ) :
                return;
            else: 
            $url = get_permalink() . 'lv_id/' . $data['id'];
            if (!empty($daten['optionen']['sem'])) 
                $url .= '&sem=' . $daten['optionen']['sem']; ?>
            <li>
                <h3><a href="<?php echo $url;?>"><?php echo $data['name'];?></a></h3>
                    <ul>
                    <?php 
                    if (array_key_exists('terms', $data) && array_key_exists('term', $data['terms'][0])) :

                    //if (!empty(univisController::get_key($data, 'terms', 0)) && !empty(univisController::get_key($data['terms'], 'term', 0))) {
                    foreach ($data['terms'][0]['term'] as $term) :
                        if (!empty($term['date'])) :
                            $t['date'] = $term['date'];
                        endif;
                        if (!empty($term['starttime'])) :
                            $time['starttime'] = $term['starttime'];
                        endif;
                        if (!empty($term['endtime'])) :
                            $time['endtime'] = $term['endtime'];
                        endif;
                        if (!empty($time)) :
                            $t['time'] = $time['starttime'] . '-' . $time['endtime'] . ' ' . __('Uhr', RRZE_UnivIS::textdomain);
                        else:
                            $t['time'] = __('Zeit n.V.', RRZE_UnivIS::textdomain);
                        endif;
                        if (!empty($term['room_short'])) :
                            if (!empty($t['time'])) :
                                $t['time'] .= ',';
                            elseif (!empty($t['date'])) :
                                $t['date'] .= ',';
                            endif;
                            $t['room_short'] = $term['room_short'];
                        endif;
                        //_rrze_debug($term);
                        if (!empty($term['exclude'])) :
                            $t['exclude'] = '(' . __('außer', RRZE_UnivIS::textdomain) . ' ' . $term['exclude'] . ')';
                        endif;
                        $term_formatted = implode(' ', $t);
                        ?>    
                        <li><?php echo $term_formatted; ?></li>
                    <?php  endforeach; 
                    
                    elseif(array_key_exists('courses', $data) && array_key_exists('course', $data['courses'][0])) :
                        if(array_key_exists('comment', $data)) : ?>
                            <p><?php echo $data['comment']; ?></p>
                        <?php endif;
//                            foreach ($data['courses'][0]['course'] as $course):
//                                if(array_key_exists('terms', $course) && array_key_exists('term', $course['terms'][0])):
//                                    //_rrze_debug($course['terms'][0]['term']);
//                                    foreach ($course['terms'][0]['term'] as $term) :
//                                    //_rrze_debug($term);
//                                        if (!empty($term['date'])) :
//                                            $t['date'] = $term['date'];
//                                        endif;
//                                        if (!empty($term['starttime'])) :
//                                            $time['starttime'] = $term['starttime'];
//                                        endif;
//                                        if (!empty($term['endtime'])) :
//                                            $time['endtime'] = $term['endtime'];
//                                        endif;
//                                        if (!empty($time)) :
//                                            $t['time'] = $time['starttime'] . '-' . $time['endtime'] . ' ' . __('Uhr', RRZE_UnivIS::textdomain);
//                                        else:
//                                            $t['time'] = __('Zeit n.V.', RRZE_UnivIS::textdomain);
//                                        endif;
//                                        if (!empty($term['room_short'])) :
//                                            if (!empty($t['time'])) :
//                                                $t['time'] .= ',';
//                                            elseif (!empty($t['date'])) :
//                                                $t['date'] .= ',';
//                                            endif;
//                                            $t['room_short'] = $term['room_short'];
//                                        endif;
//                                        if (!empty($term['exclude'])) :
//                                            $t['exclude'] = '(' . __('außer', RRZE_UnivIS::textdomain) . ' ' . $term['exclude'] . ')';
//                                        endif;
//                                        $term_formatted = implode(' ', $t);
                                        //_rrze_debug($term_formatted);
                                        ?>    
                                        <li><?php _rrze_debug($data['id']);
                                        _rrze_debug($data['courses'][0]['course']);//echo $term_formatted; ?></li>
                                    <?php  //endforeach; 
//                                endif;
//                            endforeach;
                    else : ?>
                            <li><?php _e('Zeit/Ort n.V.', RRZE_UnivIS::textdomain);?></li>
                    <?php endif; ?>
                    </ul>

            </li>
            <?php endif;
            endforeach;
        endif; ?>

                
	</ul>
    <?php 
    endforeach;
                
endif; ?>

