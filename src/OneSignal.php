<?php

namespace micheleraso\onesignal;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use micheleraso\onesignal\resolver\ResolverFactory;
use micheleraso\onesignal\service\Apps;
use micheleraso\onesignal\service\Devices;
use micheleraso\onesignal\service\Notifications;
use micheleraso\onesignal\service\Request;
use Yii;
use yii\base\Component;
use yii\log\FileTarget;

/**
 * @property-read Apps $apps          Applications API service
 * @property-read Devices $devices       Devices API service
 * @property-read Notifications $notifications Notifications API service
 */
class OneSignal extends Component
{
    public $apiUrl = 'https://onesignal.com/api/v1';
    public $appId;
    public $appAuthKey;
    public $userAuthKey;
    public $enabled = true;
    /**
     * @var bool
     */
    public $log = true;
    /**
     * @var bool
     */
    public $logTarget = [
        'class' => '\yii\log\FileTarget',
        'logFile' => '@runtime/logs/onesignal.log',
        'categories' => ['micheleraso\onesignal\*'],
        'logVars' => [],
    ];

    /**
     * HTTP client used for the API calls.
     *
     * - a `GuzzleHttp\ClientInterface` instance is used as is;
     * - an array is treated as Guzzle request options, merged over DEFAULT_CLIENT_OPTIONS
     *   (e.g. `['timeout' => 10]`, or `['verify' => '/path/to/cacert.pem']`);
     * - null uses DEFAULT_CLIENT_OPTIONS.
     *
     * TLS certificate verification follows Guzzle's default (enabled).
     *
     * @var ClientInterface|array|null
     */
    public $client;

    /**
     * Default Guzzle options: a slow or unreachable OneSignal must not block the caller.
     */
    const DEFAULT_CLIENT_OPTIONS = [
        'timeout' => 5,
        'connect_timeout' => 3,
    ];

    /**
     * @var array
     */
    private $services = [
        'apps' => 'micheleraso\onesignal\service\Apps',
        'devices' => 'micheleraso\onesignal\service\Devices',
        'notifications' => 'micheleraso\onesignal\service\Notifications',
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!$this->client instanceof ClientInterface) {
            $this->client = new Client(array_merge(
                self::DEFAULT_CLIENT_OPTIONS,
                is_array($this->client) ? $this->client : []
            ));
        }

        if (!empty($this->logTarget)) {
            Yii::$app->getLog()->targets['onesignal'] = Yii::createObject($this->logTarget);
        }
    }

    /**
     * Create required services on the fly.
     *
     * @param string $name
     *
     * @return \micheleraso\onesignal\service\Request
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\UnknownPropertyException
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->services)) {
            if (!is_object($this->services[$name])) {
                $this->services[$name] = Yii::createObject([
                    'class' => $this->services[$name],
                    'api' => $this,
                    'basicAuthKey' => ($name == 'apps' ? $this->userAuthKey : $this->appAuthKey),
                ]);
            }
            return $this->services[$name];
        }
        return parent::__get($name);
    }

    /**
     * @param $message
     * @param $category
     */
    public function log($message, $category = 'micheleraso\onesignal')
    {
        if($this->log){
            Yii::info($message, $category);
        }
    }
}
