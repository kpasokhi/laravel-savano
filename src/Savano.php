<?php

namespace kpasokhi\savano;

/**
 * Savano Payment Gateway Extension For Laravel
 *
 * @author Amir Khoshhal <amirkhoshhal@gmail.com>
 * @author Koorosh Pasokhi <kpasokhi@gmail.com>
 */

class Savano
{
    public  $pin;
    public  $callback;
    private $form;
    private $formDetails;
    private $authority;
    private $bankAuthority = 0;
    private $result = 0;
    private $errMsg = null;


    /**
     * Payment Request
     *
     * Save Authority In Your Database, You Need This When You Call Verify Method
     *
     * @param int    $price
     * @param int    $orderId
     * @param string $callback
     * @param string $email
     * @param string $description
     * @param string $name
     * @param string $mobile
     * @param string $ip
     * @param int    $callbackType
     *
     * @return $this
     */
    public function request($price, $orderId, $callback, $email = '', $description = '', $name = '', $mobile = '', $ip = '', $callbackType = 2)
    {
        $this->callback = $callback;

        $dataString = json_encode([
            'pin'      => $this->pin,
            'price'    => $price,
            'callback' => $this->callback,
            'order_id' => $orderId,
            'email'    => $email,
            'description'   => $description,
            'name'          => $name,
            'mobile'        => $mobile,
            'ip'            => $ip,
            'callback_type' => $callbackType,
        ]);

        $result = $this->curl('https://developerapi.net/api/v1/request', $dataString);

        // Result
        $json = json_decode($result,true);

        $this->result    = $json['result'];
        $this->authority = $json['au'];

        if($this->result === 1)
        {
            $this->form        = $json['form'];
            $this->formDetails = $json['form_details'];
        }
        else
        {
            $this->errMsg      = $json['msg'];
        }

        return $this;
    }

    /**
     * Payment Verification
     *
     * @param string $authority
     * @param int $price
     * @param int $orderId
     *
     * @return $this
     */
    public function verify($authority, $price, $orderId)
    {
        $this->authority = $authority;

        $dataString = json_encode([
            'pin'      => $this->pin,
            'price'    => $price,
            'order_id' => $orderId,
            'au'       => $this->authority,
            'bank_return' => [
                'SaleReferenceId' => '20170814113803',
                'ResCode'  => 'random_res_code_8',
                'card_pan' => '1111111111111111',
                'State'    => '1',
            ],
        ]);

        $result = $this->curl('https://developerapi.net/api/v1/verify', $dataString);

        // Result
        $json = json_decode($result, true);

        $this->result        = $json['result'];
        $this->authority     = $json['au'];

        if($this->result === 1)
        {
            $this->bankAuthority = $json['bank_au'];
        }
        else if($this->result < 0)
        {
            $this->errMsg        = $json['msg'];
        }

        return $this;
    }

    /**
     * Get Result Code
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Get Authority
     * @return mixed
     */
    public function getAuthority()
    {
        return $this->authority;
    }

    /**
     * Get Error Message
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->errMsg;
    }

    /**
     * Get Form To Redirect User To Bank
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get Redirect Url
     * @return string
     */
    public function getRedirectUrl()
    {
        $url = $this->formDetails['action'];

        $token = $this->formDetails['fields']['Token'];

        $finalUrl = $url.'?Token='.$token;

        return $finalUrl;
    }

    /**
     * Send Request by CURL
     * @param $url
     * @param $data
     * @return mixed
     */
    private function curl($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($data)]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return  $result;
    }
}