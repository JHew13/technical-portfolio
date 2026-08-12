<?php
/*
 * Audit Dates Agent View
 * Backend endpoint that retrieves audit-related records for display in an internal web interface.
 *
 * Portfolio note:
 * This public sample is based on code I originally wrote for an internal business tool.
 * Proprietary names, endpoints, identifiers, sample data, and environment-specific
 * values have been replaced with generic equivalents.
 */

 // ini_set('display_errors', 1);
?>

<?php include ("DATABASE_CONFIG.php");?>

<?php

 // Starts the PHP session so values can be shared between the agent and admin sections.
 session_start();

ob_start();

// Pulls the signed-in Windows account from the server authentication information.
$agntSgnin = $_SERVER['AUTH_USER'];

// Splits the domain and username so the username can be used to find the matching agent record.
$WindowLogin= explode("\\",$agntSgnin);

$AgentName = $WindowLogin[1];
$DomainName= $WindowLogin[0];
$Status=1;
	
	echo $AgentName;
        echo  $_SESSION["1"];
        echo  $_SESSION["2"];
        echo  $_SESSION["3"];
        echo  $_SESSION["4"];
        echo  $_SESSION["5"];
{

	//-------------------------------------------------------------PRODUCTION PAGE-----------------------------------------------------------
				
	
	
	// Pulls the most recent scorecard record for the currently signed-in agent.
	$query=$conn->query("Select 
*,
	date_format(HireDate,'%m/%d/%Y') as HireDate 
	
	from vw_Agent_Monthly_Scorecard where WinLog = '$AgentName'
	
	ORDER BY LoadDate DESC LIMIT 1 ");


	//-------------------------------------------------------------PRODUCTION PAGE-----------------------------------------------------------


	
	// Checks that the query returned a record before trying to build the response.
	$rowCount=$query->num_rows;
   
	if($rowCount>0)

		
	{
		// Loops through the returned database row and maps the values into a structured response.
		while($row = mysqli_fetch_assoc($query))
		{
			
		
				
				// Builds the data object that will be returned to the page as JSON.
				$output = array 
				(
					
			//-------------------------------------------------------------PRODUCTION PAGE-----------------------------------------------------------
			
			//Start Agent Id Card-------------	
				 "Fullname" => $row['Fullname'],
				 "JobTitle" => $row['JobTitle'],
				 "HireDate" => $row['HireDate'],
				 "CiscoID" => $row['CiscoID'],
				 "IEX_ID" => $row['IEX_ID'],
				 "WinLog" => $row['WinLog'],
			//End Agent Id Card---------------
					
				//-------------------------------------------------------------PRODUCTION PAGE-----------------------------------------------------------
		
					
			//Start Agent Score Overall-------
				"Metric1Point" => $row['Metric1Point'],
				"Metric2Point" => $row['Metric2Point'],
				"Metric3Point" => $row['Metric3Point'],
				"TotalScore" => $row['TotalScore'],
			//Start Agent Score Overall------
				
				//-------------------------------------------------------------PRODUCTION PAGE-----------------------------------------------------------
		
					
			//Start BreakDownTitles-----------------
				"M1ActMin1Name" => $row['M1ActMin1Name'], 		// TalkTimeTitle	
				"M1ActMin2Name" => $row['M1ActMin2Name'], 		// ACW Title
				"Metric2Name" => $row['Metric2Name'],    		// Adh+ Title
			//End BreakDownTitles-----------------	
					
				//-------------------------------------------------------------PRODUCTION PAGE-----------------------------------------------------------
		
					
			//Start Volume-------------	
				"M1ActMin1" => $row['M1ActMin1'],         		 //TalkTimeMins
				"M1ActMin2" => $row['M1ActMin2'], 		  		 //ACWTimeMins
				"Metric1Metric" => $row['Metric1Metric'],  		 //Volume Total 
			//End Volume--------------------	
					
				//-------------------------------------------------------------PRODUCTION PAGE-----------------------------------------------------------
		
					
			//Start Adherence--------------------			
				"Metric2Metric" => $row['Metric2Metric'],  		 //ADH Metric
				"M2MinutesIn" => $row['M2MinutesIn'],  		  	 //MinIn 
				"M2MinutesOut" => $row['M2MinutesOut'],  	 	 //MinOut Metric
			//End Adherence--------------------	
					
					
				//-------------------------------------------------------------PRODUCTION PAGE-----------------------------------------------------------
		
			//Start Quality--------------------	
				"M3Audit1" => $row['M3Audit1'],		//QualityAudit1
				"M3Audit2" => $row['M3Audit2'],		//QualityAudit2
				"M3Audit3" => $row['M3Audit3'],		//QualityAudit3
				"M3Audit4" => $row['M3Audit4'],		//QualityAudit4
				"M3Audit5" => $row['M3Audit5'],		//QualityAudit5		
			//End Quality--------------------		
			"CPI" => $row['CPI'],

				
		//-------------------------------------------------------------PRODUCTION PAGE-----------------------------------------------------------

					
					
					
				
					
	$IEX_IDVar=$row['IEX_ID'],
	$Namecpi=$row['Fullname']
				 
				 
				 );	
					
		
				
					 

			//print_r(array_values($query));
			//echo'<option value="'.$row['Fullname'].'">'.$row['IEX_ID'].'</option>';
			//echo'<option value="'.$row['IEX_ID'].'">'.$row['BalancedScore'].'</option>';
}
		
   // Saves the selected agent information in the session so it can be reused by other page logic.
   $_SESSION["IEX_MATCH"] = $IEX_IDVar;
   $_SESSION["Namecpi"] = $Namecpi;

		echo  $_SESSION["IEX_MATCH"];
		echo  $_SESSION["Namecpi"];


        	ob_end_clean();
		
			// Converts the PHP array into JSON so the frontend can consume the returned data.
			echo json_encode($output);

			
			
		
			
	}
}


{
//-------------------------------------------------------------ADMIN PAGE-----------------------------------------------------------
					
?>
<?php
// Starts/reuses the session for the admin-side lookup.
session_start();
	// Pulls the selected agent's monthly scorecard information for the admin view.
	$query=$conn->query("Select

*


    
	from dbo.tbl_Agent_Scorecard_ByMonth
    
    where IEX_ID = '$MatchAgentIEX'  order by IEX_ID asc");




//-------------------------------------------------------------ADMIN PAGE-----------------------------------------------------------

	
	$rowCount=$query->num_rows;
   
	if($rowCount>0)

		
	{
		while($row = mysqli_fetch_assoc($query))
		{
			
		
				
				$output2 = array 
				(
					
//-------------------------------------------------------------ADMIN PAGE-----------------------------------------------------------

                    
                    	//Start Quality--------------------	
					$AuditDateAgent1= $row['M3Audit1Date'],		//QualityAudit1
				 	$AuditDateAgent2=$row['M3Audit2Date'],		//QualityAudit2
				    $AuditDateAgent3=$row['M3Audit3Date'],		//QualityAudit3
			     	$AuditDateAgent4=$row['M3Audit4Date'],		//QualityAudit4
			    	$AuditDateAgent5= $row['M3Audit5Date']
		//-------------------------------------------------------------ADMIN PAGE-----------------------------------------------------------
				 
				 );	

	//-------------------------------------------------------------ADMIN PAGE-----------------------------------------------------------


			//print_r(array_values($query));
			//echo'<option value="'.$row['Fullname'].'">'.$row['IEX_ID'].'</option>';
			//echo'<option value="'.$row['IEX_ID'].'">'.$row['BalancedScore'].'</option>';
}			
        

        
        	// Stores each audit date in the session so the values can be displayed/reused elsewhere.
        	$_SESSION["1"] = $AuditDateAgent1;
        	$_SESSION["2"] = $AuditDateAgent2;
        	$_SESSION["3"] = $AuditDateAgent3;
        	$_SESSION["4"] = $AuditDateAgent4;
        	$_SESSION["5"] = $AuditDateAgent5;



        

			ob_end_clean();
		



	}
}


?>

