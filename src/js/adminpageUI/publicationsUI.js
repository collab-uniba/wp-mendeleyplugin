//avoid conflict with php and wordpress
var $j = jQuery.noConflict()


//show window insert new author
function windowInsertAuthor(author){

	closeWindowProgress()
	
	var windowAuthor=$j('<div>')
	windowAuthor
		.attr('id','div-insertAuthor')
		.html('<table id="table-newAuthor"><tr><td>Mendeley ID</td><td><input type="text" id="txt-id_author" value="'+author.id_author+'" readonly ></td></tr><tr><td>Forename</td><td><input type="text" id="txt-forename" value="'+author.forename+'" readonly></td></tr><tr><td>Surname</td><td><input type="text" id="txt-surname" value="'+author.surname+'" readonly></td></tr></table>')
		.dialog({
		 title: 'New Author',
		 modal: true,
		 close: function(event, ui) { $j('#div-insertAuthor').dialog("destroy") },//hide X button
		 buttons: [{ text: "OK", 
		 			click: function(){

		 				setAuthorPublications()
		 				

		 			}}]})



}

//close window author
function closeWindowInserAuthor(){

	$j('#div-insertAuthor').dialog("destroy")

}

//create table authors in publications manager tab
function showAuthors(authors){

	$j('#div-authorsList').html('')
	$j('#button-deleteAuthor').hide()
	$j('#button-showAuthorPublications').hide()
	$j('#button-showExcludedPublication').hide()
	$j('#button-updAuthorPublications').hide()

	//create table content
	var tableContainer=$j('<table>')

		tableContainer
					  .attr('id','table-authors')
					  //.attr('border','1')

	var caption=$j('<caption>')

		caption
			   .html('Authors')
			   .appendTo(tableContainer)

	var thid_author=$j('<th>')

		thid_author
				  .html('Id Author')
				  .appendTo(tableContainer)

	var thforename=$j('<th>')

		thforename
				  .html('Forename')
				  .appendTo(tableContainer)


	var thsurname=$j('<th>')

		thsurname
				  .html('Surname')
				  .appendTo(tableContainer)


	var thtotpub=$j('<th>')

		thtotpub
				  .html('Publications')
				  .appendTo(tableContainer)

	var thtotpub_ex=$j('<th>')

		thtotpub_ex
				  .html('Publications excluded')
				  .appendTo(tableContainer)

	var size=authors.length
	
	//get data from authors object 
	for (var i=0;i<size;i++){


		var tr=$j('<tr>')

		tr
			.attr('id',authors[i][0].nick)
			.appendTo(tableContainer)
			//set appereance when select a row
			.click(function(){

				
																
				//if already selected, deselect row
				if (($j(this).attr('selected')=='selected')) {

					
					
						//deselect all rows
						$j.each($j('#table-authors tr'),function(){

						$j(this)
								.removeAttr('selected')
								.css('background-color','white')

				})


						//hide delete button
						$j('#button-deleteAuthor').hide()
						//hide publications button
						$j('#button-showAuthorPublications').hide()
						//hide publications button
						$j('#button-showExcludedPublication').hide()
						//hide update publications button
						$j('#button-updAuthorPublications').hide()



				}

				//if not already selected, select row
				else {

					//deselect all rows
					$j.each($j('#table-authors tr'),function(){

							$j(this)
									.removeAttr('selected')
									.css('background-color','white')

					})

						//set selected 
						$j(this)
								.css('background-color','rgb(38,153,204)')
								.attr('selected',true)

						//show delete button
						$j('#button-deleteAuthor').show()

						//show publications button
						$j('#button-showAuthorPublications').show()
						//show publications button
						$j('#button-showExcludedPublication').show()
						//show update publications button
						$j('#button-updAuthorPublications').show()
				}

				checkLoggedAuthor()
			})

			//hover functions on table rows
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

					else 
						$j(this)
					     .css('background-color','white')

				})

		var tdid_author=$j('<td>')

		tdid_author
				  .html(authors[i][0].nick)
				   .css('border-left','2px solid #c5dbec')
				  .appendTo(tr)

		var tdforename=$j('<td>')

		tdforename
				  .html(authors[i][0].fname)
				  .css('border-left','2px solid #c5dbec')
				  .appendTo(tr)

		var tdsurname=$j('<td>')

		tdsurname
				  .html(authors[i][0].sname)
				  .css('border-left','2px solid #c5dbec')
				  .appendTo(tr)

		if (authors[i][0].publ==null) authors[i][0].publ='0'
		var tdpub=$j('<td>')

		tdpub
				  .html(authors[i][0].publ)
				  .css('border-left','2px solid #c5dbec')
				  .appendTo(tr)
		
		if (authors[i][0].publ_ex==null) authors[i][0].publ_ex='0'
		var tdpub_ex=$j('<td>')

		tdpub_ex
				  .html(authors[i][0].publ_ex)
				  .css('border-left','2px solid #c5dbec')
				  .css('border-right','2px solid #c5dbec')
				  .appendTo(tr)

	}

	var authorsList=$j('#div-authorsList')

			authorsList
					.html(tableContainer)
				

}

