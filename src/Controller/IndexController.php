<?php
/**
 * Created by PhpStorm.
 * User: chrystelle
 * Date: 2020-03-03
 * Time: 17:20
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class IndexController extends AbstractController
{
    /** @var EntityManagerInterface $manager */
    protected $manager;

    /**
     * IndexController constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('pages/index.html.twig');
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        $form = $this->createForm(LoginType::class);

        return $this->render('pages/login.html.twig', [
            'form'  => $form->createView(),
            'error' => $error
        ]);
    }

    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        /** @var User $user */
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $plainPassword = $user->getPassword();
            $password = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($password);

            $this->manager->persist($user);
            $this->manager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('pages/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
