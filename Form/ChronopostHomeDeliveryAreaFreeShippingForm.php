<?php

namespace ChronopostHomeDelivery\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints;

class ChronopostHomeDeliveryAreaFreeShippingForm extends ChronopostHomeDeliveryAddPriceForm
{
    /**
     * @return null|void
     */
    protected function buildForm()
    {
        parent::buildForm();
        $this->formBuilder
            ->remove('price')
            ->remove('weight')
            ->remove('franco')
            ->remove('delivery_mode')
            ->add(
                'delivery_mode_id',
                HiddenType::class
            )
            ->add('cart_amount', NumberType::class, array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(
                        array($this, 'verifyValidPrice')
                    )
                )
            ));
        ;
    }

    /**
     * The name of you form. This name must be unique
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'chronopost_home_delivery_area_freeshipping';
    }

}
