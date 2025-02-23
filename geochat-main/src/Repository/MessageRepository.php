<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function save(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve les messages proches d'une longitude/latitude donnée en paramètre
     *
     * @param  float $longitude 
     * @param  float $latitude
     * @param  float $radius la distance seuil pour filtrer les résultats
     */
    public function findClose(float $longitude, float $latitude, float $radius): QueryBuilder
    {
        return $this->createQueryBuilder('m')
            ->addSelect('stdistance_sphere(point(m.longitude, m.latitude), point(:lng, :lat)) AS dist')
            ->having('dist < :radius')
            ->setParameter('lng', $longitude)
            ->setParameter('lat', $latitude)
            ->setParameter('radius', $radius);
    }
}
