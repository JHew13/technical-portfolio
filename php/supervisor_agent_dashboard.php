<?php
/*
 * Supervisor Agent Dashboard
 * Internal dashboard that retrieves and presents individual employee operational data.
 *
 * Portfolio note:
 * This public sample is based on code I originally wrote for an internal business tool.
 * Proprietary names, endpoints, identifiers, sample data, and environment-specific
 * values have been replaced with generic equivalents.
 */

/*
 * SANITIZED PORTFOLIO SAMPLE
 * Original business-specific identifiers, internal paths, table names,
 * system acronyms, and endpoint names have been replaced with generic terms.
 * No credentials or production connection details are included in this file.
 */
?>

<?php
// Pull in the database connection so this page can run the queries it needs.
include ('config/database.php');


?>

<?php
// Check who is logged in and what level of access they have before loading the page.
include ("../auth/check_role.php"); ?>

<?php

// Save the user's current permission type so I can decide where they should be sent.
$PermissionType = $UserStatusNew;




					// If there is no permission set yet, send the user to the access registration page.
					if (is_null($PermissionType) === true){
							
						 
							header("location: access-registration.php");
                    }
                        
					// Agents should not be able to use the supervisor view, so send them back to their dashboard.
					else if  ($PermissionType === "Agent" ){
							 
							 header("location: ../dashboard.php");
                            
                        
					

exit();
						
						}




?>










<?php


// Grab the unique supervisor list for the dropdown at the top of the page.
$query=$conn->query("SELECT  DISTINCT Supervisor, Sup_ID  FROM supervisor_monthly_scorecard ORDER BY Supervisor ASC");

$rowCount=$query->num_rows;
			


?>




<!doctype html>

<html >
	
	
<head>

	
	<title>Supervisor Daily</title>

<!--------- Start Link to Local Files ------------>
	
			<link href="../assets/css/page-background.css?version=51" rel="stylesheet" type="text/css"> 
			
		    <link href="../assets/css/supervisor-dashboard.css?version=51" rel="stylesheet" type="text/css"> 
			

			<link href="../assets/css/dropdown-table.css?version=51" rel="stylesheet" type="text/css"> 
	
    <link rel="stylesheet" type="text/css" href="../assets/css/agent-overview.css?version=51">
	
			<link href="../assets/css/home.css?version=51" rel="stylesheet" type="text/css">	
	
	        <!-- Load c3.css -->

<!--------- End Link to Local Files ------------>


	

    <!--------- Start Link to External Files ------------>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



    <!--------- End Link to Local Files ------------>

    <script src="../pure-knob-master/pureknob.js" type="text/javascript"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>


	
