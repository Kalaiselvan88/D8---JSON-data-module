<?php

namespace Drupal\json_data\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Serializer;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Extend Controller to emit node as JSON.
 */
class JsonController extends ControllerBase {

  /**
   * The serializer.
   *
   * @var \Symfony\Component\Serializer\Serializer
   */
  protected $serializer;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * Constructs a new JsonController object.
   *
   * @param \Symfony\Component\Serializer\Serializer $serializer
   *   The serializer service.
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The entity query service.
   */
  public function __construct(Serializer $serializer, EntityTypeManager $entity_manager, ConfigFactory $config_factory, QueryFactory $query_factory) {
    $this->serializer = $serializer;
    $this->entityManager = $entity_manager;
    $this->configFactory = $config_factory;
    $this->queryFactory = $query_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('serializer'),
      $container->get('entity_type.manager'),
      $container->get('config.factory'),
      $container->get('entity.query')
    );
  }

  /**
   * Use Serialiizer sevice to convert node to JSON.
   */
  public function content($apikey, $nid) {
    $node = $this->entityManager->getStorage('node')->load($nid);
    $data = $this->serializer->serialize($node, 'json', ['plugin_id' => 'entity']);

    return ['#markup' => $data];
  }

  /**
   * Check for access conditions as per requirement.
   */
  public function access($apikey, $nid) {
    $config = $this->configFactory->get('system.site');
    $node_exists = $this->queryFactory->get('node')->condition('nid', $nid)->execute();
    if (($apikey == $config->get('siteapikey')) && (!empty($node_exists))) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

}
