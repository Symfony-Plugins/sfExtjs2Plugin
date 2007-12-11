<?php

/**
 * @plugin           sfExts2Plugin
 * @description      sfExtjs2Plugin is a symfony plugin that provides an easy to use wrapper for the Ext javascript library
 * @author           Wolfgang Kubens<wolfgang.kubens [at] gmx [dot] net>, Benjamin Runnels<benjamin.r.runnels [at] citi [dot] com>
 * @version          0.0.23
 * @last modified    11.22.2007 Kubens:
 *                                         - Added parameter support for custom methods
 *                                         - Added features to create application
 *                                         - Added parameter support for Ext.object constructors
 *                                      11.17.2007 Kubens:
 *                                         - Added features to create custom classes and custom methods
 *                                      11.12.2007    Kubens:
 *                                         - Fixed loading order of adapters. If adapters are used then it is important to load
 *                                             adapters and coresponding files before ext-all.js
 *                                       - Overworked: load method. Adapters and themes are setuped in config.php
 *                                         - Overworked: constructor. If no adapter or theme is passed then default
 *                                             settings from config.php will used
 *                                      11.07.2007 KRavEN:
 *                                         - Fixed the adapter includes to load all required files in the correct order
 *                      moved ext-base into adapters, pass ext as adapter for standalone
 *                      changed all javascript to load first so they will come before files specified in view.yml
 *                   07.15.2007 Kubens:
 *                                         - created
 */
class sfExtjs2Plugin {

  const LBR    = "\n";
  const LBR_CM = ",\n";
  const LBR_SM = ";\n";

  private $items     = array();
  private $adapter   = ''; // current adapter
  private $theme     = ''; // current theme
  private $namespace = ''; // current namespace

  public function __construct($options = array())
  {
    $this->adapter = is_array($options) && array_key_exists('adapter',$options) && array_key_exists($options['adapter'], sfConfig::get('sf_extjs2_adapters', array())) ? $options['adapter'] : sfConfig::get('sf_extjs2_default_adapter');
    $this->theme   = is_array($options) && array_key_exists('theme',$options)   && array_key_exists($options['theme'],   sfConfig::get('sf_extjs2_themes', array()))   ? $options['theme']   : sfConfig::get('sf_extjs2_default_theme');
  }

  public static function __call ($class, $parameters)
  {
    $classes = sfConfig::get('classes');
    if (is_array($classes) && array_key_exists($class, $classes))
    {
      $object = sfConfig::get($classes[$class]);
        return sfExtjs2Plugin::getExtObject($object['class'], $parameters[0]);
    }
  }

  /**
   * Renders javascript source for Ext.Object
   * Example usage:
   *
   * Syntax A = short form without any options
   *
   *   echo $sfExtjs2Plugin->Object(array
   *         (
   *           'id'       => 'id',
   *           'renderTo' => 'document.body',
   *           'items'    => array
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
   *           'name'       => 'string',      // option to render Javascript variable
   *           'attributes' => array                    // attributes for Ext constructor
   *           (
   *             'id'       => 'id',
   *             'renderTo' => 'document.body',
   *             'items'    => array
   *               (
   *                 $sfExtjs2Plugin->Object(array('title' => '"Object A"')),
   *                 $sfExtjs2Plugin->Object(array('title' => '"Object B"'))
   *               )
   *           )
   *                      'parameters' => array
   *                      (
   *                          'parameter1',
   *                          'parameter2'
   *                      )
   *         );
   *
   * @param string class
   * @param array attributes
   * @return string source of Ext component
   *
   */
  public static function getExtObject($class, $attribs = array())
  {
    # parameters for constructor
    $parameters = array();
    if (is_array($attribs) && array_key_exists('parameters', $attribs))
    {
      $parameters = $attribs['parameters'];
      unset($attribs['parameters']);
    }

    #make it easier on us so not so much escaping in the attibutes setup
    foreach ($attribs as $key => $value)
    {
      #enables us to have empty keys removed
      if(is_null($value)) continue;

      #quotes everything not in the quote_except value list
      if(!is_array($value))
      {
        $attributes[$key] = (sfExtjs2Plugin::quote_except($key, $value) ? '\''.$value.'\'' : $value);
      }
      else
      {
        // TODO: fix this $value = array, so encode it to { key, value} recursively!
        $attributes[$key] = $value;
      }
    }

    # syntax A is a shortform of syntax B
    # if syntax A is used then convert syntax A to syntax B
    if (is_array($attributes) && !array_key_exists('attributes', $attributes))
    {
      $tmp = $attributes;
      $attributes = array();
      $attributes['attributes'] = $tmp;
    }

    # list attributes must defined as an Javascript array
    # therefore all list attributes must be rendered as [attributeA, attributeB, attributeC]
    foreach (sfConfig::get('sf_extjs2_list_attributes') as $attribute)
    {
      if (is_array($attributes) && array_key_exists($attribute, $attributes['attributes']))
      {
        $attStr = implode(',',$attributes['attributes'][$attribute]);
        $attributes['attributes'][$attribute] = (sfExtjs2Plugin::quote_except($attribute, null) ? '['.$attStr.']' : '{'.$attStr.'}');
        //$attributes['attributes'][$attribute] = sprintf('[%s]', implode(',',$attributes['attributes'][$attribute]));
      }
    }

    // get source of component
    $source = call_user_func(array('sfExtjs2Plugin', 'getExtObjectComponent'), $attributes['attributes'], sfConfig::get($class), $parameters);

    // if 'name' is assigned then we must render
    // either a Javascript variable or an attribute of this
    if (is_array($attributes) && array_key_exists('name', $attributes))
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
    if (is_array($attributes) && array_key_exists('lbr', $attributes))
    {
      $source .= $attributes['lbr'];
    }

    return $source;
  }