//window that show field canonical_id, title and authors of all publications authored by id_author
function windowAuthorPublications(publications,id_author){

	//remove previous dialog
	$j('#div-authorPublications').remove()

	var tableContainer=$j('<table>')

	tableContainer
				  .attr('id','table-authorPublications')

	var windowAuthorPublic=$j('<div>')

		windowAuthorPublic
						  .html(tableContainer)
						  .attr('id','div-authorPublications')
						  .dialog({
						  	 title: id_author+' Publications',
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
							 		checkLoggedAuthor()

									},
							 close: function( event, ui ) {$j( this ).dialog( "destroy" )},
							 buttons: [{ 
							 				//move publication in excluded publications
								 			id:"button-deletePublication",
								 		    text: "Exclude publication", 
								 			click: function(){

								 				//$j( this ).dialog( "destroy" )
								 				 windowConfirm('Excluding publication...', 'Confirm?',excludePublication)

								 			}},

							 			{ 	//show all publications fields
								 			id:"button-showDetailsPublication",
								 		    text: "Show details", 
								 			click: function(){

								 				var canonical_id=$j('#table-authorPublications tr[selected="selected"]').attr('id')
								 				windowDetailsPublication(canonical_id,publications)

								 			}},

								 		{ 
								 			//close window and destroy it
								 			 text: "Close",
								 			click: function(){

								 				$j( this ).dialog( "destroy" )

								 			}}


							 			]})
	
	//buttons will be shown only if user select a publication from table
	$j('#button-deletePublication').hide()
	$j('#button-showDetailsPublication').hide()
	//create table content
	
				
	//order
	var thnumber=$j('<th>')

		thnumber
				  .html('#')
				  .appendTo(tableContainer)

	var thcanonical_id=$j('<th>')

		thcanonical_id
				  .html('ID')
				  .appendTo(tableContainer)

	

	var thtitle=$j('<th>')

		thtitle
				  .html('Title')
				  .appendTo(tableContainer)


	var thauthors=$j('<th>')

		thauthors
				  .html('Authors')
				  .appendTo(tableContainer)

	var size=publications.length

	//get data from publications object 
	for (var i=0;i<size;i++){


		var tr=$j('<tr>')

		tr
			.attr('id',publications[i].canonical_id)
			.appendTo(tableContainer)
			//set appereance when select a row
			.click(function(){
									
				//if already selected, deselect row
				if (($j(this).attr('selected')=='selected')) {

					
					
						//deselect all rows
						$j.each($j('#table-authorPublications tr'),function(){

						$j(this)
								.removeAttr('selected')
								.css('background-color','white')

				})


						//hide delete button
						$j('#button-deletePublication').hide()
						$j('#button-showDetailsPublication').hide()

				}

				//if not already selected, select row
				else {

					//deselect all rows
					$j.each($j('#table-authorPublications tr'),function(){

							$j(this)
									.removeAttr('selected')
									.css('background-color','white')

					})

						//set selected 
						$j(this)
								.css('background-color','rgb(38,153,204)')
								.attr('selected',true)

						//show delete button
						$j('#button-deletePublication').show()

						//show publications button
						$j('#button-showDetailsPublication').show()
				}

					checkLoggedAuthor()
			})

			//hover functions on table rows
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

					else 
						$j(this)
					     .css('background-color','white')

				})


			var tdnumber=$j('<td>')

			tdnumber
					  .html(i+1)
					  .css('border-left','2px solid #c5dbec')
					  .appendTo(tr)

	    	var tdcanonical_id=$j('<td>')

			tdcanonical_id
					  .html(publications[i].canonical_id)
					  .css('border-left','2px solid #c5dbec')
					  .appendTo(tr)

			var tdtitle=$j('<td>')

			tdtitle
					  .html(publications[i].title)
					  .css('border-left','2px solid #c5dbec')
					  .appendTo(tr)

			var tdauthors=$j('<td>')

			var authors=''
			
			for (var j = 0; j < publications[i].authors.length; j++) {
				
				authors+=(publications[i].authors[j].forename+' '+publications[i].authors[j].surname+' <br>')

			}

			tdauthors
					  .html(authors)
					  .css('border-left','2px solid #c5dbec')
					  .appendTo(tr)

	}



}

