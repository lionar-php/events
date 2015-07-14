<?php

namespace Lionar\Events;

use InvalidArgumentException,
    ReflectionFunction;

class Dispatcher
{
    protected $resolver = null;
    protected $events = array( );

    public function __construct( ListenerResolver $resolver )
    {
        $this->resolver = $resolver;
    }

    public function add( $event, Callable $listener )
    {
        $this->events[ $event ][ ] = $listener;
    }

    public function hasRegistered( $event )
    {
        if( array_key_exists( $event, $this->events ) )
            return true;
        return false;
    }

    public function fire( $event, $payload = array( ) )
    {
        if( ! $this->hasRegistered( $event ) )
            return false;

        $results = $this->gatherResultsFrom( $event, $payload );

        return array_values( array_filter( $results, function( $value ) { return ! is_null( $value ); } ) );
    }

    private function gatherResultsFrom( $event, array $payload, array $results = array( ) )
    {
        foreach( $this->events[ $event ] as $listener )
            $results[ ] = $this->call( $listener, $payload );
        return $results;
    }

    private function call( Callable $listener, array $payload = array( ) )
    {
        try
        {
            return $this->resolver->call( $listener, $payload );
        }
        catch( InvalidArgumentException $exception )
        {
            return null;
        }
    }
}