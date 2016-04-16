<?php include("includes/head.main.inc.php"); ?>
	<div id="mainBanner">
		<div id="title">
			<h1>Create Template</h1>
		</div>
	</div>
	<div id="mainContent">
		<div class="break"></div>
		<p>
			The new Template based Config Generation feature, new to <span class="rconfigNameStyle">rConfig</span> version 3.1.0, allows for easy creation of configuration files that are based off a standard template.
		</p>
		<div class="break"></div>
		<p>
			You are able to create multiple Config Templates to use when creating configs to base them on.  For example: you can create a template for routers at your remote sites, and another for switches at your remote sites.
		</p>
		<div class="break"></div>
		<p>
			To get started, click on The Templates menu, where you'll be taken to the Create Template page.  Click the Add Config Template button, as seen in fig1.
		</p>
		<div class="break"></div>
		<p>
			fig1.
			<div class="break"></div>
			<img class="imgfig" src="images/template1.jpg"/>
		</p>
		<div class="break"></div>
		<p>
			In the form, create a unique template name and description.  For the Config Template, paste in the standard config template you want to use when generating new configuration files.  The only difference is, you need to substitute out any settings that change on a per-device basis.  Replace each occurance of this variable setting with a text string surrounded by two @ symbols.  It does not need to be capitalized, though in this example it is for ease of recognition (see fig2).  Click Save once complete.
		</p>
		<div class="break"></div>
		<p>
			For example: In the below picture example, I’ve replaced the following line items in a normal running-configuration file with variable settings:
			<ul><li>The IP address for the default gateway becomes @GATEWAY@</li>
			<li>The access vlan is replaced by @DATAVLAN@ for every occurance of the data vlan number</li>
			<li>The voice vlan is replaced by @VOICEVLAN@ for every occurance of the voice vlan number</li>
			<li>The common interface description is replaced by @DESC@ for each interface needing this description.</li></ul>
		</p>
		<div class="break"></div>
		<p>
			fig2.
			<div class="break"></div>
			<img class="imgfig" src="images/template2.jpg"/>
		</p>
		<div class="break"></div>
		<p>
			Once the base template is created, you can generate multiple running-configs off of one template.  Select the Config Template you want to generate off of, then click the Generate Config button (fig3).
		</p>
		<div class="break"></div>
		<p>
			fig3.
			<div class="break"></div>
			<img class="imgfig" src="images/template3.jpg"/>
		</p>
		<div class="break"></div>
		<p>
			In the form, the Template Name and Extracted Variables are set to Read-Only, as they are based off the previously created template.
		</p>
		<div class="break"></div>
		<p>
			For New Config Name, create a unqiue for this config, such as the device or location it will be used for.  In this example, this is for the switch in New York.
		</p>
		<div class="break"></div>
		<p>
			In the Variable Substitution box, you need to enter in the value you wish to substitute in for EACH variable previously set in the Config Template.  These variables match up on a 1-to-1 basis, so the first item you put in this box will be used for the first variable, the second line will be for the second variable, etc.  Click Save when done.
		</p>
		<div class="break"></div>
		<p>
			For example:
			<ul><li>I am substituting the phrase “Office User” everywhere in the Config Template where I put @DESC@.</li>
			<li>I am substituting the number 20 everywhere in the Template where the variable @DATAVLAN@ was located</li>
			<li>I am substituting the number 30 everywhere in the Template where the variable @VOICEVLAN@ was located</li>
			<li>I am substituting the IP address 10.0.0.1 everywhere in the Template where the variable @GATEWAY@ was located</li></ul>
		</p>
		<div class="break"></div>
		<p>
			fig4.
			<div class="break"></div>
			<img class="imgfig" src="images/template4.jpg"/>
		</p>
		<p>
			Once the config is generated based off a template, you can view the newly generated config file by going to the Generated Configs sub-menu.
		</p>
		<div class="break"></div>
		<div class="break"></div>
	</div>
</body>
</html>