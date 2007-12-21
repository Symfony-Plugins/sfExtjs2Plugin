<?php

/**
 * @plugin           sfExtjs2Plugin
 * @description      sfExtjs2Plugin is a symfony plugin that provides an easy to use wrapper for the Ext javascript library
 * @author           Benjamin Runnels<benjamin.r.runnels [at] citi [dot] com>, Leon van der Ree, Wolfgang Kubens<wolfgang.kubens [at] gmx [dot] net>
 * @version          0.0.54
 * @last modified    12.20.2007 Wolfgang
 * 										- Added method asListener
 * 										- Renamed method customClass into asCustomClass
 * 										- Renamed method anonymousClass into asAnonymousClass
 * 									 12.19.2007 Wolfgang
 *                    - Added method asVar
 *                    - Added logic for anonymousClass
 *                    - Changed parameters handling 
 *                   12.18.2007 Wolfgang
 *                    - Added sf_extjs2_comment
 *                   12.17.2007 Leon:
 *                    - handling of inner (recursive) arrays (see items => array(array(...), array(...))
 *                   12.17.2007 Kubens:
 *                    - Added handling of boolean values
 *                    - Fixed quoting logic for beginClass
 *                    - Fixed quoting logic for beginApplication
 *                   12.15.2007 Kubens:
 *                     - Overworked quoting logic
 *                   11.22.2007 Kubens:
 *                     - Added features to create application
 *                     - Added parameters support for Ext.object constructors
 *                    11.17.2007 Kubens:
 *                     - Added features to create custom classes and custom methods
 *                    11.12.2007  Kubens:
 *                     - Fixed loading order of adapters. If adapters are used then it is important to load
 *                       adapters and coresponding files before ext-all.js
 *                     - Overworked: load method. Adapters and themes are setuped in config.php
 *                     - Overworked: constructor. If no adapter or theme is passed then default
 *                       settings from config.php will used
 *                    11.07.2007 Benjamin:
 *                     - Fixed the adapter includes to load all required files in the correct order
 *                       moved ext-base into adapters, pass ext as adapter for standalone
 *                       changed all javascript to load first so they will come before files specified in view.yml
 *                   07.15.2007 Kubens:
 *                     - created
 */
class sfExtjs2Plugin {

  const LBR    = "\n";
  const LBR_CM = ",\n";
  const LBR_SM = ";\n";
  
  private $items     = array();
  private $adapter   = ''; // current adapter
  private $theme     = ''; // current theme
  private $namespace = ''; // current namespace
  
  /**
   * Creates an instance of sfExtjs2Plugin.
   *
   * Usage:
   *
   *   $sfExtjs2Plugin = new sfExtjs2Plugin(
   *                           array
   *                           (
   *                             'adapter' => 'jquery', // config.sf_extjs2_adapters
   *                             'theme'   => 'gray'    // config.sf_extjs2_themes
   *                           )
   *                         );
   *
   * @param array options
   */
  public function __construct($options = array())
  {
    $this->adapter = is_array($options) && array_key_exists('adapter',$options) && array_key_exists($options['adapter'], sfConfig::get('sf_extjs2_adapters', array())) ? $options['adapter'] : sfConfig::get('sf_extjs2_default_adapter');
    $this->theme   = is_array($options) && array_key_exists('theme',$options)   && array_key_exists($options['theme'],   sfConfig::get('sf_extjs2_themes', array()))   ? $options['theme']   : sfConfig::get('sf_extjs2_default_theme');
  }

  /**
   * If method does not exists and method is listed in
   * config.sf_extjs2_classes then Extjs2.class.constructor will rendered.
   *
   * Usage:
   *
   *   $sfExtjs2Plugin = new sfExtjs2Plugin(
   *                           array
   *                           (
   *                             'adapter' => 'jquery',
   *                             'theme'   => 'gray'
   *                           )
   *                         );
   *   $sfExtjs2Plugin->Window(
   *                      array
   *                      (
   *                        'title'  => 'Window Title',
   *                        'border' => false,
   *                        'width'  => 600,
   *                        'height' => 500
   *                      )
   *                    );
   *
   * @param string class
   * @param array attributes
   * @return string Javascript source of Extjs2.class
   */
  public static function __call ($class, $attributes)
  {
    $classes = sfConfig::get('sf_extjs2_classes');
    if (is_array($classes) && array_key_exists($class, $classes))
    {
      $object = sfConfig::get($classes[$class]);
      return sfExtjs2Plugin::getExtObject($object['class'], $attributes[0]);
    }
  }

