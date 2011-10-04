<script type="text/javascript" src="/js/coregFunctions.js"></script>
<form class="formWrapper" method="post" action="javascript:void(0);" onsubmit="submitForm('debt', this, '<?= $this->getNextUrl($this->position, true) ?>');">
	<h2>Do You Have More Than $10K In Credit Card Debt?</h2>
	<p>If you could eliminate your debt without permanently damaging your credit, why wouldn't you? We can help!</p>
	<div class="formProcessWaiting">
		<img src="/templates/<?= $this->templateFolder ?>/ajax-loader.gif" alt="" style="margin-right: 5px;" />
		Processing Form, Please Wait...
	</div>
	<ul id="debtForm" class="coregForm">
		<li>
			<input type="hidden" class="formInputHidden" name="refererUrl" id="debtRefererUrl" value="<?= rawurlencode($_SERVER['HTTP_REFERER']) ?>" />
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
<div id="skipWrapper">
	Once you've completed this step, go to the next one.
	<a class="skipButton floatRight" href="<?= $this->nextUrl ?>">CONTINUE</a>
</div>
