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
    protected string $src_path;

    /**
     * @var string
     */
    protected string $dest_path;

    /**
     * @var
     */
    protected string $ssh;

    /**
     * Rsync constructor.
     * @param string $src_path
     * @param string $dest_path
     */
    public function __construct(string $src_path, string $dest_path)
    {
        $this->src_path = $src_path;
        $this->dest_path = $dest_path;
    }

    /**
     * @param string $attribute
     */
    public function addAttribute(string $attribute)
    {
        $this->attributes[] = $attribute;
    }

    /**
     * @param string $ssh_user
     * @param string $ssh_host
     */
    public function setSshConnection(string $ssh_user, string $ssh_host)
    {
        $this->ssh = sprintf('%s@%s', $ssh_user, $ssh_host);
    }

    /**
     * Execute
     */
    public function execute()
    {
        $exec = implode(' ', array_filter(array_merge(
            ['rsync'],
            $this->attributes,
            [sprintf('%s:%s', $this->ssh, $this->src_path)],
            [$this->dest_path]
        )));

        passthru($exec);
    }
}
