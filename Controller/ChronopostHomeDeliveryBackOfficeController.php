<?php

namespace ChronopostHomeDelivery\Controller;


use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Config\ChronopostHomeDeliveryConst;
use ChronopostHomeDelivery\Form\ChronopostHomeDeliveryConfigurationForm;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/module/ChronopostHomeDelivery/config", name="ChronopostHomeDelivery_config")
 */
class ChronopostHomeDeliveryBackOfficeController extends BaseAdminController
{
    /**
     * Render the module config page
     *
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function viewAction($tab)
    {
        return $this->render(
            'module-configure',
            [
                'module_code' => 'ChronopostHomeDelivery',
                'current_tab' => $tab,
            ]
        );
    }

    /**
     * Save configuration form - Chronopost informations
     *
     * @return mixed|null|\Symfony\Component\HttpFoundation\Response|\Thelia\Core\HttpFoundation\Response
     * @Route("", name="_save", methods="POST")
     */
    public function saveAction()
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], 'ChronopostHomeDelivery', AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm(ChronopostHomeDeliveryConfigurationForm::getName());

        try {
            $data = $this->validateForm($form)->getData();

            /** Basic informations */
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_CODE_CLIENT]);
            ChronopostHomeDelivery::setConfigValue(ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PASSWORD, $data[ChronopostHomeDeliveryConst::CHRONOPOST_HOME_DELIVERY_PASSWORD]);

            /** Delivery types */
            foreach (ChronopostHomeDeliveryConst::getDeliveryTypesStatusKeys() as $statusKey) {
                ChronopostHomeDelivery::setConfigValue($statusKey, $data[$statusKey]);
            }

        } catch (\Exception $e) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans(
                    "Error",
                    [],
                    ChronopostHomeDelivery::DOMAIN_NAME
                ),
                $e->getMessage(),
                $form
            );

            return $this->viewAction('configure');
        }

        return $this->generateSuccessRedirect($form);
    }
}