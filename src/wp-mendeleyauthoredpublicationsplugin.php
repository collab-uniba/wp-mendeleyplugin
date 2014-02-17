<?php
/*
Plugin Name: Mendeley Authored Publications Plugin
Plugin URI: 
Version: 1.0

Author: Nicola Musicco
Author URI: http://www.facebook.com/coccu
License: http://www.opensource.org/licenses/mit-license.php
Description: This plugin offers the possibility to access Mendeley repository and show authored publications with a customized format. To show publications in a Wordpress page, write a shortcode in the form [publications id_author="id_author author"]. This puglin supports MySQL dbms.
*/

/* 
The MIT License

Copyright (c) 2013-2016 Nicola Musicco (email: nicolamc@hotmail.it)
 
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/



//import jquery script from wp-include wordpress folder

wp_enqueue_script("jquery");
//wp_enqueue_script("quicktags");
//wp_head(); 

add_action( 'admin_menu', 'plugin_settings_menu' );



//add option to settings menu
function plugin_settings_menu(){

	add_options_page('MAP Plugin Admin Page', 'MAP Plugin', 'manage_options', basename(__FILE__), 'showAdminPage');

};

//add javascript files to page headers
function admin_page_js() {
    
   
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/jquery/jquery-ui.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/spectrum/spectrum.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/spectrum/prettify.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/spectrum/toc.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/adminpageUI/adminpageUI.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/adminpageUI/keysUI.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/adminpageUI/publicationsUI.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/adminpageUI/outputUI.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/adminpageUI/utilsUI.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/adminpage/adminpage.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/adminpage/keys.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/adminpage/publications.js></script>';
    echo '<script type="text/javascript" src='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/adminpage/output.js></script>';
   
   
    
};

//add css files to page headers
function admin_page_css() {
    echo '<link rel="stylesheet" href='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/css/adminpage.css>';
    echo '<link rel="stylesheet" href='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/css/spectrum/spectrum.css>';
    echo '<link rel="stylesheet" href='.plugins_url().'/wp-mendeleyauthoredpublicationsplugin/css/redmond/jquery-ui-1.10.2.custom.css>';
   
    

};

// Add hook for admin <head></head>
add_action('admin_head', 'admin_page_js');
// Add hook for front-end <head></head>
add_action('wp_head', 'admin_page_js');
// Add hook for admin <head></head>
add_action('admin_head', 'admin_page_css');
// Add hook for front-end <head></head>
add_action('wp_head', 'admin_page_css');

//add hook for shortcode
add_shortcode( 'publications', 'process_shortcode' );

//add_filter( 'wp_title',get_title_page());

//

//show admin page
function showAdminPage(){
?>
<div id="div-tabs">
  <ul>
    <li><a href="#tab-keys">Mendeley Keys Manager</a></li>
    <li><a href="#tab-publications">Publications Manager</a></li>
    <li><a href="#tab-output">Output Manager</a></li>
  </ul>
  <div id="tab-keys">
    <p><b>Mendeley Authored Publications Plugin</b></p>
		<p>This plugin offer the possibility to access Mendeley repository, get author publications and show them by categories with a customized format.</p>
		<p>Set-up Mendeley Authored Publications Plugin: register an application <a href='http://dev.mendeley.com/applications/register/'>here</a>:<br><br>
    1. get consumer key and consumer secret; <br>
    2. put keys into fields below;<br>
    3. click "Save/Update keys" button.</p>
    To change keys, get other keys and repeat from step 1.<br>
		<p>Consumer Key: <br><input type='text' id='txt-consumerkey' size=50 name='consumer' ></p>
    <p>Consumer Secret: <br><input type='text' id='txt-consumersecret' size=50 name='secret' ></p>
    <p>Current Mendeley User: <br><input type='text' id='txt-mendeleyUser' size=50 name='user' readonly></p>
		<input type='button' id='button-getKey'   value='Save keys/Switch user'><br><br>
    N.B.: You need both keys to get author publications. To switch author, first delete "api.mendeley.com" cookies from your browser.<br><br>
    To show publications in Wordpress, write a shortcode in the form <b>[publications id_author="id author"]</b>.

  </div>
  <div id="tab-publications">
  Add autenticated Mendeley author and import his publications.
  <br>
    <div id='div-authorsList'></div><br><br>
      <input type='button' id='button-newAuthor'  value='Import/Update publications' style='margin-right:10px;'>
      <input type='button' id='button-deleteAuthor'  value='Delete author' style='margin-right:10px;'>
      <input type='button' id='button-showAuthorPublications'  value='Show Publications' style='margin-right:10px;'>
      <input type='button' id='button-showExcludedPublication'  value='Show Excluded Publications' style='margin-right:10px;'>
     
   
  </div>
  
  <div id="tab-output">
    Select an author and show publications preview: <select id='select-authorPreview'></select> 
    <input type='button' id='button-previewAuthorPublications'  value='Preview Publications' style='margin-right:10px;'><br>
    <hr><br>
    Sets the order in which you want to show publications according to their type.<br><br>
    <input type='button' id='button-orderTypePublications'  value='Set Order Type Publications' style='margin-right:10px;'><br>
    <hr><br>
     Sets the order in which you want to show publications fields and their appearance.<br><br>
    <div id='div-formatOutput'>
  

    
    </div>
    
    <br>
    <div id='div-formatToolbar'>
      <div id='div-fontButtons'>
      <input type='checkbox'  id='button-fontBold'/><label id='lbl-fontBold' for="button-fontBold"></label>
      <input  type='checkbox' id='button-fontItalic' /><label id='lbl-fontItalic' for="button-fontItalic"></label>
      <input  type='checkbox' id='button-fontUnderline' /><label id='lbl-fontUnderline' for="button-fontUnderline"></label>
      </div>
      
     <select id='select-fontFamily'>
      <optgroup label="Font family">
      <option value='Arial'>Arial</option>
      <option value='Century'>Century</option>
      <option value='Century Gothic'>Century Gothic</option>
      <option value='Courier'>Courier</option>
      <option value='Lucida Console'>Lucida Console</option>
      <option value="'Lucida Grande', 'Lucida Sans', Arial, sans-serif">'Lucida Grande', 'Lucida Sans', Arial, sans-serif</option>
      <option value='Tahoma'>Tahoma</option>
      <option value='Times New Roman'>Times New Roman</option>
      <option value='Trebuchet MS'>Trebuchet MS</option>
      <option value='Verdana'>Verdana</option>
      </optgroup>

     </select>
      
      <div id='div-fontSize'>
      <button id='button-fontDescrease'></button>
      <input type='text' id='input-fontSize' value='Font size'>
      <select id='select-fontMeasure'>
         <optgroup label="Font measure">
          <option value='px'>px</option>
          <option value='pt'>pt</option>
          <option value='pc'>pc</option>
          <option value='in'>in</option>
          <option value='cm'>cm</option>
          <option value='mm'>mm</option>
          <option value='em'>em</option>
          <option value='ex'>ex</option>
          <option value='%'>%</option>
      </select>
      <button id='button-fontIncrease'></button>
    </div>
    <button id='button-fontColor'></button>
    <div id='div-fieldOrder'>
       <button id='button-moveUp'></button>
       <button id='button-moveDown'></button>

    </div>
     <button id='button-showHide'></button>
     <input type='text' id='input-labelFor' value=''>
    <br>


      
    </div>

  </div>
</div>


<?php
}
//shortcode will be in the form: [publications id_author="id_author author"]
function process_shortcode($atts){


    //session_start();
    extract( shortcode_atts( array(
          'id_author' => ''
          ), $atts ) );

    $fields = array(

            'request'=>'getPreviewAuthorPublications',
            'id_author'=>$id_author
            
        );
    //request manager url
    $url=plugins_url().'/wp-mendeleyauthoredpublicationsplugin/requestermanager.php';
    //if(session_status() != 2) {session_start();}
     //if (session_id() == "") session_start();
    $_SESSION['id_author']=$id_author;
    
    //wp_title('-',get_title_page());
    return execRequestWordpress($url, $fields);  

}



//execute curl request and return data
function execRequestWordpress($url, $fields){

      //url-ify the data for the POST
      $fields_string='';
      foreach($fields as $key=>$value) { 

        $fields_string .= $key.'='.$value.'&'; 

      }
      //remove last &
      rtrim($fields_string,'&');
      // Setting curl options
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_POST,count($fields));
      curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
      
      ob_start();
     $data=curl_exec ($ch);
     curl_close ($ch);
     ob_end_clean();

    return $data;


} 


function add_shortcode_button() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_shortcode_tinymce_plugin");
     add_filter('mce_buttons', 'register_shortcode_button');
   }
}
 
function register_shortcode_button($buttons) {
   array_push($buttons, "|", "buttonMAPshortcode");
   return $buttons;
}
 
// Load the TinyMCE plugin : buttonShortcodeUI.js
function add_shortcode_tinymce_plugin($plugin_array) {
   $plugin_array['mapshortcode'] = plugins_url().'/wp-mendeleyauthoredpublicationsplugin/js/adminpageUI/buttonShortcodeUI.js';
   return $plugin_array;
}
 
function my_refresh_mce($ver) {
  $ver += 3;
  return $ver;
}

// init process for button control
add_filter( 'tiny_mce_version', 'my_refresh_mce');
add_action('init', 'add_shortcode_button');
?>