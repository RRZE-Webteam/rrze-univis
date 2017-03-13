<?php if ($daten['veranstaltungen']) :
    foreach ($daten['veranstaltungen'] as $veranstaltung) : ?>
	<h2><?php 
        echo $veranstaltung['title'];?></h2>
	<ul>
        <?php if (!empty($veranstaltung['data'])) : 
            foreach ($veranstaltung['data'] as $data) : 
            $url = 'http://univis.uni-erlangen.de/prg?search=lectures&id=' . $data['id'] . '&show=long';
            if (!empty($daten['optionen']['sem'])) 
                $url .= '&sem=' . $daten['optionen']['sem']; ?>
            <li>
                <h3><a href="<?php echo $url;?>"><?php echo $data['name'];?></a></h3>
                    <ul>
                    <?php 
                    if (array_key_exists('terms', $data)) {
                    //if (!empty(univisController::get_key($data, 'terms', 0)) && !empty(univisController::get_key($data['terms'], 'term', 0))) {
                        foreach ($data['terms'][0]['term'] as $term) : 
                        if( !empty($term['starttime']) || !empty($term['endtime']) ) {
                            $term_time = ' ' . $term['starttime'] . '-' . $term['endtime'] . ' ' . __('Uhr', RRZE_UnivIS::textdomain) . ', ' ;
                        } else {
                            $term_time = ' ' . __('Zeit n.V.', RRZE_UnivIS::textdomain) . ', ' ;
                        }
                        $term_formatted = $term['date'] . $term_time . $term['room_short'];
                        if (!empty($term['exclude']))
                            $term_formatted .= ' (' . __('außer', RRZE_UnivIS::textdomain) . ' ' . $term['exclude'] . ')'; ?>
                            <li><?php echo $term_formatted;?></li>
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

