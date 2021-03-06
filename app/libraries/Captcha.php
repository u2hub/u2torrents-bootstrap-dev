<?php

class Captcha
{
    private $key;
    private $secret;

    public function __construct()
    {
        $this->key = Config::TT()['CAPTCHA_KEY'];
        $this->secret = Config::TT()['CAPTCHA_SECRET'];
    }

    public function response($captcha)
    {
        if (Config::TT()['CAPTCHA_ON']) {
            if (!$captcha) {
                    Redirect::autolink(URLROOT . '/login', Lang::T('<b>Please check the the captcha form.</b>'));
            }
            $secret = Config::TT()['CAPTCHA_SECRET'];
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $this->secret . '&response=' . $captcha);
            $responseData = json_decode($verifyResponse);
            if ($responseData->success) {
                // its successfull so let it go
            }
        }
    }

    public function html()
    {
        if (Config::TT()['CAPTCHA_ON']) {
            return print("<div class='g-recaptcha' data-theme='light' data-sitekey='" . $this->key . "'></div>");
        }
    }

}