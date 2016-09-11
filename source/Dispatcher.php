<?php

namespace Events;

use Agreed\Application;
use Exception;
use ReflectionFunction;


class Dispatcher implements \Agreed\Events\Dispatcher
{
    private $resolver = null;
    private $events = array ( );

    public function __construct ( Application $resolver )
    {
        $this->resolver = $resolver;
    }

    public function listen ( $event, Callable $listener )
    {
        $this->events [ $event ] [ ] = $listener;
    }

    public function fire ( $event, $payload = array ( ) ) : array
    {
        if( ! $this->has ( $event ) )
            return array ( );

        $results = $this->gatherResultsFrom ( $event, $payload );

        return array_values ( array_filter ( $results, function ( $value ) { return ! is_null ( $value ); } ) );
    }

    public function has ( $event ) : bool
    {
        if ( array_key_exists ( $event, $this->events ) )
            return true;
        return false;
    }

    private function gatherResultsFrom ( $event, array $payload, array $results = array ( ) )
    {
        foreach ( $this->events [ $event ] as $listener )
            $results [ ] = $this->call ( $listener, $payload );
        return $results;
    }

    private function call ( Callable $listener, array $payload = array ( ) )
    {
        try
        {
            return $this->resolver->call ( $listener, $payload );
        }
        catch ( Exception $exception )
        {
            return null;
        }
    }
}