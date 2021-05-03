<?php if ($data) :
    foreach ($data as $year => $entries) : ?>
    <?php echo '<h' . $this->atts['hstart'] . '>' . $year . '</h' . $this->atts['hstart'] . '>'; ?>
        <ul>
            <?php foreach ($entries as $entry) : ?>
                <li style="margin-bottom: 10px;">  
                    <span>
                        <?php foreach ($entry['authors'] as $author) :
                            if (isset($author['person_id'])) : 
                                $url = get_permalink() . 'univisid/' . $author['person_id'];
                                echo '<a href="' . $url . '">' . $author['lastname'] . (isset($author['firstname'])?', ' . $author['firstname']:'') . '</a>; ';
                            else :    
                                echo $author['lastname'] . (isset($author['firstname'])?', ' . $author['firstname']:'') . '; ';
                            endif;
                        endforeach; ?>         
                    </span>
                    <br>
                    <b><i><?php echo $entry['pubtitle']; ?></i></b>
                    <br />
                </li>
        <?php endforeach; ?> 
        </ul>
    <?php endforeach;
endif; 
