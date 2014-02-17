<?php


include('keymanager.php');
include('datamanager.php');
/**
* 
*/
class RequesterManager
{
	 private $keymamanger;
	 private $datamanager;

	function __construct()
	{

		$this->keymamanger=new KeyManager();
		$this->datamanager=new DataManager();
		//$this->dm=new DataManager();

		if (isset($_POST['request'])){


			if ($_POST['request']=='setKeys'){

				
				echo $this->updateKeys($_POST['consumer'],$_POST['secret']);
			}

			if ($_POST['request']=='getKeys') {
				
				
				echo $this->getKeys();
			}


			if ($_POST['request']=='insertAuthor') {
				
				
				echo $this->insertAuthor($_POST['id_author'],$_POST['forename'],$_POST['surname']);
			}


			if ($_POST['request']=='getListAuthors') {
				
				
				echo $this->getListAuthors();
			}

			if ($_POST['request']=='getForenameSurname') {
				
				
				echo $this->getForenameSurname($_POST['id_author']);
			}


			if ($_POST['request']=='deleteAuthor') {
				
				
				echo $this->deleteAuthor($_POST['id_author']);
			}

			//if id_author is empty, return all publications
			if ($_POST['request']=='getAuthorPublications') {
				
				
				echo $this->getAuthorPublications($_POST['id_author']);
			}

			if ($_POST['request']=='excludePublication') {
				
				
				echo $this->excludePublication($_POST['canonical_id'],$_POST['id_author']);
			}

			if ($_POST['request']=='deleteExcludedPublication') {
				
				
				echo $this->deleteExcludedPublication($_POST['canonical_id'],$_POST['id_author']);
			}


			if ($_POST['request']=='restoreExlcudedPublication') {
				
				
				echo $this->restoreExlcudedPublication($_POST['canonical_id'],$_POST['id_author']);
			}


			
			if ($_POST['request']=='getExcludedAuthorPublications') {
				
				
				echo $this->getExcludedAuthorPublications($_POST['id_author']);
			}


			
			if ($_POST['request']=='getOrderTypePublications') {
				
				
				echo $this->getOrderTypePublications();
			}


			
			if ($_POST['request']=='setOrderTypePublications') {
				
				
				echo $this->setOrderTypePublications($_POST['orderPublications']);
			}

			if ($_POST['request']=='getOrderFields') {
				
				
				echo $this->getOrderFields();
			}


			if ($_POST['request']=='setOrderFields') {
				
				
				echo $this->setOrderFields($_POST['orderFields']);
			}

			if ($_POST['request']=='getCssFields') {
				
				
				echo $this->getCssFields();
			}

			if ($_POST['request']=='setCssFields') {
				
				
				echo $this->setCssFields($_POST['cssFields']);
			}

			if ($_POST['request']=='setVisibleFields') {
				
				
				echo $this->setVisibleFields($_POST['visibField']);
			}

			if ($_POST['request']=='setLabelForFields') {
				
				
				echo $this->setLabelForFields($_POST['labelForField']);
			}

			if ($_POST['request']=='getPreviewAuthorPublications') {
				
				
				echo $this->getPreviewAuthorPublications($_POST['id_author']);
			}

			if ($_POST['request']=='getLoggedAuthor') {
				
				
				echo $this->getLoggedAuthor();
			}



		}
	}	


	//if id_author is empty, return all publications
	public function getAuthorPublications($id_author){

		return $this->datamanager->getAuthorPublications($id_author);

	}

	//if id_author is empty, return all exlcuded publications
	public function getExcludedAuthorPublications($id_author){

		return $this->datamanager->getExcludedAuthorPublications($id_author);

	}


	public function insertAuthor($id_author,$forename,$surname){

		$keys=$this->getKeys();
		
		echo $this->datamanager->insertAuthor($id_author,$forename,$surname,$keys);

	}



	public function getKeys(){

		return $this->keymamanger->getKeys();

	}


	public function updateKeys($consumer, $secret){


		return $this->keymamanger->updateKeys($consumer, $secret);

	}


	public function getListAuthors(){

		return $this->datamanager->getListAuthors();

	}

	public function deleteAuthor($id_author){

		return $this->datamanager->deleteAuthor($id_author);

	}


	public function excludePublication($canonical_id,$id_author){

		return $this->datamanager->excludePublication($canonical_id,$id_author);
	}


	public function deleteExcludedPublication($canonical_id,$id_author){

		echo $this->datamanager->deleteExcludedPublication($canonical_id,$id_author);
	}

	public function restoreExlcudedPublication($canonical_id,$id_author){

		echo $this->datamanager->restoreExlcudedPublication($canonical_id,$id_author);
	}


	public function getOrderTypePublications(){

		
		return $this->datamanager->getOrderTypePublications();
	}

	public function setOrderTypePublications($orderPublications){

		
		echo $this->datamanager->setOrderTypePublications($orderPublications);
	}

	public function getOrderFields(){

		
		return $this->datamanager->getOrderFields();
	}

	public function setOrderFields($orderFields){

		
		return $this->datamanager->setOrderFields($orderFields);
	}

	public function getCssFields(){

		return $this->datamanager->getCssFields();
		
	}

	public function setCssFields($cssFields){

		return $this->datamanager->setCssFields($cssFields);

	}

	public function setVisibleFields($visibFields){

		
		return $this->datamanager->setVisibleFields($visibFields);

	}

	public function setLabelForFields($labelForFields){

		
		return $this->datamanager->setLabelForFields($labelForFields);

	}


	public function getPreviewAuthorPublications($id_author){

		
		return $this->datamanager->getPreviewAuthorPublications($id_author);

	}

	public function getLoggedAuthor(){

		$keys=$this->getKeys();
		echo $this->datamanager->getLoggedAuthor(null,$keys);

	}

}

$rm=new RequesterManager();


?>