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
 * @since      28.01.21
 */

declare(strict_types=1);

namespace Laemmi\SyncTools\Service\FileSync;

class Rsync
{
    /**
     * @var array|string[]
     */
    protected array $attributes = [
        '--archive',
        '--compress',
        '--delete',
        '--progress',
        '--verbose',
    ];

    /**
     * @var string
     */
    protected string $src_ssh_path;

    /**
     * @var string
     */
    protected string $src_ssh_user;

    /**
     * @var string
     */
    protected string $src_ssh_host;

    /**
     * @var string
     */
    protected string $dest_path;

    /**
     * @return string
     */
    public function getSrcSshPath(): string
    {
        return $this->src_ssh_path;
    }

    /**
     * @param string $src_ssh_path
     */
    public function setSrcSshPath(string $src_ssh_path): void
    {
        $this->src_ssh_path = $src_ssh_path;
    }

    /**
     * @return string
     */
    public function getSrcSshUser(): string
    {
        return $this->src_ssh_user;
    }

    /**
     * @param string $src_ssh_user
     */
    public function setSrcSshUser(string $src_ssh_user): void
    {
        $this->src_ssh_user = $src_ssh_user;
    }

    /**
     * @return string
     */
    public function getSrcSshHost(): string
    {
        return $this->src_ssh_host;
    }

    /**
     * @param string $src_ssh_host
     */
    public function setSrcSshHost(string $src_ssh_host): void
    {
        $this->src_ssh_host = $src_ssh_host;
    }

    /**
     * @return string
     */
    public function getDestPath(): string
    {
        return $this->dest_path;
    }

    /**
     * @param string $dest_path
     */
    public function setDestPath(string $dest_path): void
    {
        $this->dest_path = $dest_path;
    }

    /**
     * @return array|string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $attribute
     */
    public function addAttribute(string $attribute)
    {
        $this->attributes[] = $attribute;
    }

    /**
     * Execute
     */
    public function execute()
    {
        $exec = implode(' ', array_filter(array_merge(
            ['rsync'],
            $this->getAttributes(),
            [sprintf(
                '%s@%s:%s',
                $this->getSrcSshUser(),
                $this->getSrcSshHost(),
                $this->getSrcSshPath()
            )],
            [$this->getDestPath()]
        )));

//        echo $exec;
        passthru($exec);
    }
}
