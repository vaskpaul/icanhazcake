<?php
$config = array (
  'debug' => 2,
  'App' => 
  array (
    'fullBaseUrl' => 'http://todo.90grad.de',
    'imageBaseUrl' => 'img/',
    'cssBaseUrl' => 'css/',
    'jsBaseUrl' => 'js/',
    'base' => false,
    'baseUrl' => false,
    'dir' => 'app',
    'webroot' => 'webroot',
    'www_root' => '/home/todoapp/html/app/webroot/',
    'encoding' => 'UTF-8',
  ),
  'Error' => 
  array (
    'handler' => 'ErrorHandler::handleError',
    'level' => 22527,
    'trace' => true,
  ),
  'Exception' => 
  array (
    'handler' => 'ErrorHandler::handleException',
    'renderer' => 'ExceptionRenderer',
    'log' => true,
  ),
  'Session' => 
  array (
    'defaults' => 'php',
  ),
  'Security' => 
  array (
    'salt' => 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi',
    'cipherSeed' => '76859309657453542496749683645',
  ),
  'Acl' => 
  array (
    'classname' => 'DbAcl',
    'database' => 'default',
  ),
  'Dispatcher' => 
  array (
    'filters' => 
    array (
      0 => 'AssetDispatcher',
      1 => 'CacheDispatcher',
    ),
  ),
  'status' => 
  array (
    0 => 'todo',
    1 => 'doing',
  ),
  'categories' => 
  array (
  ),
  'tags' => 
  array (
  ),
);