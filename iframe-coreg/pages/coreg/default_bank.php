<script type="text/javascript" src="/js/coregFunctions.js"></script>
<form class="formWrapper" method="post" action="javascript:void(0);" onsubmit="submitForm('bank', this, '<?= $this->getNextUrl($this->position, true) ?>');">
	<h2>Are You Interested In Speaking To A Bankruptcy Lawyer?</h2>
	<p>Talk to a bankruptcy lawyer on whether or not filing for bankruptcy is the right option for you.</p>
	<div class="formProcessWaiting">
		<img src="/templates/<?= $this->templateFolder ?>/ajax-loader.gif" alt="" style="margin-right: 5px;" />
		Processing Form, Please Wait...
	</div>
	<ul id="bankForm" class="coregForm">
		<li>
			<input type="hidden" class="formInputHidden" name="refererUrl" id="bankRefererUrl" value="<?= rawurlencode($_SERVER['HTTP_REFERER']) ?>" />
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
<div id="skipWrapper">
	Once you've completed this step, go to the next one.
	<a class="skipButton floatRight" href="<?= $this->nextUrl ?>">CONTINUE</a>
</div>
