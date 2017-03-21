<div id="univis-personenindex">
    <p class="groupindex">
        <?php if ($daten['optionen']['Zeige_Sprungmarken']) : ?>
        <?php foreach ($daten['gruppen'] as $v) : ?>
        <a href="#<?php echo $person['name'];?>"><?php echo $person['name'];?></a>&nbsp;
        <?php endforeach; ?>
        <?php endif; ?>
    </p>

    <?php foreach ($daten['gruppen'] as $gruppe) : ?>
    <h2><a name="<?php echo $gruppe['name'];?>"><?php echo $gruppe['name'];?></a></h2>
    <ul>
        <?php foreach ($gruppe['personen'] as $person) : ?>
        <li class="vcard" itemprop="name" class="person liste-person" itemscope itemtype="http://schema.org/Person">
            <span class="fn n">
                <a class="url" href="http://univis.uni-erlangen.de/prg?search=persons&id=<?php echo $person['id'];?>&show=info">
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
            </span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
</div>