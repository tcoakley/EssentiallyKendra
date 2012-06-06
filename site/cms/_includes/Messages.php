<!-- Message area -->
<?php
	if (array_key_exists("ErrorMessage", $_SESSION)) {
		if (is_array($_SESSION["ErrorMessage"])) {
			?>
			<div id="MessageBorder">
				<div id="ErrorMessage">
					<?php print implode("<br>\n", $_SESSION["ErrorMessage"])?>

				</div>
			</div>
			<div class="clear"></div>
			<?php
			ClearErrors();
		}
	}
	if (array_key_exists("IncomingMessage", $_SESSION)) {
		if (is_array($_SESSION["IncomingMessage"])) {
			?>
			<div id="MessageBorder">
				<div id="IncomingMessage">
					<?php print implode("<br>\n", $_SESSION["IncomingMessage"])?>

				</div>
			</div>
			<div class="clear"></div>
			<?php
			ClearIncomingMessages();
		}
	}
	if (array_key_exists("PendingMessage", $_SESSION)) {
		if (is_array($_SESSION["PendingMessage"])) {
			?>
			<div id="MessageBorder">
				<div id="PendingMessage">
					<?php print implode("<br>\n", $_SESSION["PendingMessage"])?>

				</div>
			</iv>
			<div class="clear"></div>
			<?php
			ClearPendingMessages();
		}
	}
?>
<!-- /Message area -->
