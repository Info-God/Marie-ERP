<!DOCTYPE html>
<html>
<head>
    <title>Perfex CRM - Server Requirements</title>
    <style>
    body {
        padding-top:15px;
        font-family: Tahoma, Geneva, sans-serif;
        background: #f9fafb;
        font-size:14px;
    }
    #wrapper {
        width:600px;
        margin:0 auto;
        background: #fff;
        border-radius:10px;
        padding: 15px;
        border:2px solid #f0f0f0;
        -webkit-box-shadow: 0px 1px 15px 1px rgba(90, 90, 90, 0.08);
        box-shadow: 0px 1px 15px 1px rgba(90, 90, 90, 0.08);
    }
    a {
        text-decoration:none;
        color:#276bb3;
    }

    h1 {
        text-align: center;
        color: #424242;
        border-bottom: 1px solid #e4e4e4;
        padding-bottom: 25px;
        font-size:22px;
        font-weight:normal;
    }
    table {
        width: 100%;
        padding: 10px;
        border-radius: 3px;
    }
    table thead th {
        text-align:left;
        padding: 5px 0px 5px 0px;
    }
    table tbody td {
        padding:5px 0px;
    }

    table tbody td:last-child,table thead th:last-child {
        text-align:right;
    }
    .label {
        padding: 3px 10px;
        border-radius:4px;
        color: #fff;

    }
    .label.label-success {
        background: #4ac700;
    }
    .label.label-warning {
        background: #dc2020;
    }
    #loader {
     position: relative;
     width: 44px;
     height: 8px;
     margin: 5px auto;
     padding-top: 35px;
     padding-bottom: 30px;
 }
 .dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 4px;
    background: #ccc;
    position: absolute;
}

.dot_1 {
    animation: animateDot1 1.5s linear infinite;
    left: 12px;
    background: #e579b8;
}

.dot_2 {
    animation: animateDot2 1.5s linear infinite;
    animation-delay: 0.5s;
    left: 24px;
}

.dot_3 {
    animation: animateDot3 1.5s linear infinite;
    left: 12px;
}

.dot_4 {
    animation: animateDot4 1.5s linear infinite;
    animation-delay: 0.5s;
    left: 24px;
}
.logo {
    margin-bottom: 35px;
    margin-top: 20px;
    display: block;
}
.logo img {
    margin:0 auto;
    display:block;
}
@keyframes animateDot1 {
    0% {
        transform: rotate(0deg) translateX(-12px);
    }
    25% {
        transform: rotate(180deg) translateX(-12px);
    }
    75% {
        transform: rotate(180deg) translateX(-12px);
    }
    100% {
        transform: rotate(360deg) translateX(-12px);
    }
}
@keyframes animateDot2 {
    0% {
        transform: rotate(0deg) translateX(-12px);
    }
    25% {
        transform: rotate(-180deg) translateX(-12px);
    }
    75% {
        transform: rotate(-180deg) translateX(-12px);
    }
    100% {
        transform: rotate(-360deg) translateX(-12px);
    }
}
@keyframes animateDot3 {
    0% {
        transform: rotate(0deg) translateX(12px);
    }
    25% {
        transform: rotate(180deg) translateX(12px);
    }
    75% {
        transform: rotate(180deg) translateX(12px);
    }
    100% {
        transform: rotate(360deg) translateX(12px);
    }
}
@keyframes animateDot4 {
    0% {
        transform: rotate(0deg) translateX(12px);
    }
    25% {
        transform: rotate(-180deg) translateX(12px);
    }
    75% {
        transform: rotate(-180deg) translateX(12px);
    }
    100% {
        transform: rotate(-360deg) translateX(12px);
    }
}

