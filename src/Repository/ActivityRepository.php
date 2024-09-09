<?php

namespace App\Repository;

use App\DTO\searchActivityDTO;
use App\Entity\Activity;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{

    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Activity::class);
        $this->security = $security;
    }

    public function findByNameAndDate(searchActivityDTO $searchActivityDTO)
    {
        $queryBuilder = $this->createQueryBuilder('a');

        //Filtrer par campus
        if(!empty($searchActivityDTO->campus)){
            $queryBuilder->andWhere('a.campus = :campus');
            $queryBuilder->setParameter('campus', $searchActivityDTO->campus);
        }
        //Filtrer par recherche nom
        if(!empty($searchActivityDTO->activityName)){
            $queryBuilder->andWhere('a.activityName LIKE :searchName');
            $queryBuilder->setParameter('searchName', '%'. $searchActivityDTO->activityName.'%');
        }

        //Filtrer par date
        if (!empty($searchActivityDTO->filterDateMin)){
            $queryBuilder->andWhere('a.dateTimeStart>= :startdate');
            $queryBuilder->setParameter('startdate', $searchActivityDTO->filterDateMin);
        }

        if (!empty($searchActivityDTO->filterDateMax)){
            $queryBuilder->andWhere('a.dateLimitInscription<= :endDate');
            $queryBuilder->setParameter('endDate', $searchActivityDTO->filterDateMax);
        }

        //Filtrer par si je suis organisateur
        if ($searchActivityDTO->checkboxOrganizer === true){
            //Récupère l'utilisateur connecté
            $user = $this->security->getUser();
            if ($user instanceof User){
                $queryBuilder
                    ->innerJoin('a.organizer', 'uo')
                    ->andWhere('uo.id = :userId')
                    ->setParameter('userId', $user->getId());
            }
        }

        //Filtrer par si je suis inscrit
        if ($searchActivityDTO->checkBoxBooked === true){
            $user = $this->security->getUser();
                if($user instanceof User){
                    $queryBuilder
                        ->innerJoin('a.users', 'ub')
                        ->andWhere('ub = :user')
                        ->setParameter('user', $user);
                }

        }

        //Filtrer par si je ne suis pas inscrit
        if ($searchActivityDTO->checkBoxNotBooked === true){
            $user = $this->security->getUser();
            $subQuery = $this->createQueryBuilder('not_b')
                ->select('not_b.id')
                ->join('not_b.users', 'sub_u')
                ->where('sub_u.id = :userId')
                ->getDQL();

            $queryBuilder
                ->andWhere($queryBuilder->expr()->notIn('a.id', $subQuery))
                ->setParameter('userId', $user->getId());
        }

        //Filtrer des activités passées en fonction des dates
        if (!empty($searchActivityDTO->activityPassed)){
            $date = new \DateTime('now');
            $queryBuilder->andWhere('a.dateTimeStart < :date');
            $queryBuilder->setParameter('date',$date);
        }

        //Recup des résultats pour les retourner
        $query = $queryBuilder->getQuery();
        $results =$query->getResult();
        return $results;

    }

}


