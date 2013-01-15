<?php

namespace Zerebral\BusinessBundle\Model\Notification;

use Zerebral\BusinessBundle\Model\Notification\om\BaseNotification;

class Notification extends BaseNotification
{
    public function preInsert(\PropelPDO $con = null)
    {
        $this->setCreatedAt(date("Y-m-d H:i:s", time()));
        return parent::preInsert($con);
    }

    public function getParams()
    {
        if (empty($this->params)) {
            return array();
        }

        return json_decode($this->params, true);
    }

    public function setParams($params)
    {
        parent::setParams(json_encode($params));
    }

    public function setParam($name, $value)
    {
        $params = $this->getParams();
        $params[$name] = $value;
        $this->setParams($params);
    }

    public function getParam($name)
    {
        $params = $this->getParams();
        return isset($params[$name]) ? $params[$name] : null;
    }
}
