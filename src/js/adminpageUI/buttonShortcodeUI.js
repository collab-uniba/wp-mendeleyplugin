var id_authors

(function() {


	tinymce.create('tinymce.plugins.MAPshortcode', {

		init :
			 function(ed, url) {

					ed.addButton('buttonMAPshortcode', {
						title : 'Add MAP Shortcode',
						image : '../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/images/icons/shortcodeIcon.png',
						onclick : function() {
							
							windowSelectid_authorShortcode()
						},

						onMouseDown: function() {

							$j(".ui-widget-overlay").remove()
						}
					});
				},
				createControl : function(n, cm) {
					return null;
				},
				getInfo : function() {
					return {
						longname : "MAP Shortcode",
						author : 'Nicola Musicco'
						
					};
				}
	});
	tinymce.PluginManager.add('mapshortcode', tinymce.plugins.MAPshortcode);

})()



function selectId_authorShortcode(){

	//$j('#select-id_authorShortcode').html('')
	//authors 
	authors=id_authors


	
	var selectid_author=$j('<select>')

		selectid_author
					.attr('id','select-id_authorShortcode')
					

	//empty option
	var option=$j('<option>')

			option
				  .val('')
				  .html('')
				  .appendTo(selectid_author)

	var size=authors.length
	//put authors
	for (var i = 0 ; i < size; i++) {
		
		var option=$j('<option>')

			option
				  .val(authors[i][0].nick)
				  .html(authors[i][0].fname+' '+authors[i][0].sname)
				  .appendTo(selectid_author)


	}

	return selectid_author

}


function windowSelectid_authorShortcode(){


	selectid_author=selectId_authorShortcode()


	var windowMsg=$j('<div>')
	
	//set up dialog window
	windowMsg

		
		.html(selectid_author)
		.css('text-align','center')
		.dialog({
		 title: 'Select author',
		 modal: true,
		 closeOnEscape: false,
		 
		 buttons: [ { text: "Ok", 
		 			click: function() { 

		 					var id_author=selectid_author.val()
		 					
		 					if(id_author!=''){

		 							var shortcode='[publications id_author="'+id_author+'"]'
		 							
				 					tinymce.activeEditor.setContent(tinymce.activeEditor.getContent()+' '+shortcode)
				 					
				 					$j( this ).dialog( "destroy" )

		 					}
		 					else windowMessage('Error!', 'Select author...')

		 					}
		 					
		 				},

		 				{ text: "Close", 
		 					click: function() { 

		 					
		 					$j( this ).dialog( "destroy" )
		 					
		 					
		 				}



		 				 } ]
		 })
}