<meta charset="utf-8">
	

	<script>
    // Small helper I use when I need to check whether a returned value is empty or null.
    function isEmpty(value) {
              switch (typeof(value)) {
                case "string": return (value.length === 0);
                case "number":
                case "boolean": return false;
                case "undefined": return true;
                case "object": return !value ? true : false; // handling for null.
                default: return !value ? true : false
              }
            }</script>
    
    

    
    
    

	
	
   

    <script>
        $( document ).ready( function ( demoKnob ) {

            // Set up the score gauge once the page finishes loading.

            window.knob = pureknob.createKnob( 225, 225 );
        } );
    </script>







    <script>
        $( document ).ready( function GetSupervisor() {

            // When a supervisor is selected, pull back only the agents that belong to that supervisor.

            //ajax call
            $( '#Supervisor' ).on( 'change', function () {

                //jquery
                var AgentMatch = $( this ).val();

                if ( AgentMatch ) {
                    //ajax call
                    $.ajax( {
                        type: 'POST',
                        url: '../api/get_agents_by_supervisor.php',
                        cache: false,
                        data: "AgentMatch=" + AgentMatch,

                        success: function ( html ) {
                            console.log( html );

                            $( '#agent' ).html( html );



                            $( '#agent' ).unbind( 'change' ).on( 'change', function () {


                            } );

                            // Once the agent list is loaded, set up the next step that pulls the selected agent's data.
                            GetAllTheAgents();




                            // Force Agent Change           
                            $( "#agent" ).val();
                            $( "#agent" ).change();




                        },








                    } );

                };

            } );



            // Handle the agent selection and load everything needed for that agent's scorecard.
            function GetAllTheAgents() {


                $( '#agent' ).on( 'change', function ( event ) { //jquery



                    var AgentMatch = $( this ).val();

                    console.log( AgentMatch );



                    if ( AgentMatch ) {
                        //ajax call
                        $.ajax( {
                            type: 'POST',
                            data: "AgentMatch=" + AgentMatch,
                            url: '../api/get_agent_summary.php',
                            cache: false,

                            success: function ( data ) {


                                // The backend sends the agent data back as JSON, so convert it into an object I can use on the page.
                                var obj = JSON.parse( data );

                                // Fill in the main agent information card and scorecard values below.
                                // Basic agent info shown at the top of the scorecard.
                                //Agent Full Name
                                $( '#AgentFullName_SupSelect' ).html( obj.Fullname );
                                $( '#AgentTitleName_SupSelect' ).html( obj.JobTitle ); //Job Title	
                                $( '#hireDate_SupSelect' ).html( obj.HireDate ); //Hire Date
                                $( '#WorkforceID_SupSelect' ).html( obj.WorkforceID ); // Workforce system ID
                                window.WorkforceID = obj.WorkforceID;
                                $( '#PhoneExtension_SupSelect' ).html( obj.PhoneExtension ); // Phone extension	
                                $( '#NetworkLogin_SupSelect' ).html( obj.NetworkLogin ); //Window Login					
                                //End Agent ID Card -------------------

                                // Overall points used to build the agent's total score.
                                $( '#Volume' ).html( obj.Metric1Point ); //Volume Point			
                                $( '#ProdPnt' ).html( obj.Metric2Point ); //ADH + Point
                                $( '#hmeBscore' ).html( obj.Metric3Point ); //Quality Point					
                                $( '#TotalScore' ).html( obj.TotalScore ); //Agent Total Socre					
                                //End Agent Overall Score--------------	


                                // Load the metric names dynamically because they can vary by department or role.	
                                $( '#FCRVariable' ).html( obj.M1ActMin1Name ); //TalkTime -Title
                                $( '#Metric5C3name' ).html( obj.M1ActMin2Name ); //ACW -Title
                                $( '#Metric3ModName' ).html( obj.Metric2Name ); //ADH+ -Title
                                //End Agent Breakdown Names----------------	

                                // Load the actual metric values that go with those labels.					
                                $( '#Metric5Metric' ).html( obj.M1ActMin1 ); //TalkTime-Minutes
                                $( '#Metric5C3' ).html( obj.M1ActMin2 ); //TalkTime-Minutes
                                $( '#VolTotalMetric' ).html( obj.Metric1Metric ); //TotalVolume
                                //End Agent Breakdown Scores--------------	

                                // Pull in the additional productivity/adherence details for the scorecard.	
                                $( '#Metric3Metric' ).html( obj.Metric2Metric ); // ADH+ metric
                                $( '#MinutesIn' ).html( obj.M2MinutesIn ); // MinutesIn
                                
                                $( '#AdhPercentMetric' ).html( obj.M2MinutesOut ); //MinutesOut						
                                $( '#SpecialTeam_ERROR_Point' ).html( obj.Metric4Point ); //MinutesOut						
                                $( '#Metric4Name' ).html( obj.Metric4Name ); //MinutesOut						

                                       $( '#M1Goal' ).html( obj.M1Goal ); //MinutesOut						

                      $( '#M1Metric' ).html( obj.M1Metric );
                                
                               
                                    
                                    
                                // Load the quality/audit results for the selected agent.
                                $( '#Audit1' ).html( obj.M3Audit1 );
                                $( '#Audit2' ).html( obj.M3Audit2 );
                                $( '#Audit3' ).html( obj.M3Audit3 );
                                $( '#Audit4' ).html( obj.M3Audit4 );
                                $( '#Audit5' ).html( obj.M3Audit5 );
                                //end Audits				
                            $('#AgentDepartment').html(obj.Type);

                                $( '#Cur_Month_Date1' ).html( obj.current_month_date_supervisor_1 );
                                $( '#Cur_Month_Date2' ).html( obj.current_month_date_supervisor_2 );
                                $( '#Cur_Month_Date3' ).html( obj.current_month_date_supervisor_3 );
                                $( '#Cur_Month_Date4' ).html( obj.current_month_date_supervisor_4 );
                                $( '#Cur_Month_Date5' ).html( obj.current_month_date_supervisor_5 );
                                //end Audits				             

                                $( '#MetricTitle_Actual_MTDTABLE_AdherenceTitle' ).html( obj.Metric2Name );



                            
                            // Keep the agent's department available so I can change the layout when a team has extra metrics.
                            window.AgentDepartment = $('#AgentDepartment').text();


             
        
    console.log(window.AgentDepartment); 


     

        window.PhoneExtensionError = $('#PhoneExtension_SupSelect').text();
    console.log(window.PhoneExtensionError); 


// If the phone extension is missing, show a warning so the supervisor knows the profile needs to be fixed.
if(window.PhoneExtensionError == "Not Listed" ){

    alert("This agent is missing thier phone extension!\nTo fix this, please reach out to workforce support\nand request to have the agents profile updated with the correct extension.");
}



                      
                            
                            
                            
        // Some teams have an extra score, so expand the scorecard only when that team is selected.
        if (window.AgentDepartment == "SpecializedTeam")  {

         $('#ScoreCardOverviewALLcontain-Agent').animate({"left": "-9","width":"990px"});
                  $('#SpecialTeamError').fadeIn();
        }
                                else{   
                                    
                                    $('#SpecialTeamError').fadeOut("fast");
                    $('#ScoreCardOverviewALLcontain-Agent').animate({"left": "-9","width":"850px"});
                   }
                
                    

                       

                                // Pull the day-by-day metrics separately so the supervisor can see how the agent performed throughout the month.
                                $.ajax( {

                                    type: 'POST',
                                    url: '../api/get_agent_daily_metrics.php',
                                    data: "AgentMatch=" + AgentMatch,
                                    cache: false,

                                    success: function ( data ) {



                                        // Convert the daily results into an object, then map each day's values into the table.
                                        var obj = JSON.parse( data );

                                        console.log( obj );
                                        $( '#Date1' ).html( obj.Date1 );
                                        $( '#Goal1' ).html( obj.Goal1 );
                                        $( '#Actual1' ).html( obj.Metric1 );
                                        $( '#Difference1' ).html( obj.GoalDiff1 );
                                        $( '#MinutesIn1' ).html( obj.M2MinutesIn1 );
                                        $( '#MinutesOut1' ).html( obj.MinutesOut1 );
                                        $( '#Metric2Metric1' ).html( obj.Metric2Metric1 );
                                        $( '#Date2' ).html( obj.Date2 );
                                        $( '#Goal2' ).html( obj.Goal2 );
                                        $( '#Actual2' ).html( obj.Metric2 );
                                        $( '#Difference2' ).html( obj.GoalDiff2 );
                                        $( '#MinutesIn2' ).html( obj.M2MinutesIn2 );
                                        $( '#MinutesOut2' ).html( obj.MinutesOut2 );
                                        $( '#Metric2Metric2' ).html( obj.Metric2Metric2 );
                                        $( '#Date3' ).html( obj.Date3 );
                                        $( '#Goal3' ).html( obj.Goal3 );
                                        $( '#Actual3' ).html( obj.Metric3 );
                                        $( '#Difference3' ).html( obj.GoalDiff3 );
                                        $( '#MinutesIn3' ).html( obj.M2MinutesIn3 );
                                        $( '#MinutesOut3' ).html( obj.MinutesOut3 );
                                        $( '#Metric2Metric3' ).html( obj.Metric2Metric3 );
                                        $( '#Date4' ).html( obj.Date4 );
                                        $( '#Goal4' ).html( obj.Goal4 );
                                        $( '#Actual4' ).html( obj.Metric4 );
                                        $( '#Difference4' ).html( obj.GoalDiff4 );
                                        $( '#MinutesIn4' ).html( obj.M2MinutesIn4 );
                                        $( '#MinutesOut4' ).html( obj.MinutesOut4 );
                                        $( '#Metric2Metric4' ).html( obj.Metric2Metric4 );
                                        $( '#Date5' ).html( obj.Date5 );
                                        $( '#Goal5' ).html( obj.Goal5 );
                                        $( '#Actual5' ).html( obj.Metric5 );
                                        $( '#Difference5' ).html( obj.GoalDiff5 );
                                        $( '#MinutesIn5' ).html( obj.M2MinutesIn5 );
                                        $( '#MinutesOut5' ).html( obj.MinutesOut5 );
                                        $( '#Metric2Metric5' ).html( obj.Metric2Metric5 );
                                        $( '#Date6' ).html( obj.Date6 );
                                        $( '#Goal6' ).html( obj.Goal6 );
                                        $( '#Actual6' ).html( obj.Metric6 );
                                        $( '#Difference6' ).html( obj.GoalDiff6 );
                                        $( '#MinutesIn6' ).html( obj.M2MinutesIn6 );
                                        $( '#MinutesOut6' ).html( obj.MinutesOut6 );
                                        $( '#Metric2Metric6' ).html( obj.Metric2Metric6 );
                                        $( '#Date7' ).html( obj.Date7 );
                                        $( '#Goal7' ).html( obj.Goal7 );
                                        $( '#Actual7' ).html( obj.Metric7 );
                                        $( '#Difference7' ).html( obj.GoalDiff7 );
                                        $( '#MinutesIn7' ).html( obj.M2MinutesIn7 );
                                        $( '#MinutesOut7' ).html( obj.MinutesOut7 );
                                        $( '#Metric2Metric7' ).html( obj.Metric2Metric7 );
                                        $( '#Date8' ).html( obj.Date8 );
                                        $( '#Goal8' ).html( obj.Goal8 );
                                        $( '#Actual8' ).html( obj.Metric8 );
                                        $( '#Difference8' ).html( obj.GoalDiff8 );
                                        $( '#MinutesIn8' ).html( obj.M2MinutesIn8 );
                                        $( '#MinutesOut8' ).html( obj.MinutesOut8 );
                                        $( '#Metric2Metric8' ).html( obj.Metric2Metric8 );
                                        $( '#Date9' ).html( obj.Date9 );
                                        $( '#Goal9' ).html( obj.Goal9 );
                                        $( '#Actual9' ).html( obj.Metric9 );
                                        $( '#Difference9' ).html( obj.GoalDiff9 );
                                        $( '#MinutesIn9' ).html( obj.M2MinutesIn9 );
                                        $( '#MinutesOut9' ).html( obj.MinutesOut9 );
                                        $( '#Metric2Metric9' ).html( obj.Metric2Metric9 );
                                        $( '#Date10' ).html( obj.Date10 );
                                        $( '#Goal10' ).html( obj.Goal10 );
                                        $( '#Actual10' ).html( obj.Metric10 );
                                        $( '#Difference10' ).html( obj.GoalDiff10 );
                                        $( '#MinutesIn10' ).html( obj.M2MinutesIn10 );
                                        $( '#MinutesOut10' ).html( obj.MinutesOut10 );
                                        $( '#Metric2Metric10' ).html( obj.Metric2Metric10 );
                                        $( '#Date11' ).html( obj.Date11 );
                                        $( '#Goal11' ).html( obj.Goal11 );
                                        $( '#Actual11' ).html( obj.Metric11 );
                                        $( '#Difference11' ).html( obj.GoalDiff11 );
                                        $( '#MinutesIn11' ).html( obj.M2MinutesIn11 );
                                        $( '#MinutesOut11' ).html( obj.MinutesOut11 );
                                        $( '#Metric2Metric11' ).html( obj.Metric2Metric11 );
                                        $( '#Date12' ).html( obj.Date12 );
                                        $( '#Goal12' ).html( obj.Goal12 );
                                        $( '#Actual12' ).html( obj.Metric12 );
                                        $( '#Difference12' ).html( obj.GoalDiff12 );
                                        $( '#MinutesIn12' ).html( obj.M2MinutesIn12 );
                                        $( '#MinutesOut12' ).html( obj.MinutesOut12 );
                                        $( '#Metric2Metric12' ).html( obj.Metric2Metric12 );
                                        $( '#Date13' ).html( obj.Date13 );
                                        $( '#Goal13' ).html( obj.Goal13 );
                                        $( '#Actual13' ).html( obj.Metric13 );
                                        $( '#Difference13' ).html( obj.GoalDiff13 );
                                        $( '#MinutesIn13' ).html( obj.M2MinutesIn13 );
                                        $( '#MinutesOut13' ).html( obj.MinutesOut13 );
                                        $( '#Metric2Metric13' ).html( obj.Metric2Metric13 );
                                        $( '#Date14' ).html( obj.Date14 );
                                        $( '#Goal14' ).html( obj.Goal14 );
                                        $( '#Actual14' ).html( obj.Metric14 );
                                        $( '#Difference14' ).html( obj.GoalDiff14 );
                                        $( '#MinutesIn14' ).html( obj.M2MinutesIn14 );
                                        $( '#MinutesOut14' ).html( obj.MinutesOut14 );
                                        $( '#Metric2Metric14' ).html( obj.Metric2Metric14 );
                                        $( '#Date15' ).html( obj.Date15 );
                                        $( '#Goal15' ).html( obj.Goal15 );
                                        $( '#Actual15' ).html( obj.Metric15 );
                                        $( '#Difference15' ).html( obj.GoalDiff15 );
                                        $( '#MinutesIn15' ).html( obj.M2MinutesIn15 );
                                        $( '#MinutesOut15' ).html( obj.MinutesOut15 );
                                        $( '#Metric2Metric15' ).html( obj.Metric2Metric15 );
                                        $( '#Date16' ).html( obj.Date16 );
                                        $( '#Goal16' ).html( obj.Goal16 );
                                        $( '#Actual16' ).html( obj.Metric16 );
                                        $( '#Difference16' ).html( obj.GoalDiff16 );
                                        $( '#MinutesIn16' ).html( obj.M2MinutesIn16 );
                                        $( '#MinutesOut16' ).html( obj.MinutesOut16 );
                                        $( '#Metric2Metric16' ).html( obj.Metric2Metric16 );
                                        $( '#Date17' ).html( obj.Date17 );
                                        $( '#Goal17' ).html( obj.Goal17 );
                                        $( '#Actual17' ).html( obj.Metric17 );
                                        $( '#Difference17' ).html( obj.GoalDiff17 );
                                        $( '#MinutesIn17' ).html( obj.M2MinutesIn17 );
                                        $( '#MinutesOut17' ).html( obj.MinutesOut17 );
                                        $( '#Metric2Metric17' ).html( obj.Metric2Metric17 );
                                        $( '#Date18' ).html( obj.Date18 );
                                        $( '#Goal18' ).html( obj.Goal18 );
                                        $( '#Actual18' ).html( obj.Metric18 );
                                        $( '#Difference18' ).html( obj.GoalDiff18 );
                                        $( '#MinutesIn18' ).html( obj.M2MinutesIn18 );
                                        $( '#MinutesOut18' ).html( obj.MinutesOut18 );
                                        $( '#Metric2Metric18' ).html( obj.Metric2Metric18 );
                                        $( '#Date19' ).html( obj.Date19 );
                                        $( '#Goal19' ).html( obj.Goal19 );
                                        $( '#Actual19' ).html( obj.Metric19 );
                                        $( '#Difference19' ).html( obj.GoalDiff19 );
                                        $( '#MinutesIn19' ).html( obj.M2MinutesIn19 );
                                        $( '#MinutesOut19' ).html( obj.MinutesOut19 );
                                        $( '#Metric2Metric19' ).html( obj.Metric2Metric19 );
                                        $( '#Date20' ).html( obj.Date20 );
                                        $( '#Goal20' ).html( obj.Goal20 );
                                        $( '#Actual20' ).html( obj.Metric20 );
                                        $( '#Difference20' ).html( obj.GoalDiff20 );
                                        $( '#MinutesIn20' ).html( obj.M2MinutesIn20 );
                                        $( '#MinutesOut20' ).html( obj.MinutesOut20 );
                                        $( '#Metric2Metric20' ).html( obj.Metric2Metric20 );
                                        $( '#Date21' ).html( obj.Date21 );
                                        $( '#Goal21' ).html( obj.Goal21 );
                                        $( '#Actual21' ).html( obj.Metric21 );
                                        $( '#Difference21' ).html( obj.GoalDiff21 );
                                        $( '#MinutesIn21' ).html( obj.M2MinutesIn21 );
                                        $( '#MinutesOut21' ).html( obj.MinutesOut21 );
                                        $( '#Metric2Metric21' ).html( obj.Metric2Metric21 );
                                        $( '#Date22' ).html( obj.Date22 );
                                        $( '#Goal22' ).html( obj.Goal22 );
                                        $( '#Actual22' ).html( obj.Metric22 );
                                        $( '#Difference22' ).html( obj.GoalDiff22 );
                                        $( '#MinutesIn22' ).html( obj.M2MinutesIn22 );
                                        $( '#MinutesOut22' ).html( obj.MinutesOut22 );
                                        $( '#Metric2Metric22' ).html( obj.Metric2Metric22 );
                                        $( '#Date23' ).html( obj.Date23 );
                                        $( '#Goal23' ).html( obj.Goal23 );
                                        $( '#Actual23' ).html( obj.Metric23 );
                                        $( '#Difference23' ).html( obj.GoalDiff23 );
                                        $( '#MinutesIn23' ).html( obj.M2MinutesIn23 );
                                        $( '#MinutesOut23' ).html( obj.MinutesOut23 );
                                        $( '#Metric2Metric23' ).html( obj.Metric2Metric23 );
                                        $( '#Date24' ).html( obj.Date24 );
                                        $( '#Goal24' ).html( obj.Goal24 );
                                        $( '#Actual24' ).html( obj.Metric24 );
                                        $( '#Difference24' ).html( obj.GoalDiff24 );
                                        $( '#MinutesIn24' ).html( obj.M2MinutesIn24 );
                                        $( '#MinutesOut24' ).html( obj.MinutesOut24 );
                                        $( '#Metric2Metric24' ).html( obj.Metric2Metric24 );
                                        $( '#Date25' ).html( obj.Date25 );
                                        $( '#Goal25' ).html( obj.Goal25 );
                                        $( '#Actual25' ).html( obj.Metric25 );
                                        $( '#Difference25' ).html( obj.GoalDiff25 );
                                        $( '#MinutesIn25' ).html( obj.M2MinutesIn25 );
                                        $( '#MinutesOut25' ).html( obj.MinutesOut25 );
                                        $( '#Metric2Metric25' ).html( obj.Metric2Metric25 );
                                        $( '#Date26' ).html( obj.Date26 );
                                        $( '#Goal26' ).html( obj.Goal26 );
                                        $( '#Actual26' ).html( obj.Metric26 );
                                        $( '#Difference26' ).html( obj.GoalDiff26 );
                                        $( '#MinutesIn26' ).html( obj.M2MinutesIn26 );
                                        $( '#MinutesOut26' ).html( obj.MinutesOut26 );
                                        $( '#Metric2Metric26' ).html( obj.Metric2Metric26 );
                                        $( '#Date27' ).html( obj.Date27 );
                                        $( '#Goal27' ).html( obj.Goal27 );
                                        $( '#Actual27' ).html( obj.Metric27 );
                                        $( '#Difference27' ).html( obj.GoalDiff27 );
                                        $( '#MinutesIn27' ).html( obj.M2MinutesIn27 );
                                        $( '#MinutesOut27' ).html( obj.MinutesOut27 );
                                        $( '#Metric2Metric27' ).html( obj.Metric2Metric27 );
                                        $( '#Date28' ).html( obj.Date28 );
                                        $( '#Goal28' ).html( obj.Goal28 );
                                        $( '#Actual28' ).html( obj.Metric28 );
                                        $( '#Difference28' ).html( obj.GoalDiff28 );
                                        $( '#MinutesIn28' ).html( obj.M2MinutesIn28 );
                                        $( '#MinutesOut28' ).html( obj.MinutesOut28 );
                                        $( '#Metric2Metric28' ).html( obj.Metric2Metric28 );
                                        $( '#Date29' ).html( obj.Date29 );
                                        $( '#Goal29' ).html( obj.Goal29 );
                                        $( '#Actual29' ).html( obj.Metric29 );
                                        $( '#Difference29' ).html( obj.GoalDiff29 );
                                        $( '#MinutesIn29' ).html( obj.M2MinutesIn29 );
                                        $( '#MinutesOut29' ).html( obj.MinutesOut29 );
                                        $( '#Metric2Metric29' ).html( obj.Metric2Metric29 );
                                        $( '#Date30' ).html( obj.Date30 );
                                        $( '#Goal30' ).html( obj.Goal30 );
                                        $( '#Actual30' ).html( obj.Metric30 );
                                        $( '#Difference30' ).html( obj.GoalDiff30 );
                                        $( '#MinutesIn30' ).html( obj.M2MinutesIn30 );
                                        $( '#MinutesOut30' ).html( obj.MinutesOut30 );
                                        $( '#Metric2Metric30' ).html( obj.Metric2Metric30 );
                                        $( '#Date31' ).html( obj.Date31 );
                                        $( '#Goal31' ).html( obj.Goal31 );
                                        $( '#Actual31' ).html( obj.Metric31 );
                                        $( '#Difference31' ).html( obj.GoalDiff31 );
                                        $( '#MinutesIn31' ).html( obj.M2MinutesIn31 );
                                        $( '#MinutesOut31' ).html( obj.MinutesOut31 );
                                        $( '#Metric2Metric31' ).html( obj.Metric2Metric31 );









                                        //ajax call$(document).on('change', "#agent", function(Get

                                        //ajax call
                                        $.ajax( {
                                            type: 'POST',
                                            data: "AgentMatch=" + AgentMatch,
                                            url: '../api/get_agent_day_metrics.php',
                                            cache: false,
                                            success: function ( data ) {




                                                var obj = JSON.parse( data );

                                                //console.log(obj);

                                                //Agent Full Name
                                                $( '#DateUpdateDate' ).html( obj.DateDate );
                                                $( '#DailyVolumeLeft' ).html( obj.Metric1Metric ); //Job Title	
                                                $( '#DailyVolumeStat' ).html( obj.Goal ); // Phone extension	
                                                //End Agent ID Card -------------------
                                                $( '#MTDData' ).html( obj.MonthGoal ); // Phone extension	           


                                                $( '#GoalgLabel1' ).html( obj.GoalName );
                                                $( '#MTDmetric' ).html( obj.MonthGoalName );
                                                $( '#AdherencePlusBanner' ).html( obj.Metric2Name );
                                                $( '#SmartAdhTitle' ).html( obj.Metric2Name );

                                                //Start Agent Overall Score -----------
                                                //$('#DailyVolumeStat').html(obj.MonthGoal);		//Volume Point			
                                                $( '#MetricDailyMinutesIn' ).html( obj.M2MinutesIn ); //ADH + Point
                                                $( '#MetricDailyMinutesOut' ).html( obj.M2MinutesOut ); //Quality Point					
                                                //End Agent Overall Score--------------	




                                                $( '#MetricDailyAdh' ).html( obj.Metric2Metric ); //ACW -Title
                                                $( '#Time' ).html( obj.LastUpdate ); //ADH+ -Title
                                                //End Agent Breakdown Names----------------	


                                            },



                                        } );



                                        KnobPop();

                                    },



                                } );


                            },





                        } );

                    };


                } );




            };






            function KnobPop() {

                var knob = window.knob;



                knob.setProperty( 'angleStart', -0.95 * Math.PI );
                knob.setProperty( 'angleEnd', 0.95 * Math.PI );
                knob.setProperty( 'colorFG', '#89ece7' );
                knob.setProperty( 'colorBG', '#e07f22' );
                knob.setProperty( 'trackWidth', 0.35 );
                knob.setProperty( 'valMin', 0.00 );
                
                
                
                
                      if (window.AgentDepartment == "SpecializedTeam")  {
                knob.setProperty( 'valMax',100.00 );

        }
                                else{   
                                     knob.setProperty( 'valMax', 75.00 );

                   }
                
                knob.setProperty( 'readonly', 'TRUE' );



                var totalScoreFinal = parseFloat( $( '#TotalScore' ).text() );



                knob.setValue( totalScoreFinal );





                // Create element node.
                var node = knob.node();

                // Add it to the DOM.

                var elem = document.getElementById( 'some_element' );






                elem.appendChild( node );


            };

        } );
    </script>








    <!-------------------------------GetUser Script Menu Script---------------------------------------->
                
	<!-------------------------------GetUser Script Menu Script---------------------------------------->	

