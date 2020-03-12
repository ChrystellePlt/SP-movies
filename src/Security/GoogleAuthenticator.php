<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    /** @var ClientRegistry $clientRegistry */
    protected $clientRegistry;

    /** @var EntityManagerInterface $manager */
    protected $manager;

    /** @var UserPasswordEncoderInterface $encoder */
    protected $encoder;

    /**
     * GoogleAuthenticator constructor.
     *
     * @param ClientRegistry               $clientRegistry
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->clientRegistry = $clientRegistry;
        $this->manager        = $manager;
        $this->encoder        = $encoder;
    }

    /**
     * @param Request $request
     *
     * @return bool|void
     */
    public function supports(Request $request)
    {
        // continue ONLY if the current route matches the login route
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    /**
     * @param Request $request
     *
     * @return array|\League\OAuth2\Client\Token\AccessToken|mixed
     */
    public function getCredentials(Request $request)
    {
        if ($request->isMethod('POST')) {
            return $request->request->all()['login'];
        };

        // this method is only called if supports() returns true
        return $this->fetchAccessToken($this->getGoogleClient());
    }

    /**
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface
     */
    private function getGoogleClient()
    {
        return $this->clientRegistry->getClient('google');
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface|null
     * @throws \Exception
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var UserRepository $repository */
        $repository = $this->manager->getRepository(User::class);

        if (is_array($credentials)) {
            /** @var User $user */
            $user = $repository->findOneBy(['email' => $credentials['email']]);

            // check if user exists
            if ($user) {
                // if user exists, check his credentials
                if ($this->checkCredentials($credentials, $user)) {

                    return $user;
                }
            }

            return null;

        } else {
            /** @var GoogleUser $googleUser */
            $googleUser = $this->getGoogleClient()->fetchUserFromToken($credentials);

            /** @var User $existingUser */
            $user = $repository->findOneBy(['googleId' => $googleUser->getId()]);

            // check if user has logged in with Google before
            if (!$user) {
                // as user has never logged in before, we create a new one
                $user = new User();
                $user->setEmail($googleUser->getEmail());
                $user->setGoogleId($googleUser->getId());
                $user->setUsername($googleUser->getName());
                // set random password
                $plainPassword = random_bytes(10);
                $password = $this->encoder->encodePassword($user, $plainPassword);
                $user->setPassword($password);

                $this->manager->persist($user);
                $this->manager->flush();
            }
        }

        return $repository->findOneBy(['email' => $user->getEmail()]);
    }

    /**
     * @param               $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if (!$user->getGoogleId()) {
            // check if the given password matches the password stored in the database
            return $this->encoder->isPasswordValid($user, $credentials['password']);
        }

        return true;
    }

    /**
     * This method is called only if the method getUser() above returns a User
     *
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    /**
     * This method is called only if the method getUser() above returns 'null'
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * This method is called when authentication is needed, but nothing is sent to authenticate the user
     *
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return RedirectResponse|Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login', Response::HTTP_TEMPORARY_REDIRECT);
    }
}
