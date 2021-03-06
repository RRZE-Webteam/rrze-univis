(function() {
    tinymce.PluginManager.add('rrze_univis_shortcode', function(editor) {

    var menuItems = [];
    menuItems.push({
        text: 'Lehrveranstaltungen',
        icon: 'paste', 
        menu: [
            {
                type: 'menuitem',
                text: 'Alle',
                onclick: function() {
                    editor.insertContent('[univis task="lehrveranstaltungen-alle"]');
                }
            },
            {
                type: 'menuitem',
                text: 'Einzelne',
                onclick: function() {
                    editor.insertContent('[univis task="lehrveranstaltungen-einzeln" lv_id=""]');
                }
            },
        ]
    });
    menuItems.push({
        text: 'Mitabeiter',
        icon: 'user', 
        menu: [
            {
                type: 'menuitem',
                text: 'Alle',
                onclick: function() {
                    editor.insertContent('[univis task="mitarbeiter-alle"]');
                }
            },
            {
                type: 'menuitem',
                text: 'Einzel',
                onclick: function() {
                    editor.insertContent('[univis task="mitarbeiter-einzeln" univisid=""]');
                }
            },
            {
                type: 'menuitem',
                text: 'Organisation',
                onclick: function() {
                    editor.insertContent('[univis task="mitarbeiter-orga"]');
                }
            },
            {
                type: 'menuitem',
                text: 'Telefonbuch',
                onclick: function() {
                    editor.insertContent('[univis task="mitarbeiter-telefonbuch"]');
                }
            },
        ]
    });
    menuItems.push({
        text: 'Publikationen',
        icon: 'preview', 
        menu: [
            {
                type: 'menuitem',
                text: 'Alle',
                onclick: function() {
                    editor.insertContent('[univis task="publikationen"]');
                }
            },
            {
                type: 'menuitem',
                text: 'Einer Person',
                onclick: function() {
                    editor.insertContent('[univis task="publikationen" id=""]');
                }
            },
        ]
    });


    editor.addMenuItem('insertShortcodesRRZEUnivIS', {
        icon: 'orientation', 
        text: 'RRZE-UnivIS',
        menu: menuItems,
        context: 'insert',
    });
});
})();