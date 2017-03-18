<?php

namespace TMCms\Modules\Sessions\Entity;

use TMCms\Orm\EntityRepository;

/**
 * Class SessionCollection
 * @package TMCms\Modules\Sessions\Object
 *
 * @method $this setWhereSid(string $sid)
 * @method $this setWhereUserId(string $sid)
 *
 * @method $this setTs(int $ts)
 */
class SessionEntityRepository extends EntityRepository
{
    protected $db_table = 'm_sessions';

    protected $table_structure = [
        'fields' => [
            'user_id' => [
                'type' => 'index',
            ],
            'sid' => [
                'type' => 'char',
                'length' => 32,
            ],
            'ip' => [
                'type' => 'varchar',
                'length' => 15,
            ],
            'ts' => [
                'type' => 'int',
                'unsigned' => true,
            ],
            'agent' => [
                'type' => 'varchar',
            ],
            'data' => [
                'type' => 'text',
            ],
        ],
    ];
}