<?php
/*
 * @version $Id: auth.others.php 22656 2014-02-12 16:15:25Z moyo $
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2014 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

/** @file
* @brief
*/

include ('../inc/includes.php');

Session::checkRight("config", UPDATE);

$config = new Config();

//Update CAS configuration
if (isset($_POST["update"])) {
   $_POST['id'] = 1;
   $config->update($_POST);
   Html::redirect($CFG_GLPI["root_doc"] . "/front/auth.others.php");
}

Html::header(__('External authentication sources'), $_SERVER['PHP_SELF'], "config", "auth", "others");

Auth::showOtherAuthList();

Html::footer();
?>