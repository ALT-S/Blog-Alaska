<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 13/02/2017
 * Time: 14:17
 */

namespace ALT\AppBundle\Service;


class BilletService
{
    private $billets;

    public function __construct()
    {
        $this->adverts = array(array());
    }

    public function getAll()
    {
        return $this->$billets;
    }

    public function getOne($id)
    {
        foreach ($this->billets as $billet) {
            if ($billet['id'] == $id) {
                return $billet;
            }
        }

        return null;
    }

    public function getLast($max)
    {

        $billets = array_reverse($this->billets);

        $output = array();
        for ($i=0; $i < $max; $i++) {
            if (!isset($billets[$i])) {
                break;
            }

            $output[] = $billets[$i];
        }

        return $output;
    }
}
