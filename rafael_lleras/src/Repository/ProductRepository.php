<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\ConsoleOutput;
use Psr\Log\LoggerInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
    
    public function getAllProducts($order_by = 'name', $page = null): array {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT 
                p.id AS id, 
                p.code AS code, 
                p.name AS name, 
                p.description AS description, 
                p.brand AS brand, 
                p.price AS price, 
                p.category_id AS category_id, 
                c.name AS category_name 
            FROM 
                product AS p, 
                category AS c 
            WHERE 
                p.category_id = c.id  
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
                product AS p
            WHERE 
                p.name = ? OR p.code = ?
        ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $code);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function deleteProduct($id) {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            DELETE FROM 
                product  
            WHERE 
                id = ?
        ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
    }

    public function filterProducts($product = null): array {
        $code = "%" . $product->getCode() . "%";
        $name = "%" . $product->getName() . "%";
        $description = "%" . $product->getDescription() . "%";
        $brand = "%" . $product->getBrand() . "%";
        $price = $product->getPrice();
        $category = $product->getCategory();
        if($price == null) {
            $price = 0;
        }
        $category_id = 0;
        if($category != null) {
            $category_id = $category->getId();
        }
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT 
                p.id AS id, 
                p.code AS code, 
                p.name AS name, 
                p.description AS description, 
                p.brand AS brand, 
                p.price AS price, 
                p.category_id AS category_id, 
                c.name AS category_name 
            FROM 
                product AS p, 
                category AS c 
            WHERE 
                p.code LIKE ? AND 
                p.name LIKE ? AND 
                p.description LIKE ? AND 
                p.brand LIKE ? AND 
                IF(? = 0, p.price > 0, p.price = ?) AND 
                IF(? = 0, p.category_id = c.id, p.category_id = c.id AND p.category_id = ?) 
            ORDER BY p.name
        ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $code);
        $stmt->bindValue(2, $name);
        $stmt->bindValue(3, $description);
        $stmt->bindValue(4, $brand);
        $stmt->bindValue(5, $price);
        $stmt->bindValue(6, $price);
        $stmt->bindValue(7, $category_id);
        $stmt->bindValue(8, $category_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
