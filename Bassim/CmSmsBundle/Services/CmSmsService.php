<?php
namespace Bassim\CmSmsBundle\Services;

class CmSmsService
{
    private $customerId;
    private $username;
    private $password;
    private $url;

    public function __construct(
        $customerId,
        $username,
        $password,
        $url
    ) {
        $this->customerId = $customerId;
        $this->username = $username;
        $this->password = $password;
        $this->url = $url;
    }

    public function send($senderName, $body, $number, $tariff = 0)
    {
        $xmlToSend = $this->createMessage(
            $this->customerId,
            $this->username,
            $this->password,
            $tariff,
            $senderName,
            $body,
            $number
        );

        $response =  $this->sendMessage($this->url, $xmlToSend);
        if (strlen($response)>0) {
            throw new \Exception($response);
        }

        return true;
    }

    private function createMessage($customerId, $login, $password, $tariff, $senderName, $body, $msisdn)
    {
        $xmlSms = new \SimpleXMLElement('<MESSAGES/>');
        $xmlSms->addAttribute('PID', 25);
        $xmlSms->addChild('CUSTOMER');
        $xmlSms->{"CUSTOMER"}->addAttribute('ID', $customerId);
        $xmlSms->addChild('USER');
        $xmlSms->{"USER"}->addAttribute('LOGIN', $login);
        $xmlSms->{"USER"}->addAttribute('PASSWORD', $password);
        $xmlSms->addChild('TARIFF');
        $xmlSms->{"TARIFF"} = $tariff;
        $xmlSms->addChild('MSG');
        $xmlSms->{"MSG"}->addChild('FROM');
        $xmlSms->{"MSG"}->FROM = $senderName;
        $xmlSms->{"MSG"}->addChild('BODY');
        $xmlSms->{"MSG"}->BODY = $body;
        $xmlSms->{"MSG"}->addChild('TO');
        $xmlSms->{"MSG"}->TO = $msisdn;
        return $xmlSms->asXML();
    }

    private function sendMessage($url, $message)
    {
        $cHandle = curl_init();
        curl_setopt($cHandle, CURLOPT_URL, $url);
        #curL_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($cHandle, CURLOPT_POST, 1);
        curl_setopt($cHandle, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', 'Content-length: '.strlen($message)));
        curl_setopt($cHandle, CURLOPT_POSTFIELDS, $message);
        curl_setopt($cHandle, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($cHandle);
        curl_close($cHandle);
        return $return;
    }
}
