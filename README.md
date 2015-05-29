# events
simple php event dispatcher component. This dispatcher uses closures to register event callback functions.
Optionally you can supply a class that implements the ListenerResolver interface to resolve closures.
Examples will be shown below in usage. 

## usage
**NOTE: this documentation is still a work in progress. Not everything is documented as of yet. I will add
more documentation soon**

### examples

the examples below must all be seen as *separate* scenarios.

#### without supplied listener resolver
when no listener resolver was supplied the dispatcher will use call_user_func_array to resolve and call closures.

##### basic usage

```php
use Lionar\Events\Dispatcher;

$dispatcher = new Dispatcher;

$dispatcher->add( 'my eventname', function( )
{
    echo 'hello';
});

$dispatcher->fire( 'my eventname' );
```

###### result
```php
'hello'
```
##### return values

```php
use Lionar\Events\Dispatcher;

$dispatcher = new Dispatcher;

$dispatcher->add( 'i created a post', function( )
{
    return 'this is my post';
});

var_dump( $dispatcher->fire( 'i created a post' ) );
```

###### result
```php
array( 'this is my post' );
```

