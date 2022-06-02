<?php

/**
 * @file
 * Contains \Drupal\custom_fields\Plugin\Field\FieldWidget\CustomFieldsDefaultWidget.
 */

namespace Drupal\custom_fields\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'custom_fields_default' widget.
 *
 * @FieldWidget(
 *   id = "custom_fields_default",
 *   label = @Translation("Custom Fields Default"),
 *   field_types = {
 *     "custom_fields_code"
 *   }
 * )
 */
class CustomFieldsDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['source_description'] = array(
      '#title' => $this->t('Description'),
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->source_description) ? $items[$delta]->source_description : NULL,
    );
    $element['source_code'] = array(
      '#title' => $this->t('Code'),
      '#type' => 'textarea',
      '#default_value' => isset($items[$delta]->source_code) ? $items[$delta]->source_code : NULL,
    );
    $element['source_lang'] = array(
      '#title' => $this->t('Source language'),
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->source_lang) ? $items[$delta]->source_lang : NULL,
    );
    return $element;
  }
}