</head>
<body>
<!---------------------------Top Bar Start----------------------------->	

            	

	
	<div id="ContainShrnk"> 
        
        
         <span id="AgentCurrent_indicator"> </span>
        
        
		<div id="topbarSUP">
            
            
			<div id="SupAgentSelect"> 
        
	<select Class="SupervisorClass" name="Supervisor" id="Supervisor">
        
  <option id="SupOption" value="">Select Supervisor</option>
        
        
    <?php
		if($rowCount>0)
		{
			while($row=$query->fetch_assoc()){
				
				echo'<option value='.$row['Sup_ID'].'>'.$row['Supervisor'].'</option>';
			}
			
			
		}else{
			
			echo'<option value =""> Supervisor Not Found </option>';
			
			
			
			
		}
		
		
		
		?>
		
		
  </select>
                
 
  <select name="agent" id="agent"  required:"">
      
    <option id="Agent_Options" value="">Select Agent</option>



  </select>
                


            
	</div>
            
            
            <!---------------------Navigation Menu Start------------------------->
			
 <div id = "icons_container_individual"> 

           
   

    
    
	
	<!----- Next Link ----------->
	
<div class='Icons_position' id="agent_view_link_before">	<!----- Link/pic contain ----------->


		<a  class="Agent_link_after" href="../admin/Supervisor_OperationsDashboard_IndividualAgent.php" target="_self">
		
	
			<!----- pic no hover ----------->
			<div id="no_hover_agentview" ></div>
		
			<!----- pic hover----------->


	
	
		</a>
		

	
	
	<p class= "icon_titles" id="agent_view_title">Agent View</p>
       	</div>       

				
                    
                    
                    
                    
                    
					
					
	<div class='Icons_position' id="team_view_link_Before">	<!----- Link/pic contain ----------->

		<a class="Team_View_link_after" href="../../Supervisor_OperationsDashboard.php" target="_self">
		
			<!----- pic no hover ----------->
			<div id="no_hover_teamview"></div>
		
			<!----- pic hover----------->

	
	
	
	
		</a>
		

	
	<p class= 'icon_titles' id="team_view">Team View</p>
              
</div>
								
			
	
<div  class="Icons_position" id="previous_month_link_container">	
    <!----- Link/pic contain ----------->
    <a  class="previous_month_link_after"  href="../supervisor/Supervisor_PreviousMonth_Dashboard.php" target="_self">
		
			<!----- pic no hover ----------->
            <div id="no_hover_previous"></div>
        
    </a>
		
	
	
		            <p class= "icon_titles" id="previousMonthLinkTitle">Previous Month</p>

</div>				
					

                    
                    
                    
                    
    
</div>
		

	


			<script>
                        
    $(document).ready(function(){

        
 $(function() {
  $('#no_hover_previous').hover(function() {
    $('#previousMonthLinkTitle').css("color", "#5DD7E9");
  }, function() {
    // on mouseout, reset the background colour
    $('#previousMonthLinkTitle').css("color", "black");
  });
});
});
                    
                    </script>
		
			<script>
                        
    $(document).ready(function(){

        
 $(function() {
  $('#no_hover_teamview').hover(function() {
    $('#team_view').css("color", "#5DD7E9");
  }, function() {
    // on mouseout, reset the background colour
    $('#team_view').css("color", "black");
  });
});
});
                    
                    </script>
							<script>
                        
    $(document).ready(function(){

        
 $(function() {
  $('#no_hover_agentview').hover(function() {
    $('#agent_view_title').css("color", "#5DD7E9");
  }, function() {
    // on mouseout, reset the background colour
    $('#agent_view_title').css("color", "black");
  });
});
});
                    
                    </script>
								
            

	<!-------------------Navigation Menu End-------------------->
        </div>
	
		<div id="topbar_Sup_Select">
			
					
							

<!--ID Card Start--> 	
							

		

	

    
    
    
	<!--NavigationMenu-->



	
		



<!--		<div class="menuDescriptionScreCardVw" id="UseNameScoreCardView"></div> -->


																			
			
<!--<i id="ErroIcon" class="fa fa-exclamation-triangle" style="font-size:25px"><p id="alertTag"> Alerts</p></i>
			<!--USER INFORMATION START


				<div id="UserInfo-Container">

				<img src="xampp/default-avatar-2.jpg" class="image--cover">
				 <a id="logout-link" href="logout.php">Logout</a>
				</div>


			USER INFORMATION END-->
		                <div id="hideContain_SupSelect_AgentPage_sup">




                    <div class="IdContain_supIndiv" id="JobTitleContain_SUP_SELECT">
                        <div class="TitleColors" id="JobTitle_SupSelect">Job Title</div>
                        <div class="DataColors" id="AgentTitleName_SupSelect"></div>

                    </div>


                    <!--Job Title end-->

                    <!--Hire Date Start-->
                    <div class="IdContain_supIndiv" id="HireDateContain_SUP_SELECT">
                        <div class="TitleColors" id="hireDateTitle_SupSelect">Hire Date</div>
                        <div class="DataColors" id="hireDate_SupSelect"></div>

                    </div>


                    <div class="IdContain_supIndiv" id="WorkforceID_Contain_supSelect">
                        <div class="TitleColors" id="WorkforceID_Title_SupSelect">Workforce ID</div>
                        <div class="DataColors" name="WorkforceID_SupSelect" id="WorkforceID_SupSelect"></div>

                    </div>

                    <div class="IdContain_supIndiv" id="PhoneExtension_IdContain_supSelect">
                        <div class="TitleColors" id="PhoneExtensionTitle_SupSelect">Phone Extension</div>
                        <div class="DataColors" id="PhoneExtension_SupSelect"></div>

                    </div>
                    <!--Hire Date End-->






                </div>

	<!---------------------------Top Bar End----------------------------->	
		</div>
            <div id="ShrnkMain">
                
                 <div class="PositionSlidersScreOvrView_Agent_sup" id="CE-PanelSlideScreOvrView">

                <div id="CE-PanelSlideContent">

                    <div class="OverviewContain" name="CE-ExpandContainer" id="CE-ExpandContainer">

                        <div class="BreakdownTitleBoxes" id="CE-Banner">

                            <div class="BannerBox" name="CE-Box" id="CE-Box">Quality Breakdown</div>


                            <div class="XButtonContainer" id="CE-closeXContainer">
                                <input type="image" class="XButton-Stats CE-OpenX buttonUnclicked" id="CE-X-Unclicked" src="CLOSE-Unclicked.png"/>
                                <input type="image" class="XButton-Stats CE-ClosedX buttonClicked" id="CE-X-Clicked" src="CLOSE-Clicked.png"/>
                            </div>


                        </div>

                        <div class="CE-Conatiners-Toggles" id="WOW-Container-Supervisor">

                            <div class="TitleContainQA" name="Metric4C2name" id="Metric4C2name">Audit 1:</div>
                            <!--Wow box-->
                            <div class=" QA-Metrics_Supervisor" name="Metric4ModName" id="Audit1"></div>

                            <!--Wow Score -->
                            <div id="Cur_Month_Date1" class="audit-date"></div>


                        </div>



                        <div class="CE-Conatiners-Toggles" id="Satisfied-Container">
                            <div class="Metric4C3name TitleContainQA" id="Metric4C3name">Audit 2:</div>
                            <div class="Metric4C3 QA-Metrics_Supervisor" name="Metric4C3" id="Audit2"></div>

                            <!--Wow Score -->
                            <div id="Cur_Month_Date2" class="audit-date"></div>

                        </div>

                        <div class="CE-Conatiners-Toggles" id="Dissatisfied-Container">
                            <div class="Metric4C4name TitleContainQA" id="Metric4C4name">Audit 3:</div>
                            <div class="Metric4C4 QA-Metrics_Supervisor" name="Metric4C4" id="Audit3"></div>
                            <!--Wow Score -->
                            <div id="Cur_Month_Date3" class="audit-date"></div>

                        </div>

                        <!--Audit Count-->
                        <div class="CE-Conatiners-Toggles" id="Review-Container">
                            <div class="Metric4C1name  TitleContainQA" id="Metric4C1name">Audit 4:</div>
                            <div class="Metric4C1 QA-Metrics_Supervisor" id="Audit4"></div>
                            <!--Wow Score -->
                            <div id="Cur_Month_Date4" class="audit-date"></div>

                        </div>

                        <div class="CE-Conatiners-Toggles" id="Audit5-Container">

                            <div class="Metric4C1name  TitleContainQA" id="Audit5Title">Audit 5:</div>

                            <div class="Metric4C1 QA-Metrics_Supervisor" id="Audit5"></div>
                            <!--Wow Score -->
                            <div id="Cur_Month_Date5" class="audit-date"></div>

                        </div>


                    </div>
                </div>
            </div>
            <!--CE-Breakdown Finish-->


                <!----------------------------------------------------------------------------------------------AIM-Breakdown Start----------------------------------------------------------->


                <!--SmartADH-Breakdown Process end-->

                <div class="PositionSlidersScreOvrView_Agent_sup" id="FCR-PanelSlideScreOvrView">

                    <div id="FCR-PanelSlideContent">

                        <div class="OverviewContain" id="FCR-ExpandContainer">

                            <div class="BreakdownTitleBoxes">

                                <div class="BannerBox">Volume Data (MTD)</div>


                                <div class="XButtonContainer" id="FCR-closeXContainer">

                                    <input type="image" class="XButton-Stats FCR-OpenX buttonUnclicked " id="FCR-X-Unclicked" src="CLOSE-Unclicked.png"/>

                                    <input type="image" class="XButton-Stats FCR-ClosedX buttonClicked" id="FCR-X-Clicked" src="CLOSE-Clicked.png"/>
                                </div>

                            </div>




                            <div class="CE-Conatiners-Toggles" id="ACW-Container">

                                <div class="Metric5C3name TitleContain" id="Metric5C3name"></div>

                                <div class="Metric5C3 CE-Metrics" id="Metric5C3"></div>

                            </div>


                            <div class="CE-Conatiners-Toggles" id="TalkTime-Container">

                                <div class="FCRVariable TitleContain" id="TalkTimeMetric">Total Volume:</div>

                                <div class="Metric5Metric CE-Metrics " id="VolTotalMetric"></div>


                            </div>
                            
                                 <div class="CE-Conatiners-Toggles" id="MTD-Container">

                                <div class="FCRVariable TitleContain" id="MTD_Title">MTD Goal:</div>

                                <div class="Metric5Metric CE-Metrics " id="M1Goal"></div>


                            </div>

               <div class="CE-Conatiners-Toggles" id="MTD_Volume-Container">

                                <div class="FCRVariable TitleContain" id="M1Metric_Title">Volume Completed:</div>

                                <div class="Metric5Metric CE-Metrics " id="M1Metric"></div>


                            </div>

                            <div class="CE-Conatiners-Toggles" id="MTD_ANT_Goal">

                                <div class="FCRVariable TitleContain" id="MTDmetric_Static">Anticipated Monthly Goal:</div>

                                <div class="Metric5Metric CE-Metrics " id="MTDData"></div>


                            </div>



                        </div>
                    </div>
                </div>



                <!---------------------------------------------------------------------------------------------->

                <div class="PositionSlidersScreOvrView_Agent_sup" id="AIM-PanelSlideScreOvrView">
                    <div id="AIM-PanelSlideContent">
                        <div class="OverviewContain" name="Aim-ExpandContainer " id="Aim-ExpandContainer">


                            <div class="BreakdownTitleBoxes" id="CE-Banner">
                                <div class="BannerBox" id="AIM-Box-Title">Adherence Breakdown</div>

                                <!--Close Button-->
                                <div class="XButtonContainer" id="AIM-closeXContainer">

                                    <input type="image" class="XButton-Stats  AIM-OpenX buttonUnclicked" id="AIM-X-Unclicked" src="CLOSE-Unclicked.png"/>
                                    <input type="image" class="XButton-Stats AIM-ClosedX buttonClicked" id="AIM-X-Clicked" src="CLOSE-Clicked.png"/>

                                </div>
                                <!--Close end-->
                            </div>
                            <!--Smart Adherence Breakdown Data Start-->
                            <div class="CE-Conatiners-Toggles" id="MinutesIn-Container">
                                <!--MinutesIn Title Div-->
                                <div class="Metric3ModName TitleContain" id="Metric3ModName">ADH Metric:</div>
                                <!--MinutesIn Data Div-->
                                <div class="Metric3Metric CE-Metrics" name="Metric3Metric" id="Metric3Metric"></div>
                                <div class="Metric3Metric CE-Metrics" name="Metric3Metric" id="Metric3Metric"></div>
                            </div>

                            <div class="CE-Conatiners-Toggles" id="MinutesOut-Container">
                                <!--MinutesOut Title Div-->
                                <div class="Metric3ModName TitleContain" id="Metric3ModName">Minutes In:</div>
                                <!--MinutesOut Data Div-->
                                <div class="Metric3Metric CE-Metrics" name="Metric3Metric" id="MinutesIn"></div>
                            </div>

                            <div class="CE-Conatiners-Toggles" id="AdherencePlus-Container">
                                <!--ACW Title Div-->
                                <div class="Metric3ModName TitleContain" id="AdhPercent">Minutes Out:</div>
                                <!--ACW Data Div-->
                                <div class="Metric3Metric CE-Metrics" name="Metric3Metric" id="AdhPercentMetric"></div>


                            </div>
                            <!--Smart Adherence Breakdown Data end-->
                        </div>

                    </div>

                </div>
            <div id="TotalScore"></div>

                <div id="AgentDepartment"></div>


     


                <div id="panel" class="one" style="display: none;">

                    <div id="panel-content">

                        <ul>
                            <li> No Errors to report. You can check your stats!</li>
                            <br>
                            <!-- ALERT 2 <li> Errors will be reported here </li>
			<br>-->
                            <!-- ALERT 3 <li> I function like I should. </li>
			<br>-->
                        </ul>

                    </div>
                </div>

                <!---------------------------- ALERT SECTION END ------------------------>



            

                <!------------------------------MainOverview Start----------------------------->


                <!----------------------------------------------------------------------------------------------Volume-Breakdown END----------------------------------------------------------->





                <!--Start_SCReOVErVw-->
                <!--Start_SCReOVErVw-->

      <!--Start_SCReOVErVw-->
    <div id = "containscoresandcontainbreakdowns">

               <div id="innerContain">
                   
                   <div id="ScoreCardOverviewALLcontain-Agent">

                    <div id="ScoreCardOverviewBanner">

                        <div id="BlnceContainTitle">Overall Score</div>


                    </div>
                    <div id="some_element" value="" class="dial"></div>








                    <div id="PointContainer">




                        <!-- ProdPoint Home Screen Container and Point -->


                        <div class="OveVwScoreContain" id="CallVolumeVw">

                            <div class="mainTitleScore" id="CallVolumeTitle">Volume Score</div>

                            <div class="hmescrefontstyle PrdClrChnge" id="ProdPntHme">

                                <h1 id="Volume" class="HmeOvrVwFnt">-</h1>

                            </div>

                        </div>
                        
                        <div class="OveVwScoreContain" id="SmartADHVw">
                            <div class="mainTitleScore" id="SmartAdhTitle">Adherence Score</div>



                            <div class="hmescrefontstyle" id="ID">

                                <h1 id="ProdPnt" class="HmeOvrVwFnt">-</h1>

                            </div>

                        </div>


                        <div class="OveVwScoreContain" id="QualityVw">
                            <div class="mainTitleScore" id="QualityTitle">Quality Score</div>

                            <div class="hmescrefontstyle PrdClrChnge" id="ProdPntHme">

                                <h1 id="hmeBscore" class="HmeOvrVwFnt">-</h1>

                            </div>

                        </div>



                        <div class="OveVwScoreContain" id="SpecialTeamError">
                            <div class="mainTitleScore" id="SpecialTeam_Err_Name">Special Team Errors</div>

                            <div Class="hmescrefontstyle" id="BscoreFntHme">

                                <h1 id="SpecialTeam_ERROR_Point" class="HmeOvrVwFnt">-</h1>

                            </div>

                        </div>





                    </div>

                </div>
                
                    
                    
                    
            </div>         
                    
                    
    
           
                 </div>
                
                
                
                <div id = "VoulumeDateContainerINline"> 
                          
                    
                    <div id="innerContainerVolumeGoals"> 
                        
                    <div id="DateTimeContainer">
                    <div id="DateUpdateTitle">Date:</div>
                    <div id="DateUpdateDate"></div>
    


                    <div id="DateTimeTitle"> Last Update:

                    </div>

                    <div id="Time"></div>
            
                    </div>    
                <div class="DailyGoalContainerPosition" id="VolumeDailyGoalContainer">

                    <div id="VolumeDailyBanner">


                        <div id="TodayVolume_Banner">Today's Volume</div>


                    </div>
