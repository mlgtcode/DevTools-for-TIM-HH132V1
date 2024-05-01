<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DevTools for TIM-HH132V1</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="/">DevTools</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="/">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/index.php?pload=action&acmd=GetUsageSettings&anum=0">data usage</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/index.php?pload=action&acmd=GetUsageRecord&anum=0">usage record</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/index.php?pload=action&acmd=GetNetworkInfo&anum=2">network info</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/index.php?pload=apidirect">other</a>
            </li>
          </ul>
          <form class="d-flex" role="search" action="/" method="get">
            <input class="form-control me-2" name="id" type="id" placeholder="_TclRequestVerificationToken" aria-label="Save">
            <button class="btn btn-outline-success" type="submit">Save</button>
          </form>
        </div>
      </div>
    </nav>

    <div class="container my-5">
<!-- sudo php -S 192.168.1.58:8585 -->
<?php
if (isset($_GET["id"]))
{
    file_put_contents('rtoken.txt', $_GET["id"]);
}
?>
<form>

<?php
function routerAPI($fname, $cpid, $params)
{
    $rettok = htmlspecialchars(file_get_contents('rtoken.txt'));
    $headers = [
        "Referer: http://192.168.1.1/default.html",
        "Origin: http://192.168.1.1", "Content-Type: application/json",
        "_TclRequestVerificationKey: XXX",
        "_TclRequestVerificationToken: $rettok",
        ];

    $payload = ["id" => $cpid, "jsonrpc" => "2.0", "method" => $fname, "params" => $params, ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_URL, "http://192.168.1.1/jrd/webapi");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    $result_dec = json_decode($result, true);
    if (isset($result_dec["error"]["message"]))
    {
        echo "Error: " . $result_dec["error"]["message"];
    }
    else
    {
        if ($fname === "GetUsageSettings")
        {
            echo "\n<b>GIGAS USED (last reboot till now):</b> \n" . $result_dec["result"]["UsedData"] / pow(1024, 3) . " GB\n\n"; // traffico   
            // echo "<hr><b>GB TOTAL:</b> " . htmlspecialchars(file_get_contents('gb_tot.txt')) / pow(1024, 3) . " GB\n\n";
            // echo "<br><br>SRL:" . $result_dec["result"]["UsedData"];

        }
        else
        {
            // var_dump($result);
            $result2 = json_decode($result, true);
            highlight_string(json_encode($result2, JSON_PRETTY_PRINT));
        }
    }
}

if ($_GET['pload'] === "GUS")
{
    routerAPI("GetUsageSettings", "0", "{}");
}
else if ($_GET['pload'] === "apidirect")
{
    echo "Send a GET request to this endpoint, with following post fields: <code>acmd</code> for action, <code>num</code> for action number, <code>param</code> for parameter. You may also use the form below.<hr>";
    echo '<div class="container px-5 my-5">
    <form id="routerForm" action="index.php?pload=apidirect" method="get">
        <input type="hidden" name="pload" value="apidirect" />
        <div class="mb-3">
            <label class="form-label" for="action">Action</label>
            <input class="form-control" name="command" id="command" type="text" placeholder="Action" data-sb-validations="required" />
            <div class="invalid-feedback" data-sb-feedback="action:required">Action is required.</div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="numOptional">Num (optional)</label>
            <input class="form-control" id="numOptional" type="text" name="num" id="num" placeholder="Num (optional)" data-sb-validations="" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="params">Params</label>
            <textarea class="form-control" id="params" type="text" name="params" id="params" placeholder="{ }" style="height: 10rem;"></textarea>
        </div>
        <div class="d-none" id="submitSuccessMessage">
            <div class="text-center mb-3">
                <div class="fw-bolder">Form submission successful!</div>
            </div>
        </div>
        <div class="d-none" id="submitErrorMessage">
            <div class="text-center text-danger mb-3">Error sending message!</div>
        </div>
        <div class="d-grid">
            <button class="btn btn-primary btn-lg" id="submitButton" type="submit" actoon="submit">Submit</button>
        </div>
    </form>
</div>
';
    echo "<h4>Result</h4>";
    routerAPI($_GET['command'], $_GET['num'], $_GET['param']);
}
else if ($_GET['pload'] === "action")
{
    routerAPI($_GET['acmd'], $_GET['anum'], "{}");
}
else
{
    echo "<center><h3>Welcome!<h3></center>";
    echo "<center><img src=' data:image/png;base64,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'></img></center>";
    echo "<hr><p>Make sure to provide a valid and up-to-date Token, otherwise you will see 'unauthorized' erros.<br><br>Currently the following _TclRequestVerificationToken is set:<br><code>";
    echo htmlspecialchars(file_get_contents('rtoken.txt'));
    echo "</code></p>";
}
?>

        <hr class="col-1 my-4">
        <small>DevTools for TIM HH132V1</small>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>
