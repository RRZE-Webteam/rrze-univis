(function() {

    var plugin_is_usable = tinymce.get('content').plugins.faurteshortcodes;
    console.log(plugin_is_usable);
    console.log(tinymce.get('content'));
    //console.log(tinymce.get('content').menuItems.InsertShortcodes.context);


    tinymce.PluginManager.add('rrzeunivisshortcodes', function(editor) {

        var menuItems = [];
        menuItems.push({
            text: 'Lehrveranstaltungen',
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
                        editor.insertContent('[univis task="mitarbeiter-telefonbuch"]');
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
        icon: 'code',
        text: 'RRZE-UnivIS',
        menu: menuItems,
        context: 'insert',
    });
});
})();