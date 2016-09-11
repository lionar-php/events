<?php

namespace Events\Tests;

use Events\Dispatcher,
    InvalidArgumentException,
    Mockery,
    Testing\TestCase;

class Dependency { }

class DispatcherTest extends TestCase
{
    private $dispatcher, $resolver  = null;
    private $event                  = 'i created a post';
    private $inexistentEvent        = 'in-existent event';

    public function setUp ( )
    {
        $this->resolver = Mockery::mock ( 'Agreed\\Application' );
        $this->dispatcher = new Dispatcher ( $this->resolver );
    }

    /*
    |--------------------------------------------------------------------------
    | Has method testing
    |--------------------------------------------------------------------------
    */

    /**
     * @test
     */
    public function has_withInexistentEvent_returnsFalse ( )
    {
        $this->assertFalse ( $this->dispatcher->has ( $this->inexistentEvent ) );
    }

    /**
     * @test
     */
    public function has_withExistentEvent_returnsTrue ( )
    {
        $this->dispatcher->listen ( $this->event, function ( ) { } );
        $this->assertTrue ( $this->dispatcher->has( $this->event ) );
    }

    /*
    |--------------------------------------------------------------------------
    | Fire method testing
    |--------------------------------------------------------------------------
    */

   /**
     * @test
     */
    public function fire_withInexistentEvent_returnsAnEmptyArray ( )
    {
        $results = $this->dispatcher->fire ( 'in existent event' );
        $this->assertEquals ( array ( ), $results );
    }

    /**
     * @test
     */
    public function fire_withExistentEventAndRegisteredListenerThatReturnsNothing_firesTheListenerAndReturnsAnEmptyArray ( )
    {
        $this->resolver->shouldReceive ( 'call' );
        $this->dispatcher->listen ( $this->event, function( ) { } );
        $this->assertEmpty ( $this->dispatcher->fire( $this->event ) );
    }

    /**
     * @test
     * @dataProvider eventReturnValues
     */
    public function fire_withExistentEventRegisteredListenerAndProvidedPayload_callsListenerResolverWithProvidedPayloadAndReturnsItsResultsAsAnArray ( $value )
    {
        $payload = array ( 'value' => $value );

        $listener = function ( $value )
        {
            return $value;
        };

        $this->resolver->shouldReceive ( 'call' )->with ( $listener, $payload )->twice ( )->andReturn ( $value );

        for ( $times = 2; $times > 0; $times -= 1 )
            $this->dispatcher->listen ( $this->event, $listener );

        $this->assertEquals ( array ( $value, $value ), $this->dispatcher->fire ( $this->event, $payload ) );
    }

	/**
     * @test
     */
    public function fire_withExistentEventAndRegisteredListenerWithNonResolverResolvableParameterNoProvidedCorrespondingPayloadAndNoDefaultValueForParameter_skipsThatListener ( )
    {
        $this->resolver->shouldReceive ( 'call' )->once ( )->andThrow ( new InvalidArgumentException );

        $this->dispatcher->listen ( $this->event, function ( $someNotProvidedParameter )
		{
			return $someNotProvidedParameter;
		});

		$this->assertEmpty ( $this->dispatcher->fire( $this->event ) );
    }

    /*
    |--------------------------------------------------------------------------
    | Data providers
    |--------------------------------------------------------------------------
    */

    public function eventReturnValues ( )
    {
        return array(

            array( 'hello' ),
            array( 'world' ),
            array( false ),
            array( true ),
            array( 0 )
        );
    }
}
