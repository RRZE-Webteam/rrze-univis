<?php if ($daten['veranstaltungen']) :
    foreach ($daten['veranstaltungen'] as $veranstaltung) : ?>
	<h2><?php 
        echo $veranstaltung['title'];?></h2>
	<ul>
        <?php if (!empty($veranstaltung['data'])) : 
            foreach ($veranstaltung['data'] as $data) : 
            $url = get_permalink() . 'lv_id/' . $data['id'];
            if (!empty($daten['optionen']['sem'])) 
                $url .= '&sem=' . $daten['optionen']['sem']; ?>
            <li>
                <h3><a href="<?php echo $url;?>"><?php echo $data['name'];?></a></h3>
                    <ul>
                    <?php 
                    if (array_key_exists('terms', $data) && array_key_exists('term', $data['terms'][0])) {
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
                        if (!empty($term['exclude'])) :
                            $t['exclude'] = '(' . __('außer', RRZE_UnivIS::textdomain) . ' ' . $term['exclude'] . ')';
                        endif;
                        $term_formatted = implode(' ', $t);
                        ?>    
                        <li><?php echo $term_formatted; ?></li>
                    <?php  endforeach; 
                    } else { ?>
                            <li><?php _e('Zeit/Ort n.V.', RRZE_UnivIS::textdomain);?></li>
                    <?php } ?>
                    </ul>

            </li>
            <?php endforeach;
        endif; ?>

                
	</ul>
    <?php endforeach;
                
endif; ?>

