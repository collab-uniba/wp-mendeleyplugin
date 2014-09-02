( function() {
    tinymce.PluginManager.add( 'collab_mendeley', function( editor, url ) {
        var tmp_path = url.split("/admin");
        var icon_path = tmp_path[0] + '/assets/img/icon_mendeley.png';
        // console.log(icon_path);
        // Add a button that opens a window
        editor.addButton( 'collab_mendeley_button', {

            //text: 'Mendeley',
            //icon: 'icon cmp-mendeley-button',
            image: icon_path,
            onclick: function() {
                // Open window
                editor.windowManager.open( {
                    title: 'Mendeley Authored Publications',
                    body: [{
                        type: 'textbox',
                        name: 'title',
                        label: 'Publications List Title'
                    },
                        {
                            type: 'textbox',
                            name: 'titletag',
                            label: 'Title HTML Tag'
                        },
                        {
                            type: 'textbox',
                            name: 'sectiontag',
                            label: 'Sections HTML Tag'
                        }],
                    onsubmit: function( e ) {
                        // Insert content when the window form is submitted
                        editor.insertContent( '[mendeley titletag=' + e.data.titletag + ' sectiontag=' + e.data.sectiontag + ']' + e.data.title + '[/mendeley]' );
                    }

                } );
            }

        } );

    } );

} )();