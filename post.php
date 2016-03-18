<?php
$DEV = strpos($_SERVER['REQUEST_URI'], '~') != 0;
set_include_path(get_include_path() . PATH_SEPARATOR . ($DEV ? '/home/mgorman/public_html/_resources/php' : '/var/www/php.iwu.edu/htdocs/_resources/php'));
require_once('_class.IWU_DB.php');
require_once('_class.IWU_DataRow.php');
require_once('_class.IWU_Auth.php');
//require_once('_class.IWU_Template.php');
require_once('../directory/_db.php');

IWU_Auth::forceAuthentication();

$db = new DirectoryDB();

$data = array(
    'Sabbatical' => ($_POST['sabbatical'] === 'on' ? 1 : 0),
    'Abroad' => ($_POST['abroad'] === 'on' ? 1 : 0),
    'AppointmentOnly' => ($_POST['appointmentonly'] === 'on' ? 1 : 0),
    'HoursMonday' => $_POST['monday'],
    'HoursTuesday' => $_POST['tuesday'],
    'HoursWednesday' => $_POST['wednesday'],
    'HoursThursday' => $_POST['thursday'],
    'HoursFriday' => $_POST['friday']
);

if(!$DEV) {
    $data['LastUpdated'] = date('Y-m-d h:i:s');
    $data['LastUpdatedBy'] = IWU_Auth::getUser();
}

if(isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];
}
elseif(isset($_POST['NetID'])) {
    $id = $db->getEmployeeByNetID($_POST['NetID']);
}

// does the record exist?
if($db->hasOfficeHours($id)) {
    $db->updateOfficeHours($id, $data);
    $data['EmployeeID'] = $id;
}
else {
    $data['EmployeeID'] = $id;
    $db->addOfficeHours($data);
}

/*if(isset($_POST['id']) && is_numeric($_POST['id'])) {
    $db->updateOfficeHours($_POST['id'], $data);
    $data['EmployeeID'] = $_POST['id'];
}
else {
    $data['EmployeeID'] = $db->getEmployeeByNetID($_POST['NetID']);
    if($data['EmployeeID']) {
        $db->addOfficeHours($data);
    }
}*/

if(FALSE) {
?><pre><?php
var_dump($_POST);
var_dump($data);
die();
?></pre><?php
}


if($db->isOfficeHoursAdmin(IWU_Auth::getUser())) {
    header('Location: ./admin.php');
}
else {
    header('Location: /directory/employees.php?id='.$data['EmployeeID']);
}
?>