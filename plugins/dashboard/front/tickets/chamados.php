<?php

define('GLPI_ROOT', '../../../..');
include (GLPI_ROOT . "/inc/includes.php");
include "../inc/functions.php";
global $DB, $CFG_GLPI;

Session::checkLoginUser();
Session::checkRight("profile", READ);
?>

<html> 
<head>
<title> GLPI - <?php echo __('Open Tickets','dashboard'); ?> </title>
<!-- <base href= "<?php $_SERVER['SERVER_NAME'] ?>" > -->
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="content-language" content="en-us" />
<meta http-equiv="refresh" content= "45"/>

<link rel="icon" href="../img/dash.ico" type="image/x-icon" />
<link rel="shortcut icon" href="../img/dash.ico" type="image/x-icon" />
<link href="../css/styles.css" rel="stylesheet" type="text/css" />
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="../css/bootstrap-responsive.css" rel="stylesheet" type="text/css" />

<script src="../js/jquery.min.js" type="text/javascript" ></script>
<script src="../js/jquery.jclock.js"></script>

<script src="../js/media/js/jquery.dataTables.min.js"></script>
<link href="../js/media/css/dataTables.bootstrap.css" type="text/css" rel="stylesheet" />  
<script src="../js/media/js/dataTables.bootstrap.js"></script> 

<script src="../js/extensions/ColVis/css/dataTables.colVis.min.css"></script>
<script src="../js/extensions/ColVis/js/dataTables.colVis.min.js"></script>

<script src="../lib/sweet-alert.min.js"></script>
<link href="../lib/sweet-alert.css" type="text/css" rel="stylesheet" />

<script type="text/javascript">
	$(function($) {
		var options = {
		timeNotation: '24h',
		am_pm: true,
		fontSize: '38px'
	}
		$('#clock').jclock(options);
	});
	
</script>
<?php echo '<link rel="stylesheet" type="text/css" href="../css/style-'.$_SESSION['style'].'">';  

$status = "('5','6')";

//$ent = $_REQUEST['ent'];
$entities = Profile_User::getUserEntities($_SESSION['glpiID'], true);
$ent = implode(",",$entities);

// contar chamados abertos
$sql = "SELECT COUNT(glpi_tickets.id) AS total
FROM glpi_tickets
WHERE glpi_tickets.status NOT IN ".$status."
AND glpi_tickets.is_deleted = 0
AND glpi_tickets.entities_id IN (".$ent.")" ;

$result = $DB->query($sql);
$data = $DB->fetch_assoc($result);

$abertos = $data['total']; 

//insert if not exist entity
$query_i = "
INSERT IGNORE INTO glpi_plugin_dashboard_count (type, id, quant) 
VALUES ('1','". $ent ."', '" . $abertos ."')  ";

$result_i = $DB->query($query_i);

// get quantity
$query = "SELECT quant 
FROM glpi_plugin_dashboard_count
WHERE id IN (".$ent.")
AND type = 1 ";

$result = $DB->query($query);
$quant = $DB->fetch_assoc($result);

$atual = $quant['quant']; 

//update tickets count
$query_up = "UPDATE glpi_plugin_dashboard_count 
SET quant=".$data['total']."
WHERE id IN (".$ent.") 
AND type = 1 ";

$result_up = $DB->query($query_up);

?> 
</head>
<?php

if($abertos > $atual) {
	
	//modal alert			
	echo '<body style="background-color: #fff;" onload=\'swal("' . __('New ticket') . '!", "'. __('') .'","success")\'>';		
		
	//sound
	if($_SESSION['glpilanguage'] == "pt_BR") {	
	
	    // IE    
	    echo '<!--[if IE]>';
	    echo '<embed src="../sounds/novo_chamado.mp3" autostart="true" width="0" height="0" type="application/x-mplayer2"></embed>';
	    echo '<![endif]-->';   
	    // Browser HTML5    
	    echo '<audio preload="auto" autoplay>';
	    echo '<source src="../sounds/novo_chamado.ogg" type="audio/ogg"><source src="sounds/novo_chamado.ogg" type="audio/mpeg">';
	    echo '</audio>';
	}
	
	else {
	
	    // IE    
	    echo '<!--[if IE]>';
	    echo '<embed src="../sounds/new_ticket.mp3" autostart="true" width="0" height="0" type="application/x-mplayer2"></embed>';
	    echo '<![endif]-->';
	    // Browser HTML5   
	    echo '<audio preload="auto" autoplay>';
	    echo '<source src="../sounds/new_ticket.ogg" type="audio/ogg"><source src="sounds/new_ticket.ogg" type="audio/mpeg">';
	    echo '</audio>';
	}
}	

else {
   echo '<body style="background-color: #fff;">';	
	}


