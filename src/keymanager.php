<?php

/**
* 
*/

if (!class_exists("DatabaseManager")) {

    include_once 'databasemanager.php';
}



class KeyManager 
{


	private $dbmanager;

	function __construct()
	{
		//create table OAuthMendeleyKeys to storage all keys, even if table doesn't exist
		//$keydb it's a class that allows interfacing with Wordpress Database
		$this->dbmanager=new DatabaseManager();
		
		
	}

	//update keys and tokens
	public function updateKeys($consumer,$secret,$token,$token_secret){

		//delete all keys from table and insert new keys
		
		$response=$this->dbmanager->setKeys($consumer,$secret,$token,$token_secret);
		if ($response==false) return 'Error saving keys...';
		else return 'Keys saved...';
		

	}

	
	//get keys and tokens
	public function getKeys(){
		
		$data_json=json_encode($this->dbmanager->getKeys());
		return $data_json;
		

	}

	

	
}







?>