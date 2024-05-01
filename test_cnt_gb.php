<?php
/*
Very simple script to keep data usage even after reboot
You need to run this script with a cronjob approx. every 30 minutes. Set gb used at gb_cache.txt, etc.
Version: 0.1 ALPHA
*/

$rettok = htmlspecialchars(file_get_contents('/home/testuser/public_html/rtoken.txt'));
$zgwt = htmlspecialchars(file_get_contents('/home/testuser/public_html/gb_cache.txt'));
$towt = htmlspecialchars(file_get_contents('/home/testuser/public_html/gb_tot.txt'));
$headers = ["Referer: http://192.168.1.1/default.html", "Origin: http://192.168.1.1", "Content-Type: application/json", "_TclRequestVerificationKey: XXX", "_TclRequestVerificationToken: $rettok", // token was set via WebUi
];

$fname = "GetUsageSettings";
$params = "{ }";
$cpid = "1";

$payload = ["id" => $cpid, "jsonrpc" => "2.0", "method" => $fname, "params" => $params, ];

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_URL, "http://192.168.1.1/jrd/webapi");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
$result_dec = json_decode($result, true);
$cur_dat = "UNSET";
if (isset($result_dec["error"]["message"]))
{
    echo "Error: " . $result_dec["error"]["message"];
}
else
{
    $cur_dat = $result_dec["result"]["UsedData"];
    echo "cur_dat: >" . $cur_dat . "<\n";
}

$VALX = $cur_dat - $zgwt;
echo "Differende is: " . $VALX . "\n\n";

if ($cur_dat === "UNSET")
{
    echo "CNT IS UNSET, TERMINATING SCRIPT.";
}
else
{
    if (substr(strval($VALX) , 0, 1) == "-")
    {
        echo "IS NAGATIV. SETTING CACHE TO ZERO...";
        // resetting gp_cache
        file_put_contents('/home/testuser/public_html/gb_cache.txt', '0');
    }
    else
    {
        echo "COUNTING...";
        $fresult = $towt + $VALX;
        // write $VALX to gb_tot;
        file_put_contents('/home/testuser/public_html/gb_tot.txt', $fresult);
        file_put_contents('/home/testuser/public_html/gb_cache.txt', $cur_dat);
    }
}
?>
