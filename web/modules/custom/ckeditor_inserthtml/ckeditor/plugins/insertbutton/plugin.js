/*********************************************************************************************************/
/**
 * insertbutton plugin for CKEditor 4.x (Author: Lokeshwari ;)
 * version:	2.0
 * Modified from original: insertbutton plugin for CKEditor
 * mod-version:	 1.0
 */
/*********************************************************************************************************/

CKEDITOR.plugins.add('insertbutton',   
  {    
    icons: 'insertbutton',
    init: function( editor ) { 
		editor.addCommand( 'insertbutton', new CKEDITOR.dialogCommand( 'insertbuttonDialog' ) );
        editor.ui.addButton( 'insertbutton', {
            label: 'Insert Button',
            command: 'insertbutton',
            toolbar: 'insert',
            icon : this.path + 'simplebutton.png'
        });

        CKEDITOR.dialog.add( 'insertbuttonDialog', this.path + 'dialogs/insertbutton.js' );
    }    
});