//contar chamados de hoje - today tickets
$datahoje = date("Y-m-d");

$sql = "
SELECT COUNT( * ) AS total
FROM glpi_tickets
WHERE glpi_tickets.date LIKE '".$datahoje."%'
AND glpi_tickets.is_deleted =0 
AND glpi_tickets.entities_id IN (".$ent.")" ;

$result = $DB->query($sql);
$hoje = $DB->fetch_assoc($result);


// chamados de ontem - yesterday tickets
$dataontem = date('Y-m-d', strtotime('-1 day'));

$sql = "
SELECT COUNT( * ) AS total
FROM glpi_tickets
WHERE glpi_tickets.date LIKE '".$dataontem."%'
AND glpi_tickets.is_deleted =0 
AND glpi_tickets.entities_id IN (".$ent.")" ;

$result = $DB->query($sql);
$ontem = $DB->fetch_assoc($result);

//$cham_ontem = "'Chamados de ontem: " . $ontem['total'] . "'";
if ($ontem['total'] > $hoje['total']) { $up_down = "../img/down.png"; }
if ($ontem['total'] < $hoje['total']) { $up_down = "../img/up.png"; }
if ($ontem['total'] == $hoje['total']) { $up_down = "../img/blank.gif"; }

//entity name
$query_name = "
SELECT name 
FROM glpi_entities
WHERE glpi_entities.id IN (".$ent.")" ;

$result_n = $DB->query($query_name);
$ent_name = $DB->result($result_n, 0, 'name');

?>

<div id="clock" style="align:right; position:absolute; margin-top:5px;"></div>

