<?php

/* 
- https://github.com/ofcdeex
*/


function read($start, $end, $string)
{
    $a = explode($start, $string)[1];
    $b = explode($end, $a);
    return $b[0];
}

function sendRequest($config)
{

    if ($config['type'] == "follow") {
        $payload = '{"input":{"attribution_id_v2":"ProfileCometTimelineListViewRoot.react,comet.profile.timeline.list,via_cold_start,1675060340444,115651,190055527696468,","is_tracking_encrypted":false,"subscribe_location":"PROFILE","subscribee_id":"' . $config['pageid'] . '","tracking":null,"actor_id":"' . $config['c_user'] . '","client_mutation_id":"1"},"scale":1}';
        $doc_id = "5032256523527306";
    } else {
        $payload = '{"input":{"attribution_id_v2":"CometSinglePageHomeRoot.react,comet.page,tap_search_bar,1675057224789,689882,250100865708545,","is_tracking_encrypted":true,"page_id":"' . $config['pageid'] . '","source":"unknown","tracking":[],"actor_id":"' . $config['c_user'] . '","client_mutation_id":"4"},"isAdminView":false}';
        $doc_id = "5491200487600992";
    }

    $hash = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'cookie: xs=' . $config['xs'],
                'cookie: c_user=' . $config['c_user'],
                'content-type: application/x-www-form-urlencoded',
                'origin: https://www.facebook.com',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'sec-gpc: 1'
            ]
        ]
    ]);


    $gHash = file_get_contents('https://m.facebook.com/?tbua=1', false, $hash);

    $protectToken = read('<input type="hidden" name="fb_dtsg" value="', '"', $gHash);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'cookie: xs=' . $config['xs'],
                'cookie: c_user=' . $config['c_user'],
                'content-type: application/x-www-form-urlencoded',
                'origin: https://www.facebook.com',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'sec-gpc: 1'
            ],
            'content' => http_build_query([
                'variables' => $payload,
                'doc_id' => $doc_id,
                'fb_dtsg' => $protectToken
            ])
        ]
    ]);

    return file_get_contents("https://www.facebook.com/api/graphql/", false, $context);
}


// Setup

$config = [
    'xs' => '', // COOKIE (XS)
    'c_user' => '', // COOKIE (C_USER)
    'pageid' => '', // ID PAGE TARGET
    'type' => 'pagelike' // Type: pagelike LIKE IN PAGE | follow PROFILE FOLLOW
];


print_r(sendRequest($config));