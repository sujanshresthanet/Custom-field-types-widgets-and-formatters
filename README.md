[![Donate via PayPal](https://img.shields.io/badge/donate-paypal-87ceeb.svg)](https://www.paypal.com/donate/?hosted_button_id=2L5XM9USTV4K4)
*Please consider supporting this project by making a donation via [PayPal](https://www.paypal.com/donate/?hosted_button_id=2L5XM9USTV4K4)*

## Description
I have created a "basic" custom field in Drupal 8. I won't go into detail about [PSR–4](https://drupal.org/node/1971198), [annotations](https://drupal.org/node/1882526) or [plugins](https://drupal.org/node/2087839) or this tutorial will be huge.

Instead, I'll add links to other websites that explain the concept further.

That being said, if you're looking for detailed documentation on the Field API in Drupal 8.

In Drupal 8, fields are not implemented using hooks like they are in Drupal 7. Instead, they are created using Drupal 8's new [Plugin API](https://drupal.org/node/2087839). This means that instead of implementing hooks, we define a class for a widget, formatter and field item. Most Drupal 7 field hooks like `hook_field_schema`, `hook_field_is_empty` and more; are now methods in classes.


**Getting started:-**


### [](#s-step-1-implement-field-item "Permalink to this headline")Step 1: Implement Field Item

The first bit of work we need to do is define a field item class called `CustomFieldsItem` that extends the `FieldItemBase` class.

1\. In Drupal 8 classes are loaded using [PSR-4](https://drupal.org/node/1971198).

So, to define the `CustomFieldsItem` class, we need to create a `CustomFieldsItem.php` file and place it in `"module"/src/Plugin/Field/FieldType/CustomFieldsItem.php`

```php
/**
 * @file
 * Contains \Drupal\custom_fields\Plugin\Field\FieldType\CustomFieldsItem.
 */

namespace Drupal\custom_fields\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

```

Then in the file we add a namespace `Drupal\custom_fields\Plugin\Field\FieldType` and three _use_ statements:

*   `Drupal\Core\Field\FieldItemBase`.
*   `Drupal\Core\Field\FieldStorageDefinitionInterface` .
*   `Drupal\Core\TypedData\DataDefinition`.

2\. Now we need to define the actual field details like the field id, label, default widget and formatter etc.. This is equivalent of implementing `hook_field_info` in Drupal 7.

In Drupal 8 a lot, if not all, of the info hooks have been replaced by [annotations](https://drupal.org/node/1882526).

```php
/**
 * Plugin implementation of the 'custom_fields' field type.
 *
 * @FieldType(
 *   id = "custom_fields_code",
 *   label = @Translation("CustomFields field"),
 *   description = @Translation("This field stores code custom_fields in the database."),
 *   default_widget = "custom_fields_default",
 *   default_formatter = "custom_fields_default"
 * )
 */
class CustomFieldsItem extends FieldItemBase { }
```

So instead of implementing `hook_field_info`, we define the field as an annotation inside of a comment above the class.

The annotation attributes are quite self-explanatory. Just make sure that the `default_widget` and `default_formatter` reference the widget and formatter annotation ID and not the class.

> If you want to learn more about annotations, check out the [Annotations-based plugins](https://drupal.org/node/1882526) documentation page on drupal.org.

3\. Now that we have our field item class, we need to define a few methods. The first one we'll look at is `schema()`.

In Drupal 7, when you create a custom field you define its schema using `hook_field_schema`. In Drupal 8, we define the schema by adding a `schema()` method to the `CustomFieldsItem` class.

> The [Schema API documentation](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Database%21database.api.php/group/schemaapi/latest) provides a description of schema array structure and possible values. 

```php
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
```

4\. Now we need to add the `isEmpty()` method and define what constitutes an empty field item. This method is the same as implementing `hook_field_is_empty` in Drupal 7.

```php
/**
 * {@inheritdoc}
 */
public function isEmpty() {
  $value = $this->get('source_code')->getValue();
  return $value === NULL || $value === '';
}
```

5\. The final method we'll add to the class is the `propertyDefinitions()` method.

```php
/**
 * {@inheritdoc}
 */
static $propertyDefinitions;

/**
 * {@inheritdoc}
 */
public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['source_description'] = DataDefinition::create('string')
      ->setLabel(t('CustomField description'));

    $properties['source_code'] = DataDefinition::create('string')
      ->setLabel(t('CustomField code'));

    $properties['source_lang'] = DataDefinition::create('string')
      ->setLabel(t('Programming Language'))
      ->setDescription(t('CustomField code language'));

    return $properties;
  }
```

This method is used to define the type of data that exists in the field values. The "CustomFields field" has just three values: description, code and language. So I just added those values to the method as strings.

> Go to the [How Entity API implements Typed Data API](https://drupal.org/node/1795854) documentation on drupal.org to learn more about this.

Note: it needs to be updated to the PSR-4 specification, see [https://www.drupal.org/node/2128865](https://www.drupal.org/node/2128865) for more details._

### [](#s-step-2-implement-field-widget "Permalink to this headline")Step 2: Implement Field Widget

Now that we've defined the field item, let's create the field widget. We need to create a class called `CustomFieldsDefaultWidget` that extends the `WidgetBase` class.

1\. So create a `CustomFieldsDefaultWidget.php` file and add it to `"module"/src/Plugin/Field/FieldWidget/CustomFieldsDefaultWidget.php`.

```php
/**
 * @file
 * Contains \Drupal\custom_fields\Plugin\Field\FieldWidget\CustomFieldsDefaultWidget.
 */

namespace Drupal\custom_fields\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
```

Make sure the file namespace is `Drupal\custom_fields\Plugin\Field\FieldWidget` and add the following three _use_ statements:

*   `Drupal\Core\Field\FieldItemListInterface`.
*   `Drupal\Core\Field\WidgetBase`.
*   `Drupal\Core\Form\FormStateInterface`.

2\. Next, we need to define the widget using an annotation. This is the equivalent of using `hook_field_widget_info` in Drupal 7.

```php
/**
 * Plugin implementation of the 'custom_fields_default' widget.
 *
 * @FieldWidget(
 *   id = "custom_fields_default",
 *   label = @Translation("CustomFields default"),
 *   field_types = {
 *     "custom_fields_code"
 *   }
 * )
 */
class CustomFieldsDefaultWidget extends WidgetBase { }
```

Just a heads up, make sure that the `field_types` attribute in the annotation references the field types using their ID. For this module, it is `custom_fields_code` because we added `id = "custom_fields_code",` to the `@FieldType` annotation.

3\. And finally, we need to define the actual widget form. We do this by adding a `formElement()` method to the `CustomFieldsDefaultWidget` class. This method is the same as using `hook_field_widget_form` in Drupal 7.

```php
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
```

 Note: it needs to be updated to the PSR-4 specification, see [https://www.drupal.org/node/2128865](https://www.drupal.org/node/2128865) for more details._

### [](#s-step-3-implement-field-formatter "Permalink to this headline")Step 3: Implement Field Formatter

The final piece to the puzzle, is the field formatter, and we create it by defining a class called `CustomFieldsDefaultFormatter` that extends the `FormatterBase` class.

1\. Create a `CustomFieldsDefaultFormatter.php` file and add it to `"module"/src/Plugin/Field/FieldFormatter/CustomFieldsDefaultFormatter.php`.

```php
/**
 * @file
 * Contains \Drupal\custom_fields\Plugin\field\formatter\CustomFieldsDefaultFormatter.
 */

namespace Drupal\custom_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
```

Make sure the file namespace is `Drupal\custom_fields\Plugin\Field\FieldFormatter` and add the following _use_ statements:

*   `Drupal\Core\Field\FieldItemListInterface`.
*   `Drupal\Core\Field\FormatterBase`.

2\. Next, we need to define the formatter as an annotation. The same as we did for the widget and field type, this is the equivalent of using `hook_field_formatter_info`.

```php
/**
 * Plugin implementation of the 'custom_fields_default' formatter.
 *
 * @FieldFormatter(
 *   id = "custom_fields_default",
 *   label = @Translation("CustomFields default"),
 *   field_types = {
 *     "custom_fields_code"
 *   }
 * )
 */
class CustomFieldsDefaultFormatter extends FormatterBase { }
```

3\. Now the only thing left to do is add the `viewElements()` method and define the actual field formatter. Again, this method is the same as using `hook_field_formatter_view` in Drupal 7.

```php
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
```

4\. Create a custom_fields.module file and add code that defines twig templates. The key of each item in the array is what you will need to call the template.

```php
/**
 * Implements hook_theme().
 */
function custom_fields_theme() {
  return array(
    'custom_fields_default' => array(
      'variables' => array('source_description' => NULL, 'source_code' => NULL),
      'template' => 'custom-fields-default',
    ),
  );
}
```

5\. In our module, inside of the templates folder, create a twig template. The name of the file has to match what you put into hook_theme() (make sure replace underscores with dashes). In this case, the file name would be `custom-fields-default.html.twig`. The reason for this is I didn't want to put a lot of logic or HTML code in the `viewElements()` method.

```twig
{% if source_description %}
  <div class="snippets-description">{{ source_description }}</div>
{% endif %}
{% if source_code %}
  <pre>{{ source_code }}</pre>
{% endif %}
```

### [](#s-conclusion "Permalink to this headline")Conclusion

As stated earlier the biggest change in Drupal 8 is that fields are created using the [Plugin API](https://drupal.org/node/2087839) and not hooks. Once you understand that, the concept of creating a field is very similar to Drupal 7. A lot of the methods in Drupal 8 match the hooks in Drupal 7.
