<?php

$data = array(
    array('data1', 'data2'),
    array('data3', 'data4')
);

Excel::create('Filename', function($excel) use($data) {

    $excel->sheet('Sheetname', function($sheet) use($data) {

        $sheet->fromArray($data);

    });

})->export('xls');
