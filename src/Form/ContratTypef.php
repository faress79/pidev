<?php

namespace App\Form;

use App\Entity\Contrat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ContratTypef extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prixContrat')
            ->add('dateEffet', null, [
                'widget' => 'single_text',
                'data' => $options['is_edit'] ? $options['data']->getDateDepartPack() : new \DateTime(),
            ])
            ->add('typeContrat', ChoiceType::class, [
                'choices' => [
                    'Mensuel' => 'Mensuel',
                    'Semestriel' => 'Semestriel',
                    'Annuel' => 'Annuel',
                ],
                'placeholder' => 'Choose an option', // Optional placeholder
                // Other options you may want to add:
                // 'expanded' => true, // Renders as radio buttons
                // 'multiple' => false, // Allows multiple selections
            ])
            ->add('utilisateur');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
            'is_edit' => false,

        ]);
    }
}
