<?php
/**
 * Crypto Payment API
 *
 * The Class for Process Crypto Payment Gateways
 * Copyright (c) 2018 - 2024, Foris Limited ("Crypto.com")
 *
 * @class      Crypto_Payment_Api
 * @package    Crypto/Classes
 * @located at /includes/
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * The Class for Processing Crypto Payment API
 */
class Crypto_Payment_Api
{
    /**
     * payment api url
     *
     * @var string $crypto_api_payment_url
     */
    protected static $crypto_api_payment_url = 'https://pay.crypto.com/api/payments/';
    protected static $crypto_api_refund_url = 'https://pay.crypto.com/api/refunds/';

    /**
     * Get http response
     *
     * @param string $url url.
     * @param string $secret_key secret key.
     * @param string $method method.
     * @param string $data data.
     * @return array
     */
    private static function get_http_response($url, $secret_key, $method = 'get', $data = '')
    {

        if ('get' === $method) {
            $response = wp_remote_get($url,
                array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $secret_key,
                    ),
                )
            );
        } else {
            $response = wp_remote_post($url,
                array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $secret_key,
                    ),
                    'body' => $data,
                )
            );
        }

        $result = array();

        // if wordpress error
        if (is_wp_error($response)) {
            $result['error'] = $response->get_error_message();
            $result['request'] = $data;
            return $result;
        }

        $response = wp_remote_retrieve_body($response);
        $response_json = json_decode($response, true);

        // if outgoing request get back a normal response, but containing an error field in JSON body
        if ($response_json['error']) {
            $result['error'] = $response_json['error'];
            $result['error']['message'] = $result['error']['param'] . ' ' . $result['error']['code'];
            $result['request'] = $data;
            return $result;
        }

        // if everything normal
        $result['success'] = $response_json;
        return $result;
    }

    /**
     * create a payment
     * 
     * @param string $order_id
     * @param string $currency currency
     * @param string $amount amount
     * @param string $customer_name customer name
     * @param string $secret_key secret key
     * @since 1.3.0
     */
    public static function request_payment($order_id, $currency, $amount, $customer_name, $return_url, $cancel_url, $secret_key) 
    {
        $data = array(
            'order_id' => $order_id,
            'currency' => $currency,
            'amount' => Currency_Helper::get_currency_in_subunit($currency, $amount),
            'reason' => $reason,
            'description' => 'WooCommerce order ID: ' . $order_id,
            'metadata' => array (
                'customer_name' => $customer_name,
				'plugin_name' => 'woocommerce',
                'plugin_flow' => 'redirect'
            ),
            'return_url' => $return_url,
            'cancel_url' => $cancel_url
        );

        return self::get_http_response(self::$crypto_api_payment_url, $secret_key, 'post', $data);
    }

    /**
     * retrieve a payment by payment unique id
     *
     * @param string $payment_id payment id.
     * @param string $secret_key secret key.
     * @return array
     */
    public static function retrieve_payment($payment_id, $secret_key)
    {
        $crypto_api_payment_url = self::$crypto_api_payment_url . $payment_id;
        return self::get_http_response($crypto_api_payment_url, $secret_key);
    }

    /**
     * request a refund by payment unique id
     *
     * @param string $payment_id payment id
     * @param string $order_id WooCommerce order id
     * @param string $currency currency
     * @param string $amount amount
     * @param string $reason reason
     * @param string $secret_key secret key
     * @return array
     * @since 1.1.0
     */
    public static function request_refund($payment_id, $order_id, $currency, $amount, $reason, $secret_key)
    {

        $data = array(
            'payment_id' => $payment_id,
            'currency' => $currency,
            'amount' => Currency_Helper::get_currency_in_subunit($currency, $amount),
            'reason' => $reason,
            'description' => 'Refund for WooCommerce order ID: ' . $order_id,
        );

        return self::get_http_response(self::$crypto_api_refund_url, $secret_key, 'post', $data);
    }

}
