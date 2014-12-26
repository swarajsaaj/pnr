##A Laravel Package for PNR Enquiry through Indian Railways

<<<<<<< HEAD:READEME.md
>Note:- This is not intended for any DOS attack, just to ease the Automation of PNR enquiry through Laravel

PNR Enquiry for Laravel

Add swarajsaaj/pnr to composer.json.

"swarajsaaj/pnr": "dev-master"

Run <code>composer update</code> to pull down the latest version.

Now open up app/config/app.php and add the service provider to your providers array.

<code>
'providers' => array(
    'Swarajsaaj\Pnr\PnrServiceProvider',
)
</code>

Now add the alias.

<code>
'aliases' => array(
    'Pnr' => 'Swarajsaaj\Pnr\Facades\Pnr',
)
</code>

##Use

Use following

<code>
	Pnr::request(1234567890);  //PNR number here
</code>

it returns the PNR information in a JSON format as follows

<code>
	{
   "status":"OK",
   "data":{
      "pnr":1234567890,
      "train_name":"HIMACHAL EXPRES",
      "train_number":"*14554",
      "from":"RPAR",
      "to":"DLI ",
      "reservedto":"DLI ",
      "board":"GANL",
      "class":" SL",
      "travel_date":"27-12-2014",
      "passenger":[
         {
            "seat_number":"W\/L 22,GNWL",
            "status":"W\/L 2"
         }
      ]
   }
}
</code>

Hope it helps out. 
Will be working on adding other Information in the API .
=======
Note:- This is not intended for any DOS attack, just to ease the Automation of PNR enquiry through Laravel
>>>>>>> 5be2fd478244ebd63be0732454a138df40cae254:README.md
