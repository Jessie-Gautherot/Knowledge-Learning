<?php

namespace App\Form;

use App\Entity\Cursus;
use App\Entity\Lesson;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form used to create and edit a Lesson.
 */
class LessonType extends AbstractType
{
    /**
     * Build the lesson form fields.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la leçon',
            ])

            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
            ])

            ->add('videoUrl', TextType::class, [
                'label' => 'URL de la vidéo',
                'required' => false,
            ])

            ->add('price', IntegerType::class, [
                'label' => 'Prix en centimes',
            ])

            ->add('cursus', EntityType::class, [
                'class' => Cursus::class,
                'choice_label' => 'title',
                'label' => 'Cursus',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}