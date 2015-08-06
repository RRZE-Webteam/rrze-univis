(function() {

    tinymce.PluginManager.add('univisrteshortcodes', function( editor )
    {
		
		editor.addMenuItem('shortcode_univis', {
                        text: 'UnivIS Shortcode einfügen',
                        context: 'tools',
                        onclick: function() {
                                editor.insertContent('[univis number=""]');
                        }
                });

    });
})();