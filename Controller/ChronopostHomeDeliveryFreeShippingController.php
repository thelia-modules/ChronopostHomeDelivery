<?php

namespace ChronopostHomeDelivery\Controller;


use ChronopostHomeDelivery\Form\ChronopostHomeDeliveryFreeShippingForm;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryAreaFreeshipping;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryAreaFreeshippingQuery;
use ChronopostHomeDelivery\Model\ChronopostHomeDeliveryDeliveryModeQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Model\AreaQuery;
use Symfony\Component\Routing\Annotation\Route;

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
            $response = Response::create('');
        } catch (\Exception $e) {
            $response = JsonResponse::create(array("error" => $e->getMessage()), 500);
        }

        return $response;
    }

    /**
     * @return mixed|Response
     * @throws \Propel\Runtime\Exception\PropelException
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

        $data = $requestStack->getCurrentRequest()->request;

        try {

            $chronopostAreaId = $data->get('area-id');
            $chronopostDeliveryId = $data->get('delivery-mode');
            $cartAmount = $data->get("cart-amount");

            if ($cartAmount < 0 || $cartAmount === '') {
                $cartAmount = null;
            }

            $areaQuery = AreaQuery::create()->findOneById($chronopostAreaId);
            if (null === $areaQuery) {
                return null;
            }

            $deliveryModeQuery = ChronopostHomeDeliveryDeliveryModeQuery::create()->findOneById($chronopostDeliveryId);
            if (null === $deliveryModeQuery) {
                return null;
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
        } catch (\Exception $e) {

        }

        return $this->generateRedirectFromRoute(
            "admin.module.configure",
            array(),
            array(
                'current_tab' => 'prices_slices_tab_' . $data->get('delivery-mode'),
                'module_code' => "ChronopostHomeDelivery",
                '_controller' => 'Thelia\\Controller\\Admin\\ModuleController::configureAction',
                'price_error_id' => null,
                'price_error' => null
            )
        );
    }

}