<?php

/**
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package    sync-tools
 * @author     Michael Lämmlein <laemmi@spacerabbit.de>
 * @copyright  ©2021 Spacerabbit
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0.0
 * @since      27.01.21
 */

declare(strict_types=1);

namespace Laemmi\SyncTools\Service\DatabaseDump;

class Mysql
{
    /**
     * @var string
     */
    protected string $host;

    /**
     * @var string
     */
    protected string $user;

    /**
     * @var string
     */
    protected string $password;

    /**
     * @var string
     */
    protected string $database;

    /**
     * @var int
     */
    protected int $port;

    /**
     * @var array|string[]
     */
    protected array $attributes = [
        '--verbose',
        '--single-transaction',
        '--skip-lock-tables',
    ];

    /**
     * @var string
     */
    protected string $path;

    /**
     * @var
     */
    protected string $ssh;

    /**
     * Mysql constructor.
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param int $port
     */
    public function __construct(
        string $host,
        string $user,
        string $password,
        string $database,
        int $port = 3306
    ) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
    }

    /**
     * @param string $attribute
     */
    public function addAttribute(string $attribute)
    {
        $this->attributes[] = $attribute;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param string $ssh_user
     * @param string $ssh_host
     */
    public function setSshConnection(string $ssh_user, string $ssh_host)
    {
        $this->ssh = sprintf('ssh %s@%s', $ssh_user, $ssh_host);
    }

    /**
     * Execute
     */
    public function execute()
    {
        $mysqldump = array_merge(
            ['"mysqldump'],
            $this->attributes,
            [
                sprintf('--host=\"%s\"', $this->host),
                sprintf('--user=\"%s\"', $this->user),
                sprintf('--password=\"%s\"', $this->password),
                sprintf('--port=%s', $this->port),
            ],
            [$this->database . '"']
        );

        $exec = implode(' ', array_filter(array_merge(
            [$this->ssh],
            $mysqldump,
            [
                '| sed -e \'s/DEFINER[ ]*=[ ]*[^*]*\*/\*/\'',
                '| gzip -9'
            ],
            [sprintf('> %s/%s.sql.gz', $this->path, $this->database)]
        )));

        passthru($exec);
    }
}
