<?php

namespace ChronopostHomeDelivery\Form;


use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class ChronopostHomeDeliveryConfigurationForm extends BaseForm
{
    protected function buildForm()
    {
        $config = ChronopostHomeDeliveryConst::getConfig();

        $this->formBuilder

            /** Chronopost basic informations */
            ->add(
                ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT],
                    'label'         => Translator::getInstance()->trans("Chronopost client ID"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("Your Chronopost client ID"),
                    ],
                ]
            )
            ->add(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PASSWORD,
                "text",
                [
                    'required'      => true,
                    'data'          => $config[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PASSWORD],
                    'label'         => Translator::getInstance()->trans("Chronopost password"),
                    'label_attr'    => [
                        'for'           => 'title',
                    ],
                    'attr'          => [
                        'placeholder'   => Translator::getInstance()->trans("Your Chronopost password"),
                    ],
                ]
            )
        ;

        /** Delivery types */
        foreach (ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys() as $deliveryTypeName => $statusKey) {
            $this->formBuilder
                ->add($statusKey,
                    "checkbox",
                    [
                        'required'      => false,
                        'data'          => (bool)$config[$statusKey],
                        'label'         => Translator::getInstance()->trans("\"" . $deliveryTypeName . "\" Delivery (Code : " . ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES[$deliveryTypeName] . ")"),
                        'label_attr'    => [
                            'for'           => 'title',
                        ],
                    ]
                )
            ;
        }

        /** BUILDFORM END */
    }

    public function getName()
    {
        return "chronopost_home_delivery_configuration_form";
    }
}