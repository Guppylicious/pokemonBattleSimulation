<?php

namespace Game;

use League\Csv\Writer;
use League\Csv\Reader;
use League\Csv\Statment;

class Loader
{
    /**
     * Gets data of a given object
     * @param string $get The name fo the objects to get
     * @return array The found data
     */
    public function getData($get)
    {
        $file = Reader::createFromPath('csv/' . $get . '.csv', 'r');
        $file->addStreamFilter('convert.iconv.ISO-8859-1/UTF-8//TRANSLIT');
        $fileKeys = $file->fetchOne();
        $data = $file->setHeaderOffset(0)->getRecords($fileKeys);
        return $data;
    }

    /**
     * Find a given object in a dataset
     * @param string $searchable The name or number of the object to search for
     * @param string $dataName The name of the dataset to search through
     * @return array|null The found data or null if nothing found
     */
    public function find($searchable, $dataName)
    {
        $data = $this->getData($dataName);

        foreach ($data as $d) {
            if ($d['Name'] == $searchable || $d['Number'] == $searchable) {
                return $d;
            }
        }

        return null;
    }

    /**
     * Find a random object in a dataset
     * @param string $dataName The name of the dataset to search through
     * @return array The random data object
     */
    public function findRandom($dataName)
    {
        $file = Reader::createFromPath('csv/' . $dataName . '.csv', 'r');
        $file->setHeaderOffset(0);

        return $this->find(rand(1, count($file)), $dataName);
    }
}
