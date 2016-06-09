<!DOCTYPE html>
<?php 
	$PS1 = str_replace("\n","","[".shell_exec('whoami')." @ ".shell_exec("hostname")."] >> ");
?>

<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js">
		</script>
		<link rel="stylesheet" type="text/css" href="shell.css">
	</head>
	<div id="wrapper">
		<body>
			<div id="resultWrapper">
			</div>
			<div id="promptBox"><div class="promptPS1"> 
				<?php print $PS1; ?>
			</div>
			<input id="shell" class="promptInput" autofocus on type="text">  </div>
		</body>	
	</div>
</html>

<script type="text/javascript">
//Looks after the up/down command cycling
var commandHistory = [" "];
var historyPointer = 0;
//Used to control command std_out flow
var stackpointer = 0;
var resultCounter = [0];

var prompt = document.getElementById("shell");

//prompt input event handler
prompt.addEventListener("keyup", function(event) 
{
	event.preventDefault();
	if(event.keyCode == 38)
	{
		if(commandHistory[0] != undefined && historyPointer > 0)
		{
			prompt.value = commandHistory[historyPointer];
			historyPointer--;
			console.log(historyPointer);
		}
	}	
   	else if(event.keyCode == 40)
	{
		if(commandHistory[0] != undefined && historyPointer < commandHistory.length-1)
		{
			prompt.value = commandHistory[historyPointer+1];
			historyPointer++;
			console.log(historyPointer);
		}
	}  
	else if(event.keyCode == 13) 
	{
		var command = document.getElementById("shell").value;
		$.ajax({
			type: "POST",
			url: "exec.php",
			datatype: 'json',
			cache: false,
			data: { data : command },
			success: function(data)
			{
				//Create div with result from exec()
				$("#resultWrapper").append("<div id='"+stackpointer+"'><a style='margin-bottom:2px;color:white;'><?php echo $PS1 ?> $ "+command + "</a><br>" + printJSON(data) + "</div>");				
				//Handle the text buffer/flow
				stackpointer++;
				resultCounter.push(stackpointer);

				//If more than 10 divs, start clearing.
				if(resultCounter.length > 10)
				{
					document.getElementById(resultCounter[0]).remove();
					resultCounter.shift();
				}
				//Keep at the top of text stack
				var scrollRegion = document.getElementById("resultWrapper");
				scrollRegion.scrollTop = scrollRegion.scrollHeight;
				//Add an entry to our command history
				commandHistory.push(prompt.value);
				console.log(commandHistory);
				//Clear our input prompt
				prompt.value = "";
			
				historyPointer++;
			}
		});  
    }
});

//Will deserialize the result of a command
function printJSON(info)
{
	var myObj = JSON.parse(info);
	var output = "";

	//console.log(myObj);

	for(i = 0; i < myObj.length; i++)
	{
		output += (myObj[i]) + "</br>";
	}
	console.log(output);
	return output;
}
</script>
