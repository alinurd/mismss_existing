<?php

namespace DOKU;

use DOKU\Service\VirtualAccount;

use DOKU\Service\MandiriVa;
use DOKU\Service\DokuVa;
use DOKU\Service\BcaVa;
use DOKU\Service\BniVa;
use DOKU\Service\BriVa;
use DOKU\Service\BsiVa;
use DOKU\Service\CimbVa;
use DOKU\Service\DanamonVa;
use DOKU\Service\PermataVa;
use DOKU\Service\AlfaO2o;
use DOKU\Service\IndomaretO2o;

class Client
{
    /**
     * @var array
     */
    private $config = array();

    public function isProduction($value)
    {
        $this->config['environment'] = $value;
    }

    public function setClientID($clientID)
    {
        $this->config['client_id'] = $clientID;
    }

    public function setSharedKey($key)
    {
        $this->config['shared_key'] = $key;
    }

    public function getConfig()
    {
        return $this->config;
    }

    //####################Virtual Account######################
    public function generateMandiriVa($params)
    {
        $this->config = $this->getConfig();
        return MandiriVa::generated($this->config, $params);
    }

    public function generateDokuVa($params)
    {
        $this->config = $this->getConfig();
        return DokuVa::generated($this->config, $params);
    }

    public function generateBcaVa($params)
    {
        $this->config = $this->getConfig();
        return BcaVa::generated($this->config, $params);
    }

    public function generateBsiVa($params)
    {
        $this->config = $this->getConfig();
        return BsiVa::generated($this->config, $params);
    }

    public function generateBriVa($params)
    {
        $this->config = $this->getConfig();
        return BriVa::generated($this->config, $params);
    }

    public function generatePermataVa($params)
    {
        $this->config = $this->getConfig();
        return PermataVa::generated($this->config, $params);
    }

    public function generateCimbVa($params)
    {
        $this->config = $this->getConfig();
        return CimbVa::generated($this->config, $params);
    }

    public function generateDanamonVa($params)
    {
        $this->config = $this->getConfig();
        return DanamonVa::generated($this->config, $params);
    }

    public function generateBniVa($params)
    {
        $this->config = $this->getConfig();
        return BniVa::generated($this->config, $params);
    }

    //####################Online 2 Offline######################
    public function generateAlfaO2o($params)
    {
        $this->config = $this->getConfig();
        return AlfaO2o::generated($this->config, $params);
    }
    public function generateIndomaretO2o($params)
    {
        $this->config = $this->getConfig();
        return IndomaretO2o::generated($this->config, $params);
    }


}
