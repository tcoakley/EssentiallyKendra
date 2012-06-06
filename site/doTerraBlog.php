<?php

$xml = null;
try {
    $xml = simplexml_load_file('http://doterrablog.com/feed');
//    $json = json_encode($xml);
//    print $json;
} catch(Exception $e) {
    
}
if (!is_null($xml)) {

    $values = toArray($xml);
    foreach ($values['channel']['item'] as $item) {
        print '$item: ';
        var_dump($item);
        print "<br><hr><br>"; 
    }
}

function toArray(SimpleXMLElement $xml) {
    $array = (array)$xml;

    foreach ( array_slice($array, 0) as $key => $value ) {
        if ( $value instanceof SimpleXMLElement ) {
            $array[$key] = empty($value) ? NULL : toArray($value);
        }
    }
    return $array;
}
