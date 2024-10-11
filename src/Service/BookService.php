<?php

namespace App\Service;


use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\BookRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class BookService
{
    public function __construct(
        private EntityManagerInterface $entityManager, 
        private BookRepository $bookRepository,
        #[Autowire('%kernel.project_dir%')] private string $projectDir) 
    {}

    public function index(): array
    {
        return $this->bookRepository->findAll();
    }

    public function save(FormInterface $form): Book
    {
        $book = $form->getData();
        $mainImageFile = $form->get('main_image')->getData();

        if ($mainImageFile) {
            $this->removeMainImageIfExist($book);

            $fileName = $this->saveFile($mainImageFile);
            $book->setMainImage($fileName);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book;
    }

    public function remove(Book $book): void
    {
        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }

    private function saveFile(UploadedFile $mainImageFile): string
    {
        $uploadDir = "{$this->projectDir}/public/uploads/books/";
        $fileName = uniqid() . '.' . $mainImageFile->guessExtension();

        $mainImageFile->move($uploadDir, $fileName);
        return "/uploads/books/$fileName";
    }

    private function removeMainImageIfExist(Book $book): void
    {
        if ($book->getMainImage()) {
            $path_file = "{$this->projectDir}/public" . $book->getMainImage();    
            if (file_exists($path_file)) {
                unlink($path_file);
            }
        }
    }
}


