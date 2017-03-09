<?php if ($daten['veranstaltungen']) :
    foreach ($daten['veranstaltungen'] as $veranstaltung) : ?>
	<h2><?php echo $veranstaltung['title'];?></h2>
	<ul>
        <?php if (!empty($veranstaltung['data'])) : 
            foreach ($veranstaltung['data'] as $data) : 
            $url = 'http://univis.uni-erlangen.de/prg?search=lectures&id=' . $data['id'] . '&show=long';
            if (!empty($daten['optionen']['sem'])) 
                $url .= '&sem=' . $daten['optionen']['sem']; ?>


            <li>
                <h3><a href="<?php echo $url;?>"><?php echo $data['name'];?></a></h3>
                    <ul>
                    <?php foreach ($data['terms'][0]['term'] as $term) : 
                        $term_formatted = $term['date'] . ' ' . $term['starttime'] . '-' . $term['endtime'] . __('Uhr', RRZE_UnivIS::textdomain) . ', ' . $term['room_short'];
                        if (!empty($term['exclude']))
                            $term_formatted .= ' (' . __('außer', RRZE_UnivIS::textdomain) . ' ' . $term['exclude'] . ')'; ?>
                            <li><?php echo $term_formatted;?></li>
                    <?php  endforeach; ?>
                    </ul>

            </li>
        <?php _rrze_debug_log($veranstaltung); ?>
            <?php endforeach;
        endif; ?>

                
	</ul>
    <?php endforeach;
                
endif; ?>

