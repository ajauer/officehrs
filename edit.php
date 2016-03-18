<?php
$DEV = strpos($_SERVER['REQUEST_URI'], '~') != 0;
set_include_path(get_include_path() . PATH_SEPARATOR . ($DEV ? '/home/mgorman/public_html/_resources/php' : '/var/www/php.iwu.edu/htdocs/_resources/php'));
require_once('_class.IWU_DB.php');
require_once('_class.IWU_DataRow.php');
require_once('_class.IWU_Auth.php');
require_once('_class.IWU_Template.php');
//require_once('_class.IWU_Paginate.php');
require_once('../directory/_db.php');
//require_once('_functions.php');

IWU_Auth::forceAuthentication();

$db = new DirectoryDB();

$page = new IWU_Template();
$page->wwwPath = '/provost/';
$page->headTitle = 'Faculty Office Hours';
//$page->customStyle = $page->getIncludeContents('_inc.style.php');
//$page->headcode = '<style>'.$page->getIncludeContents('_inc.style.php').'</style>';
$page->headerBG = '/_resources/images/banners/full/ames-school-art_banner.jpg';
$page->headerDepartment = 'Provost\'s Office';
$page->bodyTitle = 'Faculty Office Hours';
$page->contactNetID = 'jgreen';
ob_start();
	
/*if(!$db->isOfficeHoursAdmin(IWU_Auth::getUser())) {
    header('Location: /officehrs/');
    die();
}*/

$person = array();

if($_GET['id'] === 'self') {
    $_GET['id'] = $db->getEmployeeByNetID(IWU_Auth::getUser());
}

if($db->isOfficeHoursAdmin(IWU_Auth::getUser()) || $db->getEmployeeByNetID(IWU_Auth::getUser()) === $_GET['id']) {
    if(isset($_GET['id']) && is_numeric($_GET['id'])) {
        if($db->hasOfficeHours($_GET['id'])) {
            $person = $db->getPersonForOfficeHours($_GET['id']);
            $page->contactNetID = $person['NetID'];
        }
        else {
            $person = array(
                'EmployeeID' => $_GET['id'],
                'NetID' => $db->getNetIDByEmployee($_GET['id']),
                'Sabbatical' => '',
                'Abroad' => '',
                'AppointmentOnly' => '',
                'HoursMonday' => '',
                'HoursTuesday' => '',
                'HoursWednesday' => '',
                'HoursThursday' => '',
                'HoursFriday' => ''
            );
        }
    }
    
    if(!isset($person['EmployeeID'])) {
        if($db->isOfficeHoursAdmin(IWU_Auth::getUser())) {
            $person = array(
                'EmployeeID' => 'NEW',
                'NetID' => '',
                'Sabbatical' => '',
                'Abroad' => '',
                'AppointmentOnly' => '',
                'HoursMonday' => '',
                'HoursTuesday' => '',
                'HoursWednesday' => '',
                'HoursThursday' => '',
                'HoursFriday' => ''
            );
        }
        elseif($db->isEmployee(IWU_Auth::getUser())) {
            $person = array(
                'EmployeeID' => $db->getEmployeeByNetID(IWU_Auth::getUser()),
                'NetID' => IWU_Auth::getUser(),
                'Sabbatical' => '',
                'Abroad' => '',
                'AppointmentOnly' => '',
                'HoursMonday' => '',
                'HoursTuesday' => '',
                'HoursWednesday' => '',
                'HoursThursday' => '',
                'HoursFriday' => ''
            );
        }
    }
}

if(!isset($person['EmployeeID'])) {
    header('Location: /officehrs/');
}

?>
<form action="post.php" method="post">
    <input type="hidden" name="id" value="<?php echo $person['EmployeeID']; ?>" />
    <?php if($person['NetID'] === '') { ?>
    <fieldset>
        <legend>Person</legend>
        <label for="NetID" class="required">
            NetID
            <input type="text" name="NetID" id="NetID" required="required" />
        </label>
    </fieldset>
    <?php } else { ?>
    <input type="hidden" name="NetID" value="<?php echo $person['NetID']; ?>" />
    <?php } ?>
    <fieldset>
        <legend>Status</legend>
        <label for="sabbatical">
            Sabbatical?
            <input type="checkbox" name="sabbatical" id="sabbatical" <?php if($person['Sabbatical'] == '1') { echo 'checked="checked"'; } ?> />
        </label>
        <label for="abroad">
            Abroad?
            <input type="checkbox" name="abroad" id="abroad" <?php if($person['Abroad'] == '1') { echo 'checked="checked"'; } ?> />
        </label>
        <label for="appointmentonly">
            Appointment only?
            <input type="checkbox" name="appointmentonly" id="appointmentonly" <?php if($person['AppointmentOnly'] == '1') { echo 'checked="checked"'; } ?> />
        </label>
    </fieldset>
    <fieldset>
        <legend>Hours</legend>
        <p>Format suggestions adapted from the <a href="https://www.iwu.edu/writingguide/StyleGuide.html#T">University Writing Guide</a>:</p>
        <ul>
            <li>Use "noon" and "midnight" instead of 12 a.m. and 12 p.m.</li>
            <li>Do not use :00 when distinguishing time.</li>
            <li>Use a.m. and p.m. (lowercase)</li>
            <li>Separate multiple sets of hours with commas and ampersands.</li>
            <li>If you have multiple offices, specify which one for each set of hours. If you only have one office, don't.</li>
            <li><em>Put parentheses around hours that require an appointment &ndash; e.g. <strong>(8-10 a.m.)</strong> &ndash; so the system can apply special formatting.</em></li>
            <li>Example: <strong>8-9 a.m. in CLA 100, (10-11 a.m. in CNS C200A) &amp; noon-1 p.m. in Ames 300</strong></li>
        </ul>
        <label for="monday">
            Monday
            <input type="text" name="monday" id="monday" value="<?php echo $person['HoursMonday']; ?>" />
        </label>
        <label for="tuesday">
            Tuesday
            <input type="text" name="tuesday" id="tuesday" value="<?php echo $person['HoursTuesday']; ?>" />
        </label>
        <label for="wednesday">
            Wednesday
            <input type="text" name="wednesday" id="wednesday" value="<?php echo $person['HoursWednesday']; ?>" />
        </label>
        <label for="thursday">
            Thursday
            <input type="text" name="thursday" id="thursday" value="<?php echo $person['HoursThursday']; ?>" />
        </label>
        <label for="friday">
            Friday
            <input type="text" name="friday" id="friday" value="<?php echo $person['HoursFriday']; ?>" />
        </label>
    </fieldset>
    <input type="submit" />
</form>
<?php
$page->contentPrimary = ob_get_clean();
echo $page;
?>