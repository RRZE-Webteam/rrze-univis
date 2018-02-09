(function () {
    tinymce.PluginManager.add('univis_shortcode', function (editor) {
        editor.addMenuItem('shortcode_univis', {
            text: editor.getLang('rrze_univis_mce_plugin.add_univis'),
            context: 'tools',
            onclick: function () {
                editor.insertContent('[univis number=""]');
            }
        });
    });
})();
