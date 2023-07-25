<?php

namespace App\Controller\Admin;

use App\Entity\AnswerPost;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class AnswerPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AnswerPost::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('post')
            ->formatValue(function ($value, $entity) {
                return $entity->getPost()->getTitle();
            }),
            IdField::new('id_author'),
            TextareaField::new('body'),
            DateTimeField::new('created_at')
            ->hideWhenUpdating()
            ->setFormat('dd/MM/yyyy HH:mm:ss')
            ->setFormTypeOption('data', new \DateTimeImmutable())
        ];
    }

}
