<?php

use Lionar\Events\Dispatcher;
use Lionar\Events\Container;

require __DIR__ . '/vendor/autoload.php';

// $dispatcher = new Dispatcher;
// $dispatcher->add(  'test', function( )
// {
// 	return 'hello';
// });

// var_dump( $dispatcher->fire( 'test' ) );


$container = new Container;

$dispatcherWithContainer = new Dispatcher;
// $dispatcherWithContainer->add(  'test', function( )
// {
// 	return 'hello';
// });

// var_dump($dispatcherWithContainer->fire( 'test' ));

$dispatcherWithContainer->add( 'test', function( $param = 'blaaah' )
{
	return null;
});

$dispatcherWithContainer->add( 'test', function( $param = 'blaaah' )
{
	return $param;
});

var_dump($dispatcherWithContainer->fire( 'test' ));