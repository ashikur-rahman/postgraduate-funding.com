<?php

namespace App\Controllers;

use App\Libraries\Paypal_lib;
use Config\Services;

class Paypal extends BaseController
{

    /**
     * Payment success handler
     */
    public function success()
    {

        $request = Services::request();

        $productsModel = new \App\Models\Product();
        $paymentModel  = new \App\Models\Payment();

        $item_number    = $request->getPost('item_number');
        $txn_id         = $request->getPost('txn_id');
        $payment_gross  = $request->getPost('payment_gross');
        $mc_currency    = $request->getPost('mc_currency');
        $payment_status = $request->getPost('payment_status');


        if (!empty($item_number) && !empty($txn_id)) {

            $productData = $productsModel->getSingleRow($item_number);

            $publish_up_date = date(
                'Y-m-d',
                strtotime($request->getPost('payment_date'))
            );

            $days = $productData[0]['duration_in_days'];

            $publish_down_date = date(
                'Y-m-d',
                strtotime("+$days days")
            );


            /**
             * Activate subscription
             */
            if ($payment_status === "Completed") {

                $user_id = $request->getPost('custom');

                $subscribedUserModel =
                    new \App\Models\Subscribed_user_management();

                $subscribedUserModel->update_by_user_id($user_id, [

                    'enabled' => 1,
                    'state' => 'C',
                    'processor_key' => $txn_id,
                    'publish_up' => $publish_up_date,
                    'publish_down' => $publish_down_date
                ]);


                /**
                 * Activate user account
                 */
                $UserModel = new \App\Models\UserModel();

                $UserModel->update($user_id, [

                    'status' => 1
                ]);


                /**
                 * Send confirmation email
                 */
                $email = Services::email();

                $email->setTo('XXXX@domain.com');

                $email->setFrom('XXXX@domain.com', 'XXXX System');

                $email->setSubject('Subscription Confirmation');

                $email->setMessage('Your subscription is now active.');

                $email->send();


                /**
                 * Store payment record
                 */
                $paymentModel->insert([

                    'user_id' => $user_id,
                    'product_id' => $item_number,
                    'txn_id' => $txn_id,
                    'payment_gross' => $payment_gross,
                    'currency_code' => $mc_currency,
                    'status' => $payment_status
                ]);


                return view('XXXX/paypal/success');
            }
        }
    }


    /**
     * Payment cancel page
     */
    public function cancel()
    {
        return view('XXXX/paypal/cancel');
    }


    /**
     * PayPal IPN Listener
     */
    public function ipn()
    {

        $paypal = new Paypal_lib();

        $verified = $paypal->verifyIPN();

        if ($verified) {

            $paymentModel = new \App\Models\Payment();

            $paymentModel->insert([

                'user_id' => 1,
                'product_id' => $_POST["item_number"],
                'txn_id' => $_POST["txn_id"],
                'payment_gross' => $_POST["mc_gross"],
                'currency_code' => $_POST["mc_currency"],
                'status' => $_POST["payment_status"]
            ]);
        }

        header("HTTP/1.1 200 OK");
    }
}