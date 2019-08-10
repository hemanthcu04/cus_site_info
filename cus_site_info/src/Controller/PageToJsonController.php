<?php
/**
 * Created by PhpStorm.
 * User: 212542639
 * Date: 09-08-2019
 * Time: 10:41
 */

namespace Drupal\cus_site_info\Controller;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Access\AccessResultForbidden;

class PageToJsonController extends ControllerBase {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $systemConfig;

  /**
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * PageToJsonController constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param \Symfony\Component\Serializer\SerializerInterface $serializer
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, SerializerInterface $serializer, ConfigFactoryInterface $configFactory) {
    $this->entityTypeManager = $entityTypeManager;
    $this->serializer = $serializer;
    $this->systemConfig = $configFactory->get("system.site");
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return \Drupal\Core\Controller\ControllerBase|\Drupal\cus_site_info\Controller\PageToJsonController
   */
  public static function create(ContainerInterface $container) {
    $serializer = $container->get('serializer');
    $entityTypeManager = $container->get("entity_type.manager");
    $configFactory = $container->get("config.factory");
    return new static($entityTypeManager, $serializer, $configFactory);
  }

  /**
   * @param $node_id
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function json($node_id) {
    //Get the storage of node
    try {
      $node_storage = $this->entityTypeManager->getStorage('node');
    } catch (InvalidPluginDefinitionException $e) {
    } catch (PluginNotFoundException $e) {
    }
    //Load node with type page and specific node id.
    $node = $node_storage->loadByProperties(['type' => 'page','nid' => $node_id]);

    // Serialize the node and display the output in json format.
    $data = $this->serializer->serialize($node, 'json', ['plugin_id' => 'entity']);
    $response['data'] = $data;
    $response['method'] = 'GET';
    return new JsonResponse($response);
  }

  /**
   * Checks if the accessed node is of type page and if site api key is set or not.
   * @param null $node_id
   *
   * @return mixed
   */
  public function access($node_id) {

    //Get the site api key value
    $site_api_key = $this->systemConfig ->get('siteapikey');

    try {
      $query = $this->entityTypeManager->getStorage('node')->getQuery();
    } catch (InvalidPluginDefinitionException $e) {
    } catch (PluginNotFoundException $e) {
    }
    //
    $values = $query->condition('nid', $node_id)->condition('type', 'page')->execute();
    if (!empty($site_api_key) && !empty($values)) {
      return AccessResultAllowed::allowed();
    }
    return AccessResultForbidden::forbidden();
  }
}