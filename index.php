<?php
$DEV = strpos($_SERVER['REQUEST_URI'], '~') != 0;
set_include_path(get_include_path() . PATH_SEPARATOR . ($DEV ? '/home/mgorman/public_html/_resources/php' : '/var/www/php.iwu.edu/htdocs/_resources/php'));
require_once('_class.IWU_DB.php');
require_once('_class.IWU_DataRow.php');
//require_once('_class.IWU_Auth.php');
require_once('_class.IWU_Template.php');
//require_once('_class.IWU_Paginate.php');
require_once('../directory/_db.php');
//require_once('_functions.php');

//IWU_Auth::forceAuthentication();

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
	
/*if(!$db->isAdmin(IWU_Auth::getUser())) {
    die();
}*/

$people = $db->getPeopleForOfficeHours();

	function format_hours($data) {
		if($data['HoursMonday'] || $data['HoursTuesday'] || $data['HoursWednesday'] || $data['HoursThursday'] || $data['HoursFriday']) {
			$result = '';

			$result .= format_day('M', $data['HoursMonday']);
			$result .= format_day('T', $data['HoursTuesday']);
			$result .= format_day('W', $data['HoursWednesday']);
			$result .= format_day('Th', $data['HoursThursday']);
			$result .= format_day('F', $data['HoursFriday']);

			return $result;
		}
        elseif($data['Sabbatical'] === '1') {
            return '<div><em class="appt">On sabbatical.</em></div>';
        }
		elseif($data['Abroad'] === '1') {
            return '<div><em class="appt">Currently abroad.</em></div>';
        }
		elseif($data['AppointmentOnly'] === '1') {
			return '<div><em class="appt">By appointment only.</em></div>';
		}
        else {
            return '';
        }
	}

	function format_day($day, $text) {
		$orig = array('(', ')');
		$new = array('<em class="appt">', '</em>');

		if($text != '') {
			return '<div><span class="day">'.$day.'</span> <span class="hours">'.str_replace($orig, $new, $text).'</span></div>';
		}
		else {
			return '';
		}
	}

    function format_phone($phone) {
        if(strlen($phone) === 4) {
            $phone = '(309) 556-' . $phone;
        } elseif(strlen($phone) === 8) {
            $phone = '(309) ' . $phone;
        } elseif(strlen($phone) === 12) {
            $phone = $phone;
        } else {
            $phone = $phone;
        }
        
        return $phone;
    }
?>
<style type="text/css">
.person {
	clear: both;
	min-height: 50px;
	margin: 1em auto;
}
.name {
	float: left;
	width: 150px;
}
div.hours {
	padding-left: 160px;
}
.rest {
	float: right;
}
.col {
	float: right;
	width: 200px;
	padding: 0 1em;
}
.fname {
	font-size: 90%;
}
.lname {
	font-size: 110%;
	font-weight: bold;
}
div.hours {
	margin: 0 1em;
}
.updated {
	font-size: 75%;
}
.day {
	font-weight: bold;
	width: 25px;
	display: inline-block;
}
span.hours em.appt:after {
	content: " (appt. req'd)";
}
#middle2 .name {
	width: 110px;
}
#middle2 div.hours {
	padding-left: 110px;
}
</style>
<?php foreach($people as $person) { ?>
<div class="person">
	<div class="name">
		<div class="fname"><?php echo $person['FirstName']; ?></div>
		<div class="lname"><?php echo $person['LastName']; ?></div>
	</div>
	<div class="rest">
		<div class="col">
			<div class="department"><?php echo $person['Department']; ?></div>
			<div class="updated">updated <?php echo (strtotime($person['LastUpdated']) > 0 ? date('M Y', strtotime($person['LastUpdated'])) : ''); ?></div>
		</div>
		<div class="col">
			<?php if($person['Office']) { ?>
			<div class="office"><?php echo $person['Office']; ?></div>
			<?php } ?>
			<?php if($person['Phone']) { ?>
			<div class="phone"><?php echo format_phone($person['Phone']); ?></div>
			<?php } ?>
			<?php if($person['NetID']) { ?>
			<div class="email"><a href="mailto:<?php echo $person['NetID']; ?>@iwu.edu"><?php echo $person['NetID']; ?>@iwu.edu</a></div>
			<?php } ?>
		</div>
	</div>
	<div class="hours"><?php echo format_hours($person); ?></div>
</div>
<?php } ?>
<?php
$page->contentPrimary = ob_get_clean();
echo $page;
?>