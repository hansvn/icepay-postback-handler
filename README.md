# Laravel Icepay Postback handler

This is an example of [the laravel-icepay package](https://github.com/hansvn/icepay).

First migrate the migrations to be able to create the orders in the DB
 ```shell
php artisan migrate
```

Example usage in routes:

1. Create the database order
2. Get the icepay object
3. Return the url and let icepay handle the payment
4. The other routes handle the postback returned from the postbackhandler itself

```php
Route::get('/', function() {
    $ip = \Brandworks\Icepay\DBOrder::create(array());
    $icepay = $ip->icepayObject;
    $icepay->setAmount(1000)
                ->setCountry("BE")
                ->setLanguage("NL")
                ->setReference("My Sample Website")
                ->setDescription("My Sample Payment")
                ->setCurrency("EUR");

    $basic = Icepay::basicMode();
    $basic->validatePayment($icepay);

    return sprintf("<a href=\"%s\">%s</a>",$basic->getURL(),$basic->getURL());
});

Route::get('/thanks', function() {
    echo "thanks <br /> Your order id: ";
    echo Session::pull('OrderID');
});


Route::get('/error', function() {
    echo "error <br /> Your order id: ";
    Session::pull('OrderID');
    echo "<br />";
});
```