<?php

namespace Lionar\Events;

interface ListenerResolver
{
	public function call( $alias, array $args = array( ) );

	// public function call( Callable $listener, array $payload = array( ) );
}