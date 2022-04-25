<?php

namespace App\Form;

use App\Entity\ExchangeRate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExchangeRatesFormDatesScopeType extends ExchangeRateFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'label' => 'Data startowa',
                'widget' => 'single_text'
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Data końcowa',
                'widget' => 'single_text'
            ]);

        parent::buildForm($builder, $options);

        $builder 
        ->add('save', SubmitType::class, ['label' => 'Sprawdź różnice'])
        ->remove('amount', MoneyType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExchangeRate::class,
        ]);
    }
}
