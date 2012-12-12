Example implementation of 2-legged OAuth with [Silex](http://silex.sensiolabs.org/) (Inspired from [Designing a Secure REST (Web) API without OAuth](http://www.thebuzzmedia.com/designing-a-secure-rest-api-without-oauth-authentication/))

### Installation ###

With [Composer](http://getcomposer.org/), install all dependencies.

```
curl -s https://getcomposer.org/installer | php
php composer.phar install
```

### Usage ###

Set api clients to `config.php`

``` php
Hash::setClients(array(
    'A_CLIENT'       => 'A_CLIENT_SECRET_KEY',
    'ANOTHER_CLIENT' => 'ANOTHER_CLIENT_SECRET_KEY',
));
```

API design is in the `api.php`. Change routes according to your needs. But don't remove hash validation middleware. Then check `index.php` to see how make an api call.