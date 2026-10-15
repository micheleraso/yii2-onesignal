Yii2 Onesignal api
==================
This is a restful api to onesignal (fork of vasadibt/yii2-onesignal).

Installation
------------

Add the repository and the requirement to your `composer.json`:

```json
"repositories": [
    {"type": "vcs", "url": "https://github.com/micheleraso/yii2-onesignal.git"}
],
"require": {
    "micheleraso/yii2-onesignal": "^1.2"
}
```

Config
-----

Once the extension is installed, configure the api component:

```php
'components' => [
    'onesignal' => [
        'class' => \micheleraso\onesignal\OneSignal::class,
        'appId' => 'your-app-id',
        'appAuthKey' => 'your-rest-api-key',
        'userAuthKey' => 'your-user-auth-key', // only for the "apps" endpoints
        'enabled' => true,
    ],
],
```

Optional settings:

- `apiUrl` (default `https://onesignal.com/api/v1`) and `authScheme` (default `Basic`): fit the legacy REST API keys.
  With the App API keys (November 2024 onwards, the only ones available in new apps) use
  `'apiUrl' => 'https://api.onesignal.com'` and `'authScheme' => 'Key'`.
- `client`: Guzzle options merged over the defaults (`timeout` 5 s, `connect_timeout` 3 s),
  or a `GuzzleHttp\ClientInterface` instance. TLS certificates are verified (Guzzle default):
  if the server has no CA bundle, fix the server or pass `'client' => ['verify' => '/path/to/cacert.pem']`.
- `log` (default `true`): writes requests and responses to `@runtime/logs/onesignal.log`,
  payload included. Set it to `false` if the payload contains personal data.

API errors (including timeouts) are thrown as `yii\base\Exception`.

Usage
-----

Call api endpoints:

```php

Yii::$app->onesignal->apps->getAll();

Yii::$app->onesignal->devices->getOne('asd-asd-asd-asd-asd-asd');
Yii::$app->onesignal->devices->getAll();


Yii::$app->onesignal->notifications->add([
    'include_player_ids' => ['player-hash-code-12345-123456789'],
    'contents' => ["en" => 'New message'],
]);


Yii::$app->onesignal->notifications->add([
    'included_segments' => ['Subscribed Users'],
    'contents' => ["en" => 'New message'],
]);
```

`contents` (and `headings`, if used) must always include the `en` key.