  /**
   * Creates Javascript source for Extjs2.class
   *
   * Usage:
   * 
   *   Syntax A = short form without any options
   *   $sfExtjs2Plugin->Object(array
   *   (
   *     'id'       => 'id',
   *     'renderTo' => $sfExtjs2Plugin->asVar('document.body'),
   *     'items'    => array
   *     (
   *       $sfExtjs2Plugin->Object(array('title'=>'Object A')),
   *       $sfExtjs2Plugin->Object(array('title'=>'Object B'))
   *     )
   *   ));
   * 
   *   => new Object({id: 'id', renderTo: document.body, items: [new Object(title: 'Object A'), new Object(title: 'Object B')]})   
   *
   *
   *   Syntax B = long form with additional options
   *   $sfExtjs2Plugin->Object(array
   *   (
   *     'name'       => 'string',      // option to render Javascript variable
   *     'parameters' => array
   *      (
   *        'parameter1',
   *        'parameter2'
   *      ),
   *     'attributes' => array          // attributes for Ext constructor
   *     (
   *       'id'       => 'id',
   *       'renderTo' => 'document.body',
   *       'items'    => array
   *       (
   *         $sfExtjs2Plugin->Object(array('title' => 'Object A')),
   *         $sfExtjs2Plugin->Object(array('title' => 'Object B'))
   *       )
   *     )
   *   ));
   *   
   *   => new Object(parameter1, parameter2, {id: 'id', renderTo: document.body, items: [new Object(title: 'Object A'), new Object(title: 'Object B')]})   
   *
   * @param string class
   * @param array attributes
   * @return string source
   */
  public static function getExtObject($class, $attributes = array())
  {
    # parameters for constructor
    $parameters = array();
    if (is_array($attributes) && array_key_exists('parameters', $attributes))
    {
      $parameters = $attributes['parameters'];
      unset($attributes['parameters']);
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
      if (array_key_exists($attribute, $attributes['attributes']) && !$attributes['attributes'][$attribute] instanceof sfExtjs2Var)
      {
        $attributes['attributes'][$attribute] = sprintf('[%s]', sfExtjs2Plugin::_build_attributes($attributes['attributes'][$attribute]));
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
   * Creates Javascript source for Extjs2.class
   *
   * @param array attributes
   * @param array config
   * @param array parameters
   * @return string source
   */
  public static function getExtObjectComponent($attributes = array(), $config = array(), $parameters = array())
  {
    $attributes = sfExtjs2Plugin::_build_attributes($attributes, $config['attributes']);
    $attributes = sprintf('%s', $attributes != '' ? '{'.$attributes.'}' : '');
  
    $parameters = implode(',', $parameters);
  
    switch ($config['class'])
    {
      case 'anonymousClass':
        $source = sprintf(
          '%s%s',
          $parameters != '' ? $parameters . ',' : '',
          $attributes
        );
        return $source;

      case 'customClass':
        $source = sprintf(
          '{%s}',
          $attributes
        );
        return $source;

      default:
        $source = sprintf(
          'new %s (%s%s%s)',
          $config['class'],
          $parameters != '' ? '['.$parameters.']' : '',
          $parameters != '' && $attributes != '' ? ',' : '',
          $attributes
        );
        return $source;
    }
  
  }

  /**
   * add sources for css and js to html head
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
   * @return string source
   */
  public function begin()
  {
    $source  = sfExtjs2Plugin::LBR;
    $source .= sprintf("<script type='text/javascript'>%s", sfExtjs2Plugin::LBR);
    $source .= sfExtjs2Plugin::_comment(sprintf("%s// sfExtjs2Helper: %s%s", sfExtjs2Plugin::LBR, sfConfig::get('sf_extjs2_version'), sfExtjs2Plugin::LBR));
    $source .= sprintf("Ext.BLANK_IMAGE_URL = '%s'%s", sfConfig::get('sf_extjs2_spacer'), sfExtjs2Plugin::LBR_SM);
  
    echo $source;
  }

  /**
   * writes closing tag for javascript
   *   * 
   * @param  string source
   * @return Javascript source
   */
  public function end($source = '')
  {
    $source  = sprintf("%s%s%s", sfExtjs2Plugin::LBR, $source, $source != '' ? sfExtjs2Plugin::LBR : '');
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
   * @return string source
   */
  public function beginClass($namespace = null, $classname = null, $extend = null, $attributes = array())
  {
    $source = '';
  
    // write namespace directive
    // prevent double output of namespace directive
    if ($this->namespace !== $namespace)
    {
      $this->namespace = $namespace;
      $source .= sfExtjs2Plugin::_comment(sprintf("%s// namespace: %s%s", sfExtjs2Plugin::LBR, $namespace, sfExtjs2Plugin::LBR));
      $source .= sprintf("Ext.namespace('%s')%s", $namespace, sfExtjs2Plugin::LBR_SM);
    }
  
    // write class tag
    $source .= sfExtjs2Plugin::_comment(sprintf("%s// class: %s.%s%s", sfExtjs2Plugin::LBR, $namespace, $classname, sfExtjs2Plugin::LBR));
    $source .= sprintf("%s.%s = Ext.extend(%s, {%s", $namespace, $classname, $extend, sfExtjs2Plugin::LBR);
  
    // write attributes
    $source .= sfExtjs2Plugin::_build_attributes($attributes);
  
    echo $source;
  }

  /**
   * writes closing class tag
   *
   * @return Javascript source
   */
  public function endClass()
  {
    $source  = '';
    $source .= sprintf("})%s%s", sfExtjs2Plugin::LBR_SM, sfExtjs2Plugin::LBR_SM);

    echo $source;
  }

  /**
   * writes begining application tag
   *
   * @param attributes['name']
   * @param attributes['private']
   * @param attributes['public']
   * @return string source
   */
   public function beginApplication($attributes = array())
   {
     // private attributes
     $sourcePrivate = '';
     if (array_key_exists('private', $attributes))
     {
       foreach ($attributes['private'] as $key => $value)
       {
         $sourcePrivate .= sprintf("%svar %s = %s;", sfExtjs2Plugin::LBR, $key, sfExtjs2Plugin::_quote($key, $value));
       }
     }

     // public attributes
     $sourcePublic = '';
     if (array_key_exists('public', $attributes))
     {
       // write attributes
       $sourcePublic .= sfExtjs2Plugin::_build_attributes($attributes['public']);
     }

     // write application syntax
     $source  = '';
     $source .= sfExtjs2Plugin::_comment(sprintf("%s// application: %s%s", sfExtjs2Plugin::LBR, $attributes['name'], sfExtjs2Plugin::LBR));
     $source .= sprintf(
       'var %s = function() { %sreturn {%s%s %s %s',
       $attributes['name'],
       sfExtjs2Plugin::LBR,
       sfExtjs2Plugin::LBR,
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
    $source .= sprintf("%s}}()%s", sfExtjs2Plugin::LBR, sfExtjs2Plugin::LBR_SM);
  
    echo $source;
  }

  /**
   * returns source of custom class
   * 
   * Usage:
   * 
   * 		$sfExtjs2Plugin->customClass('Ext.app.symfony.ModuleA', array('title' => 'Module A', 'closable' => false));
   * 
   * 		=> new Ext.app.symfony.ModuleA ({title:'Module A',closable:false})
   *
   * @param string classname
   * @package array attributes
   * @return string source
   */
  public function asCustomClass($classname, $attributes = array())
  {
    $source  = '';
    $source .= $this->getExtObjectComponent($attributes, array('attributes'=>array(), 'class'=>$classname));

    return new sfExtjs2Var($source);
  }

  /**
   * returns source of anonymous class
   * 
   * Usage:
   * 
   * 		$sfExtjs2Plugin->asAnonymousClass(array('name'=>'id','mapping'=>'id','type'=>'int'));
   * 
   * 		=> {name: 'id', mapping: 'id', type: 'int'}
   *
   * @param string classname
   * @package array attributes
   * @return string source
   */
  public function asAnonymousClass($attributes = array())
  {
    $source  = '';
    $source .= $this->getExtObject('anonymousClass', $attributes);

    return new sfExtjs2Var($source);
  }

  /**
   * returns source of anonymous listener
   * 
   * Usage:
   * 
   * 		$sfExtjs2Plugin->asListener(array
   * 		(
   *      'rowcontextmenu' => $sfExtjs2Plugin->asMethod(array
   *      (
   *        'parameters' => 'grid, rowIndex, e',
   *        'source'     => "
   *          
   * 					// ensure that row could reselect
   *          // if onLoad event of data store occurs
   *          grid.selectedRowIndex = rowIndex;
   *          grid.getSelectionModel().selectRow(rowIndex);
   *
   *          // prevent browser default context menu
   *          e.stopEvent();
   * 
   *          // show context menu
   *          var coords = e.getXY();
   *          grid.cmenu.showAt([coords[0], coords[1]]);
   *        "
   *      ))
   *    ))
   *
   * @param string classname
   * @package array attributes
   * @return string source
   */
  public function asListener($attributes = array())
  {
    $source = '';
    foreach ($attributes as $key => $value) 
    {
      $source .= sprintf
      (
        '%s"%s":%s',
        $source != '' ? ',' : '',
        $key,
        $value 
      );    
    }
    $source = sprintf('{%s}', $source);
    
    return new sfExtjs2Var($source);
  }
    
  /**
   * returns string the passed string without additional quoting
   *
   * @param string var
   * @return sfExtjs2Var var
   */
  public static function asVar($var)
  {
    return new sfExtjs2Var($var);
  }

  /**
   * returns source for method including output of evaled php code
   * 
   * Usage:
   * 
   *    Syntax A = short form without any options
   *    $sfExtjs2Plugin->asMethod('alert("foo");');
   * 
   * 		=> function() { alert("foo"); }
   *    
   *    Syntax B = short form with parameters
   *    $sfExtjs2Plugin->asMethod(array('parameters' => 'msg', 'source' => 'alert(msg)');
   * 
   * 		=> function(msg) { alert(msg); }
   * 
   * @param array attributes
   * @return string source
   */
  public static function asMethod($attributes = array())
  {
    $source = is_array($attributes) && array_key_exists('source', $attributes) ? $attributes['source'] : $attributes;
    $source = preg_replace_callback(
      '/(\<\?php)(.*?)(\?>)/si',
      array('self', '_methodEvalPHP'),
      $source
    );
    $source = sprintf("function (%s) { %s }", is_array($attributes) && array_key_exists('parameters', $attributes) ? $attributes['parameters'] : '', $source);
  
    return new sfExtjs2Var($source);
  }

  /**
   * returns output of evaled php code
   *
   * @param array matches
   * @return string source
   */
  private static function _methodEvalPHP ($matches)
  {
    $source = str_replace( array('<?php', '<?', '?>'), '', $matches[0]);
    ob_start();
    eval($source);
    $source = ob_get_contents();
    ob_end_clean();
  
    return $source;
  }

  /**
   * Build attributes based on custom attributes and default attributes.
   * Custom attributes and default attributes will merged.
   * Custom attributes overwrites default attributes.
   *
   * Usage:
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
   */
  private static function _build_attributes ($custom_attributes = array(), $default_attributes = array())
  {
    $attributes = '';
  
    $merged_attributes = $default_attributes;
    if (is_array($custom_attributes) && is_array($default_attributes))
    {
      $merged_attributes = array_merge($default_attributes, $custom_attributes);
    }
  
    foreach ($merged_attributes as $key => $value)
    {
      if (!is_numeric($key))
      {
        $attributes .= sprintf('%s%s:%s', ($attributes === '' ? '' : ','), $key, sfExtjs2Plugin::_quote($key, $value));
      }
      else
      {
        $attributes .= sprintf('%s%s', ($attributes === '' ? '' : ','), sfExtjs2Plugin::_quote($key, $value));
      }
    }
  
    return $attributes;
  }
    
  /**
   * quotes everything except:
   *   values that are arrays
   *   values that are sfExtjs2Var
   *   values and keys that are listed in sf_extjs2_quote_except
   *
   * @param string key
   * @param string value
   * @return string attribute
   */
  private static function _quote($key, $value)
  {
    if (is_array($value))
    {
      $attribute = '';
      foreach ($value as $k => $v)
      {
        if (!is_numeric($k))
        {
          $attribute .= sprintf('%s%s:%s', ($attribute === '' ? '' : ','), $k, sfExtjs2Plugin::_quote($k, $v));
        }
        else
        {
          $attribute .= sprintf('%s%s', ($attribute === '' ? '' : ','), sfExtjs2Plugin::_quote($k, $v));
        }
      }
  
      $attribute = sprintf('{%s}', $attribute);
      return $attribute;
    }
  
    if (is_bool($value ))
    {
      $attribute = $value ? 'true' : 'false';
      return $attribute;
    }
  
    if (!$value instanceof sfExtjs2Var && sfExtjs2Plugin::_quote_except($key, $value))
    {
      $attribute = '\''.$value.'\'';
      return $attribute;
    }
  
    $attribute = $value;
    return $attribute;
  }

  /**
   * @param string key
   * @param string value
   * @return boolean quote
   */
  private static function _quote_except($key, $value)
  {
    $quoteExcept = sfConfig::get('sf_extjs2_quote_except');
  
    if (is_int($key) || is_int($value))
    {
      return false;
    }
  
    $listAttributes = sfConfig::get('sf_extjs2_list_attributes');
    if (in_array($key, $listAttributes))
    {
      return false;
    }
  
    foreach ($quoteExcept['key'] as $except)
    {
      if ($key == $except)
      {
        return false;
      }
    }
  
    foreach ($quoteExcept['value'] as $except)
    {
      if (substr($value, 0, strlen($except)) == $except)
      {
        return false;
      }
    }
  
    return true;
  }

  /**
   * @param string comment
   * @return string comment
   */
  private static function _comment($comment)
  {
    if (sfConfig::get('sf_extjs2_comment'))
    {
      return $comment;
    }
    else
    {
      return '';
    }
  }

}  

/**
 * @class            sfExtjs2Var
 * @description      sfExtjs2Var is used by quoting logic which ignores variables of this class
 * @author           Leon van der Ree
 * @version          0.0.01
 * @last modified    12.13.2007 Leon:
 *                     - created
 */
class sfExtjs2Var {

  private $var = '';

  public function __construct($var)
  {
    $this->var = $var;
  }

  public function __toString()
  {
    return $this->var;
  }

}



?>