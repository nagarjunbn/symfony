<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Users;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

use AppBundle\Service\MessageGenerator;
//
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

//use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
//use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class AjaxController extends Controller
{

    /**
     * @Route("/ajax/index", name="ajax_list")
     */
    public function indexAction(Request $request)
    {
        $tokenStorage = $this->get('security.token_storage');
        $loggedInUser = $tokenStorage->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:Users')->findAll();

        $one = $em->getRepository('AppBundle:Users')->find(1);

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($one, 'json');
        header('Content-Type: application/json');
        echo $jsonContent;die;
        // return $jsonContent;

//        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
//        $normalizer->normalize($one);
//        dump($normalizer);die;
//        dump($users);exit;
//        return $jsonContent;
        // replace this example code with whatever you need
//        return $this->render('users/index.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
//            'users' => $users
//        ]);
    }

    /**
     * @Route("/ajax/add", name="ajax_add")
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
     * @Route("/ajax/edit", name="ajax_edit")
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
     * @Route("/ajax/delete", name="ajax_delete")
     */
    public function deleteAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:Users')->findOneBy(array('id' => $request->query->get('id')));
        $em->remove($user);
        $em->flush();
        $this->addFlash("notice", "Delete successful!");
        return $this->redirectToRoute('users_list');
    }
}
