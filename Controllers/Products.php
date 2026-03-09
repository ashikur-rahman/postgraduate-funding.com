<?php

namespace App\Controllers;

use App\Libraries\Paypal_lib;

class Products extends BaseController
{

    /**
     * Display available subscription products
     */
    public function index()
    {
        $productsModel = new \App\Models\Product();

        $data['products'] = $productsModel->getRows();

        return view('XXXX/products/index', $data);
    }


    /**
     * Generate unique activation token
     */
    public function generate_uid()
    {
        return md5(uniqid(mt_rand(), true));
    }


    /**
     * Handle subscription purchase
     */
    public function buy()
    {

        $user_type = $_POST["user_type"] ?? null;

        $product_id = $_POST["id"];
        $coupon_id  = $_POST["coupon_id"] ?? null;

        $productsModel = new \App\Models\Product();
        $product = $productsModel->getSingleRow($product_id);


        /**
         * Apply coupon discount
         */
        if (!empty($coupon_id)) {

            $couponModel = new \App\Models\Coupons_management();
            $coupon = $couponModel->getSingleRow($coupon_id);

            $gross_price = $product[0]['price'] - $coupon[0]['value'];

            if ($gross_price <= 0) {
                $gross_price = 0;
            }

        } else {

            $gross_price = $product[0]['price'];
        }


        /**
         * PayPal redirect URLs (sanitized)
         */
        $returnURL = base_url() . '/XXXX-success';
        $cancelURL = base_url() . '/XXXX-cancel';
        $notifyURL = base_url() . '/XXXX-ipn';


        helper(['form']);

        $validation = \Config\Services::validation();


        $rules = [

            'name'  => 'required|min_length[3]|max_length[200]',
            'email' => 'required|min_length[6]|max_length[200]|valid_email'
        ];


        /**
         * Existing user renewal flow
         */
        if ($user_type === 're_new') {

            if ($this->validate($rules)) {

                $user_id = $_POST["user_id"];

                $userModel = new \App\Models\UserModel();

                $userModel->update($user_id, [

                    'role'   => 'editor',
                    'status' => 'pending'
                ]);


                /**
                 * Subscription duration
                 */
                $publish_up_date = date("Y-m-d H:i:s");

                $days = $product[0]['duration_in_days'];

                $publish_down_date = date(
                    'Y-m-d',
                    strtotime("+$days days")
                );


                /**
                 * Prepare payment record
                 */
                $payment_data = [

                    'user_id' => $user_id,
                    'level_id' => $product[0]['id'],
                    'publish_up' => $publish_up_date,
                    'publish_down' => $publish_down_date,
                    'enabled' => 0,
                    'processor' => 'paypal',
                    'state' => 'N',
                    'net_amount' => $product[0]['price'],
                    'gross_amount' => $gross_price,
                    'created_on' => $publish_up_date,
                    'coupon_id' => $coupon_id ?? 0
                ];


                $Subscribed_user = new \App\Models\Subscribed_user_management();

                $subscribe_user = $Subscribed_user
                    ->where('user_id', $user_id)
                    ->first();

                $Subscribed_user->update(
                    $subscribe_user['subscription_id'],
                    $payment_data
                );


                /**
                 * Redirect to PayPal
                 */
                $paypal = new Paypal_lib();

                $paypal->add_field('return', $returnURL);
                $paypal->add_field('cancel_return', $cancelURL);
                $paypal->add_field('notify_url', $notifyURL);

                $paypal->add_field('item_name', $product[0]['name']);
                $paypal->add_field('custom', $user_id);
                $paypal->add_field('item_number', $product[0]['id']);
                $paypal->add_field('amount', $gross_price);

                $paypal->paypal_auto_form();
            }
        }
    }
}