<?php


namespace App\Repository\Mongo;


use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\ODM\MongoDB\UnitOfWork;
use App\Document\Mongo\User;


class UserRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm, UnitOfWork $uow, ClassMetadata $classMetadata)
    {
        $uow = $dm->getUnitOfWork();
        $classMetadata = $dm->getClassMetadata(User::class);
        parent::__construct($dm, $uow, $classMetadata);
    }

    public function getAll(){
        return
            $this->createQueryBuilder('User')
                ->sort('createdAt', 'desc')
                ->getQuery()
                ->execute();

    }
    /**
     * @param $field
     * @param $data
     *
     * @return bool
     */


    public function isEmailExists($email):bool
    {

        if ( $this->createQueryBuilder('User')
            ->field('email')->equals($email)
            ->getQuery()
            ->getSingleResult())
        {
            return true;
        }
        return false;



    }


}