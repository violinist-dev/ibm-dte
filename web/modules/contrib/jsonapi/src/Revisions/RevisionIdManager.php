<?php

namespace Drupal\jsonapi\Revisions;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides an Revision ID plugin manager.
 *
 * @see \Drupal\jsonapi\Revisions\Annotation\RevisionId
 * @see \Drupal\jsonapi\Revisions\RevisionsIdInterface
 * @see plugin_api
 */
class RevisionIdManager extends DefaultPluginManager {

  /**
   * Constructs a RevisionIdManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/RevisionId', $namespaces, $module_handler, 'Drupal\jsonapi\Revisions\RevisionIdInterface', 'Drupal\jsonapi\Revisions\Annotation\RevisionId');
    $this->alterInfo('revision_id_info');
    $this->setCacheBackend($cache_backend, 'revision_id_info_plugins');
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, array $configuration = []) {
    return parent::createInstance($plugin_id, $configuration);
    $plugin_definition = $this->getDefinition($plugin_id);
    $plugin_class = DefaultFactory::getPluginClass($plugin_id, $plugin_definition, 'Drupal\jsonapi\Revisions\RevisionIdInterface');
    return new $plugin_class($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function getInstance(array $options) {
    foreach ($this->getDefinitions() as $plugin_id => $definition) {
      return $this->createInstance($plugin_id, $options);
    }
  }

}
