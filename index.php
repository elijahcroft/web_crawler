<?php

$start = "http://localhost/test_crawl/test.html";

$already_crawled = array();
$crawling = array();

function get_details($url) {
    $options = array('http' => array(
        'method' => "GET",
        'headers' => "User-agent: howBot/0.1\r\n"
    ));

    $context = stream_context_create($options);

    $doc = new DOMDocument();
    $htmlContent = @file_get_contents($url, false, $context);

  
    if ($htmlContent === false || empty($htmlContent)) {
        echo "Failed to fetch content for URL: $url\n";
        return null;
    }

    @$doc->loadHTML($htmlContent);

    // Extract title
    $titleNode = $doc->getElementsByTagName("title");
    $title = $titleNode->length > 0 ? $titleNode->item(0)->nodeValue : "No title found";

    
    $description = "No description found";
    $keywords = "No keywords found";

    $metas = $doc->getElementsByTagName("meta");
    for ($i = 0; $i < $metas->length; $i++) {
        $meta = $metas->item($i);

        if (strtolower($meta->getAttribute("name")) == "description") {
            $description = $meta->getAttribute("content");
        }
        if (strtolower($meta->getAttribute("name")) == "keywords") {
            $keywords = $meta->getAttribute("content");
        }
    }

    // Return metadata in JSON format
    return json_encode([
        "Title" => str_replace("\n","",$title),
        "Description" => str_replace("\n", "", $description),
        "Keywords" => str_replace("\n","",$keywords) ,
        "URL" => $url
        
    ],JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

function follow_links($url) {
    global $already_crawled;
    global $crawling;

    $options = array('http' => array(
        'method' => "GET",
        'headers' => "User-agent: howBot/0.1\r\n"
    ));

    $context = stream_context_create($options);

    $doc = new DOMDocument();
    $htmlContent = @file_get_contents($url, false, $context);

    
    if ($htmlContent === false || empty($htmlContent)) {
        echo "Failed to fetch or empty content for URL: $url\n";
        return;
    }

    @$doc->loadHTML($htmlContent);

    $linklist = $doc->getElementsByTagName("a");

    foreach ($linklist as $link) {
        $l = $link->getAttribute("href");

        $parsedUrl = parse_url($url);

        
        if (substr($l, 0, 1) == "/" && substr($l, 0, 2) != "//") {
            $l = $parsedUrl["scheme"] . "://" . $parsedUrl["host"] . $l;
        } else if (substr($l, 0, 2) == "//") {
            $l = $parsedUrl["scheme"] . ":" . $l;
        } else if (substr($l, 0, 2) == "./") {
            $l = $parsedUrl["scheme"] . "://" . $parsedUrl["host"] . dirname($parsedUrl["path"]) . "/" . substr($l, 2);
        } else if (substr($l, 0, 1) == "#") {
            $l = $parsedUrl["scheme"] . "://" . $parsedUrl["host"] . $parsedUrl["path"] . $l;
        } else if (substr($l, 0, 3) == "../") {
            $l = $parsedUrl["scheme"] . "://" . $parsedUrl["host"] . "/" . $l;
        } else if (substr($l, 0, 11) == "javascript:") {
            continue; // Skip JavaScript links
        } else if (substr($l, 0, 5) != "https" && substr($l, 0, 4) != "http") {
            $l = $parsedUrl["scheme"] . "://" . $parsedUrl["host"] . "/" . $l;
        }

        
        if (!in_array($l, $already_crawled)) {
            $already_crawled[] = $l;
            $crawling[] = $l;

            $details = get_details($l);
            if ($details) {
                echo $details . "\r\n";
            }

            
            
        }
    }

    array_shift($crawling);
    foreach ($crawling as $site){
        follow_links($site);
    }
}


follow_links($start);


print_r($already_crawled);
