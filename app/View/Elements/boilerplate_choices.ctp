<p style="margin-bottom:0;">
	<?php 
	switch ($name) {
	    case "lang": ?>
		When you enter a value for <strong>language</strong>, you should pick one of the existing values (shown below: just
		click on them to use them).
		The exception is if you're <em>sure</em> you're adding the first string in a language that's not here already.
		<?php
	        break;
	    case "type": ?>
			Currently the API knows about <code>reply</code> and <code>hide-reason</code> only. You almost certainly
			should use one of these for the <strong>type</strong> value.
		<?php
	        break;
	} ?>
</p>
<div class="mm-string-choices">
	Click to use existing values:
	<?php
		foreach ($choices as $ch) {
			echo("<a href='#$name'>$ch</a>");
		}
	?>
</div>