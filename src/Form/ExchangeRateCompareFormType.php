<?php

namespace App\Form;

use App\Entity\Currency\ExchangeRateCurrency;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

class ExchangeRateCompareFormType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'fromExchangeRateCurrency',
                EntityType::class,
                [
                    'class' => ExchangeRateCurrency::class,
                    'multiple' => false,
                    'choice_label' => static function (ExchangeRateCurrency $exchangeRateCurrency): string {
                        return (string)$exchangeRateCurrency->getTargetCurrencyCode();
                    },
                    'label' => 'From exchange rate',
                ]
            )
            ->add(
                'amount',
                NumberType::class,
                [
                'required' => false,
                    'constraints' => [
                        new NotBlank(),
                        new Positive(),
                        new Type('float')
                    ],
                    'attr' => [
                        'min' => 1
                    ]
                ]
            )
            ->add(
                'toExchangeRateCurrencies',
                EntityType::class,
                [
                    'class' => ExchangeRateCurrency::class,
                    'multiple' => true,
                    'choice_label' => static function (ExchangeRateCurrency $exchangeRateCurrency): string {
                        return (string)$exchangeRateCurrency->getTargetCurrencyCode();
                    },
                    'label' => 'To exchange rate',
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Convert'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
