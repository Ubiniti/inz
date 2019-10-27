<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Video;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditVideoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Tytuł',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Opis',
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Kategoria',
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name'])
            ->add('isPublic', CheckboxType::class, [
                'label' => 'Publiczny',
                'required' => false,
            ]) ->add('allowsAds', CheckboxType::class, [
                'label' => 'Zezwól na wyświetlanie reklam',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Zapisz',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
