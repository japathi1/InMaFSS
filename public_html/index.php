<?php

/* =================================================================================*\
  |* This file is part of InMaFSS                                                    *|
  |* InMaFSS - INformation MAnagement for School Systems - Keep yourself up to date! *|
  |* ############################################################################### *|
  |* Copyright (C) flx5                                                              *|
  |* E-Mail: me@flx5.com                                                             *|
  |* ############################################################################### *|
  |* InMaFSS is free software; you can redistribute it and/or modify                 *|
  |* it under the terms of the GNU Affero General Public License as published by     *|
  |* the Free Software Foundation; either version 3 of the License,                  *|
  |* or (at your option) any later version.                                          *|
  |* ############################################################################### *|
  |* InMaFSS is distributed in the hope that it will be useful,                      *|
  |* but WITHOUT ANY WARRANTY; without even the implied warranty of                  *|
  |* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                            *|
  |* See the GNU Affero General Public License for more details.                     *|
  |* ############################################################################### *|
  |* You should have received a copy of the GNU Affero General Public License        *|
  |* along with InMaFSS; if not, see http://www.gnu.org/licenses/.                   *|
  \*================================================================================= */

namespace InMaFSS;

$_ENV['SLIM_MODE'] = 'development';

require __DIR__ . '/../vendor/autoload.php';

Compability::magicQuotes();
$config = new Config();
Database::Initialize($config);

// http://docs.slimframework.com/
$app = New \SlimController\Slim(array(
    'templates.path' => __DIR__ . '/templates',
    'controller.class_prefix' => '\\InMaFSS\\Controller',
    'controller.class_suffix' => 'Controller',
    'controller.method_suffix' => 'Action',
    'controller.template_suffix' => 'tpl',
    'mode' => 'production',
    'view' => new \Slim\Views\Smarty()
        ));

$view = $app->view();
$view->setTemplatesDirectory(__DIR__ . '/../templates/');
$view->parserCompileDirectory = __DIR__ . '/../templates_c/';
$view->parserCacheDirectory = __DIR__ . '/../cache/';

$view->parserExtensions = array(
    __DIR__ . '/../vendor/slim/views/SmartyPlugins'
);

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'debug' => false
    ));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

$app->setName('InMaFSS');

$app->addRoutes(
        array('/' => array(
                'get' => 'Home:index'
            ),
            '/plan/' => array(
                'get' => 'Plan:index'
            )
        )
);

$app
        ->addControllerRoute("/plan/update/:type/:limit", "Plan:update")
        ->via('GET')->conditions(
        array(
            'type' => '(pupil|teacher)',
            'limit' => '[0-9]+'
        )
);

$app->run();