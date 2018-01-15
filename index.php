<?php

include_once("vendor/autoload.php");

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

$baseUrl = "https://api.hh.ru";
$client = new Client([
    // Base URI is used with relative requests
    'base_uri' => $baseUrl,
    'timeout'  => 2.0,

]);
$token=file_get_contents('https://opendevelopers.ru/getKey.php');
$headers=[
    'headers' => [
        'User-Agent' => 'Letunovskiymn/1.0 (miha-1221@inbox.ru)',
        'Accept' => '*/*',
        'Authorization' => 'Bearer '.$token
    ]
];

$response = $client->request('GET', '/resumes/mine', $headers);

if ($response->getStatusCode() == 200){
    $data=json_decode($response->getBody(),true);
    foreach ($data['items'] as $resume){
        $date=new DateTime();
        $idResume=$resume['id'];
        try {
            $responseResume = $client->request('POST', '/resumes/'.$idResume.'/publish', $headers);
            if ($responseResume->getStatusCode()==204){
                //very good
                file_put_contents('log.txt',$date->format('d-m-Y H:i:s')."\n".
                    $responseResume->getBody()."\n", FILE_APPEND);
            }
        }
        catch (ClientException $e){
            file_put_contents('log.txt',$date->format('d-m-Y H:i:s')."\n".
                $e->getMessage()."\n", FILE_APPEND);
        }
    }
}
