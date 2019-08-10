<?php
/**
 * Created by PhpStorm.
 * User: 212542639
 * Date: 09-08-2019
 * Time: 09:26
 */

namespace Drupal\cus_site_info\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class CustomSiteInformationRouteSubscriber extends RouteSubscriberBase
{
    /**
     * {@inheritdoc}
     */
    protected function alterRoutes(RouteCollection $collection)
    {
        if($route = $collection->get('system.site_information_settings'))
        {
            // Update the route path to our custom form.
            $route->setDefault('_form', 'Drupal\cus_site_info\Form\CustomSiteInformationForm');
        }
    }
}