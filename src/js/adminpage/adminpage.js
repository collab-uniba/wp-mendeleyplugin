var $j = jQuery.noConflict();

//function called when load admin page
$j(document).ready(function(){

	setUIElement()//set jquery ui attributes
	//bind functions to widgets
	bindKeyFunctions()
	bindPublicationsFunctions()
	bindFormatOutputFunction()



})

//bind related functions to key widget
function bindKeyFunctions(){


	//load keys from database
	getKeys()

	//call setKeys when clic on button-getKeys 
	$j('#button-getKey').click(function(){

		setKeys()

	})

}


//bind related functions to author widget
function bindPublicationsFunctions(){

	//call at starting time
	getLoggedAuthor(null)
	getListAuthors()
	
	$j('#div-formatOutput').html('')
	$j('#div-formatToolbar')
							.hide()

	

	//call createWindowNewAuthor when clic on button-newAuthor 
	$j('#button-newAuthor').click(function(){

		windowProgress('Getting logged author...')
		getLoggedAuthor(windowInsertAuthor)
		


	})

	$j('#button-deleteAuthor').click(function(){

		
		windowConfirm('Deleting author...','Confirm?',deleteAuthor)
		

	})


	$j('#button-showAuthorPublications').click(function(){

		//get id_author from selected row
		
		getAuthorPublications()


	})

	$j('#button-showExcludedPublication').click(function(){


		getExcludedAuthorPublications()

	})

	$j('#button-updAuthorPublications').click(function(){

		updateAuthorPublication()

	})


}

function bindFormatOutputFunction(){

	$j('#select-authorPreview').html('')
	getOrderFields()
	getAuthorDataPreview()



	$j('#button-orderTypePublications').click(function(){

		getOrderTypePublications()

	})

}
