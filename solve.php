<?php


interface Point {
    public function add($param);
    public function get($title);
}

abstract class CityMethod {
    abstract function createCity($param);
    abstract function sort($args);
    abstract function find($name, $option);
    abstract function region($region, $city);
    abstract function traceRoute();
}

class City implements Point {

    var $name   = null;
    var $x      = 0;
    var $y      = 0;

    /**
     * [__construct description]
     * @author vothaianh
     * @date   2017-05-26T12:57:09+070
     * @param  [type]                  $args [description]
     */
    function __construct($args) {
        $this->name = $args['name'];
        $this->y    = $args['y'];
        $this->x    = $args['x'];

    }

    /**
     * [add description]
     * @author vothaianh
     * @date   2017-05-26T13:23:43+070
     * @param  [type]                  $param [description]
     */
    public function add($args = []) {
        foreach($args as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * [getTitle description]
     * @author vothaianh
     * @date   2017-05-26T13:02:21+070
     * @return [type]                  [description]
     */
    public function get($title) {
        return $this->$title;
    }
}

class CityFactory extends CityMethod {

    var $source = null;
    var $cities = [];
    var $region = [];
    var $sort   = array(
        ['NE', 'NW', 'SW', 'SE'],
        ['NW', 'SW', 'SE', 'NE'],
        ['SW', 'NW', 'NE', 'SE'],
        ['SE', 'NE', 'NW', 'SW'],
    );
    var $result = [];
    var $search = '';

    /**
     * [createCity description]
     * @author vothaianh
     * @date   2017-05-26T12:47:01+070
     * @param  [type]                  $name     [description]
     * @param  [type]                  $position [description]
     * @return [type]                            [description]
     */
    function createCity($param) {

        if ($param && is_file($param)) {

            $source = file_get_contents($param);
            $source = explode("\n", $source);
            $this->source = $source;

            foreach($source as $row) {
                if (!empty($row)) {
                    $i++;
                    preg_match('/([\p{L}\s]+)\s+([\-0-9.]+)\s+([\-0-9.]+)|/u', $row, $output);
                    list ($matched, $name, $y, $x) = $output;
                    $city = new City(compact('name', 'x', 'y'));
                    $this->cities[$name] = $city;
                }
            }

        }

    }

    /**
     * [region description]
     * @author vothaianh
     * @date   2017-05-26T13:20:00+070
     * @param  [type]                  $region [description]
     * @param  [type]                  $city   [description]
     * @return [type]                          [description]
     */
    public function region($region, $city) {
        $this->region[$region][] = $city;
        $city->add(compact('region'));
    }

    /**
     * [find description]
     * @author vothaianh
     * @date   2017-05-26T13:28:37+070
     * @param  [type]                  $name [description]
     * @return [type]                        [description]
     */
    public function find($name, $option = null) {

        switch ($option) {
            case 'city':
                return $this->cities[$name];
            default:
                $this->search = $name;
                return $this;
        }

    }

    /**
     * [traceRoute description]
     * @author vothaianh
     * @date   2017-05-26T13:29:38+070
     * @return [type]                  [description]
     */
    public function traceRoute() {

        if (empty($city)) {
            $city = $this->find($this->search, 'city');
            $this->trace[] = $city;
        }

        foreach($this->sort as $sorted) {
            list($r) = $sorted;
            if ($r == $city->get('region')) $regiones = $sorted;
        }

        foreach($regiones as $k => $r) {

            $cities = $this->region[$r]; 

            foreach($cities as $cty) {

                $i++;

                $trace  = null;

                foreach($cities as $cty) {
                    if ($this->findRoute($cty->name)) continue;
                    $long = ($cty->get('x') - $city->get('x')) + ($cty->get('y') - $city->get('y'));

                    $x1 = $city->get('x');
                    $x2 = $cty->get('x');

                    $y1 = $city->get('y');
                    $y2 = $cty->get('y');

                    $ab = $x1 > $x2 ? $x1 - $x2 : $x2 - $x1;
                    $ac = $y1 > $y2 ? $y1 - $y2 : $y2 - $y1;
                    $bc = sqrt($ab * $ab + $ac * $ac);

                    $trace[$cty->name] = $bc;

                }

                if ($i == count($cities)) continue;

                asort($trace);

                foreach($trace as $search => $tracer) {
                    if ($this->findRoute($search)) continue;
                    else {
                        $city = $this->find($search, 'city');
                        $this->trace[] = $city;
                        break;
                    }
                }

            }

        }

        return $this->trace;
    }

    /**
     * [findRoute description]
     * @author vothaianh
     * @date   2017-05-26T23:18:19+070
     * @param  [type]                  $search [description]
     * @return [type]                          [description]
     */
    public function findRoute($search) {
        if (is_array($this->trace)) {
            foreach($this->trace as $city) {
                if ($city->name == $search) return true;
            }
        }
    }

    /**
     * [out description]
     * @author vothaianh
     * @date   2017-05-26T13:29:54+070
     * @return [type]                  [description]
     */
    public function out($result) {
        echo '<pre>';
        if ($result) {
            foreach($result as $city) {
                echo $city->name.'<br>';
            }
        }
        else
            echo "There's nothing to show.";
    }


    /**
     * [sort description]
     * @author vothaianh
     * @date   2017-05-26T13:07:06+070
     * @param  array                   $args [description]
     * @return [type]                        [description]
     */
    public function sort($args = null) {

        foreach($this->cities as $city) {
            if ($city->get('x') > 0 && $city->get('y') > 0) $this->region('NE', $city);
            if ($city->get('x') > 0 && $city->get('y') < 0) $this->region('SE', $city);
            if ($city->get('x') < 0 && $city->get('y') > 0) $this->region('NW', $city);
            if ($city->get('x') < 0 && $city->get('y') < 0) $this->region('SW', $city);
        }

    }
}

function test($context) {
    print '<pre>';
    print_r($context);
    exit;
}


$cities = new CityFactory;

$cities->createCity('cities.txt');
$cities->sort();

$result = $cities->find('Beijing')->traceRoute();

$cities->out($result);
