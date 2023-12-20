<?php

/**
 * @file
 * Contains \Drupal\custom_field\Plugin\Field\FieldType\CustomFieldsItem.
 */

namespace Drupal\custom_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'custom_fields' field type.
 *
 * @FieldType(
 *   id = "custom_fields_code",
 *   label = @Translation("Custom Fields Item field"),
 *   description = @Translation("This field stores code custom fields in the database."),
 *   default_widget = "custom_fields_default",
 *   default_formatter = "custom_fields_default"
 * )
 */
class CustomFieldsItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field) {
    return array(
      'columns' => array(
        'source_description' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => FALSE,
        ),
        'source_code' => array(
          'type' => 'text',
          'size' => 'big',
          'not null' => FALSE,
        ),
        'source_lang' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => FALSE,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('source_code')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  static $propertyDefinitions;

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['source_description'] = DataDefinition::create('string')
    ->setLabel(t('Snippet description'));

    $properties['source_code'] = DataDefinition::create('string')
    ->setLabel(t('Snippet code'));

    $properties['source_lang'] = DataDefinition::create('string')
    ->setLabel(t('Programming Language'))
    ->setDescription(t('Snippet code language'));

    return $properties;
  }
}
