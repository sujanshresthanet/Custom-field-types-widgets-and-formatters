<?php

/**
 * @file
 * Contains \Drupal\custom_fields\Plugin\field\formatter\CustomFieldsDefaultFormatter.
 */

namespace Drupal\custom_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'custom_fields_default' formatter.
 *
 * @FieldFormatter(
 *   id = "custom_fields_default",
 *   label = @Translation("Custom Fields Default"),
 *   field_types = {
 *     "custom_fields_code"
 *   }
 * )
 */
class CustomFieldsDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    foreach ($items as $delta => $item) {
      // Render output using custom_fields_default theme.
      $source = array(
        '#theme' => 'custom_fields_default',
        '#source_description' => $item->source_description,
        '#source_code' => $item->source_code,
      );

      $elements[$delta] = array('#markup' => \Drupal::service('renderer')->render($source));
    }

    return $elements;
  }
}