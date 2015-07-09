```php
when( 'i want to see the dashboard', then( apply( a( function( Input $goal )
{
    var_dump( $goal );
}))));
```

``
 <form method="GET">
    <input type="text" name="goal" placeholder="goal">
    <input type="submit" value="Place goal">
</form>
``

if the variable name is the same as what value is supplied,, the dispatcher
just throws in the supplied value and does not resolve it into a class