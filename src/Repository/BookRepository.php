<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{   

    public const LIMIT = 9;


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function countBooks(){
        
        return $this->createQueryBuilder('b')
        ->select('count(b.id) as count')
        ->getQuery()
        ->getSingleScalarResult();
        
    }

    public function findAllBooksByAscName(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = ' 
            SELECT book.id, book.price, book.title, book.stock, book.year, author.firstname, author.lastname, image.url, genra.name AS genre
            FROM book
            INNER JOIN  author ON book.author_id = author.id
            INNER JOIN image ON book.id = image.book_id
            INNER jOIN book_genra ON book.id = book_genra.book_id
            INNER JOIN genra ON book_genra.genra_id = genra.id
            ORDER BY book.title
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    
        // returns an array of book
        return $stmt->fetchAll();
    }

    public function findAllBooksByDescName(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = ' 
            SELECT book.id, book.price, book.title, book.stock, book.year, author.firstname, author.lastname, image.url, genra.name AS genre
            FROM book
            INNER JOIN  author ON book.author_id = author.id
            INNER JOIN image ON book.id = image.book_id
            INNER jOIN book_genra ON book.id = book_genra.book_id
            INNER JOIN genra ON book_genra.genra_id = genra.id
            ORDER BY book.title DESC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    
        // returns an array of book
        return $stmt->fetchAll();
    }

    public function findAllBooksByAscYear(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = ' 
            SELECT book.id, book.price, book.title, book.stock, book.year, author.firstname, author.lastname, image.url, genra.name AS genre
            FROM book
            INNER JOIN  author ON book.author_id = author.id
            INNER JOIN image ON book.id = image.book_id
            INNER jOIN book_genra ON book.id = book_genra.book_id
            INNER JOIN genra ON book_genra.genra_id = genra.id
            ORDER BY book.year
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    
        // returns an array of book
        return $stmt->fetchAll();
    }

    public function findAllBooksByDescYear(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = ' 
            SELECT book.id, book.price, book.title, book.stock, book.year, author.firstname, author.lastname, image.url, genra.name AS genre
            FROM book
            INNER JOIN  author ON book.author_id = author.id
            INNER JOIN image ON book.id = image.book_id
            INNER jOIN book_genra ON book.id = book_genra.book_id
            INNER JOIN genra ON book_genra.genra_id = genra.id
            ORDER BY book.year DESC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    
        // returns an array of book
        return $stmt->fetchAll();
    }

    public function findBook($id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = ' 
            SELECT book.description, book.price, book.isbn, book.title, book.stock, book.year, book.length, book.width, author.firstname, author.lastname, genra.name AS genre, image.url
            FROM book
            INNER JOIN  author ON book.author_id = author.id
            INNER jOIN book_genra ON book.id = book_genra.book_id
            INNER JOIN genra ON book_genra.genra_id = genra.id
            INNER JOIN image ON book.id = image.book_id
            WHERE book.id = :book_id
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['book_id' => $id]);
    
        // returns an array of book
        return $stmt->fetchAll();
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findPageOfListBook($offset) {

        $qb = $this->createQueryBuilder('b');

        return $qb->select(['book', 'author', 'images'])
        ->from(Book::class, 'book')
        ->innerJoin('book.author', 'author')
        ->innerJoin('book.images', 'images')
        ->setFirstResult( $offset )
        ->setMaxResults( self::LIMIT )
        ->getQuery()
        ->getArrayResult();
    }
}
