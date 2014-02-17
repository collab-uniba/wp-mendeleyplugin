//avoid conflict with php and wordpress
var $j = jQuery.noConflict()

//get authors and put them into select box preview
function selectBoxPreviewPublications(authors){

	$j('#select-authorPreview').html('')
	var size=authors.length

	//empty option
	var option=$j('<option>')

			option
				  .val('')
				  .html('')
				  .appendTo($j('#select-authorPreview'))

	//put authors
	for (var i = 0 ; i < size; i++) {
		
		var option=$j('<option>')

			option
				  .val(authors[i][0].nick)
				  .html(authors[i][0].fname+' '+authors[i][0].sname)
				  .appendTo($j('#select-authorPreview'))


	}
	//event called when an author is selected
	$j('#select-authorPreview').change(function(eventObject){


		var id_author=$j(this).children('option:selected').val()
		
		if(id_author!='') {
			
			 $j(this).val('')
			 //show preview publications
			 getPreviewAuthorPublications(id_author)

		}

		//avoid event propagation event
		eventObject.preventDefault()
  		eventObject.stopImmediatePropagation()


	})


}

//show in which order publications will be shown by type
function windowOrderTypePublications(orderPublications){


	//remove previous dialog
	$j('#div-orderTypePublications').remove()

	var tableContainer=$j('<table>')

	tableContainer
				  .attr('id','table-orderTypePublications')

	//show window order type publications
	var windowOrderTypePublic=$j('<div>')

		windowOrderTypePublic
						  .html(tableContainer)
						  .attr('id','div-orderTypePublications')
						  .dialog({
						  	 title: 'Publications Order Type - Drag and Drop type publication to change order',
							 modal: true,
							 open: function( event, ui ) {

							 		//set reduced dimensions
							 		var height=$j(window).height()*0.70
									var width=$j(window).width()*0.70
									var top=height/2
									var left=width/2
									$j(this).dialog('option','width',width)
									$j(this).dialog('option','height',height)
									$j(this).dialog('option','top',top)
									$j(this).dialog('option','left',left)
									//add resize button
							 		setButtonResizeWindow($j(this))


									},
							 close: function( event, ui ) {

							 				//destroy details dialog
								 			$j( this ).dialog( "destroy" )


							 			},
							 buttons: [{ 
								 			id:"button-setOrderPublications",
								 		    text: "Update", 
								 			click: function(){

								 				//get table content and set order type publications
								 				 setOrderTypePublications(orderPublications)
								 				 $j( this ).dialog( "destroy" )

								 			}},

									 		{ 
								 			
								 		    text: "Close", 
								 			click: function(){

								 				
												//destroy  dialog
								 				$j( this ).dialog( "destroy" )

								 			}}


							 			]})
	//create table
	var caption=$j('<caption>')

		caption
			   .html('Publications Order Type')
			   .appendTo(tableContainer)

	var thorder=$j('<th>')

		thorder
				  .html('Order')
				  .appendTo(tableContainer)

	var thtype=$j('<th>')

		thtype
				  .html('Type')
				  .appendTo(tableContainer)


	var size=orderPublications.length

	for (var i=0; i<size; i++){


		var tr=$j('<tr>')

			tr.appendTo(tableContainer)

		var tdorder=$j('<td>')

			tdorder
				.appendTo(tr)
				
				.html(orderPublications[i].orderType)
				.css('border-left','2px solid #c5dbec')
				

		var tdtype=$j('<td>')

			tdtype
				.appendTo(tr)
				.html(orderPublications[i].type)
				.attr('id',orderPublications[i].orderType)
				.attr('class','td-TypePublications')
				.css('border-left','2px solid #c5dbec')
				.css('border-right','2px solid #c5dbec')
				//event called when drag table cell
				.draggable({

						axis: "y"//cell can be moved only vertically

						},

						{ 

						containment: "#table-orderTypePublications" //cell can be moved only into parent table

						},

						{ 
						revert: true //cell return on the original position

						})
				//function called when table cell il dropped
				.droppable({
					drop: function( event, ui ) {

							//ui is dragged element
						   var draggedElement=ui.draggable.html()
						   var droppedElement=$j(this).html()
					       
					       //exchange cell content
					       $j(this).html(draggedElement)
					       ui.draggable.html(droppedElement)

					       var indexDrag=ui.draggable.attr('id')
					       var indexDrop=$j(this).attr('id')
					       orderPublications[indexDrag].type=droppedElement
					       orderPublications[indexDrop].type=draggedElement
					       

					      }})

				//hover functions on table rows
			.hover(

				//mouse in
				function(){

			         $j(this)
					     .css('background-color','rgb(38,153,204)')

			   },

			   //mouse out
				function(){

					
						$j(this)
					     .css('background-color','white')

				})



	}

}


