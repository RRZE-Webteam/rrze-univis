<div id="univis-personenindex">
    <?php foreach ($daten['gruppen'] as $gruppe) : ?>
    <h4><?php echo $gruppe['name']; ?></h4>
    <ul>
        <?php foreach ($gruppe['personen'] as $person) : ?>
            <li>                
                <?php if (!empty($person['lastname'])) : ?>
                    <span itemprop="name" class="person liste-person" itemscope itemtype="http://schema.org/Person">
                    <?php if (!empty($person['title'])) : ?>
                    <span itemprop="honorificPrefix">
                        <acronym title="<?php echo $person['title_long'];?>"><?php echo $person['title'];?></acronym>
                    </span>
                    <?php endif; ?>
                    <?php $url = get_permalink() . 'univisid/' . $person['id']; ?>
                    <a class="url" href="<?php echo $url;?>" itemprop="name">
                        <span itemprop="familyName"><?php echo $person['lastname'];?></span><?php if (!empty($person['firstname'])) : ?>, <?php endif; ?>
                        <?php if (!empty($person['firstname'])) : ?>
                        <span itemprop="givenName"><?php echo $person['firstname'];?></span><?php if (!empty($person['atitle'])) : ?>, <?php endif; ?>
                        <?php endif; ?>
                    </a>
                    <?php if (!empty($person['atitle'])) : ?>
                    <span itemprop="honorificSuffix"><acronym title="<?php echo $person['atitle_long'];?>"><?php echo $person['atitle'];?></acronym></span>
                    <?php endif; ?>
                    </span>
                    <?php $location = $person['locations'][0]['location'][0];  ?>
                                <?php if (!empty($location['tel'])) : ?>
                                <span class="person-info-phone">
                                    <span itemprop="telephone">, Tel. <?php echo $location['tel']; ?></span>
                                </span>
                                <?php endif; 
                endif;
                if ( $suffix!='' && !empty( $person[$text] ) ): 
                    $text_out = $person[$text];
                elseif ( !empty( $person['text'] ) ) :
                    $text_out = $person['text'];
                endif;
                if ( !empty( $text_out ) ) : ?>
                <span><?php echo $text_out; ?></span>
                <?php endif; ?>
            </li>            
        <?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
</div>