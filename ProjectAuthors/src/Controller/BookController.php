<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/book-list',name:'book-list')]
    public function book_list(BookRepository $book_repo){
        
        $books = $book_repo->findAll();
        return $this->render('book/list.html.twig',['books' => $books]);
    }
    
    #[Route('/add-book',name:'add-book')]
    public function add_book(ManagerRegistry $doctrine, Request $request):Response{
        $book = new Book();
        $em = $doctrine->getManager();

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            $em->persist($book);
            $author = $book->getAuthor();
            $author->setNbBooks(($author->getNbBooks())+1);
            $em->flush();
            return $this->redirectToRoute('book-list');
        }
        return $this->render('book/addBook.html.twig',['book_form'=> $form->createView()]);
    }

    #[Route('/edit-book/{id}',name:'edit-book')]
    public function edit_book($id,ManagerRegistry $doctrine, Request $request){
       
        $book_repo = $doctrine->getRepository(Book::class);
        $book = $book_repo->find($id);
        $em = $doctrine->getManager();

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('book-list');
        }
        return $this->render('book/editBook.html.twig',['book_form'=> $form->createView()]);
    }

    #[Route('/delete-book/{id}',name:'delete-book')]
    public function delete_book($id,ManagerRegistry $doctrine){

        $book_repo = $doctrine->getRepository(Book::class);
        $em = $doctrine->getManager();
        $book = $book_repo->find($id);

        $em->remove($book);
        $author = $book->getAuthor();
        $author->setNbBooks(($author->getNbBooks())-1);
        $em->flush();

        return $this->redirectToRoute('book-list');
    }


}
