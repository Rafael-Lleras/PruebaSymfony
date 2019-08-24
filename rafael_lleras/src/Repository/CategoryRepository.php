<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\ConsoleOutput;
use Psr\Log\LoggerInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }
    
    public function getAllCategories($order_by = 'name', $page = null): array {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT 
                * 
            FROM 
                category  
            ORDER BY 
        ' . $order_by;
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function nameAndCodeAreUniques($name, $code): array {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT 
                * 
            FROM 
                category AS c
            WHERE 
                c.name = ? OR c.code = ?
        ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $code);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
