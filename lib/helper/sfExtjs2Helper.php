<?php

/**
 * @plugin           sfExts2Plugin
 * @description      sfExtjs2Plugin is a symfony plugin that provides an easy to use wrapper for the Ext javascript library
 * @author           Wolfgang Kubens<wolfgang.kubens [at] gmx [dot] net>, Benjamin Runnels<benjamin.r.runnels [at] citi [dot] com>
 * @version          0.0.17
 * @last modified    11.12.2007	Kubens: 
 * 										- Fixed loading order of adapters. If adapters are used then it is important to load 
 * 											adapters and coresponding files before ext-all.js
 * 									  - Overworked: load method. Adapters and themes are setuped in config.php
 * 										- Overworked: constructor. If no adapter or theme is passed then default
 * 											settings from config.php will used 
 * 									 11.07.2007 KRavEN: 
 * 										- Fixed the adapter includes to load all required files in the correct order
 *                      moved ext-base into adapters, pass ext as adapter for standalone
 *                      changed all javascript to load first so they will come before files specified in view.yml
 *                   07.15.2007 Kubens: 
 * 										- created
 */

class sfExtjs2Plugin {

  private $items    = array();
  private $adapter  = '';
  private $theme    = '';

  public function __construct($options = array())
  {
    $this->adapter = key_exists('adapter',$options) && key_exists($options['adapter'], sfConfig::get('sf_extjs2_adapters', array())) ? $options['adapter'] : sfConfig::get('sf_extjs2_default_adapter');
    $this->theme   = key_exists('theme',$options)   && key_exists($options['theme'],   sfConfig::get('sf_extjs2_themes', array()))   ? $options['theme']   : sfConfig::get('sf_extjs2_default_theme');
  }

  public static function __call ($class, $parameters)
  {
    $classes = sfConfig::get('classes');
    if (key_exists($class, $classes))
    {
      $object = sfConfig::get($classes[$class]);
      return sfExtjs2Plugin::getExtObject($object['class'], $parameters[0]);
    }
  }

  /**
   * renders a Ext.Object
   *
   * Example usage:
   * 
   * Syntax A = short form without any options
   *
   *   echo $sfExtjs2Plugin->Object(array
   *         (
   *           'id' => 'id',
   *           'renderTo' => 'document.body',
   *           'items' => array
   *           (
   *             $sfExtjs2Plugin->Object(array('title'=>'"Object A"')),
   *             $sfExtjs2Plugin->Object(array('title'=>'"Object B"'))
   *           )
   *         );
   *
   * Syntax B = long form with additional options
   *
   *   echo $sfExtjs2Plugin->Object(array
   *         (
   *           'name' => 'object',      // option to render Javascript variable
   *           'ext' => array
   *           (
   *             'id' => 'id',
   *             'renderTo' => 'document.body',
   *               'items' => array
   *               (
   *                 $sfExtjs2Plugin->Object(array('title'=>'"Object A"')),
   *                 $sfExtjs2Plugin->Object(array('title'=>'"Object B"'))
   *               )
   *           )
   *         );
   *
   * @param string object
   * @param array attributes
   * @return string source of Ext component
   *
   */
  public static function getExtObject($class, $attributes = array(), $options = array())
  {
    # syntax A is a shortform of syntax B
    # if syntax A is used then convert syntax A to syntax B
    if (!array_key_exists('ext', $attributes))
    {
      $tmp = $attributes;
      $attributes = array();
      $attributes['ext'] = $tmp;
    }

    # list attributes must defined as an Javascript array
    # therefore all list attributes must be rendered as [attributeA, attributeB, attributeC]
    foreach (sfConfig::get('sf_extjs2_list_attributes') as $attribute)
    {
      if (array_key_exists($attribute, $attributes['ext']))
      {
        $attributes['ext'][$attribute] = sprintf('[%s]', implode(',',$attributes['ext'][$attribute]));
      }
    }

    // get source of component
    $source = call_user_func(array('sfExtjs2Plugin', 'getExtObjectComponent'), $attributes['ext'], sfConfig::get($class));

    // if 'name' assigned then we must render
    // either a Javascript variable or an attribute of this
    if (array_key_exists('name', $attributes))
    {
      $source = sprintf
      (
        '%s%s = %s',
        strpos($attributes['name'], 'this.') === false ? 'var ' : '',
        $attributes['name'],
        $source
      );
    }

    // if 'lbr' assigned then we must render a line break
    if (array_key_exists('lbr', $attributes))
    {
      $source .= $attributes['lbr'];
    }

    return $source;
  }

  /**
   * @param array attributes
   * @param array config
   * @param boolean inline
   * @return string source of Ext component
   *
   */
  public static function getExtObjectComponent($attributes = array(), $config = array(), $inline = false)
  {
    $attributes = sfExtjs2Plugin::_build_attributes($attributes, $config['attributes']);
    $source = $inline ? sprintf('{%s}', $attributes) : sprintf('new %s ({%s})', $config['class'], $attributes);

    return $source;
  }

  /**
   * writes sources for
   *  css files
   *  js files
   *
   */
  public function load()
  {
    $response = sfContext::getInstance()->getResponse();
    
    // add javascript sources for adapter
    $adapters = sfConfig::get(sf_extjs2_adapters, array()); 
    foreach ($adapters[$this->adapter] as $file)
    {
      $response->addJavascript(sfConfig::get('sf_extjs2_js_dir'). $file, 'first');
    }

    // add javascript sources for ext all
    $response->addJavascript(sfConfig::get('sf_extjs2_js_dir'). 'ext-all.js', 'last');
    
    // add css sources for ext all
    $response->addStylesheet(sfConfig::get('sf_extjs2_css_dir'). 'ext-all.css', 'last');

    // add css sources for theme
    $themes = sfConfig::get(sf_extjs2_themes, array()); 
    foreach ($themes[$this->theme] as $file)
    {
      $response->addStylesheet(sfConfig::get('sf_extjs2_css_dir'). $file, 'last');
    }
  }

  /**
   * writes opening tag for javascript
   *
   */
  public function begin()
  {
    echo "\n";
    echo "<script type='text/javascript'>\n";
    echo "Ext.onReady(function(){\n";
    echo "Ext.BLANK_IMAGE_URL = '".sfConfig::get('sf_extjs2_spacer')."'\n";
  }

  /**
   * writes closing tag for javascript
   *
   */
  public function end()
  {
    echo "\n";
    echo "});\n";
    echo "</script>\n";
  }

  /**
   * Build attributes based on custom attributes and default attributes.
   * Custom attributes and default attributes will merged.
   * Custom attributes overwrites default attributes.
   *
   * Example usage:
   *
   *         _buid_attributes(
   *             array('foo' => 'custombar', 'foo1' => 'bar1', 'foo2' => 'bar2'),    // custom attributes
   *             array('foo' => 'defbar')                                                                                    // default attributes
   *        )
   *
   *         returns 'foo: custombar, foo1: bar1, foo2: bar2'
   *
   * @param array custom attributes
   * @param array default attributes
   * @return string merged attributes
   *
   */
  private static function _build_attributes ($custom_attributes = array(), $default_attributes = array())
  {
    $merged_attributes = array_merge($default_attributes, $custom_attributes);

    $attributes = '';
    foreach ($merged_attributes as $key => $value)
    {
      $attributes .= sprintf('%s%s:%s', ($attributes === '' ? '' : ','), $key, $value);
    }

    return $attributes;
  }
 
}

?>
