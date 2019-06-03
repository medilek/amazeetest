<?php

namespace Drupal\amazeetest\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\amazeetest\Plugin\Services\Lexer as Lexer;

/**
 * Plugin implementation of the 'lexer_formula' formatter.
 *
 * @FieldFormatter(
 *   id = "lexer_formula",
 *   label = @Translation("Lexer formula"),
 *   field_types = {
 *     "lexer"
 *   }
 * )
 */
class LexerFormulaFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'title' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['title'] = [
      '#type' => 'textfield',
      '#title' => t('Title to replace formula with result'),
      '#default_value' => $this->getSetting('title'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $settings = $this->getSettings();

    if (!empty($settings['title'])) {
      $summary[] = t('Link using text: @title', ['@title' => $settings['title']]);
    }
    else {
      $summary[] = t('Display formula with result');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    \Drupal::logger('amazeetest')->debug('viewElements');
    $element = [];
    $title_setting = $this->getSetting('title');
    \Drupal::logger('amazeetest')->debug(json_encode($items));
    
    $lexer = new Lexer();
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#type' => 'formula',
        '#title' => $title_setting ?: $item->value.'='.$lexer->calculate($item->value),
      ];

      if (!empty($item->_attributes)) {
        $element[$delta]['#options'] += ['attributes' => []];
        $element[$delta]['#options']['attributes'] += $item->_attributes;
        // Unset field item attributes since they have been included in the
        // formatter output and should not be rendered in the field template.
        unset($item->_attributes);
      }

    }
    
    return $element;
  }

}
