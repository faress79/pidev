<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\Expression;


class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_event')
            ->add('date_event', DateTimeType::class, [
                'widget' => 'single_text',
                
            ])
            ->add('adress_event')
            ->add('description')
            ->add('image', FileType::class, [
                'label' => 'image (PNG file) ',
                'mapped' => true,
                
                
                'constraints' => [
                    new File([
                        'maxSize' => '51200k',
                        'mimeTypes' => [
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PNG file'
                    ])
                ],
            ])
        ;
        $builder->get('image')->addModelTransformer(new EventImageTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
