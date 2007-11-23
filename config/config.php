<?php
sfConfig::set('sf_extjs2_version', 'v0.23');
#
# adapters
#
sfConfig::set('sf_extjs2_default_adapter', 'ext');
sfConfig::set('sf_extjs2_adapters',
  array(
    'jquery' => array(
      'adapter/jquery/jquery.js',
      'adapter/jquery/jquery-plugins.js',
      'adapter/jquery/ext-jquery-adapter.js'
    ),
		'prototype' => array(
      'adapter/prototype/prototype.js',
      'adapter/prototype/scriptaculous.js?load=effects.js',
      'adapter/prototype/ext-prototype-adapter.js'
		),
		'yui' => array(
      'adapter/yui/yui-utilities.js',
      'adapter/yui/ext-yui-adapter.js'
    ),
		'ext' => array(
      'adapter/ext/ext-base.js'
    )
  )
);
#
# themes
#
sfConfig::set('sf_extjs2_default_theme', 'aero');
sfConfig::set('sf_extjs2_themes',
  array(
    'aero' => array( ),
    'gray' => array( 'xtheme-gray.css' )
  )
);
#
# base directories
#
sfConfig::set('sf_extjs2_js_dir', '/sfExtjs2Plugin/');
sfConfig::set('sf_extjs2_css_dir', '/sfExtjs2Plugin/resources/css/');
sfConfig::set('sf_extjs2_images_dir', '/sfExtjs2Plugin/resources/images/');
#
# spacer gif
#
sfConfig::set('sf_extjs2_spacer', '/sfExtjs2Plugin/resources/images/default/s.gif');
#
# attributes which must handled as array
#
sfConfig::set('sf_extjs2_list_attributes', array('items', 'tbar', 'buttons'));
#
# mapping plugin method against class 
#
sfConfig::set('classes',
  array(
    // data
    'JsonReader'	 => 'Ext.data.JsonReader',
    'Store'				 => 'Ext.data.Store',
    'HttpProxy' 	 => 'Ext.data.HttpProxy',
    // widgets
    'BoxComponent' => 'Ext.BoxComponent',
    'Button'       => 'Ext.Button',
    'GridPanel'		 => 'Ext.grid.GridPanel',
    'ColumnModel'	 => 'Ext.grid.ColumnModel',
    'Panel'        => 'Ext.Panel',
    'TabPanel'     => 'Ext.TabPanel',
    'Viewport'     => 'Ext.Viewport',
    'Window'       => 'Ext.Window',
    'FormPanel'    => 'Ext.FormPanel',
    'DateField'    => 'Ext.form.DateField',
    'TextField'    => 'Ext.form.TextField',
    'TimeField'    => 'Ext.form.TimeField',
    'HtmlEditor'   => 'Ext.form.HtmlEditor',
    'Menu'	  		 => 'Ext.menu.Menu',
    'Item'	  		 => 'Ext.menu.Item'    
  )
);
#
# default setting for classes
#

#
# data
#
sfConfig::set('Ext.data.JsonReader',
  array(
    'class'       => 'Ext.data.JsonReader',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.data.Store',
  array(
    'class'       => 'Ext.data.Store',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.data.HttpProxy',
  array(
    'class'       => 'Ext.data.HttpProxy',
    'attributes'  => array()
  )
);

#
# widgets
#
sfConfig::set('Ext.BoxComponent',
  array(
    'class'       => 'Ext.BoxComponent',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.Button',
  array(
    'class'       => 'Ext.Button',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.grid.GridPanel',
  array(
    'class'       => 'Ext.grid.GridPanel',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.grid.ColumnModel',
  array(
    'class'       => 'Ext.grid.ColumnModel',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.Panel',
  array(
    'class'       => 'Ext.Panel',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.TabPanel',
  array(
    'class'       => 'Ext.TabPanel',
    'attributes'  => array(
      'resizeTabs'      => 'true',
      'minTabWidth'     => '100',
      'tabWidth'        => '150',
      'activeTab'       => '0',
      'enableTabScroll' => 'true',
      'defaults'        => '{ autoScroll: true }'
    )
  )
);

sfConfig::set('Ext.Viewport',
  array(
    'class'       => 'Ext.Viewport',
    'attributes'  => array('layout' => '"border"')
  )
);

sfConfig::set('Ext.Window',
  array(
    'class'       => 'Ext.Window',
    'attributes'  => array(
      'constrain'   => 'true',
      'layout'      => '"fit"',
      'width'       => '500',
      'height'      => '300',
      'closeAction' => '"hide"',
      'plain'       => 'true'
    )
  )
);


sfConfig::set('Ext.FormPanel',
  array(
    'class'       => 'Ext.FormPanel',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.form.DateField',
  array(
    'class'       => 'Ext.form.DateField',
    'attributes'  => array('fieldLabel' => '"Default FieldLabel"')
  )
);

sfConfig::set('Ext.form.TextField',
  array(
    'class'       => 'Ext.form.TextField',
    'attributes'  => array('fieldLabel' => '"Default FieldLabel"')
  )
);

sfConfig::set('Ext.form.TimeField',
  array(
    'class'       => 'Ext.form.TimeField',
    'attributes'  => array('fieldLabel' => '"Default FieldLabel"')
  )
);

sfConfig::set('Ext.form.HtmlEditor',
  array(
    'class'       => 'Ext.form.HtmlEditor',
    'attributes'  => array('fieldLabel' => '"Default FieldLabel"')
  )
);

sfConfig::set('Ext.menu.Menu',
  array(
    'class'       => 'Ext.menu.Menu',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.menu.Item',
  array(
    'class'       => 'Ext.menu.Item',
    'attributes'  => array()
  )
);

?>
