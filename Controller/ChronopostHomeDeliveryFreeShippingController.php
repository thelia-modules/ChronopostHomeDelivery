<?php

namespace ChronopostHomeDelivery\Controller;

use ChronopostHomeDelivery\ChronopostHomeDelivery;
use ChronopostHomeDelivery\Form\ChronopostHomeDeliveryAreaFreeShippingForm;
use ChronopostHomeDelivery\Form\ChronopostHomeDeliveryFreeShippingForm;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryAreaFreeshipping;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryAreaFreeshippingQuery;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryDeliveryModeQuery;
use Exception;
use Propel\Runtime\Exception\PropelException;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Model\AreaQuery;

/**
 * @Route("/admin/module/chronopost-home-delivery", name="chronopost-home-delivery")
 */
class ChronopostHomeDeliveryFreeShippingController extends BaseAdminController
{
    /**
     * Toggle free shipping for the delivery type being edited.
     *
     * @return mixed|null|Response|static
     * @Route("/freeshipping", name="_freeshipping", methods="POST")
     */
    public function toggleFreeShippingActivation()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('ChronopostHomeDelivery'), AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm(ChronopostHomeDeliveryFreeShippingForm::getName());
        $response = null;

        try {
            $vform = $this->validateForm($form);
            $freeshipping = $vform->get('freeshipping')->getData();
            $deliveryModeId = $vform->get('delivery_mode')->getData();

            $deliveryMode = ChronopostHomeDeliveryDeliveryModeQuery::create()->findOneById($deliveryModeId);
            $deliveryMode
                ->setFreeshippingActive($freeshipping)
                ->save();
            $response = new Response('');
        } catch (Exception $e) {
            $response = new JsonResponse(array("error" => $e->getMessage()), 500);
        }

        return $response;
    }

    /**
     * @return mixed|Response
     * @throws PropelException
     * @Route("/freeshipping_from", name="_freeshipping_from", methods="POST")
     */
    public function setFreeShippingFrom(RequestStack $requestStack)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('ChronopostHomeDelivery'), AccessManager::UPDATE)) {
            return $response;
        }

        $data = $requestStack->getCurrentRequest()->request;
        $deliveryMode = ChronopostHomeDeliveryDeliveryModeQuery::create()->findOneById($data->get('delivery-mode'));

        $price = $data->get("price") === "" ? null : $data->get("price");

        if ($price < 0) {
            $price = null;
        }
        $deliveryMode->setFreeshippingFrom($price)
            ->save();

        return $this->generateRedirectFromRoute(
            "admin.module.configure",
            array(),
            array(
                'current_tab'=>'prices_slices_tab_' . $data->get('delivery-mode'),
                'module_code'=>"ChronopostHomeDelivery",
                '_controller' => 'Thelia\\Controller\\Admin\\ModuleController::configureAction',
                'price_error_id' => null,
                'price_error' => null
            )
        );
    }

    /**
     * Set free shipping for a given area of the delivery type being edited.
     *
     * @return mixed|null|Response
     * @Route("/area_freeshipping", name="_area_freeshipping", methods="POST")
     */
    public function setAreaFreeShipping(RequestStack $requestStack)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('ChronopostHomeDelivery'), AccessManager::UPDATE)) {
            return $response;
        }
        $form = $this->createForm(ChronopostHomeDeliveryAreaFreeShippingForm::getName());
        try {
            $data = $this->validateForm($form)->getData();
            $chronopostAreaId = $data['area'];
            $chronopostDeliveryId = $data['delivery_mode_id'];
            $cartAmount = $data['cart_amount'];

            if ($cartAmount < 0 || $cartAmount === '') {
                $cartAmount = null;
            }

            $areaQuery = AreaQuery::create()->findOneById($chronopostAreaId);
            if (null === $areaQuery) {
                throw new RuntimeException('Area not found');
            }

            $deliveryModeQuery = ChronopostHomeDeliveryDeliveryModeQuery::create()->findOneById($chronopostDeliveryId);
            if (null === $deliveryModeQuery) {
                throw new RuntimeException('Delivery mode not found');
            }

            $chronopostFreeShipping = new ChronopostHomeDeliveryAreaFreeshipping();
            $chronopostFreeShippingQuery = ChronopostHomeDeliveryAreaFreeshippingQuery::create()
                ->filterByAreaId($chronopostAreaId)
                ->filterByDeliveryModeId($chronopostDeliveryId)
                ->findOne();

            if (null === $chronopostFreeShippingQuery) {
                $chronopostFreeShipping
                    ->setAreaId($chronopostAreaId)
                    ->setDeliveryModeId($chronopostDeliveryId)
                    ->setCartAmount($cartAmount)
                    ->save();
            }

            $cartAmountQuery = ChronopostHomeDeliveryAreaFreeshippingQuery::create()
                ->filterByAreaId($chronopostAreaId)
                ->filterByDeliveryModeId($chronopostDeliveryId)
                ->findOneOrCreate()
                ->setCartAmount($cartAmount)
                ->save();
        } catch (Exception $e) {
            $requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', $e->getMessage());
            $this->setupFormErrorContext(
                Translator::getInstance()->trans(
                    'error',
                    [],
                    ChronopostHomeDelivery::DOMAIN_NAME
                ),
                $e->getMessage(),
                $form
            );

            return $this->generateErrorRedirect($form);
        }
        return $this->generateSuccessRedirect($form);
    }

}
