<?php

namespace DOKU\Service;

use DOKU\Common\PaycodeGenerator;

class AlfaO2o
{

    public static function generated($config, $params)
    {
        $params['targetPath'] = '/alfa-online-to-offline/v2/payment-code';
        return PaycodeGenerator::post($config, $params);
    }
}
