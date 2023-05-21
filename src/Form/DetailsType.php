<?php

namespace App\Form;

use App\Entity\Details;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class DetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ico', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'IČO musí obsahovat přesně 8 čísel',
                    ]),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'IČO musí obsahovat pouze čísla',
                    ]),
                ],
            ])
            ->add('dico', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 15,
                        'minMessage' => 'DIČ musí mít nejméně 3 znaky',
                        'maxMessage' => 'DIČ musí mít nejvíce 15 znaků',
                    ]),
                    new Regex([
                        'pattern' => '/^[A-Za-z]{2}\d+$/',
                        'message' => 'DIČ musí mít formát dvou písmen a následujících čísel',
                    ]),
                ],
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Zadejte název',
                    ])
                ]
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Zadejte adresu',
                    ])
                ]
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Zadejte město',
                    ])
                ]
            ])
            ->add('zipCode', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Zadejte PSČ',
                    ])
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\+?\d+$/',
                        'message' => 'Telefonní číslo může obsahovat pouze číslice a může začínat "+"',
                    ])
                ]
            ]);
    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Details::class,
        ]);
    }
}
