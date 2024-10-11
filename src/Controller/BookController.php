<?php

namespace App\Controller;


use App\Entity\Book;
use App\Form\Type\BookType;
use App\Service\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/books', name: 'book_')]
class BookController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(BookService $bookService): Response
    {
        $books = $bookService->index();
        return $this->render('book/index.html.twig', compact('books'));
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, BookService $bookService): Response
    {
        $book = new Book();

        $form = $this->createForm(BookType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $bookService->save($form);
            $this->addFlash('success', "Книга {$book->getName()} добавлена");
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/create.html.twig', compact('form'));
    }
    
    #[Route('/{id}', name: 'show')]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', compact('book'));
    }

    
    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request, Book $book, BookService $bookService): Response
    {
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $bookService->save($form);            
            $this->addFlash('success', "Книга {$book->getName()} изменена");
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/edit.html.twig', compact('book', 'form'));
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST', 'HEAD'])]
    public function delete(Request $request, Book $book, BookService $bookService): Response
    {
        $token = $request->getPayload()->get('token');

        if ($this->isCsrfTokenValid('book_delete', $token)) {
            $bookService->remove($book);        
            $this->addFlash('success', "Книга {$book->getName()} удалена");
            return $this->redirectToRoute('book_index');
        }

        throw $this->createAccessDeniedException();
    }
}