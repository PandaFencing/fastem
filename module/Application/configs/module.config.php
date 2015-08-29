<?php
return [
  'router' => [
      'routes' => [
          'type' => 'literal',
          'options' => [
              'route' => '/',
              'defaults' => [
                  'controller' => 'Application\Controller\Index',
                  'action' => 'index'
              ]
          ]
      ]
  ],
  'controllers' => [
      'invokables' => [
          'Application\Controller\Index' => 'Application\Controller\IndexController'
      ]
  ],
  'view_manager' => [
      'display_not_found_reason' => true,
      'display_exceptions' => true,
      'not_found_template' => 'error/404',
      'exception_template' => 'error/index',
      'template_map' => [
          'layout/layout' => __DIR__.'/../view/layout.phtml',
          'error/404' => __DIR__.'/../view/error/404.phtml',
          'error/index' => __DIR__.'/../view/error/index.phtml'
      ],
      'template_path_stack' => [
          __DIR__.'/../view'
      ]
  ]
];