<div id="dailyStatsContains"> 
               




                        
    <div id="ActualContainer"> 
    
                        <div id="MinsNameActual">Actual</div>
                        

                        <p id="DailyVolumeLeft" class="ADHDailyFont_Test"></p>
    </div>
    


    <div id="GoalContainer"> 
                        
        
        <div id="GoalgLabel1"></div>

                        <p id="DailyVolumeStat"></p>


                    </div>
</div>

             

                </div>

                
                

                <div class="DailyGoalContainerPosition" id="VolumeGoalDailyContainOPPSC_dev">

                    <div id="ADHPlusBannerDaily">
                        <div id="AdherencePlusBanner">Smart Adherence</div>
                    </div>






                    <div id="DailyADHStatContain">

                        <div class="ScoreContentHolder">

                            <div class="AdherencePlusDailyScoreContain" id="ADHPlusDailyScore">
                                
                                
<div  class="DailyADHBreakdown" id="MinInDaily">Minutes In</div>


                                <div class="hmescrefontstyle" id="MinutesIn_Daily">

                        <h1 id="MetricDailyMinutesIn" class="ADHDailyStats_Sup"></h1>

                                </div>

                            </div>
                            
                            

                            
                            
                            <div class="AdherencePlusDailyScoreContain" id="MinOutDailyContain">
                                
                                
     <div class="DailyADHBreakdown" id="MinOutDaily">Minutes Out</div>

                 


                                <div class="hmescrefontstyle" id="MinutesOut_Daily">

                                    <h1 id="MetricDailyMinutesOut" class="ADHDailyStats_Sup"></h1>

                                </div>

                            </div>


                 <div class="AdherencePlusDailyScoreContain" id="Metric_Daily_Adherence">
     <div class="DailyADHBreakdown" id="MinOutDaily">Metric</div>
                                <div class="hmescrefontstyle" id="Metric_Daily">

                                    <h1 id="MetricDailyAdh" class="ADHDailyStats_Sup" ></h1>

                                </div>

                            </div>




                            <!-- ProdPoint Home Screen Container and Point -->



                        </div>

                    </div>




                </div>


