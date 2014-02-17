//avoid conflict with php and wordpress
var $j = jQuery.noConflict()

//associate ui appearance to main elements
function setUIElement(){

	$j(document).tooltip()
	
	$j('#div-tabs').tabs({
						/*
						activate: function( event, ui ) {

								//get selected tab id
								var id=ui.newTab.children('a').attr('id')
								
								if(id=='ui-id-2'){

									//show authors when 2° has been selected
									
								}


								if(id=='ui-id-3'){

									//show order fields when 3° has been selected
									

								}



						}

					*/
					})
	
	
	$j('#button-getKey').button()
	$j('#button-newAuthor')
							.button()
							.attr('title','Insert new author and import his publications')

	$j('#button-deleteAuthor')
							.button()
							.attr('title','Delete selected author and related publications')
							.hide()

	$j('#button-showAuthorPublications')
							.button()
							.attr('title','Show related publications')
							.hide()

	$j('#button-showExcludedPublication')
							.button()
							.attr('title','Show excluded publications. If id_author is specified, show related excluded publications')
							.hide()

	$j('#button-updAuthorPublications')
							.button()
							.attr('title','Update related publications')
							.hide()

	$j('#txt-id_authorPubl')
							
							.attr('title','Insert id_author to show related publications. If empty, will shown all publications')

	$j('#button-showPublications')
							.button()
							.attr('title','Show publications. If id_author is specified, show related publications')


	$j('#button-previewAuthorPublications')
							.button()
							.attr('title','Preview publications')
							.hide()

	$j('#button-orderTypePublications')
							.button()
							.attr('title','Set publications order type')

	$j('#div-formatToolbar')
							.hide()
	
	

}