<?php


/**
* 
*/
include ('databasemanager.php');
if (!class_exists("DataManager")) {
class DataManager 
{
	


	
	private $dbmanager;
	
	private $loggedAuthor;
	function __construct()
	{
		
	
		$this->dbmanager=new DatabaseManager();

		
	}

	//get logged author
	public function getLoggedAuthor($author,$keys){

		ob_start();
		//if(session_status() != 2) {session_start();}
		 if (session_id() == "") session_start();
		$keys=json_decode($keys);
		if ($author==null)
		{

			
			echo $_SESSION['consumer']=$keys[0]->consumer;
			echo $_SESSION['secret']=$keys[0]->secret;
			echo $_SESSION['token']=$keys[0]->token;
			echo $_SESSION['token_secret']=$keys[0]->token_secret;
			//elaboration passes to publicationsmanager.php
			header('Location: publicationsmanager.php?author=true');

		}

	    else {

	    	ob_end_flush();
	        //if(session_status() != 2) {session_start();}
	        if (session_id() == "") session_start();
			echo stripslashes(json_encode($author));
			$_SESSION['author']=$author;
			//var_dump($_SESSION);

		}
			//var_dump($keys);
		 	
	}


	//insert a new author and import his publications from Mendeley
	public function insertAuthor($id_author,$forename,$surname, $keys){
	
		
		$response=$this->dbmanager->insertAuthor($id_author,$forename,$surname);//insert author 
		$keys=json_decode($keys);
		//if(session_status() != 2) {session_start();}
		if (session_id() == "") session_start();
		if (isset($_SESSION['publications'])) $_SESSION['publications']=array();
		$_SESSION['consumer']=$keys[0]->consumer;
		$_SESSION['secret']=$keys[0]->secret;
		$_SESSION['token']=$keys[0]->token;
		$_SESSION['token_secret']=$keys[0]->token_secret;

		//elaboration passes to publicationsmanager.php. Get data from Mendeley
		header('Location: publicationsmanager.php');
			
		
	}

	//insert publications
	public function insertPublications($publications){

		
		return $this->dbmanager->insertPublications($publications);

	}

	//get related publications passing author id_author
	
	public function getAuthorPublications($id_author){

		$data=$this->dbmanager->getAuthorPublications($id_author);

		$size=count($data);

		//convert field authors in associative array
		for ($i=0; $i <$size ; $i++) { 

			//separe authors
			//str_replace('\u0000\u0000\u0000', '', $data[$i]->authors);
			$authorC=explode('[#]', $data[$i]->authors);
			$sizeAuth=count($authorC);
			$authorsSeparated=array();

			//separe forename and surname
			for ($j=0; $j <$sizeAuth ; $j++) { 

				# code...
				$array=explode('[*]', $authorC[$j]);
				$forename=$array[0];
				$surname=$array[1];
				//for each publication, put separated data into authorsSeparated
				array_push($authorsSeparated, array('forename'=>$forename, 'surname'=>$surname));

			}

			//set converted data
			$data[$i]->authors=$authorsSeparated;
		}

		//var_dump($data);
		//encode data in json format
		$data_json=json_encode($data);
		//return data
		return $data_json;

	}

	//if id_author is empty, return all exlcuded publications
	public function getExcludedAuthorPublications($id_author){

		$data=$this->dbmanager->getExcludedAuthorPublications($id_author);

		$size=count($data);

		//convert field authors in associative array
		for ($i=0; $i <$size ; $i++) { 

			//separe authors
			//str_replace('\u0000\u0000\u0000', '', $data[$i]->authors);
			$authorC=explode('[#]', $data[$i]->authors);
			$sizeAuth=count($authorC);
			$authorsSeparated=array();

			//separe forename and surname
			for ($j=0; $j <$sizeAuth ; $j++) { 

				# code...
				$array=explode('[*]', $authorC[$j]);
				$forename=$array[0];
				$surname=$array[1];
				//for each publication, put separated data into authorsSeparated
				array_push($authorsSeparated, array('forename'=>$forename, 'surname'=>$surname));

			}

			//set converted data
			$data[$i]->authors=$authorsSeparated;
		}

		//var_dump($data);
		//encode data in json format
		$data_json=json_encode($data);
		//return data
		return $data_json;

	}

	//delete author and his publications
	public function deleteAuthor($id_author){

	
		//if(session_status() != 2) {session_start();}
		if (session_id() == "") session_start();
		//if logged author == id author passed
		if($id_author==$_SESSION['author']['profile_id']){

			return $this->dbmanager->deleteAuthor($id_author);
		}

		else {

			return 'Not allowed...';
		}
	}

	//get all author
	public function getListAuthors(){

		//get data from database
		$data=$this->dbmanager->getListAuthors();
		//encode data in json format
		$data_json=json_encode($data);
		//return data
		return  $data_json;

		
		
	}

		//get forename and surname for updating author publications
	public function getForenameSurname($id_author){

		$data=$this->dbmanager-> getForenameSurname($id_author);
		$data_json=json_encode($data);
		//return data
		return  $data_json;

	}

	//delete publication for an author
	public function excludePublication($canonical_id,$id_author){

		//if(session_status() != 2) {session_start();}
		if (session_id() == "") session_start();
		//if logged author == id author passed
		if($id_author==$_SESSION['author']['profile_id']){

			return $this->dbmanager->excludePublication($canonical_id,$id_author);
		}

		else {

			return 'Not allowed...';
		}

		
	}

	//delete excluded publication for an author
	public function deleteExcludedPublication($canonical_id,$id_author){

		//if(session_status() != 2) {session_start();}
		if (session_id() == "") session_start();
		//if logged author == id author passed
		if($id_author==$_SESSION['author']['profile_id']){

			return $this->dbmanager->deleteExcludedPublication($canonical_id,$id_author);
		}

		else {

			return 'Not allowed...';
		}

		
	}


	public function restoreExlcudedPublication($canonical_id,$id_author){

	
		//if(session_status() != 2) {session_start();}
		if (session_id() == "") session_start();
		//if logged author == id author passed
		if($id_author==$_SESSION['author']['profile_id']){

			return $this->dbmanager->restoreExlcudedPublication($canonical_id,$id_author);
		}

		else {

			return 'Not allowed...';
		}
	}


	public function getOrderTypePublications(){

		
		//get data from database
		$data=$this->dbmanager->getOrderTypePublications();
		//encode data in json format
		$data_json=json_encode($data);
		//return data
		return  $data_json;
	}

	public function setOrderTypePublications($orderPublications){

		$data=json_decode(stripslashes($orderPublications));
			
		
		return $this->dbmanager->setOrderTypePublications($data);
	}

	public function getOrderFields(){

		//get data from database
		$data=$this->dbmanager->getOrderFields();
		//encode data in json format
		$data_json=json_encode($data);
		//return data
		return  $data_json;
	}

	public function setOrderFields($orderFields){

		$data=json_decode(stripslashes($orderFields));

		return $this->dbmanager->setOrderFields($data);
	}

	public function getCssFields(){

		//get data from database
		$data=$this->dbmanager->getCssFields();
		//encode data in json format
		$data_json=json_encode($data);
		//return data
		return  $data_json;
		
	}

	public function setCssFields($cssFields){

		$data=json_decode(stripslashes($cssFields));
		return $this->dbmanager->setCssFields($data);

		
	}


	public function setVisibleFields($visibFields){

		$data=json_decode(stripslashes($visibFields));
		return $this->dbmanager->setVisibleFields($data);

	}

	public function setLabelForFields($labelForFields){

		$data=json_decode(stripslashes($labelForFields));
		return $this->dbmanager->setLabelForFields($data);

	}


	public function getPreviewAuthorPublications($id_author){

		//get data from database
		$data=$this->dbmanager->getPreviewAuthorPublications($id_author);

		// array css (last element)
		$formatCSS=array_pop($data);

		//labelfor fields
		$labelfor=array_pop($data);

		//number of publications
		$sizePublications=count($data);
		
		$sizeFormatCSS=count($formatCSS);

		$output='';
		//current publication type
		$currentType='';

		$counter=1;

		for ($i=0; $i <$sizePublications ; $i++) { 

			$date='';
			$setDate=false;
			//set type publication
			$type=$data[$i]->type;
			
			switch ($type) {
				case 'Journal Article':
					$type='Journal Articles';
					break;
				
				case 'Magazine Article':
					$type='Magazine Articles';
					break;

				case 'Conference Proceedings':
					$type='Conference & Workshop Proceedings';
					break;
			}

			//create list based on publication type
			if ($currentType!=$type){

				$label='<label ';
				$class='class="lbl-type" ';
				$style='style="';
				$labelClose='</label>';

				for ($j=0;$j<$sizeFormatCSS;$j++){
						
						if ($formatCSS[$j]->field=='type')

							$style.=$formatCSS[$j]->propertyCSS.':'.$formatCSS[$j]->valueCSS.';';
					
				}

				$style.='">';

				$label.=$class.$style.$type.$labelClose;
				$output.='<br>'.$label.'<br>';
				$currentType=$type;
				$counter=1;
			}

		
			$output.='<ol type="1." start="'.$counter.'" style="margin-bottom:0px;"><li class="li-previewPublication">';
			$counter++;
			
			//put publications into list 
			foreach ($data[$i] as $key => $value) {

			 	if (($key!='website')&&($key!='mendeley_url')&&(!is_null($value))&&(!empty($value))){


			 		$label='';
			 		$field=$key;
			 		$text='';
			 		
			 		
			 		//format date
					if (($field=='day')||($field=='month')||($field=='year')){

						

						if ($setDate==false){

							$controlDate=0;
							//format date
							foreach ($data[$i] as $field => $value) {

							  $label='';
							  
							  if (($field=='day')||($field=='month')||($field=='year')){

							  		if (($value==NULL)||($value=='')||($value=="0")) continue; //if values are null or empty or == 0

							  		else{
							  	
							  		
							  		$controlDate++;

									if (isset($labelfor[$field])&&($labelfor[$field]!='')){

							 			$label=$labelfor[$field];
							 		}

							 		if ($field=='month') $value=$this->getMonth($value); //month as string

									if ($controlDate==1) $text=$value;
							 		else $text=" ".$value;

									

									
									//create html tag
									$label.='<label ';
									$class='class="lbl-'.$field.'" ';
									$style='style="';

									//find and set css field properties
									for ($j=0;$j<$sizeFormatCSS;$j++){

											if ($field==$formatCSS[$j]->field)

												$style.=$formatCSS[$j]->propertyCSS.':'.$formatCSS[$j]->valueCSS.';';

										
									}

									//complete html tag
									$style.='">';
									$labelClose='</label>';
									$label.=$class.$style.$text.$labelClose;
									$date.=$label;

							  }

					
							 }
							
							}
							$setDate=true;
							$output.=$date;
							
						} else continue;


					}
					//if field is not part of date
				 	else {
					 		//set field labelfor
				 		if (isset($labelfor[$field])&&($labelfor[$field]!='')){

				 			$label.=$labelfor[$field].' ';
				 		}
					 	//create html tag
						$label.='<label ';
						$class='class="lbl-'.$key.'" ';
						$style='style="';

						//find and set css field properties
						for ($j=0;$j<$sizeFormatCSS;$j++){

								if ($field==$formatCSS[$j]->field)

									$style.=$formatCSS[$j]->propertyCSS.':'.$formatCSS[$j]->valueCSS.';';

							
						}

						//complete html tag
						$style.='">';
						
						//format authors
						if ($field=='authors'){

							$value=$this->formatAuthors($value,$id_author);
						}
						//format editors
						elseif ($field=='editors'){

							$value=$this->formatEditors($value);
						}


						$text=$value;
						rtrim($text);

						$labelClose='</label>';

						$label.=$class.$style.$text.$labelClose;

						//set mendeley link  publication
						if ($field=='title'){

							$label='<a href="'.$data[$i]->mendeley_url.'" style="text-decoration:none;">"'.$label.'"</a>';
						}
						//set doi link publication
						elseif ($field=='doi') {
							
							$label='<a href="http://dx.doi.org/'.$data[$i]->doi.'" style="text-decoration:none;">'.$label.'</a>';
						}
						
						//do nothing
						elseif ($field=='type') {

							continue;
							# code...
						}
						
					
				   }

				   $output.=$label.', ';
				}

				
			}
			//rtrim($output);
			$output=substr($output,0,strlen($output)-2);
			$output.='.</li></ol>';
			//$output.='<br>';


		}
		//var_dump($publicationType);
		return $output;
}

//format authors output
private function formatAuthors($authors,$id_author){


	//convert field authors in associative array
		//separe authors
		$output='';
		$authorC=explode('[#]', $authors);
		$sizeAuth=count($authorC);
		$authorsSeparated=array();

		//separe forename and surname
		for ($j=0; $j <$sizeAuth ; $j++) { 

			# code...
			$array=explode('[*]', $authorC[$j]);
			$forename=$array[0][0];//set only initial letter of name
		    $surname=$array[1];
			//for each publication, put separated data into authorsSeparated
			array_push($authorsSeparated, array('forename'=>$forename, 'surname'=>$surname));
			

		}

		//get author forename and surname to set bold
		//var_dump($authorsSeparated);
		$nameAuthor=json_decode($this->getForenameSurname($id_author));
		$size=count($authorsSeparated);

		for($i=0;$i<$size;$i++) {
			
			if (strtolower($authorsSeparated[$i]['surname'])==strtolower($nameAuthor[0]->surname)){
				
				if(strtolower($authorsSeparated[$i]['forename'])==strtolower($nameAuthor[0]->forename[0])){
						
					 $surname='<b>'.$authorsSeparated[$i]['surname'].'</b>';
					 $forename='<b>'.$authorsSeparated[$i]['forename'].'.</b>';
				}

			}
			else{

				$surname=$authorsSeparated[$i]['surname'];
				$forename=$authorsSeparated[$i]['forename'].'.';
			}

			
			$output.=$forename.' '.$surname.', ';
		}
		$output=substr($output,0,strlen($output)-2);
		
		return $output;

}

//set editors output
private function formatEditors($editors){


	//convert field authors in associative array
		//separe authors
		$output='';
		$authorC=explode('[#]', $editors);
		$sizeAuth=count($authorC);
		$authorsSeparated=array();

		//separe forename and surname
		for ($j=0; $j <$sizeAuth ; $j++) { 

			# code...
			$array=explode('[*]', $authorC[$j]);
			$forename=$array[0][0];//set only initial letter of name
		    $surname=$array[1];
			//for each publication, put separated data into authorsSeparated
			array_push($authorsSeparated, array('forename'=>$forename, 'surname'=>$surname));
			

		}

		$size=count($authorsSeparated);

		for($i=0;$i<$size;$i++) {
			
			

				$surname=$authorsSeparated[$i]['surname'];
				$forename=$authorsSeparated[$i]['forename'].'.';
			

			
			$output.=$forename.' '.$surname.', ';
		}
		$output=substr($output,0,strlen($output)-2);
		
		return $output;

}


private function getMonth($m){

	$month="";
	switch ($m) {
		case '1':
			$month="Jan.";
			break;
		case '2':
			$month="Feb.";
			break;
		case '3':
			$month="Mar.";
			break;
		case '4':
			$month="Apr.";
			break;
		case '5':
			$month="May.";
			break;
		case '6':
			$month="June";
			break;
		case '7':
			$month="July.";
			break;
		case '8':
			$month="Aug.";
			break;
		case '9':
			$month="Sept.";
			break;
		case '10':
			$month="Oct.";
			break;
		case '11':
			$month="Nov.";
			break;
		case '12':
			$month="Dec.";
			break;
		
	}

	return $month;
}


}

}


?>