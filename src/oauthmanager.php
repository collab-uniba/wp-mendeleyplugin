<?php
include_once "oauth/OAuthStore.php";
include_once "oauth/OAuthRequester.php";
include_once "keymanager.php";


define("MENDELEY_REQUEST_TOKEN_URL", "http://api.mendeley.com/oauth/request_token/");
define("MENDELEY_AUTHORIZE_URL", "http://api.mendeley.com/oauth/authorize/");
define("MENDELEY_ACCESS_TOKEN_URL", "http://api.mendeley.com/oauth/access_token/");

//define('OAUTH_TMP_DIR', function_exists('sys_get_temp_dir') ? sys_get_temp_dir() : realpath($_ENV["TMP"]));

// Start the session
$callback=("http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
//if(session_status() != 2) {session_start();}
 if (session_id() == "") session_start();
$response='';

try
{
        //  STEP 1:  If we do not have an OAuth token yet, go get one
        if (empty($_GET["oauth_verifier"]))
        {
                

                $getAuthTokenParams = array(
                        'oauth_callback'=>$callback.'?accessPlugin=false'
                );
                $options = array (
                        'oauth_as_header' => false
                );

                //  Init the OAuthStore
                $options = array(
                        'consumer_key' => $_POST['consumer'], 
                        'consumer_secret' => $_POST['secret'],
                        'server_uri' => 'http://api.mendeley.com',
                        'request_token_uri' => MENDELEY_REQUEST_TOKEN_URL,
                        'authorize_uri' => MENDELEY_AUTHORIZE_URL,
                        'access_token_uri' => MENDELEY_ACCESS_TOKEN_URL
                );

                // Note: do not use "Session" storage in production. Prefer a database
                // storage, such as MySQL.
                OAuthStore::instance("Session", $options);
                
                // get a request token
                $tokenResultParams = OAuthRequester::requestRequestToken($_POST['consumer'], 0, $getAuthTokenParams, 'GET', NULL);
                $_SESSION['oauth_token'] = $tokenResultParams['token'];
                $_SESSION['consumer_key']=$_POST['consumer'];
                $_SESSION['consumer_secret']=$_POST['secret'];

                //  return redirect url to the MENDELEY authorization page. Redirect will be done into a frame
               
               //if author accept to collegate to mendeley
                if (!isset($_GET['accessPlugin']))

                //  redirect to the MENDELEY authorization page, they will redirect back
                   echo MENDELEY_AUTHORIZE_URL."?oauth_token=" . $tokenResultParams['token'].'&oauth_callback='.urlencode($callback);

        }

        else {

                //  STEP 2:  Get an access token

                //get consumer and secret keys from session store

                try {


                    $options = array(
                        'consumer_key' => $_SESSION['consumer_key'], 
                        'consumer_secret' => $_SESSION['consumer_secret'],
                        'server_uri' => 'http://api.mendeley.com',
                        'request_token_uri' => MENDELEY_REQUEST_TOKEN_URL,
                        'authorize_uri' => MENDELEY_AUTHORIZE_URL,
                        'access_token_uri' => MENDELEY_ACCESS_TOKEN_URL
                    );

                        // Note: do not use "Session" storage in production. Prefer a database
                        // storage, such as MySQL.
                        OAuthStore::instance("Session", $options);

                  
                   
                   $tokens= OAuthRequester::requestAccessToken($_SESSION['consumer_key'], $_SESSION['oauth_token'], 0, 'GET', $options=array(
                                'oauth_verifier'=>$_GET['oauth_verifier']
                        ));

                   //save tokens and keys
                
                   $keymanager=new KeyManager();

                   $response=$keymanager->updateKeyS($_SESSION['consumer_key'], $_SESSION['consumer_secret'], $tokens['oauth_token'], $tokens['oauth_token_secret']);
                   manageIframe($response);

                }
                catch (OAuthException2 $e)
                {
                        manageIframe($e->getMessage());
                    
                    
                }

                 
        }
}
catch(OAuthException2 $e) {
       manageIframe("OAuthException:  " . $e->getMessage().'. Probally you reject request');
        
}

//manage iframe
function manageIframe($r){

?>
<html>
    <head>
        <script type="text/javascript" src='../wp-mendeleyauthoredpublicationsplugin/js/jquery/jquery.js'></script>
        

    </head>

    <body>

    <div>
        
        <script type="text/javascript">

            function callback(){

              
               parent.top.closeWindowMendeley()

            }
            var $j = jQuery.noConflict()
            
            
            parent.top.windowMessage('Mendeley', <?php echo '"'.$r.'"' ?> +'.')//show message
           parent.top.closeWindowMendeley()


        </script>

    </div>

    </body>

    </html>
<?php
}
?>

