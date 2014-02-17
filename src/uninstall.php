<?php
//delete plugin table from database
include('../../../wp-load.php');//import wp-load.php to interface wordpress database with wpdb class

if(defined('WP_UNINSTALL_PLUGIN') ){  
  
  global $wpdb;


  //delete authored
  $query='drop table mendeley_authored;';
  $wpdb->query($query);

  //delete excluded
  $query='drop table mendeley_excluded_publication;';
  $wpdb->query($query);  

  //delete author
  $query='drop table mendeley_author;';
  $wpdb->query($query);  

  //delete publications
  $query='drop table mendeley_publication;';
  $wpdb->query($query);

  //delete key
  $query='drop table mendeley_key;';
  $wpdb->query($query);

  //delete order type
  $query='drop table mendeley_order_type_publications;';
  $wpdb->query($query);

  //delete format
  $query='drop table mendelely_format_publications;';
  $wpdb->query($query);

  //delete order field
  $query='drop table mendeley_order_field;';
  $wpdb->query($query);

} 

?>