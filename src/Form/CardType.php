<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                    new Assert\PositiveOrZero()
                ]
            ])
            ->add('currency', ChoiceType::class, [
                'choices' => [
                    'USD' => 'USD',
                    'EUR' => 'EUR'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice(['USD', 'EUR']),
                    new Assert\Length(['min' => 3, 'max' => 3])
                ]
            ])
            ->add('card', FormType::class, [])
            ->get('card')
            ->add('number', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 16, 'max' => 16]),
                    new Assert\Type('numeric')
                ]
            ])
            ->add('expMonth', ChoiceType::class, [
                'choices' => array_combine(range(1, 12), range(1, 12)),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice(range(1, 12))
                ]
            ])
            ->add('expYear', ChoiceType::class, [
                'choices' => array_combine(range(date('Y'), date('Y') + 10), range(date('Y'), date('Y') + 10)),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Callback(function($value, $context) {
                        if ($value == 2023 && $context->getRoot()->get('card')->get('expMonth')->getData() < 10) {
                            $context->buildViolation('For the year 2023, the month should be between 10 and 12.')
                                ->atPath('card.expMonth')
                                ->addViolation();
                        }
                        if ($value < 2023) {
                            $context->buildViolation('Year should be 2023 or later.')
                                ->atPath('card.expYear')
                                ->addViolation();
                        }
                    })
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CardData::class,
        ]);
    }
}
