<?php
/**
 * DES 解密加密类
 *
 */

class Mcrypt
{
    // set default
    var $_key = '!tA:mEv,';
    var $_algorithm = 'des';
    var $_mode = 'ecb';
    static $_td = false;

    function __construct() {
        $this->setDefault();
    }

    function __destruct() {
        if (self::$_td)
            $this->fini();
    }

    // return a negative value on error
    function init() {
        // Open the cipher
        self::$_td = mcrypt_module_open($this->_algorithm, '', $this->_mode, '');
        if (!self::$_td)
            return false;

        // Create the IV and determine the keysize length, used MCRYPT_RAN on Windows instead
        $size = mcrypt_enc_get_iv_size(self::$_td);
        $iv = $this->my_mcrypt_create_iv($size);

           return mcrypt_generic_init(self::$_td, $this->_key, $iv);
    }

    function fini() {
        if (self::$_td) {
            mcrypt_generic_deinit(self::$_td);
            mcrypt_module_close(self::$_td);
        }
        self::$_td = false;
    }

    function setMode($algorithm, $mode) {
        $this->_algorithm = $algorithm;
        $this->_mode = $mode;
    }

    function setKey($key) {
        $this->_key = $key;
    }

    function getKey() {
        return $this->_key;
    }

    function encrypt($data) {
        if (self::$_td == false) {
            if ($this->init() < 0)
                return false;
        }

        return mcrypt_generic(self::$_td, $data);
    }

    function decrypt($encrypted) {
        if (self::$_td == false) {
            if ($this->init() < 0)
                return false;
        }

        return mdecrypt_generic(self::$_td, $encrypted);
    }

    private function setDefault() {
        $_key = '!tA:mEv,';
        $_algorithm = 'des';
        $_mode = 'ecb';
        $_td = false;
    }

    private function my_mcrypt_create_iv($size) {
        $iv = '';
        for ($i =0; $i < $size; $i++) {
            $iv .= chr(rand(0,255));
        }
        return $iv;
    }
}
