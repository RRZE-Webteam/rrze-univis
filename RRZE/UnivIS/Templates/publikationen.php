<?php if ($daten['years']) :
    foreach ($daten['years'] as $years) : ?>
        <h2><?php echo $years['title']; ?></h2>  
        <ul>
            <?php foreach ($years['data'] as $data) : ?>
                <li style="margin-bottom: 10px;">  
                    <span>
                        <?php foreach ($data['authors'] as $authors) :
                            foreach ($authors['author'] as $author) :
                                if (array_key_exists('full_profile', $author['pkey'])) :
                                    $url = get_permalink() . 'univisid/' . $author['pkey']['full_profile'][0]['id'];
                                    ?>
                                    <a href="<?php echo $url; ?>"><?php echo $author['pkey']['full_profile'][0]['lastname'] . ', ' . $author['pkey']['full_profile'][0]['firstname']; ?></a>;
                                <?php
                                else :
                                    echo $author['pkey'][0]['name'] . ';';
                                endif; ?>
                            <?php endforeach;
                        endforeach; ?>         
                    </span>
                    <br>
                    <strong><em><?php echo $data['pubtitle']; ?></em></strong>
                    <br />
                </li>
        <?php endforeach; ?> 
        </ul>
    <?php endforeach;
endif; 
