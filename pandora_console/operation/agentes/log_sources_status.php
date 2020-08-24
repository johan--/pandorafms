<?php

// Pandora FMS - http://pandorafms.com
// ==================================================
// Copyright (c) 2005-2009 Artica Soluciones Tecnologicas
// Please see http://pandorafms.org for full contribution list
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation for version 2.
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
global $config;

// Login check
check_login();

$agent_id = get_parameter_get('id_agente', 0);

$table = new stdClass();
$table->width = '100%';
$table->class = 'info_table';
$table->cellpadding = '0';
$table->cellspacing = '0';
$table->head = [];
$table->align = [];

$table->head[0] = "<span title='".__('Source')."'>".__('Source').'</span>';
$table->head[1] = "<span title='".__('Review')."'>".__('Review').'</span>';
$table->head[2] = "<span title='".__('Last contact')."'>".__('Last contact').'</span>';

$table->style = [];
$table->style['source'] = 'width: 80%;';

$table->data = [];

$row = [];


// Get most recent sources for active agent.
$sql = "select source, MAX(utimestamp) AS last_contact from tagent_module_log where id_agent=$agent_id GROUP BY source";

$logs = mysql_db_get_all_rows_sql($sql);

foreach ($logs as $log) {
    $row['source'] = $log['source'];
    $row['review'] = '<a href="javascript:void(0)">'.html_print_image('images/zoom.png', true, ['title' => __('Review in log viewer'), 'alt' => '', 'onclick' => "send_form('".$log['source'].'-'.$agent_id."')"]).'</a>';
    $row['last_contact'] = human_time_comparation($log['last_contact']);

    $table->data[] = $row;
}

ob_start();

if (!empty($table->data)) {
    echo '<div id="log_sources_status" style="width:100%;">';
    html_print_table($table);
    echo '</div>';
} else {
    ui_print_info_message(['no_close' => true, 'message' => __('No log sources found') ]);
    $log_sources_defined = false;
}

// Hidden form to perform post request to Log Viewer page when clicking on the Review field icon.
echo '<form method="POST" action="index.php?sec=estado&sec2=enterprise/operation/log/log_viewer" name="review_log_form" id="review_log_form" style="display: none;">';

html_print_input_hidden('agent_id', $agent_id, false);
html_print_input_hidden('source', null, false);
html_print_input_hidden('redirect_search', 1, false);


echo '</form>';

$html_content = ob_get_clean();

// Create controlled toggle content.
ui_toggle(
    $html_content,
    __('Log sources status'),
    'log_sources_status',
    !$log_sources_defined,
    false,
    '',
    'white_table_graph_content no-padding-imp'
);

?>

<script type="text/javascript">

function send_form(source) {
    var review_form = document.getElementById("review_log_form");
    var source_input = document.getElementById('hidden-source');

    source_input.value = source;

    review_form.submit();
}

</script>