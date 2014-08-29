<?php
namespace UberLog;

class HttpClient
{

    public $ch;

    public function __construct($url)
    {
        $ch = curl_init($url);
        $this->ch = $ch;
    }

    public function postArray($data)
    {
        $data_string = json_encode($data);

        $headers = array (
            'Content-Type: application/json'
        );
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data_string);

        return curl_exec($this->ch);
    }

}
