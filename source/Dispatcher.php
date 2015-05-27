<?php

namespace Lionar\Events;

use InvalidArgumentException,
    ReflectionFunction;

class Dispatcher
{
    protected $resolver = null;
    protected $events = array( );

    public function __construct( ListenerResolver $resolver = null )
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
            return;

        $results = array( );

        foreach( $this->events[ $event ] as $listener )
            if( $this->hasCorrectParametersFor( $listener, $payload ) )
                $results[ ] = $this->call( $listener, $payload );

        return array_values( array_filter($results, function( $value ) { return ! is_null( $value ); }) );
    }

	private function hasCorrectParametersFor( Callable $listener, $payload )
	{
		$reflection = new ReflectionFunction( $listener );
		$parameters = $reflection->getParameters( );
		
		foreach( $parameters as $parameter )
			if( ! $parameter->getClass( ) and ! array_key_exists( $parameter->getName( ), $payload ) and ! $parameter->isDefaultValueAvailable( ) )
				return false;
		return true;
	}

    private function call( Callable $listener, $payload )
    {
        if( isset( $this->resolver ) )
            return $this->resolver->call( $listener, $payload );
        return call_user_func_array( $listener, $payload );
    }
}

// implement default parameter now

// 1. if a $payload parameter has been provided with same name as $parameters->getName( )
//      use that provided value
//      
// 2. if the parameter is a class, then let the resolver resolve that class
// 3. if a default value is provided, and no provided value or resolvable class was given then use that default value
// 4. if none of the above applies, skip the listener altogether and log this skip