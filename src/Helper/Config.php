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

namespace Laemmi\SyncTools\Helper;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class Config
{
    /**
     * @var string
     */
    protected string $file;

    /**
     * @var Parser
     */
    protected Parser $parser;

    /**
     * Config constructor.
     * @param string $file
     * @param Parser $parser
     */
    public function __construct(string $file, Parser $parser)
    {
        if (!is_file($file)) {
            throw new \InvalidArgumentException('No config file found');
        }

        $this->file = $file;

        $this->parser = $parser;
    }

    public function parseFile(): array
    {
        $config = $this->parser->parseFile($this->file);

        $config['path_tmp'] = realpath($config['path_tmp']);

        if (!$config['path_tmp']) {
            throw new \InvalidArgumentException('path_tmp not exists');
        }

        $config['dest']['path'] = realpath($config['dest']['path']);

        if (!$config['dest']['path']) {
            throw new \InvalidArgumentException('dest:path not exists');
        }

        foreach ($config['databases'] as $key => $db) {
            $config['databases'][$key]['src']['db_dump'] = sprintf(
                '%s/%s.sql.gz',
                $config['path_tmp'],
                $db['src']['db_dbname']
            );
        }

        return $config;
    }
}
