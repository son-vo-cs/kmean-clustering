<?php
    
    function calSpace($item)
    {
        $space = 20;
        $max_align = 4;
        return $space + ($max_align - strlen($item))*2;
    }
    function display($arr)
    {
        $space = 20;
        $max_align = 4;
        arsort($arr);
        echo "<br><br>Score".str_repeat("&nbsp;",$space)."Cluster<br>";
        foreach ($arr as $key => $val)
        {
            $val_temp = sprintf("%.2f",$val);
            $space = calSpace($key);
            echo $key .str_repeat("&nbsp;",$space). $val_temp . "<br>";
        }
        echo "<br>";
    }

    function convert_to_arr($str)
    {
        $arr = explode(' ', $str);
        $result = array_map('floatval', array_filter($arr, 'is_numeric'));
        return array_unique($result);    
    }

    
    function get_random_element($arr, $num)
    {
        if ($num == 1)
        {
            return $arr;
        }
        if (count($arr) < $num)
        {
            return [];
        }
        $indexes = array_rand($arr, $num);
        
        $result = array();
        foreach ($indexes as $i)
        {
            array_push($result, $arr[$i]);
        }
        
        return $result;
    }

    function decide_cluster($clusters, $num)
    {
        $smallest = abs($clusters[0] - $num);
        $cluster = 0;
        for ($i = 1; $i < count($clusters); $i++)
        {
            $temp = abs($clusters[$i] - $num);
            if ($temp < $smallest)
            {
                $smallest = $temp;
                $cluster = $i;
            }
        }
        return $clusters[$cluster];
    }

    function recal_clusters($arr)
    {
        arsort($arr);
        $result = array();
        $keys = array_keys($arr);
        $temp = $arr[$keys[0]];
        $count = 0;
        $sum = 0.0;
        foreach ($arr as $key => $val)
        {
            if ($val === $temp)
            {
                $count++;
                $sum = $sum + $key;
            }
            else
            {
                array_push($result,$sum/$count);
                $count = 1;
                $sum = 0.0 + $key;
                $temp = $val;
                
            }
        }
        array_push($result,$sum/$count);
        return $result;
    }

    function isValid($str, $cluster_num)
    {
        if ($cluster_num < 1)
        {
            return "The number of cluster must be greater than 0<br>";
        }
        if (!(preg_match('/^[0-9 ]+(\.[0-9 ]+)?$/', $str)))
        {
            return "Error. Make sure the input contains only numbers, and your numbers must be sperated by a space<br>";
        }
        $arr = convert_to_arr($str);
        if (count($arr) < $cluster_num)
        {
            return "Error. The number of clusters is greater than the number of unique values in the input<br>";
        }
        return "";
    }

    function average($arr)
    {
        return array_sum($arr)/count($arr);
    }
    function kmean($str, $cluster_num)
    {
        $original_arr = convert_to_arr($str);
        if ($cluster_num == 1)
        {
            return array_fill_keys($original_arr,average($original_arr));
        }
        $clusters = get_random_element($original_arr, $cluster_num);
        $arr_key = array_fill_keys($original_arr, 0);
        $again = true;
        $count = 0;
        while ($again === true)
        {
            $again = false;
            foreach ($arr_key as $score => $cluster)
            {
                $new_cluster = decide_cluster($clusters, $score);
                if ($new_cluster !== $cluster)
                {
                    $again = true;
                    $arr_key[$score] = $new_cluster;
                }
            }
            if ($again === true)
            {
                $clusters = recal_clusters($arr_key);
            }
            $count++;
        }
        
        return $arr_key;
        
    }

    
    
?>