<?php

use Events\Dispatcher;
use Foundation\Application;

require __DIR__ . '/../vendor/autoload.php';

class Responder
{
	public function with ( $string )
	{
		echo $string;
	}
}


$application = new Application;



$dispatcher = new Dispatcher ( $application );

$dispatcher->add ( 'respond', function ( Responder $respond )
{
	$respond->with ( 'That actually just happened?' );
} );

$dispatcher->fire ( 'respond' );