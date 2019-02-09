<?php
/**
 * Created by PhpStorm.
 * User: veronique
 * Date: 09/02/19
 * Time: 17:12
 */

namespace AppBundle\Service;


class YearPaquetageService
{

    public function getYearPaquetage()
    {
        $yearPaquetage = '';
        $yearPaquetageOld = '';
        $date = new \DateTime();
        $dateMonth = $date->format('m');
        $year = $date->format('Y');
        if (in_array($dateMonth,[7,8,9,10,11,12])){
            $yearPaquetage = intval($year) + 1;
            $yearPaquetageOld = intval($year);
        } else if (in_array($dateMonth,[1,2,3,4,5,6])){
            $yearPaquetage = intval($year);
            $yearPaquetageOld = intval($year)-1;
        }
        return [$yearPaquetage,$yearPaquetageOld];
    }

}