<?php
namespace Sample\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityController extends Controller
{
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function loginAction(Request $request)
    {
        $areas = $this->container->getParameter('areas');
        $default = $this->container->getParameter('ad.default.area');
        return $this->render('SampleSecurityBundle:Security:login.html.twig', array(
            'areas'         => $areas,
             'default_area' => $default,
        ));
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function loginCheckAction(Request $request)
    {
        // 
        $user = $this->getDoctrine()
            ->getRepository('SampleSecurityBundle:ADUser')
            ->findOneBy(array(
                'username' => $request->get('username')
            ));
        
        if (!$user) {
            $this->get('session')
                ->getFlashBag()
                ->add('error', 'User Not Allowed');
            return $this->redirect($this->generateUrl('login'));
        }
        $user->setPassword($request->get('password'));
        $user->setArea($request->get('area'));
        $adHost = $this->container->getParameter('host');
        $adPort = $this->container->getParameter('port');

        $ds = ldap_connect($adHost, $adPort);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

        $passValid = @ldap_bind($ds, $user->getUsername().'@'.$user->getArea(), $user->getPassword());

        if ($passValid) {
            $token = new UsernamePasswordToken($user->getUsername(), null, 'main', $user->getRoles());

            $session = $this->get('session');
            $this->get('security.context')->setToken($token);
            $session->set('_security_main', serialize($token));
            $session->save();

            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            return $this->redirect($this->generateUrl('admin_home'));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Wrong user or password');
            return $this->redirect($this->generateUrl('login'));
        }
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function adminAction(Request $request)
    {
        $search = $request->get('search');
        
        $users = $this->getDoctrine()
            ->getRepository('SampleSecurityBundle:ADUser')
            ->getAllWithRoles($search);
        
        $paginator = $this->get('knp_paginator')->paginate(
            $users,
            $request->get('page', 1),
            $this->container->getParameter('knp.rows_per_page')
        );
        
        $roles = $this->getDoctrine()
            ->getRepository('SampleSecurityBundle:ADRole')
            ->findAll();

        return $this->render('SampleSecurityBundle:Admin:index.html.twig', array(
            'users' => $paginator,
            'roles' => $roles,
        ));
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addroleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->getDoctrine()
            ->getRepository('SampleSecurityBundle:ADUser')
            ->find($request->get('id'));
        
        $role = $this->getDoctrine()
            ->getRepository('SampleSecurityBundle:ADRole')
            ->find($request->request->get('role'));
        
        $user->addRole($role);
        
        $em->persist($user);
        $em->flush();
        
        return $this->redirect($this->generateUrl('user_admin'));
    }
} 