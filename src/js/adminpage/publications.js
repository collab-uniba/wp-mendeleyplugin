var $j = jQuery.noConflict();

var loggedAuthor=new Object()
//save author and his publications
function setAuthorPublications(){

	//check author data
		var id_author=$j('#txt-id_author').val()
		var forename=$j('#txt-forename').val()
		var surname=$j('#txt-surname').val()

		//error if there are empty fields
		if ((id_author=='')||(forename=='')||(surname=='')){

			//window error
			windowMessage('Error','Please insert all author data')

		}

		//starts import
		else{

			
			closeWindowInserAuthor()

			//get consumer key
			$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{ request: 'getkeys'}, function(data) {

					var consumer
					if (data.indexOf('Error')>-1) {

						//window error
						windowMessage('Error!','Error reading keys')
						

					}
						
		  			else {

		  				
						//send request to get publications from Mendeley and save them into db
						windowProgress('Getting publications...')

		  				$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{ request: 'insertAuthor', id_author:id_author, forename:forename, surname:surname}, function(data) {

		  						//save author and publications

		  						console.log(data)
		  						closeWindowProgress()
					  			getListAuthors();
		  					
		  				})
		  				
		  				}
		  				
			});	 	

	}



}
//get logged author and exec callback function
function getLoggedAuthor(callback){

	//windowProgress('Getting logged author...')
	//send keys in a ajax post request 
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'getLoggedAuthor'}, function(response) {

			closeWindowProgress()
			
			//decode json response
			//console.log(response)
			var author=new Object()
			var data=$j.parseJSON(response)
			if (data!=null){

					
					author.id_author=data.profile_id
					var name=data.name.split(' ')
					author.forename=name[0]
					author.surname=name[1]
					loggedAuthor=author
					//set logged author input text 
					$j('#txt-mendeleyUser').val(author.forename+' '+author.surname)

			}

			if (callback!=null) callback(author)
			
	});


}


//get author list
function getListAuthors(){

	//windowProgress('Getting authors...')
	
	//send keys in a ajax post request 
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'getListAuthors'}, function(response) {

			//closeWindowProgress()

			//decode json response
			var data=$j.parseJSON(response)
			
			//show authors
			showAuthors(data)
			selectBoxPreviewPublications(data)
			id_authors=data


	});


}
//delete  logged author
function deleteAuthor(){

	var id_author=$j('#table-authors tr[selected="selected"]').attr('id')
	windowProgress('Deleting selected author...')
	//send keys in a ajax post request 
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'deleteAuthor',id_author:id_author}, function(response) {

			closeWindowProgress()
			//console.log(response)
			getListAuthors()


	});


}

//get and save logged author publications
function getAuthorPublications(){

	var id_author=$j('#table-authors tr[selected="selected"]').attr('id')
	windowProgress('Getting publications...')

	//send  ajax post request 
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'getAuthorPublications',id_author:id_author}, function(response) {

			closeWindowProgress()
			//decode json response
			var data=$j.parseJSON(response)
			//show authors
			windowAuthorPublications(data,id_author)
			


	})

	


}

//delete logged author publications
function excludePublication(){

	var canonical_id=$j('#table-authorPublications tr[selected="selected"]').attr('id')
	var id_author=$j('#table-authors tr[selected="selected"]').attr('id')
	windowProgress('Deleting publication...')
	//send ajax post request 
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'excludePublication',canonical_id:canonical_id,id_author:id_author}, function(response) {

			closeWindowProgress()
			closeWindowPublications()
			closeWindowPublicationDetails()
			//show authors
			getListAuthors()
			
			


	});


}

//delete logged author excluded publications
function deleteExlcudedPublication(){

	var canonical_id=$j('#table-authorExcludedPublications tr[selected="selected"]').attr('id')
	var id_author=$j('#table-authors tr[selected="selected"]').attr('id')
	windowProgress('Deleting exlcuded publication...')
	//send ajax post request 
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'deleteExcludedPublication',canonical_id:canonical_id,id_author:id_author}, function(response) {

			closeWindowProgress()
			closeWindowExcludedPublications()
			closeWindowExcludedPublicationDetails()
			//show authors
			getListAuthors()
			//console.log(response)
			


	});


}

//delete logged author publications
function updateAuthorPublication(){

	var id_author=$j('#table-authors tr[selected="selected"]').attr('id')
	windowProgress('Updating '+id_author+' publications...')
	//send ajax post request 
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'updateAuthorPublications',id_author:id_author}, function(response) {

			closeWindowProgress()
			//show authors
			getListAuthors()
			if (response!='')  windowMessage('Error! ',response)
			
			


	});

}
//get logged author excluded publications
function getExcludedAuthorPublications(){

	var id_author=$j('#table-authors tr[selected="selected"]').attr('id')
	windowProgress('Getting exlcuded publications...')

	//send  ajax post request 
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'getExcludedAuthorPublications',id_author:id_author}, function(response) {

			closeWindowProgress()
			//decode json response
			var data=$j.parseJSON(response)
			//show authors
			windowExcludedAuthorPublications(data,id_author+' Excluded')
			//console.log(response)


	})

	


}

//restore logged author excluded publications
function restoreExcludedPublication(){

	var canonical_id=$j('#table-authorExcludedPublications tr[selected="selected"]').attr('id')
	var id_author=$j('#table-authors tr[selected="selected"]').attr('id')
	windowProgress('Restoring exlcuded publication...')
	//send ajax post request 
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'restoreExlcudedPublication',canonical_id:canonical_id,id_author:id_author}, function(response) {

			closeWindowProgress()
			closeWindowExcludedPublications()
			closeWindowExcludedPublicationDetails()
			//show authors
			getListAuthors()
			//console.log(response)
			


	});


}