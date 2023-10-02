<?php

namespace ChronopostHomeDelivery\Form;


use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryDeliveryModeQuery;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\LangQuery;

class ChronopostHomeDeliveryConfigurationForm extends BaseForm
{
    protected function buildForm(): void
    {
        $config = ChronopostHomeDeliveryConst::getConfig();

        $this->formBuilder

            /** Chronopost basic information */
            ->add(
                ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT,
                TextType::class,
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
                PasswordType::class,
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

        $lang = $this->getRequest()->getSession()->get('thelia.current.admin_lang');
        if (null === $lang) {
            $lang = LangQuery::create()
                ->filterByByDefault(1)
                ->findOne();
        }
        /** Delivery types */
        foreach (ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys() as $deliveryTypeName => $statusKey) {
            $deliveryMode = ChronopostHomeDeliveryDeliveryModeQuery::create()
                ->filterByCode(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES[$deliveryTypeName])
                ->findOne();
            $deliveryModeTitle = $deliveryMode ? $deliveryMode->setLocale($lang->getLocale())->getTitle() : $deliveryTypeName;
            $this->formBuilder
                ->add($statusKey,
                    CheckboxType::class,
                    [
                        'required'      => false,
                        'data'          => (bool)$config[$statusKey],
                        'label'         => Translator::getInstance()->trans("\"" . $deliveryModeTitle . "\" Delivery (Code : " . ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_DELIVERY_CODES[$deliveryTypeName] . ")"),
                        'label_attr'    => [
                            'for'           => 'title',
                        ],
                    ]
                )
            ;
        }

        /** BUILD FORM END */
    }

    public static function getName(): string
    {
        return "chronopost_home_delivery_configuration_form";
    }
}