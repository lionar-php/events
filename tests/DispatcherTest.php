<?php

namespace Lionar\Events\Tests;

use InvalidArgumentException,
    Lionar\Events\Dispatcher,
    Mockery;

class Dependency { }

class DispatcherTest extends TestCase
{
    private $dispatcher, $resolver  = null;
    private $event                  = 'i created a post';
    private $inexistentEvent        = 'in-existent event';

    public function setUp( )
    {
        $this->resolver = Mockery::mock( 'Lionar\\Events\\ListenerResolver' );
        $this->dispatcher = new Dispatcher( $this->resolver );
    }

    /**
     * @test
     */
    public function hasRegistered_withInexistentEvent_returnsFalse( )
    {
        $this->assertFalse( $this->dispatcher->hasRegistered( $this->inexistentEvent ) );
    }

    /**
     * @test
     */
    public function hasRegistered_withExistentEvent_returnsTrue( )
    {
        $this->dispatcher->add( $this->event, function( ) { } );
        $this->assertTrue( $this->dispatcher->hasRegistered( $this->event ) );
    }

    /**
     * @test
     */
    public function fire_withExistentEventAndRegisteredListenerThatReturnsNothing_firesTheListenerAndReturnsAnEmptyArray( )
    {
        $this->resolver->shouldReceive( 'call' );
        $this->dispatcher->add( $this->event, function( ) { } );
        $this->assertEmpty( $this->dispatcher->fire( $this->event ) );
    }

    /**
     * @test
     */
    public function fire_withExistentEventRegisteredListenerAndProvidedPayload_callsListenerResolverWithProvidedPayloadAndReturnsItsResultsAsAnArray( )
    {
        $value = 'hello world';
        $payload = array( 'value' => $value );

        $listener = function( $value )
        {
            return $value;
        };

        $this->resolver->shouldReceive( 'call' )->with( $listener, $payload )->twice( )->andReturn( $value );

        for ( $times = 2; $times > 0; $times -= 1 )
            $this->dispatcher->add( $this->event, $listener );

        $this->assertEquals( array( $value, $value ), $this->dispatcher->fire( $this->event, $payload ) );
    }

	/**
     * @test
     */
    public function fire_withExistentEventAndRegisteredListenerWithNonResolverResolvableParameterNoProvidedCorrespondingPayloadAndNoDefaultValueForParameter_skipsThatListener( )
    {
        $this->resolver->shouldReceive( 'call' )->once( )->andThrow( new InvalidArgumentException );

        $this->dispatcher->add( $this->event, function( $someNotProvidedParameter )
		{
			return $someNotProvidedParameter;
		});

		$this->assertEmpty( $this->dispatcher->fire( $this->event ) );
    }


    /**
     * @test
     */
    public function fire_withExistentEventAndRegisteredListenerWithNonResolverResolvableParametersNoDefaultValueAndNoProvidedCorrespondingPayload_LogsItsMistakes( )
    {
        $this->dispatcher->add( $this->event, function( $someNotProvidedParameter )
        {
            return $someNotProvidedParameter;
        });

        $this->markTestIncomplete(
            'Still need to mock a logger and test if the dispatcher logs the mistake of insufficient listener parameters'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Data providers
    |--------------------------------------------------------------------------
    */

    public function eventReturnValues( )
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
