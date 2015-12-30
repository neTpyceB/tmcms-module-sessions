<?php

namespace TMCms\Modules\Sessions\Entity;

use TMCms\Orm\EntityRepository;

/**
 * Class SessionCollection
 * @package TMCms\Modules\Sessions\Object
 *
 * @method setWhereSid(string $sid)
 * @method setTs(int $ts)
 */
class SessionEntityRepository extends EntityRepository
{
    protected $db_table = 'm_sessions';
}