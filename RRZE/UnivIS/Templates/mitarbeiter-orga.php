<div id="univis-personenindex">
    <ul class="groupindex">
        <?php if ($daten['optionen']['zeige_sprungmarken']) : // wird nur angezeigt, wenn es mehr als eine OrgUnit gibt ?>
        <?php foreach ($daten['gruppen'] as $v) : ?>
        <li><a href="#<?php echo $v['name'];?>"><?php echo $v['name'];?></a></li>
        <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <?php foreach ($daten['gruppen'] as $gruppe) : ?>
    <h2><a name="<?php echo $gruppe['name'];?>"><?php echo $gruppe['name'];?></a></h2>
    <ul>
        <?php foreach ($gruppe['personen'] as $person) : 
            $p = array();
            $pers = array();
            $fullname = '';
            $out = '';
            ?>
        <li itemprop="name" class="person liste-person" itemscope itemtype="http://schema.org/Person">
                <?php $url = get_permalink() . 'univisid/' . $person['id']; ?>
                <a class="url" href="<?php echo $url;?>">
                    <?php if (!empty($person['title'])) : ?>
                    <span class="honorific-prefix" itemprop="honorificPrefix">
                        <acronym title="<?php echo $person['title_long'];?>"><?php echo $person['title'];?></acronym>
                    </span>
                    <?php endif; ?>
                    <span class="given-name" itemprop="givenName"><?php echo $person['firstname'];?></span>
                    <span class="family-name" itemprop="familyName"><?php echo $person['lastname'];?><?php if (!empty($person['atitle'])) : ?>, <?php endif; ?></span>
                    <?php if (!empty($person['atitle'])) : ?>
                    <span class="honorific-suffix" itemprop="honorificSuffix">
                        <acronym title="<?php echo $person['atitle_long'];?>"><?php echo $person['atitle'];?></acronym>
                    </span>
                    <?php endif; ?>
                </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
</div>
