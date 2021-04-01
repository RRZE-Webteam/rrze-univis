<!-- 2DO: Termine: label "Einzeltermin" / Rooms by term -->
<?php if ($data) :
    foreach ($data as $typ => $veranstaltungen) : 
        ?>
	<h2>
            <?php echo $typ; ?>
        </h2>
	<ul>
        <?php 
            foreach ($veranstaltungen as $veranstaltung) : 
            // if( empty( $this->optionen['leclanguage'] ) || ( isset( $data['leclanguage'] ) && strpbrk( $data['leclanguage'], $this->optionen['leclanguage'] ) != FALSE  ) )  :
                // if ( !isset ($data['parent_course_id']) ): 
                // $url = get_permalink() . 'lv_id/' . $data['id'];
                // if (!empty($daten['optionen']['sem'])) :
                //     $url .= '&sem=' . $daten['optionen']['sem']; 
                // endif; 
                $url = get_permalink() . 'lv_id/' . $veranstaltung['lecture_id'];
                ?>
                <li>
                    <h3><a href="<?php echo $url; ?>"><?php echo $veranstaltung['name']; ?></a></h3>
                    <?php 
                    // if( $this->optionen['kompakt'] == 0 ):
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
                                    if (!empty($term['startdate'])) :
                                        $t['date'] = $term['startdate'];
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
                                        $t['time'] = __('Time on appointment', 'rrze-univis');
                                    endif;
                                    if (!empty($term['room_short'])) :
                                        if (!empty($t['time'])) :
                                            $t['time'] .= ',';
                                        elseif (!empty($t['date'])) :
                                            $t['date'] .= ',';
                                        endif;
                                        $t['room_short'] = __('Room', 'rrze-univis') . ' ' . $term['room_short'];
                                    endif;
                                    if (!empty($term['exclude'])) :
                                        $t['exclude'] = '(' . __('exclude', 'rrze-univis') . ' ' . $term['exclude'] . ')';
                                    endif;
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
                // endif;
                // endif;
                // endif;
            endforeach;
        ?>

                
	</ul>
    <?php 
    endforeach;
                
endif;
