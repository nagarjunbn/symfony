<?php

namespace AppBundle\Service;

use Psr\Log\LoggerInterface;

class MessageGenerator
{

    private $logger;
    private $adminEmail;

    public function __construct(LoggerInterface $logger,$adminEmail)
    {
        $this->logger = $logger;
        $this->adminEmail = $adminEmail;
    }

    public function getHappyMessage()
    {
        $this->logger->critical($this->adminEmail.'this is a message in service');
        $messages = [
            'You did it! You updated the system! Amazing!',
            'That was one of the coolest updates I\'ve seen all day!',
            'Great work! Keep going!',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }
}
