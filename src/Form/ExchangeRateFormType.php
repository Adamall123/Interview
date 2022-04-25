<?php

namespace App\Form;

use App\Entity\ExchangeRate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Polyfill\Intl\Idn\Resources\unidata\Regex;

class ExchangeRateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startingCurrency', CurrencyType::class, [
                'label' => 'Mam:',
                'preferred_choices' => ['EUR', 'PLN', 'GBP', 'USD', 'CHF', 'SAR'],
            ])
            ->add('finalCurrency', CurrencyType::class, [
                'label' => 'Wymieniam na:',
                'preferred_choices' => ['PLN', 'EUR', 'GBP', 'USD', 'CHF', 'SAR'],
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Kwota',
                'currency' => '',
                'constraints' => array(
                    new \Symfony\Component\Validator\Constraints\NotBlank(['message' => 'Proszę wpisać ilość']),
                )
                
            ])
            ->add('save', SubmitType::class, ['label' => 'Sprawdź kurs']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExchangeRate::class,
        ]);
    }
}
