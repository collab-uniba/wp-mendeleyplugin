var $j = jQuery.noConflict();
//get list author  and put them into select element
function getAuthorDataPreview(){

	//windowProgress('Getting authors...')
	//send ajax post request 
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'getListAuthors'}, function(response) {

			closeWindowProgress()

			//decode json response
			var data=$j.parseJSON(response)

			selectBoxPreviewPublications(data)
			id_authors=data

	});

}

//save order type publications
function setOrderTypePublications(orderPublications){

	windowProgress('Setting order type...')

	//encode parameters in JSON format
	var orderPublicationsJSON=JSON.stringify(orderPublications)
	//send ajax post request 
	console.log(orderPublicationsJSON)
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'setOrderTypePublications', orderPublications:orderPublicationsJSON}, function(response) {

			closeWindowProgress()
			windowMessage('Finish','Operation completed!')
			
			console.log(response)
			

	});

}

//get order type publications
function getOrderTypePublications(){

	windowProgress('Getting order type...')

	//send ajax post request 

	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'getOrderTypePublications'}, function(response) {

			closeWindowProgress()

			
			//decode json response
			var data=$j.parseJSON(response)
			windowOrderTypePublications(data)
			//update 
			selectBoxFieldType(data)

			

	});

}
//get order fields
function getOrderFields(){

	//windowProgress('Getting order fields...')

	//send ajax post request 

	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'getOrderFields'}, function(response) {

			//closeWindowProgress()

			
			//decode json response
			var dataOrder=$j.parseJSON(response)

			//windowProgress('Getting css fields...')
			//console.log(response)
			//send ajax post request 

			$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'getCssFields'}, function(response) {

					//closeWindowProgress()

					
					//decode json response
					var dataCSS=$j.parseJSON(response)
					tableFormatPublications(dataOrder,dataCSS)

					});
			

			});
}

//set order fields
function setOrderFields(orderFields){

	//windowProgress('Setting order fields...')

	//encode parameters in JSON format
	var orderFieldsJSON=JSON.stringify(orderFields)
	
	//send ajax post request  

	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'setOrderFields', orderFields:orderFieldsJSON}, function(response) {

			closeWindowProgress()
			console.log(response)

			});	


}

//set css properties
function setCssFields(cssFields){

	//windowProgress('setting css fields...')
	var cssFieldsJSON=JSON.stringify(cssFields)
	//send ajax post request 

	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'setCssFields',cssFields:cssFieldsJSON}, function(response) {

			closeWindowProgress()

			console.log(response)

			});

}

//set visibility property
function setVisibleFields(visibField){

	//windowProgress('setting css fields...')
	var visibFieldJSON=JSON.stringify(visibField)
	//send ajax post request 

	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'setVisibleFields',visibField:visibFieldJSON}, function(response) {

			closeWindowProgress()

			console.log(response)

			});

}

//set label field
function setLabelForFields(labelForField){

	//windowProgress('setting css fields...')
	var labelForFieldJSON=JSON.stringify(labelForField)
	//send ajax post request 
	console.log(labelForFieldJSON)
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'setLabelForFields',labelForField:labelForFieldJSON}, function(response) {

			closeWindowProgress()

			console.log(response)

			});

}

//get preview publications
function getPreviewAuthorPublications(id_author){

	//send ajax post request 
	windowProgress('Getting '+id_author+' preview publications...')
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{request: 'getPreviewAuthorPublications',id_author:id_author}, function(response) {

			closeWindowProgress()
			//var dataPreview=$j.parseJSON(response)
			console.log(response)
			
			windowPreviewAuthorPublications(id_author,response)

			});


}