//create format toolbar passing css parameters
function formatToolbar(parametersCSS){

	$j('#div-formatToolbar').show()
	
	//color palette
	$j('#button-fontColor')
					.attr('title','Font color')
					.button({
				     
				     
				      })
					//call spectrum plugin 
					.spectrum({
						showInput: true,
						showPalette:true,	
						preferredFormat:'hex',						   
					    chooseText: "Choose",
						cancelText: "Close",
						color:$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('color'),
						hide: function(color){

							//set color to sample cell content in hex format 
							setColor(color.toHex())
							

						},

						move: function(color){

							//setColor(color.toHex())
							

						}
					})

	$j('.sp-cancel').button({})//button that close palette
				 				      

	$j('.sp-choose').button({})//button that confirm color
					


	$j('#div-fontButtons').buttonset()//set button
	$j('#div-fontSize').buttonset()//set button

	$j('#select-fontFamily')
							.attr('title','Font family')//tooltip
	//set font family

	$j('#select-fontFamily')
							.val(parametersCSS.fontFamily)
							//function  called when a font type is selected
							.change(function(eventObject){

								var fontFamily=$j('#select-fontFamily option:selected').val()
								//get content from field table and save css properties
								setFontFamily(fontFamily)
								//avoid event propagation event
								eventObject.preventDefault()
					      		eventObject.stopImmediatePropagation()
							})

	//set font weight
	$j('#button-fontBold')
					.button({
				     
				     
				      })
					//function called when button is pressed
					.click(function(eventObject){

						//get content from field table and save css properties
						setBold()
						//avoid event propagation event
						eventObject.preventDefault()
			      		eventObject.stopImmediatePropagation()

					})
					

	//set format toolbar
	if (parametersCSS.fontWeight=='bold'){

		 $j('#button-fontBold').attr('checked')

		 $j('#lbl-fontBold')
				      	.attr('title','Normal')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-active')
				      	.attr('aria-pressed','true')


		$j('#button-fontBold').attr('checked',true)


	}


	else {
			$j('#lbl-fontBold')
				      	.attr('title','Bold')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only')
				      	.attr('aria-pressed','false')

			$j('#button-fontBold').removeAttr('checked')
		}


		

	//set font style
	$j('#button-fontItalic')
					.button({
				     
				     
				      })
					//function called when button is pressed
					.click(function(eventObject){
						//get content from field table and save css properties
						setItalic()
						//avoid event propagation event
						eventObject.preventDefault()
			      		eventObject.stopImmediatePropagation()

					})
	//set format toolbar
	if (parametersCSS.fontStyle=='italic') {


		$j('#button-fontItalic').attr('checked')

		 $j('#lbl-fontItalic')
				      	.attr('title','Normal')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-active')
				      	.attr('aria-pressed','true')


		$j('#button-fontItalic').attr('checked',true)


	}


	else {
			$j('#lbl-fontItalic')
				      	.attr('title','Italic')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only')
				      	.attr('aria-pressed','false')

			$j('#button-fontItalic').removeAttr('checked')
		}


	//set underlined text
	$j('#button-fontUnderline')
					.button({
				     
				     
				      })
					//function called when button is pressed
					.click(function(eventObject){

						//get content from field table and save css properties
						setUnderline()
						//avoid event propagation event
						eventObject.preventDefault()
			      		eventObject.stopImmediatePropagation()

					})

	//set format toolbar
	if (parametersCSS.textDecoration=='underline') {


		$j('#button-fontUnderline').attr('checked')

		 $j('#lbl-fontUnderline')
				      	.attr('title','No underline')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-active')
				      	.attr('aria-pressed','true')


		$j('#button-fontUnderline').attr('checked',true)


	}


	else {
			$j('#lbl-fontUnderline')
				      	.attr('title','Underline')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only')
				      	.attr('aria-pressed','false')

			$j('#button-fontUnderline').removeAttr('checked')

		}

	//decrease font size
	$j('#button-fontDescrease')
							.button({
				     
				     
				      		})
				      		//function called when button is pressed
				      		.click(function(eventObject){

				      			var fontSize=parseInt($j('#input-fontSize').val())
								var measure=$j('#select-fontMeasure').val()
								$j('#input-fontSize').val(fontSize-1)
								//get content from field table and save css properties
								setFontSize(fontSize-1,measure)
								//avoid event propagation event
								eventObject.preventDefault()
					      		eventObject.stopImmediatePropagation()
				      			
				      		})
				      		.attr('title','Decrease font size')

	//increase font size
	$j('#button-fontIncrease')
							.button({
				     
				     
				      		})
				      		//functionc called when button is pressed
				      		.click(function(eventObject){

				      			var fontSize=parseInt($j('#input-fontSize').val())
								var measure=$j('#select-fontMeasure').val()
								$j('#input-fontSize').val(fontSize+1)
								//get content from field table and save css properties
								setFontSize(fontSize+1,measure)
								//avoid event propagation event
								eventObject.preventDefault()
					      		eventObject.stopImmediatePropagation()

				      		})
				      		.attr('title','Increase font size')

	//set font size
	
	$j('#input-fontSize')
						.val(parametersCSS.fontSize.match(/\d/g).join(''))
						//function called when send button is pressed
						.keypress(function(eventObject){

							//if has been pressed enter key
							if (eventObject.which==13){

								var fontSize=parseInt($j(this).val())
								var measure=$j('#select-fontMeasure').val()
								//get content from field table and save css properties
								setFontSize(fontSize,measure)
								//avoid event propagation event
								eventObject.preventDefault()
					      		eventObject.stopImmediatePropagation()
								
							}
						})
						//function called when focus is lost
						.focusout(function(eventObject){

							
								var fontSize=parseInt($j(this).val())
								var measure=$j('#select-fontMeasure').val()
								//get content from field table and save css properties
								setFontSize(fontSize,measure)
								//avoid event propagation event
								eventObject.preventDefault()
					      		eventObject.stopImmediatePropagation()
								
							
						})
   //set font measure
	$j('#select-fontMeasure')
							.val(parametersCSS.fontSize.match(/\D/g).join(''))//get measure using regular expression
							//function called when a measure is selected
							.change(function(eventObject){

								var fontSize=parseInt($j('#input-fontSize').val())
								var measure=$j('#select-fontMeasure option:selected').val()
								//get content from field table and save css properties
								setFontSize(fontSize,measure)
								//avoid event propagation event
								eventObject.preventDefault()
					      		eventObject.stopImmediatePropagation()

							})

	$j('#div-fieldOrder').buttonset()//set button

	//move up a field
	$j('#button-moveUp')
						.button({

							
				      	})
				      	//function called when button is pressed
				      	.click(function(eventObject){
				      			
				      		//get content from field table and save order fields
				      		moveField(-1)
				      		//avoid event propagation event
							eventObject.preventDefault()
				      		eventObject.stopImmediatePropagation()
							
				      	})
				      	
				      	.attr('title','Move up field')
	//move down a field
	$j('#button-moveDown')
						.button({

								})
							//function called when a button is pressed
				     		.click(function(eventObject){

				     			//get content from field table and save order fields
				     			moveField(1)
				     			//avoid event propagation event
								eventObject.preventDefault()
					      		eventObject.stopImmediatePropagation()

				      	})
				  
				      	.attr('title','Move down field')

	//set shown/hidden field
	$j('#button-showHide')
						.button({
				     
				     
				      	})
						//function called when a button is pressed
				      	.click(function(eventObject){
				      		//get content from field table and save visibility fields
				      		setShowHide()
				      		//avoid event propagation event
							eventObject.preventDefault()
				      		eventObject.stopImmediatePropagation()
				      	})

	//set label before a field
	$j('#input-labelFor')
						.attr('title','Label associated to field')
						.val(parametersCSS.labelfor)
						//function called when send button is pressed
						.keypress(function(eventObject){

							//if has been pressed enter key
							if (eventObject.which==13){
								
								var labelfor=$j('#input-labelFor').val()
								//get content from field table and save fields labels
								setLabelFor(labelfor)
								//avoid event propagation event
								eventObject.preventDefault()
					      		eventObject.stopImmediatePropagation()
								
							}
						})
						//function called when focus is lost
						.focusout(function(eventObject){

							
								var labelfor=$j('#input-labelFor').val()
								//get content from field table and save fields labels
								setLabelFor(labelfor)
								//avoid event propagation event
								eventObject.preventDefault()
					      		eventObject.stopImmediatePropagation()
								
						
						})

	//set shown button
	if (parametersCSS.shown=='y'){

		$j('#button-showHide')
				      	
				      	.css('background-image','url("../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/images/icons/hide.png")')


	}

	//set hidden button
	else {
			$j('#button-showHide')
				      	
				      	.css('background-image','url("../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/images/icons/show.png")')
		}




	//if field= type, hide button showHide, move up and move down, else show it
	var field=$j('#table-formatOutput tr[selected="selected"]').children('.td-field').html()
	console.log(field)
	if (field=='type') {

		$j('#button-showHide').hide()
		$j('#button-showHide').hide()
		$j('#button-moveUp').hide()
		$j('#lbl-moveUp').hide()
		$j('#button-moveDown').hide()
		$j('#lbl-moveDown').hide()

	}

	else {

		$j('#button-showHide').show()
		$j('#lbl-showHide').show()
		$j('#button-moveUp').show()
		$j('#lbl-moveUp').show()
		$j('#button-moveDown').show()
		$j('#lbl-moveDown').show()

	}

}

