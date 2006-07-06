<?php
// Pandora - the Free monitoring system
// ====================================
// Copyright (c) 2004-2006 Sancho Lerena, slerena@gmail.com
// Copyright (c) 2005-2006 Artica Soluciones Tecnol�icas S.L, info@artica.es
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

// Cargamos variables globales
session_start();
include ("../include/config.php");
include ("../include/functions.php");
include("../include/functions_db.php");
include("../include/languages/language_".$language_code.".php");
echo '<style>';
include("../include/styles/pandora.css");
echo '</style>';
if (comprueba_login() == 0) {
	// Has access to this page ???
	if (isset($_GET["tipo"]) AND isset($_GET["id"])) {
		$tipo =entrada_limpia($_GET["tipo"]);
		$id_agente_modulo = entrada_limpia($_GET["id"]);
	}
	else {
		echo "<h3 class='error'>".$lang_label["graf_error"]."</h3>";
		exit;	
	}
	
	// Nota: En los intervalos, se han aumentado por encima de los 24 del grafico diario y los 7 del semanal para
	// que la grafica tenga mas resolucion. Cuanto mayor sea el intervalo mas tardara la grafica en generarse !!!.
	
	// TODO: Crear una variable para que se pueda utilizar como factor de resolucion de graficos y parametrizarlo en un
	// archivo de configuracion.
	
	$module_interval = give_moduleinterval($id_agente_modulo); 
	// Interval defined for this module or agent general interval, if interval for this specific module not defined
	$module_interval = $module_interval / 60; // Convert to resol / minute
	// Please be caution, interval now is in MINUTES not in seconds
	// interval is the number of rows that will store data. more rows, more resolution

	switch ($tipo) {
		case "mes": 	$intervalo = 30 * $config_graph_res;
				$intervalo_real = (43200 / $module_interval);
				if ($intervalo_real < $intervalo ){
					$intervalo = $intervalo_real;
				}
				echo "<img src='fgraph.php?id=".$id_agente_modulo."&color=6e90ff&tipo=sparse&periodo=43200&intervalo=".$intervalo."&label=".$lang_label["month_graph"]."' border=0>";
				break;

		case "dia": 	$intervalo = 24 * $config_graph_res;
				$intervalo_real = (1440 / $module_interval);
				if ($intervalo_real < $intervalo ){
					$intervalo = $intervalo_real;
				}
				echo "<img src='fgraph.php?id=".$id_agente_modulo."&color=f3c330&tipo=sparse&periodo=1440&intervalo=".$intervalo."&label=".$lang_label["day_graph"]."' border=0 alt=''>";
				break;
		case "semana": $intervalo = 28 * $config_graph_res;
				$intervalo_real = (10080 / $module_interval);
				if ($intervalo_real < $intervalo ) {
					$intervalo = $intervalo_real;
				}
				echo "<img src='fgraph.php?id=".$id_agente_modulo."&color=e366cd&tipo=sparse&periodo=10080&intervalo=".$intervalo."&label=".$lang_label["week_graph"]."' border=0 alt=''>";
		 		break;
		case "hora": $intervalo = 5 * $config_graph_res;
				$intervalo_real = 60 / $module_interval;
				if ($intervalo_real < $intervalo ) {
					$intervalo = $intervalo_real;
				}
				// $intervalo=20;
				echo "<img src='fgraph.php?id=".$id_agente_modulo."&color=40d840&tipo=sparse&periodo=60&intervalo=".$intervalo."&label=".$lang_label["hour_graph"]."' border=0 alt=''>";
				break;		

	}
} // Fin pagina

?>