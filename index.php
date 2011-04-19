<?php

$framework_name = 'mvcglue';
$framework_version = 0.3;

/*** error reporting on ***/
error_reporting(E_ALL);

/*** define the site path ***/
$site_path = realpath(dirname(__FILE__));
define ('__SITE_PATH', $site_path.'/'.$framework_name);

/*** include the init.php file ***/
include $framework_name.'/includes/init.php';

/*** include the helpers.php file ***/
include $framework_name.'/includes/helpers.php';

/*** load the router ***/
$registry->router = new router($registry);

/*** set the controller path ***/
$registry->router->setPath (__SITE_PATH . '/controller');

/*** load up the template ***/
$registry->template = new template($registry);

/*** load the controller ***/
$registry->router->loader();

