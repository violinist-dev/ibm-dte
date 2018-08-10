<?php

namespace Drupal\jsonapi\Revisions;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\jsonapi\Revisions\RevisionIdNegotiationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base revision id plugin.
 */
class RevisionIdNegotiationBase extends PluginBase implements PluginInspectionInterface, ContainerFactoryPluginInterface, RevisionIdNegotiationInterface {

  /**
   * The current revision id value.
   *
   * @var string
   */
  const CURRENT = 'working-copy';

  /**
   * The latest revision id value.
   *
   * @var string
   */
  const LATEST = 'latest';

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionId(EntityInterface $entity, $revision_id_value) {
    // To be implemented by individual plugin instances.
    return $revision_id_value;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

}