//close format toolbar
function closeFormatToolbar(){

	$j('#div-formatToolbar').hide()

}

//create table format publications. This table allows user to set css property, 
//set order fields and set if a field will be shown in final output
function tableFormatPublications(orderField,parametersCSS){

	$j('#div-formatOutput').html('')

	var tableContainer=$j('<table>')

	tableContainer
					.attr('id','table-formatOutput')
					.appendTo($j('#div-formatOutput'))

	var caption=$j('<caption>')

		caption
			   .html('Output Format')
			   .appendTo(tableContainer)

	var thorder=$j('<th>')

		thorder
				  .html('Order')
				  .appendTo(tableContainer)

	var thfield=$j('<th>')

		thfield
				  .html('Field')
				  .appendTo(tableContainer)

	

	var thsample=$j('<th>')

		thsample
				  .html('Sample')
				  .appendTo(tableContainer)

	
	var size=orderField.length

	for (var i=0;i<size;i++){	


		var tr=$j('<tr>')

		  tr

		  	.attr('id',orderField[i].orderField)
		  	.attr('shown',orderField[i].shown)
		  	.attr('field',orderField[i].field)
		  	.attr('label',orderField[i].labelfor)
			.appendTo(tableContainer)	
			//hover function
			.hover(

			//mouse in
			function(){

		         $j(this)
				     .css('background-color','rgb(38,153,204)')
				     

		   },

		   
			//mouse out
			function(){

				if (($j(this).attr('selected')=='selected')) {


				 $j(this)
				    .css('background-color','rgb(38,153,204)')

				}

				else {

					if ($j(this).attr('shown')=='y')

							 $j(this)
								.css('background-color','white')
					else 

						//hidden field will have different background color	
						$j(this)
								.css('background-color','#bdb9b9')

					 //row type
					 if (($j(this).attr('id')=="0"))

						 $j('#table-formatOutput tr[id="0"]')
					 									.css('background-color','lightblue')

				}
					

				

			})
			//click function on row table
			.click(function(){


				//if already selected, deselect row
				if (($j(this).attr('selected')=='selected')) {

					
					
						//deselect all rows
						$j.each($j('#table-formatOutput tr'),function(){

							$j(this)
								.removeAttr('selected')

							if ($j(this).attr('shown')=='y')

								 $j(this)
									.css('background-color','white')

							//hidden field will have different background color	
							else 
								$j(this)
									.css('background-color','#bdb9b9')

							//row type
							$j('#table-formatOutput tr[id="0"]')
															.css('background-color','lightblue')

						})

						//hide  format toolbar
						closeFormatToolbar()
						

				}

				//if not already selected, select row
				else {

					//deselect all rows
					$j.each($j('#table-formatOutput tr'),function(){

							$j(this)
									.removeAttr('selected')

							if ($j(this).attr('shown')=='y')

								 $j(this)
									.css('background-color','white')

							//hidden field will have different background color	
							else 
								$j(this)
									.css('background-color','#bdb9b9')

							//row type
							$j('#table-formatOutput tr[id="0"]')
															.css('background-color','lightblue')

					})

						//set selected 
						$j(this)
								.css('background-color','rgb(38,153,204)')
								.attr('selected',true)


						//get font css property 
						var tdSampleSelected=$j(this).children('.td-sample')
						var parametersCSS=new Object()
						parametersCSS.fontStyle=tdSampleSelected.css('font-style')
						parametersCSS.fontFamily=tdSampleSelected.css('font-family')
						parametersCSS.fontWeight=tdSampleSelected.css('font-weight')
						parametersCSS.fontSize=tdSampleSelected.css('font-size')
						parametersCSS.color=tdSampleSelected.css('color')
						parametersCSS.textDecoration=tdSampleSelected.css('text-decoration')
						//get shown/hidden property
						parametersCSS.shown=$j(this).attr('shown')
						//get label 
						parametersCSS.labelfor=$j(this).attr('label')
						//show format toolbar with relative parameters
						formatToolbar(parametersCSS)

						
				}

			})
		//change background row color if a field is hidden
		if (orderField[i].shown=='n'){

			tr.css('background-color','#bdb9b9')
		}
		//table cells
		var tdorder=$j('<td>')

			tdorder
					.html(orderField[i].orderField)
					.attr('class','td-order')
					.css('border-left','2px solid #c5dbec')
					.appendTo(tr)

		var tdfield=$j('<td>')

			tdfield
					.html(orderField[i].field)
					.attr('class','td-field')
					.css('border-left','2px solid #c5dbec')
					.appendTo(tr)

		var tdsample=$j('<td>')

			tdsample
					.html(orderField[i].sample)
					.attr('class','td-sample')
					.css('border-left','2px solid #c5dbec')
				  	.css('border-right','2px solid #c5dbec')
					.appendTo(tr)

		//change background row color if field is type. Field type cannot be moved or hidden

		if(orderField[i].field=='type'){

			tr.css('background-color','lightblue')
		}


	
	}

	
	//set css properties in table cell sample content
	$j.each(parametersCSS, function(i){

		currentField=parametersCSS[i].field
		$j('#table-formatOutput tr[field="'+currentField+'"] .td-sample').css(parametersCSS[i].propertyCSS,parametersCSS[i].valueCSS)
	
	})

	//legend labels
	$j('#div-formatOutput').append('<br><label style="background-color:#bdb9b9;color:#bdb9b9">Row</label> Hidden field')
	$j('#div-formatOutput').append('<br><label style="background-color:#c5dbec;color:#c5dbec">Row</label> Locked field')
	$j('#div-formatOutput').append('<br><label style="background-color:rgb(38, 153, 204);color:rgb(38, 153, 204)">Row</label> Selected field<br>')
}

