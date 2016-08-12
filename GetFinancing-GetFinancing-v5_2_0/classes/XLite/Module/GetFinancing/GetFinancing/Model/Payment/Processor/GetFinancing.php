<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\GetFinancing\GetFinancing\Model\Payment\Processor;

/**
 * GetFinancing processor
 *
 * Find the latest API document here:
 * http://docs.GetFinancing.com/
 */
class GetFinancing extends \XLite\Model\Payment\Base\WebBased
{

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return 'modules/GetFinancing/GetFinancing/config.tpl';
    }

    /**
     * Get input template
     *
     * @return string|void
     */
    public function getInputTemplate()
    {
        return 'modules/GetFinancing/GetFinancing/payment.tpl';
    }

    /**
     * Process return
     *
     * @param \XLite\Model\Payment\Transaction $transaction Return-owner transaction
     *
     * @return void
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processReturn($transaction);

    }

    public function processCallback(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processCallback($transaction);

        $request = \XLite\Core\Request::getInstance();

        $json = file_get_contents('php://input');

        //\XLite\Logger::logCustom('pmt', var_export($json,1), '');

        $temp = json_decode($json, true);


        if ($signature_check != $temp['updates']['approved']) {
            //hack detected
            $status = $transaction::STATUS_FAILED;
            $this->setDetail('verification', 'Verification failed', 'Verification');

            $this->transaction->setNote('Verification Status: '.$temp['updates']['approved']);

        } else {
            $status = $transaction::STATUS_SUCCESS;
            $this->setDetail('result', 'Accept', 'Result');
        }

        $this->transaction->setStatus($status);
    }

    /**
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return parent::isConfigured($method)
            && $method->getSetting('username')
            && $method->getSetting('merchant_id')
            && $method->getSetting('password');
    }

    /**
     * Get return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return self::RETURN_TYPE_HTML_REDIRECT;
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return array(
            'username',
            'merchant_id',
            'password',
            'test',
        );
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }

    /**
     * Check - payment method has enabled test mode or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isTestMode(\XLite\Model\Payment\Method $method)
    {
        return (bool)$method->getSetting('test');
    }

    /**
     * Get allowed currencies
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return array
     */
    protected function getAllowedCurrencies(\XLite\Model\Payment\Method $method)
    {
        return array('AUD', 'USD', 'CAD', 'EUR', 'GBP', 'NZD');
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return 'https://demoshop.pagamastarde.com/getfinancing/xcart/redirect.php';
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        /** @var \XLite\Model\Currency $currency */
        $currency = $this->transaction->getCurrency();

        $ok_url = $this->getReturnURL(null, true);
        $nok_url = $this->getPaymentReturnURL('decline');
        $callback_url = $this->getCallbackURL(null, true); //$this->getPaymentReturnURL('accept');
        $merchant_loan_id = md5(time() .$this->getSetting('merchant_id'). $this->getProfile()->getBillingAddress()->getFirstname() .$this->transaction->getValue());

        $gf_data = array(
            'amount'           => round($this->transaction->getValue(), 2),
            'product_info'     => $this->_getProductsInfo(),
            'first_name'       => $this->getProfile()->getBillingAddress()->getFirstname(),
            'last_name'        => $this->getProfile()->getBillingAddress()->getLastname(),
            'shipping_address' => array(
                'street1'  => $this->getProfile()->getBillingAddress()->getStreet(),
                'city'    => $this->getProfile()->getBillingAddress()->getCity(),
                'state'   => $this->getProfile()->getBillingAddress()->getState()->getCode(),
                'zipcode' => $this->getProfile()->getBillingAddress()->getZipcode()
            ),
            'billing_address' => array(
                'street1'  => $this->getProfile()->getBillingAddress()->getStreet(),
                'city'    => $this->getProfile()->getBillingAddress()->getCity(),
                'state'   => $this->getProfile()->getBillingAddress()->getState()->getCode(),
                'zipcode' => $this->getProfile()->getBillingAddress()->getZipcode()
            ),
            'version'          => '1.9',
            'email'            => $this->getProfile()->getLogin(),
            'phone'            => $this->getProfile()->getBillingAddress()->getPhone(),
            'merchant_loan_id' => $merchant_loan_id,
            'success_url' => $ok_url,
            'failure_url' => $nok_url,
            'postback_url' => 'https://demoshop.pagamastarde.com/getfinancing/xcart/callback.php',
            'software_name' => 'x-cart',
            'software_version' => 'xcart 5'
        );

        $body_json_data = json_encode($gf_data);
        $header_auth = base64_encode($this->getSetting('username') . ":" . $this->getSetting('password'));

        $post_args = array(
            'body' => $body_json_data,
            'timeout' => 60,     // 60 seconds
            'blocking' => true,  // Forces PHP wait until get a response
            'headers' => array(
              'Content-Type' => 'application/json',
              'Authorization' => 'Basic ' . $header_auth,
              'Accept' => 'application/json'
             )
        );
        if ($this->getSetting('test')){
           $this->gf_url = 'https://api-test.getfinancing.com';
        }else{
           $this->gf_url = 'https://api.getfinancing.com';
        }
        $url_to_post =  $this->gf_url.'/merchant/' . $this->getSetting('merchant_id')  . '/requests';
        $gf_response = $this->_remote_post( $url_to_post, $post_args );
        $gf_response = json_decode($gf_response);
         $fields = array(
             'callback_url'    => $callback_url,
             'success_url'          => $ok_url,
             'failure_url'         => $nok_url,
             'pay_method'      => 'CC',
             'form_url' => $gf_response->href,
             'trx_id' => $merchant_loan_id,
         );


        /* not using shipping address
        $shippingAddress = $this->getProfile()->getShippingAddress();
        if ($shippingAddress) {

            $fields += array(
                'x_ship_to_first_name'  => $shippingAddress->getFirstname(),
                'x_ship_to_last_name'   => $shippingAddress->getLastname(),
                'x_ship_to_address'     => $shippingAddress->getStreet(),
                'x_ship_to_city'        => $shippingAddress->getCity(),
                'x_ship_to_state'       => $shippingAddress->getState()->getCode()
                    ? $shippingAddress->getState()->getCode()
                    : 'n/a',
                'x_ship_to_zip'         => $shippingAddress->getZipcode(),
                'x_ship_to_country'     => $shippingAddress->getCountry()->getCountry(),
            );
        }
        */
        //\XLite\Logger::logCustom('pmt', var_export($fields,1), '');
        return $fields;
    }

    /**
     * Get payment return URL, type: accept, decline, exception, cancel
     *
     * @param string $type Type of return
     *
     * @return string
     */
    protected function getPaymentReturnURL($type)
    {
        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL(
                'payment_return',
                null,
                array(
                    self::RETURN_TXN_ID => $this->transaction->getPublicTxnId(),
                    'type'              => $type,
                )
            ),
            \XLite\Core\Config::getInstance()->Security->customer_security
        );
    }

    /**
     * Set up RemotePost / Curl.
     */
    function _remote_post($url,$args=array()) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $args['body']);
        curl_setopt($curl, CURLOPT_USERAGENT, 'X-Cart - GetFinancing Payment Module ');
        if (defined('CURLOPT_POSTFIELDSIZE')) {
            curl_setopt($curl, CURLOPT_POSTFIELDSIZE, 0);
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, $args['timeout']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        $array_headers = array();
        foreach ($args['headers'] as $k => $v) {
            $array_headers[] = $k . ": " . $v;
        }
        if (sizeof($array_headers)>0) {
          curl_setopt($curl, CURLOPT_HTTPHEADER, $array_headers);
        }

        if (strtoupper(substr(@php_uname('s'), 0, 3)) === 'WIN') {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $resp = curl_exec($curl);
        curl_close($curl);
        if (!$resp) {
          return false;
        } else {
          return $resp;
        }
    }

    // build product description
    function _getProductsInfo(){
      $des=array();
      foreach ($this->getOrder()->getItems() as $item) {
          $product = $item->getProduct();
          $description = $product->getCommonDescription() ?: $product->getName();
          if ($item->getAmount() > 1) {
              $desc  = substr($product->getName(), 0, 127) .
              " (".$item->getAmount().") ";
          } else {
              $desc  = substr($product->getName(), 0, 127);
          }
          $des[]=$desc;
      }
      return implode(",",$des);
    }
}
