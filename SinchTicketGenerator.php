<?php

/**
 * Generates a ticket to be used in Sincha when using own authentication method
 */
class SinchTicketGenerator
{
    private $applicationKey; 
    private $applicationSecret;
    public function __construct($applicationKey, $applicationSecret)
    {
        $this->applicationKey = $applicationKey;
        $this->applicationSecret = $applicationSecret;
    }
    public function generateTicket($username, DateTime $createdAt, $expiresIn)
    {
        $userTicket = [
            'identity' => [
                'type'      => 'username',
                'endpoint'  => $username,
            ],
            'expiresIn'         => $expiresIn,
            'applicationKey'    => $this->applicationKey,
            'created'           => $createdAt->format('c'),
        ];
        $userTicketJson = preg_replace('/\s+/', '', json_encode($userTicket));
        $userTicketBase64 = $this->base64Encode($userTicketJson);
        $digest = $this->createDigest($userTicketJson);
        $signature = $this->base64Encode($digest);
        $userTicketSigned = $userTicketBase64.':'.$signature;
        return $userTicketSigned;
    }
    private function base64Encode($data)
    {
        return trim(base64_encode($data));
    }
    private function createDigest($data)
    {
        return trim(hash_hmac('sha256', $data, base64_decode($this->applicationSecret), true));
    }
}