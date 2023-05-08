<?php


namespace App\Repository\Mongo;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\ODM\MongoDB\UnitOfWork;
use App\Document\Mongo\User;

class MessageRepository extends DocumentRepository
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
     * @param $myId
     * @param $messagesFrom
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    public function getAllMessages($myId, $messagesFrom)
    {
        return $this->createQueryBuilder()
            ->addOr([ 'from' => $myId, 'to' => $messagesFrom])
            ->addOr(['from'=> $messagesFrom, 'to' => $myId])
            ->sort(['createdAt' => 'desc'])
            ->limit(30);
    }

}