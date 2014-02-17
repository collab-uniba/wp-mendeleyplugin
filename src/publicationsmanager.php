<?php

if (!class_exists("DataManager")) {

    include_once 'datamanager.php';
}

//if(session_status() != 2) {session_start();}
if (session_id() == "") session_start();

$callback=("http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
$elapse_time=time()+20;//control timeout page


        
        //  STEP 1:  save publications ids into session variable
             
                ob_start();

                if (isset($_GET['total_pages'])){

                    if (isset($_GET['current_page'])){

                         $p=intval($_GET['current_page']);
                         $total_pages=intval($_GET['total_pages']);

                          //check timeout page
                         while (time()<$elapse_time){

                             if ($p<($total_pages)){

                                 $urlRequest='http://api.mendeley.com/oapi/library/documents/authored';
                                 $params='&page='.$p;
                                 $data=decodeJSONData(execAuthorRequest($urlRequest));
                                 $_SESSION['data']=$data;
                            
                                 array_push($_SESSION['canonical_id'],$data->document_ids);
                                 $p=intval($_GET['current_page']);
                                 $p++;
                                 
                            }
                           
                            else {
                            ob_start();
                             ob_end_flush();
                             header('Location: '.$callback.'?details_publications=true'); 
                            }

                         }
                        
                            
                            if ($p<$total_pages){
                                ob_start();
                                ob_end_flush();
                                 //  STEP 6:  save id into session variable
                                 header('Location: '.$callback.'?total_pages='.$data->total_pages.'&current_page='.$p);

                             }

                              //  STEP 7: start getting publications details
                             else {
                                ob_start();
                                ob_end_flush();
                                header('Location: '.$callback.'?details_publications=true');      
                             }

                        }
                    }
                
            

            //  STEP 8: get publications details and save

            else if (isset($_GET['details_publications'])){
                
                while(time()<$elapse_time){

                       
                        if (!empty($_SESSION['canonical_id'][0])){

                                 $id=array_pop($_SESSION['canonical_id'][0]);
                                 getPublicationDetails($id);
                                
                             }

                        else {

                            echo 'Saving Publications...';
                            insertPublications();
                            break;

                        }
                }

                if (!empty($_SESSION['canonical_id'][0])){
                    ob_start();
                    ob_end_flush();
                    header('Location: '.$callback.'?details_publications=true');

                }
                
               
            }

            else if (isset($_GET['author'])){

                 $urlRequest='http://api.mendeley.com/oapi/profiles/info/me';
                 $data=decodeJSONData(execAuthorRequest($urlRequest,null));
                 $author=array('profile_id'=>$data->main->profile_id, 'name'=>$data->main->name);
                 
                 getLoggedAuthor($author);

            }

            else {
                ob_start();
                 $urlRequest='http://api.mendeley.com/oapi/library/documents/authored';
                 $data=decodeJSONData(execAuthorRequest($urlRequest,null));
                 $total_pages=$data->total_pages;
                 $_SESSION['publications']=array();
                 $_SESSION['canonical_id']=array();
                 ob_end_flush();
                 header('Location: '.$callback.'?total_pages='.$data->total_pages.'&current_page=0');
                 
            }



function getPublicationDetails($id){

   
    $result=execAuthorRequest('http://api.mendeley.com/oapi/library/documents/'.$id,null);

    //if request ok, get publications details and save them into session variable
    if ($result!= '') {
            
            //decode response
            $data=decodeJSONData($result);
            $publications=array();
            if (isset($data->canonical_id)) $publications['canonical_id']=$data->canonical_id;
            if (isset($data->authors)) $publications['authors']=$data->authors;
            if (isset($data->title)) $publications['title']=$data->title;
            if (isset($data->publication_outlet)) $publications['publication_outlet']=$data->publication_outlet;
            if (isset($data->abstract)) $publications['abstract']=$data->abstract;
            if (isset($data->volume)) $publications['volume']=$data->volume;
            if (isset($data->issue)) $publications['issue']=$data->issue;   
            if (isset($data->publisher)) $publications['publisher']=$data->publisher;
            if (isset($data->year)) $publications['year']=$data->year;
            if (isset($data->pages)) $publications['pages']=$data->pages;
            if (isset($data->website)) $publications['website']=$data->website;
            if (isset($data->mendeley_url)) $publications['mendeley_url']=$data->mendeley_url;

            if (isset($data->identifiers)){

                 if (isset($data->identifiers->doi)) $publications['doi']=$data->identifiers->doi;
                 if (isset($data->identifiers->issn)) $publications['issn']=$data->identifiers->issn;
                 if (isset($data->identifiers->isbn)) $publications['isbn']=$data->identifiers->isbn;

            }

            if (isset($data->type)) $publications['type']=$data->type;
            if (isset($data->city)) $publications['city']=$data->city;
            if (isset($data->day)) $publications['day']=$data->day;
            if (isset($data->month)) $publications['month']=$data->month;
            if (isset($data->editors)) $publications['editors']=$data->editors;
            if (isset($data->edition)) $publications['edition']=$data->edition;
            if (isset($data->chapter)) $publications['chapter']=$data->chapter;
            if (isset($data->type_of_work)) $publications['type_of_work']=$data->type_of_work;
            if (isset($data->institution)) $publications['institution']=$data->institution;
            if (isset($data->department)) $publications['department']=$data->department;
            if (isset($data->university)) $publications['university']=$data->university;
            if (isset($data->number)) $publications['number']=$data->number;
            if (isset($data->series)) $publications['series']=$data->series;

            array_push($_SESSION['publications'], $publications);

    }
    else {
           return;
    }

}

function getLoggedAuthor($data){

    $datamanager=new DataManager();
  // echo $_SESSION['consumer'];

   $datamanager->getLoggedAuthor($data,null);

}


//decode json data and return an object of arrays
 function decodeJSONData($data){

    //decode data and returns data
    return json_decode($data);
}


function execAuthorRequest($url,$params){

    $header=getHeaderRequest($url,$params);

    $ch=curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
    curl_setopt($ch, CURLOPT_USERAGENT,      'anyMeta/OAuth 1.0 - ($LastChangedRevision: 174 $)');
    curl_setopt($ch, CURLOPT_URL,            $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER,         'Accept: application/json');
    curl_setopt($ch, CURLOPT_TIMEOUT,        30);
          
      
    $response=curl_exec($ch);
    curl_close($ch);
   
    return $response;

}

//insert publications into database
function insertPublications(){

    $datamanager=new DataManager();
    $publications=$_SESSION['publications'];
    $datamanager->insertPublications($publications);
    

}

function calculateSignature($url, $params){

    $timestamp=time();
    $nonce=uniqid('');
    $b=urlencode($url);

    if ($params!=null)
    $p=urlencode('oauth_consumer_key='.stripslashes($_SESSION['consumer']).
         '&oauth_nonce='.$nonce.
         '&oauth_signature_method=HMAC-SHA1'.
         '&oauth_timestamp='.$timestamp.
         '&oauth_token='.stripslashes($_SESSION['token']).
         '&oauth_version=1.0'.
         $params
         );

    else
    $p= urlencode('oauth_consumer_key='.stripslashes($_SESSION['consumer']).
         '&oauth_nonce='.$nonce.
         '&oauth_signature_method=HMAC-SHA1'.
         '&oauth_timestamp='.$timestamp.
         '&oauth_token='.stripslashes($_SESSION['token']).
         '&oauth_version=1.0'
        );

    $base_string='GET&'.$b.'&'.$p;
    $key= (urlencode_($_SESSION['secret']).'&'.urlencode_($_SESSION['token_secret']));

    $s=(base64_encode(hash_hmac("sha1", $base_string, $key, true)));
    $signature= urlencode_($s);
    $data=array('signature'=>$signature, 'nonce'=>$nonce, 'timestamp'=>$timestamp);
    return $data;

}

function getHeaderRequest($url,$params){

    $data= calculateSignature($url,$params);

    $ch=curl_init();
    $header=array();
    $header[0]='"Accept: application/json"';
    $header[1]='Authorization: OAuth realm=""';
    $header[1].=',oauth_token="'.$_SESSION['token'].'"';
    $header[1].=',oauth_consumer_key="'.$_SESSION['consumer'].'"';
    $header[1].=',oauth_timestamp="'.$data['timestamp'].'"';
    $header[1].=',oauth_nonce="'.$data['nonce'].'"';
    $header[1].=',oauth_version="1.0'.'"';
    $header[1].=',oauth_signature_method="HMAC-SHA1'.'"';
    $header[1].=',oauth_signature="'.$data['signature'].'""';

    return $header;

}

function urlencode_ ( $s )
    {
        if ($s === false)
        {
            return $s;
        }
        else
        {
            return str_replace('%7E', '~', rawurlencode($s));
        }
    }

?>