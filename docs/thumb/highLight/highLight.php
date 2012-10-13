<?php

class HighLight {

    public static function dump($data) {

        $code = highlight_file($data, TRUE);
        $trans = array("<code>" => '<code class ="hightLight-code">',
            //comment
            '<span style="color: #FF8000">' => '<span class ="hightLight-comment">',
            // string
            '<span style="color: #DD0000">' => '<span class ="hightLight-string">',
            // variable
            '<span style="color: #0000BB">$' => '<span class ="hightLight-variable">$',
            //keyword
            '<span style="color: #007700">' => '<span class ="hightLight-keyword">',
            //default
            '<span style="color: #0000BB">' => '<span class ="hightLight-default">',
        );


        // number
        for ($index = 0; $index < 10; $index++) {
            $trans['<span style="color: #0000BB">' . $index] = '<span class ="hightLight-number">' . $index;
        }

        // element
        $modif = array('true', 'TRUE', 'false', 'FALSE', 'null', 'NULL');
        foreach ($modif as $value) {
            $trans['<span style="color: #0000BB">' . $value] = '<span class ="hightLight-key">' . $value;
        }

        return strtr($code, $trans);
    }

}