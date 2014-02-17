//avoid conflict with php and wordpress
var $j = jQuery.noConflict()

//get keys
function getKeys(){

	
	//windowProgress('Getting consumer key...')
	$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/requestermanager.php',{ request: 'getKeys'}, function(response) {

					//decode json response
					
					var data=$j.parseJSON(response)
  					$j('#txt-consumerkey').val(data[0].consumer)
  					$j('#txt-consumersecret').val(data[0].secret)
  					
  					
  				 			

		})

}

//set keys
function setKeys(){

	//if keys were not inserted
	if (($j('#txt-consumerkey').val()=='')||($j('#txt-consumersecret').val()=='')){

		windowMessage('Error','Please insert both keys')
	}


	//if keys were inserted, send a request to get request keys and access keys
	else {

		var consumer=$j('#txt-consumerkey').val()
		var secret=$j('#txt-consumersecret').val()
		windowProgress('Setting application keys...')
		//send keys in a ajax post request 
		$j.post('../wp-content/plugins/wp-mendeleyauthoredpublicationsplugin/oauthmanager.php',{ consumer: consumer, secret:secret}, function(response) {

			var url=response
			console.log(url)
			closeWindowProgress()
  			windowMendeley('Get Mendeley access tokens', url)

  			
		});

	}


}