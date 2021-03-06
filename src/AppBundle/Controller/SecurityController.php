<?php
/**
 * Created by PhpStorm.
 * User: gandalf
 * Date: 25/12/17
 * Time: 22:57
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\LoginType;
use AppBundle\Form\RegistrationType;
use AppBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @Route("/security")
 *
 */
class SecurityController extends Controller
{
    /**
     * @Route("/inscription", name="security_inscription")
     */
    public function inscriptionAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password)
                ->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush();

            $this->authenticateUser($user);
            return $this->redirectToRoute('accueil');

        }

        return $this->render('security/inscription.html.twig', array('form' => $form->createView()));


    }

    private function authenticateUser(User $user)
    {
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

    }

    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {


        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('accueil');
        } else {
            $user = new User();
            $form = $this->createForm(LoginType::class, $user, ['action' => $this->generateUrl('login_check')]);

            if ($form->isSubmitted() & $form->isValid()) {
                $this->authenticateUser($user);
                return $this->redirectToRoute('accueil');
            } else if ($form->isSubmitted() & !$form->isValid()) {
                throw new Exception('mot de passe invalide');
            }

            return $this->render('security/login.html.twig',
                array('form' => $form->createView(),
                    'errors' => $authenticationUtils->getLastAuthenticationError()));
        }
    }

    /**
     * @Route("/checkusernamelogin", name="username_loginform")
     */
    public function usernameLogin(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = $request->get('matricule');
            $em = $this->getDoctrine()->getManager();
            $compagnon = $em->getRepository(User::class)->findBy(['matricule'=>$data]);
            $compagnon = $compagnon[0];
            return new JsonResponse(array("username" => json_encode($compagnon->getUsername()),
            ));
        }
    }

}