</style>
</head>
<body>
    <?php
    $error = false;
    if (version_compare(PHP_VERSION, '7.2.5') >= 0) {
        $requirement1 = "<span class='label label-success'>v." . PHP_VERSION . '</span>';
    } else {
        $error        = true;
        $requirement1 = "<span class='label label-warning'>Your PHP version is " . PHP_VERSION . '</span>';
    }

    if (!extension_loaded('mysqli')) {
        $error = true;
        $requirement2 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement2 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('pdo')) {
        $error = true;
        $requirement3 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement3 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('curl')) {
        $error = true;
        $requirement4 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement4 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('openssl')) {
        $error = true;
        $requirement5 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement5 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('mbstring')) {
        $error = true;
        $requirement6 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement6 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('iconv') && !function_exists('iconv')) {
        $error = true;
        $requirement7 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement7 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('imap')) {
        $error = true;
        $requirement8 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement8 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('gd')) {
        $error = true;
        $requirement9 = "<span class='label label-warning'>Not enabled</span>";
    } else {
        $requirement9 = "<span class='label label-success'>Enabled</span>";
    }

    if (!extension_loaded('zip')) {
        $error = true;
        $requirement10 = "<span class='label label-warning'>Zip Extension is not enabled</span>";
    } else {
        $requirement10 = "<span class='label label-success'>Enabled</span>";
    }

    $url_f_open = ini_get('allow_url_fopen');
    if ($url_f_open != '1'
        && strcasecmp($url_f_open, 'On') != 0
        && strcasecmp($url_f_open, 'true') != 0
        && strcasecmp($url_f_open, 'yes') != 0) {
        $error         = true;
        $requirement11 = "<span class='label label-danger'>Allow_url_fopen is not enabled!</span>";
    } else {
        $requirement11 = "<span class='label label-success'>Enabled</span>";
    }

    ?>
    <div id="wrapper">
        <div class="logo">
            <a href="https://www.perfexcrm.com"><img src="data:image/webp;base64,UklGRm4DAABXRUJQVlA4TGEDAAAvlUAIECfjGpJk2SqAC3QxrkmxlMYzeAaP5v3efbNw1EiSI9X4GQ557nn8Adnkca4iSbKUQgNm0MVXSahAGR4QgId9MmzbxnGMdu57Udu2DeOeLXcQ3P/r+/xNGC2EhNWGkBBhI1Q7w0ObcAiX1pB2htVWOIRD28OlhfCFo034E3athIQnYQghfFpDWgklXGG1//f4Zny9EobwaxMinNoRVmtIS0hruGlbWO0IQ9i0CWm5y0Uernqc7bjqcfRjH8tVr21On/YxtbdtLsselj0c/Xiv+Qz/8nc1DP1pPsO6hrMd61r2sWxz2cfyPnTV603SBET0PxYREdK1bU8j5VucwvhMoFBG1j1QWN9xHIK7S87/IJo32m5/R/TfgdtIiuQ+xszt0RsWym7ezY1zun3nChV+Avn7Au5ruuB2OZDfH85ts3GuFMj3VvPVcvDvpX13qxTIH4+tji+WAvnBfnSTcpqoy/cE34qL4v8rq3VDe9LFSSpB9qmA6RaF/PnE5p6hnQt9eIATXa2wUhjyo82hEzGMfPfQuMJYQsUhX1s8sKww5XxP7VaCgFesQH49Ndu1rJARwbtiqLWwbrfbxIQKYxWiKOnGWUSqGiHEGAdtDAuaqiAs+cWs74CG+hu1O4T30R6DCLXlkqyEU7IntFta45qqQO0qKoAFJt+Y9tOqA0PAouH9HaO9WEUskhAmDnGCa+mLFNrvZwab5CAS2RARU8/UZWobDCPtDYUvAecqgHOe2OMohbBEbbQoOPlV359ed1CB59Q6mkSwtATAwllFayKyx2kXDStDWEJ48i3uRy2ySfGeIsGHTSEoC1jRDppao8YhDt9JQ7gsD3+eq/1+g6y0WdHPczXwHgWLZOMQRxTtCQGX5UJ+ezQ/HVwmR3uxtlBtTLoWDnGQFENDTOF8Ns6nnc4FIgcr3S68X7WF7nFtFr0Y4+AsWAxImGdcIyfwQCZbhgov5jgybDWWE3H3vC/KMoaRL4c4vEYl7kU5ET1vK+qdBAWVRWcOcXDZFm3BRTmZ1H3Bt92Qw/8EZw5xTFVUKIIvypyI+5c8ERtqgT7scXtaHF6UE7HsiyorQ+hLuz7McdCWQk2aFSzmZdYm/2kyxnKMy4s4uEpFnAIQayVF3CgpJ9fOviO3WTr7SmYAAA=="></a>
        </div>
        <h1><a href="https://www.perfexcrm.com">Perfex CRM</a> - Server Requirements</h1>
        <div id="loader">
            <span class="dot dot_1"></span>
            <span class="dot dot_2"></span>
            <span class="dot dot_3"></span>
            <span class="dot dot_4"></span>
        </div>
        <table class="table table-hover" id="requirements" style="display:none;">
            <thead>
                <tr>
                    <th>Requirements</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>PHP >= 7.2.5</td>
                    <td><?php echo $requirement1; ?></td>
                </tr>
                <tr>
                    <td>MySQLi PHP Extension</td>
                    <td><?php echo $requirement2; ?></td>
                </tr>
                <tr>
                    <td>PDO PHP Extension</td>
                    <td><?php echo $requirement3; ?></td>
                </tr>
                <tr>
                    <td>cURL PHP Extension</td>
                    <td><?php echo $requirement4; ?></td>
                </tr>
                <tr>
                    <td>OpenSSL PHP Extension</td>
                    <td><?php echo $requirement5; ?></td>
                </tr>
                <tr>
                    <td>MBString PHP Extension</td>
                    <td><?php echo $requirement6; ?></td>
                </tr>
                <tr>
                    <td>iconv PHP Extension</td>
                    <td><?php echo $requirement7; ?></td>
                </tr>
                <tr>
                    <td>IMAP PHP Extension</td>
                    <td><?php echo $requirement8; ?></td>
                </tr>
                <tr>
                    <td>GD PHP Extension</td>
                    <td><?php echo $requirement9; ?></td>
                </tr>
                <tr>
                    <td>Zip PHP Extension</td>
                    <td><?php echo $requirement10; ?></td>
                </tr>
                <tr>
                    <td>allow_url_fopen</td>
                    <td><?php echo $requirement11; ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <br />
                        <br />
                        Additionaly you will need <b>mod_rewrite</b> enabled in your server. <br /><small>(this script unable to check if mod_rewrite extension is allowed in your server, consult with your hosting provider for this extension)</small>
                    </td>
                </tr>
            </tfoot>
        </table>
        <br />

    </div>
    <script>
        var loading = {
            complete: function () {
                var loading = document.getElementById("loader");
                loading.remove(loading);
            }
        };
        document.addEventListener("readystatechange", function () {
            if (document.readyState === "complete") {
                setTimeout(function(){
                    loading.complete();
                    var requirements = document.getElementById("requirements");
                    requirements.style['display'] = null;
                },3000);
            }
        });
    </script>
</body>
</html>

