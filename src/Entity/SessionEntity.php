<?php

namespace neTpyceB\TMCms\Modules\Sessions\Entity;

use neTpyceB\TMCms\Orm\Entity;

/**
 * Class Session
 * @package neTpyceB\TMCms\Modules\Sessions\Object
 *
 * @method string getData()
 * @method int getTs()
 * @method setAgent(string $agent)
 * @method setData(string $data)
 * @method setIp(string $ip)
 * @method setSid(string $sid)
 * @method setTs(int $ts)
 * @method setUserId(int $user_id)
 */
class SessionEntity extends Entity
{
    protected $db_table = 'm_sessions';

    protected function beforeCreate()
    {
        $this->setIp(IP);
        $this->setTs(NOW);
        $this->setAgent(USER_AGENT);

        parent::beforeCreate();
    }
}