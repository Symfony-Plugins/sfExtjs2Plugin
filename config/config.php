<?php
sfConfig::set('sf_extjs2_js_dir', '/sfExtjs2Plugin');
sfConfig::set('sf_extjs2_css_dir', '/sfExtjs2Plugin/resources/css/');
sfConfig::set('sf_extjs2_images_dir', '/sfExtjs2Plugin/resources/images/');
sfConfig::set('sf_extjs2_spacer', '/sfExtjs2Plugin/resources/images/default/s.gif');
sfConfig::set('sf_extjs2_list_attributes', array('items', 'tbar', 'buttons'));

sfConfig::set('classes',
  array(
    'Button'     => 'Ext.Button',
    'Panel'      => 'Ext.Panel',
    'TabPanel'   => 'Ext.TabPanel',
    'Viewport'   => 'Ext.Viewport',
    'Window'     => 'Ext.Window',
    'FormPanel'  => 'Ext.FormPanel',
    'DateField'  => 'Ext.form.DateField',
    'TextField'  => 'Ext.form.TextField',
    'TimeField'  => 'Ext.form.TimeField',
    'HtmlEditor' => 'Ext.form.HtmlEditor'
  )
);

sfConfig::set('Ext.Button',
  array(
    'prefix'      => 'btn',
    'class'       => 'Ext.Button',
    'attributes'  => array('text' => '"Default Text"')
  )
);

sfConfig::set('Ext.Panel',
  array(
    'prefix'      => 'pnl',
    'class'       => 'Ext.Panel',
    'attributes'  => array('title' => '"Default Title"')
  )
);

sfConfig::set('Ext.TabPanel',
  array(
    'prefix'      => 'tab',
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
    'prefix'      => 'vprt',
    'class'       => 'Ext.Viewport',
    'attributes'  => array('layout' => '"border"')
  )
);

sfConfig::set('Ext.Window',
  array(
    'prefix'      => 'wnd',
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
    'prefix'      => 'frm',
    'class'       => 'Ext.FormPanel',
    'attributes'  => array()
  )
);

sfConfig::set('Ext.form.DateField',
  array(
    'prefix'      => 'dfld',
    'class'       => 'Ext.form.DateField',
    'attributes'  => array('fieldLabel' => '"Default FieldLabel"')
  )
);

sfConfig::set('Ext.form.TextField',
  array(
    'prefix'      => 'txtfld',
    'class'       => 'Ext.form.TextField',
    'attributes'  => array('fieldLabel' => '"Default FieldLabel"')
  )
);

sfConfig::set('Ext.form.TimeField',
  array(
    'prefix'      => 'tmfld',
    'class'       => 'Ext.form.TimeField',
    'attributes'  => array('fieldLabel' => '"Default FieldLabel"')
  )
);

sfConfig::set('Ext.form.HtmlEditor',
  array(
    'prefix'      => 'htmledtr',
    'class'       => 'Ext.form.HtmlEditor',
    'attributes'  => array('fieldLabel' => '"Default FieldLabel"')
  )
);

?>
