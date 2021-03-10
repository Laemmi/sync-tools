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

namespace Laemmi\SyncTools;

use DateTime;
use Laemmi\SyncTools\Config\DatabaseItem;
use Symfony\Component\Yaml\Parser;

class Config
{
    /**
     * @var bool
     */
    public bool $debug;

    /**
     * @var string
     */
    protected string $file;

    /**
     * @var string
     */
    protected string $default;

    /**
     * @var DatabaseItem
     */
    protected DatabaseItem $databaseItem;

    /**
     * @var Parser
     */
    protected Parser $parser;

    /**
     * @var string
     */
    public string $path_tmp;

    /**
     * @var string
     */
    public string $path_backup;

    /**
     * @var string
     */
    public string $src_ssh_host;

    /**
     * @var int
     */
    public int $src_ssh_port;

    /**
     * @var string
     */
    public string $src_ssh_user;

    /**
     * @var string
     */
    public string $src_ssh_identity;

    /**
     * @var string
     */
    public string $src_ssh_path;

    /**
     * @var string
     */
    public string $dest_path;

    /**
     * @var array
     */
    public array $attributes_rsync;

    /**
     * @var array
     */
    public array $databases;

    /**
     * Config constructor.
     * @param string $file
     * @param string $default
     * @param DatabaseItem $databaseItem
     * @param Parser $parser
     */
    public function __construct(string $file, string $default, DatabaseItem $databaseItem, Parser $parser)
    {
        if (!is_file($file)) {
            throw new \InvalidArgumentException('No config file found');
        }

        if (!is_file($default)) {
            throw new \InvalidArgumentException('No default config file found');
        }

        $this->file = $file;
        $this->default = $default;
        $this->databaseItem = $databaseItem;
        $this->parser = $parser;

        $this->parseFile();
    }

    /**
     * @return array
     */
    protected function parseFile(): array
    {
        $config = array_replace_recursive(
            $this->parser->parseFile($this->default),
            $this->parser->parseFile($this->file)
        );

        $this->debug = $config['debug'];

        $this->path_tmp = $config['path_tmp'];

        if (!is_dir($this->path_tmp)) {
            throw new \InvalidArgumentException('path_tmp not exists');
        }

        $this->path_tmp = realpath($this->path_tmp);

        $this->path_backup = $config['path_backup'] ? $config['path_backup'] : $this->path_tmp;

        if (!is_dir($this->path_backup)) {
            throw new \InvalidArgumentException('path_backup not exists');
        }

        $this->path_backup = realpath($this->path_backup);

        $this->dest_path = $config['dest']['path'];

        if (!is_dir($this->dest_path)) {
            throw new \InvalidArgumentException('dest:path not exists');
        }

        $this->dest_path = realpath($this->dest_path);

        $this->src_ssh_host     = $config['src']['ssh_host'];
        $this->src_ssh_port     = $config['src']['ssh_port'];
        $this->src_ssh_user     = $config['src']['ssh_user'];
        $this->src_ssh_identity = $config['src']['ssh_identity'];
        $this->src_ssh_path     = $config['src']['ssh_path'];

        $this->attributes_rsync = (array) $config['attributes']['rsync'];

        foreach ($config['databases'] as $key => $db) {
            $item = clone $this->databaseItem;
            $item->attributes_mysqldump = (array) $db['attributes']['mysqldump'];

            $item->src_db_host = $db['src']['db_host'];
            $item->src_db_port = isset($db['src']['db_port']) ? $db['src']['db_port'] : 3306;
            $item->src_db_user = $db['src']['db_user'];
            $item->src_db_pw = $db['src']['db_pw'];
            $item->src_db_dbname = $db['src']['db_dbname'];
            $item->src_db_dump = sprintf(
                '%s/%s.sql.gz',
                $this->path_tmp,
                $item->src_db_dbname
            );

            $item->dest_db_host = $db['dest']['db_host'];
            $item->dest_db_port = isset($db['dest']['db_port']) ? $db['dest']['db_port'] : 3306;
            $item->dest_db_user = $db['dest']['db_user'];
            $item->dest_db_pw = $db['dest']['db_pw'];
            $item->dest_db_dbname = $db['dest']['db_dbname'];
            $item->dest_db_dump = sprintf(
                '%s/%s-%s.sql.gz',
                $this->path_backup,
                $item->dest_db_dbname,
                (new DateTime('now'))->format('Y-m-d_H-i-s')
            );

            $item->dest_additional_dump = [];
            foreach ((array) $db['dest']['additional_dump'] as $ad) {
                if (!is_file($ad)) {
                    continue;
                }
                $item->dest_additional_dump[] = realpath($ad);
            }

            $this->databases[] = $item;
        }

        return $config;
    }
}
