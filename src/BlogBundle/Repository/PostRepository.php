<?php

namespace BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;


class PostRepository extends \Doctrine\ORM\EntityRepository
{
 
    public function getBaseQb() {
        $qb = $this->createQueryBuilder('post');
        $qb->leftJoin('post.category', 'category');
        return $qb;
    }
    
    public function getLastArticles($page, $max_results = 5){
        $qb = $this->getBaseQb();
        $qb
                ->orderBy('post.date', 'DESC')
                ->setFirstResult(($page-1) * $max_results)
                ->setMaxResults($max_results);
       
        return new Paginator($qb);
    }
    
    public function getLastArticlesByCategory($title, $page, $max_results = 5){
        $qb = $this->getBaseQb();
        $qb
                ->where($qb->expr()->like('category.title', ':title'))
                    ->setParameter('title', str_replace("-", " ", $title))
                ->orderBy('post.date', 'DESC')
                ->setFirstResult(($page-1) * $max_results)
                ->setMaxResults($max_results);
        
         return new Paginator($qb);
    }
    
    public function getPostByTitle($title){
        $qb = $this->getBaseQb();
        $qb
                ->where($qb->expr()->like('post.title', ':title'))
                    ->setParameter('title', str_replace("-", " ", $title))
                ->orderBy('post.date', 'DESC')
                ->setMaxResults(5);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
        
}
