<?php

namespace ChronopostHomeDelivery\Controller;

use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Form\ChronopostHomeDeliveryTaxRuleForm;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

#[Route('/admin/module/ChronopostHomeDelivery/tax_rule', name: 'chronopost_home_delivery_tax_rule_')]
class ChronopostHomeDeliveryTaxRuleController extends BaseAdminController
{
    #[Route('/save', name: 'save')]
    public function saveTaxRule()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, ChronopostHomeDelivery::DOMAIN_NAME, AccessManager::UPDATE)) {
            return $response;
        }

        $taxRuleForm = $this->createForm(ChronopostHomeDeliveryTaxRuleForm::getName());

        $message = false;

        $url = '/admin/module/ChronopostHomeDelivery';

        try {
            $form = $this->validateForm($taxRuleForm);

            // Get the form field values
            $data = $form->getData();

            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDelivery::CHRONOPOST_TAX_RULE_ID, $data["tax_rule_id"]);

        } catch (FormValidationException $ex) {
            $message = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
        }

        if ($message !== false) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans('Error', [], ChronopostHomeDelivery::DOMAIN_NAME),
                $message,
                $taxRuleForm,
                $ex
            );
        }

        return $this->generateRedirect(URL::getInstance()->absoluteUrl($url, [ 'current_tab' => 'tax_rule']));
    }
}