//change rows content and appearance
//if direction =-1 move up else move down
function moveField(direction){

	var indexTrSelected= parseInt($j('#table-formatOutput tr[selected="selected"]').attr('id'))

					//field type will be not moved
					if (((indexTrSelected!=1))||((indexTrSelected==1)&&(direction>0))){
				      		
							
							if ((indexTrSelected>0)&&(indexTrSelected<25)){
								
								var indexMove=$j('#table-formatOutput tr[id="'+(indexTrSelected+direction)+'"]').attr('id')
								
								var fieldMove=$j('#table-formatOutput tr[id="'+(indexTrSelected+direction)+'"]').children('.td-field').html()
								
								var sampleMove=$j('#table-formatOutput tr[id="'+(indexTrSelected+direction)+'"]').children('.td-sample').html()

								
								var fontCSSMove=$j('#table-formatOutput tr[id="'+(indexTrSelected+direction)+'"]').children('.td-sample').css('font')
								var colorCSSMove=$j('#table-formatOutput tr[id="'+(indexTrSelected+direction+direction)+'"]').children('.td-sample').css('color')
								var underlineCSSMove=$j('#table-formatOutput tr[id="'+(indexTrSelected+direction)+'"]').children('.td-sample').css('text-decoration')
								
								var shownMove=$j('#table-formatOutput tr[id="'+(indexTrSelected+direction)+'"]').attr('shown')

								var fieldTrSelected=$j('#table-formatOutput tr[selected="selected"]').children('.td-field').html()
								
								var sampleTrSelected=$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').html()

								var fontCSSSelected=$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('font')
								var colorCSSSelected=$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('color')
								var underlineCSSSelected=$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('text-decoration')

								var shownSelected=$j('#table-formatOutput tr[selected="selected"]').attr('shown')
								
								
								//invert properties
								$j('#table-formatOutput tr[selected="selected"]')
																				.children('.td-field').html(fieldMove)
								$j('#table-formatOutput tr[selected="selected"]')
																				.children('.td-sample').html(sampleMove)

								$j('#table-formatOutput tr[selected="selected"]')
																				.children('.td-sample').css('font',fontCSSMove)

								$j('#table-formatOutput tr[selected="selected"]')
																				.children('.td-sample').css('color',colorCSSMove)

								$j('#table-formatOutput tr[selected="selected"]')
																				.children('.td-sample').css('text-decoration',underlineCSSMove)

								$j('#table-formatOutput tr[selected="selected"]')
																				.attr('shown',shownMove)
								
								$j('#table-formatOutput tr[id="'+(indexMove)+'"]')
																				.children('.td-field').html(fieldTrSelected)
								$j('#table-formatOutput tr[id="'+(indexMove)+'"]')
																				.children('.td-sample').html(sampleTrSelected)

								$j('#table-formatOutput tr[id="'+(indexMove)+'"]')
																				.children('.td-sample').css('font',fontCSSSelected)

								$j('#table-formatOutput tr[id="'+(indexMove)+'"]')
																				.children('.td-sample').css('color',colorCSSSelected)

								$j('#table-formatOutput tr[id="'+(indexMove)+'"]')
																				.children('.td-sample').css('text-decoration',underlineCSSSelected)

								$j('#table-formatOutput tr[id="'+(indexMove)+'"]')
																				  .attr('shown',shownSelected)

								

								if (shownMove=='n'){

									$j('#table-formatOutput tr[selected="selected"]').css('background-color','#bdb9b9')
								}

								else {

									$j('#table-formatOutput tr[selected="selected"]').css('background-color','white')
								}
																				

								

 								if (shownSelected=='n'){

									$j('#table-formatOutput tr[id="'+(indexMove)+'"]').css('background-color','#bdb9b9')
								}

								else {

									$j('#table-formatOutput tr[id="'+(indexMove)+'"]').css('background-color','rgb(38,153,204)')
								}

								$j('#table-formatOutput tr[selected="selected"]')
																				.removeAttr('selected')

								$j('#table-formatOutput tr[id="'+(indexMove)+'"]')

 																				.attr('selected',true)
							
							var orderFields=new Array()
							//get order fields from table
							$j.each($j('#table-formatOutput tr'),function(){


								dataFields=new Object()
								dataFields.orderField=$j(this).children('.td-order').html()
								dataFields.field=$j(this).children('.td-field').html()	
								dataFields.sample=$j(this).children('.td-sample').html()
								dataFields.shown=$j(this).attr('shown')
								dataFields.labelfor=$j(this).attr('label')
								orderFields.push(dataFields)
								

							})
							
							//save order fields
							setOrderFields(orderFields)
						}

					}
}

