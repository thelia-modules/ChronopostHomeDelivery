<?php

namespace ChronopostHomeDelivery\Form;

use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryDeliveryModeQuery;
use Symfony\Component\Validator\Constraints;

use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\AreaQuery;

class ChronopostHomeDeliveryAddPriceForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add("area", "integer", array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(array(
                        "methods" => array(
                            array($this,
                                "verifyAreaExist")
                        )
                    ))
                )
            ))
            ->add("delivery_mode", "integer", array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(array(
                        "methods" => array(
                            array($this,
                                "verifyDeliveryModeExist")
                        )
                    ))
                )
            ))
            ->add("weight", "number", array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(array(
                        "methods" => array(
                            array($this,
                                "verifyValidWeight")
                        )
                    ))
                )
            ))
            ->add("price", "number", array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(array(
                        "methods" => array(
                            array($this,
                                "verifyValidPrice")
                        )
                    ))
                )
            ))
            ->add("franco", "number", array())
        ;
    }

    public function verifyAreaExist($value, ExecutionContextInterface $context)
    {
        $area = AreaQuery::create()->findPk($value);
        if (null === $area) {
            $context->addViolation(Translator::getInstance()->trans("This area doesn't exists.", [], ChronopostHomeDelivery::DOMAIN_NAME));
        }
    }

    public function verifyDeliveryModeExist($value, ExecutionContextInterface $context)
    {
        $mode = ChronopostHomeDeliveryDeliveryModeQuery::create()->findPk($value);
        if (null === $mode) {
            $context->addViolation(Translator::getInstance()->trans("This delivery mode doesn't exists.", [], ChronopostHomeDelivery::DOMAIN_NAME));
        }
    }

    public function verifyValidWeight($value, ExecutionContextInterface $context)
    {
        if (!preg_match("#^\d+\.?\d*$#", $value)) {
            $context->addViolation(Translator::getInstance()->trans("The weight value is not valid.", [], ChronopostHomeDelivery::DOMAIN_NAME));
        }

        if ($value < 0) {
            $context->addViolation(Translator::getInstance()->trans("The weight value must be superior to 0.", [], ChronopostHomeDelivery::DOMAIN_NAME));
        }
    }

    public function verifyValidPrice($value, ExecutionContextInterface $context)
    {
        if (!preg_match("#^\d+\.?\d*$#", $value)) {
            $context->addViolation(Translator::getInstance()->trans("The price value is not valid.", [], ChronopostHomeDelivery::DOMAIN_NAME));
        }
    }

    public function getName()
    {
        return "chronopost_home_delivery_price_create";
    }
}