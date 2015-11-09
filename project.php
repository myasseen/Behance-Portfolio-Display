<?php
	$be_api  = 'API_KEY'; //generate your API key here: https://www.behance.net/dev/apps
	$be_user = 'USERNAME'; //username of account to pull projects from
	$project_id = isset($_GET['id']) && !preg_match('/[^0-9\-]/', $_GET['id']) ? $_GET['id'] : false;
	
	if($project_id != false) {
		$be_feed = file_get_contents('http://www.behance.net/v2/projects/' . $project_id . '?api_key=' . $be_api);
		$be_data = json_decode($be_feed);
		$project = $be_data->project;
		
		// Fields
		$field_count = count($project->fields);
		$field_index = 0;
		$field_list = '';
		foreach($project->fields as $field) {
			$field_list .= $field;
			if(++$field_index == $field_count) {
				break;
			} else {
				$field_list .= ', ';
			}
		}
		
		// Tags
		$tag_count = count($project->tags);
		$tag_index = 0;
		$tag_list = '';
		foreach($project->tags as $tag) {
			$tag_list .= $tag;
			if(++$tag_index == $tag_count) {
				break;
			} else {
				$tag_list .= ', ';
			}
		}
		
		// Tools
		$tool_count = count($project->tools);
		$tool_index = 0;
		$tool_list = '';
		foreach($project->tools as $tool) {
			$tool_list .= $tool->title;
			if(++$tool_index == $tool_count) {
				break;
			} else {
				$tool_list .= ', ';
			}
		}
		
		// Owners/team members
		$owners_count = count($project->owners);
		$owners_index = 0;
		$owners_list = '';
		foreach($project->owners as $owner) {
			$owners_list .= '<div class="team-member"><a href="' . $owner->url . '" target="_blank"><img src="' . $owner->images->{'100'} . '" alt="' . $owner->username . '" />' . $owner->first_name . ' ' . $owner->last_name . '</a></div>';
		}
		if($owners_count > 1) {
			$display_team = '<h3>Meet the Team</h3><div class="team">' . $owners_list . '</div>';
		} else {
			$display_team = '';
		}
		
		// Styles
		echo '
			<script type="text/javascript">
			$(\'section#project-' . $project->id . '\').ready(function(){
				$(\'section#project-' . $project->id . ' article\').css({
					background: \'#' . $project->styles->background->color . '\',
					paddingTop: \'' . $project->styles->spacing->project->top_margin . 'px\'
				});
				$(\'section#project-' . $project->id . ' article .module\').css({
					marginBottom: \'' . $project->styles->spacing->modules->bottom_margin . 'px\'
				});
			});
		</script>';
		/*	This pulls some of the styling from within the project.
			It can be removed if you prefer to style differently on your website,
		*/
		
		echo '
			<section id="project-' . $project->id . '" class="project">
				<header>
					<h1>' . $project->name . '</h1>
					<p>' . $field_list . '</p>
				</header>
				<article>
		';
		/*	This is the start of the wrapper for the project.
			This can be modified however you'd like.
		*/
		
		$mod_count = count($project->modules);
		$mod_index = 0;
		foreach($project->modules as $module) {
			echo '<div class="module mod-type-' . $module->type . ' mod-align-' . $module->alignment . ' mod-align-cap-' . $module->caption_alignment . ' mod-width-' . ($module->full_bleed == 1 ? 'full' : 'normal') . '">';
			switch($module->type) {
				case 'text':
					echo $module->text;
					break;
				case 'image':
					if( isset($module->caption_plain) && strlen($module->caption_plain) > 140) {
						$mod_img_alttxt = substr($module->caption_plain,0,140) . ' ...';
					} else if( isset($module->caption_plain) && strlen($module->caption_plain) > 140) {
						$mod_img_alttxt = $module->caption_plain;
					} else {
						$mod_img_alttxt = '';
					}
					
					$mod_img_src  = isset($module->sizes->src) ? $module->sizes->src : false;
					$mod_img_disp = isset($module->sizes->disp) ? $module->sizes->disp : false;
					$mod_img_1200 = isset($module->sizes->max_1200) ? $module->sizes->max_1200 : false;
					$mod_img_1240 = isset($module->sizes->max_1240) ? $module->sizes->max_1240 : false;
					$mod_img_1400 = isset($module->sizes->{'1400'}) ? $module->sizes->{'1400'} : false;
					$mod_img_1920 = isset($module->sizes->max_1920) ? $module->sizes->max_1920 : false;
					$mod_img_3840 = isset($module->sizes->max_3840) ? $module->sizes->max_3840 : false;
					$mod_img_orig = isset($module->sizes->original) ? $module->sizes->original : false;
					
					if($mod_img_3840 != false) {
						echo '<div class="mod-img"><img src="' . $mod_img_3840 . '" alt="' . $mod_img_alttxt . '" /></div>';
					} else if($mod_img_1920 != false) {
						echo '<div class="mod-img"><img src="' . $mod_img_1920 . '" alt="' . $mod_img_alttxt . '" /></div>';
					} else if($mod_img_1400 != false) {
						echo '<div class="mod-img"><img src="' . $mod_img_1400 . '" alt="' . $mod_img_alttxt . '" /></div>';
					} else if($mod_img_1240 != false) {
						echo '<div class="mod-img"><img src="' . $mod_img_1240 . '" alt="' . $mod_img_alttxt . '" /></div>';
					} else if($mod_img_1200 != false) {
						echo '<div class="mod-img"><img src="' . $mod_img_1200 . '" alt="' . $mod_img_alttxt . '" /></div>';
					} else if($mod_img_disp != false) {
						echo '<div class="mod-img"><img src="' . $mod_img_disp . '" alt="' . $mod_img_alttxt . '" /></div>';
					} else {
						echo '<div class="mod-img"><img src="' . $mod_img_src  . '" alt="' . $mod_img_alttxt . '" /></div>';
					}
					
					if(isset($module->caption)) {
						echo '<div class="mod-img-cap">' . $module->caption . '</div>';
					}
					break;
				default: break;
			}
			echo '</div>';
			
			if(++$mod_index == $mod_count) { break; }
		}
		
		echo '
				</article>
				<aside>
					<h3>About This Project</h3>
					<p class="p-desc">' . $project->description . '</p>
					<h3>Tools Used</h3>
					<p class="p-tools">' . $tool_list . '</p>
					<h3>Tags</h3>
					<p class="p-tags">' . $tag_list . '</p>' .
					$display_team .
					'<h3>Behance Stats</h3>
					<p class="p-stats"><span class="p-views">' . $project->stats->views . '</span><span class="p-likes">' . $project->stats->appreciations . '</span><span class="p-comments">' . $project->stats->comments . '</span></p>
					<p class="p-date">
						Posted: ' . date("F jS, Y",$project->published_on) . (isset($project->modified_on) && $project->modified_on != $project->published_on ? '<br />
						Edited: ' . date("F jS, Y",$project->modified_on) : '') . '<br />
						<a href="' . $project->url . '" target="_blank">View on Behance</a>
					</p>
				</aside>
				<div class="clear"></div>
			</section>
		';
		/*	This is the end of the wrapper for the project.
			Again, t can be modified however you'd like.
			I used the 'aside' element to create a sidebar,
			similar to how it is displayed on the Behance website,
			but you can change it however you'd like.
		*/
	} else {
		echo 'Ooops! Something\'s wrong. I cannot load the project you have requested.';
	}
?>
