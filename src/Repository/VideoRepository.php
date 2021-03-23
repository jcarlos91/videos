<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    /**
     * @return QueryBuilder
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getActiveVideos(){
        $q = $this->createQueryBuilder('v');
        $q
            ->select('count(v.id)')
            ->where('v.deleted = 0')
        ;

        return $q->getQuery()->getSingleScalarResult();
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNumReproductions(){
        $q = $this->createQueryBuilder('v');
        $q
            ->select('sum(v.reproductions)')
            ->where('v.deleted = 0')
        ;

        return $q->getQuery()->getSingleScalarResult();
    }

    public function search($data){
        $q = $this->createQueryBuilder('v');
        $q
            ->where('v.deleted = 0')
        ;

        if(!empty($data)){
            $q
                ->andWhere('v.title like :title')
                ->andWhere('v.description like :title')
//                ->andWhere($q->expr()->like('v.title', ':title'))
//                ->andWhere($q->expr()->like('v.description', ':title'))
                ->setParameter('title','%'.$data.'%')
            ;
        }

        return $q->getQuery()->getResult();
    }
}