//close window publications
function closeWindowPublications(){

	$j('#div-authorPublications').dialog('destroy')
}

//show window publication details creating a tabs set inside a dialog window
function windowDetailsPublication(canonical_id,publications){

	var publicDetails
	var size=publications.length

	//get publication with canonical_id
	for (var i = 0; i < size; i++) {

		if (publications[i].canonical_id==canonical_id) {

			publicDetails=publications[i]
			break
		}
	}


	//hide publications dialog 
	$j('div[aria-describedby="div-authorPublications"]').hide()

	//main div tab
	var tabContainer=$j('<div>')

	tabContainer
				  .attr('id','div-publicationDetails')
				  
	//dialog window
	var windowAuthorPublicDetails=$j('<div>')

		windowAuthorPublicDetails
						  .html(tabContainer)
						  .attr('id','div-authorPublicationDetails')
						  .dialog({
						  	 title: 'Publications Details: '+canonical_id,
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
							 		checkLoggedAuthor()

									},
							 close: function( event, ui ) {

							 				
							 				//show publications dialog 
							 				$j('div[aria-describedby="div-authorPublications"]').show()
							 				//destroy details dialog
								 			$j( this ).dialog( "destroy" )


							 			},
							 buttons: [{ 
								 			id:"button-deletePublication",
								 		    text: "Exclude publication", 
								 			click: function(){

								 				
								 				 windowConfirm('Excluding publication...', 'Confirm?',excludePublication)
								 				 //$j( this ).dialog( "destroy" )

								 			}},

							 	

								 		{ 
								 			//id:"button-showDetailsPublication",
								 		    text: "Close", 
								 			click: function(){

								 				//show publications dialog 
												$j('div[aria-describedby="div-authorPublications"]').show()
												//destroy details dialog
								 				$j( this ).dialog( "destroy" )

								 			}}


							 			]})
	
	//create tab content
	checkLoggedAuthor()
	
	var labelTab=$j('<ul>')

		labelTab.appendTo(tabContainer)

	

	var licanonical_id=$j('<li>')

		licanonical_id
				  
				  .appendTo(labelTab)

	var a=$j('<a>')

		a
		.attr('href','#div-canonical_id')
		.html('canonical_id')
		.appendTo(licanonical_id)


	var lititle=$j('<li>')

		lititle
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-title')
		.html('Title')
		.appendTo(lititle)


	var liauthors=$j('<li>')

		liauthors
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-authors')
		.html('Authors')
		.appendTo(liauthors)


	var lioutlet=$j('<li>')

		lioutlet
			      
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-outlet')
		.html('Publ. outlet')
		.appendTo(lioutlet)


	var liabstract=$j('<li>')

		liabstract
			      
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-abstract')
		.html('Abstract')
		.appendTo(liabstract)


	var livolume=$j('<li>')

		livolume
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-volume')
		.html('Volume')
		.appendTo(livolume)



	var liissue=$j('<li>')

		liissue
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-issue')
		.html('Issue')
		.appendTo(liissue)


	var lipages=$j('<li>')

		lipages
		 				  
				  .appendTo(labelTab)


	var a=$j('<a>')
	
		a
		.attr('href','#div-pages')
		.html('Pages')
		.appendTo(lipages)


	var lipublisher=$j('<li>')

		lipublisher
				  
				  .appendTo(labelTab)


	var a=$j('<a>')
	
		a
		.attr('href','#div-publisher')
		.html('Publisher')
		.appendTo(lipublisher)


	var liyear=$j('<li>')

		liyear
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-year')
		.html('Year')
		.appendTo(liyear)


	var lidoi=$j('<li>')

		lidoi
				  
				  .appendTo(labelTab)


	var a=$j('<a>')
	
		a
		.attr('href','#div-doi')
		.html('DOI')
		.appendTo(lidoi)


	var liisbn=$j('<li>')

		liisbn
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-isbn')
		.html('ISBN')
		.appendTo(liisbn)


	var liissn=$j('<li>')

		liissn
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-issn')
		.html('ISSN')
		.appendTo(liissn)


	var litype=$j('<li>')

		litype
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-type')
		.html('Type')
		.appendTo(litype)


    //div tabs
	var divcanonical_id=$j('<div>')

	divcanonical_id
			  .attr('id','div-canonical_id')
			  .html(publicDetails.canonical_id)
			  .css('border-left','2px solid #c5dbec')
			  .appendTo(tabContainer)

	var divtitle=$j('<div>')

	divtitle
			  .attr('id','div-title')
			  .html(publicDetails.title)
			  .css('border-left','2px solid #c5dbec')
			  .appendTo(tabContainer)

	var divauthors=$j('<div>')

	var authors=''
	
	for (var j = 0; j < publicDetails.authors.length; j++) {
		
		authors+=(publicDetails.authors[j].forename+' '+publicDetails.authors[j].surname+' <br>')

	}

	divauthors
			  .attr('id','div-authors')
			  .html(authors)
			  .css('border-left','2px solid #c5dbec')
			  .appendTo(tabContainer)

	var divoutlet=$j('<div>')

		divoutlet
				  .attr('id','div-outlet')
				  .html(publicDetails.publication_outlet)
				  .appendTo(tabContainer)

	var divabstract=$j('<div>')

		divabstract
				  .attr('id','div-abstract')
				  .html(publicDetails.abstract)
				  .appendTo(tabContainer)

	var divvolume=$j('<div>')

		divvolume
				  .attr('id','div-volume')
				  .html(publicDetails.volume)
				  .appendTo(tabContainer)

	var divissue=$j('<div>')

		divissue
				  .attr('id','div-issue')
				  .html(publicDetails.issue)
				  .appendTo(tabContainer)

	var divpages=$j('<div>')

		divpages
				  .attr('id','div-pages')
				  .html(publicDetails.pages)
				  .appendTo(tabContainer)

	var divpublisher=$j('<div>')

		divpublisher
				  .attr('id','div-publisher')
				  .html(publicDetails.publisher)
				  .appendTo(tabContainer)

	var divyear=$j('<div>')

		divyear
				  .attr('id','div-year')
				  .html(publicDetails.year)
				  .appendTo(tabContainer)

	var divdoi=$j('<div>')

		divdoi
				  .attr('id','div-doi')
				  .html(publicDetails.doi)
				  .appendTo(tabContainer)

	var divisbn=$j('<div>')

		divisbn
				  .attr('id','div-isbn')
				  .html(publicDetails.isbn)
				  .appendTo(tabContainer)


	var divissn=$j('<div>')

		divissn
				  .attr('id','div-issn')
				  .html(publicDetails.issn)
				  .appendTo(tabContainer)

	var divtype=$j('<div>')

		divtype
		          .attr('id','div-type')
				  .html(publicDetails.type)
				  .appendTo(tabContainer)

	//set tab appereance

	$j('#div-publicationDetails').tabs()
	

}

