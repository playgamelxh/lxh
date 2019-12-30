<?php
/**
 * 长尾关键词组合
 */
$arr = array(
    '疑问词' => '哪家',
//    '价格词' => '多少钱',
    '公司企业' => '公司',
    '需求方名词' => '学校',
    '主体名词' => '网站',
    '评估词' => '大概',
    '数量词' => '建一个',
    '需求词' => '建网站',
    '口碑词' => '比较好',
//    '询问词' => '如何',
    '类型词' => '英文',
);
$keyArr = array('疑问词', '公司企业', '需求方名词', '主体名词', '评估词', '数量词', '需求词', '口碑词', '类型词');
$filter = array(
    'first' => array(
//        '询问词'
    ),
    'last' => array(
//        '价格词',
        '口碑词',
    ),
    'repel' => array(
//        array('询问词', '价格词'),
//        array('询问词', '评估词'),
//        array('疑问词', '价格词'),
//        array('价格词', '口碑词'),
    ),
);

//生成
ini_set('memory_limit', '10240M');
for($i=1; $i<=count($keyArr); $i++) {
    $resArr = getUniqueDataFromArray($keyArr, $i);
//    $resArr = getUniqueDataFromArray($keyArr, 10);
    foreach ($resArr as $value) {
        $temp = explode('+', $value);
        $temp = filter_rule($temp, $filter);
        if (is_array($temp) && !empty($temp)) {
            $str = "";
            foreach ($temp as $key) {
                $key = str_replace(array("[!--", "--]"), "", $key);
                $str .= $arr[$key];
            }
            $str .= "\t{$value}";
            echo $str,"\r\n";
        }
    }
}

//从数组中取$num个元素排列组合
function getUniqueDataFromArray($arr, $num)
{
    if ($num > count($arr)) {
        return array();
    }
    if ($num == 1) {
        $temp = array();
        foreach ($arr as $value) {
            $temp[] = "[!--{$value}--]";
        }
        return $temp;
    } else {
        $ret =  array();
        for($i= 0;$i<count($arr);$i++) {
            //遍历取一个。
            $temp = $arr;
            $one = $temp[$i];
            unset($temp[$i]);
            sort($temp);

            //剩余的递归组合
            $remain = getUniqueDataFromArray($temp, $num-1);
            foreach ($remain as $value) {
                $ret[] = "[!--{$one}--]+{$value}";
            }
        }
        return $ret;
    }
}

//规则过滤
function filter_rule($arr, $filter)
{
    //在最开头
    if(isset($filter['first']) && !empty($filter['first'])) {
        foreach ($filter['first'] as $value) {
            $value = "[!--{$value}--]";
            if (in_array($value, $arr) && $arr[0] != $value) {
                return array();
            }
        }
    }

    //在结尾
    if(isset($filter['last']) && !empty($filter['last'])) {
        foreach ($filter['last'] as $value) {
            $value = "[!--{$value}--]";
            if (in_array($value, $arr) && array_pop($arr) != $value) {
                return array();
            }
        }
    }

    //不能同时出现
    if(isset($filter['repel']) && !empty($filter['repel'])) {
        foreach ($filter['repel'] as $value) {
            $value1 = "[!--{$value[0]}--]";
            $value2 = "[!--{$value[1]}--]";
            if (in_array($value1, $arr) && in_array($value2, $arr)) {
                return array();
            }
        }
    }
    return $arr;
}