//set font color
function setColor(color){

	$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('color','#'+color)
	console.log($j('#table-formatOutput tr[selected="selected"]').attr('id'))												
	console.log(color)
	var cssFields=new Object()
	cssFields.field=$j('#table-formatOutput tr[selected="selected"]').children('.td-field').html()
	cssFields.propertyCSS='color'
	cssFields.valueCSS='#'+color
	//save properties
	setCssFields(cssFields)
													

}


//set show or hide field
function setShowHide(){

	var shown=$j('#table-formatOutput tr[selected="selected"]').attr('shown')
	visibField=new Object()
	visibField.field=$j('#table-formatOutput tr[selected="selected"]').children('.td-field').html()
	console.log(shown)
	//invert property
	if (shown=='y'){

		visibField.shown='n'
		$j('#table-formatOutput tr[selected="selected"]')
														.attr('shown','n')
														//.css('background-color','#bdb9b9')

		$j('#button-showHide')
				      	.css('background-image','url("../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/images/icons/show.png")')

	}

	else {

		visibField.shown='y'
		$j('#table-formatOutput tr[selected="selected"]')
														.attr('shown','y')
														//.css('background-color','white')

		$j('#button-showHide')
				      	.css('background-image','url(../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/images/icons/hide.png)')


	}

	//save properties
	setVisibleFields(visibField)


}

