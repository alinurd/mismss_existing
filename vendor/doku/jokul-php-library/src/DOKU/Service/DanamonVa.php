<?php

namespace DOKU\Service;

use DOKU\Common\PaycodeGenerator;

class DanamonVa
{

    public static function generated($config, $params)
    {
        $params['targetPath'] = '/danamon-virtual-account/v2/payment-code';
        return PaycodeGenerator::post($config, $params);
    }
}
