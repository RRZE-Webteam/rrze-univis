<?php if ($data):
    foreach ($data as $year => $entries): ?>
	    <?php echo '<h' . $this->atts['hstart'] . '>' . $year . '</h' . $this->atts['hstart'] . '>'; ?>
	        <ul class="univis-publication-byyear">
	            <?php foreach ($entries as $entry): ?>
	                <li>
	                    <span>
	                        <?php foreach ($entry['authors'] as $author):
                                if (isset($author['person_id'])):
                                    $url = get_permalink() . 'univisid/' . $author['person_id'];
                                    echo '<a href="' . $url . '">' . $author['lastname'] . (isset($author['firstname']) ? ', ' . $author['firstname'] : '') . '</a>; ';
                                else:
                                    echo $author['lastname'] . (isset($author['firstname']) ? ', ' . $author['firstname'] : '') . '; ';
                                endif;
                            endforeach;?>
	                    </span>
	                    <br>
	                    <strong><em><?php echo $entry['pubtitle']; ?></em></strong>
	                    <br>
	                </li>
	        <?php endforeach;?>
        </ul>
    <?php endforeach;
endif;
