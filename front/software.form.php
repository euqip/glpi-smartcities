<?php
/*
 * @version $Id: software.form.php 23305 2015-01-21 15:06:28Z moyo $
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

Session::checkRight("software", READ);

if (!isset($_GET["id"])) {
   $_GET["id"] = "";
}
if (!isset($_GET["withtemplate"])) {
   $_GET["withtemplate"] = "";
}

$soft = new Software();
if (isset($_POST["add"])) {
   $soft->check(-1, CREATE,$_POST);

   if ($newID = $soft->add($_POST)) {
      Event::log($newID, "software", 4, "inventory",
                 sprintf(__('%1$s adds the item %2$s'), $_SESSION["glpiname"], $_POST["name"]));
      if ($_SESSION['glpibackcreated']) {
         Html::redirect($soft->getFormURL()."?id=".$newID);
      }
   }
   Html::back();

} else if (isset($_POST["delete"])) {
   $soft->check($_POST["id"], DELETE);
   $soft->delete($_POST);

   Event::log($_POST["id"], "software", 4, "inventory",
              //TRANS: %s is the user login
              sprintf(__('%s deletes an item'), $_SESSION["glpiname"]));

   $soft->redirectToList();

} else if (isset($_POST["restore"])) {
   $soft->check($_POST["id"], PURGE);

   $soft->restore($_POST);
   Event::log($_POST["id"], "software", 4, "inventory",
              //TRANS: %s is the user login
              sprintf(__('%s restores an item'), $_SESSION["glpiname"]));
   $soft->redirectToList();

} else if (isset($_POST["purge"])) {
   $soft->check($_POST["id"], PURGE);

   $soft->delete($_POST,1);
   Event::log($_POST["id"], "software", 4, "inventory",
              //TRANS: %s is the user login
              sprintf(__('%s purges an item'), $_SESSION["glpiname"]));
   $soft->redirectToList();

} else if (isset($_POST["update"])) {
   $soft->check($_POST["id"], UPDATE);

   $soft->update($_POST);
   Event::log($_POST["id"], "software", 4, "inventory",
              //TRANS: %s is the user login
              sprintf(__('%s updates an item'), $_SESSION["glpiname"]));
   Html::back();

} else {
   Html::header(Software::getTypeName(Session::getPluralNumber()), $_SERVER['PHP_SELF'], "assets", "software");
   $soft->display(array('id'           => $_GET["id"],
                        'withtemplate' => $_GET["withtemplate"]));
   Html::footer();
}
?>