//set bold text
function setBold(){

	var cssFields=new Object()
	cssFields.field=$j('#table-formatOutput tr[selected="selected"]').children('.td-field').html()
	cssFields.propertyCSS='font-weight'
	var bold=$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('font-weight')

	if (bold=='bold'){

		
		cssFields.valueCSS='normal'

		$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('font-weight','normal')

		$j('#lbl-fontBold')
				      	.attr('title','Bold')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only')
				      	.attr('aria-pressed','false')

		$j('#button-fontBold').removeAttr('checked')

	}

	else{

		cssFields.valueCSS='bold'

		$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('font-weight','bold')

		$j('#lbl-fontBold')
						.attr('title','Normal')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-active')
				      	.attr('aria-pressed','true')


		$j('#button-fontBold').attr('checked',true)

	}
//save properties
	setCssFields(cssFields)

}

//set italic text
function setItalic(){

	var cssFields=new Object()
	cssFields.field=$j('#table-formatOutput tr[selected="selected"]').children('.td-field').html()
	cssFields.propertyCSS='font-style'
	var italic=$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('font-style')
	
	if (italic=='italic'){

		
		cssFields.valueCSS='normal'

		$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('font-style','normal')

		$j('#lbl-fontItalic')
				      	.attr('title','Italic')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only')
				      	.attr('aria-pressed','false')

		$j('#button-fontItalic').removeAttr('checked')

	}

	else{

		cssFields.valueCSS='italic'

		$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('font-style','italic')

		$j('#lbl-fontItalic')
						.attr('title','Normal')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-active')
				      	.attr('aria-pressed','true')


		$j('#button-fontItalic').attr('checked',true)

	}
//save properties
	setCssFields(cssFields)

}

