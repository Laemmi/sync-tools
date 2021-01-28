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

namespace Laemmi\SyncTools\Command;

use Laemmi\SyncTools\Helper\Config;
use Laemmi\SyncTools\Service\DatabaseDump\Mysql;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseDump extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'database:dump';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = Config::parseFile();

        foreach ($config['server'] as $server) {
            $config['path_tmp'] = realpath($config['path_tmp']);

            if (!$config['path_tmp']) {
                throw new \InvalidArgumentException('path_tmp not exists');
            }

            $output->writeln([
                sprintf('Dump Mysql Database %s > %s', $server['src']['db_dbname'], $config['path_tmp'])
            ]);

            $service = new Mysql(
                $server['src']['db_host'],
                $server['src']['db_user'],
                $server['src']['db_pw'],
                $server['src']['db_dbname'],
                isset($server['src']['db_port']) ? $server['src']['db_port'] : 3306
            );

            $service->setPath($config['path_tmp']);
            $service->setSshConnection($server['src']['ssh_user'], $server['src']['ssh_host']);

            foreach ($server['attributes']['mysqldump'] as $attribute) {
                $service->addAttribute($attribute);
            }

            $service->execute();
        }

        return 0;
    }
}
