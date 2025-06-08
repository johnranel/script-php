<?php
    for($count = 1; $count<=100; $count++) {
        echo foobar($count) . ", ";
    }
    exit("\n");

    function foobar($count) {
        if($count % 3 === 0 && $count % 5 === 0)
            return "foobar";
        if($count % 3 === 0)
            return "foo";
        if($count % 5 === 0)
            return "bar";
        return $count;
    }
?>