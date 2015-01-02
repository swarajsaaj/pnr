<?php namespace Swarajsaaj\Pnr;
	
	/**
	 * Pnr Class is main class that requests PNR status from CGI script at IR website 
	 * @author Swaraj Pal (swarajsaaj)
	 */
class Pnr {


	/**
	 * toJson() converts a PHP array to JSON, incldues support for older versions of PHP
	 * @param Array $arr - Array that is to be converted to JSON
	 * @return JSON string 
	 */
        function toJson($arr) 
        {
	    	if(function_exists('json_encode'))
	    	{
	    	   return json_encode($arr); 
	    	} 
	    	$parts = array();
	    	$is_list = false;

	    	//Find out if the given array is a numerical array
	    	$keys = array_keys($arr);
	    	$max_length = count($arr)-1;
	    	
	    	if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) 
			{//See if the first key is 0 and last key is length - 1
			        $is_list = true;
			        for($i=0; $i<count($keys); $i++)
			         { //See if each key correspondes to its position
			            if($i != $keys[$i])
			            { //A key fails at position check.
			                $is_list = false; //It is an associative array.
			                break;
			            }
			         }
	       }

		    foreach($arr as $key=>$value)
		    {
		        if(is_array($value)) 
		        { //Custom handling for arrays
		            if($is_list) $parts[] = toJson($value); /* :RECURSION: */
		            else $parts[] = '"' . $key . '":' . toJson($value); /* :RECURSION: */
		        }
		        else 
		        {
		            $str = '';
		            if(!$is_list) $str = '"' . $key . '":';

		            //Custom handling for multiple data types
		            if(is_numeric($value)) $str .= $value; //Numbers
		            elseif($value === false) $str .= 'false'; //The booleans
		            elseif($value === true) $str .= 'true';
		            else $str .= '"' . addslashes($value) . '"'; //All other things
		            // :TODO: Is there any more datatype we should be in the lookout for? (Object?)

		            $parts[] = $str;
		        }
		    }

			    $json = implode(',',$parts);
			    
			    if($is_list) return '[' . $json . ']';//Return numerical JSON
			    return '{' . $json . '}';//Return associative JSON
	       }



		    /**
		     * webCall() make a call to Indian Railways site using curl
		     * @param $url URL to place call to
		     * @param $postData the post string containing post variables
		     * @param $refer The referer page, here CAPTCHA page
		     * @return string html data
		     */
			function webCall($url,$postData = null,$refer=null)
			{

				/**
				 * Make a connection
				 */
				$curl_connection = curl_init($url);

				/**
				 * Set URL options
				 */
				curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($curl_connection, CURLOPT_USERAGENT,
				  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
				curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, true);
				

				if(isset($postData))
				{
				    //Post data
				    curl_setopt($curl_connection, CURLOPT_POST,true);	
					curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $postData);
				}


				if(isset($refer))
				{
				 //set referer
				 curl_setopt($curl_connection, CURLOPT_REFERER, $refer);
				}

				//Execute Request
				$result = curl_exec($curl_connection);
				
				//Terminate Connection
				curl_close($curl_connection);

				return $result;
			}


			/**
			 * to Make a post string of post data in array
			 * @param $postArray - the post data
			 * @return string post_string
			 */
			function createPostString($postArray)
			{
			
			foreach ( $postArray as $key => $value) {
			    $post_items[] = $key . '=' . $value;
			}
			return implode ('&', $post_items);
			}

			/**
			 * The core function that accepts calls from the class
			 * @param $pnr The PNR number in integer format
			 * @return JSON data
			 */
		    function request($pnr)
		    {

			
				$pnrNumber = $pnr;
				
				//Predefined URL that is currently in use (2/1/2015)
				$urlCaptcha = 'http://www.indianrail.gov.in/pnr_Enq.html';

				// The CGI script URL
				$urlPnr = 'http://www.indianrail.gov.in/cgi_bin/inet_pnstat_cgi_10521.cgi';

				
				//Make the Post data array with field names as in original Form
				// lccp_pnrno1 is field name at the website
				$post_data['lccp_pnrno1'] = $pnrNumber;

				/**
				 * @todo Check if these following are still supported
				 */ 

				$post_data['lccp_cap_val'] = 12345; //dummy captcha
				$post_data['lccp_capinp_val'] = 12345;
				$post_data['submit'] = "Get Status";

				$post_string = $this->createPostString($post_data);

				$result = $this->webCall($urlPnr,$post_string,$urlCaptcha );

				
				/**
				 * @todo This is parsing of Resulting HTML , this can change in future
				 *
				 * The resulting table in html with all concerned values have class table_border_both
				 * Extracting them all in $matches
				 */

				$matches = array();

				preg_match_all('/<td class="table_border_both">(.*)<\/td>/i',$result,$matches);
		

				$resultVal = array(
				    'status'    =>    "INVALID",
				    'data'      =>    array()                
				);

				if (count($matches)>1&&count($matches[1])>8)
				{
				 $arr = $matches[1];
				 $i=0;
				 $j=0;
				 $tmpValue =array(
				          "pnr" => $pnrNumber,
				          "train_name" => "",
				          "train_number" => "",
				          "from" => "",
				          "to" => "",
				          "reservedto" => "",
				          "board" => "",
				          "class" => "",
				          "travel_date" => "",
				          "passenger" => array()
						 );
			
				 $tmpValue['train_number'] = $arr[0];
				 $tmpValue['train_name'] = $arr[1];
				 $tmpValue['travel_date'] = $arr[2];
				 $tmpValue['from'] = $arr[3];
				 $tmpValue['to'] = $arr[4];
				 $tmpValue['reservedto'] = $arr[5];
				 $tmpValue['board'] = $arr[6];
				 $tmpValue['class'] = $arr[7];
				 $stnum="";

				 /**
				  * Remove the formatting and add multi passenger status
				  */
				 foreach ($arr as $value) 
				 {
				 
					   $i++;

					   if($i>8)
					   {
					   		//Remove bold tags in booking status and current status
						   $value=trim(preg_replace('/<B>/', '', $value));
						   $value=trim(preg_replace('/<\/B>/', '', $value));


						   //First 8 are already filled in and 9th is pasenger name
						   //10th - Seat number , 11th- Status
						   $seatRemainder=$i%3;    
						    if($seatRemainder==1)  //Its a seat number iteration
						    {      
						     $seatNumber = $value;
						    }
						    else if($seatRemainder==2)  //It is status iteration
						    {
						      array_push($tmpValue["passenger"],array(
						           "seat_number" => $seatNumber, 
						           "status" => $value 
						        ));
						    }
					  }
				  }
				 $resultVal['data'] = $tmpValue;
				 $resultVal['status'] = 'OK';
				}
			
			    /**
			     * Returning JSON data
			     */
				$jsondata =  $this->toJson($resultVal);
				
				 if(array_key_exists('callback', $_GET)){

				    header('Content-Type: text/javascript; charset=utf8');
				    header('Access-Control-Max-Age: 3628800');
				    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

				    $callback = $_GET['callback'];
				    echo $callback.'('.$jsondata.');';
				    
				}else{
				    // normal JSON string
				    header('Content-Type: application/json; charset=utf8');
				 
				   return $jsondata;
				}
				
		   }

}
