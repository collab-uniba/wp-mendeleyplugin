<?php


ob_start();
include('../../../wp-load.php');//import wp-load.php to interface wordpress database with wpdb class
ob_end_clean();//clean respose from wp-load

/**
* 
*/
if (!class_exists("DatabaseManager")) {
class DatabaseManager 
{

	
	
	function __construct()
	{
		global $wpdb;//class of functions for all database manipulations
		//create tables
		$wpdb->query('create table if not exists mendeley_key(consumer varchar(60), secret varchar(60),token varchar(60), token_secret varchar(60), primary key c(consumer,secret));');

		$wpdb->query('create table if not exists mendeley_author(id_author varchar(20), forename varchar(20), surname varchar(40), primary key ident(id_author));');

		$wpdb->query('create table if not exists mendeley_publication(canonical_id varchar(50), authors varchar(500), title varchar(200), 
					publication_outlet varchar(200), mendeley_url varchar(500),
 					volume varchar(5), issue varchar(5), pages varchar(10), publisher varchar(20), year varchar(4),
 					doi varchar(50), website varchar(500), isbn varchar(30), issn varchar(30),type varchar(40), abstract text,
 					city varchar(30), day varchar(2), month varchar(15), editors varchar(100), edition varchar(20), chapter varchar(5), type_of_work varchar(50),
 					institution varchar(50), department varchar(50), university varchar(50), number varchar(10), series varchar(20),
 				 	primary key pk(canonical_id));');

		$wpdb->query('create table if not exists mendeley_authored(publication varchar(50), author varchar(20), primary key p(publication,author), 
					foreign key fk_p(publication) references mendeley_publication(canonical_id),
					foreign key fk_a(author) references mendeley_author(id_author));');

		$wpdb->query('create table if not exists mendeley_excluded_publication(publication varchar(50), author varchar(20),
					primary key p(publication,author),
					foreign key fk_p(publication) references mendeley_publication(canonical_id),
					foreign key fk_a(author) references mendeley_author(id_author));');


		$wpdb->query('create table if not exists mendeley_order_type_publications(type varchar(40), orderType int unique, 
					primary key tk(type));');

		$wpdb->query('create table if not exists mendelely_format_publications (field varchar(25), propertyCSS varchar(15), valueCSS varchar(30),
					  primary key pf(field, propertyCSS));');

		$wpdb->query('create table if not exists mendeley_order_field(field varchar(25), orderField int unique,  shown varchar(1), sample varchar(30), labelfor varchar(20), primary key fk(field));');
		
		$this->insertFieldOrderPublication();
		
	}




	//returns consumer key and sercret key
	public function getKeys(){

		global $wpdb;//class of functions for all database manipulations

		$rows = $wpdb->get_results( 'SELECT * from mendeley_key;' );
		
		return $rows;


	} 

	//set consumer key and secret key
	public function setKeys($consumer,$secret,$token,$token_secret){

		global $wpdb;//class of functions for all database manipulations

		//delete all rows from mendeley_keys 
		$wpdb->query('delete from mendeley_key;');
		return $wpdb->query('insert into mendeley_key() values("'.$consumer.'","'.$secret.'","'.$token.'","'.$token_secret.'");');

	}

	//get related publications passing author id_author
	//if id_author is empty, return all publications
	public function getAuthorPublications($id_author){

		global $wpdb;//class of functions for all database manipulations

			$query='select canonical_id, authors, title, publication_outlet, abstract, mendeley_url, volume, issue, pages, publisher, year, doi, website, isbn,
					   issn, type from mendeley_publication, mendeley_authored,mendeley_author
					   where canonical_id=publication and id_author=author and author="'.$id_author.'";';

		//execute query
		$rows=$wpdb->get_results($query);
		//return data
		return $rows;


	}


	//get related excluded publications passing author id_author
	//if id_author is empty, return all excluded publications
	public function getExcludedAuthorPublications($id_author){

		global $wpdb;//class of functions for all database manipulations
		
			$query='select canonical_id, authors, title, publication_outlet, abstract, mendeley_url, volume, issue, pages, publisher, year, doi, website, isbn,
					   issn, type from mendeley_publication, mendeley_excluded_publication
					   where canonical_id=publication and author="'.$id_author.'";';

		
		//execute query
		$rows=$wpdb->get_results($query);
		//return data
		return $rows;


	}



	//delete author and his publications
	public function deleteAuthor($id_author){

		global $wpdb;//class of functions for all database manipulations

		//delete references from mendeley_authored
		$query='delete from mendeley_authored where author="'.$id_author.'";';
		$wpdb->query($query);

		
		//delete from excluded publications
		$query='delete from mendeley_excluded_publication where author="'.$id_author.'";';
		$wpdb->query($query);

		//delete related publications. If there are publications related to authors saved into database, that publications will be not deleted
		$query='delete from mendeley_publication where canonical_id not in 
				(select publication from  mendeley_authored );';

		$wpdb->query($query);
		//delete author
		$query='delete from mendeley_author where id_author="'.$id_author.'";';
		$wpdb->query($query);



	}

	//get all author
	public function getListAuthors(){

		global $wpdb;//class of functions for all database manipulations
		
		//get total authors
		$query='select id_author from mendeley_author;';
		//execute query
		$rows=$wpdb->get_results($query);

		$wpdb->flush();
		//read data
		$n_authors=count($rows);

		$data=array();
		for ($i=0; $i <$n_authors ; $i++) { 
		
		$id_author=$rows[$i]->id_author;
		$query='select (select id_author from mendeley_author where id_author="'.$id_author.'") as nick, 
					   (select forename from mendeley_author where id_author="'.$id_author.'") as fname,
					   (select surname from mendeley_author where id_author="'.$id_author.'") as sname,
					   (select count(*) from mendeley_author,mendeley_authored,mendeley_publication where author="'.$id_author.'" and canonical_id=publication group by id_author limit 1) as publ,
					   (select count(*) from mendeley_author,mendeley_excluded_publication,mendeley_publication where author="'.$id_author.'"  and canonical_id=publication group by id_author limit 1) as publ_ex;';
		//execute query
		$rows_data=$wpdb->get_results($query);
		$wpdb->flush();
		array_push($data, $rows_data);
		//return data

		}
		
		return $data;
		
	}

	


	//delete publication from mendeley_publication and save canonical_id into mendeley_excluded_publication
	public function excludePublication($canonical_id,$id_author){

		global $wpdb;//class of functions for all database manipulations

		
		$query='insert into mendeley_excluded_publication(publication, author)  values("'.$canonical_id.'","'.$id_author.'");';
					   
		$wpdb->query($query);
		//delete canonical_id and id_author from mendeley_authored
		$query='delete from mendeley_authored where publication="'.$canonical_id.'" and author="'.$id_author.'";';
		//exec query
		$wpdb->query($query);
		


	}



	//delete canonical_id and id_author from mendeley_excluded_publication
	public function deleteExcludedPublication($canonical_id,$id_author){

		global $wpdb;//class of functions for all database manipulations

		$query='delete from mendeley_excluded_publication where publication="'.$canonical_id.'" and author="'.$id_author.'";';

		 $wpdb->query($query);

		 //	if there are no corrispondences with other authors, delete publication 
		 $query='delete from mendeley_publication where canonical_id= not in (select canonical_id from mendeley_authored)';
		  $wpdb->query($query);

		//if there are no more publication related in excluded publications, delete author and 
		$query='select count(*) as n_pub from mendeley_publication,mendeley_authored where 
				canonical_id=publication and author="'.$id_author.'";';

		$rows=$wpdb->get_results($query);

		if($rows[0]->n_pub==0){

			$this->deleteAuthor($id_author);
			 //	if there are no corrispondences with other authors, delete publication 
		 $query='delete from mendeley_publication where canonical_id= not in (select canonical_id from mendeley_authored)';
		  $wpdb->query($query);
		}
	}

	//restore excluded publication
	public function restoreExlcudedPublication($canonical_id,$id_author){

		global $wpdb;//class of functions for all database manipulations
		$query='insert into mendeley_authored(publication, author)  values("'.$canonical_id.'","'.$id_author.'");';
					   
		$wpdb->query($query);
		//delete canonical_id and id_author from mendeley_authored
		$query='delete from mendeley_excluded_publication where publication="'.$canonical_id.'" and author="'.$id_author.'";';
		//exec query
		$wpdb->query($query);


	}


	//insert publication details
	public function insertPublications($publications){

		global $wpdb;//class of functions for all database manipulations

		//get array size
		$size=count($publications);//publications is a multidimensional array

		//set query
		if (!empty($publications))
		{
				for ($i=0; $i <$size ; $i++) { 
						
						$query_col='insert ignore into mendeley_publication(';
						$query_data=') values(';

						//create insert query
						if (isset($publications[$i]['canonical_id'])){

							$canonical_id=$publications[$i]['canonical_id'];
							$query_col.='canonical_id,';
							$query_data.='"'.$canonical_id.'",';
						}

						if (isset($publications[$i]['title'])){

							$title=str_replace('"', '', $publications[$i]['title']);
							//$title=$publications[$i]['title'];
							$query_col.='title,';
							$query_data.='"'.$title.'",';
						}

						if (isset($publications[$i]['publication_outlet'])){

							$publication_outlet=str_replace('"', '', $publications[$i]['publication_outlet']);
							//$publication_outlet=$publications[$i]['publication_outlet'];
							$query_col.='publication_outlet,';
							$query_data.='"'.$publication_outlet.'",';
						}

						if (isset($publications[$i]['abstract'])){

							$abstract=str_replace('"', '', $publications[$i]['abstract']);
							//$abstract=$publications[$i]['abstract'];
							$query_col.='abstract,';
							$query_data.='"'.$abstract.'",';
						}

						if (isset($publications[$i]['volume'])){

							$volume=str_replace('"', '', $publications[$i]['volume']);
							//$volume=$publications[$i]['volume'];
							$query_col.='volume,';
							$query_data.='"'.$volume.'",';
						}

						if (isset($publications[$i]['issue'])){

							$issue=str_replace('"', '', $publications[$i]['issue']);
							//$issue=$publications[$i]['issue'];
							$query_col.='issue,';
							$query_data.='"'.$issue.'",';
						}
						
						if (isset($publications[$i]['publisher'])){

							$publisher=str_replace('"', '', $publications[$i]['publisher']);
							//$publisher=$publications[$i]['publisher'];
							$query_col.='publisher,';
							$query_data.='"'.$publisher.'",';
						}
						
						if (isset($publications[$i]['year'])){

							$year=$publications[$i]['year'];
							$query_col.='year,';
							$query_data.='"'.$year.'",';
						}
						
						if (isset($publications[$i]['pages'])){

							$pages=$publications[$i]['pages'];
							$query_col.='pages,';
							$query_data.='"'.$pages.'",';
						}	
						
						if (isset($publications[$i]['website'])){

							$website=$publications[$i]['website'];
							$query_col.='website,';
							$query_data.='"'.$website.'",';
						}	
						
						if (isset($publications[$i]['mendeley_url'])){

							$mendeley_url=$publications[$i]['mendeley_url'];
							$query_col.='mendeley_url,';
							$query_data.='"'.$mendeley_url.'",';
						}	
						
						
						if (isset($publications[$i]['doi'])){

							$doi=$publications[$i]['doi'];
							$query_col.='doi,';
							$query_data.='"'.$doi.'",';
						}	
						
						if (isset($publications[$i]['issn'])){

							$issn=$publications[$i]['issn'];
							$query_col.='issn,';
							$query_data.='"'.$issn.'",';
						}	

						if (isset($publications[$i]['isbn'])){

							$isbn=$publications[$i]['isbn'];
							$query_col.='isbn,';
							$query_data.='"'.$isbn.'",';
						}	

						if (isset($publications[$i]['type'])){

							$type=$publications[$i]['type'];
							$query_col.='type,';
							$query_data.='"'.$type.'",';
							$this->insertTypePublication($type);
						}

						if (isset($publications[$i]['city'])){

							$city=$publications[$i]['city'];
							$query_col.='city,';
							$query_data.='"'.$city.'",';
							
						}

						if (isset($publications[$i]['day'])){

							$day=$publications[$i]['day'];
							$query_col.='day,';
							$query_data.='"'.$day.'",';
							
						}

						if (isset($publications[$i]['month'])){

							$month=$publications[$i]['month'];
							$query_col.='month,';
							$query_data.='"'.$month.'",';
							
						}

						
						if (isset($publications[$i]['edition'])){

							$edition=$publications[$i]['edition'];
							$query_col.='edition,';
							$query_data.='"'.$edition.'",';
							
						}

						if (isset($publications[$i]['chapter'])){

							$edition=$publications[$i]['chapter'];
							$query_col.='chapter,';
							$query_data.='"'.$chapter.'",';
							
						}

						if (isset($publications[$i]['type_of_work'])){

							$type_of_work=$publications[$i]['type_of_work'];
							$query_col.='type_of_work';
							$query_data.='"'.$type_of_work.'",';
							
						}

						if (isset($publications[$i]['institution'])){

							$institution=$publications[$i]['institution'];
							$query_col.='institution,';
							$query_data.='"'.$institution.'",';
							
						}

						if (isset($publications[$i]['department'])){

							$department=$publications[$i]['department'];
							$query_col.='department,';
							$query_data.='"'.$department.'",';
							
						}

						if (isset($publications[$i]['university'])){

							$university=$publications[$i]['university'];
							$query_col.='university,';
							$query_data.='"'.$university.'",';
							
						}

						if (isset($publications[$i]['number'])){

							$number=$publications[$i]['edition'];
							$query_col.='number,';
							$query_data.='"'.$number.'",';
							
						}

						if (isset($publications[$i]['series'])){

							$series=$publications[$i]['series'];
							$query_col.='series,';
							$query_data.='"'.$series.'",';
							
						}


						if (isset($publications[$i]['authors'])){

							//set author field
							$auth=str_replace('"', '', $publications[$i]['authors']);
							$authors='';
							$size_auth=sizeof($auth);
							for ($j=0; $j <$size_auth ; $j++) { 
								# code...
								//get forename and surname authors. Substrings '\*/' and '[#]' separe forename  surname and authors
								if ($j==($size_auth-1))
								$authors.=$publications[$i]['authors'][$j]->forename.'[*]'.$publications[$i]['authors'][$j]->surname;

								else
									$authors.=$publications[$i]['authors'][$j]->forename.'[*]'.$publications[$i]['authors'][$j]->surname.'[#]';

							}
							
							$query_col.='authors';
							$query_data.='"'.$authors.'"';


						}

						if (count($publications[$i]['editors'])>0){

							//set editor field
							$editors=str_replace('"', '', $publications[$i]['editors']);
							$editors='';
							$size_editors=sizeof($editors);
							for ($j=0; $j <$size_editors ; $j++) { 
								# code...
								//get forename and surname authors. Substrings '\*/' and '[#]' separe forename  surname and authors
								if ($j==($size_editors-1))
								$editors.=$publications[$i]['editors'][$j]->forename.'[*]'.$publications[$i]['editors'][$j]->surname;

								else
									$editors.=$publications[$i]['editors'][$j]->forename.'[*]'.$publications[$i]['editors'][$j]->surname.'[#]';

							}
							
							$query_col.=',editors';
							$query_data.=',"'.$editors.'");';
							
						}

						else {

							
							$query_data.=');';

						}


					 	$query=$query_col.$query_data;

					 	//exec query
					    $wpdb->query($query);


			    }
			    //
			    //create references in authored table
			   return $this->insertAuthored($publications);
			   
		}

		

	}

	 //create references in authored table
	private function insertAuthored($publications){


		global $wpdb;//class of functions for all database manipulations
		//get array size
		$size=count($publications);//publications is a multidimensional array
		//echo $_SESSION['id_author_mendeley'];
		for ($i=0; $i <$size ; $i++) { 
		
				$query_col='insert into mendeley_authored(';
				$query_data=') values(';

				//create insert query
				if (isset($publications[$i]['canonical_id'])){

					$canonical_id=$publications[$i]['canonical_id'];
					$query_col.='publication,';
					$query_data.='"'.$canonical_id.'",';
				}


				$query_col.='author';
				
				$query_data.='"'.$_SESSION['id_author_mendeley'].'");';
				
				$query=$query_col.$query_data;

			    $wpdb->query($query);
			    

		}

		$query='delete from mendeley_authored where author="'.$_SESSION['id_author_mendeley'].'" and publication in 
				(select publication from mendeley_excluded_publication where author="'.$_SESSION['id_author_mendeley'].'");';
		$wpdb->query($query);

		$_SESSION['id_author_mendeley']='';
	
	}

	//insert new author
	public function insertAuthor($id_author,$forename,$surname){

		session_start();
		global $wpdb;//class of functions for all database manipulations

		//insert author 
		$query='insert ignore into mendeley_author() values("'.$id_author.'","'.$forename.'","'.$surname.'");';
		 $response=$wpdb->query($query);
		 

		//save id_author into session variable. id_author will be used to update authored table
		

		$_SESSION['id_author_mendeley']=$id_author;
			

		return $response;

	}

	//get forename and surname for updating author publications
	public function getForenameSurname($id_author){

		global $wpdb;//class of functions for all database manipulations
		$query='select forename, surname from mendeley_author where id_author="'.$id_author.'";';

		//execute query
		$rows=$wpdb->get_results($query);
		//return data
		return $rows;

	}


	//insert publication type every time a publication is inserted
	public function insertTypePublication($typePublications){

		global $wpdb;//class of functions for all database manipulations

		$query='select count(*) as orderPublication from mendeley_order_type_publications;';

		//execute query
		$rows=$wpdb->get_results($query);
		$orderPublication=$rows[0]->orderPublication;

		$query='insert ignore into mendeley_order_type_publications() values("'.$typePublications.'",'.$orderPublication.')';
		$wpdb->query($query);
		 


	}

	//get order type publications
	public function getOrderTypePublications(){

		global $wpdb;//class of functions for all database manipulations

		$query='select orderType, type  from mendeley_order_type_publications;';
		//execute query
		$rows=$wpdb->get_results($query);
		return $rows;
	}


	public function setOrderTypePublications($orderPublications){
		
		global $wpdb;//class of functions for all database manipulations
		//$query_ins='';
		$query='delete from mendeley_order_type_publications;';
		//execute query
		$wpdb->query($query);
		$wpdb->flush();
		$size=count($orderPublications);
		
		for ($i=0; $i <$size ; $i++) { 

			$query_col='insert ignore into mendeley_order_type_publications(';
			$query_data=') values(';

			//create insert query
			if (isset($orderPublications[$i]->type)){

				$type=$orderPublications[$i]->type;
				$query_col.='type,';
				$query_data.='"'.$type.'",';
			}

			if (isset($orderPublications[$i]->orderType)){

				$orderType=$orderPublications[$i]->orderType;
				$query_col.='orderType';
				$query_data.='"'.$orderType.'");';

			}

			$query=$query_col.$query_data;

		 	//exec query
		   $wpdb->query($query);

		}

	
	}


	//populate table mendeley_order_field
	public function insertFieldOrderPublication(){

		global $wpdb;//class of functions for all database manipulations
		$query='select count(*) as existsTable from mendeley_order_field;';
		//exec query
		$rows=$wpdb->get_results($query);

		//if table is empty, populate table
		if ($rows[0]->existsTable==0){
		
			$query='insert ignore into mendeley_order_field() values("type",0,"y","Publication type",""),
																	("authors",1,"y","John Red, Mike White, James Green",""),
																	("title",2,"y","Computer science",""),
																	("publication_outlet",3,"y","Empirical Software Engineering Journal",""),
																	("volume",4,"y","vol. 1",""),
																	("issue",5,"y","no. 15",""),
																	("pages",6,"y","pp. 20-38",""),
																	("publisher",7,"y","Publisher",""),
																	("year",8,"y","2013",""),
																	("isbn",9,"y","000-000-0-125-222",""),
																	("issn",10,"y","1258-358-0",""),
																	("abstract",11,"y","Abstract",""),
																	("canonical_id",12,"y","0000-0000-0000-0000-0000",""),
																	("doi",13,"y","10112/search.doi",""),
																	("city",14,"y","Bari",""),
																	("day",15,"y",25,""),
																	("month",16,"y","12",""),
																	("editors",17,"y","New Editors",""),
																	("chapter",18,"y","3",""),
																	("type_of_work",19,"y","Interesting work",""),
																	("institution",20,"y","Public institution",""),
																	("department",21,"y","DIB",""),
																	("university",22,"y","University of Bari",""),
																	("number",23,"y","45",""),
																	("series",24,"y","25",""),
																	("doi",25,"y","000.000/258754","");';
			$wpdb->query($query);

			

		}
		 


	}


	public function getOrderFields(){

		global $wpdb;//class of functions for all database manipulations
		$query='select * from mendeley_order_field order by orderField;';
		//exec query
		$rows=$wpdb->get_results($query);
		return $rows;

	}

	//set order fields
	public function setOrderFields($orderFields){

		global $wpdb;//class of functions for all database manipulations
		var_dump($orderFields);
		$query='delete from mendeley_order_field;';
		//execute query
		$wpdb->query($query);
		$wpdb->flush();

		$size=count($orderFields);
		
		for ($i=0; $i <$size ; $i++) { 

			$query_col='insert ignore into mendeley_order_field(';
			$query_data=') values(';

			//create insert query
			if (isset($orderFields[$i]->field)){

				$field=$orderFields[$i]->field;
				$query_col.='field,';
				$query_data.='"'.$field.'",';
			}

			if (isset($orderFields[$i]->orderField)){

				$orderField=$orderFields[$i]->orderField;
				$query_col.='orderField,';
				$query_data.='"'.$orderField.'",';

			}

			if (isset($orderFields[$i]->shown)){

				$shown=$orderFields[$i]->shown;
				$query_col.='shown,';
				$query_data.='"'.$shown.'",';

			}

			if (isset($orderFields[$i]->sample)){

				$sample=$orderFields[$i]->sample;
				$query_col.='sample,';
				$query_data.='"'.$sample.'",';

			}


			if (isset($orderFields[$i]->labelfor)){

				$labelfor=$orderFields[$i]->labelfor;
				$query_col.='labelfor';
				$query_data.='"'.$labelfor.'");';

			}

			$query=$query_col.$query_data;

		 	//exec query
		   $wpdb->query($query);

		}

	}

	//get css property fields
	public function getCssFields(){

		global $wpdb;//class of functions for all database manipulations
		$query='select * from mendelely_format_publications;';
		//exec query
		$rows=$wpdb->get_results($query);
		return $rows;

	}

	//set css property fields
	public function setCssFields($cssFields){

		global $wpdb;//class of functions for all database manipulations
		$field=$cssFields->field;
		$propertyCSS=$cssFields->propertyCSS;
		$valueCSS=$cssFields->valueCSS;
		$query='select * from mendelely_format_publications where field="'.$field.'" and propertyCSS="'.$propertyCSS.'";';
		//exec query
		$rows=$wpdb->get_results($query);

		if (count($rows)==0){

			$query='insert into mendelely_format_publications() values("'.$field.'","'.$propertyCSS.'","'.$valueCSS.'");';

		}

		else {

			$query='update mendelely_format_publications set valueCSS="'.$valueCSS.'" where field="'.$field.'" and propertyCSS="'.$propertyCSS.'";';

		}

		//exec query
		    $wpdb->query($query);

	}

	//set visibility field
	public function setVisibleFields($visibFields){

		global $wpdb;//class of functions for all database manipulations
		$field=$visibFields->field;
		$shown=$visibFields->shown;

		$query='update mendeley_order_field set shown="'.$shown.'" where field="'.$field.'";';
		//exec query
		$wpdb->query($query);
		//var_dump($visibFields);

	}

	//set label associated to field
	public function setLabelForFields($labelForFields){

		var_dump($labelForFields);
		
		global $wpdb;//class of functions for all database manipulations
		$field=$labelForFields->field;
		$label=$labelForFields->labelfor;

		$query='update mendeley_order_field set labelfor="'.$label.'" where field="'.$field.'";';
		//exec query
		$wpdb->query($query);
		//var_dump($visibFields);

	}

	//get all data for publication list
	public function getPreviewAuthorPublications($id_author){

		//get data from database
		global $wpdb;//class of functions for all database manipulations
		
		//get shown fields ordered by orderField
		$query='select field from mendeley_order_field where shown="y" order by orderField;';
		$rows=$wpdb->get_results($query);

		$wpdb->flush();

		$size=count($rows);
		$fields='';

		//set query with shown fields
		for ($i=0;$i<$size;$i++){

			if ($rows[$i]->field=='type') $fields.='mendeley_publication.type, ';//not ambiguos
			else $fields.=$rows[$i]->field.', ';
		
		//exec query
		}
		//select all shown fields
		 $query='select '.$fields.' website, mendeley_url from mendeley_publication, mendeley_authored, mendeley_author, mendeley_order_type_publications
			where canonical_id=publication and id_author=author and id_author="'.$id_author.'" and  mendeley_order_type_publications.type=mendeley_publication.type order by orderType, year desc;';

		
		$publications=$wpdb->get_results($query);

		$wpdb->flush();
		//select all properties for shown field
		$query='select mendelely_format_publications.field, propertyCSS, valueCSS from mendelely_format_publications, mendeley_order_field 
				where mendeley_order_field.field=mendelely_format_publications.field and shown="y" order by orderField;';
	
		$formatCSS=$wpdb->get_results($query);

		//get shown labelfor ordered by orderField
		$query='select field, labelfor from mendeley_order_field where shown="y" order by orderField;';
		$labelfor=$wpdb->get_results($query);

		//transform labelfor in an associative array
		$labels=array();
		$sizeLabelFor=count($labelfor);

		for($i=0;$i<$sizeLabelFor;$i++){

			$key=$labelfor[$i]->field;
			$value=$labelfor[$i]->labelfor;
			$labels[$key]=$value;
		}

		array_push($publications, $labels);
		array_push($publications, $formatCSS);
		//return publications and field properties
		return $publications;
		


	}




}




}



?>