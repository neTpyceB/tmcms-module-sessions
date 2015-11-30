<?php

namespace neTpyceB\TMCms\Modules\Sessions\Entity;

use neTpyceB\TMCms\Orm\EntityRepository;

/**
 * Class SessionCollection
 * @package neTpyceB\TMCms\Modules\Sessions\Object
 *
 * @method setWhereSid(string $sid)
 * @method setTs(int $ts)
 */
class SessionEntityRepository extends EntityRepository
{
    protected $db_table = 'm_sessions';
}