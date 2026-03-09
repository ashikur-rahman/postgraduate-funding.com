<?php

namespace App\Libraries;

class Paypal_lib
{

    /**
     * ---------------------------------------------------------
     * Core Class Properties
     * ---------------------------------------------------------
     */

    var $last_error;
    var $ipn_log;
    var $ipn_log_file;
    var $ipn_response;
    var $ipn_data = [];
    var $fields = [];

    var $submit_btn = '';
    var $button_path = '';

    /**
     * PayPal Environment Flags
     */

    private $use_sandbox = false;
    private $use_local_certs = true;

    /**
     * ---------------------------------------------------------
     * PayPal Verification Endpoints
     * ---------------------------------------------------------
     * NOTE: In public repositories these URLs are masked.
     */

    const VERIFY_URI = 'https://XXXX.paypal.com/cgi-bin/webscr';
    const SANDBOX_VERIFY_URI = 'https://XXXX.sandbox.paypal.com/cgi-bin/webscr';

    const VALID = 'VERIFIED';
    const INVALID = 'INVALID';


    /**
     * ---------------------------------------------------------
     * Constructor
     * ---------------------------------------------------------
     * Initializes PayPal configuration and default fields.
     */

    public function __construct()
    {

        helper(['url', 'form']);

        /**
         * NOTE:
         * Sensitive configuration values are masked
         * when publishing code publicly.
         */

        $paypal_config = new \Config\Paypal();

        $sandbox = $paypal_config->sandbox;

        /**
         * PayPal Payment URLs
         */

        $this->paypal_url = $sandbox
            ? 'https://XXXX.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://XXXX.paypal.com/cgi-bin/webscr';

        $this->paypal_ipn_url = $sandbox
            ? 'https://XXXX.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://XXXX.paypal.com/cgi-bin/webscr';


        $this->last_error = '';
        $this->ipn_response = '';

        /**
         * Sensitive configuration placeholders
         */

        $this->ipn_log_file = 'XXXX/logs/paypal_ipn.log';
        $this->ipn_log = true;

        $this->button_path = 'XXXX/assets/paypal_buttons';


        /**
         * Default PayPal Form Fields
         */

        $businessEmail = 'XXXX@business-email.com';

        $this->add_field('business', $businessEmail);

        $this->add_field('rm', '2');
        $this->add_field('cmd', '_xclick');

        $this->add_field('currency_code', 'XXX');

        $this->add_field('quantity', '1');

        $this->button('Pay Now');
    }


    /**
     * ---------------------------------------------------------
     * Set PayPal Button Caption
     * ---------------------------------------------------------
     */

    public function button($value)
    {
        $this->submit_btn = form_submit('pp_submit', $value);
    }


    /**
     * ---------------------------------------------------------
     * Use PayPal Image Button
     * ---------------------------------------------------------
     */

    public function image($file)
    {
        $this->submit_btn =
            '<input type="image" name="add" src="' .
            base_url(rtrim($this->button_path, '/') . '/' . $file) .
            '" border="0" />';
    }


    /**
     * ---------------------------------------------------------
     * Add Field To PayPal Form
     * ---------------------------------------------------------
     */

    public function add_field($field, $value)
    {
        $this->fields[$field] = $value;
    }


    /**
     * ---------------------------------------------------------
     * Auto Submit PayPal Payment Form
     * ---------------------------------------------------------
     */

    public function paypal_auto_form()
    {

        $this->button('Redirecting to PayPal...');

        echo '<html>';
        echo '<head><title>Processing Payment</title></head>';
        echo '<body onLoad="document.forms[\'paypal_auto_form\'].submit();">';
        echo '<p>Please wait while we redirect you to the payment gateway.</p>';

        echo $this->paypal_form('paypal_auto_form');

        echo '</body></html>';
    }


    /**
     * ---------------------------------------------------------
     * Generate PayPal HTML Form
     * ---------------------------------------------------------
     */

    public function paypal_form($form_name = 'paypal_form')
    {

        $str = '';

        $str .= '<form method="post" action="' . $this->paypal_url . '" name="' . $form_name . '">';

        $str .= csrf_field();

        foreach ($this->fields as $name => $value) {

            $str .= form_hidden($name, $value);
        }

        $str .= '<p>' . $this->submit_btn . '</p>';

        $str .= form_close();

        return $str;
    }


    /**
     * ---------------------------------------------------------
     * Validate PayPal IPN Response
     * ---------------------------------------------------------
     */

    public function validate_ipn($paypalReturn)
    {

        $ipn_response = $this->curlPost($this->paypal_ipn_url, $paypalReturn);

        if (preg_match("/VERIFIED/i", $ipn_response)) {

            return true;
        }

        $this->last_error = 'IPN Validation Failed';

        $this->log_ipn_results(false);

        return false;
    }


    /**
     * ---------------------------------------------------------
     * Log IPN Results
     * ---------------------------------------------------------
     */

    public function log_ipn_results($success)
    {

        if (!$this->ipn_log) return;

        $text = '[' . date('m/d/Y g:i A') . '] - ';

        $text .= $success ? "SUCCESS\n" : "FAIL: " . $this->last_error . "\n";

        foreach ($this->ipn_data as $key => $value) {

            $text .= "$key=$value ";
        }

        $fp = fopen($this->ipn_log_file, 'a');

        fwrite($fp, $text . "\n\n");

        fclose($fp);
    }


    /**
     * ---------------------------------------------------------
     * Send Verification Request to PayPal
     * ---------------------------------------------------------
     */

    public function curlPost($paypal_url, $paypal_return_arr)
    {

        $req = 'cmd=_notify-validate';

        foreach ($paypal_return_arr as $key => $value) {

            $value = urlencode(stripslashes($value));

            $req .= "&$key=$value";
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $paypal_url);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }


    /**
     * ---------------------------------------------------------
     * Enable Sandbox Mode
     * ---------------------------------------------------------
     */

    public function useSandbox()
    {
        $this->use_sandbox = true;
    }


    /**
     * ---------------------------------------------------------
     * Get PayPal Verification Endpoint
     * ---------------------------------------------------------
     */

    public function getPaypalUri()
    {

        return $this->use_sandbox
            ? self::SANDBOX_VERIFY_URI
            : self::VERIFY_URI;
    }


    /**
     * ---------------------------------------------------------
     * Verify PayPal IPN Message
     * ---------------------------------------------------------
     */

    public function verifyIPN()
    {

        if (!count($_POST)) {

            throw new \Exception("Missing POST Data");
        }

        $raw_post_data = file_get_contents('php://input');

        $req = 'cmd=_notify-validate';

        $ch = curl_init($this->getPaypalUri());

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);

        $res = curl_exec($ch);

        curl_close($ch);

        return ($res == self::VALID);
    }
}