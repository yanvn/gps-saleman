<?php

define('CITIES_FILENAME', 'cities.txt');

require_once('solve.php');

class CityFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp(){ }
    public function tearDown(){ }

    /**
     * [testFailingImport description]
     * @author vothaianh
     * @date   2017-05-27T15:05:31+070
     * @return [type]                  [description]
     */
    public function testFailingImport() {
        $content = file_get_contents(CITIES_FILENAME);
        $this->assertTrue(!empty($content) !== false);
    }

    /**
     * [testImportCitiesIsValid description]
     * @author vothaianh
     * @date   2017-05-27T15:05:28+070
     * @return [type]                  [description]
     */
    public function testImportCitiesIsValid() {

        $CityMethod = new CityFactory;

        $this->assertTrue($CityMethod->createCity(CITIES_FILENAME) !== false);

        $this->assertTrue(is_array($CityMethod->cities) !== false);

        $row = array_shift($CityMethod->source);

        preg_match('/([\p{L}\s]+)\s+([\-0-9.]+)\s+([\-0-9.]+)|/u', $row, $output);

        $this->assertTrue(count($output) == 4);
    }

    /**
     * [testTraceRoute description]
     * @author vothaianh
     * @date   2017-05-27T15:05:35+070
     * @return [type]                  [description]
     */
    public function testTraceRoute() {

        $CityMethod = new CityFactory;

        $CityMethod->createCity(CITIES_FILENAME);

        $CityMethod->sort();

        $CityMethod->find('Beijing')->traceRoute();

        $this->assertTrue(is_array($CityMethod->result) !== false);

        $this->assertTrue(!empty($CityMethod->score) !== false);

    }

    public function testResult() {

        $CityMethod = new CityFactory;

        $CityMethod->createCity(CITIES_FILENAME);

        $CityMethod->sort();

        $CityMethod->find('Beijing')->traceRoute();

        foreach($CityMethod->source as $row) {
            if (!empty(trim($row))) $a++;
        }

        $b = count($CityMethod->data);

        $this->assertTrue($a == $b);

    }


}
