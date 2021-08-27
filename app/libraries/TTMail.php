<?php
class TTMail
{
    // Declaration Of Variables Necessary For The Class
    public $type;

    public $smtp_host;
    public $smtp_port = 25;
    public $smtp_ssl = false;
    public $smtp_auth = false;

    public $smtp_user;
    public $smtp_pass;
    // Mail Class Constructor Function
    public function __construct()
    {
        switch (strtolower(Config::TT()['mail_type'])) {
            case "pear":
                $this->smtp_ssl = mail_smtp_ssl;

                if ($this->smtp_ssl) {
                    $this->smtp_host = "ssl://" . Config::TT()['mail_smtp_host'];
                } else {
                    $this->smtp_host = Config::TT()['mail_smtp_host'];
                }

                $this->type = "pear";
                $this->smtp_port = Config::TT()['mail_smtp_port'];
                $this->smtp_auth = Config::TT()['mail_smtp_auth'];
                $this->smtp_user = Config::TT()['mail_smtp_user'];
                $this->smtp_pass = Config::TT()['mail_smtp_pass'];

                if (!@include_once ("Mail.php")) {
                    trigger_error("Config is set to use PEAR Mail but it is not installed (or include_path is wrong).", E_USER_WARNING);
                    $this->type = "php";
                }
                break;
            case "php":
            default:
                $this->type = "php";
        }
    }
    
    // Function That Allows Sending Mail
    public function Send($to, $subject, $message, $additional_headers = "", $additional_parameters = "")
    {
        if (preg_match("!^From:(.*)!m", $additional_headers, $matches)) {
            $from = trim($matches[1]);
        } else {
            $from = Config::TT()['SITEEMAIL'];
        }

        $additional_headers = preg_replace("!^From:(.*)!m", "", $additional_headers);
        $additional_headers .= "\nFrom: $from\nReturn-Path: $from";
        $additional_headers = trim($additional_headers);
        $additional_headers = preg_replace("!\n+!", "\n", $additional_headers);

        switch ($this->type) {
            case "pear":
                $headers = array("From" => $from, "Return-Path" => $from, "To" => $to, "Subject" => $subject);
                $params = array("host" => $this->smtp_host, "port" => $this->smtp_port, "auth" => $this->smtp_auth, "username" => $this->smtp_user, "password" => $this->smtp_pass);
                $smtp = Mail::Factory("smtp", $params);

                $mail = $smtp->send($to, $headers, $message);

                break;
            case "php":
                @mail($to, $subject, $message, $additional_headers, $additional_parameters);
                break;
        }
    }
}