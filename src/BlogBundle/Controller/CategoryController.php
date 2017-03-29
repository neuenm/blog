<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Session\Session; 
use BlogBundle\Entity\Category;
use BlogBundle\Form\CategoryType;

class CategoryController extends Controller
{  private $session;

   public function __construct() {
       $this->session = new Session();
    }
    
    public function indexAction(Request $request){
        $em= $this->getDoctrine()->getManager();
        $categories= $em->getRepository("BlogBundle:Category");
        $categories=$categories->findAll();
        
        return $this->render('BlogBundle:Category:index.html.twig', array(
            "categories"=>$categories,
            ));
        
    }
    

    public function addAction(Request $request){
        $category = new Category();
        $form= $this->CreateForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()){
            if ($form->isValid()){
                $category= new Category();
                $category->setName($form->get("name")->getData());
                $category->setDescription($form->get("description")->getData());
                $em=$this->getDoctrine()->getManager();
                $em->persist($category);
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
            return $this->redirectToRoute("blog_index_category");
        }else{
            $status="la etiqueta no se ah creadooooooooo";  
        }
        
        return $this->render('BlogBundle:Category:add.html.twig', array(
            "form"=>$form->createView(),
            
        ));
    }
     
    public function deleteAction($id){
            $em = $this->getDoctrine()->getEntityManager();
            $category_repo=$em->getRepository("BlogBundle:Category");
            $category=$category_repo->find($id);
            $em->remove($category);
            $em->flush();
            

            return $this->redirectToRoute("blog_index_category");
    }
    
    public function editAction( Request $request,$id){
            $em = $this->getDoctrine()->getEntityManager();
            $category_repo=$em->getRepository("BlogBundle:Category");
            $category=$category_repo->find($id);
            $form= $this->CreateForm(CategoryType::class, $category);
            $form->handleRequest($request);
            if ($form->isSubmitted()){
            if ($form->isValid()){
               
                $category->setName($form->get("name")->getData());
                $category->setDescription($form->get("description")->getData());
                $em=$this->getDoctrine()->getManager();
                $em->persist($category);
                $flush=$em->flush();
                if ($flush==null){
                        $status ="Categoria editada";
                    }else{
                        $status="no se ah podido editars";
                    }
            }else{
                $status="la etiqueta no se ah editado";
            }
            $this->session->getFlashBag()-> add("status",$status);
            return $this->redirectToRoute("blog_index_category");
        }else{
            $status="la etiqueta no se ah creadooooooooo";  
        }
            
            
        return $this->render('BlogBundle:Category:edit.html.twig',array (
            "form"=>$form->createView()
        ));
    }
    
    public function categoryAction ($id){
        $em = $this->getDoctrine()->getEntityManager();
        $category_repo=$em->getRepository("BlogBundle:Category");
        $category=$category_repo->find($id);
        
        return $this->render("BlogBundle:Category:category.html.twig", array(
            "category"=>$category
        ));
    }
}