//set underlined text
function setUnderline(){

	var cssFields=new Object()
	cssFields.field=$j('#table-formatOutput tr[selected="selected"]').children('.td-field').html()
	cssFields.propertyCSS='text-decoration'
	var underline=$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('text-decoration')

	if (underline=='underline'){

		
		cssFields.valueCSS='none'

		$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('text-decoration','none')

		$j('#lbl-fontUnderline')
				      	.attr('title','Underline')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only')
				      	.attr('aria-pressed','false')

		$j('#button-fontUnderline').removeAttr('checked')

	}

	else{

		cssFields.valueCSS='underline'

		$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('text-decoration','underline')

		$j('#lbl-fontUnderline')
						.attr('title','No underline')
				      	.attr('class','ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-active')
				      	.attr('aria-pressed','true')


		$j('#button-fontUnderline').attr('checked',true)

	}
//save properties
	setCssFields(cssFields)

}

//set font family
function setFontFamily(fontFamily){

	$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('font-family',fontFamily)

	var cssFields=new Object()
	cssFields.field=$j('#table-formatOutput tr[selected="selected"]').children('.td-field').html()
	cssFields.propertyCSS='font-family'
	cssFields.valueCSS=fontFamily
//save properties
	setCssFields(cssFields)


}

//set font size
function setFontSize(fontSize,measure){

	$j('#table-formatOutput tr[selected="selected"]').children('.td-sample').css('font-size',fontSize+measure)

	var cssFields=new Object()
	cssFields.field=$j('#table-formatOutput tr[selected="selected"]').children('.td-field').html()
	cssFields.propertyCSS='font-size'
	cssFields.valueCSS=fontSize+measure
//save properties
	setCssFields(cssFields)

}

