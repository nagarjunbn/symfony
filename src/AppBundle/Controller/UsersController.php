<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Users;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

use AppBundle\Service\MessageGenerator;


class UsersController extends Controller
{

    /**
     * @Route("/users/index", name="users_list")
     */
    public function indexAction(Request $request)
    {
        $tokenStorage = $this->get('security.token_storage');
        $loggedInUser = $tokenStorage->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:Users')->findAll();
        // replace this example code with whatever you need
        return $this->render('users/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'users' => $users
        ]);
    }

    /**
     * @Route("/users/add", name="users_add")
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository('AppBundle:Roles')->findAll();
        if ($request->getMethod() == "POST") {
            $data = $request->request->all();
            $userObj = new Users();
//            $validator = Validation::createValidator();
//            $violations = $validator->validate($data['name'], array(
//                new Length(array('min' => 10)),
//                new NotBlank(),
//            ));
//            if (0 !== count($violations)) {
//                // there are errors, now you can show them
//                foreach ($violations as $violation) {
//                    echo $violation->getMessage().'<br>';
//                }
//                dump('herer');
//                exit;
//            }
            $role = $em->getRepository('AppBundle:Roles')->findOneBy(array('id' => $data['role']));
            $userObj->setName($data['name']);
            $userObj->setUsername($data['username']);
            $encoder = $this->get('security.password_encoder');
            $encodedPassword = $encoder->encodePassword($userObj, $data['new_password']);
            $userObj->setPassword($encodedPassword);
            $userObj->setRole($role);
            $em->persist($userObj);
            $em->flush();
            $this->addFlash("notice", "Addition successful!");
            return $this->redirectToRoute('users_list');
        }

        return $this->render('users/add.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'roles' => $roles
        ]);
    }


    /**
     * @Route("/users/edit", name="users_edit")
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository('AppBundle:Roles')->findAll();
        $user = $em->getRepository('AppBundle:Users')->findOneBy(array('id' => $request->query->get('id')));

        if ($request->getMethod() == "POST") {
            $data = $request->request->all();
            $userObj = $em->getRepository('AppBundle:Users')->findOneBy(array('id' => $request->query->get('id')));
            $role = $em->getRepository('AppBundle:Roles')->findOneBy(array('id' => $data['role']));
            $userObj->setName($data['name']);
            $userObj->setRole($role);
            $userObj->setUsername($data['username']);
            $em->persist($userObj);
            $em->flush();
            $em->clear();
            $this->addFlash("notice", "Update successful!");
            return $this->redirectToRoute('users_list');
        }
        return $this->render('users/edit.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'roles' => $roles,
            'user' => $user
        ]);
    }

    /**
     * @Route("/users/delete", name="users_delete")
     */
    public function deleteAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:Users')->findOneBy(array('id' => $request->query->get('id')));
        $em->remove($user);
        $em->flush();
        $this->addFlash("notice", "Delete successful!");
        return $this->redirectToRoute('users_list');
    }

    /**
     * @Route("/services", name="services_example")
     */
    public function logger(LoggerInterface $logger,MessageGenerator $messageGenerator1) {
        $message1 = $messageGenerator1->getHappyMessage();
        $logger->info('Look! I just used a service');
        $logger->warning($message1);
        $messageGenerator2 = $this->get(MessageGenerator::class);
        $message2 = $messageGenerator2->getHappyMessage();
        $logger->notice($message2);
        echo 'service triggered'."<br>";
        try {

            // Generate a version 1 (time-based) UUID object
            $uuid1 = Uuid::uuid1();
            echo $uuid1->toString() . "<br>"; // i.e. e4eaaaf2-d142-11e1-b3e4-080027620cdd

            // Generate a version 3 (name-based and hashed with MD5) UUID object
            $uuid3 = Uuid::uuid3(Uuid::NAMESPACE_DNS, 'php.net');
            echo $uuid3->toString() . "<br>"; // i.e. 11a38b9a-b3da-360f-9353-a5a725514269

            // Generate a version 4 (random) UUID object
            $uuid4 = Uuid::uuid4();
            echo $uuid4->toString() . "<br>"; // i.e. 25769c6c-d34d-4bfe-ba98-e0ee856f3e7a

            // Generate a version 5 (name-based and hashed with SHA1) UUID object
            $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'php.net');
            echo $uuid5->toString() . "<br>"; // i.e. c4a760a8-dbcf-5254-a0d9-6a4474bd1b62

        } catch (UnsatisfiedDependencyException $e) {

            // Some dependency was not met. Either the method cannot be called on a
            // 32-bit system, or it can, but it relies on Moontoast\Math to be present.
            echo 'Caught exception: ' . $e->getMessage() . "\n";

        }
        exit;
    }
}
