<?php


namespace AppBundle\Service;

//use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;

class RolesService
{
    private $em;
    public $container;

    public function __construct(\Doctrine\ORM\EntityManager $em,Container $container)
    {
//        dump('erer');
//        exit;
        return ['ROLE_ADMIN','ROLE_SA'];
    }
//        dump('asdasdad');exit;
//        $this->em = $em;
//        $this->container = $container;
//    }

    public function init()
    {
        dump("HERE");exit;
//        $roles = $this->em->getRepository('AppBundle:Roles')->findAll();
        return ['ROLE_ADMIN','ROLE_SA'];
////        $this->logger->critical($this->adminEmail.'this is a message in service');
////        $messages = [
////            'You did it! You updated the system! Amazing!',
////            'That was one of the coolest updates I\'ve seen all day!',
////            'Great work! Keep going!',
////        ];
////
////        $index = array_rand($messages);
////
////        return $messages[$index];
    }
}
