<?php

namespace ChronopostHomeDelivery\Form;


use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryDeliveryModeQuery;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\AreaQuery;

class ChronopostHomeDeliveryUpdatePriceForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add("area", IntegerType::class, array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(
                        array($this, "verifyAreaExist")
                    )
                )
            ))
            ->add("delivery_mode", IntegerType::class, array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(
                        array($this, "verifyDeliveryModeExist")
                    )
                )
            ))
            ->add("weight", NumberType::class, array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                )
            ))
            ->add("price", NumberType::class, array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(
                        array($this, "verifyValidPrice")
                    )
                )
            ))
            ->add("franco", NumberType::class, array())
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

    public function verifyValidPrice($value, ExecutionContextInterface $context)
    {
        if (!preg_match("#^\d+\.?\d*$#", $value)) {
            $context->addViolation(Translator::getInstance()->trans("The price value is not valid.", [], ChronopostHomeDelivery::DOMAIN_NAME));
        }
    }

    public static function getName()
    {
        return "chronopost_home_delivery_price_create";
    }
}