<!DOCTYPE HTML>
<html>
	<?php
		$title = "Web Authentication via Keystroke Dynamics";
		include( 'components/head.php' );
		include_once( 'components/database_fns.php' );
		include_once( 'components/keystroke_data_handlers.php' );
	?>
	<body data-spy="scroll" data-target=".subnav" data-offset="50">
		<?php include( 'components/top_menu.php' ); ?>
        <div class="container">
            <!-- Masthead
           ================================================== -->
            <?php include( 'components/page_header.php' ); ?>
			<?php
				// Set up our variables
				$phrase = cleanse_sql_and_html( $_POST['inputKeyPhrase'] );
				$i = intval(cleanse_sql_and_html( $_POST['iteration'] ));
				$totalStepsInTraining = 15;
				
				// Store previous submission's data in the DB
				insert_training_data_into_table( $phrase, $_POST['timingData'] );
			?>
			
			<section>
				<h2>Account Creation Successful!</h2>
				<div class="progress">
					<?php $totalStepsInTraining = 15; ?>
					<div class="bar" style="width: 100%;">Step <?php echo $totalStepsInTraining ?> of <?php echo $totalStepsInTraining ?></div>
				</div>
				<p>Training the system with your data . . .</p>
				<?php
					// Get all known data for this user's key phrase
					$rawTrainingData = getTrainingData( $phrase );
					
					// Format the user's data for the R script
					$formattedTrainingData = prepareTrainingData( $rawTrainingData );
					
					// Write the training data to a CSV file for the R script
					// TODO: Confirm this is right.
					$f = file( 'r/training_data.csv' );
					fwrite( $f, $formattedTrainingData );
					
					
					// Call the R script
					// TODO: Oh yeah, make this work.
					exec("/usr/bin/Rscript r/trainer.R" . ' 2>&1', $out, $return_status);
				
				?>
				<p>Successfully created your account using key phrase <strong><?php echo $phrase;?></strong>.</p>
			</section>
			
			<section>
				<h2>Log</h2>
				<p id="theLog">
				<pre>Training data: <?php echo $formattedTrainingData; ?>
				</p>
			</section>
		</div><!-- container -->
		<?php include( 'components/footer.php' ); ?>
	</body>
</html>
