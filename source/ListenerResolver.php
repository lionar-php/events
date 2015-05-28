<?php

namespace Lionar\Events;

interface ListenerResolver
{
	public function call( Callable $listener, array $payload = array( ) );
}