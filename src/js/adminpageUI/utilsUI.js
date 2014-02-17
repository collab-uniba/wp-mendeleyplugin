//avoid conflict with php and wordpress
var $j = jQuery.noConflict()

//show window progress
function windowProgress(titleWindow){

	
	//progress image
	var img=$j('<img>')

		img
			.attr('id','img-import')
			.attr('src','../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/images/progress.gif')

	var windowPr=$j('<div>')

	//set up dialog window
	windowPr
		.attr('id','div-progress')
		.html(img)
		.dialog({
		 title: titleWindow,
		 modal: true,
		 closeOnEscape: false,
		 open: function(event, ui) { $j(".ui-dialog-titlebar-close").hide() }//hide X button
		 })

}

//close window progress
function closeWindowProgress(){

	$j('#div-progress').dialog("destroy")
}


//show window message
function windowMessage(titleWindow, message){


	var windowMsg=$j('<div>')

	//set up dialog window
	windowMsg

		.html(message)
		.dialog({
		 title: titleWindow,
		 modal: true,
		 closeOnEscape: false,
		 buttons: [ { text: "Close", 
		 			click: function() { 

		 					$j( this ).dialog( "destroy" )
		 					
		 				} } ]
		 })


}



//show window confirm 
function windowConfirm(titleWindow, message,callback){

	var windowMsg=$j('<div>')
	
	//set up dialog window
	windowMsg

		.html(message)
		.dialog({
		 title: titleWindow,
		 modal: true,
		 closeOnEscape: false,
		 buttons: [ { text: "Yes", 
		 			click: function() { 

		 					callback()//callback function to execute
		 					$j( this ).dialog( "destroy" )

		 					}
		 					
		 				},

		 				{ text: "No", 
		 					click: function() { 

		 					
		 					$j( this ).dialog( "destroy" )
		 					
		 					
		 				}



		 				 } ]
		 })


}


//button resize resume window dimension
function setButtonResizeWindow(parentWindow){


	var buttonResizeWindow=$j('<button>')
		buttonResizeWindow
						
						.attr('class','button-resizeWindow')
						.attr('title','resize')
						.appendTo(parentWindow.parents('.ui-dialog').find('.ui-dialog-titlebar'))
						.click(function( event, ui ) {

								
								var resize=$j(this).attr('resize')
								
								// set max dimensions
								if (resize==null){

									var height=$j(window).height()*0.93
									var width=$j(window).width()*0.99
									parentWindow.dialog('option','width',width)
									parentWindow.dialog('option','height',height)
									parentWindow.dialog('option','top',40)
									parentWindow.dialog('option','left',0)

									$j(this)
											.attr('resize','resized')
											.button('option','icons',{ primary: 'ui-icon-newwin'})
											.children('span').css('left','1px')


								}

								//set minim dimension
								else{

									var height=$j(window).height()*0.70
									var width=$j(window).width()*0.70
									var top=height/2
									var left=width/2
									parentWindow.dialog('option','width',width)
									parentWindow.dialog('option','height',height)
									parentWindow.dialog('option','top',top)
									parentWindow.dialog('option','left',left)

									$j(this)
											.removeAttr('resize')
											.button('option','icons',{ primary: 'ui-icon-arrow-4-diag'})
											.children('span').css('left','1px')

								}

						
							})


						.button({ 

							icons: { primary: "ui-icon-arrow-4-diag"}
							
							}


							)
						.children('span').css('left','1px')



}

function buttonAddShortcode(){

//var h2Idx = edButtons.length;


}