//close window publication details
function closeWindowPublicationDetails(){

	$j('#div-authorPublicationDetails').dialog('destroy')
}

//window that show field canonical_id, title and authors of all excluded publications authored by id_author
function windowExcludedAuthorPublications(publications,id_author){

	//remove previous dialog
	$j('#div-authorExcludedPublications').remove()

	var tableContainer=$j('<table>')

	tableContainer
				  .attr('id','table-authorExcludedPublications')

	
	var windowExcludedAuthorPublic=$j('<div>')

		windowExcludedAuthorPublic
						  .html(tableContainer)
						  .attr('id','div-authorExcludedPublications')
						  .dialog({
						  	 title: id_author+' Publications',
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
							 close: function( event, ui ) {$j( this ).dialog( "destroy" )},
							 buttons: [{ 
							 				//delete definit publication
								 			id:"button-deletePublication",
								 		    text: "Delete publication", 
								 			click: function(){

								 				 //confirm operation window
								 				 windowConfirm('Deleting publication...', 'Confirm?',deleteExlcudedPublication)

								 			}},

								 		{ 	//move publication in authored publications
								 			id:"button-restorePublication",
								 		    text: "Restore publication", 
								 			click: function(){

								 				//confirm operation window
								 				 windowConfirm('Restoring publication...', 'Confirm?',restoreExcludedPublication)

								 			}},

							 			{ 	//show excluded publications details 
								 			id:"button-showDetailsPublication",
								 		    text: "Show details", 
								 			click: function(){

								 				var canonical_id=$j('#table-authorExcludedPublications tr[selected="selected"]').attr('id')
								 				windowDetailsExcludedPublication(canonical_id,publications)

								 			}},

								 		{ 
								 			//id:"button-showDetailsPublication",
								 		    text: "Close", 
								 			click: function(){

								 				$j( this ).dialog( "destroy" )

								 			}}


							 			]})

	//buttons will be shown only if user select an excluded publication from table
	$j('#button-deletePublication').hide()
	$j('#button-restorePublication').hide()
	$j('#button-showDetailsPublication').hide()
	//create table content
	
				

	var thnumber=$j('<th>')

		thnumber
				  .html('#')
				  .appendTo(tableContainer)

	var thcanonical_id=$j('<th>')

		thcanonical_id
				  .html('ID')
				  .appendTo(tableContainer)

	

	var thtitle=$j('<th>')

		thtitle
				  .html('Title')
				  .appendTo(tableContainer)


	var thauthors=$j('<th>')

		thauthors
				  .html('Authors')
				  .appendTo(tableContainer)

	
	var size=publications.length		  
	//get data from publications object 
	for (var i=0;i<size;i++){


		var tr=$j('<tr>')

		tr
			.attr('id',publications[i].canonical_id)
			.appendTo(tableContainer)
			//set appereance when select a row
			.click(function(){
									
				//if already selected, deselect row
				if (($j(this).attr('selected')=='selected')) {

					
					
						//deselect all rows
						$j.each($j('#table-authorExcludedPublications tr'),function(){

						$j(this)
								.removeAttr('selected')
								.css('background-color','white')

				})


						//hide  buttons
						$j('#button-deletePublication').hide()
						$j('#button-restorePublication').hide()
						$j('#button-showDetailsPublication').hide()

				}

				//if not already selected, select row
				else {

					//deselect all rows
					$j.each($j('#table-authorExcludedPublications tr'),function(){

							$j(this)
									.removeAttr('selected')
									.css('background-color','white')

					})

						//set selected 
						$j(this)
								.css('background-color','rgb(38,153,204)')
								.attr('selected',true)

						//show delete button
						$j('#button-deletePublication').show()

						$j('#button-restorePublication').show()

						//show publications button
						$j('#button-showDetailsPublication').show()
				}

				checkLoggedAuthor()
			})

			//hover functions on table rows
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

					else 
						$j(this)
					     .css('background-color','white')

				})


			var tdnumber=$j('<td>')

			tdnumber
					  .html(i+1)
					  .css('border-left','2px solid #c5dbec')
					  .appendTo(tr)

	    	var tdcanonical_id=$j('<td>')

			tdcanonical_id
					  .html(publications[i].canonical_id)
					  .css('border-left','2px solid #c5dbec')
					  .appendTo(tr)

			var tdtitle=$j('<td>')

			tdtitle
					  .html(publications[i].title)
					  .css('border-left','2px solid #c5dbec')
					  .appendTo(tr)

			var tdauthors=$j('<td>')

			var authors=''
			
			for (var j = 0; j < publications[i].authors.length; j++) {
				
				authors+=(publications[i].authors[j].forename+' '+publications[i].authors[j].surname+' <br>')

			}

			tdauthors
					  .html(authors)
					  .css('border-left','2px solid #c5dbec')
					  .appendTo(tr)




	}
}

