<?php if ($this->trafficCap[1] != 0 && $this->trafficCap[2] != 0 && $this->trafficCap[3] != 0 && !empty($this->numJoins[1]) && !empty($this->numJoins[2]) && !empty($this->numJoins[3]) && $this->numJoins[1] >= $this->trafficCap[1] && $this->numJoins[2] >= $this->trafficCap[2] && $this->numJoins[3] >= $this->trafficCap[3]) : ?>
<?php include($GLOBALS['serverPath'] .'pages/redirect.php'); ?>
<?php else : ?>
<script type="text/javascript" src="/js/coregFunctions.js"></script>
<?php if ($this->trafficCap[3] == 0 || empty($this->numJoins[3]) || (!empty($this->numJoins[3]) && $this->numJoins[3] < $this->trafficCap[3])) { ?>
<form class="formWrapper" method="post" action="javascript:void(0);" onsubmit="submitForm('edu', this, '<?= $this->getNextUrl($this->position, true) ?>');">
	<h2>Have You Considered Additional Education, Or Maybe A Career Change?</h2>
	<p>Speak to a Career Institute Specialist about a degree from a Leading Online University. <a href="http://www.esmartliving.com/1on1-privacy.html" target="_blank" rel="nofollow" style="font-size:10px;">Privacy Policy</a></p>
	<div>
		<input type="radio" name="yesNo" class="yesNoYes formExpandRadio" value="yes" onclick="$('#eduForm').slideDown();" />
		<strong>Yes</strong>
		<input type="radio" name="yesNo" class="yesNoNo formExpandRadio" value="no" checked="checked" onclick="$('#eduForm').slideUp();" />
		<strong>No</strong>
	</div>
	<div class="formProcessWaiting">
		<img src="/templates/<?= $this->templateFolder ?>/ajax-loader.gif" alt="" style="margin-right: 5px;" />
		Processing Form, Please Wait...
	</div>
	<ul id="eduForm" class="coregForm" style="display: none;">
		<li>
			<label>First Name*</label>
			<input type="text" class="formInputText" name="firstName" id="eduFirstName" value="<?= (!empty($this->userInfo['firstname'])) ? $this->userInfo['firstname'] : '' ?>"/>
			<input type="hidden" class="formInputHidden" name="refererUrl" id="eduRefererUrl" value="<?= rawurlencode($_SERVER['HTTP_REFERER']) ?>" />
		</li>
		<li>
			<label>Last Name*</label>
			<input type="text" class="formInputText" name="lastName" id="eduLastName" value="<?= (!empty($this->userInfo['lastname'])) ? $this->userInfo['lastname'] : '' ?>" />
		</li>
		<li>
			<label>Email Address*</label>
			<input type="text" class="formInputText" name="email" id="eduEmail" value="<?= (!empty($this->userInfo['email'])) ? $this->userInfo['email'] : '' ?>" />
		</li>
		<li>
			<label>Primary Phone*</label>
			<input type="text" class="formInputText phone1" name="phone11" id="bankPhone11" maxlength="3" value="<?= (!empty($this->userInfo['phone11'])) ? $this->userInfo['phone11'] : '' ?>" />
			-
			<input type="text" class="formInputText phone2" name="phone12" id="bankPhone12" maxlength="3" value="<?= (!empty($this->userInfo['phone12'])) ? $this->userInfo['phone12'] : '' ?>" />
			-
			<input type="text" class="formInputText phone3" name="phone13" id="bankPhone13" maxlength="4" value="<?= (!empty($this->userInfo['phone13'])) ? $this->userInfo['phone13'] : '' ?>" />
		</li>
		<li>
			<label>Secondary Phone</label>
			<input type="text" class="formInputText phone1" name="phone21" id="bankPhone21" maxlength="3" value="<?= (!empty($this->userInfo['phone21'])) ? $this->userInfo['phone21'] : '' ?>" />
			-
			<input type="text" class="formInputText phone2" name="phone22" id="bankPhone22" maxlength="3" value="<?= (!empty($this->userInfo['phone22'])) ? $this->userInfo['phone22'] : '' ?>" />
			-
			<input type="text" class="formInputText phone3" name="phone23" id="bankPhone23" maxlength="4" value="<?= (!empty($this->userInfo['phone23'])) ? $this->userInfo['phone23'] : '' ?>" />
		</li>
		<li>
			<label>Address*</label>
			<input type="text" class="formInputText" name="address" id="eduAddress" value="<?= (!empty($this->userInfo['address'])) ? $this->userInfo['address'] : '' ?>" />
		</li>
		<li>
			<label>City*</label>
			<input type="text" class="formInputText" name="city" id="eduCity" value="<?= (!empty($this->userInfo['city'])) ? $this->userInfo['city'] : '' ?>" />
		</li>
		<li>
			<label>State*</label>
			<select class="formSelect" name="state" id="eduState">
				<?php
				$state = (!empty($this->userInfo['state'])) ? $this->userInfo['state'] : '';
				echo $this->getStateList($state);
				?>
			</select>
		</li>
		<li>
			<label>Zip*</label>
			<input type="text" class="formInputText zip" name="zip" maxlength="5" id="eduZip" value="<?= (!empty($this->userInfo['zip'])) ? $this->userInfo['zip'] : '' ?>" />
		</li>
								<li>
			<label>Field of Interest*</label>
			<select class="formSelect interest" name="fieldOfInterest" id="eduInterest">
				<option value=""></option>
				<option value="Psychology And Counseling">Psychology And Counseling</option>
				<option value="Healthcare: Other/General">Healthcare: Other/General</option>
				<option value="Healthcare: Medical Assisting And Administration">Healthcare: Medical Assisting And Administration</option>
				<option value="Culinary">Culinary</option>
				<option value="Business">Business</option>
				<option value="Visual Arts And Design">Visual Arts And Design</option>
				<option value="Information Technology">Information Technology</option>
				<option value="Healthcare: Nursing">Healthcare: Nursing</option>
				<option value="Education And Teaching">Education And Teaching</option>
				<option value="Criminal Justice">Criminal Justice</option>
			</select>
		</li>
		<li>
			<label>Do You Have a High School Diploma/GED?</label>
			<select class="formSelect" name="hsOrGED" id="eduHS">
				<option value="n">No</option>
				<option value="y">Yes</option>
			</select>
		</li>								
		<li>
			<label>Are You a Citizen?*</label>
			<div class="radioWrapper">
				<input class="yesNoYes formRadio" value="yes" name="citizen" type="radio" /> Yes
				<input class="yesNoNo formRadio" value="no" name="citizen" type="radio" /> No
			</div>
		</li>
		<li class="submitWrapper">
			<input type="submit" value="SUBMIT" class="submit" />
		</li>
		<li class="submitDisclaimer">
			By clicking submit, you agree to be contacted for a free career change consultation.
		</li>
	</ul>
</form>
<?php } ?>
<?php if ($this->trafficCap[2] == 0 || empty($this->numJoins[2]) || (!empty($this->numJoins[2]) && $this->numJoins[2] < $this->trafficCap[2])) { ?>
<form class="formWrapper" method="post" action="javascript:void(0);" onsubmit="submitForm('debt', this, '<?= $this->getNextUrl($this->position, true) ?>');">
	<h2>Do You Have More Than $10K In Credit Card Debt?</h2>
	<p>If you could eliminate your debt without permanently damaging your credit, why wouldn't you? We can help!</p>
	<div>
		<input type="hidden" class="formInputHidden" name="refererUrl" id="debtRefererUrl" value="<?= rawurlencode($_SERVER['HTTP_REFERER']) ?>" />
		<input type="radio" name="yesNo" class="yesNoYes formExpandRadio" value="yes" onclick="$('#debtForm').slideDown();" />
		<strong>Yes</strong>
		<input type="radio" name="yesNo" class="yesNoNo formExpandRadio" value="no" checked="checked" onclick="$('#debtForm').slideUp();" />
		<strong>No</strong>
	</div>
	<div class="formProcessWaiting">
		<img src="/templates/<?= $this->templateFolder ?>/ajax-loader.gif" alt="" style="margin-right: 5px;" />
		Processing Form, Please Wait...
	</div>
	<ul id="debtForm" class="coregForm" style="display: none;">
		<li>
			<label>First Name*</label>
			<input type="text" class="formInputText" name="firstName" id="debtFirstName" value="<?= (!empty($this->userInfo['firstname'])) ? $this->userInfo['firstname'] : '' ?>"/>
		</li>
		<li>
			<label>Last Name*</label>
			<input type="text" class="formInputText" name="lastName" id="debtLastName" value="<?= (!empty($this->userInfo['lastname'])) ? $this->userInfo['lastname'] : '' ?>" />
		</li>
		<li>
			<label>Email Address*</label>
			<input type="text" class="formInputText" name="email" id="debtEmail" value="<?= (!empty($this->userInfo['email'])) ? $this->userInfo['email'] : '' ?>" />
		</li>
		<li>
			<label>State*</label>
			<select class="formSelect" name="state" id="eduState">
				<?php
				$state = (!empty($this->userInfo['state'])) ? $this->userInfo['state'] : '';
				echo $this->getStateList($state);
				?>
			</select>
		</li>
		<li>
			<label>Zip*</label>
			<input type="text" class="formInputText zip" name="zip" maxlength="5" id="debtZip" value="<?= (!empty($this->userInfo['zip'])) ? $this->userInfo['zip'] : '' ?>" />
		</li>
		<li>
			<label>Primary Phone*</label>
			<input type="text" class="formInputText phone1" name="phone11" id="bankPhone11" maxlength="3" value="<?= (!empty($this->userInfo['phone11'])) ? $this->userInfo['phone11'] : '' ?>" />
			-
			<input type="text" class="formInputText phone2" name="phone12" id="bankPhone12" maxlength="3" value="<?= (!empty($this->userInfo['phone12'])) ? $this->userInfo['phone12'] : '' ?>" />
			-
			<input type="text" class="formInputText phone3" name="phone13" id="bankPhone13" maxlength="4" value="<?= (!empty($this->userInfo['phone13'])) ? $this->userInfo['phone13'] : '' ?>" />
		</li>
		<li>
			<label>Secondary Phone</label>
			<input type="text" class="formInputText phone1" name="phone21" id="bankPhone21" maxlength="3" value="<?= (!empty($this->userInfo['phone21'])) ? $this->userInfo['phone21'] : '' ?>" />
			-
			<input type="text" class="formInputText phone2" name="phone22" id="bankPhone22" maxlength="3" value="<?= (!empty($this->userInfo['phone22'])) ? $this->userInfo['phone22'] : '' ?>" />
			-
			<input type="text" class="formInputText phone3" name="phone23" id="bankPhone23" maxlength="4" value="<?= (!empty($this->userInfo['phone23'])) ? $this->userInfo['phone23'] : '' ?>" />
		</li>
		<li>
			<label>Credit Card Debt*</label>
			<select name="ccdebt" id="debtDebt" class="formSelect">
				<option value="">-- Select --</option>
				<option value="7500">5,000-9,999</option>

				<option value="15000">10,000-19,999</option>
				<option value="27500">20,000-34,999</option>
				<option value="42500">35,000-49,999</option>
				<option value="75000">50,000+</option>
			</select>
		</li>
		<li>
			<label>Payment Status*</label>
			<select name="paymentstatus" id="debtPaymentStatus" class="formSelect">
				<option value="">-- Select --</option>
				<option value="0">Current</option>
				<option value="25">About To Fall Behind</option>
				<option value="50">31 - 60 Days Behind</option>
				<option value="75">61+ Days Behind</option>
			</select>
		</li>
		<li class="submitWrapper">
			<input type="submit" value="SUBMIT" class="submit" />
		</li>
		<li class="submitDisclaimer">
			By clicking submit, you agree to be contacted for a free debt relief consultation.
		</li>
	</ul>
</form>
<?php } ?>
<?php if ($this->trafficCap[1] == 0 || empty($this->numJoins[1]) || (!empty($this->numJoins[1]) && $this->numJoins[1] < $this->trafficCap[1])) { ?>
<form class="formWrapper" method="post" action="javascript:void(0);" onsubmit="submitForm('bank', this, '<?= $this->getNextUrl($this->position, true) ?>');">
	<h2>Are You Interested In Speaking To A Bankruptcy Lawyer?</h2>
	<p>Talk to a bankruptcy lawyer on whether or not filing for bankruptcy is the right option for you.</p>
	<div>
		<input type="hidden" class="formInputHidden" name="refererUrl" id="bankRefererUrl" value="<?= rawurlencode($_SERVER['HTTP_REFERER']) ?>" />
		<input type="radio" name="yesNo" class="yesNoYes formExpandRadio" value="yes" onclick="$('#bankForm').slideDown();" />
		<strong>Yes</strong>
		<input type="radio" name="yesNo" class="yesNoNo formExpandRadio" value="no" checked="checked" onclick="$('#bankForm').slideUp();" />
		<strong>No</strong>
	</div>
	<div class="formProcessWaiting">
		<img src="/templates/<?= $this->templateFolder ?>/ajax-loader.gif" alt="" style="margin-right: 5px;" />
		Processing Form, Please Wait...
	</div>
	<ul id="bankForm" class="coregForm" style="display: none;">
		<li>
			<label>First Name*</label>
			<input type="text" class="formInputText" name="firstName" id="bankFirstName" value="<?= (!empty($this->userInfo['firstname'])) ? $this->userInfo['firstname'] : '' ?>"/>
		</li>
		<li>
			<label>Last Name*</label>
			<input type="text" class="formInputText" name="lastName" id="bankLastName" value="<?= (!empty($this->userInfo['lastname'])) ? $this->userInfo['lastname'] : '' ?>" />
		</li>
		<li>
			<label>Email Address*</label>
			<input type="text" class="formInputText" name="email" id="bankEmail" value="<?= (!empty($this->userInfo['email'])) ? $this->userInfo['email'] : '' ?>" />
		</li>
		<li>
			<label>Zip*</label>
			<input type="text" class="formInputText zip" name="zip" maxlength="5" id="bankZip" value="<?= (!empty($this->userInfo['zip'])) ? $this->userInfo['zip'] : '' ?>" />
		</li>
		<li>
			<label>Primary Phone*</label>
			<input type="text" class="formInputText phone1" name="phone11" id="bankPhone11" maxlength="3" value="<?= (!empty($this->userInfo['phone11'])) ? $this->userInfo['phone11'] : '' ?>" />
			-
			<input type="text" class="formInputText phone2" name="phone12" id="bankPhone12" maxlength="3" value="<?= (!empty($this->userInfo['phone12'])) ? $this->userInfo['phone12'] : '' ?>" />
			-
			<input type="text" class="formInputText phone3" name="phone13" id="bankPhone13" maxlength="4" value="<?= (!empty($this->userInfo['phone13'])) ? $this->userInfo['phone13'] : '' ?>" />
		</li>
		<li>
			<label>Secondary Phone</label>
			<input type="text" class="formInputText phone1" name="phone21" id="bankPhone21" maxlength="3" value="<?= (!empty($this->userInfo['phone21'])) ? $this->userInfo['phone21'] : '' ?>" />
			-
			<input type="text" class="formInputText phone2" name="phone22" id="bankPhone22" maxlength="3" value="<?= (!empty($this->userInfo['phone22'])) ? $this->userInfo['phone22'] : '' ?>" />
			-
			<input type="text" class="formInputText phone3" name="phone23" id="bankPhone23" maxlength="4" value="<?= (!empty($this->userInfo['phone23'])) ? $this->userInfo['phone23'] : '' ?>" />
		</li>
		<li class="submitWrapper">
			<input type="submit" value="SUBMIT" class="submit" />
		</li>
		<li class="submitDisclaimer">
			By clicking submit, you agree to be contacted for a free bankruptcy consultation.
		</li>
	</ul>
</form>
<?php } ?>
<?php /*
<?php if ($this->trafficCap[4] == 0 || empty($this->numJoins[4]) || (!empty($this->numJoins[4]) && $this->numJoins[4] < $this->trafficCap[4])) { ?>
<form class="formWrapper" method="post" action="javascript:void(0);" onsubmit="submitForm('health', this, '<?= $this->getNextUrl($this->position, true) ?>');">
	<h2>Would You Like To Save Money On Your Health Insurance Premiums?</h2>
	<p>If you could save thousands of dollar per year on your health insurance why wouldn’t you? Get a free quote today!</p>
	<div>
		<input type="hidden" class="formInputHidden" name="refererUrl" id="healthRefererUrl" value="<?= rawurlencode($_SERVER['HTTP_REFERER']) ?>" />
		<input type="radio" name="yesNo" class="yesNoYes formExpandRadio" value="yes" onclick="$('#healthForm').slideDown();" />
		<strong>Yes</strong>
		<input type="radio" name="yesNo" class="yesNoNo formExpandRadio" value="no" checked="checked" onclick="$('#healthForm').slideUp();" />
		<strong>No</strong>
	</div>
	<div class="formProcessWaiting">
		<img src="/templates/<?= $this->templateFolder ?>/ajax-loader.gif" alt="" style="margin-right: 5px;" />
		Processing Form, Please Wait...
	</div>
	<ul id="healthForm" class="coregForm" style="display: none;">
		<li>
			<label>First Name*</label>
			<input type="text" class="formInputText" name="firstName" id="healthFirstName" value="<?= (!empty($this->userInfo['firstname'])) ? $this->userInfo['firstname'] : '' ?>"/>
		</li>
		<li>
			<label>Last Name*</label>
			<input type="text" class="formInputText" name="lastName" id="healthLastName" value="<?= (!empty($this->userInfo['lastname'])) ? $this->userInfo['lastname'] : '' ?>" />
		</li>
		<li>
			<label>Email Address*</label>
			<input type="text" class="formInputText" name="email" id="healthEmail" value="<?= (!empty($this->userInfo['email'])) ? $this->userInfo['email'] : '' ?>" />
		</li>
		<li>
			<label>Address*</label>
			<input type="text" class="formInputText" name="address" id="healthAddress" />
		</li>
		<li>
			<label>City*</label>
			<input type="text" class="formInputText" name="city" id="healthCity" />
		</li>
		<li>
			<label>State*</label>
			<select class="formSelect" name="state" id="healthState">
				<?= $this->getStateList() ?>
			</select>
		</li>
		<li>
			<label>Zip*</label>
			<input type="text" class="formInputText zip" name="zip" maxlength="5" id="healthZip" />
		</li>
		<li>
			<label>Primary Phone*</label>
			<input type="text" class="formInputText phone1" name="phone11" id="healthPhone11" maxlength="3" />
			-
			<input type="text" class="formInputText phone2" name="phone12" id="healthPhone12" maxlength="3" />
			-
			<input type="text" class="formInputText phone3" name="phone13" id="healthPhone13" maxlength="4" />
		</li>
		<li>
			<label>Secondary Phone</label>
			<input type="text" class="formInputText phone1" name="phone21" id="healthPhone21" maxlength="3" />
			-
			<input type="text" class="formInputText phone2" name="phone22" id="healthPhone22" maxlength="3" />
			-
			<input type="text" class="formInputText phone3" name="phone23" id="healthPhone23" maxlength="4" />
		</li>
		<li>
			<label>Date of Birth</label>
			<select name="birthMonth" id="healthBirthMonth" class="formSelect">
				<option value="">MM</option>
				<option value="01">01</option>
				<option value="02">02</option>
				<option value="03">03</option>
				<option value="04">04</option>
				<option value="05">05</option>
				<option value="06">06</option>
				<option value="07">07</option>
				<option value="08">08</option>
				<option value="09">09</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
			</select>
			/
			<select name="birthDate" id="healthBirthDate" class="formSelect">
				<option value="">DD</option>
				<option value="01">01</option>
				<option value="02">02</option>
				<option value="03">03</option>
				<option value="04">04</option>
				<option value="05">05</option>
				<option value="06">06</option>
				<option value="07">07</option>
				<option value="08">08</option>
				<option value="09">09</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
				<option value="31">31</option>
			</select>
			/
			<select name="birthYear" id="healthBirthYear" class="formSelect">
				<option value="">YYYY</option>
				<?php
				for ($i = date('Y'); $i >= 1900; $i--) echo '<option value="'. $i .'">'. $i ."</option>\n";
				?>
			</select>
		</li>
		<li>
			<label>Height</label>
			<select name="heightFeet" id="healthHeightFeet" class="formSelect">
				<option value="">Select</option>
				<option value="4">4 feet</option>
				<option value="5">5 feet</option>
				<option value="6">6 feet</option>
				<option value="7">7 feet</option>
			</select>
			<select name="heightInches" id="healthHeightInches" class="formSelect">
				<option value="">Select</option>
				<option value="0">0 inches</option>
				<option value="1">1 inch</option>
				<option value="2">2 inches</option>
				<option value="3">3 inches</option>
				<option value="4">4 inches</option>
				<option value="5">5 inches</option>
				<option value="6">6 inches</option>
				<option value="7">7 inches</option>
				<option value="8">8 inches</option>
				<option value="9">9 inches</option>
				<option value="10">10 inches</option>
				<option value="11">11 inches</option>
			</select>
		</li>
		<li>
			<label>Weight</label>
			<select name="weight" id="healthWeight" class="formSelect">
				<option value="">Select</option>
				<option value="80">75 - 85</option>
				<option value="90">86 - 95</option>
				<option value="100">96 - 105</option>
				<option value="110">106 - 115</option>
				<option value="120">116 - 125</option>
				<option value="130">126 - 135</option>
				<option value="140">136 - 145</option>
				<option value="150">146 - 155</option>
				<option value="160">156 - 165</option>
				<option value="170">166 - 175</option>
				<option value="180">176 - 185</option>
				<option value="190">186 - 195</option>
				<option value="200">196 - 205</option>
				<option value="210">206 - 215</option>
				<option value="220">216 - 225</option>
				<option value="230">226 - 235</option>
				<option value="240">236 - 245</option>
				<option value="250">246 - 255</option>
				<option value="260">256 - 265</option>
				<option value="270">266 - 275</option>
				<option value="280">276 - 285</option>
				<option value="290">286 - 295</option>
				<option value="300">295+</option>
			</select>
			lbs.
		</li>
		<li>
			<label>What is Your Marital Status</label>
			<select name="maritalStatus" id="healthMaritalStatus" class="formSelect">
				<option value="">Select</option>
				<option value="Single">Single</option>
				<option value="Married">Married</option>
				<option value="Separated">Separated</option>
				<option value="Divorced">Divorced</option>
				<option value="Widowed">Widowed</option>
			</select>
		</li>
		<li>
			<label>Current Occupation</label>
			<select name="occupation" id="healthOccupation" class="formSelect">
				<option value="">Select</option>
				<option value="SelfEmployed">Self Employed</option> 
				<option value="Student">Student</option>
				<option value="Retired">Retired</option>
				<option value="ProfessionalSalaried">Professional Salaried</option>
				<option value="Unemployed">Unemployed</option>
				<option value="AdministrativeClerical">Administrative Clerical</option>
				<option value="Architect">Architect</option>
				<option value="BusinessOwner">Business Owner</option>
				<option value="CertifiedPublicAccountant">Certified Public Accountant</option>
				<option value="Clergy">Clergy</option>
				<option value="ConstructionTrades">Construction Trades</option> 
				<option value="Dentist">Dentist</option>
				<option value="Disabled">Disabled</option>
				<option value="Engineer">Engineer</option>
				<option value="Homemaker">Homemaker</option>
				<option value="Lawyer">Lawyer</option>
				<option value="ManagerSupervisor">Manager Supervisor</option>
				<option value="MilitaryOfficer">Military Officer</option>
				<option value="MilitaryEnlisted">Militaty Enlisted</option>
				<option value="MinorNotApplicable">Minor Not Applicable</option>
				<option value="OtherNonTechnical">Other Non Teachnical</option>
				<option value="OtherTechnical">Other Technical</option>
				<option value="Physician">Physician</option>
				<option value="ProfessionalSalaried">Professional Salaried</option>
				<option value="Professor">Professor</option>
				<option value="Retail">Retail</option>
				<option value="Retired">Retired</option>
				<option value="SalesInside">Sales Inside</option>
				<option value="SalesOutside">Sales Outside</option>
				<option value="SchoolTeacher">School Teacher</option>
				<option value="Scientist">Scientist</option>
				<option value="SelfEmployed">Self Employed</option>
				<option value="SkilledSemiSkilled">Skilled/Semi-Skilled</option>
				<option value="Student">Student</option>
				<option value="Unemployed">Unemployed</option>
			</select>
		</li>
		<li>
			<label>Gender</label>
			<div class="radioWrapper">
				<input type="radio" name="gender" id="healthGenderM" value="M" class="yesNoYes formRadio" />
				Male
				<input type="radio" name="gender" id="healthGenderF" value="F" class="yesNoNo formRadio" />
				Female
			</div>
		</li>
		<li>
			<label>Are You Pregnant?</label>
			<div class="radioWrapper">
				<input type="radio" name="pregnant" id="healthPregnantY" value="Yes" class="yesNoYes formRadio" />
				Yes
				<input type="radio" name="pregnant" id="healthPregnantN" value="No" class="yesNoNo formRadio" />
				No
			</div>
		</li>
		<li>
			<label>Are You A Smoker?</label>
			<div class="radioWrapper">
				<input type="radio" name="smoker" id="healthSmokerY" value="Yes" class="yesNoYes formRadio" />
				Yes
				<input type="radio" name="smoke" id="healthSmokerN" value="No" class="yesNoNo formRadio" />
				No
			</div>
		</li>
		<li>
			<label>Are You Currently On Medication?</label>
			<div class="radioWrapper">
				<input type="radio" name="medication" id="healthMedicationY" value="Yes" class="yesNoYes formRadio" />
				Yes
				<input type="radio" name="medication" id="healthMedicationNo" value="No" class="yesNoNo formRadio" />
				No
			</div>
		</li>
		<li>
			<label>Do You Have Any Health Conditions?</label>
			<div class="radioWrapper">
				<input type="radio" name="conditions" id="healthConditionsY" value="Yes" onclick="$('#healthConditionsTable').slideDown();" class="yesNoYes formRadio" />
				Yes
				<input type="radio" name="conditions" id="healthConditionsN" value="No" onclick="$('#healthConditionsTable').slideUp();" class="yesNoNo formRadio" />
				No
			</div>
		</li>
		<li>
			<table width="520px" border="0" id="healthConditionsTable" style="display:none;">
				<tr>
					<td colspan="3"><div style="text-align:center; font-style:italic; padding:10px 0px; width:425px;">Check All that Apply</div></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="hiv_aids" id="hiv_aids" />HIV/AIDS</td>
					<td><input type="checkbox" name="diabetes" id="diabetes" />Diabetes </td>
					<td><input type="checkbox" name="cancer" id="cancer" />Cancer</td> 																						
					<td valign="top"><input type="checkbox" name="ulcer" id="ulcer" />Ulcer</td>
				</tr>
				<tr>
					<td><input type="checkbox" name="heart" id="heart" />Heart Disease</td>
					<td><input type="checkbox" name="vascular" id="vascular" />Vascular Disease</td>
					<td valign="top"><input type="checkbox" name="depression" id="depression" />Depression</td>
					<td valign="top"><input type="checkbox" name="cholesterol" id="cholesterol" />Cholesterol</td>
				</tr>
				<tr>
					<td valign="top"><input type="checkbox" name="alcohol_drug" id="alcohol_drug" />Alcohol/Drug Abuse</td>
					<td valign="top"><input type="checkbox" name="alzheimers" id="alzheimers" />Alzheimer's</td>
					<td valign="top"><input type="checkbox" name="kidney" id="kidney" />Kidney Disease</td>
					<td valign="top"><input type="checkbox" name="credit_other" id="credit_other" />Other</td>												
				</tr>
				<tr>
					<td valign="top"><input type="checkbox" name="liver" id="liver" />Liver Disease</td>
					<td valign="top"><input type="checkbox" name="mental_illness" id="mental_illness" />Mental Illness</td>
					<td valign="top"><input type="checkbox" name="pulmonary" id="pulmonary" />Pulmonary Disease</td>
				</tr>
				<tr>
					<td valign="top"><input type="checkbox" name="stroke" id="stroke" />Stroke</td>
					<td valign="top"><input type="checkbox" name="asthma" id="asthma" />Asthma</td>
					<td valign="top"><input type="checkbox" name="blood" id="blood" />High Blood Pressure</td>
				</tr>
			</table>
		</li>
		<li class="submitWrapper">
			<input type="submit" value="SUBMIT" class="submit" />
		</li>
		<li class="submitDisclaimer">
			By clicking submit, you agree to be contacted for a Free Health Insurance Quote.
		</li>
	</ul>
</form>
<?php } ?>
*/ ?>
<?php endif; ?>