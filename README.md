##A Laravel Package for PNR Enquiry through Indian Railways


>Note:- This is not intended for any DOS attack, just to ease the Automation of PNR enquiry through Laravel

PNR Enquiry for Laravel

Add swarajsaaj/pnr to composer.json.

"swarajsaaj/pnr": "dev-master"

Run <code>composer update</code> to pull down the latest version.

Now open up app/config/app.php and add the service provider to your providers array.

```php
'providers' => array(
    'Swarajsaaj\Pnr\PnrServiceProvider',
)
```

Now add the alias.

```php
'aliases' => array(
    'Pnr' => 'Swarajsaaj\Pnr\Facades\Pnr',
)
```

##Usage

Use Alias 'Pnr' as follows:-

```php

   $pnr=Pnr::request(1234567890);  //PNR number here
   echo $pnr;

```

it returns the PNR information in a JSON format as follows


```javasript
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
```


Hope it helps out. 
Will be working on adding other Information in the API .
