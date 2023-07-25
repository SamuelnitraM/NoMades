<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'attr' => [
                    'class' => 'form-control '
                ]
            ])
            ->add('description', ChoiceType::class, [
                'label' => 'Description',
                'multiple' => true,
                'expanded' => true,
                'choices' =>[
                    'HTML' => 'HTML',
                    'CSS' => 'CSS',
                    'JS' => 'JS',
                    'PHP' => 'PHP',
                    'Symfony' => 'Symfony',
                    'Laravel' => 'Laravel',
                    'Bootstrap' => 'Bootstrap',
                ],
                'constraints' => [
                    new Callback([
                        'callback' => function($value, ExecutionContextInterface $context) {
                            if (count($value) > 4) {
                                $context->buildViolation('You can select a maximum of 4 options.')
                                    ->addViolation();
                            }
                        }
                    ])
                ]    
            ])
            ->add('body', TextareaType::class, [
                'label' => 'Body',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
