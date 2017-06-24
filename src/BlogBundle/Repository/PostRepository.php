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
    
    public function getLastArticles($first_result, $max_results = 5){
        $qb = $this->getBaseQb();
        $qb
                ->orderBy('post.date', 'DESC')
                ->setFirstResult($first_result)
                ->setMaxResults($max_results);
        
        $pag = new Paginator($qb);
        return $pag;
        //return $qb->getQuery()->getResult();
    }
    
    public function getLastArticlesByCategory($title){
        $qb = $this->getBaseQb();
        $qb
                ->where($qb->expr()->like('category.title', ':title'))
                    ->setParameter('title', str_replace("-", " ", $title))
                ->orderBy('post.date', 'DESC')
                ->setMaxResults(5);
        
        return $qb->getQuery()->getResult();
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
