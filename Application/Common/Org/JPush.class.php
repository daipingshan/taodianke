<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2017/11/17
 * Time: 10:35
 */

namespace Common\Org;
require __DIR__.'/JPush/autoload.php';
use JPush\Client;
use Think\Exception;

/**
 * 极光推送
 * Class JPush
 *
 * @package Common\Org
 */
class JPush {

    protected $client = null;

    /**
     * JPush constructor.
     */
    public function __construct() {
        $this->client = new Client(C('JPUSH.app_key'), C('JPUSH.app_secret'));
    }

    /**
     * @param $alert
     * @param $data
     * @param string $alias
     * @param string $platform
     * @param string $type
     * @param int $time_to_live
     * @return array
     */
    public function push($alert, $data, $alias = 'all', $platform = 'all', $type = 'all', $time_to_live = 86400) {
        try {
            $pusher = $this->client->push();
            if ($alias == 'all') {
                $pusher = $pusher->addAllAudience();
            } else {
                $pusher = $pusher->addAlias($alias);
            }
            $pusher = $pusher->setPlatform($platform);
            if ($type == 'all') {
                $pusher = $pusher->androidNotification($alert, array( 'extras' => $data ));
                $pusher = $pusher->iosNotification($alert, array( 'extras' => $data ));
                $pusher = $pusher->message($alert, array( 'extras' => $data ));
            } else if ($type == 'msg') {
                $pusher = $pusher->message($alert, array( 'extras' => $data ));
            } else {
                $pusher = $pusher->androidNotification($alert, array( 'extras' => $data ));
                $pusher = $pusher->iosNotification($alert, array( 'extras' => $data ));
            }
            $pusher = $pusher->options(array( 'apns_production' => false, 'time_to_live' => $time_to_live ));
            $pusher->send();
            return array( 'status' => 0 );
        } catch (\Exception $e) {
            return array( 'status' => -1, 'info' => $e );
        }

    }

}