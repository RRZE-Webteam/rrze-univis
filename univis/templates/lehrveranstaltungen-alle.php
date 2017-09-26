<?php if ($daten['veranstaltungen']) :
    foreach ($daten['veranstaltungen'] as $veranstaltung) : 
        _rrze_debug_log($veranstaltung);
        if($this->optionen['lv_type'] == 1) : ?>
	<h2>
            <?php echo $veranstaltung['title']; ?>
        </h2>
        <?php endif; ?>
	<ul>
        <?php if (!empty($veranstaltung['data'])) : 
            foreach ($veranstaltung['data'] as $data) : 
            if( empty( $this->optionen['leclanguage'] ) || ( isset( $data['leclanguage'] ) && strpbrk( $data['leclanguage'], $this->optionen['leclanguage'] ) != FALSE  ) )  :
                if ( !isset ($data['parent_course_id']) ): 
                $url = get_permalink() . 'lv_id/' . $data['id'];
                if (!empty($daten['optionen']['sem'])) :
                    $url .= '&sem=' . $daten['optionen']['sem']; 
                endif; ?>
                <li>
                    <h3><a href="<?php echo $url; ?>"><?php echo $data['name']; ?></a></h3>
                    <?php if (array_key_exists('comment', $data)) : ?>
                        <p><?php echo $data['comment']; ?></p>
                    <?php endif; ?>
                    <ul>
                        <?php
                        if (array_key_exists('course_terms', $data)) :
                            foreach ($data['course_terms'] as $course_terms):
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
    //                            if (!empty($course_terms['coursename'])) :
    //                                $t['coursename'] = '(' . __('Kurs', RRZE_UnivIS::textdomain) . ' ' . $course_terms['coursename'] . ')';
    //                            endif;
                                $term_formatted = implode(' ', $t);
                                ?>    
                                <li><?php echo $term_formatted; ?></li>
                            <?php
                            endforeach;
                        elseif (array_key_exists('terms', $data) && array_key_exists('term', $data['terms'][0])) :
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
                                $t['time'] = $time['starttime'] . '-' . $time['endtime'];
                            else:
                                $t['time'] = __('Zeit n.V.', RRZE_UnivIS::textdomain);
                            endif;
                            if (!empty($term['room_short'])) :
                                if (!empty($t['time'])) :
                                    $t['time'] .= ',';
                                elseif (!empty($t['date'])) :
                                    $t['date'] .= ',';
                                endif;
                                $t['room_short'] = __('Raum', RRZE_UnivIS::textdomain) . ' ' . $term['room_short'];
                            endif;
                            if (!empty($term['exclude'])) :
                                $t['exclude'] = '(' . __('außer', RRZE_UnivIS::textdomain) . ' ' . $term['exclude'] . ')';
                            endif;
                            $term_formatted = implode(' ', $t);
                            ?>    
                            <li><?php echo $term_formatted; ?></li>
                        <?php  endforeach; 


                        else : ?>
                                <li><?php _e('Zeit/Ort n.V.', RRZE_UnivIS::textdomain);?></li>
                        <?php endif; ?>
                        </ul>

                </li>
                <?php endif;
                endif;
            endforeach;
        endif; ?>

                
	</ul>
    <?php 
    endforeach;
                
endif; ?>

