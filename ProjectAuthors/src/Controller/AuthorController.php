<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    
    #[Route('/author-list',name:'author-list')]
    public function author_list(AuthorRepository $repo){
        $authors=$repo->findAll();
        return $this->render('author/list.html.twig',['authors'=>$authors]);
    }

    #[Route('/add-author', name:'add-author')]
    public function add_author(ManagerRegistry $doctrine, Request $request):Response{

        $author = new Author();
        $author->setNbBooks(0);
        $em = $doctrine->getManager();

        /*
            ----    NOTES ---
            we create a form of type Author that was created in the terminal
            we associate the form to the object author
        */
        $form = $this->createForm(AuthorType::class,$author);
        //$form->add('Submit',SubmitType::class);
        $form->handleRequest($request);

        /*
        $author->setName("Victor Hugo");
        $author->setCountry("France");
        $author->setAge("83");
        */

        /*
            ----    NOTES ---
            persist() --> tells doctrine to track the changes made to this entity from 
            here on out, necessary before saving an entity in a database

            flush() --> doctrine will take all the changes made to the managed entities
            and COMMIT them to the database
        */

        if($form->isSubmitted()){
        // Handle the image upload
        $image = $form->get('image')->getData();

            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where you want to store it
                $image->move(
                    $this->getParameter('kernel.project_dir').'/public/images',
                    $newFilename
                );

                // Save the file path to the entity
                $author->setImage($newFilename);
            }

        $em->persist($author);
        $em->flush();
        return $this->redirectToRoute('author-list');//name of the route not the path of the route
        }

        return $this->render('author/addAuthor.html.twig',['author_form'=>$form->createView()]);
    }


    #[Route('/edit-author/{id}', name:'edit-author')]
    public function edit_author($id, ManagerRegistry $doctrine, Request $request){
        
        $em = $doctrine->getManager();
        $author_repo= $doctrine->getRepository(Author::class);
        $author = $author_repo->find($id);

        $form = $this->createForm(AuthorType::class,$author);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            
                $newImage = $form->get('image')->getData();

                if ($newImage) {
                    $newFilename = uniqid().'.'.$newImage->guessExtension();

                    // Move the file to the directory where you want to store it
                        $newImage->move(
                        $this->getParameter('kernel.project_dir').'/public/images',
                        $newFilename
                    );

                    // Save the file path to the entity
                    $author->setImage($newFilename);
                }
                
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('author-list');
        }

        return $this->render('author/editAuthor.html.twig',['author_form'=>$form->createView()]);
    }

    #[Route('/delete-author/{id}',name:'delete-author')]
    public function delete_author($id,ManagerRegistry $doctrine){

        $author_repo = $doctrine->getRepository(Author::class);
        $author = $author_repo->find($id);
        $em = $doctrine->getManager();

        $em->remove($author);
        $em->flush();

        return $this->redirectToRoute('author-list');
    }

    #[Route('/author-details/{name}',name:'author-details')]
    public function author_details($name,ManagerRegistry $doctrine){
        
        $author_repo = $doctrine->getRepository(Author::class);
        $author = $author_repo->findOneBy(['name'=> $name]);

        return $this->render('author/authorDetails.html.twig',['author'=>$author]);
    }









}
