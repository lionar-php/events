<?php

namespace Lionar\Events\Tests;

use Lionar\Events\Dispatcher,
    Mockery;

class UsedToCallTheResolver
{

}

class DispatcherTest extends TestCase
{
    private $dispatcher, $resolver = null;
    private $event = 'i created a post';
    private $inexistentEvent = 'inexistent event';

    public function setUp( )
    {
        $this->dispatcher = new Dispatcher( );
        $this->resolver = Mockery::mock( 'Lionar\\Events\\ListenerResolver' );
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
     * @dataProvider eventReturnValues
     */
    public function fire_withExsistentEventAndRegisteredListenerWithParameters_firesTheListenerAndReturnsItsResultsIfNotNullWasReturned( $value )
    {
        $this->dispatcher->add( $this->event, function( ) { } );
        for ( $times = 2; $times > 0; $times -= 1 )
            $this->dispatcher->add( $this->event, function( ) use( $value ) { return $value; });

        $this->assertEquals( array( $value, $value ), $this->dispatcher->fire( $this->event, array( 'value' => $value ) ) );
    }

    /**
     * @test
     */
    public function fire_withExsistentEventAndRegisteredListenerWithResolverResolvableParameters_firesTheListenerInjectsTheDependencieAndReturnsListenerResultsIfNotNullWasReturned( )
    {
        $dependency = Mockery::mock( 'Dependency' );
        $this->resolver->shouldReceive( 'call' )->once( )->andReturn( null );
        $this->resolver->shouldReceive( 'call' )->once( )->andReturn( $dependency );

        $dispatcher = new Dispatcher( $this->resolver );
        $dispatcher->add( $this->event, function( ) { } );
        
        $dispatcher->add( $this->event, function( UsedToCallTheResolver $dependency )
        {
            return $dependency;
        });

        $this->assertEquals( array( $dependency ),  $dispatcher->fire( $this->event ) );
    }

	/**
     * @test
     */
    public function fire_withExsistentEventAndRegisteredListenerWithNonResolverResolvableParameterNoProvidedCorrespondingPayloadAndNoDefaultValueForParameter_skipsThatListener( )
    {
       	$this->dispatcher->add( $this->event, function( $someNotProvidedParameter )
		{
			return $someNotProvidedParameter;
		});

		$this->assertEmpty( $this->dispatcher->fire( $this->event ) );
    }

	/**
     * @test
     */
    public function fire_withExsistentEventAndRegisteredListenerWithProvidedPayload_injectsProvidedPayloadByKeyInListener( )
    {
		$greeting = 'hello world';
		$payload = array( 'greeting' => $greeting );

       	$this->dispatcher->add( $this->event, function( $greeting )
		{
			return $greeting;
		});

		$this->assertEquals( array( $greeting ), $this->dispatcher->fire( $this->event, $payload ) );   
    }

    /**
     * @test
     */
    public function fire_withExsistentEventAndRegisteredListenerWithNonResolverResolvableParameterNoProvidedCorrespondingPayloadButProvidedDefaultValueForParameter_usesDefaultParameter( )
    {
        $this->dispatcher->add( $this->event, function( $greeting = 'hello world' )
        {
            return $greeting;
        });

        $this->assertEquals( array( 'hello world' ), $this->dispatcher->fire( $this->event ) );   
    }

    /**
     * @test
     */
    public function fire_withExsistentEventAndRegisteredListenerWithNonResolverResolvableParametersNoDefaultValueAndNoProvidedCorrespondingPayload_LogsItsMistakes( )
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
