<?php

namespace App\Form;

use App\Entity\Size;
use App\Entity\Frame;
use App\Entity\Picture;
use App\Entity\Category;
use App\Entity\Painting;
use App\Entity\Situation;
use App\Entity\Technique;
use App\Repository\FrameRepository;
use Symfony\Component\Form\FormEvent;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\FormEvents;
use App\Repository\SituationRepository;
use App\Repository\TechniqueRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PaintingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $painting = $event->getData();

                $form = $event->getForm();
                if (null === $painting->getId()) {
                    $form->add('picture', FileType::class, [
                        'label' => 'Fichier de la photo (Obligatoire)',
                        'mapped' => false,
                        'constraints' => [
                            new File([
                                'mimeTypes' => [
                                    'image/png',
                                    'image/jpeg',
                                ],
                                'mimeTypesMessage' => 'Le fichier n\'est pas au bon format (.png, .jpg, .jpeg)',
                                'maxSize' => 2000000,
                                'maxSizeMessage' => 'Le fichier est trop volumineux et ne doit pas faire plus que 2Mo.',
                            ]),
                            new NotBlank(),
                        ],
                    ]);
                } else {
                    $form->add('picture', FileType::class, [
                        'label' => 'Fichier de la photo',
                        'mapped' => false,
                        'constraints' => [
                            new File([
                                'mimeTypes' => [
                                    'image/png',
                                    'image/jpeg',
                                ],
                                'mimeTypesMessage' => 'Le fichier n\'est pas au bon format (.png, .jpg, .jpeg)',
                                'maxSize' => 2000000,
                                'maxSizeMessage' => 'Le fichier est trop volumineux et ne doit pas faire plus que 2Mo.',
                            ]),
                        ],
                    ]);
                }
            })
            ->add('title', TextType::class, [
                'label' => 'Titre de la peinture',
            ])
            ->add('dbName', TextType::class, [
                'label' => 'Nom générique',
                'constraints' => [
                    new Length([
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('date', IntegerType::class, [
                'label' => 'Année de la peinture',
                'help' => 'Le jour sera spécifié dans les informations',
            ])
            ->add('height', IntegerType::class, [
                'label' => 'Hauteur de la peinture (en cm)',
            ])
            ->add('width', IntegerType::class, [
                'label' => 'Largeur de la peinture (en cm)',
            ])
            ->add('size', EntityType::class, [
                'class' => Size::class,
                'label' => 'Format',
                'choice_label' => 'format',
                'placeholder' => 'Le tableau est au format...'
            ])
            ->add('location', TextareaType::class, [
                'label' => 'Localisation/Adresse actuelle de la peinture',
            ])
            ->add('information', TextareaType::class, [
                'label' => 'Informations complémentaires, histoire de la peinture',
            ])
            ->add('frame', EntityType::class, [
                'class' => Frame::class,
                'query_builder' => function (FrameRepository $fr) {
                    return $fr->createQueryBuilder('f')
                        ->orderBy('f.framing', 'ASC');
                },
                'label' => 'Encadrement',
                'choice_label' => 'framing',
                'placeholder' => 'Le tableau est actuellement...',
            ])
            ->add('situation', EntityType::class, [
                'class' => Situation::class,
                'query_builder' => function (SituationRepository $sr) {
                    return $sr->createQueryBuilder('s')
                        ->orderBy('s.collection', 'ASC');
                },
                'label' => 'Collection',
                'choice_label' => 'collection',
                'placeholder' => 'Le tableau se trouve actuellement...'
            ])
            ->add('technique', EntityType::class, [
                'class' => Technique::class,
                'query_builder' => function (TechniqueRepository $tr) {
                    return $tr->createQueryBuilder('t')
                        ->orderBy('t.type', 'ASC');
                },
                'label' => 'Les techniques utilisées sont :',
                'choice_label' => 'type',
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'label' => 'Ce qui décrit le tableau',
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
            ])
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('picture', FileType::class, [
            //     'label' => 'Fichier de la photo',
            //     'mapped' => false,
            //     'constraints' => [
            //         new File([
            //             'mimeTypes' => [
            //                 'image/png',
            //                 'image/jpeg',
            //             ],
            //             'mimeTypesMessage' => 'Le fichier n\'est pas au bon format (.png, .jpg, .jpeg)',
            //             'maxSize' => 2000000,
            //             'maxSizeMessage' => 'Le fichier est trop volumineux et ne doit pas faire plus que 2Mo.',
            //         ]),
            //     ],
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Painting::class,
        ]);
    }
}
