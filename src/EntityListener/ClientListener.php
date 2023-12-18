<?php

namespace App\EntityListener;

use App\Entity\Client;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientListener 
{
    private UserPasswordHasherInterface $hasher;
    
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function prePersist(Client $client)
    {
        $this->encodePassword($client);
    }

    public function preUpdate(Client $client)
    {
        $this->encodePassword($client);
    }

    public function encodePassword(Client $client)
    {
        if($client->getPlainPassword() === null){
            return;
        }

        $client->setPassword(
            $this->hasher->hashPassword(
                $client,
                $client->getPlainPassword()
            )
        );
    }
}