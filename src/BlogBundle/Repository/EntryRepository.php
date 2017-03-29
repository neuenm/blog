<?php

nameSpace BlogBundle\Repository;
use BlogBundle\Entity\Tag;
use BlogBundle\Entity\EntryTag;
use Doctrine\ORM\Tools\Pagination\Paginator;
class EntryRepository extends \Doctrine\ORM\EntityRepository {

    public function saveEntryTag($tags=null, $title=null,$user=null, $category=null, $entry=null ){
        $em=$this->getEntityManager();
        $tag_repo=$em->getRepository("BlogBundle:Tag");
        
        if($entry==null){
            $entry=$this->findOneBy(array(
                "title"=>$title,
                "category"=>$category,
                "user"=>$user,
            ));
        }else{}
        
        $tags=explode(",",$tags);
        foreach ($tags as $tag){
            $isset_tag = $tag_repo->findOneBy(array("name"=>$tag));
            if (count($isset_tag)==0){
                $tag_obj=new Tag();
                $tag_obj->setName($tag);
                $tag_obj->setDescription($tag);
                if(!empty(trim($tag))){
                    $em->persist($tag_obj);
                    $flush=$em->flush(); 
                }
            }
            $tag = $tag_repo->findOneBy(array("name"=>$tag));
            $entry_tag=new EntryTag();
            $entry_tag->setEntry($entry);
            $entry_tag->setTag($tag);
            $em->persist($entry_tag);
           
        }
         $flush=$em->flush();
         return $flush;
    }
    
    public function getPaginateEntries($pageSize=5,$currentPage=1){
        
        $em=$this->getEntityManager();
        $dql="SELECT e FROM BlogBundle\Entity\Entry e ORDER BY e.id DESC";
        $query=$em->createQuery($dql)
                    ->setFirstResult($pageSize*($currentPage-1))
                    ->setMaxResults($pageSize);
        $paginator= new Paginator($query, $fetchJoinCollection=true);
        return $paginator;
    }
}