<div id='content'>
	<div id='container-fluid' > 
		<div id="head-cham" class="row-fluid">	
				<table style="width: 100%; margin-left: auto; margin-right: auto;" >
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td align="center"><span class="titulo_cham">GLPI - <?php echo __('Open Tickets','dashboard'); ?>:</span> <span class="total"> <?php echo "&nbsp; ".$abertos ; ?> </span> </td>
					</tr>
					<tr><td></td></tr>
					
					<table style="font-size:25pt; font-weight:bold; width: 100%; margin-left: auto; margin-right: auto;"><tr><td align="center"><span class="today"><a href="chamados.php" style="font-family: 'RobotoDraft',sans-serif;"> <?php echo __('Today Tickets','dashboard'); ?>: </a> 
						<a href="../../../../front/ticket.php" target="_blank" class="total" style="font-size: 32pt;"> <?php echo "&nbsp; ".$hoje['total'] ; ?> </a>
						<img src= <?php echo $up_down ;?>  class="up_down" alt="" style="margin-top: -10px;" title= <?php echo __('Yesterday','dashboard'). ':';  echo $ontem['total'] ;?>  > </span> </td></tr>
					</table>
				</table>
			<p></p>
			</div>	
		<div id="lista_chamados" class="well info_box row-fluid report" style="width: 95%; margin-top: 160px !important;">
		
		<?php 
		
		if(isset($_REQUEST['order'])) {
		
			$order1 = $_REQUEST['order'];
			
			switch($order1) {
				 case "td": $order = "ORDER BY glpi_tickets.id DESC"; break;
				 case "ta": $order = "ORDER BY glpi_tickets.id ASC"; break;
				 case "sd": $order = "ORDER BY glpi_tickets.status DESC, glpi_tickets.id ASC"; break;
				 case "sa": $order = "ORDER BY glpi_tickets.status ASC, glpi_tickets.id ASC"; break;
				 case "tid": $order = "ORDER BY glpi_tickets.name DESC"; break;
				 case "tia": $order = "ORDER BY glpi_tickets.name ASC"; break;
				 case "ted": $order = "ORDER BY glpi_tickets.id DESC"; break;
				 case "tea": $order = "ORDER BY glpi_tickets.id ASC"; break;
				 case "pd": $order = "ORDER BY glpi_tickets.priority DESC, glpi_tickets.date ASC"; break;
				 case "pa": $order = "ORDER BY glpi_tickets.priority ASC, glpi_tickets.date ASC"; break;	
		  		 case "dd": $order = "ORDER BY glpi_tickets.due_date DESC"; break;
				 case "da": $order = "ORDER BY glpi_tickets.due_date ASC"; break;	  	
				}	
			}
			
		else {
				$order = "ORDER BY glpi_tickets.date_mod DESC";
		}
					
				//select tickets				
				$sql_cham = "SELECT DISTINCT glpi_tickets.id, glpi_tickets.name AS descri, glpi_tickets.status AS status, 
				glpi_tickets.date_mod, glpi_tickets.priority, glpi_tickets.due_date AS duedate, glpi_tickets.locations_id AS lid
				FROM glpi_tickets_users, glpi_tickets, glpi_users
				WHERE glpi_tickets.status NOT IN  ".$status." 
				AND glpi_tickets.is_deleted = 0
				AND glpi_tickets.id = glpi_tickets_users.`tickets_id`
				AND glpi_tickets_users.`users_id` = glpi_users.id				
				AND glpi_tickets.entities_id IN (".$ent.")
				".$order."";
		
				//ORDER BY glpi_tickets.date_mod DESC
				$result_cham = $DB->query($sql_cham);
				
				//check due_date	
				$sql_due = "SELECT COUNT(glpi_tickets.id) AS count_due
				FROM glpi_tickets
				WHERE  glpi_tickets.status NOT IN (4,5,6) 
				AND glpi_tickets.is_deleted = 0
				AND glpi_tickets.due_date IS NOT NULL
				AND glpi_tickets.entities_id IN (".$ent.")" ;
						
				$result_due = $DB->query($sql_due);				
				$count_due = $DB->result($result_due,0,'count_due');
					
				//Show due date or location	
				$query_due = "SELECT value FROM glpi_plugin_dashboard_config WHERE name = 'duedate' AND users_id = ".$_SESSION['glpiID']." ";																
				$result_due = $DB->query($query_due);
				
				$show_due = $DB->result($result_due,0,'value');	
				
				$query_loc = "SELECT value FROM glpi_plugin_dashboard_config WHERE name = 'location' AND users_id = ".$_SESSION['glpiID']." ";																
				$result_loc = $DB->query($query_loc);
				
				$show_loc = $DB->result($result_loc,0,'value');
				
				if($show_due != 0) {
					if($count_due > 0) {
						$th_due = "<th style='text-align:center;'><a href='chamados.php?order=da'>&nbsp<font size=2.5pt; font-family='webdings'>&#x25BE;&nbsp;</font></a>". __('Due Date','dashboard')."<a href='chamados.php?order=dd'><font size=2.5pt; font-family='webdings'>&nbsp;&#x25B4;</font></a></th>";
					}			
				}
					
					
				echo "<table id='tickets' class='display' style='font-size: 20px; font-weight:bold;' cellpadding = 2px >				
				<thead>
					<tr class='up-down'>
						<th style='text-align:center;'><a href='chamados.php?&order=ta'>&nbsp<font size=2.5pt; font-family='webdings'>&#x25BE;&nbsp;</font></a>". __('Tickets','dashboard')."<a href='chamados.php?&order=td'><font size=2.5pt; font-family='webdings'>&nbsp;&#x25B4;</font></a></th>
						<th style='text-align:center;'><a href='chamados.php?&order=sa'><font size=2.5pt; font-family='webdings'>&#x25BE;&nbsp;</font></a>". __('Status')."<a href='chamados.php?&order=sd'><font size=2.5pt; font-family='webdings'>&nbsp;&#x25B4;</font></a></th>
						<th style='text-align:center;'>". __('Title')."</th>
						<th style='text-align:center;'>". __('Technician')."</th>
						<th style='text-align:center;'>". __('Requester')."</th>";
					
				if($show_loc == 1) {	
					echo	"<th style='text-align:center;'>". __('Location')."</th>";
					}	
					
				echo $th_due."									
						<th style='text-align:center;'><a href='chamados.php?&order=pa'>&nbsp<font size=2.5pt; font-family='webdings'>&#x25BE;&nbsp;</font></a>". __('Priority')."<a href='chamados.php?&order=pd'><font size=2.5pt; font-family='webdings'>&nbsp;&#x25B4;</font></a></th>
					</tr>
				</thead>
				<tbody>";
			
		
				while($row = $DB->fetch_assoc($result_cham)){ 
				
				$status1 = $row['status']; 
				
				if($status1 == "1" ) { $status1 = "new";} 
				if($status1 == "2" ) { $status1 = "assign";} 
				if($status1 == "3" ) { $status1 = "plan";} 
				if($status1 == "4" ) { $status1 = "waiting";} 
				if($status1 == "5" ) { $status1 = "solved";}  	            
				if($status1 == "6" ) { $status1 = "closed";}
				if($status1 == "13" ) { $status1 = "feedback";}
				if($status1 == "14" ) { $status1 = "waiting_list";}
				if($status1 == "15" ) { $status1 = "in_attendance";}
				
				//get technician
				$sql_tec = "SELECT glpi_tickets.id AS id, glpi_users.firstname AS name, glpi_users.realname AS sname
				FROM `glpi_tickets_users` , glpi_tickets, glpi_users
				WHERE glpi_tickets.id = glpi_tickets_users.`tickets_id`
				AND glpi_tickets.id = ". $row['id'] ."
				AND glpi_tickets_users.`users_id` = glpi_users.id
				AND glpi_tickets.entities_id IN (".$ent.")
				AND glpi_tickets_users.type = 2 ";
				    
				$result_tec = $DB->query($sql_tec);	
				$row_tec = $DB->fetch_assoc($result_tec);
				
				//get requester
				$sql_req = "SELECT glpi_tickets.id AS id, glpi_users.firstname AS name, glpi_users.realname AS sname
				FROM `glpi_tickets_users` , glpi_tickets, glpi_users
				WHERE glpi_tickets.id = glpi_tickets_users.`tickets_id`
				AND glpi_tickets.id = ". $row['id'] ."
				AND glpi_tickets_users.`users_id` = glpi_users.id
				AND glpi_tickets_users.type = 1
				AND glpi_tickets.entities_id IN (".$ent.")" ;
				    
				$result_req = $DB->query($sql_req);	
				$row_req = $DB->fetch_assoc($result_req);
				
				//get priority
				$sql_prio = "SELECT name, value
						FROM glpi_configs
						WHERE name LIKE 'priority_".$row['priority']."' ";
				
				$result_prio = $DB->query($sql_prio);	
				$row_prio = $DB->fetch_assoc($result_prio);	
				
				$priority = substr($row_prio['name'],9,10);
				
				if($priority == 1) {
					$prio_name = _x('priority', 'Very low'); }
				
				if($priority == 2) {
					$prio_name = _x('priority', 'Low'); }
					
				if($priority == 3) {
					$prio_name = _x('priority', 'Medium'); } 		
					
				if($priority == 4) {	
					$prio_name = _x('priority', 'High'); }
					
				if($priority == 5) {
					$prio_name = _x('priority', 'Very high'); } 	
					
				if($priority == 6) {
					$prio_name = _x('priority', 'Major'); } 			 				 					 	
		
				//get Location
				$sql_loc = "SELECT id, name
				FROM glpi_locations
				WHERE glpi_locations.id = ". $row['lid'] ." ";
				    
				$result_loc = $DB->query($sql_loc);	
				$row_loc = $DB->fetch_assoc($result_loc);					
		
				echo "
				<tr class='title'>
					<td style='text-align:center; vertical-align:middle;'> <a href=../../../../front/ticket.form.php?id=". $row['id'] ." target=_blank > <span >" . $row['id'] . "</span> </a></td>
					<td style='vertical-align:middle;'><span style='color:#000099';><img src=../../../../pics/".$status1.".png />  ".Ticket::getStatus($row['status'])."</span ></td>
					<td style='vertical-align:middle;'><a href=../../../../front/ticket.form.php?id=". $row['id'] ." target=_blank > <span >" . $row['descri'] . "</span> </a></td>
					<td style='vertical-align:middle;'><span >". $row_tec['name'] ." ".$row_tec['sname'] ."</span> </td>
					<td style='vertical-align:middle;'><span >". $row_req['name'] ." ".$row_req['sname'] ."</span> </td>";
					
				if($show_loc == 1) {
					echo "<td style='vertical-align:middle; text-align:center; font-size:14pt;'>" . $row_loc['name'] . "</td>";
				}
				
				if($show_due != 0) {
					if($count_due > 0) {
						$now = date("Y-m-d H:i");
						
						if($row['duedate'] < $now ) {
							echo "<td style='vertical-align:middle; font-size:14pt; color:red;'><span>". conv_data_hora($row['duedate']) ."</span> </td>";
							}
						else {
							echo "<td style='vertical-align:middle; font-size:14pt; color:green;'><span>". conv_data_hora($row['duedate']) ."</span> </td>";
							}	
					}
				}
					
				echo "								
					<td style='vertical-align:middle; text-align:center; background-color:". $row_prio['value'] .";'>" . $prio_name . "</td>
				</tr>"; 		 
				 } 
		 		 
				echo "</tbody>
						</table>"; ?>
			
			</div>
		</div>
</div>

<style type="text/css">
table.dataTable thead > tr > th {
    padding-right: 10px;
}
</style>


<script type="text/javascript" charset="utf-8">

$('#tickets')
	.removeClass( 'display' )
	.addClass('table table-striped table-bordered table-hover');

$(document).ready(function() {
    oTable = $('#tickets').dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "bFilter": false,
        //"aaSorting": [],
        "aaSorting": false,
        "bLengthChange": false,
        "bPaginate": false, 
        "iDisplayLength": 15,
    	  "aLengthMenu": [[15, 25, 50, 100, -1], [15, 25, 50, 100, "All"]],     	    	  
    	   
        "sDom": 'T<"clear">lfrtip', 

        "bSortCellsTop": false,
        "sAlign": "right"    	      	      	  		  
    });    
} );

</script>

</body>
</html>