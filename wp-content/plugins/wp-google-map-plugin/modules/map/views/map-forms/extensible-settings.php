<?php
$extensibleSettings = '';
$markup = apply_filters('wpgmp_add_MoreSettings',$extensibleSettings);
echo wp_kses_post($markup);
?>