  /**
   * @param array attributes
   * @param array config
   * @param array parameters
   * @return string source of Ext component
   *
   */
  public static function getExtObjectComponent($attributes = array(), $config = array(), $parameters = array())
  {
    $attributes = sfExtjs2Plugin::_build_attributes($attributes, $config['attributes']);
    $attributes = sprintf('%s', $attributes != '' ? '{'.$attributes.'}' : '');

    $parameters = implode(',', $parameters);
    $parameters = sprintf('%s%s', $attributes != '' && $parameters != '' ? ',' : '', $parameters);

    $source = sprintf(
                'new %s (%s%s)',
                $config['class'],
                $attributes,
                $parameters
                            );

    return $source;
  }

  /**
   * add sources for css and js
   * files to response
   *
   */
  public function load()
  {
    $response = sfContext::getInstance()->getResponse();

    // add javascript sources for adapter
    $adapters = sfConfig::get('sf_extjs2_adapters', array());
    foreach ($adapters[$this->adapter] as $file)
    {
      $response->addJavascript(sfConfig::get('sf_extjs2_js_dir'). $file, 'first');
    }

    // add javascript sources for ext all
    $response->addJavascript(sfConfig::get('sf_extjs2_js_dir'). 'ext-all.js', 'first');

    // add css sources for ext all
    $response->addStylesheet(sfConfig::get('sf_extjs2_css_dir'). 'ext-all.css', 'first');

    // add css sources for theme
    $themes = sfConfig::get('sf_extjs2_themes', array());
    foreach ($themes[$this->theme] as $file)
    {
      $response->addStylesheet(sfConfig::get('sf_extjs2_css_dir'). $file, 'first');
    }
  }

  /**
   * writes opening tag for javascript
   *
   */
  public function begin()
  {
    $source  = sfExtjs2Plugin::LBR;
    $source .= sprintf("<script type='text/javascript'>%s", sfExtjs2Plugin::LBR);
    $source .= sprintf("Ext.BLANK_IMAGE_URL = '%s'%s", sfConfig::get('sf_extjs2_spacer'), sfExtjs2Plugin::LBR_SM);

    echo $source;
  }

  /**
   * writes closing tag for javascript
   *
   */
  public function end()
  {
    $source  = sfExtjs2Plugin::LBR;
    $source .= sprintf("</script>%s", sfExtjs2Plugin::LBR);

    echo $source;
  }

  /**
   * writes opening class tag
   *
   * @param string namespace
   * @param string classname
   * @param string extend
   * @param array attributes
   *
   */
  public function beginClass($namespace = null, $classname = null, $extend = null, $attributes = array())
  {
    $source = '';

    // write namespace directive
    // prevent double output of namespace directive
    if ($this->namespace !== $namespace)
    {
      $this->namespace = $namespace;
      $source .= sprintf("Ext.namespace('%s')%s", $namespace, sfExtjs2Plugin::LBR_SM);
    }

    // write class tag
    $source .= sprintf("%s.%s = Ext.extend(%s, {%s", $namespace, $classname, $extend, sfExtjs2Plugin::LBR);

    // write attributes
    $i = 0;
    foreach ($attributes as $key => $value)
    {
      $i++;
      $source .= sprintf('%s: %s%s', $key, $value, $i < count($attributes) ? sfExtjs2Plugin::LBR_CM : sfExtjs2Plugin::LBR );
    }

    echo $source;
  }

