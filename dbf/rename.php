<?php
function maxValueInArray($array, $keyToSearch)
{
    $currentMax = NULL;
    foreach($array as $arr)
    {
        foreach($arr as $key => $value)
        {
            if ($key == $keyToSearch && ($value >= $currentMax))
            {
                $currentMax = $value;
            }
        }
    }

    return $currentMax;
}

$dir=opendir("./files");
$i=0;
while (($file = readdir($dir)) !== false) {
		if ($file=='.' OR $file=='..') continue;
		$date_string=substr($file, 7, 8);
		$date_string=substr_replace($date_string, '-', 2, 0);
		$date_string=substr_replace($date_string, '-', 5, 0);
		$date=strtotime($date_string);
		$arr[$i][filename]=$file;
		$arr[$i][fdate]=$date;
		$i++;
	}

$max_elem = maxValueInArray($arr, 'fdate');
for ($i=0; $i<count($arr); $i++) {
	if ($arr[$i][fdate]==$max_elem) {
		$max_fname = $arr[$i][filename];
	}
}

echo ("<b>last_report:</b> $max_fname<br>");
$shop_name=substr($max_fname, 4,2);
$dir=opendir("./files");
while (($file = readdir($dir)) !== false) {
		if ($file=='.' OR $file=='..') continue;
		$new_name = substr_replace($file, $shop_name, 4,2);
		rename ("./files/$file", "./files/$new_name");
	}
?>
