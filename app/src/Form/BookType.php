<?php

namespace App\Form;

use App\Entity\Book;
use App\Services\CategoryService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BookType extends AbstractType
{
    public function __construct(
        private CategoryService $categoryService
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tree = $this->categoryService->findAllAsNestedTree();
        $choices = $this->categoryService->flattenTree($tree);

        $builder
            ->add('title')
            ->add('author')
            ->add('publishedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('description', TextareaType::class, [
                'trim' => true,
                'attr' => [
                    'rows' => 10,
                    'maxlength' => 4000,
                ]
            ])
            ->add('isbn')
            ->add('category', ChoiceType::class, [
                'choices' => $choices,
                'placeholder' => 'Choose category',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
