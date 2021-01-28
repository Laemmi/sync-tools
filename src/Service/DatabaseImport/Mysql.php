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

namespace Laemmi\SyncTools\Service\DatabaseImport;

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
    ];

    /**
     * @var string
     */
    protected string $path;

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
     * @param array $attribute
     */
    public function addAttributes(array $attribute)
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
     * Execute
     */
    public function execute()
    {
        $mysql = array_merge(
            ['mysql'],
            $this->attributes,
            [
                sprintf('--host=\"%s\"', $this->host),
                sprintf('--user=\"%s\"', $this->user),
                sprintf('--password=\"%s\"', $this->password),
                sprintf('--port=%s', $this->port),
            ],
            [$this->database]
        );

        $dump = sprintf('%s/%s.sql.gz', $this->path, $this->database);

        $exec = array_filter(array_merge(
            [sprintf('gunzip < %s |', $dump)],
            $mysql
        ));

        exec(implode(' ', $exec));
    }
}
