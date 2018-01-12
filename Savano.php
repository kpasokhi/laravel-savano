<?php

namespace amirkhh\savano;

use yii\base\Model;

class Savano extends Model
{
    public $pin;
    public $callback;
    public $form;
    public $formDetails;
    public $au;////// private
    public $bankAu = 0;
    public $result = 0;
    public $errMsg = null;

    public function request(int $price, int $orderId, string $callback, $email = '', $description = '', $name = '', $mobile = '', $ip = '', $callbackType = 2): Savano
    {
        $this->callback = $callback;

        $dataString = json_encode([
            'pin' => $this->pin,
            'price' => $price,
            'callback' => $this->callback,
            'order_id' => $orderId,
            'email' => $email,
            'description' => $description,
            'name' => $name,
            'mobile' => $mobile,
            'ip' => $ip,
            'callback_type' => $callbackType,
        ]);

        $result = $this->curl('https://developerapi.net/api/v1/request', $dataString);

        // Result
        $json = json_decode($result,true);

        $this->result = $json['result'];
        $this->au     = $json['au'];
        $this->errMsg = $json['msg'];

        if($this->result === 1)
        {
            $this->form        = $json['form'];
            $this->formDetails = $json['form_details'];
        }

        return $this;
        //var_dump($json);
    }

    public function verify(string $au, int $price, int $orderId): Savano
    {
        $this->au = $au;

        $dataString = json_encode([
            'pin' => $this->pin,
            'price' => $price,
            'order_id' => $orderId,
            'au' => $this->au,
            'bank_return' =>
                [
                    'SaleReferenceId' => '20170814113803',
                    'ResCode' => 'random_res_code_8',
                    'card_pan' => '1111111111111111',
                    'State' => '1',
                ],
        ]);

        $result = $this->curl('https://developerapi.net/api/v1/verify', $dataString);

        // Result
        $json = json_decode($result, true);

        $this->result = $json['result'];
        $this->au     = $json['au'];
        $this->bankAu = $json['bank_au'];

        return $this;
    }

    public function getRedirectUrl(): string
    {
        $url = $this->formDetails['action'];

        $token = $this->formDetails['fields']['Token'];

        $finalUrl = $url.'?Token='.$token;

        return $finalUrl;
    }

    private function curl(string $url, string $data): string
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
