<script type="text/javascript" src="/js/coregFunctions.js"></script>
<form class="formWrapper" method="post" action="javascript:void(0);" onsubmit="submitForm('edu', this, '<?= $this->getNextUrl($this->position, true) ?>');">
	<h2>Have You Considered Additional Education, Or Maybe A Career Change?</h2>
	<p>Speak to a Career Institute Specialist about a degree from a Leading Online University. <a href="http://www.esmartliving.com/1on1-privacy.html" target="_blank" rel="nofollow" style="font-size:10px;">Privacy Policy</a></p>
	<div class="formProcessWaiting">
		<img src="/templates/<?= $this->templateFolder ?>/ajax-loader.gif" alt="" style="margin-right: 5px;" />
		Processing Form, Please Wait...
	</div>
	<ul id="eduForm" class="coregForm">
		<li>
			<input type="hidden" class="formInputHidden" name="refererUrl" id="eduRefererUrl" value="<?= rawurlencode($_SERVER['HTTP_REFERER']) ?>" />
			<label>First Name*</label>
			<input type="text" class="formInputText" name="firstName" id="eduFirstName" value="<?= (!empty($this->userInfo['firstname'])) ? $this->userInfo['firstname'] : '' ?>"/>
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
<div id="skipWrapper">
	Once you've completed this step, go to the next one.
	<a class="skipButton floatRight" href="<?= $this->nextUrl ?>">CONTINUE</a>
</div>