</div>
                    
                                       
                    
                    
                    
                    
                <div class='AgentContainerRows_Agent_Table' >



                    <div id="ContainerTitles_Main">

                        <div class='MetricTitles_Agents_TitleMain' id="MetricTitles_Agent_Date"> Month to Date</div>



                    </div>
                    <!--Start of titles inside supervisor table box-->


                    <div id="ContainerTitles_ACTADH">

                        <div class="OVERVIEW_TITLE_CONTAIN_CLASS" id="OVERVIEW_TITLE_CONTAINERS">

                            <div class='MetricTitles_Agents_OverHeadTitle' id="MetricTitles_Agent_Actual_Title">Volume</div>

                        </div>
                        <div class="OVERVIEW_TITLE_CONTAIN_CLASS" id="OVERVIEW_TITLE_CONTAINERS_ADH">
                            <div class='MetricTitles_Agents_OverHeadTitle' id="MetricTitle_Actual_MTDTABLE_AdherenceTitle">-</div>

                        </div>

                    </div>


                    <div id="ContainerTitles">

                        <div class='MetricTitles_Agents' id="MetricTitles_Agent_Date">Date</div>
                        <div class='MetricTitles_Agents' id="MetricTitle_Actual_MTDTABLE_AGENT">Actual</div>
                        <div class='MetricTitles_Agents' id="MetricTitles_GOAL_MTDTABLE_AGENT">Goal</div>
                        <div class='MetricTitles_Agents' id="MetricTitle_DIFFERENCE_MTDTABLE_AGENT">Difference</div>
                        <div class='MetricTitles_Agents' id="MetricTitle_MININ_MTDTABLE_AGENT">Mins In</div>
                        <div class='MetricTitles_Agents' id="MetricTitle_MINout_MTDTABLE_AGENT">Mins Out</div>
                        <div class='MetricTitles_Agents' id="MetricTitles_METRIC_MTDTABLE_AGENT">Metric</div>


                    </div>


                    <div class='backgroundColorMTDtable' id=" backgroundTableColor">

                        <div class="Data1Contain" id="Date1"></div>
                        <div class="Data2Contain" id="Actual1"></div>
                        <div class="Data3Contain" id="Goal1"></div>
                        <div class="Data4Contain" id="Difference1"></div>
                        <div class="Data5Contain" id="MinutesIn1"></div>
                        <div class="Data6Contain" id="MinutesOut1"></div>
                        <div class="Data7Contain" id="Metric2Metric1"></div>

                        <div class="Data1Contain" id="Date2"></div>
                        <div class="Data2Contain" id="Actual2"></div>
                        <div class="Data3Contain" id="Goal2"></div>
                        <div class="Data4Contain" id="Difference2"></div>
                        <div class="Data5Contain" id="MinutesIn2"></div>
                        <div class="Data6Contain" id="MinutesOut2"></div>
                        <div class="Data7Contain" id="Metric2Metric2"></div>
                        <div class="Data1Contain" id="Date3"></div>
                        <div class="Data2Contain" id="Actual3"></div>
                        <div class="Data3Contain" id="Goal3"></div>
                        <div class="Data4Contain" id="Difference3"></div>
                        <div class="Data5Contain" id="MinutesIn3"></div>
                        <div class="Data6Contain" id="MinutesOut3"></div>
                        <div class="Data7Contain" id="Metric2Metric3"></div>
                        <div class="Data1Contain" id="Date4"></div>
                        <div class="Data2Contain" id="Actual4"></div>
                        <div class="Data3Contain" id="Goal4"></div>
                        <div class="Data4Contain" id="Difference4"></div>
                        <div class="Data5Contain" id="MinutesIn4"></div>
                        <div class="Data6Contain" id="MinutesOut4"></div>
                        <div class="Data7Contain" id="Metric2Metric4"></div>
                        <div class="Data1Contain" id="Date5"></div>
                        <div class="Data2Contain" id="Actual5"></div>
                        <div class="Data3Contain" id="Goal5"></div>
                        <div class="Data4Contain" id="Difference5"></div>
                        <div class="Data5Contain" id="MinutesIn5"></div>
                        <div class="Data6Contain" id="MinutesOut5"></div>
                        <div class="Data7Contain" id="Metric2Metric5"></div>
                        <div class="Data1Contain" id="Date6"></div>
                        <div class="Data2Contain" id="Actual6"></div>
                        <div class="Data3Contain" id="Goal6"></div>
                        <div class="Data4Contain" id="Difference6"></div>
                        <div class="Data5Contain" id="MinutesIn6"></div>
                        <div class="Data6Contain" id="MinutesOut6"></div>
                        <div class="Data7Contain" id="Metric2Metric6"></div>
                        <div class="Data1Contain" id="Date7"></div>
                        <div class="Data2Contain" id="Actual7"></div>
                        <div class="Data3Contain" id="Goal7"></div>
                        <div class="Data4Contain" id="Difference7"></div>
                        <div class="Data5Contain" id="MinutesIn7"></div>
                        <div class="Data6Contain" id="MinutesOut7"></div>
                        <div class="Data7Contain" id="Metric2Metric7"></div>
                        <div class="Data1Contain" id="Date8"></div>
                        <div class="Data2Contain" id="Actual8"></div>
                        <div class="Data3Contain" id="Goal8"></div>
                        <div class="Data4Contain" id="Difference8"></div>
                        <div class="Data5Contain" id="MinutesIn8"></div>
                        <div class="Data6Contain" id="MinutesOut8"></div>
                        <div class="Data7Contain" id="Metric2Metric8"></div>
                        <div class="Data1Contain" id="Date9"></div>
                        <div class="Data2Contain" id="Actual9"></div>
                        <div class="Data3Contain" id="Goal9"></div>
                        <div class="Data4Contain" id="Difference9"></div>
                        <div class="Data5Contain" id="MinutesIn9"></div>
                        <div class="Data6Contain" id="MinutesOut9"></div>
                        <div class="Data7Contain" id="Metric2Metric9"></div>
                        <div class="Data1Contain" id="Date10"></div>
                        <div class="Data2Contain" id="Actual10"></div>
                        <div class="Data3Contain" id="Goal10"></div>
                        <div class="Data4Contain" id="Difference10"></div>
                        <div class="Data5Contain" id="MinutesIn10"></div>
                        <div class="Data6Contain" id="MinutesOut10"></div>
                        <div class="Data7Contain" id="Metric2Metric10"></div>
                        <div class="Data1Contain" id="Date11"></div>
                        <div class="Data2Contain" id="Actual11"></div>
                        <div class="Data3Contain" id="Goal11"></div>
                        <div class="Data4Contain" id="Difference11"></div>
                        <div class="Data5Contain" id="MinutesIn11"></div>
                        <div class="Data6Contain" id="MinutesOut11"></div>
                        <div class="Data7Contain" id="Metric2Metric11"></div>
                        <div class="Data1Contain" id="Date12"></div>
                        <div class="Data2Contain" id="Actual12"></div>
                        <div class="Data3Contain" id="Goal12"></div>
                        <div class="Data4Contain" id="Difference12"></div>
                        <div class="Data5Contain" id="MinutesIn12"></div>
                        <div class="Data6Contain" id="MinutesOut12"></div>
                        <div class="Data7Contain" id="Metric2Metric12"></div>
                        <div class="Data1Contain" id="Date13"></div>
                        <div class="Data2Contain" id="Actual13"></div>
                        <div class="Data3Contain" id="Goal13"></div>
                        <div class="Data4Contain" id="Difference13"></div>
                        <div class="Data5Contain" id="MinutesIn13"></div>
                        <div class="Data6Contain" id="MinutesOut13"></div>
                        <div class="Data7Contain" id="Metric2Metric13"></div>
                        <div class="Data1Contain" id="Date14"></div>
                        <div class="Data2Contain" id="Actual14"></div>
                        <div class="Data3Contain" id="Goal14"></div>
                        <div class="Data4Contain" id="Difference14"></div>
                        <div class="Data5Contain" id="MinutesIn14"></div>
                        <div class="Data6Contain" id="MinutesOut14"></div>
                        <div class="Data7Contain" id="Metric2Metric14"></div>
                        <div class="Data1Contain" id="Date15"></div>
                        <div class="Data2Contain" id="Actual15"></div>
                        <div class="Data3Contain" id="Goal15"></div>
                        <div class="Data4Contain" id="Difference15"></div>
                        <div class="Data5Contain" id="MinutesIn15"></div>
                        <div class="Data6Contain" id="MinutesOut15"></div>
                        <div class="Data7Contain" id="Metric2Metric15"></div>
                        <div class="Data1Contain" id="Date16"></div>
                        <div class="Data2Contain" id="Actual16"></div>
                        <div class="Data3Contain" id="Goal16"></div>
                        <div class="Data4Contain" id="Difference16"></div>
                        <div class="Data5Contain" id="MinutesIn16"></div>
                        <div class="Data6Contain" id="MinutesOut16"></div>
                        <div class="Data7Contain" id="Metric2Metric16"></div>
                        <div class="Data1Contain" id="Date17"></div>
                        <div class="Data2Contain" id="Actual17"></div>
                        <div class="Data3Contain" id="Goal17"></div>
                        <div class="Data4Contain" id="Difference17"></div>
                        <div class="Data5Contain" id="MinutesIn17"></div>
                        <div class="Data6Contain" id="MinutesOut17"></div>
                        <div class="Data7Contain" id="Metric2Metric17"></div>
                        <div class="Data1Contain" id="Date18"></div>
                        <div class="Data2Contain" id="Actual18"></div>
                        <div class="Data3Contain" id="Goal18"></div>
                        <div class="Data4Contain" id="Difference18"></div>
                        <div class="Data5Contain" id="MinutesIn18"></div>
                        <div class="Data6Contain" id="MinutesOut18"></div>
                        <div class="Data7Contain" id="Metric2Metric18"></div>
                        <div class="Data1Contain" id="Date19"></div>
                        <div class="Data2Contain" id="Actual19"></div>
                        <div class="Data3Contain" id="Goal19"></div>
                        <div class="Data4Contain" id="Difference19"></div>
                        <div class="Data5Contain" id="MinutesIn19"></div>
                        <div class="Data6Contain" id="MinutesOut19"></div>
                        <div class="Data7Contain" id="Metric2Metric19"></div>
                        <div class="Data1Contain" id="Date20"></div>
                        <div class="Data2Contain" id="Actual20"></div>
                        <div class="Data3Contain" id="Goal20"></div>
                        <div class="Data4Contain" id="Difference20"></div>
                        <div class="Data5Contain" id="MinutesIn20"></div>
                        <div class="Data6Contain" id="MinutesOut20"></div>
                        <div class="Data7Contain" id="Metric2Metric20"></div>
                        <div class="Data1Contain" id="Date21"></div>
                        <div class="Data2Contain" id="Actual21"></div>
                        <div class="Data3Contain" id="Goal21"></div>
                        <div class="Data4Contain" id="Difference21"></div>
                        <div class="Data5Contain" id="MinutesIn21"></div>
                        <div class="Data6Contain" id="MinutesOut21"></div>
                        <div class="Data7Contain" id="Metric2Metric21"></div>
                        <div class="Data1Contain" id="Date22"></div>
                        <div class="Data2Contain" id="Actual22"></div>
                        <div class="Data3Contain" id="Goal22"></div>
                        <div class="Data4Contain" id="Difference22"></div>
                        <div class="Data5Contain" id="MinutesIn22"></div>
                        <div class="Data6Contain" id="MinutesOut22"></div>
                        <div class="Data7Contain" id="Metric2Metric22"></div>
                        <div class="Data1Contain" id="Date23"></div>
                        <div class="Data2Contain" id="Actual23"></div>
                        <div class="Data3Contain" id="Goal23"></div>
                        <div class="Data4Contain" id="Difference23"></div>
                        <div class="Data5Contain" id="MinutesIn23"></div>
                        <div class="Data6Contain" id="MinutesOut23"></div>
                        <div class="Data7Contain" id="Metric2Metric23"></div>
                        <div class="Data1Contain" id="Date24"></div>
                        <div class="Data2Contain" id="Actual24"></div>
                        <div class="Data3Contain" id="Goal24"></div>
                        <div class="Data4Contain" id="Difference24"></div>
                        <div class="Data5Contain" id="MinutesIn24"></div>
                        <div class="Data6Contain" id="MinutesOut24"></div>
                        <div class="Data7Contain" id="Metric2Metric24"></div>
                        <div class="Data1Contain" id="Date25"></div>
                        <div class="Data2Contain" id="Actual25"></div>
                        <div class="Data3Contain" id="Goal25"></div>
                        <div class="Data4Contain" id="Difference25"></div>
                        <div class="Data5Contain" id="MinutesIn25"></div>
                        <div class="Data6Contain" id="MinutesOut25"></div>
                        <div class="Data7Contain" id="Metric2Metric25"></div>
                        <div class="Data1Contain" id="Date26"></div>
                        <div class="Data2Contain" id="Actual26"></div>
                        <div class="Data3Contain" id="Goal26"></div>
                        <div class="Data4Contain" id="Difference26"></div>
                        <div class="Data5Contain" id="MinutesIn26"></div>
                        <div class="Data6Contain" id="MinutesOut26"></div>
                        <div class="Data7Contain" id="Metric2Metric26"></div>
                        <div class="Data1Contain" id="Date27"></div>
                        <div class="Data2Contain" id="Actual27"></div>
                        <div class="Data3Contain" id="Goal27"></div>
                        <div class="Data4Contain" id="Difference27"></div>
                        <div class="Data5Contain" id="MinutesIn27"></div>
                        <div class="Data6Contain" id="MinutesOut27"></div>
                        <div class="Data7Contain" id="Metric2Metric27"></div>
                        <div class="Data1Contain" id="Date28"></div>
                        <div class="Data2Contain" id="Actual28"></div>
                        <div class="Data3Contain" id="Goal28"></div>
                        <div class="Data4Contain" id="Difference28"></div>
                        <div class="Data5Contain" id="MinutesIn28"></div>
                        <div class="Data6Contain" id="MinutesOut28"></div>
                        <div class="Data7Contain" id="Metric2Metric28"></div>
                        <div class="Data1Contain" id="Date29"></div>
                        <div class="Data2Contain" id="Actual29"></div>
                        <div class="Data3Contain" id="Goal29"></div>
                        <div class="Data4Contain" id="Difference29"></div>
                        <div class="Data5Contain" id="MinutesIn29"></div>
                        <div class="Data6Contain" id="MinutesOut29"></div>
                        <div class="Data7Contain" id="Metric2Metric29"></div>
                        <div class="Data1Contain" id="Date30"></div>
                        <div class="Data2Contain" id="Actual30"></div>
                        <div class="Data3Contain" id="Goal30"></div>
                        <div class="Data4Contain" id="Difference30"></div>
                        <div class="Data5Contain" id="MinutesIn30"></div>
                        <div class="Data6Contain" id="MinutesOut30"></div>
                        <div class="Data7Contain" id="Metric2Metric30"></div>
                        <div class="Data1Contain" id="Date31"></div>
                        <div class="Data2Contain" id="Actual31"></div>
                        <div class="Data3Contain" id="Goal31"></div>
                        <div class="Data4Contain" id="Difference31"></div>
                        <div class="Data5Contain" id="MinutesIn31"></div>
                        <div class="Data6Contain" id="MinutesOut31"></div>
                        <div class="Data7Contain" id="Metric2Metric31"></div>

                    </div>


                </div>
 
                    
     </div>               
                    
                    
                    
                    
                    


                <!---------------------------------------------------------------------------------------------->

          
                <div id="Response"></div>
                <div id="CPI"></div>

            </div>





            <!-----------------------------------ADH Fade In Start------------------------------------>




            <!-----------------------------------Prod Fade In Start------------------------------------>

        </div>

<!-----------------------------------ADH Fade In Start------------------------------------>	

	

	
<!-----------------------------------Prod Fade In Start------------------------------------>	




</body>	
	

	<script>

