<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Repository\BookRepository;
use App\Repository\CartRepository;

use App\Entity\Book;
use App\Repository\GenraRepository;
use App\Repository\AuthorRepository;
use Doctrine\Common\Persistence\ObjectManager;

class VesoulEditionController extends AbstractController
{

    /**
     * @var integer
     */
    public $quantity;

    
    /**
     * @Route("/", name="home")
     */
    public function home(SessionInterface $session, BookRepository $repoBook, GenraRepository $repoGenra, AuthorRepository $repoAuthor)
    {
        // $session->remove('panier');

        // Si le panier est bien existant, capte le, et compte le nombre d'articles contenu.
        if($session->get('panier')) {

            $panier = $session->get('panier');

        } else { // Sinon crée le et initialise à 0 le nombre d'articles contenu.
            $session->set('panier', []);
        }
    
        $books = $repoBook->findAllBooksByAscName();
        $genras = $repoGenra->findAll();
        $authors = $repoAuthor->findAll();


        return $this->render('vesoul-edition/home.html.twig', [
            'books' => $books,
            'genras' => $genras,
            'authors' => $authors
        ]);
    }

    /**
    * @Route("/ascName", name="sortByAscName")
    *
    * @param \App\Repository\BookRepository
    */
    public function sortByAscName(BookRepository $repo) : Response
    {
        $books = $repo->findAllBooksByAscName();
        $arrayBooks = [];
        $data = [];

        foreach($books as $book){
            $arrayBooks[] = $this->render('ajax/book.html.twig', ['book' => $book]);
            $data[] = $arrayBooks[0]->getContent();
        }

        // dump($data);
        // $header =

        $json = new JsonResponse($data, 200);

        return $json;
    }

    /**
     * 
     * 
    * @Route("/descName", name="sortByDescName")
    */
    public function sortByDescName(BookRepository $repo) : Response
    {
        $books = $repo->findAllBooksByDescName();
        $arrayBooks = [];
        $data = [];

        foreach($books as $book){
            $arrayBooks[] = $this->render('ajax/book.html.twig', ['book' => $book]);
            $data[] = $arrayBooks[0]->getContent();
        }

        // dump($data);
        // $header =

        $json = new JsonResponse($data, 200);

        return $json;
       
    }

    /**
    * @Route("/ascYear", name="sortByAscYear")
    */
    public function sortByAscYear(BookRepository $repo) : Response
    {
        $books = $repo->findAllBooksByAscYear();
        $arrayBooks = [];
        $data = [];

        foreach($books as $book){
            $arrayBooks[] = $this->render('ajax/book.html.twig', ['book' => $book]);
            $data[] = $arrayBooks[0]->getContent();
        }

        // dump($data);
        // $header =

        $json = new JsonResponse($data, 200);

        return $json;
    }

    /**
    * @Route("/descYear", name="sortByDescYear")
    */
    public function sortByDescYear(BookRepository $repo) : Response
    {
        $books = $repo->findAllBooksByDescYear();
        $arrayBooks = [];
        $data = [];

        foreach($books as $book){
            $arrayBooks[] = $this->render('ajax/book.html.twig', ['book' => $book]);
            $data[] = $arrayBooks[0]->getContent();
        }

        // dump($data);
        // $header =

        $json = new JsonResponse($data, 200);

        return $json;
        
    }


    /**
     * @Route("/panier/add/{id}", name="addItem")
     */
    public function addItem(Book $book, SessionInterface $session, ObjectManager $manager)
    {
        $id = $book->getId();
        $title = $book->getTitle();
        $author = $book->getAuthor();
        $price = $book->getPrice();
        $stock = $book->getStock();

        if ($stock > 0) {

            $this->quantity++;
            $book->setStock($stock - 1);
            $panier = $session->get('panier');
            
            $manager->persist($book);
            $manager->flush();
                   
            if (array_key_exists($id, $panier)) {

                $panier[$id]['quantity']++;

            } else {
                
                array_push($panier, $id = [
                    'title'=> $title,
                    'firstname'=> $author->getFirstname(),
                    'lastname'=> $author->getLastname(),
                    'quantity'=> $this->quantity,
                    'price'=> $price                
                ]);
            }

            $session->set('panier', $panier);
            $panier = $session->get('panier');

            return $this->redirectToRoute('home');
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/product/{id}", name="product")
     */
    public function showProduct($id, BookRepository $repo)
    {
        $book = $repo->findBook($id);

        return $this->render('vesoul-edition/product.html.twig', [
            'book' => $book
        ]);
    }

    /**
     * @Route("/panier", name="panier")
     */
    public function showPanier(SessionInterface $session)
    {

        return $this->render('vesoul-edition/panier.html.twig', [
            'controller_name' => 'FrontController'
        ]);
    }

    /**
     * @Route("/commande", name="commander")
     */
    public function showCommande(SessionInterface $session)
    {
        return $this->render('vesoul-edition/commande.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

    /**
     * @Route("/confirmation", name="commander")
     */
    public function showConfirmation()
    {
        return $this->render('vesoul-edition/confirmation.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

}
// lol