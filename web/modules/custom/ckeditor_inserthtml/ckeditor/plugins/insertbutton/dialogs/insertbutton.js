/*********************************************************************************************************/
/**
 * inserthtml plugin for CKEditor 4.x (Author: gpickin ; email: gpickin@gmail.com)
 * version:	2.0
 * Released: On 2015-03-10
 * Download: http://www.github.com/gpickin/ckeditor-insertbutton 
 *
 *
 * Modified from original: insertbutton plugin for CKEditor 3.x (Author: Lokeshwari ;)
 * mod-version:	 1.0
 * mod-Released: On 2009-12-11
 */
/*********************************************************************************************************/

CKEDITOR.dialog.add('insertbuttonDialog',function(editor){	

	return{
		title:'Insert Button',
		minWidth:380,
		minHeight:220,
		contents:[
			{	id:'buttoninfo',
				label:'HTML',
				elements:[
				  { type:'text',
				    id:'insertbutton_link',
					label:'Button Link'	
				  },
				  { type:'text',
				    id:'insertbutton_title',
					label:'Button Text'
				  }
				]
			}
		],
		onOk: function() {
			var sLink=this.getValueOf('buttoninfo','insertbutton_link');
			var sText=this.getValueOf('buttoninfo','insertbutton_title');   
			if ( sLink.length > 0 ) 
			editor.insertHtml(`<a href="${sLink}" class="btn-custom">${sText}</a>`); 

		}
	};
});
