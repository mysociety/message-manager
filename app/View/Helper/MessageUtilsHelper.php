<?php
App::uses('AppHelper', 'View/Helper');

class MessageUtilsHelper extends AppHelper {

	// return a string describing lock expiry (in 30 seconds, 2 days ago, etc)
	function pretty_lock_duration($seconds) {
		if ($seconds == 0) {
			return "expires now";
		} else {
			$s = abs($seconds);
			if ($s < 120) {
				$unit = "second";
			} else {
				$s = round($s/60);
				if ($s < 60) {
					$unit = "minute";
				} else {
					$s = round($s/60);
					if ($s < 24) {
						$unit = "hour";
					} else {
						$s = round($s/24);
						$unit = "day";
					}
				}
			}
			if ($s > 1) {
				$unit .= "s";
			}
			if ($seconds > 0) {
				return "expires in $s $unit";
			} else {
				return "expired $s $unit ago";
			}
		}
	}
	
	function fms_report_url($fms_id) {
		return Configure::read('fms_site_url') .
			preg_replace('/%s/', $fms_id, Configure::read('fms_report_path'));
	}
	
	// recursively display replies: expects 'children' array, from a actsAs Tree query
	function message_entry_in_thread($replies, $depth) { 
		$css_class = "reply-" . min($depth, 6); // arbitrary limit to nesting
		foreach ($replies as $reply) { ?>
			<dt class="<?php echo($css_class);?>">Reply
			</dt>
			<dd class="<?php echo($css_class);?>">
				<a href="<?php echo $this->Html->url(array('action' => 'view', $reply['Message']['id'])); ?>" class="reply-link">
					<span class="message-sender">
						<?php echo h($reply['Message']['from_address']); ?>
					</span>
					<?php echo h($reply['Message']['message']); ?>
				&nbsp;
				</a>
			</dd>
		<?php 
			if ($reply['children']) {
				self::message_entry_in_thread($reply['children'], $depth+1);
			}
		}
	}
	
	// pretty print (HTML) tag list (sorted, comma-sep, "no-tag" at end)
	// note: not sanitising the HTML because tags are currently alpha-num only
	function pretty_tag_list_html($allowed_tags = null) {
		if (is_string($allowed_tags)) {
			$allowed_tags = preg_split("/[\s,]+/", strtoupper($allowed_tags));
		}
		if (empty($allowed_tags)) {
			return __("<em>any</em>");
		}
		sort($allowed_tags);
		$empty_tag_indices = array_keys($allowed_tags, Configure::read('no_tag_symbol'));
		if ($empty_tag_indices) {
			foreach ($empty_tag_indices as $ix) {
				array_splice($allowed_tags, $ix, 1); // delete that element (may be dups, so iterate)
			}
			array_push($allowed_tags, __("<em>no&nbsp;tag</em>"));
		}
		return implode(', ', $allowed_tags);
	}
	
} 

?>
