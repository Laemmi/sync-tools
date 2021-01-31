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

        foreach ($config['databases'] as $db) {
            $output->write(sprintf(
                'Dump Mysql Database %s > %s',
                $db['src']['db_dbname'],
                $db['src']['db_dump']
            ), true);

            $service = new Mysql(
                $db['src']['db_host'],
                $db['src']['db_user'],
                $db['src']['db_pw'],
                $db['src']['db_dbname'],
                isset($db['src']['db_port']) ? $db['src']['db_port'] : 3306
            );

            $service->setPath($db['src']['db_dump']);
            $service->setSshConnection($config['src']['ssh_user'], $config['src']['ssh_host']);

            foreach ($db['attributes']['mysqldump'] as $attribute) {
                $service->addAttribute($attribute);
            }

            $service->execute();
        }

        return 0;
    }
}