  /**
   * writes closing class tag
   *
   * @return source
   */
  public function endClass()
  {
    $source  = '';
    $source .= sprintf("})%s", sfExtjs2Plugin::LBR_SM);

    echo $source;
  }

  /**
   * writes begining application tag
   *
   * @param attributes['name']
   * @param attributes['private']
   * @param attributes['public']
   * @return source
   */
  public function beginApplication($attributes = array())
  {
    // private attributes
    $sourcePrivate = '';
    if (array_key_exists('private', $attributes))
    {
      foreach ($attributes['private'] as $key => $value)
      {
        $sourcePrivate .= sprintf('%svar %s = %s;', sfExtjs2Plugin::LBR, $key, $value);
      }
    }

    // public attributes
    $sourcePublic = '';
    if (array_key_exists('public', $attributes))
    {
      $i = 0;
      foreach ($attributes['public'] as $key => $value)
      {
        $i++;
        $sourcePublic .= sprintf('%s%s: %s%s', sfExtjs2Plugin::LBR, $key, $value, $i < count($attributes['public']) ? ',' : '' );
      }
    }

    // write application syntax
    $source = '';
    $source = sprintf(
      'var %s = function() { %s%sreturn {%s %s',
      $attributes['name'],
      $sourcePrivate,
      $sourcePrivate != '' ? sfExtjs2Plugin::LBR : '',
      $sourcePublic,
      $sourcePublic != '' ? sfExtjs2Plugin::LBR : ''
    );

    echo $source;
  }

  /**
   * writes closing application tag
   *
   * @return source
   */
  public function endApplication()
  {
    $source  = '';
    $source .= sprintf("}}()%s", sfExtjs2Plugin::LBR_SM);

    echo $source;
  }

  /**
   * returns output of evaled php code
   *
   * @param array matches
   * @return string source
   */
  public static function methodEvalPHP ($matches)
  {
    $source = str_replace( array('<?php', '<?', '?>'), '', $matches[0]);
    ob_start();
    eval($source);
    $source = ob_get_contents();
    ob_end_clean();

    return $source;
  }
  /**
   * returns source for method
   * including output of evaled php code
   *
   * @param array attributes
   * @return string source
   */
  public static function method($attributes = array())
  {
    $source = is_array($attributes) && array_key_exists('source', $attributes) ? $attributes['source'] : $attributes;
    $source = preg_replace_callback(
                '/(\<\?php)(.*?)(\?>)/si',
                array('self', 'methodEvalPHP'),
                $source
              );
    $source = sprintf("function (%s) { %s }", is_array($attributes) && array_key_exists('parameters', $attributes) ? $attributes['parameters'] : '', $source);

    return $source;
  }

  /**
   * returns source of custom class
   *
   * @param string classname
   * @package array attributes
   * @return string source
   */
  public function customClass($classname, $attributes = array())
  {
    $source  = '';
    $source .= $this->getExtObjectComponent($attributes, array('attributes'=>array(), 'class'=>$classname));

    return $source;
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
   *             array('foo' => 'defbar')                                            // default attributes
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
      $attributes .= sprintf('%s%s:%s', ($attributes === '' ? '' : ','), $key, sfExtjs2Plugin::_process_value($value));
    }

    return $attributes;
  }
  
  private static function _process_value($value = '')
  {
    if (is_array($value)) 
    {
      $array_value = $value;
      $value = '{';
      
      foreach ($array_value as $key => $v)
      {
        if (!is_array($v) && ($v != 'true') && ($v != 'false'))
        {
          $value .= sprintf('%s%s:\'%s\'', ($value === '{' ? '' : ','), $key, sfExtjs2Plugin::_process_value($v));
        }
        else
        {
          $value .= sprintf('%s%s:%s', ($value === '{' ? '' : ','), $key, sfExtjs2Plugin::_process_value($v));
        }
      }
       
      $value .= '}';
    }
    
    return $value;
  }

  private static function quote_except($key, $value)
  {
    $quoteExcept = sfConfig::get('sf_extjs2_quote_except');

    if (is_int($key))
    {
      return false;
    }
    else
    {
      foreach ($quoteExcept['key'] as $except)
      {
        if ($key == $except)
        {
          return false;
        }
      }
    }

    if (is_int($value))
    {
      return false;
    }
    else
    {
      foreach ($quoteExcept['value'] as $except)
      {
        if (substr($value, 0, strlen($except)) == $except)
        {
          return false;
        }
      }
    }

    return true;
  }

}

?>
