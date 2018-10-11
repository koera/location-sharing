<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/api/save/user", name="user.save")
     * @Method({"POST"})
     * @return Response
     */
    public function saveUser(Request $request){
        if($request->get('user_name')){
            $em = $this->getDoctrine()->getManager();
            $user = new User();
            $user->setName($request->get('user_name'));
            $em->persist($user);
            $em->flush();
            $response = new Response();
            $response->setContent(json_encode(['status'=>200,'message' => 'user saved']));
        
            $response->headers->set('Content-Type', 'application/json');
            // Allow all websites
            $response->headers->set('Access-Control-Allow-Origin', '*');  
            return $response;
        }
    }

    /**
     * @param Request $request
     * @Route("/api/save/position", name="position.save")
     * @Method({"POST"})
     * @return Response
     */
    public function savePosition(Request $request){
        if($request->get('user_name') && $request->get('latitude') && $request->get('longitude')){
            $em = $this->getDoctrine()->getManager();
            /** @var User $user */
            $user = $em->getRepository(User::class)->findOneBy(['name'=> $request->get('user_name')]);
            if($user){
                $user->setPosition([$request->get('latitude'),$request->get('longitude')]);
                $em->persist($user);
                $em->flush();
            }else{
                $user = new User();
                $user->setPosition([$request->get('latitude'),$request->get('longitude')]);
                $user->setName($request->get('user_name'));
                $em->persist($user);
                $em->flush();
            }
            $response = new Response();
            $response->setContent(json_encode(['status'=>200,'message' => 'position saved']));
        
            $response->headers->set('Content-Type', 'application/json');
            // Allow all websites
            $response->headers->set('Access-Control-Allow-Origin', '*');
            // Or a predefined website
            //$response->headers->set('Access-Control-Allow-Origin', 'https://jsfiddle.net/');
            // You can set the allowed methods too, if you want    //$response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');    
            return $response;
        }
    }

     /**
     * @param Request $request
     * @Route("/api/get/position", name="position.get")
     * @Method({"GET"})
     * @return Response
     */
    public function getPosition(Request $request){
        if($request->get('user_name')){
            $em = $this->getDoctrine()->getManager();
            /** @var User $user */
            $user = $em->getRepository(User::class)->findOneBy(['name'=> $request->get('user_name')]);
            if($user && $user->getPosition()!= null){
                $response = new Response();
                $response->setContent(json_encode(['status'=>200,'data' => $user->getPosition()]));
            
                $response->headers->set('Content-Type', 'application/json');
                // Allow all websites
                $response->headers->set('Access-Control-Allow-Origin', '*');
                // Or a predefined website
                //$response->headers->set('Access-Control-Allow-Origin', 'https://jsfiddle.net/');
                // You can set the allowed methods too, if you want    //$response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');    
                return $response;
            }
            else{
                $response = new Response();
                $response->setContent(json_encode(['status'=> 400,'message' => 'Position '.$request->get('user_name').' not found yet']));
            
                $response->headers->set('Content-Type', 'application/json');
                // Allow all websites
                $response->headers->set('Access-Control-Allow-Origin', '*');
                // Or a predefined website
                //$response->headers->set('Access-Control-Allow-Origin', 'https://jsfiddle.net/');
                // You can set the allowed methods too, if you want    //$response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');    
                return $response;
            }
        }
    }
}
