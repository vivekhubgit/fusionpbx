<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('modules_add') || permission_exists('modules_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//determin the action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$module_uuid = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//set the http post variables to php variables
	if (count($_POST)>0) {
		$module_label = check_str($_POST["module_label"]);
		$module_name = check_str($_POST["module_name"]);
		$module_description = check_str($_POST["module_description"]);
		$module_category = check_str($_POST["module_category"]);
		$module_enabled = check_str($_POST["module_enabled"]);
		$module_default_enabled = check_str($_POST["module_default_enabled"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$module_uuid = check_str($_POST["module_uuid"]);
	}

	//check for all required data
		if (strlen($module_label) == 0) { $msg .= "Please provide: Label<br>\n"; }
		if (strlen($module_name) == 0) { $msg .= "Please provide: Module Name<br>\n"; }
		//if (strlen($module_description) == 0) { $msg .= "Please provide: Description<br>\n"; }
		if (strlen($module_category) == 0) { $msg .= "Please provide: Module Category<br>\n"; }
		if (strlen($module_enabled) == 0) { $msg .= "Please provide: Enabled<br>\n"; }
		if (strlen($module_default_enabled) == 0) { $msg .= "Please provide: Default Enabled<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "includes/header.php";
			require_once "includes/persistformvar.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		}

	//add or update the database
		if ($_POST["persistformvar"] != "true") {
			if ($action == "add" && permission_exists('modules_add')) {
				$module_uuid = uuid();
				$sql = "insert into v_modules ";
				$sql .= "(";
				$sql .= "module_uuid, ";
				$sql .= "module_label, ";
				$sql .= "module_name, ";
				$sql .= "module_description, ";
				$sql .= "module_category, ";
				$sql .= "module_enabled, ";
				$sql .= "module_default_enabled ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$module_uuid', ";
				$sql .= "'$module_label', ";
				$sql .= "'$module_name', ";
				$sql .= "'$module_description', ";
				$sql .= "'$module_category', ";
				$sql .= "'$module_enabled', ";
				$sql .= "'$module_default_enabled' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);

				save_module_xml();

				require_once "includes/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=v_modules.php\">\n";
				echo "<div align='center'>\n";
				echo "Add Complete\n";
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			} //if ($action == "add")

			if ($action == "update" && permission_exists('modules_edit')) {
				$sql = "update v_modules set ";
				$sql .= "module_label = '$module_label', ";
				$sql .= "module_name = '$module_name', ";
				$sql .= "module_description = '$module_description', ";
				$sql .= "module_category = '$module_category', ";
				$sql .= "module_enabled = '$module_enabled', ";
				$sql .= "module_default_enabled = '$module_default_enabled' ";
				$sql .= "where module_uuid = '$module_uuid' ";
				$db->exec(check_sql($sql));
				unset($sql);

				save_module_xml();

				require_once "includes/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=v_modules.php\">\n";
				echo "<div align='center'>\n";
				echo "Update Complete\n";
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			} //if ($action == "update")
		} //if ($_POST["persistformvar"] != "true")
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$module_uuid = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_modules ";
		$sql .= "where module_uuid = '$module_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		foreach ($result as &$row) {
			$module_label = $row["module_label"];
			$module_name = $row["module_name"];
			$module_description = $row["module_description"];
			$module_category = $row["module_category"];
			$module_enabled = $row["module_enabled"];
			$module_default_enabled = $row["module_default_enabled"];
			break; //limit to 1 row
		}
		unset ($prep_statement);
	}

//show the header
	require_once "includes/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap><b>Module Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Module Update</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_modules.php'\" value='Back'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Label:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='module_label' maxlength='255' value=\"$module_label\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Module Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='module_name' maxlength='255' value=\"$module_name\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='module_description' maxlength='255' value=\"$module_description\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Module Category:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	$table_name = 'v_modules';$field_name = 'module_category';$sql_where_optional = "";$field_current_value = $module_category;
	echo html_select_other($db, $table_name, $field_name, $sql_where_optional, $field_current_value);
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='module_enabled'>\n";
	echo "    <option value=''></option>\n";
	if ($module_enabled == "true") { 
		echo "    <option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($module_enabled == "false") { 
		echo "    <option value='false' SELECTED >false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Default Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='module_default_enabled'>\n";
	echo "    <option value=''></option>\n";
	if ($module_default_enabled == "true") { 
		echo "    <option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($module_default_enabled == "false") { 
		echo "    <option value='false' SELECTED >false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='module_uuid' value='$module_uuid'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

	require_once "includes/footer.php";
?>