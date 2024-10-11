<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use App\Entity\Book;
use Symfony\Component\Validator\Constraints\NotNull;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void 
    {
        /** @var Book|null $book */
        $book = $options['data'] ?? null;
        $isEdit = $book && $book->getId();

        $imageConstraints = [new Image(['maxSize' => '5M'])];
        if (!$isEdit) {
            $imageConstraints[] = new NotNull(['message' => 'Please upload an image']);
        }

        $builder
            ->add('name', TextType::class, ['label' => 'Название', 'constraints' => [new Length(['min' => 3])]])
            ->add('year', NumberType::class, ['label' => 'Год выпуска', 'html5' => true, 'constraints' => [new Length(4), new LessThanOrEqual(date('Y'))]])
            ->add('description', TextareaType::class, ['label' => 'Описание', 'required' => false])
            ->add('main_image', FileType::class, [
                'label' => 'Главное изображение',     
                'required' => false,
                'mapped' => false,
                'attr' => ['accept' => 'image/*'], 
                'constraints' => $imageConstraints
            ])
            ->add('save', SubmitType::class, ['label' => 'Сохранить']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class
        ]);
    }
}