function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {

    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
</script>
	
	

	
	
	<!--START OF STATIC DATA PULL------------------------------------------------------------>



		




		<script>

								  
			$(document).ready(function(){
	
				
				
    $("#QualityVw").click(function(){
		
		
	

     	$('#CE-PanelSlideScreOvrView').fadeIn(50);
	
	$('#panel,#AIM-PanelSlideScreOvrView,#FCR-PanelSlideScreOvrView,#PROD-PanelSlideScreOvrView,#CON-PanelSlideScreOvrView,#Prod-PanelSlideScreOvrView').fadeOut(25);
		
		
		
		
    });
	
});
				</script>

				<script>

$(document).ready(function(){
	
    $("#SmartADHVw").click(function(){
		
     	$('#AIM-PanelSlideScreOvrView').fadeIn(50);

	$('#CE-PanelSlideScreOvrView,#panel,#PROD-PanelSlideScreOvrView,#CON-PanelSlideScreOvrView,#Prod-PanelSlideScreOvrView,#FCR-PanelSlideScreOvrView,#ADH-PanelSlideScreOvrView').fadeOut(25);

		
		
    });
	
});
		
		</script>
		
						<script>

$(document).ready(function(){
	
    $("#CallVolumeVw").click(function(){
		
     	$('#FCR-PanelSlideScreOvrView').fadeIn(50);

	$('#CE-PanelSlideScreOvrView,#panel,#AIM-PanelSlideScreOvrView,#ADH-PanelSlideScreOvrView,#PROD-PanelSlideScreOvrView,#CON-PanelSlideScreOvrView').fadeOut(25);
		

		
    });
	
});
		
		</script>




				<script>
			
//------------------------------------------------------------------------------------------		
	
$('#CE-X-Unclicked').mouseover(function(){
				
	 	$('#CE-X-Unclicked').css("display","none")
	 	$('#CE-X-Clicked').css("display","block");

});	
			
$('.CE-ClosedX').mouseleave(function(){
				
	 $('#CE-X-Unclicked').css("display","block")
	 $('#CE-X-Clicked').css("display","none");
			
//------------------------------------------------------------------------------------------		

	});
	
$('#AIM-X-Unclicked').mouseover(function(){
				
	 $('#AIM-X-Unclicked').css("display","none")
	 $('#AIM-X-Clicked').css("display","block");

});			
			
$('.AIM-ClosedX').mouseleave(function(){
				
	 $('#AIM-X-Unclicked').css("display","block")
	 $('#AIM-X-Clicked').css("display","none");
});
			
//------------------------------------------------------------------------------------------	
					
	$('#FCR-X-Unclicked').mouseover(function(){
				
	 $('#FCR-X-Unclicked').css("display","none")
	 $('#FCR-X-Clicked').css("display","block");

});			
	 $('.FCR-ClosedX').mouseleave(function(){
				
	 $('#FCR-X-Unclicked').css("display","block")
	 $('#FCR-X-Clicked').css("display","none");
});
			
//------------------------------------------------------------------------------------------					
$('#ADH-X-Unclicked').mouseover(function(){
				
	 $('#ADH-X-Unclicked').css("display","none")
	 $('#ADH-X-Clicked').css("display","block");

});			
$('.ADH-ClosedX').mouseleave(function(){
				
	 $('#ADH-X-Unclicked').css("display","block")
	 $('#ADH-X-Clicked').css("display","none");
});
			
//------------------------------------------------------------------------------------------
			
$('#Prod-X-Unclicked').mouseover(function(){
				
	 $('#Prod-X-Unclicked').css("display","none")
	 $('#Prod-X-Clicked').css("display","block");

});			
$('.Prod-ClosedX').mouseleave(function(){
				
	 $('#Prod-X-Unclicked').css("display","block")
	 $('#Prod-X-Clicked').css("display","none");
});

//------------------------------------------------------------------------------------------		
	
			
$('#CFM-X-Unclicked').mouseover(function(){
				
	 $('#CFM-X-Unclicked').css("display","none")
	 $('#CFM-X-Clicked').css("display","block");

});			
$('.CFM-ClosedX').mouseleave(function(){
				
	 $('#CFM-X-Unclicked').css("display","block")
	 $('#CFM-X-Clicked').css("display","none");
});
			
			
			
	 $(".CE-ClosedX, .AIM-ClosedX, .FCR-ClosedX,.ADH-ClosedX").click(function(){
				
		 $('#CE-PanelSlideScreOvrView').fadeOut("fast");
		 $('#AIM-PanelSlideScreOvrView').fadeOut("fast");
		 $('#FCR-PanelSlideScreOvrView').fadeOut("fast");
		  $('#ADH-PanelSlideScreOvrView').fadeOut("fast");
		 
	});
	
		</script>


<script>
    
    $('#Date1').click(function(){
  $('#Date1,	#Actual1,	#Goal1,	#Difference1,	#MinutesIn1,	#MinutesOut1,	#Metric2Metric1,	#ScheduleMins1').toggleClass('clicked');
        
        
});
$('#Date2').click(function(){$('#Date2,#Actual2,#Goal2,#Difference2,#MinutesIn2,#MinutesOut2,#Metric2Metric2,#ScheduleMins2').toggleClass('clicked'); });
$('#Date3').click(function(){$('#Date3,#Actual3,#Goal3,#Difference3,#MinutesIn3,#MinutesOut3,#Metric2Metric3,#ScheduleMins3').toggleClass('clicked'); });
$('#Date4').click(function(){$('#Date4,#Actual4,#Goal4,#Difference4,#MinutesIn4,#MinutesOut4,#Metric2Metric4,#ScheduleMins4').toggleClass('clicked'); });
$('#Date5').click(function(){$('#Date5,#Actual5,#Goal5,#Difference5,#MinutesIn5,#MinutesOut5,#Metric2Metric5,#ScheduleMins5').toggleClass('clicked'); });
$('#Date6').click(function(){$('#Date6,#Actual6,#Goal6,#Difference6,#MinutesIn6,#MinutesOut6,#Metric2Metric6,#ScheduleMins6').toggleClass('clicked'); });
$('#Date7').click(function(){$('#Date7,#Actual7,#Goal7,#Difference7,#MinutesIn7,#MinutesOut7,#Metric2Metric7,#ScheduleMins7').toggleClass('clicked'); });
$('#Date8').click(function(){$('#Date8,#Actual8,#Goal8,#Difference8,#MinutesIn8,#MinutesOut8,#Metric2Metric8,#ScheduleMins8').toggleClass('clicked'); });
$('#Date9').click(function(){$('#Date9,#Actual9,#Goal9,#Difference9,#MinutesIn9,#MinutesOut9,#Metric2Metric9,#ScheduleMins9').toggleClass('clicked'); });
$('#Date10').click(function(){$('#Date10,#Actual10,#Goal10,#Difference10,#MinutesIn10,#MinutesOut10,#Metric2Metric10,#ScheduleMins10').toggleClass('clicked'); });
$('#Date11').click(function(){$('#Date11,#Actual11,#Goal11,#Difference11,#MinutesIn11,#MinutesOut11,#Metric2Metric11,#ScheduleMins11').toggleClass('clicked'); });
$('#Date12').click(function(){$('#Date12,#Actual12,#Goal12,#Difference12,#MinutesIn12,#MinutesOut12,#Metric2Metric12,#ScheduleMins12').toggleClass('clicked'); });
$('#Date13').click(function(){$('#Date13,#Actual13,#Goal13,#Difference13,#MinutesIn13,#MinutesOut13,#Metric2Metric13,#ScheduleMins13').toggleClass('clicked'); });
$('#Date14').click(function(){$('#Date14,#Actual14,#Goal14,#Difference14,#MinutesIn14,#MinutesOut14,#Metric2Metric14,#ScheduleMins14').toggleClass('clicked'); });
$('#Date15').click(function(){$('#Date15,#Actual15,#Goal15,#Difference15,#MinutesIn15,#MinutesOut15,#Metric2Metric15,#ScheduleMins15').toggleClass('clicked'); });
$('#Date16').click(function(){$('#Date16,#Actual16,#Goal16,#Difference16,#MinutesIn16,#MinutesOut16,#Metric2Metric16,#ScheduleMins16').toggleClass('clicked'); });
$('#Date17').click(function(){$('#Date17,#Actual17,#Goal17,#Difference17,#MinutesIn17,#MinutesOut17,#Metric2Metric17,#ScheduleMins17').toggleClass('clicked'); });
$('#Date18').click(function(){$('#Date18,#Actual18,#Goal18,#Difference18,#MinutesIn18,#MinutesOut18,#Metric2Metric18,#ScheduleMins18').toggleClass('clicked'); });
$('#Date19').click(function(){$('#Date19,#Actual19,#Goal19,#Difference19,#MinutesIn19,#MinutesOut19,#Metric2Metric19,#ScheduleMins19').toggleClass('clicked'); });
$('#Date20').click(function(){$('#Date20,#Actual20,#Goal20,#Difference20,#MinutesIn20,#MinutesOut20,#Metric2Metric20,#ScheduleMins20').toggleClass('clicked'); });
$('#Date21').click(function(){$('#Date21,#Actual21,#Goal21,#Difference21,#MinutesIn21,#MinutesOut21,#Metric2Metric21,#ScheduleMins21').toggleClass('clicked'); });
$('#Date22').click(function(){$('#Date22,#Actual22,#Goal22,#Difference22,#MinutesIn22,#MinutesOut22,#Metric2Metric22,#ScheduleMins22').toggleClass('clicked'); });
$('#Date23').click(function(){$('#Date23,#Actual23,#Goal23,#Difference23,#MinutesIn23,#MinutesOut23,#Metric2Metric23,#ScheduleMins23').toggleClass('clicked'); });
$('#Date24').click(function(){$('#Date24,#Actual24,#Goal24,#Difference24,#MinutesIn24,#MinutesOut24,#Metric2Metric24,#ScheduleMins24').toggleClass('clicked'); });
$('#Date25').click(function(){$('#Date25,#Actual25,#Goal25,#Difference25,#MinutesIn25,#MinutesOut25,#Metric2Metric25,#ScheduleMins25').toggleClass('clicked'); });
$('#Date26').click(function(){$('#Date26,#Actual26,#Goal26,#Difference26,#MinutesIn26,#MinutesOut26,#Metric2Metric26,#ScheduleMins26').toggleClass('clicked'); });
$('#Date27').click(function(){$('#Date27,#Actual27,#Goal27,#Difference27,#MinutesIn27,#MinutesOut27,#Metric2Metric27,#ScheduleMins27').toggleClass('clicked'); });
$('#Date28').click(function(){$('#Date28,#Actual28,#Goal28,#Difference28,#MinutesIn28,#MinutesOut28,#Metric2Metric28,#ScheduleMins28').toggleClass('clicked'); });
$('#Date29').click(function(){$('#Date29,#Actual29,#Goal29,#Difference29,#MinutesIn29,#MinutesOut29,#Metric2Metric29,#ScheduleMins29').toggleClass('clicked'); });
$('#Date30').click(function(){$('#Date30,#Actual30,#Goal30,#Difference30,#MinutesIn30,#MinutesOut30,#Metric2Metric30,#ScheduleMins30').toggleClass('clicked'); });
$('#Date31').click(function(){$('#Date31,#Actual31,#Goal31,#Difference31,#MinutesIn31,#MinutesOut31,#Metric2Metric31,#ScheduleMins31').toggleClass('clicked'); });


                               
                                                                                       
        </script>      



<script> 
$(function(){   $("#Date1").hover(function(){$("#Date1").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual1").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal1").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference1").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn1").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut1").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric1").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins1").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date1").css('background-color',' #669999 ');$("#Actual1").css('background-color',' #669999 ');$("#Goal1").css('background-color',' #669999 ');$("#Difference1").css('background-color',' #669999 ');$("#MinutesIn1").css('background-color',' #669999 ');$("#MinutesOut1").css('background-color',' #669999 ');$("#Metric2Metric1").css('background-color',' #669999 ');$("#ScheduleMins1").css('background-color',' #669999 ');});});
    
    
$(function(){   $("#Date3").hover(function(){$("#Date3").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual3").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal3").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference3").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn3").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut3").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric3").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins3").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date3").css('background-color',' #669999 ');$("#Actual3").css('background-color',' #669999 ');$("#Goal3").css('background-color',' #669999 ');$("#Difference3").css('background-color',' #669999 ');$("#MinutesIn3").css('background-color',' #669999 ');$("#MinutesOut3").css('background-color',' #669999 ');$("#Metric2Metric3").css('background-color',' #669999 ');$("#ScheduleMins3").css('background-color',' #669999 ');});});
$(function(){   $("#Date5").hover(function(){$("#Date5").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual5").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal5").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference5").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn5").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut5").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric5").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins5").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date5").css('background-color',' #669999 ');$("#Actual5").css('background-color',' #669999 ');$("#Goal5").css('background-color',' #669999 ');$("#Difference5").css('background-color',' #669999 ');$("#MinutesIn5").css('background-color',' #669999 ');$("#MinutesOut5").css('background-color',' #669999 ');$("#Metric2Metric5").css('background-color',' #669999 ');$("#ScheduleMins5").css('background-color',' #669999 ');});});
$(function(){   $("#Date7").hover(function(){$("#Date7").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual7").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal7").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference7").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn7").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut7").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric7").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins7").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date7").css('background-color',' #669999 ');$("#Actual7").css('background-color',' #669999 ');$("#Goal7").css('background-color',' #669999 ');$("#Difference7").css('background-color',' #669999 ');$("#MinutesIn7").css('background-color',' #669999 ');$("#MinutesOut7").css('background-color',' #669999 ');$("#Metric2Metric7").css('background-color',' #669999 ');$("#ScheduleMins7").css('background-color',' #669999 ');});});
$(function(){   $("#Date9").hover(function(){$("#Date9").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual9").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal9").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference9").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn9").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut9").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric9").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins9").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date9").css('background-color',' #669999 ');$("#Actual9").css('background-color',' #669999 ');$("#Goal9").css('background-color',' #669999 ');$("#Difference9").css('background-color',' #669999 ');$("#MinutesIn9").css('background-color',' #669999 ');$("#MinutesOut9").css('background-color',' #669999 ');$("#Metric2Metric9").css('background-color',' #669999 ');$("#ScheduleMins9").css('background-color',' #669999 ');});});
$(function(){   $("#Date11").hover(function(){$("#Date11").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual11").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal11").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference11").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn11").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut11").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric11").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins11").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date11").css('background-color',' #669999 ');$("#Actual11").css('background-color',' #669999 ');$("#Goal11").css('background-color',' #669999 ');$("#Difference11").css('background-color',' #669999 ');$("#MinutesIn11").css('background-color',' #669999 ');$("#MinutesOut11").css('background-color',' #669999 ');$("#Metric2Metric11").css('background-color',' #669999 ');$("#ScheduleMins11").css('background-color',' #669999 ');});});
$(function(){   $("#Date13").hover(function(){$("#Date13").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual13").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal13").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference13").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn13").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut13").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric13").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins13").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date13").css('background-color',' #669999 ');$("#Actual13").css('background-color',' #669999 ');$("#Goal13").css('background-color',' #669999 ');$("#Difference13").css('background-color',' #669999 ');$("#MinutesIn13").css('background-color',' #669999 ');$("#MinutesOut13").css('background-color',' #669999 ');$("#Metric2Metric13").css('background-color',' #669999 ');$("#ScheduleMins13").css('background-color',' #669999 ');});});
$(function(){   $("#Date15").hover(function(){$("#Date15").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual15").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal15").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference15").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn15").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut15").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric15").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins15").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date15").css('background-color',' #669999 ');$("#Actual15").css('background-color',' #669999 ');$("#Goal15").css('background-color',' #669999 ');$("#Difference15").css('background-color',' #669999 ');$("#MinutesIn15").css('background-color',' #669999 ');$("#MinutesOut15").css('background-color',' #669999 ');$("#Metric2Metric15").css('background-color',' #669999 ');$("#ScheduleMins15").css('background-color',' #669999 ');});});
$(function(){   $("#Date17").hover(function(){$("#Date17").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual17").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal17").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference17").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn17").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut17").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric17").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins17").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date17").css('background-color',' #669999 ');$("#Actual17").css('background-color',' #669999 ');$("#Goal17").css('background-color',' #669999 ');$("#Difference17").css('background-color',' #669999 ');$("#MinutesIn17").css('background-color',' #669999 ');$("#MinutesOut17").css('background-color',' #669999 ');$("#Metric2Metric17").css('background-color',' #669999 ');$("#ScheduleMins17").css('background-color',' #669999 ');});});
$(function(){   $("#Date19").hover(function(){$("#Date19").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual19").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal19").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference19").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn19").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut19").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric19").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins19").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date19").css('background-color',' #669999 ');$("#Actual19").css('background-color',' #669999 ');$("#Goal19").css('background-color',' #669999 ');$("#Difference19").css('background-color',' #669999 ');$("#MinutesIn19").css('background-color',' #669999 ');$("#MinutesOut19").css('background-color',' #669999 ');$("#Metric2Metric19").css('background-color',' #669999 ');$("#ScheduleMins19").css('background-color',' #669999 ');});});
$(function(){   $("#Date21").hover(function(){$("#Date21").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual21").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal21").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference21").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn21").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut21").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric21").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins21").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date21").css('background-color',' #669999 ');$("#Actual21").css('background-color',' #669999 ');$("#Goal21").css('background-color',' #669999 ');$("#Difference21").css('background-color',' #669999 ');$("#MinutesIn21").css('background-color',' #669999 ');$("#MinutesOut21").css('background-color',' #669999 ');$("#Metric2Metric21").css('background-color',' #669999 ');$("#ScheduleMins21").css('background-color',' #669999 ');});});
$(function(){   $("#Date23").hover(function(){$("#Date23").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual23").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal23").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference23").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn23").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut23").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric23").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins23").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date23").css('background-color',' #669999 ');$("#Actual23").css('background-color',' #669999 ');$("#Goal23").css('background-color',' #669999 ');$("#Difference23").css('background-color',' #669999 ');$("#MinutesIn23").css('background-color',' #669999 ');$("#MinutesOut23").css('background-color',' #669999 ');$("#Metric2Metric23").css('background-color',' #669999 ');$("#ScheduleMins23").css('background-color',' #669999 ');});});
$(function(){   $("#Date25").hover(function(){$("#Date25").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual25").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal25").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference25").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn25").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut25").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric25").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins25").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date25").css('background-color',' #669999 ');$("#Actual25").css('background-color',' #669999 ');$("#Goal25").css('background-color',' #669999 ');$("#Difference25").css('background-color',' #669999 ');$("#MinutesIn25").css('background-color',' #669999 ');$("#MinutesOut25").css('background-color',' #669999 ');$("#Metric2Metric25").css('background-color',' #669999 ');$("#ScheduleMins25").css('background-color',' #669999 ');});});
$(function(){   $("#Date27").hover(function(){$("#Date27").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual27").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal27").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference27").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn27").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut27").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric27").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins27").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date27").css('background-color',' #669999 ');$("#Actual27").css('background-color',' #669999 ');$("#Goal27").css('background-color',' #669999 ');$("#Difference27").css('background-color',' #669999 ');$("#MinutesIn27").css('background-color',' #669999 ');$("#MinutesOut27").css('background-color',' #669999 ');$("#Metric2Metric27").css('background-color',' #669999 ');$("#ScheduleMins27").css('background-color',' #669999 ');});});
$(function(){   $("#Date29").hover(function(){$("#Date29").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual29").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal29").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference29").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn29").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut29").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric29").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins29").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date29").css('background-color',' #669999 ');$("#Actual29").css('background-color',' #669999 ');$("#Goal29").css('background-color',' #669999 ');$("#Difference29").css('background-color',' #669999 ');$("#MinutesIn29").css('background-color',' #669999 ');$("#MinutesOut29").css('background-color',' #669999 ');$("#Metric2Metric29").css('background-color',' #669999 ');$("#ScheduleMins29").css('background-color',' #669999 ');});});
$(function(){   $("#Date31").hover(function(){$("#Date31").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual31").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal31").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference31").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn31").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut31").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric31").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins31").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date31").css('background-color',' #669999 ');$("#Actual31").css('background-color',' #669999 ');$("#Goal31").css('background-color',' #669999 ');$("#Difference31").css('background-color',' #669999 ');$("#MinutesIn31").css('background-color',' #669999 ');$("#MinutesOut31").css('background-color',' #669999 ');$("#Metric2Metric31").css('background-color',' #669999 ');$("#ScheduleMins31").css('background-color',' #669999 ');});});