//close window excluded publications
function closeWindowExcludedPublications(){

	$j('#div-authorExcludedPublications').dialog('destroy')
}


//show window excluded publication details creating a tabs set inside a dialog window
function windowDetailsExcludedPublication(canonical_id,publications){

	var publicDetails
	var size=publications.length

	//get publication with canonical_id
	for (var i = 0; i < size; i++) {

		if (publications[i].canonical_id==canonical_id) {

			publicDetails=publications[i]
			break
		}
	}


	//hide publications dialog 
	$j('div[aria-describedby="div-authorExcludedPublications"]').hide()

	//main div tab
	var tabContainer=$j('<div>')

	tabContainer
				  .attr('id','div-publicationDetails')
				  
			
	//dialog window
	var windowAuthorPublicDetails=$j('<div>')

		windowAuthorPublicDetails
						  .html(tabContainer)
						  .attr('id','div-authorExlcudedPublicationDetails')
						  .dialog({
						  	 title: 'Publications Details: '+canonical_id,
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
							 		checkLoggedAuthor()


									},
							 close: function( event, ui ) {

							 				
							 				//show publications dialog 
							 				$j('div[aria-describedby="div-authorExcludedPublications"]').show()
							 				//destroy details dialog
								 			$j( this ).dialog( "destroy" )


							 			},
							 buttons: [{ 
								 			id:"button-deletePublication",
								 		    text: "Delete publication", 
								 			click: function(){

								 				
								 				 windowConfirm('Deleting publication...', 'Confirm?',deleteExlcudedPublication)
								 				// $j( this ).dialog( "destroy" )

								 			}},

								 		{ 
								 			id:"button-restorePublication",
								 		    text: "Restore publication", 
								 			click: function(){

								 				//$j( this ).dialog( "destroy" )
								 				 windowConfirm('Restoring publication...', 'Confirm?',restoreExcludedPublication)

								 			}},

							 	

								 		{ 
								 			//id:"button-showDetailsPublication",
								 		    text: "Close", 
								 			click: function(){

								 				//show publications dialog 
												$j('div[aria-describedby="div-authorExcludedPublications"]').show()
												//destroy details dialog
								 				$j( this ).dialog( "destroy" )

								 			}}


							 			]})

	
	//create tab content
	
	var labelTab=$j('<ul>')

		labelTab.appendTo(tabContainer)

	

	var licanonical_id=$j('<li>')

		licanonical_id
				  
				  .appendTo(labelTab)

	var a=$j('<a>')

		a
		.attr('href','#div-canonical_id')
		.html('canonical_id')
		.appendTo(licanonical_id)


	var lititle=$j('<li>')

		lititle
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-title')
		.html('Title')
		.appendTo(lititle)


	var liauthors=$j('<li>')

		liauthors
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-authors')
		.html('Authors')
		.appendTo(liauthors)


	var lioutlet=$j('<li>')

		lioutlet
			      
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-outlet')
		.html('Publ. outlet')
		.appendTo(lioutlet)


	var liabstract=$j('<li>')

		liabstract
			      
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-abstract')
		.html('Abstract')
		.appendTo(liabstract)


	var livolume=$j('<li>')

		livolume
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-volume')
		.html('Volume')
		.appendTo(livolume)



	var liissue=$j('<li>')

		liissue
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-issue')
		.html('Issue')
		.appendTo(liissue)


	var lipages=$j('<li>')

		lipages
		 				  
				  .appendTo(labelTab)


	var a=$j('<a>')
	
		a
		.attr('href','#div-pages')
		.html('Pages')
		.appendTo(lipages)


	var lipublisher=$j('<li>')

		lipublisher
				  
				  .appendTo(labelTab)


	var a=$j('<a>')
	
		a
		.attr('href','#div-publisher')
		.html('Publisher')
		.appendTo(lipublisher)


	var liyear=$j('<li>')

		liyear
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-year')
		.html('Year')
		.appendTo(liyear)


	var lidoi=$j('<li>')

		lidoi
				  
				  .appendTo(labelTab)


	var a=$j('<a>')
	
		a
		.attr('href','#div-doi')
		.html('DOI')
		.appendTo(lidoi)


	var liisbn=$j('<li>')

		liisbn
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-isbn')
		.html('ISBN')
		.appendTo(liisbn)


	var liissn=$j('<li>')

		liissn
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-issn')
		.html('ISSN')
		.appendTo(liissn)


	var litype=$j('<li>')

		litype
				  
				  .appendTo(labelTab)

	var a=$j('<a>')
	
		a
		.attr('href','#div-type')
		.html('Type')
		.appendTo(litype)


    //div tabs
	var divcanonical_id=$j('<div>')

	divcanonical_id
			  .attr('id','div-canonical_id')
			  .html(publicDetails.canonical_id)
			  .css('border-left','2px solid #c5dbec')
			  .appendTo(tabContainer)

	var divtitle=$j('<div>')

	divtitle
			  .attr('id','div-title')
			  .html(publicDetails.title)
			  .css('border-left','2px solid #c5dbec')
			  .appendTo(tabContainer)

	var divauthors=$j('<div>')

	var authors=''
	
	for (var j = 0; j < publicDetails.authors.length; j++) {
		
		authors+=(publicDetails.authors[j].forename+' '+publicDetails.authors[j].surname+' <br>')

	}

	divauthors
			  .attr('id','div-authors')
			  .html(authors)
			  .css('border-left','2px solid #c5dbec')
			  .appendTo(tabContainer)

	var divoutlet=$j('<div>')

		divoutlet
				  .attr('id','div-outlet')
				  .html(publicDetails.publication_outlet)
				  .appendTo(tabContainer)

	var divabstract=$j('<div>')

		divabstract
				  .attr('id','div-abstract')
				  .html(publicDetails.abstract)
				  .appendTo(tabContainer)

	var divvolume=$j('<div>')

		divvolume
				  .attr('id','div-volume')
				  .html(publicDetails.volume)
				  .appendTo(tabContainer)

	var divissue=$j('<div>')

		divissue
				  .attr('id','div-issue')
				  .html(publicDetails.issue)
				  .appendTo(tabContainer)

	var divpages=$j('<div>')

		divpages
				  .attr('id','div-pages')
				  .html(publicDetails.pages)
				  .appendTo(tabContainer)

	var divpublisher=$j('<div>')

		divpublisher
				  .attr('id','div-publisher')
				  .html(publicDetails.publisher)
				  .appendTo(tabContainer)

	var divyear=$j('<div>')

		divyear
				  .attr('id','div-year')
				  .html(publicDetails.year)
				  .appendTo(tabContainer)

	var divdoi=$j('<div>')

		divdoi
				  .attr('id','div-doi')
				  .html(publicDetails.doi)
				  .appendTo(tabContainer)

	var divisbn=$j('<div>')

		divisbn
				  .attr('id','div-isbn')
				  .html(publicDetails.isbn)
				  .appendTo(tabContainer)


	var divissn=$j('<div>')

		divissn
				  .attr('id','div-issn')
				  .html(publicDetails.issn)
				  .appendTo(tabContainer)

	var divtype=$j('<div>')

		divtype
		          .attr('id','div-type')
				  .html(publicDetails.type)
				  .appendTo(tabContainer)

	//set tab appereance

	$j('#div-publicationDetails').tabs()
	

}

//close window excluded publication details
function closeWindowExcludedPublicationDetails(){

	$j('#div-authorExlcudedPublicationDetails').dialog('destroy')
}

//check logged author 
function checkLoggedAuthor(){

	closeWindowProgress()
	
	id_author=String($j('#table-authors tr[selected="selected"]').attr('id'))

	//if logged author is not equals to author selected into table, hide buttons, else show buttons
	if (loggedAuthor.id_author!=id_author){

		$j('#button-deleteAuthor').hide()
		$j('#button-deletePublication').hide()
		$j('#button-restorePublication').hide()
		$j('#button-showDetailsPublication').hide()
		
	}

	else{

		$j('#button-deleteAuthor').show()
		$j('#button-deletePublication').show()
		$j('#button-restorePublication').show()
		$j('#button-showDetailsPublication').show()
		
	}

	

}
