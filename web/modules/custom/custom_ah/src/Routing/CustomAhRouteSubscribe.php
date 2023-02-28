<?php

namespace Drupal\custom_ah\Routing;

class CustomAhRouteSubscribe extends \Drupal\Core\Routing\RouteSubscriberBase
{

    /**
     * @inheritDoc
     */
    protected function alterRoutes(\Symfony\Component\Routing\RouteCollection $collection)
    {
      // Change path '/user/login' to '/login'.
      if ($route = $collection->get('user.login')) {
        //$route->setDefault('_form', 'Drupal\custom_ah\Form\CustomAhLoginForm');
      }
    }
}
