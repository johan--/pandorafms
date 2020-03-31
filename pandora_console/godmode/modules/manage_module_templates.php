<?php

// Pandora FMS - http://pandorafms.com
// ==================================================
// Copyright (c) 2005-2020 Artica Soluciones Tecnologicas
// Please see http://pandorafms.org for full contribution list
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation for version 2.
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// Load global vars
// TESTING
    /*
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    error_reporting(E_ALL); */
// END
global $config;
/*
    hd($_POST);
hd($_REQUEST); */
check_login();

if (! check_acl($config['id_user'], 0, 'PM')) {
    db_pandora_audit(
        'ACL Violation',
        'Trying to access Network Profile Management'
    );
    include 'general/noaccess.php';
    return;
}


require_once $config['homedir'].'/include/class/ModuleTemplates.class.php';
$ajaxPage = ENTERPRISE_DIR.'/godmode/agentes/ModuleTemplates';
// Control call flow.
try {
    // User access and validation is being processed on class constructor.
    $moduleTemplates = new ModuleTemplates('');
} catch (Exception $e) {
    echo '[ModuleTemplates]'.$e->getMessage();
    /*
        if (is_ajax()) {
        echo json_encode(['error' => '[MiFuncionalidad]'.$e->getMessage() ]);
        exit;
        } else {
        echo '[MiFuncionalidad]'.$e->getMessage();
        }
    */
    // Stop this execution, but continue 'globally'.
    return;
}

// AJAX controller.
if (is_ajax()) {
    $method = get_parameter('method');
    /*
        if (method_exists($miFuncionalidad, $method) === true) {
        if ($miFuncionalidad>ajaxMethod($method) === true) {
            $miFuncionalidad>{$method}();
        } else {
            $miFuncionalidad>error('Unavailable method.');
        }
        } else {
        $miFuncionalidad->error('Method not found. ['.$method.']');
        }
    */
    // Stop any execution.
    exit;
} else {
    // Run.
    $moduleTemplates->run();
    // Get the id_np.
    $id_np = $moduleTemplates->getIdNp();
    $moduleTemplates->processData();
    if ($id_np === -1) {
        // List all Module Blocks.
        $moduleTemplates->moduleBlockList();
    } else {
        // Create new o update Template.
        $moduleTemplates->moduleTemplateForm();
    }
}

?>
<script>
    $(document).ready (function () {
        //Close button
        $("[id*=checkbox-switch]").click (function () {
            $("[id*=checkbox-block_id]").toggleClass('alpha50');
        });
    });
</script>