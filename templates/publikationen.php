<?php if ($data):
    foreach ($data as $year => $entries): ?>
		    <?php echo '<h' . $this->atts['hstart'] . '>' . $year . '</h' . $this->atts['hstart'] . '>'; ?>
		        <ul class="univis-publication-byyear">
		            <?php foreach ($entries as $entry): ?>
		                <li>
		                    <span>
		                        <?php
                                $authors = isset($entry['authors']) && is_array($entry['authors']) ? $entry['authors'] : [];

                                foreach ($authors as $author) {
                                    if (!is_array($author)) {
                                        continue;
                                    }

                                    $lastname = isset($author['lastname']) ? (string)$author['lastname'] : '';
                                    $firstname = isset($author['firstname']) ? (string)$author['firstname'] : '';
                                    $displayName = trim($lastname . ($firstname !== '' ? ', ' . $firstname : ''));

                                    if ($displayName === '') {
                                        $displayName = isset($author['name']) ? (string)$author['name'] : '';
                                    }

                                    if ($displayName === '') {
                                        continue;
                                    }

                                    if (isset($author['person_id'])) {
                                        $url = trailingslashit(get_permalink()) . 'univisid/' . $author['person_id'];
                                        echo '<a href="' . esc_url($url) . '">' . esc_html($displayName) . '</a>; ';
                                    } else {
                                        echo esc_html($displayName) . '; ';
                                    }
                                }
                                ?>
		                    </span>
		                    <br>
		                    <strong><em><?php echo isset($entry['pubtitle']) ? esc_html((string)$entry['pubtitle']) : ''; ?></em></strong>
		                    <br>
		                </li>
		        <?php endforeach;?>
	        </ul>
	    <?php endforeach;
	endif;
