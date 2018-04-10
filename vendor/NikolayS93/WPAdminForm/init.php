<?php

namespace NikolayS93\WPAdminForm;

if ( ! defined( 'ABSPATH' ) )
  exit; // With wordpress only

if( class_exists('NikolayS93\WPAdminForm\Init') ) {
    include_once __DIR__ . '/src/Scaffolding.php';
    include_once __DIR__ . '/src/Active.php';
    include_once __DIR__ . '/src/Form.php';

    include_once __DIR__ . '/src/Entry.php';
    include_once __DIR__ . '/src/Input.php';
    include_once __DIR__ . '/src/Defaults.php';
    include_once __DIR__ . '/src/Utils.php';
}