$(function(){   $("#Date2").hover(function(){$("#Date2").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual2").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal2").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference2").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn2").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut2").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric2").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins2").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date2").css('background-color',' #407F7F ');$("#Actual2").css('background-color',' #407F7F ');$("#Goal2").css('background-color',' #407F7F ');$("#Difference2").css('background-color',' #407F7F ');$("#MinutesIn2").css('background-color',' #407F7F ');$("#MinutesOut2").css('background-color',' #407F7F ');$("#Metric2Metric2").css('background-color',' #407F7F ');$("#ScheduleMins2").css('background-color',' #407F7F ');});});
$(function(){   $("#Date4").hover(function(){$("#Date4").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual4").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal4").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference4").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn4").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut4").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric4").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins4").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date4").css('background-color',' #407F7F ');$("#Actual4").css('background-color',' #407F7F ');$("#Goal4").css('background-color',' #407F7F ');$("#Difference4").css('background-color',' #407F7F ');$("#MinutesIn4").css('background-color',' #407F7F ');$("#MinutesOut4").css('background-color',' #407F7F ');$("#Metric2Metric4").css('background-color',' #407F7F ');$("#ScheduleMins4").css('background-color',' #407F7F ');});});
$(function(){   $("#Date6").hover(function(){$("#Date6").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual6").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal6").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference6").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn6").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut6").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric6").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins6").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date6").css('background-color',' #407F7F ');$("#Actual6").css('background-color',' #407F7F ');$("#Goal6").css('background-color',' #407F7F ');$("#Difference6").css('background-color',' #407F7F ');$("#MinutesIn6").css('background-color',' #407F7F ');$("#MinutesOut6").css('background-color',' #407F7F ');$("#Metric2Metric6").css('background-color',' #407F7F ');$("#ScheduleMins6").css('background-color',' #407F7F ');});});
$(function(){   $("#Date8").hover(function(){$("#Date8").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual8").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal8").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference8").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn8").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut8").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric8").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins8").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date8").css('background-color',' #407F7F ');$("#Actual8").css('background-color',' #407F7F ');$("#Goal8").css('background-color',' #407F7F ');$("#Difference8").css('background-color',' #407F7F ');$("#MinutesIn8").css('background-color',' #407F7F ');$("#MinutesOut8").css('background-color',' #407F7F ');$("#Metric2Metric8").css('background-color',' #407F7F ');$("#ScheduleMins8").css('background-color',' #407F7F ');});});
$(function(){   $("#Date10").hover(function(){$("#Date10").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual10").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal10").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference10").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn10").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut10").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric10").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins10").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date10").css('background-color',' #407F7F ');$("#Actual10").css('background-color',' #407F7F ');$("#Goal10").css('background-color',' #407F7F ');$("#Difference10").css('background-color',' #407F7F ');$("#MinutesIn10").css('background-color',' #407F7F ');$("#MinutesOut10").css('background-color',' #407F7F ');$("#Metric2Metric10").css('background-color',' #407F7F ');$("#ScheduleMins10").css('background-color',' #407F7F ');});});
$(function(){   $("#Date12").hover(function(){$("#Date12").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual12").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal12").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference12").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn12").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut12").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric12").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins12").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date12").css('background-color',' #407F7F ');$("#Actual12").css('background-color',' #407F7F ');$("#Goal12").css('background-color',' #407F7F ');$("#Difference12").css('background-color',' #407F7F ');$("#MinutesIn12").css('background-color',' #407F7F ');$("#MinutesOut12").css('background-color',' #407F7F ');$("#Metric2Metric12").css('background-color',' #407F7F ');$("#ScheduleMins12").css('background-color',' #407F7F ');});});
$(function(){   $("#Date14").hover(function(){$("#Date14").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual14").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal14").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference14").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn14").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut14").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric14").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins14").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date14").css('background-color',' #407F7F ');$("#Actual14").css('background-color',' #407F7F ');$("#Goal14").css('background-color',' #407F7F ');$("#Difference14").css('background-color',' #407F7F ');$("#MinutesIn14").css('background-color',' #407F7F ');$("#MinutesOut14").css('background-color',' #407F7F ');$("#Metric2Metric14").css('background-color',' #407F7F ');$("#ScheduleMins14").css('background-color',' #407F7F ');});});
$(function(){   $("#Date16").hover(function(){$("#Date16").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual16").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal16").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference16").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn16").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut16").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric16").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins16").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date16").css('background-color',' #407F7F ');$("#Actual16").css('background-color',' #407F7F ');$("#Goal16").css('background-color',' #407F7F ');$("#Difference16").css('background-color',' #407F7F ');$("#MinutesIn16").css('background-color',' #407F7F ');$("#MinutesOut16").css('background-color',' #407F7F ');$("#Metric2Metric16").css('background-color',' #407F7F ');$("#ScheduleMins16").css('background-color',' #407F7F ');});});
$(function(){   $("#Date18").hover(function(){$("#Date18").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual18").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal18").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference18").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn18").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut18").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric18").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins18").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date18").css('background-color',' #407F7F ');$("#Actual18").css('background-color',' #407F7F ');$("#Goal18").css('background-color',' #407F7F ');$("#Difference18").css('background-color',' #407F7F ');$("#MinutesIn18").css('background-color',' #407F7F ');$("#MinutesOut18").css('background-color',' #407F7F ');$("#Metric2Metric18").css('background-color',' #407F7F ');$("#ScheduleMins18").css('background-color',' #407F7F ');});});
$(function(){   $("#Date20").hover(function(){$("#Date20").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual20").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal20").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference20").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn20").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut20").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric20").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins20").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date20").css('background-color',' #407F7F ');$("#Actual20").css('background-color',' #407F7F ');$("#Goal20").css('background-color',' #407F7F ');$("#Difference20").css('background-color',' #407F7F ');$("#MinutesIn20").css('background-color',' #407F7F ');$("#MinutesOut20").css('background-color',' #407F7F ');$("#Metric2Metric20").css('background-color',' #407F7F ');$("#ScheduleMins20").css('background-color',' #407F7F ');});});
$(function(){   $("#Date22").hover(function(){$("#Date22").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual22").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal22").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference22").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn22").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut22").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric22").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins22").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date22").css('background-color',' #407F7F ');$("#Actual22").css('background-color',' #407F7F ');$("#Goal22").css('background-color',' #407F7F ');$("#Difference22").css('background-color',' #407F7F ');$("#MinutesIn22").css('background-color',' #407F7F ');$("#MinutesOut22").css('background-color',' #407F7F ');$("#Metric2Metric22").css('background-color',' #407F7F ');$("#ScheduleMins22").css('background-color',' #407F7F ');});});
$(function(){   $("#Date24").hover(function(){$("#Date24").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual24").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal24").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference24").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn24").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut24").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric24").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins24").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date24").css('background-color',' #407F7F ');$("#Actual24").css('background-color',' #407F7F ');$("#Goal24").css('background-color',' #407F7F ');$("#Difference24").css('background-color',' #407F7F ');$("#MinutesIn24").css('background-color',' #407F7F ');$("#MinutesOut24").css('background-color',' #407F7F ');$("#Metric2Metric24").css('background-color',' #407F7F ');$("#ScheduleMins24").css('background-color',' #407F7F ');});});
$(function(){   $("#Date26").hover(function(){$("#Date26").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual26").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal26").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference26").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn26").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut26").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric26").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins26").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date26").css('background-color',' #407F7F ');$("#Actual26").css('background-color',' #407F7F ');$("#Goal26").css('background-color',' #407F7F ');$("#Difference26").css('background-color',' #407F7F ');$("#MinutesIn26").css('background-color',' #407F7F ');$("#MinutesOut26").css('background-color',' #407F7F ');$("#Metric2Metric26").css('background-color',' #407F7F ');$("#ScheduleMins26").css('background-color',' #407F7F ');});});
$(function(){   $("#Date28").hover(function(){$("#Date28").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual28").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal28").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference28").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn28").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut28").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric28").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins28").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date28").css('background-color',' #407F7F ');$("#Actual28").css('background-color',' #407F7F ');$("#Goal28").css('background-color',' #407F7F ');$("#Difference28").css('background-color',' #407F7F ');$("#MinutesIn28").css('background-color',' #407F7F ');$("#MinutesOut28").css('background-color',' #407F7F ');$("#Metric2Metric28").css('background-color',' #407F7F ');$("#ScheduleMins28").css('background-color',' #407F7F ');});});
$(function(){   $("#Date30").hover(function(){$("#Date30").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual30").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal30").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference30").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn30").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut30").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric30").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins30").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date30").css('background-color',' #407F7F ');$("#Actual30").css('background-color',' #407F7F ');$("#Goal30").css('background-color',' #407F7F ');$("#Difference30").css('background-color',' #407F7F ');$("#MinutesIn30").css('background-color',' #407F7F ');$("#MinutesOut30").css('background-color',' #407F7F ');$("#Metric2Metric30").css('background-color',' #407F7F ');$("#ScheduleMins30").css('background-color',' #407F7F ');});});


		</script>  


