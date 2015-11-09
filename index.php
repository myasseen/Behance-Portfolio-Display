<?php
	$be_api  = 'API_KEY'; //generate your API key here: https://www.behance.net/dev/apps
	$be_user = 'USERNAME'; //username of account to pull projects from

	// Project List
	$be_limit = 10;
	$be_feed = file_get_contents('http://www.behance.net/v2/users/' . $be_user . '/projects?api_key=' . $be_api);
	$be_data = json_decode($be_feed);
	$be_count = 0;
	if( $be_data->http_code == 200) {
		foreach($be_data->projects as $project) {
			echo '<div class="project-thumb">';
				echo '<a href="project.php?id=' . $project->id . '">';
				/*	An alternate option would be to use jQuery and Ajax to pull in the
					projects dynamically, which is what I did on my website, but for the
					sake of simplicity in this example, a regular link to project.php
					will work just as well.
				*/
					echo '<div class="img"><img src="' . $project->covers->{'404'} . '" alt="' . $project->name . '" /></div>';
					echo '<div class="txt">';
						echo '<h3>' . $project->name . '</h3>';
						echo '<p>';
						$field_limit = count($project->fields);
						$field_count = 0;
						foreach($project->fields as $field){
							echo $field;
							if(++$field_count < $field_limit) echo ', ';
						}
						echo '</p>';
					echo '</div>';
				echo '</a>';
			echo '</div>';
			/*	You can adjust the HTML however you want to make it display
				however you'd like.
			*/
			
			if(++$be_count == $be_limit) break;
		}
	} else {
		echo 'Error: ' . $be_data->http_code;
	}
?>
