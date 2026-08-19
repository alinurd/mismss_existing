<?php

namespace DOKU\Service;

use DOKU\Common\PaycodeGenerator;

class IndomaretO2o
{

    public static function generated($config, $params)
    {
        $params['targetPath'] = '/indomaret-online-to-offline/v2/payment-code';
        return PaycodeGenerator::post($config, $params);
    }
}
