<?php
sfConfig::set('sf_extjs2_version', 'v0.58');
sfConfig::set('sf_extjs2_comment', true);
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
      'adapter/ext/ext-base.js' //-debug
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
sfConfig::set('sf_extjs2_plugin_dir', '/sfExtjs2Plugin/');
sfConfig::set('sf_extjs2_js_dir', '/sfExtjs2Plugin/extjs/');
sfConfig::set('sf_extjs2_css_dir', '/sfExtjs2Plugin/extjs/resources/css/');
sfConfig::set('sf_extjs2_images_dir', '/sfExtjs2Plugin/extjs/resources/images/');
#
# spacer gif
#
sfConfig::set('sf_extjs2_spacer', sfConfig::get('sf_extjs2_images_dir').'default/s.gif');
#
# attributes which must handled as array
#
sfConfig::set('sf_extjs2_list_attributes', array('items', 'tbar', 'bbar', 'buttons', 'plugins', 'view', 'fields'));
#
# array values that don't need quotes
#
sfConfig::set('sf_extjs2_quote_except',
  array(
    'value' => array('true', 'false', 'new Ext.', 'function', 'Ext.'),
    'key'   => array('renderer', 'store', 'defaults', 'plugins', 'cm', 'ds', 'view', 'tbar', 'bbar')
  )
);
#
# mapping plugin method against class
#
sfConfig::set('sf_extjs2_classes',
  array(
    // data
    'JsonReader'    => 'Ext.data.JsonReader',
    'Store'         => 'Ext.data.Store',
    'GroupingStore' => 'Ext.data.GroupingStore',
    'HttpProxy'     => 'Ext.data.HttpProxy',
    'Template'      => 'Ext.Template',
    'XTemplate'     => 'Ext.XTemplate',
    // widgets
    'BoxComponent'      => 'Ext.BoxComponent',
    'Button'            => 'Ext.Button',
    'GridPanel'         => 'Ext.grid.GridPanel',
    'ColumnModel'       => 'Ext.grid.ColumnModel',
    'GridView'          => 'Ext.grid.GridView',
    'GroupingView'      => 'Ext.grid.GroupingView',
    'EditorGridPanel'   => 'Ext.grid.EditorGridPanel',
    'RowSelectionModel' => 'Ext.grid.RowSelectionModel',
    'Panel'             => 'Ext.Panel',
    'TabPanel'          => 'Ext.TabPanel',
    'FormPanel'         => 'Ext.FormPanel',
    'Viewport'          => 'Ext.Viewport',
    'Window'            => 'Ext.Window',
    'FieldSet'          => 'Ext.form.FieldSet',
    'Hidden'            => 'Ext.form.Hidden',
    'DateField'         => 'Ext.form.DateField',
    'TextField'         => 'Ext.form.TextField',
    'TimeField'         => 'Ext.form.TimeField',
    'HtmlEditor'        => 'Ext.form.HtmlEditor',
    'Menu'              => 'Ext.menu.Menu',
    'Item'	  		      => 'Ext.menu.Item',
    'CheckItem' 	      => 'Ext.menu.CheckItem',
    'Toolbar'           => 'Ext.Toolbar',
    'MenuButton'        => 'Ext.Toolbar.MenuButton',
    'Fill'              => 'Ext.Toolbar.Fill',
    'Separator'         => 'Ext.Toolbar.Separator',
    'Spacer'            => 'Ext.Toolbar.Spacer',
    'PagingToolbar'     => 'Ext.PagingToolbar',
    'MessageBox'        => 'Ext.MessageBox',
    'KeyMap'            => 'Ext.KeyMap'
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

sfConfig::set('Ext.data.GroupingStore',
  array(
    'class'       => 'Ext.data.GroupingStore',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.data.HttpProxy',
  array(
    'class'       => 'Ext.data.HttpProxy',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.Template',
  array(
    'class'       => 'Ext.Template',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.XTemplate',
  array(
    'class'       => 'Ext.XTemplate',
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

sfConfig::set('Ext.grid.GridView',
  array(
    'class'       => 'Ext.grid.GridView',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.grid.GroupingView',
  array(
    'class'       => 'Ext.grid.GroupingView',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.grid.EditorGridPanel',
  array(
    'class'       => 'Ext.grid.EditorGridPanel',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.grid.RowSelectionModel',
  array(
    'class'       => 'Ext.grid.RowSelectionModel',
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

sfConfig::set('Ext.FormPanel',
  array(
    'class'       => 'Ext.FormPanel',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.Viewport',
  array(
    'class'       => 'Ext.Viewport',
    'attributes'  => array('layout' => 'border')
  )
);

sfConfig::set('Ext.Window',
  array(
    'class'       => 'Ext.Window',
    'attributes'  => array(
      'constrain'   => 'true',
      'layout'      => 'fit',
      'width'       => '500',
      'height'      => '300',
      'closeAction' => 'hide',
      'plain'       => 'true'
    )
  )
);

sfConfig::set('Ext.form.FieldSet',
  array(
    'class'       => 'Ext.form.FieldSet',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.form.Hidden',
  array(
    'class'       => 'Ext.form.Hidden',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.form.DateField',
  array(
    'class'       => 'Ext.form.DateField',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.form.TextField',
  array(
    'class'       => 'Ext.form.TextField',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.form.TimeField',
  array(
    'class'       => 'Ext.form.TimeField',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.form.HtmlEditor',
  array(
    'class'       => 'Ext.form.HtmlEditor',
    'attributes'  => array()
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

sfConfig::set('Ext.menu.CheckItem',
  array(
    'class'       => 'Ext.menu.CheckItem',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.Toolbar',
  array(
    'class'       => 'Ext.Toolbar',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.Toolbar.MenuButton',
  array(
    'class'       => 'Ext.Toolbar.MenuButton',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.Toolbar.Fill',
  array(
    'class'       => 'Ext.Toolbar.Fill',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.Toolbar.Separator',
  array(
    'class'       => 'Ext.Toolbar.Separator',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.Toolbar.Spacer',
  array(
    'class'       => 'Ext.Toolbar.Spacer',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.PagingToolbar',
  array(
    'class'       => 'Ext.PagingToolbar',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.MessageBox',
  array(
    'class'       => 'Ext.MessageBox',
    'attributes'  => array()
  )
);


sfConfig::set('Ext.KeyMap',
  array(
    'class'       => 'Ext.KeyMap',
    'attributes'  => array()
  )
);


sfConfig::set('anonymousClass',
  array(
    'class'       => 'anonymousClass',
    'attributes'  => array()
  )
);

?>
