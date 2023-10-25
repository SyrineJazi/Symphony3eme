<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ref')
            ->add('title')
            ->add('genre', ChoiceType::class,[
                'choices' =>[
                    'Science-fiction' => 'Science-fiction',
                    'Autobiography' => 'Autobiography',
                    'Historique' => 'Historique',
                    'Fantasy' => 'Fantasy',
                    'Dystopian' => 'Dystopian',
                    'Educational' => 'Educational',
                ],
                ])

            ->add('published' ,CheckboxType::class, ['data'=> true , 'required' => false])
            ->add('author', EntityType::class, ['class'=>Author::class,'choice_label'=>'name','multiple' =>false, 'expanded'=>false, 'placeholder'=>'Select an author'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
