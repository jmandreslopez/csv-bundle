<?php
namespace Sample\SecurityBundle\Security\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ADProvider extends UserAuthenticationProvider
{
    private $adService;
    private $userProvider;
    private $encoderFactory;

    public function __construct($adService, UserProviderInterface $userProvider, UserCheckerInterface $userChecker, $providerKey, EncoderFactoryInterface $encoderFactory, $hideUserNotFoundExceptions = true)
    {
        parent::__construct($userChecker, $providerKey, $hideUserNotFoundExceptions);
        $this->encoderFactory   = $encoderFactory;
        $this->userProvider     = $userProvider;
        $this->adService = $adService;
    }

    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        $currentUser = $token->getUser();
        if ($currentUser instanceof UserInterface) {
            if (!$presentedPassword = $token->getCredentials()) {
                throw new BadCredentialsException('The presented password cannot be empty.');
            }
            $ds = ldap_connect($this->adService['host'], $this->adService['port']);
            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            $passValid = @ldap_bind($ds, $token->getUsername() .'@' . $token->getArea(), $token->getCredentials());
            if (!$passValid) {
                throw new BadCredentialsException('The password is invalid');
            }

        }
    }

    protected function retrieveUser($username, UsernamePasswordToken $token)
    {

        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            return $user;
        }
        try {
            $user = $this->userProvider->loadUserByUsername($username);

            if (!$user instanceof UserInterface) {
                throw new AuthenticationServiceException('The user must return a UserInterface Object');
            }
            $ds = ldap_connect($this->adService['host'], $this->adService['port']);
            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            $passValid = @ldap_bind($ds, $token->getUsername() .'@' . $token->getArea(), $token->getCredentials());
            if (!$passValid) {
                throw new BadCredentialsException('The password is invalid');
            }
            return $user;
        } catch(UsernameNotFoundException $notFound) {
            throw $notFound;
        } catch (\Exception $repositoryProblem) {
            throw new AuthenticationServiceException($repositoryProblem->getMessage(), 0, $repositoryProblem);
        }
    }

    public function authenticate(TokenInterface $token)
    {
        $parentToken = parent::authenticate($token);
        if (!$parentToken) {
            return;
        }
        $authenticatedToken = new ADToken($parentToken->getUsername(), $parentToken->getCredentials(), $parentToken->getProviderKey(),
            $parentToken->getRoles(), $token->getArea());
        $authenticatedToken->setAttributes($token->getAttributes());
        return $authenticatedToken;
    }
} 