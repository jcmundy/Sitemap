<?php
//$this->navigation()
//     ->sitemap()
//     ->setFormatOutput(true); // default is false

// other possible methods:
// ->setUseXmlDeclaration(false); // default is true
// ->setServerUrl('http://my.otherhost.com');
// default is to detect automatically

$nav = new Omeka_Navigation;
// $nav->loadAsOption(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME);
// $nav->addPagesFromFilter(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_FILTER_NAME);

$collections = get_db()->getTable('Collection')->findAll();
foreach($collections as $collection) {
    $page = new Omeka_Navigation_Page_Mvc(array(
        'label'      => metadata($collection,array('Dublin Core','Title')),
        'route'      => 'id',
        'action'     => 'show',
        'controller' => 'collections',
        'params'     => array('id' => $collection->id)
    ));
    
    $nav->addPage($page);
}

$items = get_db()->getTable('Item')->findBy(
    array('sort_field' => 'added', 'sort_dir' => 'a')
);
foreach($items as $item) {
    $page = new Omeka_Navigation_Page_Mvc(array(
        'label'      => metadata($item,array('Dublin Core','Title')),
        'route'      => 'id',
        'action'     => 'show',
        'controller' => 'items',
        'params'     => array('id' => $item->id)
    ));

    $nav->addPage($page);
}

if(plugin_is_active('SimplePages')){
    $simples = get_db()->getTable('SimplePagesPage')->findAll();
    foreach($simples as $simple) {
        $page = new Omeka_Navigation_Page_Mvc(array(
            'route'      => 'simple_pages_show_page_' . $simple->id,
            'action'     => '',
            'controller' => 'page',
            'params'     => array('slug' => $simple->slug)
        ));
        $nav->addPage($page);
    }
}

if(plugin_is_active('ExhibitBuilder')){
    $exhibits = get_db()->getTable('Exhibit')->findAll();
    foreach($exhibits as $exhibit) {
			if($exhibit->public == 1) {
      $exhibiturl = WEB_ROOT . "/exhibits/show/" . $exhibit->slug;
      $page = new Omeka_Navigation_Page_Mvc(array(
          'route'      => 'exhibitSimple',
          'action'     => 'show',
          'controller' => 'exhibits',
          'params'     => array('slug' => $exhibit->slug)
      ));
      $nav->addPage($page);
      $subpages = get_db()->getTable('ExhibitPage')->findall();
        foreach($subpages as $subpage) {
          if ($subpage->exhibit_id == $exhibit->id) {
            $sectionurl =  $exhibit->slug . '/' . $subpage->slug;
            $page = new Omeka_Navigation_Page_Mvc(array(
              'route'  => 'exhibitSimple',
              'action' => '',
              'controller' => 'exhibits',
              'params' => array('slug' => $sectionurl),
              'encode_url' => FALSE)
            );
            $nav->addPage($page);
            }
          }
        }
    }
}

echo $this->navigation()->sitemap($nav);
?>
