<?php

namespace App\Form;

use App\Dto\VideoUploadFormDto;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddVideoType extends AbstractType
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
            ->add('file', FileType::class, [
                'label' => 'Wideo',
            ])
            ->add('thumbnail', FileType::class, [
                'label' => 'Thumnbail',
                'required' => false,
            ])
            ->add('price', NumberType::class, [
                'label' => 'Cena',
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Kategoria',
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name'])
            ->add('hasDemo', CheckboxType::class, [
                'label' => 'Utwórz wersję demo filmu',
                'required' => false,
            ])
            ->add('isPublic', CheckboxType::class, [
                'label' => 'Widoczny na stronie głównej',
                'required' => false,
            ])
            ->add('allowsAds', CheckboxType::class, [
                'label' => 'Zezwól na wyświetlanie reklam',
                'required' => false,
            ])
            ->add('watermark', CheckboxType::class, [
                'label' => 'Dodaj znak wodny do filmu',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Zapisz',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VideoUploadFormDto::class,
        ]);
    }
}
