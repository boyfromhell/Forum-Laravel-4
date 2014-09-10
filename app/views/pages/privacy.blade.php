@extends('layout')

@section('content')

<div class="welcome wide no-margin">

	<div class="header">Privacy Policy</div>

	<div class="body">
	
	<p>
	<b>What information do we collect?</b>
	</p>

	<p>
	We collect information from you when you register on our site.
	</p>

	<p>
	<b>What do we use your information for?</b>
	</p>

	<p>
	Any of the information we collect from you may be used in one of the following ways:
	</p>

	<p>
	<ul>
	<li>To personalize your experience<br />(your information helps us to better respond to your individual needs)
	<li>To improve our website<br />(we continually strive to improve our website offerings based on the information and feedback we receive from you)
	<li>To improve customer service<br />(your information helps us to more effectively respond to your customer service requests and support needs)
	<li>To administer a contest, promotion, survey or other site feature
	</ul>
	</p>

	<p>
	<b>How do we protect your information?</b>
	<p>

	<p>
	We implement a variety of security measures to maintain the safety of your personal information when you enter, submit, or access your personal information.
	</p>

	<p>
	<b>Do we use cookies?</b>
	</p>

	<p>
	Yes (Cookies are small files that a site or its service provider transfers to your computers hard drive through your Web browser (if you allow) that enables the sites or service providers systems to recognize your browser and capture and remember certain information)
	</p>

	<p>
	We use cookies to understand and save your preferences for future visits and compile aggregate data about site traffic and site interaction so that we can offer better site experiences and tools in the future. We may contract with third-party service providers to assist us in better understanding our site visitors. These service providers are not permitted to use the information collected on our behalf except to help us conduct and improve our business.
	</p>

	<p>
	<b>Do we disclose any information to outside parties?</b>
	</p>

	<p>
	We do not sell, trade, or otherwise transfer to outside parties your personally identifiable information. This does not include trusted third parties who assist us in operating our website, conducting our business, or servicing you, so long as those parties agree to keep this information confidential. We may also release your information when we believe release is appropriate to comply with the law, enforce our site policies, or protect ours or others rights, property, or safety. However, non-personally identifiable visitor information may be provided to other parties for marketing, advertising, or other uses.
	</p>

	<p>
	<b>Third party links</b>
	</p>

	<p>
	Occasionally, at our discretion, we may include or offer third party products or services on our website. These third party sites have separate and independent privacy policies. We therefore have no responsibility or liability for the content and activities of these linked sites. Nonetheless, we seek to protect the integrity of our site and welcome any feedback about these sites.
	</p>

	<p>
	<b>California Online Privacy Protection Act Compliance</b>
	</p>

	<p>
	Because we value your privacy we have taken the necessary precautions to be in compliance with the California Online Privacy Protection Act. We therefore will not distribute your personal information to outside parties without your consent.
	</p>

	<p>
	<b>Childrens Online Privacy Protection Act Compliance</b>
	</p>

	<p>
	We are in compliance with the requirements of COPPA (Childrens Online Privacy Protection Act), we do not collect any information from anyone under 13 years of age. Our website, products and services are all directed to people who are at least 13 years old or older.
	</p>

	<p>
	<b>Terms and Conditions</b>
	</p>

	<p>
	Please also visit our Terms and Conditions section establishing the use, disclaimers, and limitations of liability governing the use of our website at <a href="/terms">http://{{{ Config::get('app.domain') }}}/terms</a>
	</p>

	<p>
	<b>Your Consent</b>
	</p>

	<p>
	By using our site, you consent to our online privacy policy</a>.
	</p>

	<p>
	<b>Changes to our Privacy Policy</b>
	</p>

	<p>
	If we decide to change our privacy policy, we will post those changes on this page, and/or update the Privacy Policy modification date below.
	</p>

	<p>
	<i>This policy was last modified on August 8, 2012</i>
	</p>

	<p>
	<b>Contacting Us</b>
	</p>

	<p>
	If there are any questions regarding this privacy policy you may contact us using the information below.
	</p>

	<p>
	<a href="/">http://{{{ Config::get('app.domain') }}}/</a><br>
	{{{ Config::get('app.forum_name') }}}<br>
	29617 N Waukegan Rd Apt 303<br>
	Lake Bluff, IL 60044<br>
	United States<br>
	{{{ Config::get('app.admin_email') }}}
	</p>

	</div>
</div>

@stop
