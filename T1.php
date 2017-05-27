<?php
/* ЗАДАЧА 1
 *
 * Дано:
 * 	- Текст из *.csv файла
 * Необходимо:
 * 	1. Распарсить текст, подготовить данные к работе (элемент = тип Объект)
 * 	2. Отсортировать данные по дате затем КБК и вывести в таблице,
 *  таким образом, что если существует несколько записей на одну дату с одним КБК,
 * то в поле %% считать среднее, а в скобках вывести кол-во елементов.
 *
 *  Пример Табл.:
 *  | ДАТА       | КБК      | Адрес             | %%      |
 *  | 11.01.2013 | 1-01-001 | Спб, Восстания, 1 | 84% (2) |
 *
 */
require_once 'Table.php';
$data = "
02-01-2013;1-01-001;Спб, Восстания, 1;95
05-01-2013;1-02-011;Спб, Савушкина, 106;87
01-01-2013;1-01-003;Спб, Обводный канал, 12 ;92
06-02-2013;2-05-245;Ростов-на-Дону, Стачек, 41;79
12-01-2012;5-10-002;Новосибирск, Ленина, 105;75
01-01-2013;1-01-003;Спб, Обводный канал, 12 ;98
03-01-2013;6-30-855;Сочи, Коммунистическая, 2;84
05-01-2013;2-04-015;Ростов-на-Дону, Пушкинская, 102;71
07-01-2013;6-01-010;Сочи, Приморская, 26;62
05-01-2013;1-02-011;Спб, Савушкина, 106;89
01-01-2013;1-01-003;Спб, Обводный канал, 12 ;57
";

$lines = explode(PHP_EOL, $data);
$array = array();

foreach ($lines as $line) {
    if (!empty($line)) {
        //$array[][] = str_getcsv($line, ';'); использовать для не ассоциативного массива
        $association =  array(
            'ДАТА' =>  str_getcsv($line, ';')[0],
            'КБК' => str_getcsv($line, ';')[1],
            'Адрес' => str_getcsv($line, ';')[2],
            '%%' => str_getcsv($line, ';')[3],
            'count_%%' => 1
        );
        if (empty($array)) {
            $array[] = $association;
        } else {
            $key = 'none';
            foreach ($array as $k=>$item) {
                //print $k;
                if (($item['КБК'] == $association['КБК']) and ($item['ДАТА'] == $association['ДАТА'])) {
                    $key = $k;
                } else {

                }
            }
            if ($key == 'none') {
                $array[] = $association;
            } else {
                $pp = $array[$key]['%%'] + $association['%%'];
                $pp_count = $array[$key]['count_%%'] += 1;
                $array[$key]['%%'] = $pp;
                $array[$key]['count_%%'] = $pp_count;
                $key = 'none';
            }
        }
    }
}

$kbk = array();
$date = array();

foreach ($array as $key => $row) {

    $date[$key] = strtotime($row['ДАТА']);
    $kbk[$key]  = intval(str_replace('-','',$row['КБК']));
}

array_multisort($date, SORT_ASC, $kbk, SORT_ASC, $array);


$tbl = new Console_Table();
$tbl->setHeaders(
    array('ДАТА', 'КБК', 'Адрес', '%%')
);

foreach ($array as $item) {
    $pp_format = $item['%%'];
    if ($item['%%']>1) $pp_format = $pp_format.' ('.$item['count_%%'].')';
    $tbl->addRow(array($item['ДАТА'], $item['КБК'], $item['Адрес'], $pp_format));
}
echo $tbl->getTable();

?>