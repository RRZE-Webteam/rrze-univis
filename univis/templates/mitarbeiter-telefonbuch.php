<div id="univis-personenindex">
    <p class="groupindex">
        <?php if ($daten['optionen']['zeige_sprungmarken']) : ?>
        <?php foreach ($daten['gruppen'] as $v) : ?>
        <a href="#<?php echo $v['name'];?>"><?php echo $v['name'];?></a>&nbsp;
        <?php endforeach; ?>
        <?php endif; ?>
    </p>

    <?php foreach ($daten['gruppen'] as $gruppe) : ?>
    <h2><a name="<?php echo $gruppe['name'];?>"><?php echo $gruppe['name'];?></a></h2>
    <ul>
        <?php foreach ($gruppe['personen'] as $person) : ?>
        <li class="vcard" itemprop="name" class="person liste-person" itemscope itemtype="http://schema.org/Person">
            <span class="fn n">
                <a class="url" href="univisid/<?php echo $person['id'];?>">
                    <?php if (!empty($person['lastname'])) : ?>                    
                    <span class="family-name" itemprop="familyName"><?php echo $person['lastname'];?><?php if (!empty($person['firstname'])) : ?>, <?php endif; ?></span>
                    <?php endif;?>
                    <?php if (!empty($person['firstname'])) : ?>
                    <span class="given-name" itemprop="givenName"><?php echo $person['firstname'];?></span>
                    <?php endif;?>
                </a>
            </span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
</div>