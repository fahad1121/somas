<?php

function boost_upgrade_to_1_0_3(){

	$boost_model = new Boost_Boost_Model(BOOST_PLUGIN_NAME, BOOST_PLUGIN_VERSION);

	$all_boosts = $boost_model->get_boosts(array());

	foreach ($all_boosts as $boost) {
		$boost_data = stripslashes_deep((array)$boost);
		$boost_model->update_boost($boost_data, $boost_data['id']);
	}

}