//set label for
function setLabelFor(labelfor){

	$j('#table-formatOutput tr[selected="selected"]').attr('label',labelfor)
	labelFor=new Object()
	labelFor.field=$j('#table-formatOutput tr[selected="selected"]').children('.td-field').html()
	labelFor.labelfor=labelfor
//save properties
	setLabelForFields(labelFor)

}

//window that show output publications authored by id_author
function windowPreviewAuthorPublications(id_author,dataPreview){

	var windowPreviewAuthorPublic=$j('<div>')

		windowPreviewAuthorPublic
						  .html(dataPreview)
						  .attr('id','div-previewAuthorPublications')
						  .dialog({
						  	 title: id_author+' publications preview',
							 modal: true,
							 open: function( event, ui ) {

							 		//set reduced dimensions
							 		var height=$j(window).height()*0.70
									var width=$j(window).width()*0.70
									var top=height/2
									var left=width/2
									$j(this).dialog('option','width',width)
									$j(this).dialog('option','height',height)
									$j(this).dialog('option','top',top)
									$j(this).dialog('option','left',left)
									//add resize button
							 		setButtonResizeWindow($j(this))


									},
							 close: function( event, ui ) {

							 				//destroy details dialog
								 			$j( this ).dialog( "destroy" )


							 			},
							 buttons: [{ 
								 			
								 		    text: "Close", 
								 			click: function(){

								 				
												//destroy  dialog
								 				$j( this ).dialog( "destroy" )

								 			}}


							 			]})


}