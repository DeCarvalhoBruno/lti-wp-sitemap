<?php
// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

//Deleting plugin options
delete_option('lti_sitemap_options');