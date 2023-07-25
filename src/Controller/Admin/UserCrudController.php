<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $passwordField = TextField::new('password')
            ->setFormType(PasswordType::class)
            ->onlyOnForms();

        if ($pageName === Crud::PAGE_EDIT){
            $passwordField
            ->setLabel('Password (Ne pas modifier)');
        }

        if ($pageName === Crud::PAGE_NEW) {
            $passwordField->setFormTypeOption('empty_data', '');
        } else {
            $passwordField->setRequired(false);
        }

        $rolesField = ChoiceField::new('roles')
            ->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',
                'Super Administrateur' => 'ROLE_SUPERADMIN',
            ])
            ->allowMultipleChoices()
            ->setRequired(true);

        if (!$this->isGranted('ROLE_SUPERADMIN')) {
            $rolesField
            ->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',]);
        }

        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('username'),
            EmailField::new('email'),
            $passwordField,
            $rolesField,
            DateTimeField::new('created_at')
                ->hideWhenUpdating()
                ->setFormat('dd/MM/yyyy HH:mm:ss')
                ->setFormTypeOption('data', new \DateTimeImmutable())
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function encodePassword(User $user)
    {
        $plainPassword = $user->getPassword();

        if (!$plainPassword) {
            return;
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
    }
}
