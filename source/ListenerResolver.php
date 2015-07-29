<?php

namespace Lionar\Events;

interface ListenerResolver
{
    /**
     * Resolve the callable and return it's result.
     *
     * @param  Callable $listener           Listener to be resolved.
     * @param  array    $payload            Arguments to use with resolving.
     *                                      Name/value pairs corresponding to
     *                                      callable arguments.
     *
     * @throws InvalidArgumentException     In case the callable contains arguments
     *                                      that cannot be resolved.
     * @return mixed                        Result of resolving the callable
     */
	public function call( $listener, array $payload = array( ) );
}