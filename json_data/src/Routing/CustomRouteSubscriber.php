<?php

namespace Drupal\json_data\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Extend RouteSubscibernBase to alter route of site config form.
 */
class CustomRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('system.site_information_settings')) {
      $route->setDefault('_form', '\Drupal\json_data\Form\AlterSiteConfigForm');
    }
  }

}