<script>
    
    
    $(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual1").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal1").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference1").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual1").css('background-color',' #669999 ');$("#Goal1").css('background-color',' #669999 ');$("#Difference1").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual3").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal3").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference3").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual3").css('background-color',' #669999 ');$("#Goal3").css('background-color',' #669999 ');$("#Difference3").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual5").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal5").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference5").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual5").css('background-color',' #669999 ');$("#Goal5").css('background-color',' #669999 ');$("#Difference5").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual7").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal7").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference7").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual7").css('background-color',' #669999 ');$("#Goal7").css('background-color',' #669999 ');$("#Difference7").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual9").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal9").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference9").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual9").css('background-color',' #669999 ');$("#Goal9").css('background-color',' #669999 ');$("#Difference9").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual11").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal11").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference11").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual11").css('background-color',' #669999 ');$("#Goal11").css('background-color',' #669999 ');$("#Difference11").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual13").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal13").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference13").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual13").css('background-color',' #669999 ');$("#Goal13").css('background-color',' #669999 ');$("#Difference13").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual15").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal15").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference15").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual15").css('background-color',' #669999 ');$("#Goal15").css('background-color',' #669999 ');$("#Difference15").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual17").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal17").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference17").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual17").css('background-color',' #669999 ');$("#Goal17").css('background-color',' #669999 ');$("#Difference17").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual19").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal19").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference19").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual19").css('background-color',' #669999 ');$("#Goal19").css('background-color',' #669999 ');$("#Difference19").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitl1").hover(function(){$("#Actual21").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal21").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference21").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual21").css('background-color',' #669999 ');$("#Goal21").css('background-color',' #669999 ');$("#Difference21").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual23").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal23").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference23").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual23").css('background-color',' #669999 ');$("#Goal23").css('background-color',' #669999 ');$("#Difference23").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual25").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal25").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference25").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual25").css('background-color',' #669999 ');$("#Goal25").css('background-color',' #669999 ');$("#Difference25").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual27").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal27").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference27").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual27").css('background-color',' #669999 ');$("#Goal27").css('background-color',' #669999 ');$("#Difference27").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual29").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal29").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference29").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual29").css('background-color',' #669999 ');$("#Goal29").css('background-color',' #669999 ');$("#Difference29").css('background-color',' #669999 ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle").hover(function(){$("#Actual31").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal31").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference31").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Actual31").css('background-color',' #669999 ');$("#Goal31").css('background-color',' #669999 ');$("#Difference31").css('background-color',' #669999 ');});});

$(function(){   $("#MetricTitles_Agents_OverHeadTitle2").hover(function(){$("#Date2").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual2").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal2").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference2").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn2").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut2").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric2").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins2").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date2").css('background-color',' #407F7F ');$("#Actual2").css('background-color',' #407F7F ');$("#Goal2").css('background-color',' #407F7F ');$("#Difference2").css('background-color',' #407F7F ');$("#MinutesIn2").css('background-color',' #407F7F ');$("#MinutesOut2").css('background-color',' #407F7F ');$("#Metric2Metric2").css('background-color',' #407F7F ');$("#ScheduleMins2").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle4").hover(function(){$("#Date4").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual4").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal4").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference4").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn4").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut4").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric4").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins4").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date4").css('background-color',' #407F7F ');$("#Actual4").css('background-color',' #407F7F ');$("#Goal4").css('background-color',' #407F7F ');$("#Difference4").css('background-color',' #407F7F ');$("#MinutesIn4").css('background-color',' #407F7F ');$("#MinutesOut4").css('background-color',' #407F7F ');$("#Metric2Metric4").css('background-color',' #407F7F ');$("#ScheduleMins4").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle6").hover(function(){$("#Date6").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual6").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal6").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference6").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn6").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut6").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric6").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins6").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date6").css('background-color',' #407F7F ');$("#Actual6").css('background-color',' #407F7F ');$("#Goal6").css('background-color',' #407F7F ');$("#Difference6").css('background-color',' #407F7F ');$("#MinutesIn6").css('background-color',' #407F7F ');$("#MinutesOut6").css('background-color',' #407F7F ');$("#Metric2Metric6").css('background-color',' #407F7F ');$("#ScheduleMins6").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle8").hover(function(){$("#Date8").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual8").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal8").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference8").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn8").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut8").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric8").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins8").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date8").css('background-color',' #407F7F ');$("#Actual8").css('background-color',' #407F7F ');$("#Goal8").css('background-color',' #407F7F ');$("#Difference8").css('background-color',' #407F7F ');$("#MinutesIn8").css('background-color',' #407F7F ');$("#MinutesOut8").css('background-color',' #407F7F ');$("#Metric2Metric8").css('background-color',' #407F7F ');$("#ScheduleMins8").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle10").hover(function(){$("#Date10").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual10").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal10").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference10").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn10").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut10").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric10").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins10").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date10").css('background-color',' #407F7F ');$("#Actual10").css('background-color',' #407F7F ');$("#Goal10").css('background-color',' #407F7F ');$("#Difference10").css('background-color',' #407F7F ');$("#MinutesIn10").css('background-color',' #407F7F ');$("#MinutesOut10").css('background-color',' #407F7F ');$("#Metric2Metric10").css('background-color',' #407F7F ');$("#ScheduleMins10").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle12").hover(function(){$("#Date12").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual12").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal12").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference12").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn12").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut12").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric12").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins12").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date12").css('background-color',' #407F7F ');$("#Actual12").css('background-color',' #407F7F ');$("#Goal12").css('background-color',' #407F7F ');$("#Difference12").css('background-color',' #407F7F ');$("#MinutesIn12").css('background-color',' #407F7F ');$("#MinutesOut12").css('background-color',' #407F7F ');$("#Metric2Metric12").css('background-color',' #407F7F ');$("#ScheduleMins12").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle14").hover(function(){$("#Date14").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual14").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal14").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference14").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn14").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut14").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric14").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins14").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date14").css('background-color',' #407F7F ');$("#Actual14").css('background-color',' #407F7F ');$("#Goal14").css('background-color',' #407F7F ');$("#Difference14").css('background-color',' #407F7F ');$("#MinutesIn14").css('background-color',' #407F7F ');$("#MinutesOut14").css('background-color',' #407F7F ');$("#Metric2Metric14").css('background-color',' #407F7F ');$("#ScheduleMins14").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle16").hover(function(){$("#Date16").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual16").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal16").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference16").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn16").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut16").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric16").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins16").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date16").css('background-color',' #407F7F ');$("#Actual16").css('background-color',' #407F7F ');$("#Goal16").css('background-color',' #407F7F ');$("#Difference16").css('background-color',' #407F7F ');$("#MinutesIn16").css('background-color',' #407F7F ');$("#MinutesOut16").css('background-color',' #407F7F ');$("#Metric2Metric16").css('background-color',' #407F7F ');$("#ScheduleMins16").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle18").hover(function(){$("#Date18").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual18").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal18").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference18").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn18").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut18").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric18").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins18").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date18").css('background-color',' #407F7F ');$("#Actual18").css('background-color',' #407F7F ');$("#Goal18").css('background-color',' #407F7F ');$("#Difference18").css('background-color',' #407F7F ');$("#MinutesIn18").css('background-color',' #407F7F ');$("#MinutesOut18").css('background-color',' #407F7F ');$("#Metric2Metric18").css('background-color',' #407F7F ');$("#ScheduleMins18").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle20").hover(function(){$("#Date20").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual20").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal20").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference20").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn20").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut20").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric20").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins20").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date20").css('background-color',' #407F7F ');$("#Actual20").css('background-color',' #407F7F ');$("#Goal20").css('background-color',' #407F7F ');$("#Difference20").css('background-color',' #407F7F ');$("#MinutesIn20").css('background-color',' #407F7F ');$("#MinutesOut20").css('background-color',' #407F7F ');$("#Metric2Metric20").css('background-color',' #407F7F ');$("#ScheduleMins20").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle22").hover(function(){$("#Date22").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual22").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal22").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference22").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn22").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut22").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric22").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins22").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date22").css('background-color',' #407F7F ');$("#Actual22").css('background-color',' #407F7F ');$("#Goal22").css('background-color',' #407F7F ');$("#Difference22").css('background-color',' #407F7F ');$("#MinutesIn22").css('background-color',' #407F7F ');$("#MinutesOut22").css('background-color',' #407F7F ');$("#Metric2Metric22").css('background-color',' #407F7F ');$("#ScheduleMins22").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle24").hover(function(){$("#Date24").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual24").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal24").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference24").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn24").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut24").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric24").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins24").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date24").css('background-color',' #407F7F ');$("#Actual24").css('background-color',' #407F7F ');$("#Goal24").css('background-color',' #407F7F ');$("#Difference24").css('background-color',' #407F7F ');$("#MinutesIn24").css('background-color',' #407F7F ');$("#MinutesOut24").css('background-color',' #407F7F ');$("#Metric2Metric24").css('background-color',' #407F7F ');$("#ScheduleMins24").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle26").hover(function(){$("#Date26").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual26").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal26").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference26").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn26").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut26").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric26").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins26").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date26").css('background-color',' #407F7F ');$("#Actual26").css('background-color',' #407F7F ');$("#Goal26").css('background-color',' #407F7F ');$("#Difference26").css('background-color',' #407F7F ');$("#MinutesIn26").css('background-color',' #407F7F ');$("#MinutesOut26").css('background-color',' #407F7F ');$("#Metric2Metric26").css('background-color',' #407F7F ');$("#ScheduleMins26").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle28").hover(function(){$("#Date28").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual28").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal28").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference28").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn28").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut28").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric28").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins28").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date28").css('background-color',' #407F7F ');$("#Actual28").css('background-color',' #407F7F ');$("#Goal28").css('background-color',' #407F7F ');$("#Difference28").css('background-color',' #407F7F ');$("#MinutesIn28").css('background-color',' #407F7F ');$("#MinutesOut28").css('background-color',' #407F7F ');$("#Metric2Metric28").css('background-color',' #407F7F ');$("#ScheduleMins28").css('background-color',' #407F7F ');});});
$(function(){   $("#MetricTitles_Agents_OverHeadTitle30").hover(function(){$("#Date30").css('background-color','rgba( 0, 0, 0, .4 )');$("#Actual30").css('background-color','rgba( 0, 0, 0, .4 )');$("#Goal30").css('background-color','rgba( 0, 0, 0, .4 )');$("#Difference30").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesIn30").css('background-color','rgba( 0, 0, 0, .4 )');$("#MinutesOut30").css('background-color','rgba( 0, 0, 0, .4 )');$("#Metric2Metric30").css('background-color','rgba( 0, 0, 0, .4 )');$("#ScheduleMins30").css('background-color','rgba( 0, 0, 0, .4 )');},function(){$("#Date30").css('background-color',' #407F7F ');$("#Actual30").css('background-color',' #407F7F ');$("#Goal30").css('background-color',' #407F7F ');$("#Difference30").css('background-color',' #407F7F ');$("#MinutesIn30").css('background-color',' #407F7F ');$("#MinutesOut30").css('background-color',' #407F7F ');$("#Metric2Metric30").css('background-color',' #407F7F ');$("#ScheduleMins30").css('background-color',' #407F7F ');});});

</script>

    <script>  var $window = $(window),
       $stickyEl = $('#topbarSUP'),
       elTop = $stickyEl.offset().top;


   $window.scroll(function() {
        $stickyEl.toggleClass('sticky', $window.scrollTop() > elTop);

    });</script>




<script>  var $windowIcons = $(window),
       $stickyIcons = $('#icons_container'),
       elTop = $stickyIcons.offset().top;


   $windowIcons.scroll(function() {
        $stickyIcons.toggleClass('sticky_icons', $windowIcons.scrollTop() > elTop);

    });</script>

<script>
    $( window ).on( "load", CPIResponses );



    function CPIResponses() {
        //jquery

        var AgentResponse = 5;
        //ajax call
        $.ajax( {

            type: 'POST',
            url: '../CPI_agent_check_the_status.php',
            cache: false,

            success: function ( data ) {


                var obj = JSON.parse( data );

                $( '#Response' ).html( obj.Response );
                window.AgentResponse = $( '#Response' ).text();
            },


        } )




    };
</script>


<script>
    $( window ).on( "load", agentRes );

    function agentRes() {

        var url = "CPI_PopUpPage.php";

        $.ajax( {
            type: 'POST',
            url: '../DataPull_Agent.php',
            cache: false,
            data: 'CPI',
            success: function ( CPI ) {




                window.obj = $( '#CPI' ).text();





                console.log( window.AgentResponse );



                console.log( window.obj );




                if ( window.obj == 0 ) {

                    $( '#CPI_Popup_link' ).animate( {
                        "top": "-143px",
                        "z-index": "1"
                    } );

                } else if ( window.obj == 1 && window.AgentResponse == "Yes" ) {

                } else if ( window.obj == 1 && window.AgentResponse == "No" ) {

                } else if ( window.AgentResponse == "Undecided" ) {

                } else if ( window.obj == null && window.AgentResponse == null ) {
                    $( location ).attr( 'href', url );


                }

            },


        } )




    };
</script>
    
    



		
</html>