<?php
namespace App\Helpers;
use Exception;

const RANDOM_BYTE_LENGTH = 16;

class UtilityHelper {

/**
 * @throws Exception
 */
public static function generateRandomHash(){
    try {
        return bin2hex(random_bytes(RANDOM_BYTE_LENGTH));
    }catch (\Exception $exception){
        //Todo: log this error as critical
        throw new Exception("System error: sorry unable to process request at this time");
    }
}

public static function getWidgetCode($alias){
    $url =  env('WIDGET_URL', '');
    return '<script src="' . $url . '/js/widget.min.js" data-hash="' . $alias . '" data-mode="version-1"></script>';
}

public static function getUserOS() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    $osArray = [
        'Windows'   => 'Win',
        'MacOS'     => 'Macintosh|MacIntel',
        'iOS'       => 'iPhone|iPad',
        'Android'   => 'Android',
        'Linux'     => 'Linux',
        'ChromeOS'  => 'CrOS',
    ];

    foreach ($osArray as $os => $pattern) {
        if (preg_match("/$pattern/i", $userAgent)) {
            return $os;
        }
}

return 'Unknown OS';
}

}
