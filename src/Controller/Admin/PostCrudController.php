<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {

        $descriptionField = ChoiceField::new('description')
        ->setChoices([
            'HTML' => 'HTML',
            'CSS' => 'CSS',
            'JS' => 'JS',
            'Bootstrap' => 'Boostrap',
            'PHP' => 'PHP',
            'Symfony' => 'Symfony',
            'Laravel' => 'Laravel'
        ])
        ->allowMultipleChoices(true)
        ->autocomplete(true)
        ->renderExpanded(true)
        ->setFormTypeOptions(['multiple' => true, 'attr' => ['data-max-options' => 4]]);

        $idAuthorField = IdField::new('id_author')
        ->setFormTypeOption('disabled', true)
        ->setCustomOption('value', $this->getUser()->getId());


        return [
            $idAuthorField,
            TextField::new('title'),
            TextareaField::new('body'),
            $descriptionField,
        ];
    }

}
