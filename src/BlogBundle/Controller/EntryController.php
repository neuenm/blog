<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BlogBundle\Entity\Entry;
use BlogBundle\Form\EntryType;

class EntryController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function indexAction($page) {
        $em = $this->getDoctrine()->getEntityManager();
        
        $category_repo = $em->getRepository("BlogBundle:Category");
        $categories = $category_repo->findAll();
        
        $pageSize= 5;
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $entries = $entry_repo->getPaginateEntries($pageSize,$page);
        $totalItems=  count($entries);
        
        
        $pagesCount= ceil($totalItems/$pageSize);
        
        return $this->render("BlogBundle:Entity:index.html.twig", array(
                    "entries" => $entries,
                    "categories" => $categories,
                    "totalItems"=> $totalItems,
                    "pagesCount"=> $pagesCount,
                    "page"=> $page,
        ));
    }

    public function addAction(Request $request) {
        $entry = new Entry();
        $form = $this->CreateForm(EntryType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $category_repo = $em->getRepository("BlogBundle:Category");
                $entry_repo = $em->getRepository("BlogBundle:Entry");


                $entry = new Entry();
                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());

                $file = $form["image"]->getData();
                $ext = $file->guessExtension();
                $file_name = time() . $ext;
                $file->move("uploads", $file_name);


                $entry->setImage($file_name);
                $category = $category_repo->find($form->get("category")->getData());
                $entry->setCategory($category);
                $user = $this->getUser();
                $entry->setUser($user);
                $em->persist($entry);
                $flush = $em->flush();

                $entry_repo->saveEntryTag(
                        $form->get("tags")->getData(), $form->get("title")->getData(), $user, $category);

                if ($flush == null) {
                    $status = "La entrada se a añadido correctamente";
                } else {
                    $status = "La entrada no se a creado";
                }
            } else {
                $status = "la entrada no se ah creado";
            }
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_homepage");
        } else {
            $status = "la categoria no se ah creadooooooooo";
        }

        return $this->render('BlogBundle:Entity:add.html.twig', array(
                    "form" => $form->createView(),
        ));
    }

    public function deleteAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $entry = $entry_repo->find($id);
        $entry_tag_repo = $em->getRepository("BlogBundle:EntryTag");
        $entry_tag_repo=$entry_tag_repo->findBy(array("entry"=>$entry));
       
        foreach ($entry_tag_repo as $et){
            $em->remove($et);
            $em->flush();
        }

        $em->remove($entry);
        $em->flush();


        return $this->redirectToRoute("blog_homepage");
    }

    
    public function editAction (Request $request, $id){
        $em = $this->getDoctrine()->getEntityManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $entry_repo=$em->getRepository("BlogBundle:Entry");
        $entry=$entry_repo->find($id);
        $form= $this->CreateForm(EntryType::class, $entry);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if($form->isValid()){
                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());

                $file = $form["image"]->getData();
                $ext = $file->guessExtension();
                $file_name = time() . $ext;
                $file->move("uploads", $file_name);


                $entry->setImage($file_name);
                $category = $category_repo->find($form->get("category")->getData());
                $entry->setCategory($category);
                $user = $this->getUser();
                $entry->setUser($user);
                $em->persist($entry);
                $flush = $em->flush();

                $entry_repo->saveEntryTag(
                $form->get("tags")->getData(), $form->get("title")->getData(), $user, $category);

                if ($flush == null) {
                        $status = "La entrada se a añadido correctamente";
                    } else {
                        $status = "La entrada no se a creado";
                    }
                     
            }else{
                $status = "Formulario incorrecto";
            }
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_homepage"); 
           
        }
        return $this->render('BlogBundle:Entity:edit.html.twig',array (
            "form"=>$form->createView(),
            "entry"=>$entry
            
        ));
    }
}
