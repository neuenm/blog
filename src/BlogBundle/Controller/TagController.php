<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Session\Session; 
use BlogBundle\Entity\Tag;
use BlogBundle\Form\TagType;

class TagController extends Controller
{  private $session;

   public function __construct() {
       $this->session = new Session();
    }
    
    public function indexAction(Request $request){
        $em= $this->getDoctrine()->getManager();
        $tags= $em->getRepository("BlogBundle:Tag");
        $tags=$tags->findAll();
        
        return $this->render('BlogBundle:Tag:index.html.twig', array(
            "tags"=>$tags,
            ));
        
    }
    

    public function addAction(Request $request){
        $tag = new Tag();
        $form= $this->CreateForm(TagType::class, $tag);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()){
            if ($form->isValid()){
                $tag= new Tag();
                $tag->setName($form->get("name")->getData());
                $tag->setDescription($form->get("description")->getData());
                $em=$this->getDoctrine()->getManager();
                $em->persist($tag);
                $flush=$em->flush();
                if ($flush==null){
                        $status ="Nuevo etiqueta ingresada";
                    }else{
                        $status="no se ah ingresado la etiqueta";
                    }
            }else{
                $status="la etiqueta no se ah creado";
            }
            $this->session->getFlashBag()-> add("status",$status);
            return $this->redirectToRoute("blog_index_tag");
        }else{
            $status="la etiqueta no se ah creadooooooooo";  
        }
        
        return $this->render('BlogBundle:Tag:add.html.twig', array(
            "form"=>$form->createView(),
            
        ));
    }
     
    public function deleteAction($id){
            $em = $this->getDoctrine()->getEntityManager();
            $tag_repo=$em->getRepository("BlogBundle:Tag");
            $tag=$tag_repo->find($id);

            if(count($tag->getEntryTag())== 0){
                    $em->remove($tag);
                    $em->flush();
            }

            return $this->redirectToRoute("blog_index_tag");
    }
}
