	<?php

	require_once('db.php');

		if($_POST['gj_hidden'] == 'Y') {
			//Form data sent
				global $post;
				if ($_POST['id']) {

					if ($_POST['delete']) {
						//Delete Selected cat
						deleteCat($_POST['id']);
					} else {
						//Update existing cat
						$cat = array();
						foreach ($_POST as $key=>$value) {
							if ($key !== 'gj_hidden') {
								$cat[$key] = $value;
							}
						}
						editCat($cat);
					}

				} else {
					//Add new Category
					$cat = array();
					foreach ($_POST as $key=>$value) {
						if ($key !== 'gj_hidden') {
							$cat[$key] = $value;
						}
					}
					saveCat($cat);
				}

		}

		$GJ_cat = new GJ_cat();
        $cat = $GJ_cat->gj_get_cat();

		?>
		<div class="wrap">
			<?php    echo "<h2>" . __( 'GJ Maps Categories', 'gj_trdom' ) . "</h2>"; ?>

			<h4>Add New</h4>
				<form name="gj_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
					<input type="hidden" name="gj_hidden" value="Y"/>
					<input type="text" name="name" placeholder="Name"/>
					<input type="text" name="color" id="newColor" class="color-picker"/>

					<p class="submit"><input type="submit" value="<?php _e('Add Category', 'gj_trdom' ) ?>" /></p>
				</form>


			<h4>Edit Categories</h4>

				<?php

				foreach ($cat as $index=>$object) {
				?>
					<form name="gj_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
					<input type="hidden" name="gj_hidden" value="Y"/>
					<input type="hidden" name="id" value="<?php echo $object->id; ?>"/>

					<label for="name">Name: 
					<input type="text" name="name" placeholder="Name" value="<?php echo $object->name; ?>"/>
					</label>

					<label for="cat">Category: 
					<input type="text" name="color" class="color-picker" id="<?php $object->id; ?>Color" value="<?php echo $object->color; ?>"/>
					</label>

					<br />

					<label for="delete">Delete this Category? : 
					<input type="checkbox" name="delete"/>
					</label>

					<br />
					<input type="submit" name="Submit" value="<?php _e('Submit Changes', 'gj_trdom' ) ?>" />

					</form>

				<br /><hr /><br />
				<?php } ?>

		</div>