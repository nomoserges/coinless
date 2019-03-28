<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Prolib {

    protected static $CI;

    public function __construct() {
        self::$CI =& get_instance();
    }

    public function coors() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") { die(); }
    }

    /**
     * Recupere les donnees RAW-JSON des formulaires client.
     */
    public static function jsonInput(){
        $_REQUEST= json_decode(trim(file_get_contents('php://input')), true);
    }

    /** Renvoie des informations au format json.
     * @param string $msgType
     * @param $msgTitle
     * @param $msgBody
     * @param array $msgData
     */
    public static function jsonOutput(string $msgType, string $msgTitle, array $msgData){
        echo json_encode(
            array(
                'msgType' => $msgType,
                'msgTitle' => $msgTitle,
                'msgData' => $msgData,
            )
        );
    }

    /**
     * @param array $mailFrom[mail_address, name]
     * @param string $mailTo
     * @param string $mailSubject
     * @param $mailBody
     * @return mixed
     */
    public static function sendMail(string $mailTo, string $mailSubject, $mailBody){
        self::$CI->email->from('Coinless', 'noreply@coinless.io');
        self::$CI->email->to($mailTo);
        self::$CI->email->subject($mailSubject);
        self::$CI->email->message($mailBody);
        return self::$CI->email->send();
    }

    /** Check if value is a correct email address.
     * @param $value
     * @return bool
     */
    public static function checkEmail($value){
        if(filter_var($value, FILTER_VALIDATE_EMAIL)) {
            # valid address
            return true;
        } else {    return false;   }
    }

    /** Clean phone number input
     * @param $value
     * @return bool|mixed|string|string[]|null
     */
    public static function sanitizePhoneNumber($value){
        # eliminate every char except 0-9
        $phoneNum = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        #   Remove "-" from number
        $phoneNum = str_replace("-", "", $phoneNum);
        #   remove 237 or 00237 or +237
        if( substr($phoneNum, 0, 3)=='237'){
            $phoneNum = self::strReplaceFirst('237', '', $phoneNum, 1);
        }
        if( substr($phoneNum, 0, 4)=='+237'){
            $phoneNum = self::strReplaceFirst('+237', '', $phoneNum, 1);
        }
        if( substr($phoneNum, 0, 5)=='00237'){
            $phoneNum = self::strReplaceFirst('00237', '', $phoneNum, 1);
        }
        if( substr($phoneNum, 0, 4)=='0237'){
            $phoneNum = self::strReplaceFirst('0237', '', $phoneNum, 1);
        }
        //eliminate leading 1 if its there
        if ( strlen($phoneNum) > 9 ){
            $phoneNum = substr($phoneNum, 0, 9);
        } elseif ( strlen($phoneNum) < 9 ) {
            return 'invalid';
        } else {
            return $phoneNum;
        }
    }

    private static function strReplaceFirst($from, $to, $content) {
        return preg_replace('/'.preg_quote($from, '/').'/', $to, $content, 1);
    }

    public static function qrGenerate($filename, $input){
        # we generate de qrcode
        $qr_image = $filename.'.png';
        $params['data'] = $input;
        $params['level'] = 'H';
        $params['size'] = 117;
        $params['savename'] =FCPATH."writable/qrimages/".$qr_image;
        # if the same filename exist, we remove it before add new one.
        if( file_exists($params['savename']) && is_file($params['savename']) ){
            @unlink($params['savename']);
        }
        if(self::$CI->ciqrcode->generate($params)) {
            return true;
        } else {
            echo false;
        }
    }
}
