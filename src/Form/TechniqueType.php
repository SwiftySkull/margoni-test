<?php

namespace App\Form;

use App\Entity\Technique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TechniqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'label' => 'Nom de la technique :',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 40,
                    ]),
                ],
            ])
            // ->add('createdAt')
            // ->add('uptadetAt')
            // ->add('paintings')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Technique::class,
        ]);
    }
}
