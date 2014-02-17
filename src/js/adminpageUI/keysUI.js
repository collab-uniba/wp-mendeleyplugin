var $j = jQuery.noConflict()


//show window mendeley
function windowMendeley(titleWindow, url){

	var iframe=$j('<iframe>')
		iframe
			  .attr('id','iframe-publications')
			  .attr('src',url)


	
	//progress image
	var img=$j('<img>')

		img
			.attr('id','img-import')
			.attr('src','../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/images/progress.gif')

	var height=$j(window).height()*0.60
	var width=$j(window).width()*0.60
	var top=height/2
	var left=width/2
	var windowPr=$j('<div>')

	//set up dialog window
	windowPr
		.attr('id','div-mendeley')
		.html(iframe)
		.dialog({

		 height:height,
		 width:width,
		 top:height/2,
		 left:width/2,
		 title: titleWindow,
		 modal: true,
		 closeOnEscape: false,
		 close: function(event, ui) { closeWindowMendeley() }//hide X button
		 })
		

}

//close window progress
function closeWindowMendeley(){

	$j('#div-mendeley').dialog("destroy")
	location.reload()
	
}
