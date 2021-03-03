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

use Laemmi\SyncTools\Service\ServiceTrait;

class Mysql
{
    use ServiceTrait;

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
    protected int $port = 3306;

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
     * @var string
     */
    protected string $ssh;

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @param string $database
     */
    public function setDatabase(string $database): void
    {
        $this->database = $database;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return array|string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array|string[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @return mixed
     */
    public function getSsh(): string
    {
        return $this->ssh;
    }

    /**
     * @param string $ssh_user
     * @param string $ssh_host
     * @param int $ssh_port
     */
    public function setSsh(string $ssh_user, string $ssh_host, int $ssh_port): void
    {
        $this->ssh = sprintf('ssh -p %3$d %1$s@%2$s', $ssh_user, $ssh_host, $ssh_port);
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
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Execute
     * @return string
     */
    public function execute(): string
    {
        $mysqldump = array_merge(
            ['"mysqldump'],
            $this->attributes,
            [
                sprintf('--host=\"%s\"', $this->getHost()),
                sprintf('--user=\"%s\"', $this->getUser()),
                sprintf('--password=\"%s\"', $this->getPassword()),
                sprintf('--port=%s', $this->getPort()),
            ],
            [$this->getDatabase() . '"']
        );

        $exec = implode(' ', array_filter(array_merge(
            [$this->getSsh()],
            $mysqldump,
            [
                '| sed -e \'s/DEFINER[ ]*=[ ]*[^*]*\*/\*/\'',
                '| gzip -9'
            ],
            [sprintf('> %s', $this->getPath())]
        )));

        return $this->executeCommand($exec);
    }
}
