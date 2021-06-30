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
 * @version    1.1.0
 * @since      11.03.21
 */

declare(strict_types=1);

namespace Laemmi\SyncTools\Service\FileBackup;

use Laemmi\SyncTools\Service\ServiceTrait;

class TarGz
{
    use ServiceTrait;

    /**
     * @var array|string[]
     */
    protected array $attributes = [
        '-cvz',
    ];

    /**
     * @var string
     */
    protected string $src_path;

    /**
     * @var string
     */
    protected string $archive_file;

    /**
     * @return string
     */
    public function getSrcPath(): string
    {
        return $this->src_path;
    }

    /**
     * @param string $src_path
     */
    public function setSrcPath(string $src_path): void
    {
        $this->src_path = $src_path;
    }

    /**
     * @return string
     */
    public function getArchivefile(): string
    {
        return $this->archive_file;
    }

    /**
     * @param string $archive_file
     */
    public function setArchivefile(string $archive_file): void
    {
        $this->archive_file = $archive_file;
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
     * @return string
     */
    public function execute(): string
    {
        $exec = implode(' ', array_filter(array_merge(
            ['tar'],
            $this->getAttributes(),
            [sprintf('-f %s', $this->getArchivefile())],
            [$this->getSrcPath()],
        )));

        return $this->executeCommand($exec);
    }
}
