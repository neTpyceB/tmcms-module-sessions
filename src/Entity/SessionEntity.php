<?php

namespace TMCms\Modules\Sessions\Entity;

use neTpyceB\TMCms\Orm\Entity;

/**
 * Class Session
 * @package TMCms\Modules\Sessions\Object
 *
 * @method string getData()
 * @method int getTs()
 * @method int getUserId()
 * @method $this setAgent(string $agent)
 * @method $this setData(string $data)
 * @method $this setIp(string $ip)
 * @method $this setSid(string $sid)
 * @method $this setTs(int $ts)
 * @method $this setUserId(int $user_id)
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