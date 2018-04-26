<?php

namespace Drupal\replication\Normalizer;

use Drupal\serialization\Normalizer\NormalizerBase;

class AllDocsNormalizer extends NormalizerBase {

  protected $supportedInterfaceOrClass = ['Drupal\replication\AllDocs\AllDocsInterface'];

  /**
   * {@inheritdoc}
   */
  public function normalize($all_docs, $format = NULL, array $context = []) {
    $data = [
      'offset' => 0,
      'rows' => [],
    ];

    /** @var \Drupal\replication\AllDocs\AllDocsInterface $all_docs */
    $rows = $all_docs->execute();

    foreach ($rows as $key => $value) {
      $data['rows'][] = [
        'id' => $key,
        'key' => $key,
        'value' => $value
      ];
    }

    $data['total_rows'] = count($rows);
    return $data;
  }
}
