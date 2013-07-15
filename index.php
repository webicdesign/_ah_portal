<?php
/**
 * @author atabak.h@gmail.com
 * ah_framework
 * Copyright 2010-2013 gbl group
 * atabak hosein nia production
 * supported by webicdesign.net
 * support@webicdesign.net
 */

# main path for begin framework
defined ( 'AH_ROOT'	) or define( 'AH_ROOT' , getcwd().'/');

# booting portal
include AH_ROOT.'core/boot/_boot.php';
_ah_boot();

?>