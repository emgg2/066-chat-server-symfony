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


    /**
     * @return \Doctrine\ODM\MongoDB\Iterator\Iterator|int|mixed[]|\MongoDB\DeleteResult|\MongoDB\InsertOneResult|\MongoDB\UpdateResult|object|null
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */

    public function getAll(){
        return
            $this->createQueryBuilder()
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

        if ( $this->createQueryBuilder()
            ->field('email')->equals($email)
            ->getQuery()
            ->getSingleResult())
        {
            return true;
        }
        return false;



    }

    /**
     * @param $email
     * @return User | null
     */
    public function getUserByEmail($email)
    {

        return $this->createQueryBuilder()
            ->field("email")->equals($email)
            ->getQuery()
            ->getSingleResult();

    }

    /**
     * @param $uid
     * @return User | null
     */
    public function getUserById($uid)
    {
        return $this->createQueryBuilder()
            ->field("_id")->equals(new \MongoId($uid))
            ->getQuery()
            ->getSingleResult();

    }

}