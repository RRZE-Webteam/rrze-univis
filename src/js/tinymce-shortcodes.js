(function() {
    tinymce.PluginManager.add('rrze_shortcode', function(editor) {
        if (typeof phpvar !== 'undefined') {
            for(i=0; i < phpvar.length; i++){
                shortcode = phpvar[i].shortcode;
                editor.addMenuItem('insert_' + phpvar[i].name, {
                    id: i,
                    icon: phpvar[i].icon,
                    text: phpvar[i].title,
                    context: 'insert',
                    onclick: function() {
                        editor.insertContent(phpvar[this.settings.id].shortcode);
                    }
                });
            }
        }
    });
})();